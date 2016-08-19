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
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Blob\Models\GetBlobResult;
use MicrosoftAzure\Storage\Blob\Models\BlobProperties;

/**
 * Unit tests for class GetBlobResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class GetBlobResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::setMetadata
     */
    public function testSetMetadata()
    {
        // Setup
        $properties = new GetBlobResult();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        
        // Test
        $properties->setMetadata($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::getMetadata
     */
    public function testGetMetadata()
    {
        // Setup
        $properties = new GetBlobResult();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $properties->setMetadata($expected);
        
        // Test
        $actual = $properties->getMetadata();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::setProperties
     */
    public function testSetProperties()
    {
        // Setup
        $properties = new GetBlobResult();
        $expected = new BlobProperties();
        
        // Test
        $properties->setProperties($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::getProperties
     */
    public function testGetProperties()
    {
        // Setup
        $properties = new GetBlobResult();
        $expected = new BlobProperties();
        $properties->setProperties($expected);
        
        // Test
        $actual = $properties->getProperties();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::setContentStream
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::getContentStream
     */
    public function testSetContentStream()
    {
        // Setup
        $expected = Utilities::stringToStream('0x8CAFB82EFF70C46');
        $result = new GetBlobResult();
        
        // Test
        $result->setContentStream($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getContentStream());
    }
}


