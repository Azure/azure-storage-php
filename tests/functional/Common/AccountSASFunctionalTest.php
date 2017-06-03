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
 
namespace MicrosoftAzure\Storage\Tests\functional\Common;

use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Tests\framework\TestResources;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

/**
 * Tests for account SAS proxy tests.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class AccountSASFunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected $connectionString;
    protected $xmlSerializer;
    protected $builder;
    protected $serviceSettings;
    protected $createdContainer;
    protected $createdTable;
    protected $createdQueue;
    protected $createdShare;
    protected $blobRestProxy;
    protected $tableRestProxy;
    protected $queueRestProxy;
    protected $fileRestProxy;

    public function __construct()
    {
        $this->xmlSerializer = new XmlSerializer();
        $this->builder = new ServicesBuilder();
        $this->connectionString = TestResources::getWindowsAzureStorageServicesConnectionString();
        $this->serviceSettings =
            StorageServiceSettings::createFromConnectionString(
                $this->connectionString
            );
    }

    protected function setUp()
    {
        parent::setUp();
        $this->createdContainer = array();
        $this->createdTable     = array();
        $this->createdQueue     = array();
        $this->createdShare     = array();
        $this->blobRestProxy    = null;
        $this->tableRestProxy   = null;
        $this->queueRestProxy   = null;
        $this->fileRestProxy    = null;
    }

    protected function tearDown()
    {
        if ($this->blobRestProxy != null) {
            foreach ($this->createdContainer as $container) {
                $this->safeDeleteContainer($container);
            }
        }

        if ($this->tableRestProxy != null) {
            foreach ($this->createdTable as $table) {
                $this->safeDeleteTable($table);
            }
        }

        if ($this->queueRestProxy != null) {
            foreach ($this->createdQueue as $queue) {
                $this->safeDeleteQueue($queue);
            }
        }

        if ($this->fileRestProxy != null) {
            foreach ($this->createdShare as $share) {
                $this->safeDeleteShare($share);
            }
        }
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::generateAccountSharedAccessSignatureToken
    */
    public function testServiceAccountSASPositive()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //Full permission
        $this->initializeProxiesWithSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'ftqb',
                'ocs'
            )
        );

        //Validate 'rwdlc'
        $container = TestResources::getInterestingName('con');
        $count0 = count($this->blobRestProxy->listContainers()->getContainers());
        $this->safeCreateContainer($container);
        $count1 = count($this->blobRestProxy->listContainers()->getContainers());
        $this->assertEquals(
            $count0 + 1,
            $count1,
            sprintf("Expected %d container(s), listed %d container(s).", $count0 + 1, $count1)
        );
        $blob = TestResources::getInterestingName('blob');
        $content = 'test content';
        $this->blobRestProxy->createBlockBlob($container, $blob, $content);
        $getContent = stream_get_contents($this->blobRestProxy->getBlob($container, $blob)->getContentStream());
        $this->assertEquals($content, $getContent, "Expected {$content}, got {$getContent}.");
        $this->safeDeleteContainer($container);
        $count1 = count($this->blobRestProxy->listContainers()->getContainers());
        $this->assertEquals(
            $count0,
            $count1,
            sprintf("Expected %d container(s), listed %d container(s).", $count0, $count1)
        );

        $share = TestResources::getInterestingName('share');
        $count0 = count($this->fileRestProxy->listShares()->getShares());
        $this->safeCreateShare($share);
        $count1 = count($this->fileRestProxy->listShares()->getShares());
        $this->assertEquals(
            $count0 + 1,
            $count1,
            sprintf("Expected %d share(s), listed %d share(s).", $count0 + 1, $count1)
        );
        $file = TestResources::getInterestingName('file');
        $content = 'test content';
        $this->fileRestProxy->createFileFromContent($share, $file, $content);
        $getContent = stream_get_contents($this->fileRestProxy->getFile($share, $file)->getContentStream());
        $this->assertEquals($content, $getContent, "Expected {$content}, got {$getContent}.");
        $this->safeDeleteShare($share);
        $count1 = count($this->fileRestProxy->listShares()->getShares());
        $this->assertEquals(
            $count0,
            $count1,
            sprintf("Expected %d share(s), listed %d share(s).", $count0, $count1)
        );

        //Validate 'aup'
        $queue = TestResources::getInterestingName('queue');
        $count0 = count($this->queueRestProxy->listQueues()->getQueues());
        $this->safeCreateQueue($queue);
        $count1 = count($this->queueRestProxy->listQueues()->getQueues());
        $this->assertEquals(
            $count0 + 1,
            $count1,
            sprintf("Expected %d queue(s), listed %d queue(s).", $count0 + 1, $count1)
        );
        $message = TestResources::getInterestingName('message');
        $content = 'test content';
        $this->queueRestProxy->createMessage($queue, $content);
        $messages = $this->queueRestProxy->listMessages($queue)->getQueueMessages();
        $found = false;
        $resultMessage = null;
        foreach ($messages as $value) {
            if ($value->getMessageText() === $content) {
                $found = true;
                $resultMessage = $value;
                break;
            }
        }
        $this->assertTrue($found, "Created message not found in the specified queue");
        $count3 = count($messages);
        $this->queueRestProxy->deleteMessage(
            $queue,
            $resultMessage->getMessageId(),
            $resultMessage->getPopReceipt()
        );
        $count1 = count($this->queueRestProxy->listMessages($queue)->getQueueMessages());
        $this->assertEquals(
            $count3 - 1,
            $count1,
            sprintf("Expected %d messages(s), listed %d messages(s).", $count3 - 1, $count1)
        );
        $this->safeDeleteQueue($queue);
        $count1 = count($this->queueRestProxy->listQueues()->getQueues());
        $this->assertEquals(
            $count0,
            $count1,
            sprintf("Expected %d queue(s), listed %d queue(s).", $count0, $count1)
        );
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::generateAccountSharedAccessSignatureToken
    */
    public function testServiceAccountSASSSNegative()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //qtf permission
        $this->initializeProxiesWithSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'ftq',
                'ocs'
            )
        );

        //Validate cannot access blob service
        $message = '';
        try {
            $this->blobRestProxy->listContainers();
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(
            'not authorized to perform this operation',
            $message,
            'Error: access not blocked for blob service.'
        );
        //Validate can access table, file and queue service
        $this->tableRestProxy->queryTables();
        $this->queueRestProxy->listQueues();
        $this->fileRestProxy->listShares();

        //btf permission
        $this->initializeProxiesWithSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'btf',
                'ocs'
            )
        );

        //Validate cannot access queue service
        $message = '';
        try {
            $this->queueRestProxy->listQueues();
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(
            'not authorized to perform this operation',
            $message,
            'Error: access not blocked for queue service.'
        );
        //Validate can access blob, file and table service
        $this->tableRestProxy->queryTables();
        $this->blobRestProxy->listContainers();
        $this->fileRestProxy->listShares();

        //bqf permission
        $this->initializeProxiesWithSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'fqb',
                'ocs'
            )
        );

        //Validate cannot access queue service
        $message = '';
        try {
            $this->tableRestProxy->queryTables();
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(
            'not authorized to perform this operation',
            $message,
            'Error: access not blocked for table service.'
        );
        //Validate can access blob, table and file service
        $this->queueRestProxy->listQueues();
        $this->blobRestProxy->listContainers();
        $this->fileRestProxy->listShares();

        //btq permission
        $this->initializeProxiesWithSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'qtb',
                'ocs'
            )
        );

        //Validate cannot access file service
        $message = '';
        try {
            $this->fileRestProxy->listShares();
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(
            'not authorized to perform this operation',
            $message,
            'Error: access not blocked for file service.'
        );
        //Validate can access blob, table and queue service
        $this->queueRestProxy->listQueues();
        $this->blobRestProxy->listContainers();
        $this->tableRestProxy->queryTables();
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::generateAccountSharedAccessSignatureToken
    */
    public function testServiceAccountSASSPNegative()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //rdaup permit
        $this->initializeProxiesWithSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'rdaup',
                'btqf',
                'ocs'
            )
        );
        $queue = TestResources::getInterestingName('queue');
        $message = '';
        try {
            $this->queueRestProxy->listQueues();
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(
            'not authorized to perform this operation',
            $message,
            'Error: access not blocked for list queue operation.'
        );
        $message = '';
        try {
            $this->queueRestProxy->createQueue('exceptionqueue');
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(
            'not authorized to perform this operation',
            $message,
            'Error: access not blocked for create queue operation.'
        );

        //wlcu permit
        $this->initializeProxiesWithSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'wlcu',
                'btqf',
                'ocs'
            )
        );
        $container = TestResources::getInterestingName('container');
        $blob = TestResources::getInterestingName('blob');
        $this->safeCreateContainer($container);
        $this->blobRestProxy->createBlockBlob($container, $blob, 'test message');
        $message = '';
        try {
            $this->blobRestProxy->getBlob($container, $blob);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(
            'not authorized to perform this operation',
            $message,
            'Error: access not blocked for get blob operation.'
        );
        $message = '';
        try {
            $this->blobRestProxy->deleteBlob($container, $blob);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(
            'not authorized to perform this operation',
            $message,
            'Error: access not blocked for delete blob operation.'
        );

        //initialize with full permision for tearDown
        $this->initializeProxiesWithSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'rdlwaup',
                'btqf',
                'ocs'
            )
        );
    }

    private function initializeProxiesWithSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateAccountSharedAccessSignatureToken(
            $testCase['signedVersion'],
            $testCase['signedPermissions'],
            $testCase['signedService'],
            $testCase['signedResourceType'],
            $testCase['signedExpiracy'],
            $testCase['signedStart'],
            $testCase['signedIP'],
            $testCase['signedProtocol']
        );

        $accountName = $helper->getAccountName();

        $connectionString = Resources::BLOB_ENDPOINT_NAME .
                             '='.
                             'https://' .
                             $accountName .
                             '.' .
                             Resources::BLOB_BASE_DNS_NAME .
                             ';';
        $connectionString .= Resources::QUEUE_ENDPOINT_NAME .
                             '='.
                             'https://' .
                             $accountName .
                             '.' .
                             Resources::QUEUE_BASE_DNS_NAME .
                             ';';
        $connectionString .= Resources::TABLE_ENDPOINT_NAME .
                             '='.
                             'https://' .
                             $accountName .
                             '.' .
                             Resources::TABLE_BASE_DNS_NAME .
                             ';';
        $connectionString .= Resources::FILE_ENDPOINT_NAME .
                             '='.
                             'https://' .
                             $accountName .
                             '.' .
                             Resources::FILE_BASE_DNS_NAME .
                             ';';
        $connectionString .= Resources::SAS_TOKEN_NAME .
                             '='.
                             $sas;

        $this->blobRestProxy  =
            $this->builder->createBlobService($connectionString);
        $this->queueRestProxy =
            $this->builder->createQueueService($connectionString);
        $this->tableRestProxy =
            $this->builder->createTableService($connectionString);
        $this->fileRestProxy =
            $this->builder->createFileService($connectionString);
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
     */
    private function safeDeleteContainer($name)
    {
        try {
            $this->blobRestProxy->deleteContainer($name);
            $this->createdContainer = array_diff($this->createdContainer, [$name]);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     */
    private function safeCreateContainer($name)
    {
        try {
            $this->blobRestProxy->createContainer($name);
            $this->createdContainer[] = $name;
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::deleteQueue
     */
    private function safeDeleteQueue($name)
    {
        try {
            $this->queueRestProxy->deleteQueue($name);
            $this->createdQueue = array_diff($this->createdQueue, [$name]);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::createQueue
     */
    private function safeCreateQueue($name)
    {
        try {
            $this->queueRestProxy->createQueue($name);
            $this->createdQueue[] = $name;
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::deleteTable
     */
    private function safeDeleteTable($name)
    {
        try {
            $this->tableRestProxy->deleteTable($name);
            $this->createdTable = array_diff($this->createdTable, [$name]);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createTable
     */
    private function safeCreateTable($name)
    {
        try {
            $this->tableRestProxy->createTable($name);
            $this->createdTable[] = $name;
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     */
    private function safeDeleteShare($name)
    {
        try {
            $this->fileRestProxy->deleteShare($name);
            $this->createdShare = array_diff($this->createdShare, [$name]);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\File\FileRestProxy::createShare
     */
    private function safeCreateShare($name)
    {
        try {
            $this->fileRestProxy->createShare($name);
            $this->createdShare[] = $name;
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }
}
