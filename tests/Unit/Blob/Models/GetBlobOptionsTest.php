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

use MicrosoftAzure\Storage\Common\Models\Range;
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
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getAccessConditions
     */
    public function testGetAccessConditions()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new GetBlobOptions();
        $result->setAccessConditions($expected);
        
        // Test
        $actual = $result->getAccessConditions();
        
        // Assert
        $this->assertEquals($expected, $actual[0]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setAccessConditions
     */
    public function testSetAccessConditions()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new GetBlobOptions();
        
        // Test
        $result->setAccessConditions($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getAccessConditions()[0]);
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
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setRange
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getRange
     */
    public function testSetRange()
    {
        // Setup
        $expected = new Range(0, 123);
        $options = new GetBlobOptions();

        // Test
        $options->setRange($expected);

        // Assert
        $this->assertEquals($expected, $options->getRange());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::setRangeGetContentMD5
     */
    public function testSetRangeGetContentMD5()
    {
        // Setup
        $options = new GetBlobOptions();
        $expected = true;
        
        // Test
        $options->setRangeGetContentMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getRangeGetContentMD5());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobOptions::getRangeGetContentMD5
     */
    public function testGetRangeGetContentMD5()
    {
        // Setup
        $options = new GetBlobOptions();
        $expected = true;
        $options->setRangeGetContentMD5($expected);
        
        // Test
        $actual = $options->getRangeGetContentMD5();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}
