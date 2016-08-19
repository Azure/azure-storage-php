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
use MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult;

/**
 * Unit tests for class GetQueueMetadataResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class GetQueueMetadataResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult::__construct
     */
    public function test__construct()
    {
        // Setup
        $count = 10;
        $metadata = array('key1' => 'value1', 'key2' => 'value2');
        
        // Test
        $actual = new GetQueueMetadataResult($count, $metadata);
        
        // Assert
        $this->assertEquals($count, $actual->getApproximateMessageCount());
        $this->assertEquals($metadata, $actual->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult::getApproximateMessageCount
     */
    public function testGetApproximateMessageCount()
    {
        // Setup
        $expected = 10;
        $metadata = new GetQueueMetadataResult($expected, array());
        
        // Test
        $actual = $metadata->getApproximateMessageCount();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult::setApproximateMessageCount
     */
    public function testSetApproximateMessageCount()
    {
        // Setup
        $expected = 10;
        $metadata = new GetQueueMetadataResult(30, array());
        
        // Test
        $metadata->setApproximateMessageCount($expected);
        
        // Assert
        $this->assertEquals($expected, $metadata->getApproximateMessageCount());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult::getMetadata
     */
    public function testGetMetadata()
    {
        // Setup
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $metadata = new GetQueueMetadataResult(0, $expected);
        
        // Test
        $actual = $metadata->getMetadata();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult::setMetadata
     */
    public function testSetMetadata()
    {
        // Setup
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $metadata = new GetQueueMetadataResult(0, $expected);
        
        // Test
        $metadata->setMetadata($expected);
        
        // Assert
        $this->assertEquals($expected, $metadata->getMetadata());
    }
}


