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
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class AccessCondition
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class AccessConditionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::__construct
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getValue
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::setHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::setValue
     */
    public function test__construct()
    {
        // Setup
        $expectedHeaderType = Resources::IF_MATCH;
        $expectedValue = '0x8CAFB82EFF70C46';
        
        // Test
        $actual = AccessCondition::ifMatch($expectedValue);
        
        // Assert
        $this->assertEquals($expectedHeaderType, $actual->getHeader());
        $this->assertEquals($expectedValue, $actual->getValue());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::none
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getValue
     */
    public function testNone()
    {
        // Setup
        $expectedHeader = Resources::EMPTY_STRING;
        $expectedValue = null;
        
        // Test
        $actual = AccessCondition::none();
        
        // Assert
        $this->assertEquals($expectedHeader, $actual->getHeader());
        $this->assertEquals($expectedValue, $actual->getValue());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::ifModifiedSince
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getValue
     */
    public function testIfModifiedSince()
    {
        // Setup
        $expectedHeader = Resources::IF_MODIFIED_SINCE;
        $expectedValue = new \DateTime('Sun, 25 Sep 2011 00:42:49 GMT');
        
        // Test
        $actual = AccessCondition::ifModifiedSince($expectedValue);
        
        // Assert
        $this->assertEquals($expectedHeader, $actual->getHeader());
        $this->assertEquals($expectedValue, $actual->getValue());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::ifMatch
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getValue
     */
    public function testIfMatch()
    {
        // Setup
        $expectedHeader = Resources::IF_MATCH;
        $expectedValue = '0x8CAFB82EFF70C46';
        
        // Test
        $actual = AccessCondition::ifMatch($expectedValue);
        
        // Assert
        $this->assertEquals($expectedHeader, $actual->getHeader());
        $this->assertEquals($expectedValue, $actual->getValue());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::ifNoneMatch
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getValue
     */
    public function testIfNoneMatch()
    {
        // Setup
        $expectedHeader = Resources::IF_NONE_MATCH;
        $expectedValue = '0x8CAFB82EFF70C46';
        
        // Test
        $actual = AccessCondition::ifNoneMatch($expectedValue);
        
        // Assert
        $this->assertEquals($expectedHeader, $actual->getHeader());
        $this->assertEquals($expectedValue, $actual->getValue());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::ifNotModifiedSince
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::getValue
     */
    public function testIfNotModifiedSince()
    {
        // Setup
        $expectedHeader = Resources::IF_UNMODIFIED_SINCE;
        $expectedValue = new \DateTime('Sun, 25 Sep 2011 00:42:49 GMT');
        
        // Test
        $actual = AccessCondition::ifNotModifiedSince($expectedValue);
        
        // Assert
        $this->assertEquals($expectedHeader, $actual->getHeader());
        $this->assertEquals($expectedValue, $actual->getValue());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::isValid
     */
    public function testIsValidWithValid()
    {
        // Test
        $actual = AccessCondition::isValid(Resources::IF_MATCH);
        
        // Assert
        $this->assertTrue($actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessCondition::isValid
     */
    public function testIsValidWithInvalid()
    {
        // Test
        $actual = AccessCondition::isValid('1234');
        
        // Assert
        $this->assertFalse($actual);
    }
}


