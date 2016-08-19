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
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Common\Internal\InvalidArgumentTypeException;

/**
 * Unit tests for class CreateContainerOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateContainerOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions::getPublicAccess
     */
    public function testGetPublicAccess()
    {
        // Setup
        $properties = new CreateContainerOptions();
        $expected = 'blob';
        $properties->setPublicAccess($expected);
        
        // Test
        $actual = $properties->getPublicAccess();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions::setPublicAccess
     */
    public function testSetPublicAccess()
    {
        // Setup
        $properties = new CreateContainerOptions();
        $expected = 'container';
        
        // Test
        $properties->setPublicAccess($expected);
        
        // Assert
        $actual = $properties->getPublicAccess();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions::setPublicAccess
     */
    public function testSetPublicAccessInvalidValueFail()
    {
        // Setup
        $properties = new CreateContainerOptions();
        $expected = new \DateTime();
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        
        // Test
        $properties->setPublicAccess($expected);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions::setMetadata
     */
    public function testSetMetadata()
    {
        // Setup
        $container = new CreateContainerOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        
        // Test
        $container->setMetadata($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions::getMetadata
     */
    public function testGetMetadata()
    {
        // Setup
        $container = new CreateContainerOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $container->setMetadata($expected);
        
        // Test
        $actual = $container->getMetadata();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions::addMetadata
     */
    public function testAddMetadata()
    {
        // Setup
        $container = new CreateContainerOptions();
        $key = 'key1';
        $value = 'value1';
        $expected = array($key => $value);
        
        // Test
        $container->addMetadata($key, $value);
        
        // Assert
        $this->assertEquals($expected, $container->getMetadata());
    }
}


