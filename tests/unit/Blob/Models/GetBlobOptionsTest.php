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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\Unit\Blob\Models;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;
use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;

/**
 * Unit tests for class GetBlobOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class GetBlobOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getLeaseId
     */
    public function testSetLeaseId()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new GetBlobOptions();
        $options->setLeaseId($expected);
        
        // Test
        $options->setLeaseId($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getAccessCondition
     */
    public function testGetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new GetBlobOptions();
        $result->setAccessCondition($expected);
        
        // Test
        $actual = $result->getAccessCondition();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setAccessCondition
     */
    public function testSetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new GetBlobOptions();
        
        // Test
        $result->setAccessCondition($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getAccessCondition());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setSnapshot
     */
    public function testSetSnapshot()
    {
        // Setup
        $blob = new GetBlobOptions();
        $expected = TestResources::QUEUE1_NAME;
        
        // Test
        $blob->setSnapshot($expected);
        
        // Assert
        $this->assertEquals($expected, $blob->getSnapshot());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getSnapshot
     */
    public function testGetSnapshot()
    {
        // Setup
        $blob = new GetBlobOptions();
        $expected = TestResources::QUEUE_URI;
        $blob->setSnapshot($expected);
        
        // Test
        $actual = $blob->getSnapshot();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setRangeStart
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getRangeStart
     */
    public function testSetRangeStart()
    {
        // Setup
        $expected = 123;
        $prooperties = new GetBlobOptions();
        $prooperties->setRangeStart($expected);
        
        // Test
        $prooperties->setRangeStart($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getRangeStart());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setRangeEnd
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getRangeEnd
     */
    public function testSetRangeEnd()
    {
        // Setup
        $expected = 123;
        $prooperties = new GetBlobOptions();
        $prooperties->setRangeEnd($expected);
        
        // Test
        $prooperties->setRangeEnd($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getRangeEnd());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setComputeRangeMD5
     */
    public function testSetComputeRangeMD5()
    {
        // Setup
        $options = new GetBlobOptions();
        $expected = true;
        
        // Test
        $options->setComputeRangeMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getComputeRangeMD5());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getComputeRangeMD5
     */
    public function testGetComputeRangeMD5()
    {
        // Setup
        $options = new GetBlobOptions();
        $expected = true;
        $options->setComputeRangeMD5($expected);
        
        // Test
        $actual = $options->getComputeRangeMD5();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}


