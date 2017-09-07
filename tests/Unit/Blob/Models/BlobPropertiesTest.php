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

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Blob\Models\BlobProperties;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class BlobProperties
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BlobPropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::createFromHttpHeaders
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        $expected = $sample['Blobs']['Blob']['Properties'];
        $expectedDate = Utilities::rfc1123ToDateTime($expected['Last-Modified']);
        
        // Test
        $actual = BlobProperties::createFromHttpHeaders($expected);
        
        // Assert
        $this->assertEquals($expectedDate, $actual->getLastModified());
        $this->assertEquals($expected['Etag'], $actual->getETag());
        $this->assertEquals(intval($expected['Content-Length']), $actual->getContentLength());
        $this->assertEquals($expected['Content-Type'], $actual->getContentType());
        $this->assertEquals($expected['Content-Encoding'], $actual->getContentEncoding());
        $this->assertEquals($expected['Content-Language'], $actual->getContentLanguage());
        $this->assertEquals($expected['Content-MD5'], $actual->getContentMD5());
        $this->assertEquals($expected['Cache-Control'], $actual->getCacheControl());
        $this->assertEquals(intval($expected['x-ms-blob-sequence-number']), $actual->getSequenceNumber());
        $this->assertEquals($expected['x-ms-blob-type'], $actual->getBlobType());
        $this->assertEquals($expected['x-ms-lease-status'], $actual->getLeaseStatus());
        $this->assertEquals(Utilities::toBoolean($expected['x-ms-server-encrypted']), $actual->getServerEncrypted());
        $this->assertEquals(Utilities::toBoolean($expected['x-ms-incremental-copy']), $actual->getIncrementalCopy());
        $this->assertEquals($expected['x-ms-copy-destination-snapshot'], $actual->getCopyDestinationSnapshot());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $expected = Utilities::rfc1123ToDateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $properties = new BlobProperties();
        $properties->setLastModified($expected);
        
        // Test
        $properties->setLastModified($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getLastModified());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setETag
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getETag
     */
    public function testSetETag()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setETag($expected);
        
        // Test
        $properties->setETag($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getETag());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setContentRange
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getContentRange
     */
    public function testSetContentRange()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setContentRange($expected);
        
        // Test
        $properties->setContentRange($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getContentRange());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setContentType
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getContentType
     */
    public function testSetContentType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setContentType($expected);
        
        // Test
        $properties->setContentType($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getContentType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setContentLength
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getContentLength
     */
    public function testSetContentLength()
    {
        // Setup
        $expected = 100;
        $properties = new BlobProperties();
        $properties->setContentLength($expected);
        
        // Test
        $properties->setContentLength($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getContentLength());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setContentEncoding
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getContentEncoding
     */
    public function testSetContentEncoding()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setContentEncoding($expected);
        
        // Test
        $properties->setContentEncoding($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getContentEncoding());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setContentLanguage
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getContentLanguage
     */
    public function testSetContentLanguage()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setContentLanguage($expected);
        
        // Test
        $properties->setContentLanguage($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getContentLanguage());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setContentMD5
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getContentMD5
     */
    public function testSetContentMD5()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setContentMD5($expected);
        
        // Test
        $properties->setContentMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getContentMD5());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setCacheControl
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getCacheControl
     */
    public function testSetCacheControl()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setCacheControl($expected);
        
        // Test
        $properties->setCacheControl($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getCacheControl());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setContentDisposition
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getContentDisposition
     */
    public function testSetContentDisposition()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setContentDisposition($expected);
        
        // Test
        $properties->setContentDisposition($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getContentDisposition());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setBlobType
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getBlobType
     */
    public function testSetBlobType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setBlobType($expected);
        
        // Test
        $properties->setBlobType($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getblobType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setLeaseStatus
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getLeaseStatus
     */
    public function testSetLeaseStatus()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $properties = new BlobProperties();
        $properties->setLeaseStatus($expected);
        
        // Test
        $properties->setLeaseStatus($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getLeaseStatus());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setSequenceNumber
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getSequenceNumber
     */
    public function testSetSequenceNumber()
    {
        // Setup
        $expected = 123;
        $properties = new BlobProperties();

        // Test
        $properties->setSequenceNumber($expected);
        
        // Assert
        $this->assertEquals($expected, $properties->getSequenceNumber());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setCopyDestinationSnapshot
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getCopyDestinationSnapshot
     */
    public function testSetCopyDestinationSnapshot()
    {
        // Setup
        $expected = '2017-09-07T06:57:06.0830478Z';
        $properties = new BlobProperties();

        // Test
        $properties->setCopyDestinationSnapshot($expected);

        // Assert
        $this->assertEquals($expected, $properties->getCopyDestinationSnapshot());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setIncrementalCopy
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getIncrementalCopy
     */
    public function testSetIncrementalCopy()
    {
        // Setup
        $expected = true;
        $properties = new BlobProperties();

        // Test
        $properties->setIncrementalCopy($expected);

        // Assert
        $this->assertEquals($expected, $properties->getIncrementalCopy());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::setServerEncrypted
     * @covers MicrosoftAzure\Storage\Blob\Models\BlobProperties::getServerEncrypted
     */
    public function tesSetServerEncrypted()
    {
        // Setup
        $expectedTrue = true;
        $expectedFalse = false;
        $expectedNull = null;

        $propertiesTrue = new BlobProperties();
        $propertiesFalse = new BlobProperties();
        $propertiesNull = new BlobProperties();

        // Test
        $propertiesTrue->setServerEncrypted($expectedTrue);
        $propertiesFalse->setServerEncrypted($expectedFalse);
        $propertiesNull->setServerEncrypted($expectedNull);

        // Assert
        $this->assertEquals($expectedTrue, $propertiesTrue->getServerEncrypted());
        $this->assertEquals($propertiesFalse, $propertiesFalse->getServerEncrypted());
        $this->assertEquals($propertiesNull, $propertiesNull->getServerEncrypted());
    }
}
