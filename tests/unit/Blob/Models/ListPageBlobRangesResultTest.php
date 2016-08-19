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
use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult;
use MicrosoftAzure\Storage\Blob\Models\PageRange;

/**
 * Unit tests for class ListPageBlobRangesResultTest
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ListPageBlobRangesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::setLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::getLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $expected = Utilities::rfc1123ToDateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $result = new ListPageBlobRangesResult();
        $result->setLastModified($expected);
        
        // Test
        $result->setLastModified($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getLastModified());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::setETag
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::getETag
     */
    public function testSetETag()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $result = new ListPageBlobRangesResult();
        $result->setETag($expected);
        
        // Test
        $result->setETag($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getETag());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::setContentLength
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::getContentLength
     */
    public function testSetContentLength()
    {
        // Setup
        $expected = 100;
        $result = new ListPageBlobRangesResult();
        $result->setContentLength($expected);
        
        // Test
        $result->setContentLength($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getContentLength());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::setPageRanges
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::getPageRanges
     */
    public function testSetPageRanges()
    {
        // Setup
        $expected = array(0 => new PageRange(0, 10), 1 => new PageRange(20, 40));
        $result = new ListPageBlobRangesResult();
        
        // Test
        $result->setPageRanges($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getPageRanges());
    }
}


