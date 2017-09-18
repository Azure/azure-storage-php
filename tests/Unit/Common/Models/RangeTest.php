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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\Unit\Common\Models;

use MicrosoftAzure\Storage\Common\Models\Range;

/**
 * Unit tests for class Range
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class RangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Range::__construct
     * @covers MicrosoftAzure\Storage\Common\Models\Range::getStart
     * @covers MicrosoftAzure\Storage\Common\Models\Range::getEnd
     */
    public function testConstruct()
    {
        // Setup
        $expectedStart = 0;
        $expectedEnd = 512;
        
        // Test
        $actual = new Range($expectedStart, $expectedEnd);
        
        // Assert
        $this->assertEquals($expectedStart, $actual->getStart());
        $this->assertEquals($expectedEnd, $actual->getEnd());
     
        return $actual;
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Range::setStart
     * @depends testConstruct
     */
    public function testSetStart($obj)
    {
        // Setup
        $expected = 10;
        
        // Test
        $obj->setStart($expected);
        
        // Assert
        $this->assertEquals($expected, $obj->getStart());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Range::setEnd
     * @depends testConstruct
     */
    public function testSetEnd($obj)
    {
        // Setup
        $expected = 10;
        
        // Test
        $obj->setEnd($expected);
        
        // Assert
        $this->assertEquals($expected, $obj->getEnd());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Range::setLength
     * @depends testConstruct
     */
    public function testSetLength($obj)
    {
        // Setup
        $expected = 10;
        $start = $obj->getStart();
        
        // Test
        $obj->setLength($expected);
        
        // Assert
        $this->assertEquals($start + $expected - 1, $obj->getEnd());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Range::getLength
     * @depends testConstruct
     */
    public function testGetLength($obj)
    {
        // Setup
        $expected = 10;
        $obj->setLength($expected);
        
        // Test
        $actual = $obj->getLength();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}
