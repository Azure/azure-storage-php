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
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;
use MicrosoftAzure\Storage\Blob\Models\BlobProperties;

/**
 * Unit tests for class SetBlobPropertiesOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class SetBlobPropertiesOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::__construct
     */
    public function test__construct()
    {
        // Setup
        $expectedLength = 10;
        $blobProperties = new BlobProperties();
        $blobProperties->setContentLength($expectedLength);
        
        // Test
        $options = new SetBlobPropertiesOptions($blobProperties);
        
        // Assert
        $this->assertNotNull($options);
        $this->assertEquals($expectedLength, $options->getBlobContentLength());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setBlobContentType
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getBlobContentType
     */
    public function testSetBlobContentType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new SetBlobPropertiesOptions();
        $options->setBlobContentType($expected);
        
        // Test
        $options->setBlobContentType($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setBlobContentEncoding
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getBlobContentEncoding
     */
    public function testSetBlobContentEncoding()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new SetBlobPropertiesOptions();
        $options->setBlobContentEncoding($expected);
        
        // Test
        $options->setBlobContentEncoding($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentEncoding());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setBlobContentLength
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getBlobContentLength
     */
    public function testSetContentLength()
    {
        // Setup
        $expected = 123;
        $options = new SetBlobPropertiesOptions();
        $options->setBlobContentLength($expected);
        
        // Test
        $options->setBlobContentLength($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentLength());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setBlobContentLanguage
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getBlobContentLanguage
     */
    public function testSetBlobContentLanguage()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new SetBlobPropertiesOptions();
        $options->setBlobContentLanguage($expected);
        
        // Test
        $options->setBlobContentLanguage($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentLanguage());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setBlobContentMD5
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getBlobContentMD5
     */
    public function testSetBlobContentMD5()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new SetBlobPropertiesOptions();
        $options->setBlobContentMD5($expected);
        
        // Test
        $options->setBlobContentMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentMD5());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setBlobCacheControl
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getBlobCacheControl
     */
    public function testSetBlobCacheControl()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new SetBlobPropertiesOptions();
        $options->setBlobCacheControl($expected);
        
        // Test
        $options->setBlobCacheControl($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobCacheControl());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getLeaseId
     */
    public function testSetLeaseId()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new SetBlobPropertiesOptions();
        $options->setLeaseId($expected);
        
        // Test
        $options->setLeaseId($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setSequenceNumberAction
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getSequenceNumberAction
     */
    public function testSetSequenceNumberAction()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new SetBlobPropertiesOptions();
        $options->setSequenceNumberAction($expected);
        
        // Test
        $options->setSequenceNumberAction($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getSequenceNumberAction());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setSequenceNumber
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getSequenceNumber
     */
    public function testSetSequenceNumber()
    {
        // Setup
        $expected = 123;
        $options = new SetBlobPropertiesOptions();
        $options->setSequenceNumber($expected);
        
        // Test
        $options->setSequenceNumber($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getSequenceNumber());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::getAccessCondition
     */
    public function testGetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new SetBlobPropertiesOptions();
        $result->setAccessCondition($expected);
        
        // Test
        $actual = $result->getAccessCondition();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions::setAccessCondition
     */
    public function testSetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new SetBlobPropertiesOptions();
        
        // Test
        $result->setAccessCondition($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getAccessCondition());
    }
}


