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
use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class ListPageBlobRangesOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ListPageBlobRangesOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::getLeaseId
     */
    public function testSetLeaseId()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new ListPageBlobRangesOptions();
        $options->setLeaseId($expected);
        
        // Test
        $options->setLeaseId($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::getAccessCondition
     */
    public function testGetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new ListPageBlobRangesOptions();
        $result->setAccessCondition($expected);
        
        // Test
        $actual = $result->getAccessCondition();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::setAccessCondition
     */
    public function testSetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new ListPageBlobRangesOptions();
        
        // Test
        $result->setAccessCondition($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getAccessCondition());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::setSnapshot
     */
    public function testSetSnapshot()
    {
        // Setup
        $blob = new ListPageBlobRangesOptions();
        $expected = TestResources::QUEUE1_NAME;
        
        // Test
        $blob->setSnapshot($expected);
        
        // Assert
        $this->assertEquals($expected, $blob->getSnapshot());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::getSnapshot
     */
    public function testGetSnapshot()
    {
        // Setup
        $blob = new ListPageBlobRangesOptions();
        $expected = TestResources::QUEUE_URI;
        $blob->setSnapshot($expected);
        
        // Test
        $actual = $blob->getSnapshot();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::setRangeStart
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::getRangeStart
     */
    public function testSetRangeStart()
    {
        // Setup
        $expected = 123;
        $prooperties = new ListPageBlobRangesOptions();
        $prooperties->setRangeStart($expected);
        
        // Test
        $prooperties->setRangeStart($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getRangeStart());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::setRangeEnd
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions::getRangeEnd
     */
    public function testSetRangeEnd()
    {
        // Setup
        $expected = 123;
        $prooperties = new ListPageBlobRangesOptions();
        $prooperties->setRangeEnd($expected);
        
        // Test
        $prooperties->setRangeEnd($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getRangeEnd());
    }
}


