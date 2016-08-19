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

/**
 * Unit tests for class QueueMessage
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class QueueMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::getMessageText
     */
    public function testGetMessageText()
    {
        // Setup
        $queueMessage = new QueueMessage();
        $expected = 'this is message text';
        $queueMessage->setMessageText($expected);
        
        // Test
        $actual = $queueMessage->getMessageText();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\QueueMessage::setMessageText
     */
    public function testSetMessageText()
    {
        // Setup
        $queueMessage = new QueueMessage();
        $expected = 'this is message text';
        
        // Test
        $queueMessage->setMessageText($expected);
        
        // Assert
        $actual = $queueMessage->getMessageText();
        $this->assertEquals($expected, $actual);
    }
    
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
        $properties = array(XmlSerializer::ROOT_NAME => QueueMessage::$xmlRootName);
        $expected = $xmlSerializer->serialize($array, $properties);
        
        // Test
        $actual = $queueMessage->toXml($xmlSerializer);
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}


