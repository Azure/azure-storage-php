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
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\Unit\Common\Models;

use MicrosoftAzure\Storage\Common\Models\RangeDiff;

/**
 * Unit tests for class RangeDiff
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class RangeDiffTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\RangeDiff::__construct
     * @covers MicrosoftAzure\Storage\Common\Models\RangeDiff::getStart
     * @covers MicrosoftAzure\Storage\Common\Models\RangeDiff::getEnd
     */
    public function testConstruct()
    {
        // Setup
        $expectedStart = 0;
        $expectedEnd = 512;
        $expectedIsClearedPageRange = false;
        
        // Test
        $actual = new RangeDiff($expectedStart, $expectedEnd, $expectedIsClearedPageRange);
        
        // Assert
        $this->assertEquals($expectedStart, $actual->getStart());
        $this->assertEquals($expectedEnd, $actual->getEnd());
        $this->assertEquals($expectedIsClearedPageRange, $actual->isClearedPageRange());

        return $actual;
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Models\RangeDiff::setIsClearedPageRange
     * @covers MicrosoftAzure\Storage\Common\Models\RangeDiff::isClearedPageRange
     * @depends testConstruct
     */
    public function testIsClearedPageRange($obj)
    {
        // Setup
        $excepted = true;
        $obj->setIsClearedPageRange($excepted);

        // Test
        $actual = $obj->isClearedPageRange();

        // Assert
        $this->assertEquals($excepted, $actual);

        // Setup
        $excepted = false;
        $obj->setIsClearedPageRange($excepted);

        // Test
        $actual = $obj->isClearedPageRange();

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}
