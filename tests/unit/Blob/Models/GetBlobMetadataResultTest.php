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
use MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult;

/**
 * Unit tests for class GetBlobMetadataResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class GetBlobMetadataResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::getETag
     */
    public function testGetETag()
    {
        // Setup
        $getBlobMetadataResult = new GetBlobMetadataResult();
        $expected = '0x8CACB9BD7C6B1B2';
        $getBlobMetadataResult->setETag($expected);
        
        // Test
        $actual = $getBlobMetadataResult->getETag();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::setETag
     */
    public function testSetETag()
    {
        // Setup
        $getBlobMetadataResult = new GetBlobMetadataResult();
        $expected = '0x8CACB9BD7C6B1B2';
        
        // Test
        $getBlobMetadataResult->setETag($expected);
        
        // Assert
        $actual = $getBlobMetadataResult->getETag();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::getLastModified
     */
    public function testGetLastModified()
    {
        // Setup
        $getBlobMetadataResult = new GetBlobMetadataResult();
        $expected = Utilities::rfc1123ToDateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        $getBlobMetadataResult->setLastModified($expected);
        
        // Test
        $actual = $getBlobMetadataResult->getLastModified();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::setLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $getBlobMetadataResult = new GetBlobMetadataResult();
        $expected = Utilities::rfc1123ToDateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        
        // Test
        $getBlobMetadataResult->setLastModified($expected);
        
        // Assert
        $actual = $getBlobMetadataResult->getLastModified();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::setMetadata
     */
    public function testSetMetadata()
    {
        // Setup
        $container = new GetBlobMetadataResult();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        
        // Test
        $container->setMetadata($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::getMetadata
     */
    public function testGetMetadata()
    {
        // Setup
        $container = new GetBlobMetadataResult();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $container->setMetadata($expected);
        
        // Test
        $actual = $container->getMetadata();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}


