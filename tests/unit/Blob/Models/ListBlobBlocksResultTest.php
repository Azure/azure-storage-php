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
use MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class ListBlobBlocksResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ListBlobBlocksResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::setLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $expected = Utilities::rfc1123ToDateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $result = new ListBlobBlocksResult();
        $result->setLastModified($expected);
        
        // Test
        $result->setLastModified($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getLastModified());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::setETag
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getETag
     */
    public function testSetETag()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $result = new ListBlobBlocksResult();
        $result->setETag($expected);
        
        // Test
        $result->setETag($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getETag());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::setContentType
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getContentType
     */
    public function testSetContentType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $result = new ListBlobBlocksResult();
        $result->setContentType($expected);
        
        // Test
        $result->setContentType($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getContentType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::setContentLength
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getContentLength
     */
    public function testSetContentLength()
    {
        // Setup
        $expected = 100;
        $result = new ListBlobBlocksResult();
        $result->setContentLength($expected);
        
        // Test
        $result->setContentLength($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getContentLength());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::setUncommittedBlocks
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getUncommittedBlocks
     */
    public function testSetUncommittedBlocks()
    {
        // Setup
        $result = new ListBlobBlocksResult();
        $expected = array('Block1' => 10, 'Block2' => 20, 'Block3' => 30);
        
        // Test
        $result->setUncommittedBlocks($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getUncommittedBlocks());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::setCommittedBlocks
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getCommittedBlocks
     */
    public function testSetCommittedBlocks()
    {
        // Setup
        $result = new ListBlobBlocksResult();
        $expected = array('Block1' => 10, 'Block2' => 20, 'Block3' => 30);
        
        // Test
        $result->setCommittedBlocks($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getCommittedBlocks());
    }
}


