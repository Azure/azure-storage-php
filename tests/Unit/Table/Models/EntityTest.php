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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\Unit\Table\Models;

use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\Models\Property;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class Entity
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::getPropertyValue
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::setPropertyValue
     */
    public function testGetPropertyValue()
    {
        // Setup
        $entity = new Entity();
        $name = 'name';
        $edmType = EdmType::STRING;
        $value = 'MyName';
        $expected = 'MyNewName';
        $entity->addProperty($name, $edmType, $value);
        $entity->setPropertyValue($name, $expected);
        
        // Test
        $actual = $entity->getPropertyValue($name);
        
        // Assert
        $this->assertEquals($actual, $expected);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::setETag
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::getETag
     */
    public function testSetETag()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $entity = new Entity();
        $entity->setETag($expected);
        
        // Test
        $entity->setETag($expected);
        
        // Assert
        $this->assertEquals($expected, $entity->getETag());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::setPartitionKey
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::getPartitionKey
     */
    public function testSetPartitionKey()
    {
        // Setup
        $entity = new Entity();
        $expected = '1234';
        
        // Test
        $entity->setPartitionKey($expected);
        
        // Assert
        $this->assertEquals($expected, $entity->getPartitionKey());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::setRowKey
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::getRowKey
     */
    public function testSetRowKey()
    {
        // Setup
        $entity = new Entity();
        $expected = '1234';
        
        // Test
        $entity->setRowKey($expected);
        
        // Assert
        $this->assertEquals($expected, $entity->getRowKey());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::setTimestamp
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::getTimestamp
     */
    public function testSetTimestamp()
    {
        // Setup
        $entity = new Entity();
        $expected = new \DateTime();
        
        // Test
        $entity->setTimestamp($expected);
        
        // Assert
        $this->assertEquals($expected, $entity->getTimestamp());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::setProperties
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::getProperties
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::_validateProperties
     */
    public function testSetProperties()
    {
        // Setup
        $entity = new Entity();
        $expected = array('name' => new Property(EdmType::STRING, null));
        
        // Test
        $entity->setProperties($expected);
        
        // Assert
        $this->assertEquals($expected, $entity->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::getProperty
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::setProperty
     */
    public function testSetProperty()
    {
        // Setup
        $entity = new Entity();
        $expected = new Property();
        $name = 'test';
        
        // Test
        $entity->setProperty($name, $expected);
        
        // Assert
        $this->assertEquals($expected, $entity->getProperty($name));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::addProperty
     */
    public function testAddProperty()
    {
        // Setup
        $entity = new Entity();
        $name = 'test';
        $expected = new Property();
        $edmType = EdmType::STRING;
        $value = '01231232290234210';
        $expected->setEdmType($edmType);
        $expected->setValue($value);
        
        // Test
        $entity->addProperty($name, $edmType, $value);
        
        // Assert
        $this->assertEquals($expected, $entity->getProperty($name));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::isValid
     */
    public function testIsValid()
    {
        // Setup
        $entity = new Entity();
        $entity->setPartitionKey('123');
        $entity->setRowKey('456');
        
        // Assert
        $actual = $entity->isValid();
        
        // Assert
        $this->assertTrue($actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::isValid
     */
    public function testIsValidWithInvalid()
    {
        // Setup
        $entity = new Entity();
        
        // Assert
        $actual = $entity->isValid();
        
        // Assert
        $this->assertFalse($actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Entity::isValid
     */
    public function testIsValidWithEmptyPartitionKey()
    {
        // Setup
        $entity = new Entity();
        $entity->addProperty('name', EdmType::STRING, 'string');
        
        // Assert
        $actual = $entity->isValid();
        
        // Assert
        $this->assertFalse($actual);
    }
}
