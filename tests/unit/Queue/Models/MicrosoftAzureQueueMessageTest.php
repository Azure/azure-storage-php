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
use MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class MicrosoftAzureQueueMessageTest
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class MicrosoftAzureQueueMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::createFromListMessages
    */
    public function testCreateListMessages()
    {
        // Setup
        $sample = TestResources::listMessagesSample();
        $sample = $sample['QueueMessage'];
        
        // Test
        $actual = MicrosoftAzureQueueMessage::createFromListMessages($sample);
        
        // Assert
        $this->assertEquals($sample['MessageId'] , $actual->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['InsertionTime']) , $actual->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['ExpirationTime']) , $actual->getExpirationDate());
        $this->assertEquals($sample['PopReceipt'] , $actual->getPopReceipt());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['TimeNextVisible']), $actual->getTimeNextVisible());
        $this->assertEquals(intval($sample['DequeueCount']) , $actual->getDequeueCount());
        $this->assertEquals($sample['MessageText'] , $actual->getMessageText());
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::createFromPeekMessages
    */
    public function testCreateFromPeekMessages()
    {
        // Setup
        $sample = TestResources::listMessagesSample();
        $sample = $sample['QueueMessage'];
        
        // Test
        $actual = MicrosoftAzureQueueMessage::createFromPeekMessages($sample);
        
        // Assert
        $this->assertEquals($sample['MessageId'] , $actual->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['InsertionTime']) , $actual->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['ExpirationTime']) , $actual->getExpirationDate());
        $this->assertEquals(intval($sample['DequeueCount']) , $actual->getDequeueCount());
        $this->assertEquals($sample['MessageText'] , $actual->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::getMessageText
     */
    public function testGetMessageText()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'PHRlc3Q+dGhpcyBpcyBhIHRlc3QgbWVzc2FnZTwvdGVzdD4=' ;
        $azureQueueMessage->setMessageText($expected);
        
        // Test
        $actual = $azureQueueMessage->getMessageText();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::setMessageText
     */
    public function testSetMessageText()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'PHRlc3Q+dGhpcyBpcyBhIHRlc3QgbWVzc2FnZTwvdGVzdD4=';
        
        // Test
        $azureQueueMessage->setMessageText($expected);
        
        // Assert
        $actual = $azureQueueMessage->getMessageText();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::getMessageId
     */
    public function testGetMessageId()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = '5974b586-0df3-4e2d-ad0c-18e3892bfca2';
        $azureQueueMessage->setMessageId($expected);
        
        // Test
        $actual = $azureQueueMessage->getMessageId();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::setMessageId
     */
    public function testSetMessageId()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = '5974b586-0df3-4e2d-ad0c-18e3892bfca2';
        
        // Test
        $azureQueueMessage->setMessageId($expected);
        
        // Assert
        $actual = $azureQueueMessage->getMessageId();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::getInsertionDate
     */
    public function testGetInsertionDate()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'Fri, 09 Oct 2009 21:04:30 GMT';
        $azureQueueMessage->setInsertionDate($expected);
        
        // Test
        $actual = $azureQueueMessage->getInsertionDate();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::setInsertionDate
     */
    public function testSetInsertionDate()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'Fri, 09 Oct 2009 21:04:30 GMT';
        
        // Test
        $azureQueueMessage->setInsertionDate($expected);
        
        // Assert
        $actual = $azureQueueMessage->getInsertionDate();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::getExpirationDate
     */
    public function testGetExpirationDate()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'Fri, 16 Oct 2009 21:04:30 GMT';
        $azureQueueMessage->setExpirationDate($expected);
        
        // Test
        $actual = $azureQueueMessage->getExpirationDate();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::setExpirationDate
     */
    public function testSetExpirationDate()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'Fri, 16 Oct 2009 21:04:30 GMT';
        
        // Test
        $azureQueueMessage->setExpirationDate($expected);
        
        // Assert
        $actual = $azureQueueMessage->getExpirationDate();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::getPopReceipt
     */
    public function testGetPopReceipt()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'YzQ4Yzg1MDItYTc0Ny00OWNjLTkxYTUtZGM0MDFiZDAwYzEw';
        $azureQueueMessage->setPopReceipt($expected);
        
        // Test
        $actual = $azureQueueMessage->getPopReceipt();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::setPopReceipt
     */
    public function testSetPopReceipt()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'YzQ4Yzg1MDItYTc0Ny00OWNjLTkxYTUtZGM0MDFiZDAwYzEw';
        
        // Test
        $azureQueueMessage->setPopReceipt($expected);
        
        // Assert
        $actual = $azureQueueMessage->getPopReceipt();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::getTimeNextVisible
     */
    public function testGetTimeNextVisible()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'Fri, 09 Oct 2009 23:29:20 GMT';
        $azureQueueMessage->setTimeNextVisible($expected);
        
        // Test
        $actual = $azureQueueMessage->getTimeNextVisible();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::setTimeNextVisible
     */
    public function testSetTimeNextVisible()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 'Fri, 09 Oct 2009 23:29:20 GMT';
        
        // Test
        $azureQueueMessage->setTimeNextVisible($expected);
        
        // Assert
        $actual = $azureQueueMessage->getTimeNextVisible();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::getDequeueCount
     */
    public function testGetDequeueCount()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 1;
        $azureQueueMessage->setDequeueCount($expected);
        
        // Test
        $actual = $azureQueueMessage->getDequeueCount();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage::setDequeueCount
     */
    public function testSetDequeueCount()
    {
        // Setup
        $azureQueueMessage = new MicrosoftAzureQueueMessage();
        $expected = 1;
        
        // Test
        $azureQueueMessage->setDequeueCount($expected);
        
        // Assert
        $actual = $azureQueueMessage->getDequeueCount();
        $this->assertEquals($expected, $actual);
    }
}


