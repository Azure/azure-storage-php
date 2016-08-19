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
use MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;

/**
 * Unit tests for class CommitBlobBlocksOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class CommitBlobBlocksOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::setBlobContentType
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::getBlobContentType
     */
    public function testSetBlobContentType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CommitBlobBlocksOptions();
        $options->setBlobContentType($expected);
        
        // Test
        $options->setBlobContentType($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::setBlobContentEncoding
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::getBlobContentEncoding
     */
    public function testSetBlobContentEncoding()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CommitBlobBlocksOptions();
        $options->setBlobContentEncoding($expected);
        
        // Test
        $options->setBlobContentEncoding($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentEncoding());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::setBlobContentLanguage
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::getBlobContentLanguage
     */
    public function testSetBlobContentLanguage()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CommitBlobBlocksOptions();
        $options->setBlobContentLanguage($expected);
        
        // Test
        $options->setBlobContentLanguage($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentLanguage());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::setBlobContentMD5
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::getBlobContentMD5
     */
    public function testSetBlobContentMD5()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CommitBlobBlocksOptions();
        $options->setBlobContentMD5($expected);
        
        // Test
        $options->setBlobContentMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobContentMD5());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::setBlobCacheControl
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::getBlobCacheControl
     */
    public function testSetBlobCacheControl()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CommitBlobBlocksOptions();
        $options->setBlobCacheControl($expected);
        
        // Test
        $options->setBlobCacheControl($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getBlobCacheControl());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::getLeaseId
     */
    public function testSetLeaseId()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CommitBlobBlocksOptions();
        $options->setLeaseId($expected);
        
        // Test
        $options->setLeaseId($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::setMetadata
     */
    public function testSetMetadata()
    {
        // Setup
        $container = new CommitBlobBlocksOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        
        // Test
        $container->setMetadata($expected);
        
        // Assert
        $this->assertEquals($expected, $container->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::getMetadata
     */
    public function testGetMetadata()
    {
        // Setup
        $container = new CommitBlobBlocksOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $container->setMetadata($expected);
        
        // Test
        $actual = $container->getMetadata();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::getAccessCondition
     */
    public function testGetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new CommitBlobBlocksOptions();
        $result->setAccessCondition($expected);
        
        // Test
        $actual = $result->getAccessCondition();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions::setAccessCondition
     */
    public function testSetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new CommitBlobBlocksOptions();
        
        // Test
        $result->setAccessCondition($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getAccessCondition());
    }
}


