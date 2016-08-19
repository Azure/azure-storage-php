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
use MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult;

/**
 * Unit tests for class GetContainerPropertiesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class GetContainerPropertiesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult::getETag
     */
    public function testGetETag()
    {
        // Setup
        $properties = new GetContainerPropertiesResult();
        $expected = '0x8CACB9BD7C6B1B2';
        $properties->setETag($expected);
        
        // Test
        $actual = $properties->getETag();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult::setETag
     */
    public function testSetETag()
    {
        // Setup
        $properties = new GetContainerPropertiesResult();
        $expected = '0x8CACB9BD7C6B1B2';
        
        // Test
        $properties->setETag($expected);
        
        // Assert
        $actual = $properties->getETag();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult::getLastModified
     */
    public function testGetLastModified()
    {
        // Setup
        $properties = new GetContainerPropertiesResult();
        $expected = 'Fri, 09 Oct 2009 21:04:30 GMT';
        $properties->setLastModified($expected);
        
        // Test
        $actual = $properties->getLastModified();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult::setLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $properties = new GetContainerPropertiesResult();
        $expected = 'Fri, 09 Oct 2009 21:04:30 GMT';
        
        // Test
        $properties->setLastModified($expected);
        
        // Assert
        $actual = $properties->getLastModified();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult::setMetadata
     */
    public function testSetMetadata()
    {
        // Setup
        $container = new GetContainerPropertiesResult();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        
        // Test
        $container->setMetadata($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult::getMetadata
     */
    public function testGetMetadata()
    {
        // Setup
        $container = new GetContainerPropertiesResult();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $container->setMetadata($expected);
        
        // Test
        $actual = $container->getMetadata();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}


