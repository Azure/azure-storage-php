<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Tests\Functional\Common;

use MicrosoftAzure\Storage\Tests\Framework\SASFunctionalTestBase;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Tests\Functional\Common\SharedAccessSignatureHelperMock;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions;

/**
 * Tests for service SAS proxy tests.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ServiceSASFunctionalTest extends SASFunctionalTestBase
{
    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::generateBlobServiceSharedAccessSignatureToken
    */
    public function testBlobServiceSAS()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //setup the proxies for creating containers
        $this->setUpWithConnectionString($this->connectionString);

        $containerProxies = array();
        $containers = array();
        $containers[] = TestResources::getInterestingName('con');
        $this->safeCreateContainer($containers[0]);
        $containers[] = TestResources::getInterestingName('con');
        $this->safeCreateContainer($containers[1]);

        //Full permission for container0
        $containerProxies[] = $this->createProxyWithBlobSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'racwdl',
                Resources::RESOURCE_TYPE_CONTAINER,
                $containers[0]
            )
        );
        //Full permission for container1
        $containerProxies[] = $this->createProxyWithBlobSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'racwdl',
                Resources::RESOURCE_TYPE_CONTAINER,
                $containers[1]
            )
        );

        //Validate the permission for each of the proxy/container pair
        for ($i = 0; $i < 2; ++$i) {
            $proxy = $containerProxies[$i];
            $container = $containers[$i];
            //c
            $blob0 = TestResources::getInterestingName('blob');
            $proxy->createAppendBlob($container, $blob0);
            //l
            $result = $proxy->listBlobs($container);
            $this->assertEquals($blob0, $result->getBlobs()[0]->getName());
            //a
            $content = \openssl_random_pseudo_bytes(1024);
            $proxy->appendBlock($container, $blob0, $content);
            //w
            $blob1 = TestResources::getInterestingName('blob');
            $proxy->createBlockBlob($container, $blob1, $content);
            //r
            $actualContent = \stream_get_contents(
                $proxy->getBlob($container, $blob0)->getContentStream()
            );
            $this->assertEquals($content, $actualContent);
            $actualContent = \stream_get_contents(
                $proxy->getBlob($container, $blob1)->getContentStream()
            );
            $this->assertEquals($content, $actualContent);
            //d
            $proxy->deleteBlob($container, $blob0);
            $proxy->deleteBlob($container, $blob1);
            $result = $proxy->listBlobs($container);
            $this->assertEquals(0, \count($result->getBlobs()));
        }
        //Validate that a cross access with wrong proxy/container pair
        //would not be successful
        for ($i= 0; $i < 2; ++$i) {
            $proxy = $containerProxies[$i];
            $container = $containers[1 - $i];
            $blob0 = TestResources::getInterestingName('blob');
            //c
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $container, $blob0) {
                    $proxy->createAppendBlob($container, $blob0);
                }
            );
            //l
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $container) {
                    $proxy->listBlobs($container);
                }
            );
            //w
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $container) {
                    $proxy->createBlockBlob($container, 'myblob', 'testcontent');
                }
            );
        }

        //No list permission
        $containerProxy = $this->createProxyWithBlobSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'racwd',
                Resources::RESOURCE_TYPE_CONTAINER,
                $containers[0]
            )
        );
        $container = $containers[0];
        //l
        $this->validateServiceExceptionErrorMessage(
            'Server failed to authenticate the request.',
            function () use ($proxy, $container) {
                $proxy->listBlobs($container);
            }
        );
        //can c and d
        $blob0 = TestResources::getInterestingName('blob');
        $containerProxy->createAppendBlob($container, $blob0);
        $containerProxy->deleteBlob($container, $blob0);

        //blob permission
        $blob = TestResources::getInterestingName('blob');
        $blobProxy = $this->createProxyWithBlobSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'racwd',
                Resources::RESOURCE_TYPE_BLOB,
                $container . '/' . $blob
            )
        );
        //l cannot be performed
        $this->validateServiceExceptionErrorMessage(
            'The specified signed resource is not allowed for the this resource level',
            function () use ($blobProxy, $container) {
                $blobProxy->listBlobs($container);
            }
        );
        $content = \openssl_random_pseudo_bytes(20);
        //rcwd can be performed.
        $blobProxy->createBlockBlob($container, $blob, $content);
        $actual = stream_get_contents($blobProxy->getBlob($container, $blob)->getContentStream());
        $this->assertEquals($content, $actual);
        $blobProxy->deleteBlob($container, $blob);
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::generateFileServiceSharedAccessSignatureToken
    */
    public function testFileServiceSAS()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //setup the proxies for creating shares
        $this->setUpWithConnectionString($this->connectionString);

        $shareProxies = array();
        $shares = array();
        $shares[] = TestResources::getInterestingName('sha');
        $this->safeCreateShare($shares[0]);
        $shares[] = TestResources::getInterestingName('sha');
        $this->safeCreateShare($shares[1]);

        //Full permission for share0
        $shareProxies[] = $this->createProxyWithFileSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'rcwdl',
                Resources::RESOURCE_TYPE_SHARE,
                $shares[0]
            )
        );
        //Full permission for share1
        $shareProxies[] = $this->createProxyWithFileSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'rcwdl',
                Resources::RESOURCE_TYPE_SHARE,
                $shares[1]
            )
        );

        //Validate the permission for each of the proxy/share pair
        for ($i = 0; $i < 2; ++$i) {
            $proxy = $shareProxies[$i];
            $share = $shares[$i];
            //cw
            $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_1);
            $file = TestResources::getInterestingName('file');
            $proxy->createFileFromContent($share, $file, $content);
            //l
            $result = $proxy->listDirectoriesAndFiles($share);
            $this->assertEquals($file, $result->getFiles()[0]->getName());
            //r
            $actualContent = \stream_get_contents(
                $proxy->getFile($share, $file)->getContentStream()
            );
            $this->assertEquals($content, $actualContent);
            //d
            $proxy->deleteFile($share, $file);
            $result = $proxy->listDirectoriesAndFiles($share);
            $this->assertEquals(0, \count($result->getFiles()));
        }
        //Validate that a cross access with wrong proxy/share pair
        //would not be successful
        for ($i= 0; $i < 2; ++$i) {
            $proxy = $shareProxies[$i];
            $share = $shares[1 - $i];
            $file = TestResources::getInterestingName('file');
            //c
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $share, $file) {
                    $proxy->createFile($share, $file, Resources::MB_IN_BYTES_1);
                }
            );
            //l
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $share) {
                    $proxy->listDirectoriesAndFiles($share);
                }
            );
        }

        //No list permission
        $shareProxy = $this->createProxyWithFileSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'rcwd',
                Resources::RESOURCE_TYPE_SHARE,
                $shares[0]
            )
        );
        $share = $shares[0];
        //l
        $this->validateServiceExceptionErrorMessage(
            'Server failed to authenticate the request.',
            function () use ($proxy, $share) {
                $proxy->listDirectoriesAndFiles($share);
            }
        );
        //can c and d
        $file = TestResources::getInterestingName('file');
        $shareProxy->createFile($share, $file, Resources::MB_IN_BYTES_1);
        $shareProxy->deleteFile($share, $file);

        //file permission
        $file = TestResources::getInterestingName('file');
        $fileProxy = $this->createProxyWithFileSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'rcwd',
                Resources::RESOURCE_TYPE_FILE,
                $share . '/' . $file
            )
        );
        //l cannot be performed
        $this->validateServiceExceptionErrorMessage(
            'The specified signed resource is not allowed for the this resource level',
            function () use ($fileProxy, $share) {
                $fileProxy->listDirectoriesAndFiles($share);
            }
        );
        $content = \openssl_random_pseudo_bytes(20);
        //rcwd can be performed.
        $fileProxy->createFileFromContent($share, $file, $content);
        $actual = stream_get_contents($fileProxy->getFile($share, $file)->getContentStream());
        $this->assertEquals($content, $actual);
        $fileProxy->deleteFile($share, $file);
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::generateTableServiceSharedAccessSignatureToken
    */
    public function testTableServiceSAS()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //setup the proxies for creating tables
        $this->setUpWithConnectionString($this->connectionString);

        $tableProxies = array();
        $tables = array();
        $tables[] = TestResources::getInterestingName('tbl');
        $this->safeCreateTable($tables[0]);
        $tables[] = TestResources::getInterestingName('tbl');
        $this->safeCreateTable($tables[1]);

        //Full permission for table0
        $tableProxies[] = $this->createProxyWithTableSASfromArray(
            $helper,
            TestResources::getInterestingTableSASTestCase(
                'raud',
                $tables[0]
            )
        );
        //Full permission for table1
        $tableProxies[] = $this->createProxyWithTableSASfromArray(
            $helper,
            TestResources::getInterestingTableSASTestCase(
                'raud',
                $tables[1]
            )
        );

        //Validate the permission for each of the proxy/table pair
        for ($i = 0; $i < 2; ++$i) {
            $proxy = $tableProxies[$i];
            $table = $tables[$i];
            $entity = TestResources::getTestEntity('123', '456');
            //test raud.
            $proxy->insertEntity($table, $entity);
            $actual = $proxy->getEntity($table, '123', '456')->getEntity();
            $this->assertEquals(
                $entity->getPropertyValue('CustomerId'),
                $actual->getPropertyValue('CustomerId')
            );
            $entity->setPropertyValue('CustomerId', 891);
            $proxy->updateEntity($table, $entity);
            $actual = $proxy->getEntity($table, '123', '456')->getEntity();
            $this->assertEquals(
                $entity->getPropertyValue('CustomerId'),
                $actual->getPropertyValue('CustomerId')
            );
            $proxy->deleteEntity($table, '123', '456');
            $result = $proxy->queryEntities($table);
            $this->assertEquals(0, \count($result->getEntities()));
        }
        //Validate that a cross access with wrong proxy/table pair
        //would not be successfull
        for ($i= 0; $i < 2; ++$i) {
            $proxy = $tableProxies[$i];
            $table = $tables[1 - $i];
            $entity = TestResources::getTestEntity('123', '456');
            //a
            $this->validateServiceExceptionErrorMessage(
                'This request is not authorized to perform this operation.',
                function () use ($proxy, $table, $entity) {
                    $proxy->insertEntity($table, $entity);
                }
            );
            //r
            $this->validateServiceExceptionErrorMessage(
                'This request is not authorized to perform this operation.',
                function () use ($proxy, $table) {
                    $proxy->queryEntities($table);
                }
            );
        }

        //test startpk, startrk, endpk, endrk logic
        $tableProxy = $this->createProxyWithTableSASfromArray(
            $helper,
            TestResources::getInterestingTableSASTestCase(
                'raud',
                $tables[0],
                '',
                '',
                '',
                '123',
                '456',
                '123',
                '456'
            )
        );
        $table = $tables[0];
        //test raud.
        $tableProxy->insertEntity($table, $entity);
        $actual = $tableProxy->getEntity($table, '123', '456')->getEntity();
        $this->assertEquals(
            $entity->getPropertyValue('CustomerId'),
            $actual->getPropertyValue('CustomerId')
        );
        $entity->setPropertyValue('CustomerId', 891);
        $tableProxy->updateEntity($table, $entity);
        $actual = $tableProxy->getEntity($table, '123', '456')->getEntity();
        $this->assertEquals(
            $entity->getPropertyValue('CustomerId'),
            $actual->getPropertyValue('CustomerId')
        );
        $tableProxy->deleteEntity($table, '123', '456');
        $result = $tableProxy->queryEntities($table);
        $this->assertEquals(0, \count($result->getEntities()));

        //test out of scope pk cannot be accessed.
        $entity = TestResources::getTestEntity('124', '456');
        $this->validateServiceExceptionErrorMessage(
            'This request is not authorized to perform this operation.',
            function () use ($tableProxy, $table, $entity) {
                $tableProxy->insertEntity($table, $entity);
            }
        );
        //test out of scope rk cannot be accessed.
        $entity = TestResources::getTestEntity('123', '457');
        $this->validateServiceExceptionErrorMessage(
            'This request is not authorized to perform this operation.',
            function () use ($tableProxy, $table, $entity) {
                $tableProxy->insertEntity($table, $entity);
            }
        );
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::generateTableServiceSharedAccessSignatureToken
    */
    public function testQueueServiceSAS()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //setup the proxies for creating queues
        $this->setUpWithConnectionString($this->connectionString);

        $queueProxies = array();
        $queues = array();
        $queues[] = TestResources::getInterestingName('qu');
        $this->safeCreateQueue($queues[0]);
        $queues[] = TestResources::getInterestingName('qu');
        $this->safeCreateQueue($queues[1]);

        //Full permission for queue0
        $queueProxies[] = $this->createProxyWithQueueSASfromArray(
            $helper,
            TestResources::getInterestingQueueSASTestCase(
                'raup',
                $queues[0]
            )
        );
        //Full permission for queue1
        $queueProxies[] = $this->createProxyWithQueueSASfromArray(
            $helper,
            TestResources::getInterestingQueueSASTestCase(
                'raup',
                $queues[1]
            )
        );

        //Validate the permission for each of the proxy/queue pair
        for ($i = 0; $i < 2; ++$i) {
            $proxy = $queueProxies[$i];
            $queue = $queues[$i];
            $entity = TestResources::getTestEntity('123', '456');
            //test raup.
            $messageText = \uniqid();
            $proxy->createMessage($queue, $messageText);
            $options = new ListMessagesOptions();
            $options->setNumberOfMessages(1);
            $actual = $proxy->listMessages($queue, $options)->getQueueMessages()[0];
            $this->assertEquals($messageText, $actual->getMessageText());
            $messageText = \uniqid();
            $proxy->updateMessage(
                $queue,
                $actual->getMessageId(),
                $actual->getPopReceipt(),
                $messageText,
                1
            );
            //wait until visibility timeout has run out.
            \sleep(2);
            $result = $proxy->peekMessages($queue);
            $actualMessage = $result->getQueueMessages()[0];
            $this->assertEquals($messageText, $actualMessage->getMessageText());
        }
        //Validate that a cross access with wrong proxy/queue pair
        //would not be successfull
        for ($i= 0; $i < 2; ++$i) {
            $proxy = $queueProxies[$i];
            $queue = $queues[1 - $i];
            //a
            $messageText = \uniqid();
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $queue, $messageText) {
                    $proxy->createMessage($queue, $messageText);
                }
            );
            //r
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $queue) {
                    $proxy->peekMessages($queue);
                }
            );
        }
    }

    private function createProxyWithBlobSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
            $testCase['signedResource'],
            $testCase['resourceName'],
            $testCase['signedPermissions'],
            $testCase['signedExpiry'],
            $testCase['signedStart'],
            $testCase['signedIP'],
            $testCase['signedProtocol'],
            $testCase['signedIdentifier'],
            $testCase['cacheControl'],
            $testCase['contentDisposition'],
            $testCase['contentEncoding'],
            $testCase['contentLanguage'],
            $testCase['contentType']
        );

        $accountName = $helper->getAccountName();

        return $this->createProxyWithSAS($sas, $accountName, $testCase['signedResource']);
    }

    private function createProxyWithFileSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateFileServiceSharedAccessSignatureToken(
            $testCase['signedResource'],
            $testCase['resourceName'],
            $testCase['signedPermissions'],
            $testCase['signedExpiry'],
            $testCase['signedStart'],
            $testCase['signedIP'],
            $testCase['signedProtocol'],
            $testCase['signedIdentifier'],
            $testCase['cacheControl'],
            $testCase['contentDisposition'],
            $testCase['contentEncoding'],
            $testCase['contentLanguage'],
            $testCase['contentType']
        );

        $accountName = $helper->getAccountName();

        return $this->createProxyWithSAS($sas, $accountName, $testCase['signedResource']);
    }

    private function createProxyWithTableSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateTableServiceSharedAccessSignatureToken(
            $testCase['tableName'],
            $testCase['signedPermissions'],
            $testCase['signedExpiry'],
            $testCase['signedStart'],
            $testCase['signedIP'],
            $testCase['signedProtocol'],
            $testCase['signedIdentifier'],
            $testCase['startingPartitionKey'],
            $testCase['startingRowKey'],
            $testCase['endingPartitionKey'],
            $testCase['endingRowKey']
        );

        $accountName = $helper->getAccountName();

        return $this->createProxyWithSAS($sas, $accountName, Resources::RESOURCE_TYPE_TABLE);
    }

    private function createProxyWithQueueSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateQueueServiceSharedAccessSignatureToken(
            $testCase['queueName'],
            $testCase['signedPermissions'],
            $testCase['signedExpiry'],
            $testCase['signedStart'],
            $testCase['signedIP'],
            $testCase['signedProtocol'],
            $testCase['signedIdentifier']
        );

        $accountName = $helper->getAccountName();

        return $this->createProxyWithSAS($sas, $accountName, Resources::RESOURCE_TYPE_QUEUE);
    }
}
