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
use MicrosoftAzure\Storage\Blob\Models\Container;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Blob\Models\ContainerProperties;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class Container
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\Container::setName
     */
    public function testSetName()
    {
        // Setup
        $container = new Container();
        $expected = TestResources::QUEUE1_NAME;
        
        // Test
        $container->setName($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getName());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\Container::getName
     */
    public function testGetName()
    {
        // Setup
        $container = new Container();
        $expected = TestResources::QUEUE1_NAME;
        $container->setName($expected);
        
        // Test
        $actual = $container->getName();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\Container::setUrl
     */
    public function testSetUrl()
    {
        // Setup
        $container = new Container();
        $expected = TestResources::QUEUE1_NAME;
        
        // Test
        $container->setUrl($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getUrl());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\Container::getUrl
     */
    public function testGetUrl()
    {
        // Setup
        $container = new Container();
        $expected = TestResources::QUEUE_URI;
        $container->setUrl($expected);
        
        // Test
        $actual = $container->getUrl();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\Container::setMetadata
     */
    public function testSetMetadata()
    {
        // Setup
        $container = new Container();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        
        // Test
        $container->setMetadata($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\Container::getMetadata
     */
    public function testGetMetadata()
    {
        // Setup
        $container = new Container();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $container->setMetadata($expected);
        
        // Test
        $actual = $container->getMetadata();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\Container::setProperties
     */
    public function testSetProperties()
    {
        // Setup
        $date = Utilities::rfc1123ToDateTime('Wed, 12 Aug 2009 20:39:39 GMT');
        $container = new Container();
        $expected = new ContainerProperties();
        $expected->setETag('0x8CACB9BD7C1EEEC');
        $expected->setLastModified($date);
        
        // Test
        $container->setProperties($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\Container::getProperties
     */
    public function testGetProperties()
    {
        // Setup
        $date = Utilities::rfc1123ToDateTime('Wed, 12 Aug 2009 20:39:39 GMT');
        $container = new Container();
        $expected = new ContainerProperties();
        $expected->setETag('0x8CACB9BD7C1EEEC');
        $expected->setLastModified($date);
        $container->setProperties($expected);
        
        // Test
        $actual = $container->getProperties();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}


