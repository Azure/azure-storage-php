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
 * @package   MicrosoftAzure\Storage\Tests\Functional\Queue
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\Queue;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\QueueServiceOptions;

class QueueServiceFunctionalParameterTest extends FunctionalTestBase
{
    public function testGetServicePropertiesNullOptions()
    {
        try {
            $this->restProxy->getServiceProperties(null);
            $this->assertFalse($this->isEmulated(), 'Should fail if and only if in emulator');
        } catch (ServiceException $e) {
            // Expect failure when run this test with emulator, as v1.6 doesn't support this method
            if ($this->isEmulated()) {
                // Properties are not supported in emulator
                $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }
    }

    public function testSetServicePropertiesNullOptions1()
    {
        $serviceProperties = QueueServiceFunctionalTestData::getDefaultServiceProperties();
        try {
            $this->restProxy->setServiceProperties($serviceProperties);
            $this->assertFalse($this->isEmulated(), 'service properties should throw in emulator');
        } catch (ServiceException $e) {
            if ($this->isEmulated()) {
                // Properties are not supported in emulator
                $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }
    }

    public function testSetServicePropertiesNullOptions2()
    {
        $serviceProperties = QueueServiceFunctionalTestData::getDefaultServiceProperties();

        try {
            $this->restProxy->setServiceProperties($serviceProperties, null);
            $this->assertFalse($this->isEmulated(), 'service properties should throw in emulator');
        } catch (ServiceException $e) {
            if ($this->isEmulated()) {
                // Setting is not supported in emulator
                $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }
    }

    public function testListQueuesNullOptions()
    {
        $this->restProxy->listQueues(null);
        $this->assertTrue(true, 'Should just work');
    }

    public function testCreateQueueNullName()
    {
        try {
            $this->restProxy->createQueue(null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteQueueNullName()
    {
        try {
            $this->restProxy->deleteQueue(null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testSetQueueMetadataNullMetadata()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $this->restProxy->setQueueMetadata($queue, null);
        $this->assertTrue(true, 'Should just work');
    }

    public function testSetQueueMetadataEmptyMetadata()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $this->restProxy->setQueueMetadata($queue, array());
        $this->assertTrue(true, 'Should just work');
    }

    public function testSetQueueMetadataNullOptions()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $this->restProxy->setQueueMetadata($queue, array(), null);
        $this->assertTrue(true, 'Should just work');
    }

    public function testCreateMessageQueueNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        try {
            $this->restProxy->createMessage(null, null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
        $this->restProxy->clearMessages($queue);
    }

    public function testCreateMessageNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $this->restProxy->createMessage($queue, null);
        $this->restProxy->clearMessages($queue);
        $this->assertTrue(true, 'Should just work');
    }

    public function testCreateMessageBothMessageAndOptionsNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $this->restProxy->createMessage($queue, null, null);
        $this->restProxy->clearMessages($queue);
        $this->assertTrue(true, 'Should just work');
    }

    public function testCreateMessageMessageNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $this->restProxy->createMessage($queue, null, QueueServiceFunctionalTestData::getSimpleCreateMessageOptions());
        $this->restProxy->clearMessages($queue);
        $this->assertTrue(true, 'Should just work');
    }

    public function testCreateMessageOptionsNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $this->restProxy->createMessage($queue, QueueServiceFunctionalTestData::getSimpleMessageText(), null);
        $this->restProxy->clearMessages($queue);
        $this->assertTrue(true, 'Should just work');
    }

    public function testUpdateMessageQueueNull()
    {
        $queue = null;
        $messageId = 'abc';
        $popReceipt = 'abc';
        $messageText = 'abc';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect null name to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testUpdateMessageQueueEmpty()
    {
        $queue = '';
        $messageId = 'abc';
        $popReceipt = 'abc';
        $messageText = 'abc';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect null name to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testUpdateMessageMessageIdNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = null;
        $popReceipt = 'abc';
        $messageText = 'abc';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect null messageId to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'messageId'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testUpdateMessageMessageIdEmpty()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = '';
        $popReceipt = 'abc';
        $messageText = 'abc';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect null messageId to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'messageId'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testUpdateMessagePopReceiptNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = 'abc';
        $popReceipt = null;
        $messageText = 'abc';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect null popReceipt to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'popReceipt'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testUpdateMessagePopReceiptEmpty()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = 'abc';
        $popReceipt = '';
        $messageText = 'abc';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect null popReceipt to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'popReceipt'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testUpdateMessageMessageTextNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = 'abc';
        $popReceipt = 'abc';
        $messageText = null;
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect bogus message id to throw');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
        }
    }

    public function testUpdateMessageMessageTextEmpty()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = 'abc';
        $popReceipt = 'abc';
        $messageText = '';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect bogus message id to throw');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
        }
    }

    public function testUpdateMessageOptionsNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = 'abc';
        $popReceipt = 'abc';
        $messageText = 'abc';
        $options = null;
        $visibilityTimeoutInSeconds = 1;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect bogus message id to throw');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
        }
    }

    public function testUpdateMessageVisibilityTimeout0()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = 'abc';
        $popReceipt = 'abc';
        $messageText = 'abc';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = 0;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect bogus message id to throw');
        } catch (\InvalidArgumentException $e) {
            $this->fail('Should not get an InvalidArgumentException exception');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
        }
    }

    public function testUpdateMessageVisibilityTimeoutNull()
    {
        $queue = QueueServiceFunctionalTestData::$testQueueNames[0];
        $messageId = 'abc';
        $popReceipt = 'abc';
        $messageText = 'abc';
        $options = new QueueServiceOptions();
        $visibilityTimeoutInSeconds = null;

        try {
            $this->restProxy->updateMessage($queue, $messageId, $popReceipt, $messageText, $visibilityTimeoutInSeconds, $options);
            $this->fail('Expect null visibilityTimeoutInSeconds to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_MSG, 'visibilityTimeoutInSeconds'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteMessageQueueNullNoOptions()
    {
        $queue = null;
        $messageId = 'abc';
        $popReceipt = 'abc';

        try {
            $this->restProxy->deleteMessage($queue, $messageId, $popReceipt);
            $this->fail('Expect null queue to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteMessageQueueEmptyNoOptions()
    {
        $queue = '';
        $messageId = 'abc';
        $popReceipt = 'abc';

        try {
            $this->restProxy->deleteMessage($queue, $messageId, $popReceipt);
            $this->fail('Expect empty queue to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteMessageQueueNullWithOptions()
    {
        $queue = null;
        $messageId = 'abc';
        $popReceipt = 'abc';
        $options = new QueueServiceOptions();

        try {
            $this->restProxy->deleteMessage($queue, $messageId, $popReceipt, $options);
            $this->fail('Expect null queue to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteMessageMessageIdNull()
    {
        $queue = 'abc';
        $messageId = null;
        $popReceipt = 'abc';
        $options = new QueueServiceOptions();

        try {
            $this->restProxy->deleteMessage($queue, $messageId, $popReceipt, $options);
            $this->fail('Expect null messageId to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'messageId'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteMessageMessageIdEmpty()
    {
        $queue = 'abc';
        $messageId = '';
        $popReceipt = 'abc';
        $options = new QueueServiceOptions();

        try {
            $this->restProxy->deleteMessage($queue, $messageId, $popReceipt, $options);
            $this->fail('Expect empty messageId to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'messageId'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteMessagePopReceiptNull()
    {
        $queue = 'abc';
        $messageId = 'abc';
        $popReceipt = null;
        $options = new QueueServiceOptions();

        try {
            $this->restProxy->deleteMessage($queue, $messageId, $popReceipt, $options);
            $this->fail('Expect null popReceipt to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'popReceipt'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteMessagePopReceiptEmpty()
    {
        $queue = 'abc';
        $messageId = 'abc';
        $popReceipt = '';
        $options = new QueueServiceOptions();

        try {
            $this->restProxy->deleteMessage($queue, $messageId, $popReceipt, $options);
            $this->fail('Expect empty popReceipt to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'popReceipt'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testDeleteMessageOptionsNull()
    {
        $queue = 'abc';
        $messageId = 'abc';
        $popReceipt = 'abc';
        $options = null;

        try {
            $this->restProxy->deleteMessage($queue, $messageId, $popReceipt, $options);
            $this->fail('Expect bogus message id to throw');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
        }
    }

    public function testListMessagesQueueNullNoOptions()
    {
        try {
            $this->restProxy->listMessages(null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testListMessagesQueueNullWithOptions()
    {
        try {
            $this->restProxy->listMessages(null, new ListMessagesOptions());
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testListMessagesOptionsNull()
    {
        try {
            $this->restProxy->listMessages('abc', null);
            $this->fail('Expect bogus queue name to throw');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_NOT_FOUND, $e->getCode(), 'getCode');
        }
    }

    public function testListMessagesAllNull()
    {
        try {
            $this->restProxy->listMessages(null, null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testPeekMessagesQueueNullNoOptions()
    {
        try {
            $this->restProxy->peekMessages(null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testPeekMessagesQueueEmptyNoOptions()
    {
        try {
            $this->restProxy->peekMessages('');
            $this->fail('Expect empty name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testPeekMessagesQueueNullWithOptions()
    {
        try {
            $this->restProxy->peekMessages(null, new PeekMessagesOptions());
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testPeekMessagesOptionsNull()
    {
        try {
            $this->restProxy->peekMessages('abc', null);
            $this->fail('Expect bogus queue name to throw');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_NOT_FOUND, $e->getCode(), 'getCode');
        }
    }

    public function testPeekMessagesAllNull()
    {
        try {
            $this->restProxy->peekMessages(null, null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testClearMessagesQueueNullNoOptions()
    {
        try {
            $this->restProxy->clearMessages(null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testClearMessagesQueueNullWithOptions()
    {
        try {
            $this->restProxy->clearMessages(null, new QueueServiceOptions());
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }

    public function testClearMessagesOptionsNull()
    {
        try {
            $this->restProxy->clearMessages('abc', null);
            $this->fail('Expect bogus queue name to throw');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_NOT_FOUND, $e->getCode(), 'getCode');
        }
    }

    public function testClearMessagesAllNull()
    {
        try {
            $this->restProxy->clearMessages(null, null);
            $this->fail('Expect null name to throw');
        } catch (ServiceException $e) {
            $this->fail('Should not get a service exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'queueName'), $e->getMessage(), 'Expect error message');
        }
    }
}
