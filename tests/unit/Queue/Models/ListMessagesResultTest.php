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
use MicrosoftAzure\Storage\Queue\Models\ListMessagesResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class ListMessagesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ListMessagesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListMessagesResult::create
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listMessagesSample();
        
        
        // Test
        $result = ListMessagesResult::create($sample);
        
        // Assert
        $actual = $result->getQueueMessages();
        $this->assertCount(1, $actual);
        $this->assertEquals($sample['QueueMessage']['MessageId'] , $actual[0]->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage']['InsertionTime']) , $actual[0]->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage']['ExpirationTime']) , $actual[0]->getExpirationDate());
        $this->assertEquals($sample['QueueMessage']['PopReceipt'] , $actual[0]->getPopReceipt());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage']['TimeNextVisible']), $actual[0]->getTimeNextVisible());
        $this->assertEquals(intval($sample['QueueMessage']['DequeueCount']) , $actual[0]->getDequeueCount());
        $this->assertEquals($sample['QueueMessage']['MessageText'] , $actual[0]->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListMessagesResult::create
     */
    public function testCreateMultiple()
    {
        // Setup
        $sample = TestResources::listMessagesMultipleMessagesSample();
        
        // Test
        $result = ListMessagesResult::create($sample);
        
        // Assert
        $actual = $result->getQueueMessages();
        $this->assertCount(2, $actual);
        $this->assertEquals($sample['QueueMessage'][0]['MessageId'] , $actual[0]->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][0]['InsertionTime']) , $actual[0]->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][0]['ExpirationTime']) , $actual[0]->getExpirationDate());
        $this->assertEquals($sample['QueueMessage'][0]['PopReceipt'] , $actual[0]->getPopReceipt());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][0]['TimeNextVisible']), $actual[0]->getTimeNextVisible());
        $this->assertEquals(intval($sample['QueueMessage'][0]['DequeueCount']) , $actual[0]->getDequeueCount());
        $this->assertEquals($sample['QueueMessage'][0]['MessageText'] , $actual[0]->getMessageText());
        
        $this->assertEquals($sample['QueueMessage'][1]['MessageId'] , $actual[1]->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][1]['InsertionTime']) , $actual[1]->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][1]['ExpirationTime']) , $actual[1]->getExpirationDate());
        $this->assertEquals($sample['QueueMessage'][1]['PopReceipt'] , $actual[1]->getPopReceipt());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][1]['TimeNextVisible']), $actual[1]->getTimeNextVisible());
        $this->assertEquals(intval($sample['QueueMessage'][1]['DequeueCount']) , $actual[1]->getDequeueCount());
        $this->assertEquals($sample['QueueMessage'][1]['MessageText'] , $actual[1]->getMessageText());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListMessagesResult::getQueueMessages
     */
    public function testGetQueueMessages()
    {
        // Setup
        $sample = TestResources::listMessagesSample();
        $expectedMessageId = '1234b585-0ac3-4e2a-ad0c-18e3992brca1';
        $result = ListMessagesResult::create($sample);
        $expected = $result->getQueueMessages();
        $expected[0]->setMessageId($expectedMessageId);
        $result->setQueueMessages($expected);
        
        // Test
        $actual = $result->getQueueMessages();
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListMessagesResult::setQueueMessages
     */
    public function testSetQueueMessages()
    {
        // Setup
        $sample = TestResources::listMessagesSample();
        $expectedMessageId = '1234b585-0ac3-4e2a-ad0c-18e3992brca1';
        $result = ListMessagesResult::create($sample);
        $expected = $result->getQueueMessages();
        $expected[0]->setMessageId($expectedMessageId);
        
        // Test
        $result->setQueueMessages($expected);
        
        $this->assertEquals($expected, $result->getQueueMessages());
    }
}


