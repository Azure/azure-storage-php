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
use MicrosoftAzure\Storage\Blob\Models\ContainerProperties;

/**
 * Unit tests for class ContainerProperties
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ContainerPropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ContainerProperties::getETag
     */
    public function testGetETag()
    {
        // Setup
        $properties = new ContainerProperties();
        $expected = '0x8CACB9BD7C6B1B2';
        $properties->setETag($expected);
        
        // Test
        $actual = $properties->getETag();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ContainerProperties::setETag
     */
    public function testSetETag()
    {
        // Setup
        $properties = new ContainerProperties();
        $expected = '0x8CACB9BD7C6B1B2';
        
        // Test
        $properties->setETag($expected);
        
        // Assert
        $actual = $properties->getETag();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ContainerProperties::getLastModified
     */
    public function testGetLastModified()
    {
        // Setup
        $properties = new ContainerProperties();
        $expected = 'Fri, 09 Oct 2009 21:04:30 GMT';
        $properties->setLastModified($expected);
        
        // Test
        $actual = $properties->getLastModified();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ContainerProperties::setLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $properties = new ContainerProperties();
        $expected = 'Fri, 09 Oct 2009 21:04:30 GMT';
        
        // Test
        $properties->setLastModified($expected);
        
        // Assert
        $actual = $properties->getLastModified();
        $this->assertEquals($expected, $actual);
    }
}


