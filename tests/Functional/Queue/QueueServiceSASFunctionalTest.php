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

namespace MicrosoftAzure\Storage\Tests\Functional\Queue;

use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\SASFunctionalTestBase;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
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
class QueueServiceSASFunctionalTest extends SASFunctionalTestBase
{
    public function testQueueServiceSAS()
    {
        $helper = new QueueSharedAccessSignatureHelperMock(
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
