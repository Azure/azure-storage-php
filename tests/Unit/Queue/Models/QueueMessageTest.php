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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Queue\Models;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Queue\Models\QueueMessage;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class QueueMessage
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueueMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::toXml
     */
    public function testToXml()
    {
        // Setup
        $queueMessage = new QueueMessage();
        $messageText = 'this is message text';
        $array = array('MessageText' => $messageText);
        $queueMessage->setMessageText($messageText);
        $xmlSerializer = new XmlSerializer();
        $properties = array(XmlSerializer::ROOT_NAME => "QueueMessage");
        $expected = $xmlSerializer->serialize($array, $properties);
        
        // Test
        $actual = $queueMessage->toXml($xmlSerializer);
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::createFromListMessages
    */
    public function testCreateListMessages()
    {
        // Setup
        $sample = TestResources::listMessagesSample();
        $sample = $sample['QueueMessage'];
        
        // Test
        $actual = QueueMessage::createFromListMessages($sample);
        
        // Assert
        $this->assertEquals($sample['MessageId'], $actual->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['InsertionTime']), $actual->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['ExpirationTime']), $actual->getExpirationDate());
        $this->assertEquals($sample['PopReceipt'], $actual->getPopReceipt());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['TimeNextVisible']), $actual->getTimeNextVisible());
        $this->assertEquals(intval($sample['DequeueCount']), $actual->getDequeueCount());
        $this->assertEquals($sample['MessageText'], $actual->getMessageText());
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::createFromPeekMessages
    */
    public function testCreateFromPeekMessages()
    {
        // Setup
        $sample = TestResources::listMessagesSample();
        $sample = $sample['QueueMessage'];
        
        // Test
        $actual = QueueMessage::createFromPeekMessages($sample);
        
        // Assert
        $this->assertEquals($sample['MessageId'], $actual->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['InsertionTime']), $actual->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['ExpirationTime']), $actual->getExpirationDate());
        $this->assertEquals(intval($sample['DequeueCount']), $actual->getDequeueCount());
        $this->assertEquals($sample['MessageText'], $actual->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::getMessageText
     */
    public function testGetMessageText()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = 'PHRlc3Q+dGhpcyBpcyBhIHRlc3QgbWVzc2FnZTwvdGVzdD4=' ;
        $azureQueueMessage->setMessageText($expected);
        
        // Test
        $actual = $azureQueueMessage->getMessageText();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::setMessageText
     */
    public function testSetMessageText()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = 'PHRlc3Q+dGhpcyBpcyBhIHRlc3QgbWVzc2FnZTwvdGVzdD4=';
        
        // Test
        $azureQueueMessage->setMessageText($expected);
        
        // Assert
        $actual = $azureQueueMessage->getMessageText();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::getMessageId
     */
    public function testGetMessageId()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = '5974b586-0df3-4e2d-ad0c-18e3892bfca2';
        $azureQueueMessage->setMessageId($expected);
        
        // Test
        $actual = $azureQueueMessage->getMessageId();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::setMessageId
     */
    public function testSetMessageId()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = '5974b586-0df3-4e2d-ad0c-18e3892bfca2';
        
        // Test
        $azureQueueMessage->setMessageId($expected);
        
        // Assert
        $actual = $azureQueueMessage->getMessageId();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::getInsertionDate
     */
    public function testGetInsertionDate()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = new \DateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        $azureQueueMessage->setInsertionDate($expected);
        
        // Test
        $actual = $azureQueueMessage->getInsertionDate();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::setInsertionDate
     */
    public function testSetInsertionDate()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = new \DateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        
        // Test
        $azureQueueMessage->setInsertionDate($expected);
        
        // Assert
        $actual = $azureQueueMessage->getInsertionDate();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::getExpirationDate
     */
    public function testGetExpirationDate()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = new \DateTime('Fri, 16 Oct 2009 21:04:30 GMT');
        $azureQueueMessage->setExpirationDate($expected);
        
        // Test
        $actual = $azureQueueMessage->getExpirationDate();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::setExpirationDate
     */
    public function testSetExpirationDate()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = new \DateTime('Fri, 16 Oct 2009 21:04:30 GMT');
        
        // Test
        $azureQueueMessage->setExpirationDate($expected);
        
        // Assert
        $actual = $azureQueueMessage->getExpirationDate();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::getPopReceipt
     */
    public function testGetPopReceipt()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = 'YzQ4Yzg1MDItYTc0Ny00OWNjLTkxYTUtZGM0MDFiZDAwYzEw';
        $azureQueueMessage->setPopReceipt($expected);
        
        // Test
        $actual = $azureQueueMessage->getPopReceipt();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::setPopReceipt
     */
    public function testSetPopReceipt()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = 'YzQ4Yzg1MDItYTc0Ny00OWNjLTkxYTUtZGM0MDFiZDAwYzEw';
        
        // Test
        $azureQueueMessage->setPopReceipt($expected);
        
        // Assert
        $actual = $azureQueueMessage->getPopReceipt();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::getTimeNextVisible
     */
    public function testGetTimeNextVisible()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = 'Fri, 09 Oct 2009 23:29:20 GMT';
        $azureQueueMessage->setTimeNextVisible($expected);
        
        // Test
        $actual = $azureQueueMessage->getTimeNextVisible();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::setTimeNextVisible
     */
    public function testSetTimeNextVisible()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = 'Fri, 09 Oct 2009 23:29:20 GMT';
        
        // Test
        $azureQueueMessage->setTimeNextVisible($expected);
        
        // Assert
        $actual = $azureQueueMessage->getTimeNextVisible();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::getDequeueCount
     */
    public function testGetDequeueCount()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = 1;
        $azureQueueMessage->setDequeueCount($expected);
        
        // Test
        $actual = $azureQueueMessage->getDequeueCount();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::setDequeueCount
     */
    public function testSetDequeueCount()
    {
        // Setup
        $azureQueueMessage = new QueueMessage();
        $expected = 1;
        
        // Test
        $azureQueueMessage->setDequeueCount($expected);
        
        // Assert
        $actual = $azureQueueMessage->getDequeueCount();
        $this->assertEquals($expected, $actual);
    }
}
