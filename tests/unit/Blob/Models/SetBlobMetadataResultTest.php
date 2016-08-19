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
use MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult;

/**
 * Unit tests for class SetBlobMetadataResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class SetBlobMetadataResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult::getETag
     */
    public function testGetETag()
    {
        // Setup
        $getBlobMetadataResult = new SetBlobMetadataResult();
        $expected = '0x8CACB9BD7C6B1B2';
        $getBlobMetadataResult->setETag($expected);
        
        // Test
        $actual = $getBlobMetadataResult->getETag();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult::setETag
     */
    public function testSetETag()
    {
        // Setup
        $getBlobMetadataResult = new SetBlobMetadataResult();
        $expected = '0x8CACB9BD7C6B1B2';
        
        // Test
        $getBlobMetadataResult->setETag($expected);
        
        // Assert
        $actual = $getBlobMetadataResult->getETag();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult::getLastModified
     */
    public function testGetLastModified()
    {
        // Setup
        $getBlobMetadataResult = new SetBlobMetadataResult();
        $expected = Utilities::rfc1123ToDateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        $getBlobMetadataResult->setLastModified($expected);
        
        // Test
        $actual = $getBlobMetadataResult->getLastModified();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult::setLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $getBlobMetadataResult = new SetBlobMetadataResult();
        $expected = Utilities::rfc1123ToDateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        
        // Test
        $getBlobMetadataResult->setLastModified($expected);
        
        // Assert
        $actual = $getBlobMetadataResult->getLastModified();
        $this->assertEquals($expected, $actual);
    }
}


