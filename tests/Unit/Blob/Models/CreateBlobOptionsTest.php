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
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;

/**
 * Unit tests for class CreateBlobOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateBlobOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setContentType
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getContentType
     */
    public function testSetContentType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentType($expected);
        
        // Test
        $options->setContentType($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getContentType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setContentEncoding
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getContentEncoding
     */
    public function testSetContentEncoding()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentEncoding($expected);
        
        // Test
        $options->setContentEncoding($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getContentEncoding());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setContentLanguage
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getContentLanguage
     */
    public function testSetContentLanguage()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentLanguage($expected);
        
        // Test
        $options->setContentLanguage($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getContentLanguage());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setContentMD5
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getContentMD5
     */
    public function testSetContentMD5()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentMD5($expected);
        
        // Test
        $options->setContentMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getContentMD5());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setCacheControl
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getCacheControl
     */
    public function testSetCacheControl()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setCacheControl($expected);
        
        // Test
        $options->setCacheControl($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getCacheControl());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setContentDisposition
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getContentDisposition
     */
    public function testSetContentDisposition()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentDisposition($expected);
        
        // Test
        $options->setContentDisposition($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getContentDisposition());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getLeaseId
     */
    public function testSetLeaseId()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setLeaseId($expected);
        
        // Test
        $options->setLeaseId($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getLeaseId());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setSequenceNumber
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getSequenceNumber
     */
    public function testSetSequenceNumber()
    {
        // Setup
        $expected = 123;
        $options = new CreateBlobOptions();
        $options->setSequenceNumber($expected);
        
        // Test
        $options->setSequenceNumber($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getSequenceNumber());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setMetadata
     */
    public function testSetMetadata()
    {
        // Setup
        $container = new CreateBlobOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        
        // Test
        $container->setMetadata($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getMetadata
     */
    public function testGetMetadata()
    {
        // Setup
        $container = new CreateBlobOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $container->setMetadata($expected);
        
        // Test
        $actual = $container->getMetadata();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::getAccessConditions
     */
    public function testGetAccessConditions()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new CreateBlobOptions();
        $result->setAccessConditions($expected);
        
        // Test
        $actual = $result->getAccessConditions();
        
        // Assert
        $this->assertEquals($expected, $actual[0]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions::setAccessConditions
     */
    public function testSetAccessConditions()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new CreateBlobOptions();
        
        // Test
        $result->setAccessConditions($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getAccessConditions()[0]);
    }
}
