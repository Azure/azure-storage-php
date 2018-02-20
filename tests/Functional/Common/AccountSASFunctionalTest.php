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
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

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
class AccountSASFunctionalTest extends SASFunctionalTestBase
{
    public function testAccountSASPositive()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //Full permission
        $this->initializeProxiesWithAccountSASfromArray(
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

    public function testAccountSASSSNegative()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //qtf permission
        $this->initializeProxiesWithAccountSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'ftq',
                'ocs'
            )
        );

        $reflection = $this;
        //Validate cannot access blob service
        $this->validateServiceExceptionErrorMessage(
            'not authorized to perform this operation',
            function () use ($reflection) {
                $reflection->blobRestProxy->listContainers();
            },
            'Error: access not blocked for blob service.'
        );
        //Validate can access table, file and queue service
        $this->tableRestProxy->queryTables();
        $this->queueRestProxy->listQueues();
        $this->fileRestProxy->listShares();

        //btf permission
        $this->initializeProxiesWithAccountSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'btf',
                'ocs'
            )
        );

        //Validate cannot access queue service
        $this->validateServiceExceptionErrorMessage(
            'not authorized to perform this operation',
            function () use ($reflection) {
                $reflection->queueRestProxy->listQueues();
            },
            'Error: access not blocked for queue service.'
        );
        //Validate can access blob, file and table service
        $this->tableRestProxy->queryTables();
        $this->blobRestProxy->listContainers();
        $this->fileRestProxy->listShares();

        //bqf permission
        $this->initializeProxiesWithAccountSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'fqb',
                'ocs'
            )
        );

        //Validate cannot access table service
        $this->validateServiceExceptionErrorMessage(
            'not authorized to perform this operation',
            function () use ($reflection) {
                $reflection->tableRestProxy->queryTables();
            },
            'Error: access not blocked for table service.'
        );
        //Validate can access blob, queue and file service
        $this->queueRestProxy->listQueues();
        $this->blobRestProxy->listContainers();
        $this->fileRestProxy->listShares();

        //btq permission
        $this->initializeProxiesWithAccountSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'pucaldwr',
                'qtb',
                'ocs'
            )
        );

        //Validate cannot access file service
        $this->validateServiceExceptionErrorMessage(
            'not authorized to perform this operation',
            function () use ($reflection) {
                $reflection->fileRestProxy->listShares();
            },
            'Error: access not blocked for file service.'
        );
        //Validate can access blob, table and queue service
        $this->queueRestProxy->listQueues();
        $this->blobRestProxy->listContainers();
        $this->tableRestProxy->queryTables();
    }

    public function testAccountSASSPNegative()
    {
        $helper = new SharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        $reflection = $this;
        //rdaup permit
        $this->initializeProxiesWithAccountSASfromArray(
            $helper,
            TestResources::getInterestingAccountSASTestCase(
                'rdaup',
                'btqf',
                'ocs'
            )
        );
        $this->validateServiceExceptionErrorMessage(
            'not authorized to perform this operation',
            function () use ($reflection) {
                $reflection->queueRestProxy->listQueues();
            },
            'Error: access not blocked for list queue operation.'
        );
        $this->validateServiceExceptionErrorMessage(
            'not authorized to perform this operation',
            function () use ($reflection) {
                $reflection->queueRestProxy->createQueue('exceptionqueue');
            },
            'Error: access not blocked for create queue operation.'
        );

        //wlcu permit
        $this->initializeProxiesWithAccountSASfromArray(
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
        $this->validateServiceExceptionErrorMessage(
            'not authorized to perform this operation',
            function () use ($reflection, $container, $blob) {
                $reflection->blobRestProxy->getBlob($container, $blob);
            },
            'Error: access not blocked for get blob operation.'
        );
        $this->validateServiceExceptionErrorMessage(
            'not authorized to perform this operation',
            function () use ($reflection, $container, $blob) {
                $reflection->blobRestProxy->deleteBlob($container, $blob);
            },
            'Error: access not blocked for delete blob operation.'
        );
    }

    private function initializeProxiesWithAccountSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateAccountSharedAccessSignatureToken(
            $testCase['signedVersion'],
            $testCase['signedPermissions'],
            $testCase['signedService'],
            $testCase['signedResourceType'],
            $testCase['signedExpiry'],
            $testCase['signedStart'],
            $testCase['signedIP'],
            $testCase['signedProtocol']
        );

        $accountName = $helper->getAccountName();

        $this->initializeProxiesWithSASandAccountName($sas, $accountName);
    }
}
