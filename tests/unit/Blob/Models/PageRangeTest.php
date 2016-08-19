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
use MicrosoftAzure\Storage\Blob\Models\PageRange;

/**
 * Unit tests for class PageRange
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class PageRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\PageRange::__construct
     * @covers MicrosoftAzure\Storage\Blob\Models\PageRange::getStart
     * @covers MicrosoftAzure\Storage\Blob\Models\PageRange::getEnd
     */
    public function test__construct()
    {
        // Setup
        $expectedStart = 0;
        $expectedEnd = 512;
        
        // Test
        $actual = new PageRange($expectedStart, $expectedEnd);
        
        // Assert
        $this->assertEquals($expectedStart, $actual->getStart());
        $this->assertEquals($expectedEnd, $actual->getEnd());
     
        return $actual;
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\PageRange::setStart
     * @depends test__construct
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
     * @covers MicrosoftAzure\Storage\Blob\Models\PageRange::setEnd
     * @depends test__construct
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
     * @covers MicrosoftAzure\Storage\Blob\Models\PageRange::setLength
     * @depends test__construct
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
     * @covers MicrosoftAzure\Storage\Blob\Models\PageRange::getLength
     * @depends test__construct
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


