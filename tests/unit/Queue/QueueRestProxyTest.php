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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\unit\Queue;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Tests\Framework\QueueServiceRestProxyTestBase;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Queue\Models\ListQueuesOptions;
use MicrosoftAzure\Storage\Queue\Models\ListQueuesResult;
use MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions;
use MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesResult;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesResult;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult;
use MicrosoftAzure\Storage\Queue\Models\QueueServiceOptions;
use MicrosoftAzure\Storage\Queue\Models\QueueACL;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

/**
 * Unit tests for QueueRestProxy class
 *
 * @package    MicrosoftAzure\Storage\Tests\Unit\Queue
 * @author     Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright  2016 Microsoft Corporation
 * @license    https://github.com/azure/azure-storage-php/LICENSE
 * @link       https://github.com/azure/azure-storage-php
 */
class QueueRestProxyTest extends QueueServiceRestProxyTestBase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listQueues
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::send
     */
    public function testListQueuesSimple()
    {
        // Setup
        $queue1 = 'listqueuesimple1';
        $queue2 = 'listqueuesimple2';
        $queue3 = 'listqueuesimple3';

        parent::createQueue($queue1);
        parent::createQueue($queue2);
        parent::createQueue($queue3);
        
        // Test
        $result = $this->restProxy->listQueues();

        // Assert
        $queues = $result->getQueues();
        $this->assertNotNull($result->getAccountName());
        $this->assertEquals($queue1, $queues[0]->getName());
        $this->assertEquals($queue2, $queues[1]->getName());
        $this->assertEquals($queue3, $queues[2]->getName());
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listQueues
     */
    public function testListQueuesWithOptions()
    {
        // Setup
        $queue1        = 'listqueuewithoptions1';
        $queue2        = 'listqueuewithoptions2';
        $queue3        = 'mlistqueuewithoptions3';
        $metadataName  = 'Mymetadataname';
        $metadataValue = 'MetadataValue';
        $options = new CreateQueueOptions();
        $options->addMetadata($metadataName, $metadataValue);
        parent::createQueue($queue1);
        parent::createQueue($queue2, $options);
        parent::createQueue($queue3);
        $options = new ListQueuesOptions();
        $options->setPrefix('list');
        $options->setIncludeMetadata(true);
        
        // Test
        $result = $this->restProxy->listQueues($options);
        
        // Assert
        $queues   = $result->getQueues();
        $metadata = $queues[1]->getMetadata();
        $this->assertEquals(2, count($queues));
        $this->assertEquals($queue1, $queues[0]->getName());
        $this->assertEquals($queue2, $queues[1]->getName());
        $this->assertEquals($metadataValue, $metadata[$metadataName]);
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listQueues
     */
    public function testListQueuesWithNextMarker()
    {
        // Setup
        $queue1 = 'listqueueswithnextmarker1';
        $queue2 = 'listqueueswithnextmarker2';
        $queue3 = 'listqueueswithnextmarker3';
        parent::createQueue($queue1);
        parent::createQueue($queue2);
        parent::createQueue($queue3);
        $options = new ListQueuesOptions();
        $options->setMaxResults(2);
        
        // Test
        $result = $this->restProxy->listQueues($options);
        
        // Assert
        $queues = $result->getQueues();
        $this->assertEquals(2, count($queues));
        $this->assertEquals($queue1, $queues[0]->getName());
        $this->assertEquals($queue2, $queues[1]->getName());
        
        // Test
        $options->setMarker($result->getNextMarker());
        $result = $this->restProxy->listQueues($options);
        $queues = $result->getQueues();

        // Assert
        $this->assertEquals(1, count($queues));
        $this->assertEquals($queue3, $queues[0]->getName());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listQueues
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage 400
     */
    public function testListQueuesWithInvalidNextMarkerFail()
    {
        $this->skipIfEmulated();
        
        // Setup
        $queue1 = 'listqueueswithinvalidnextmarker1';
        $queue2 = 'listqueueswithinvalidnextmarker2';
        $queue3 = 'listqueueswithinvalidnextmarker3';
        parent::createQueue($queue1);
        parent::createQueue($queue2);
        parent::createQueue($queue3);
        $options = new ListQueuesOptions();
        $options->setMaxResults(2);
        
        // Test
        $this->restProxy->listQueues($options);
        $options->setMarker('wrong marker');
        $this->restProxy->listQueues($options);
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listQueues
     */
    public function testListQueuesWithNoQueues()
    {
        // Test
        $result = $this->restProxy->listQueues();
        
        // Assert
        $queues = $result->getQueues();
        $this->assertTrue(empty($queues));
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listQueues
     */
    public function testListQueuesWithOneResult()
    {
        // Setup
        $queueName = 'listqueueswithoneresult';
        parent::createQueue($queueName);
        
        // Test
        $result = $this->restProxy->listQueues();
        $queues = $result->getQueues();

        // Assert
        $this->assertEquals(1, count($queues));
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::createQueue
     */
    public function testCreateQueueSimple()
    {
        // Setup
        $queueName = 'createqueuesimple';
        
        // Test
        $this->createQueue($queueName);
        
        // Assert
        $result = $this->restProxy->listQueues();
        $queues = $result->getQueues();
        $this->assertEquals(1, count($queues));
        $this->assertEquals($queues[0]->getName(), $queueName);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::createQueue
     */
    public function testCreateQueueWithExistingQueue()
    {
        // Setup
        $queueName = 'createqueuewithexistingqueue';
        $this->createQueue($queueName);
        
        // Test
        $this->createQueue($queueName);
        
        // Assert
        $result = $this->restProxy->listQueues();
        $queues = $result->getQueues();
        $this->assertEquals(1, count($queues));
        $this->assertEquals($queues[0]->getName(), $queueName);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::createQueue
     */
    public function testCreateQueueWithMetadata()
    {
        $queueName     = 'createqueuewithmetadata';
        $metadataName  = 'Name';
        $metadataValue = 'MyName';
        $queueCreateOptions = new CreateQueueOptions();
        $queueCreateOptions->addMetadata($metadataName, $metadataValue);
        
        // Test
        $this->createQueue($queueName, $queueCreateOptions);

        // Assert
        $options = new ListQueuesOptions();
        $options->setIncludeMetadata(true);
        $result   = $this->restProxy->listQueues($options);
        $queues   = $result->getQueues();
        $metadata = $queues[0]->getMetadata();
        $this->assertEquals($metadataValue, $metadata[$metadataName]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::createQueue
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage 400
     */
    public function testCreateQueueInvalidNameFail()
    {
        // Setup
        $queueName = 'CreateQueueInvalidNameFail';
        
        // Test
        $this->createQueue($queueName);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::deleteQueue
     */
    public function testDeleteQueue()
    {
        // Setup
        $queueName = 'deletequeue';
        $this->restProxy->createQueue($queueName);
        
        // Test
        $this->restProxy->deleteQueue($queueName);
        
        // Assert
        $result = $this->restProxy->listQueues();
        $queues = $result->getQueues();
        $this->assertTrue(empty($queues));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::deleteQueue
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage 404
     */
    public function testDeleteQueueFail()
    {
        // Setup
        $queueName = 'deletequeuefail';
        
        // Test
        $this->restProxy->deleteQueue($queueName);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::getServiceProperties
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::setServiceProperties
     */
    public function testSetServiceProperties()
    {
        $this->skipIfEmulated();
        
        // Setup
        $expected = ServiceProperties::create(TestResources::setServicePropertiesSample());
        
        // Test
        $this->setServiceProperties($expected);
        //Add 30s interval to wait for setting to take effect.
        \sleep(30);
        $actual = $this->restProxy->getServiceProperties();
        
        // Assert
        $this->assertEquals($expected->toXml($this->xmlSerializer), $actual->getValue()->toXml($this->xmlSerializer));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::getQueueMetadata
     */
    public function testGetQueueMetadata()
    {
        // Setup
        $name     = 'getqueuemetadata';
        $expectedCount = 0;
        $options  = new CreateQueueOptions();
        $expected = array('name1' => 'MyName1', 'mymetaname' => '12345', 'values' => 'Microsoft_');
        $options->setMetadata($expected);
        $this->createQueue($name, $options);
        
        // Test
        $result = $this->restProxy->getQueueMetadata($name);
        
        // Assert
        $this->assertEquals($expectedCount, $result->getApproximateMessageCount());
        $this->assertEquals($expected, $result->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::setQueueMetadata
     */
    public function testSetQueueMetadata()
    {
        // Setup
        $name = 'setqueuemetadata';
        $expected = array('name1' => 'MyName1', 'mymetaname' => '12345', 'values' => 'Microsoft_');
        $this->createQueue($name);
        
        // Test
        $this->restProxy->setQueueMetadata($name, $expected);
        $actual = $this->restProxy->getQueueMetadata($name);
        
        // Assert
        $this->assertEquals($expected, $actual->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::createMessage
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::send
     */
    public function testCreateMessage()
    {
        // Setup
        $name = 'createmessage';
        $expected = 'this is message text';
        $this->createQueue($name);
        
        // Test
        $this->restProxy->createMessage($name, $expected);
        
        // Assert
        $result = $this->restProxy->listMessages($name);
        $messages = $result->getQueueMessages();
        $actual = $messages[0]->getMessageText();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listMessages
     */
    public function testListMessagesEmpty()
    {
        // Setup
        $name = 'listmessagesempty';
        $this->createQueue($name);

        // Test
        $result = $this->restProxy->listMessages($name);
        
        // Assert
        $actual = $result->getQueueMessages();
        $this->assertEmpty($actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listMessages
     */
    public function testListMessagesOneMessage()
    {
        // Setup
        $name = 'listmessagesonemessage';
        $this->createQueue($name);
        $expected = 'Message text';
        $this->restProxy->createMessage($name, $expected);
        
        // Test
        $result = $this->restProxy->listMessages($name);
        
        // Assert
        $messages = $result->getQueueMessages();
        $actual = $messages[0];
        $this->assertCount(1, $messages);
        $this->assertEquals($expected, $actual->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listMessages
     */
    public function testListMessagesCreateMultiplesReturnOne()
    {
        // Setup
        $name = 'listmessagescreatemultiplesreturnone';
        $this->createQueue($name);
        $expected1 = 'Message #1 Text';
        $message2 = 'Message #2 Text';
        $message3 = 'Message #3 Text';
        $this->restProxy->createMessage($name, $expected1);
        $this->restProxy->createMessage($name, $message2);
        $this->restProxy->createMessage($name, $message3);
        
        // Test
        $result = $this->restProxy->listMessages($name);
        
        // Assert
        $actual = $result->getQueueMessages();
        $this->assertCount(1, $actual);
        $this->assertEquals($expected1, $actual[0]->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::listMessages
     */
    public function testListMessagesMultiplesMessages()
    {
        // Setup
        $name = 'listmessagesmultiplesmessages';
        $this->createQueue($name);
        $expected1 = 'Message #1 Text';
        $expected2 = 'Message #2 Text';
        $expected3 = 'Message #3 Text';
        $this->restProxy->createMessage($name, $expected1);
        $this->restProxy->createMessage($name, $expected2);
        $this->restProxy->createMessage($name, $expected3);
        $options = new ListMessagesOptions();
        $options->setNumberOfMessages(10);
        
        // Test
        $result = $this->restProxy->listMessages($name, $options);
        
        // Assert
        $actual = $result->getQueueMessages();
        $this->assertCount(3, $actual);
        $this->assertEquals($expected1, $actual[0]->getMessageText());
        $this->assertEquals($expected2, $actual[1]->getMessageText());
        $this->assertEquals($expected3, $actual[2]->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::peekMessages
     */
    public function testPeekMessagesEmpty()
    {
        // Setup
        $name = 'peekmessagesempty';
        $this->createQueue($name);

        // Test
        $result = $this->restProxy->peekMessages($name);
        
        // Assert
        $actual = $result->getQueueMessages();
        $this->assertEmpty($actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::peekMessages
     */
    public function testPeekMessagesOneMessage()
    {
        // Setup
        $name = 'peekmessagesonemessage';
        $this->createQueue($name);
        $expected = 'Message text';
        $this->restProxy->createMessage($name, $expected);
        
        // Test
        $result = $this->restProxy->peekMessages($name);
        
        // Assert
        $messages = $result->getQueueMessages();
        $actual = $messages[0];
        $this->assertCount(1, $messages);
        $this->assertEquals($expected, $actual->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::peekMessages
     */
    public function testPeekMessagesCreateMultiplesReturnOne()
    {
        // Setup
        $name = 'peekmessagescreatemultiplesreturnone';
        $this->createQueue($name);
        $expected1 = 'Message #1 Text';
        $message2 = 'Message #2 Text';
        $message3 = 'Message #3 Text';
        $this->restProxy->createMessage($name, $expected1);
        $this->restProxy->createMessage($name, $message2);
        $this->restProxy->createMessage($name, $message3);
        
        // Test
        $result = $this->restProxy->peekMessages($name);
        
        // Assert
        $actual = $result->getQueueMessages();
        $this->assertCount(1, $actual);
        $this->assertEquals($expected1, $actual[0]->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::peekMessages
     */
    public function testPeekMessagesMultiplesMessages()
    {
        // Setup
        $name = 'peekmessagesmultiplesmessages';
        $this->createQueue($name);
        $expected1 = 'Message #1 Text';
        $expected2 = 'Message #2 Text';
        $expected3 = 'Message #3 Text';
        $this->restProxy->createMessage($name, $expected1);
        $this->restProxy->createMessage($name, $expected2);
        $this->restProxy->createMessage($name, $expected3);
        $options = new PeekMessagesOptions();
        $options->setNumberOfMessages(10);
        
        // Test
        $result = $this->restProxy->peekMessages($name, $options);
        
        // Assert
        $actual = $result->getQueueMessages();
        $this->assertCount(3, $actual);
        $this->assertEquals($expected1, $actual[0]->getMessageText());
        $this->assertEquals($expected2, $actual[1]->getMessageText());
        $this->assertEquals($expected3, $actual[2]->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::deleteMessage
     */
    public function testDeleteMessage()
    {
        // Setup
        $name = 'deletemessage';
        $expected = 'this is message text';
        $this->createQueue($name);
        $this->restProxy->createMessage($name, $expected);
        $result = $this->restProxy->listMessages($name);
        $messages   = $result->getQueueMessages();
        $messageId  = $messages[0]->getMessageId();
        $popReceipt = $messages[0]->getPopReceipt();
        
        // Test
        $this->restProxy->deleteMessage($name, $messageId, $popReceipt);
        
        // Assert
        $result   = $this->restProxy->listMessages($name);
        $messages = $result->getQueueMessages();
        $this->assertTrue(empty($messages));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::clearMessages
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::send
     */
    public function testClearMessagesWithOptions()
    {
        // Setup
        $name = 'clearmessageswithoptions';
        $msg1 = 'message #1';
        $msg2 = 'message #2';
        $msg3 = 'message #3';
        $options = new QueueServiceOptions();
        $options->setTimeout('10');
        $this->createQueue($name);
        $this->restProxy->createMessage($name, $msg1);
        $this->restProxy->createMessage($name, $msg2);
        $this->restProxy->createMessage($name, $msg3);
        
        // Test
        $this->restProxy->clearMessages($name, $options);
        
        // Assert
        $result   = $this->restProxy->listMessages($name);
        $messages = $result->getQueueMessages();
        $this->assertTrue(empty($messages));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::clearMessages
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::send
     */
    public function testClearMessages()
    {
        // Setup
        $name = 'clearmessages';
        $msg1 = 'message #1';
        $msg2 = 'message #2';
        $msg3 = 'message #3';
        $this->createQueue($name);
        $this->restProxy->createMessage($name, $msg1);
        $this->restProxy->createMessage($name, $msg2);
        $this->restProxy->createMessage($name, $msg3);
        
        // Test
        $this->restProxy->clearMessages($name);
        
        // Assert
        $result   = $this->restProxy->listMessages($name);
        $messages = $result->getQueueMessages();
        $this->assertTrue(empty($messages));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::updateMessage
     */
    public function testUpdateMessage()
    {
        // Setup
        $name = 'updatemessage';
        $expectedText = 'this is message text';
        $expectedVisibility = 10;
        $this->createQueue($name);
        $this->restProxy->createMessage($name, 'Text to change');
        $result = $this->restProxy->listMessages($name);
        $messages   = $result->getQueueMessages();
        $popReceipt = $messages[0]->getPopReceipt();
        $messageId = $messages[0]->getMessageId();
        
        // Test
        $result = $this->restProxy->UpdateMessage(
            $name,
            $messageId,
            $popReceipt,
            $expectedText,
            $expectedVisibility
        );
        
        // Assert
        $result   = $this->restProxy->listMessages($name);
        $messages = $result->getQueueMessages();
        $this->assertTrue(empty($messages));
        
        sleep($expectedVisibility);
        
        $result   = $this->restProxy->listMessages($name);
        $messages = $result->getQueueMessages();
        $actual   = $messages[0];
        $this->assertEquals($expectedText, $actual->getMessageText());
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::getQueueAcl
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::getQueueAclAsync
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::setQueueAcl
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::setQueueAclAsync
     */
    public function testGetSetQueueAcl()
    {
        // Setup
        $name = 'testgetsetqueueacl';
        $this->createQueue($name);
        $sample = TestResources::getQueueACLMultipleEntriesSample();
        $acl = QueueACL::create($sample['SignedIdentifiers']);
        //because the time is randomized, this should create a different instance
        $negativeSample = TestResources::getQueueACLMultipleEntriesSample();
        $negative = QueueACL::create($negativeSample['SignedIdentifiers']);

        // Test
        $this->restProxy->setQueueAcl($name, $acl);
        $resultAcl = $this->restProxy->getQueueAcl($name);

        $this->assertEquals(
            $acl->getSignedIdentifiers(),
            $resultAcl->getSignedIdentifiers()
        );

        $this->assertFalse(
            $resultAcl->getSignedIdentifiers() == $negative->getSignedIdentifiers(),
            'Should not equal to the negative test case'
        );
    }

    /**
     * @covers  \MicrosoftAzure\Storage\Queue\QueueRestProxy::getServiceStats
     * @covers  \MicrosoftAzure\Storage\Queue\QueueRestProxy::getServiceStatsAsync
     */
    public function testGetServiceStats()
    {
        $result = $this->restProxy->getServiceStats();

        // Assert
        $this->assertNotNull($result->getStatus());
        $this->assertNotNull($result->getLastSyncTime());
        $this->assertTrue($result->getLastSyncTime() instanceof \DateTime);
    }
}
