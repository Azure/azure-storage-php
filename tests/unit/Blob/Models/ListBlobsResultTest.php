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
use MicrosoftAzure\Storage\Blob\Models\ListBlobsResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class ListBlobsResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ListBlobsResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::create 
     */
    public function testCreateWithEmpty()
    {
        // Setup
        $sample = TestResources::listBlobsEmpty();
        
        // Test
        $actual = ListBlobsResult::create($sample);
        
        // Assert
        $this->assertCount(0, $actual->getBlobs());
        $this->assertCount(0, $actual->getBlobPrefixes());
        $this->assertEquals(0,$actual->getMaxResults());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::create 
     */
    public function testCreateWithOneEntry()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        
        // Test
        $actual = ListBlobsResult::create($sample);
        
        // Assert
        $this->assertCount(1, $actual->getBlobs());
        $this->assertCount(1, $actual->getBlobPrefixes());
        $this->assertEquals($sample['Marker'], $actual->getMarker());
        $this->assertEquals(intval($sample['MaxResults']), $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
        $this->assertEquals($sample['Delimiter'], $actual->getDelimiter());
        $this->assertEquals($sample['Prefix'], $actual->getPrefix());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::create 
     */
    public function testCreateWithMultipleEntries()
    {
        // Setup
        $sample = TestResources::listBlobsMultipleEntries();
        
        // Test
        $actual = ListBlobsResult::create($sample);
        
        // Assert
        $this->assertCount(2, $actual->getBlobs());
        $this->assertCount(2, $actual->getBlobPrefixes());
        $this->assertEquals($sample['Marker'], $actual->getMarker());
        $this->assertEquals(intval($sample['MaxResults']), $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
        
        return $actual;
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::getBlobPrefixes
     * @depends testCreateWithMultipleEntries
     */
    public function testGetBlobPrefixs($result)
    {
        // Test
        $actual = $result->getBlobPrefixes();
        
        // Assert
        $this->assertCount(2, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::setBlobPrefixes
     * @depends testCreateWithMultipleEntries
     */
    public function testSetBlobPrefixs($result)
    {
        // Setup
        $sample = new ListBlobsResult();
        $expected = $result->getBlobPrefixes();
        
        // Test
        $sample->setBlobPrefixes($expected);
        
        // Assert
        $this->assertEquals($expected, $sample->getBlobPrefixes());
        $expected[0]->setName('test');
        $this->assertNotEquals($expected, $sample->getBlobPrefixes());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::getBlobs
     * @depends testCreateWithMultipleEntries
     */
    public function testGetBlobs($result)
    {
        // Test
        $actual = $result->getBlobs();
        
        // Assert
        $this->assertCount(2, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::setBlobs
     * @depends testCreateWithMultipleEntries
     */
    public function testSetBlobs($result)
    {
        // Setup
        $sample = new ListBlobsResult();
        $expected = $result->getBlobs();
        
        // Test
        $sample->setBlobs($expected);
        
        // Assert
        $this->assertEquals($expected, $sample->getBlobs());
        $expected[0]->setName('test');
        $this->assertNotEquals($expected, $sample->getBlobs());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::setPrefix
     */
    public function testSetPrefix()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'myprefix';
        
        // Test
        $result->setPrefix($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getPrefix());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::getPrefix
     */
    public function testGetPrefix()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'myprefix';
        $result->setPrefix($expected);
        
        // Test
        $actual = $result->getPrefix();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::setNextMarker
     */
    public function testSetNextMarker()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'mymarker';
        
        // Test
        $result->setNextMarker($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getNextMarker());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::getNextMarker
     */
    public function testGetNextMarker()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'mymarker';
        $result->setNextMarker($expected);
        
        // Test
        $actual = $result->getNextMarker();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::setMarker
     */
    public function testSetMarker()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'mymarker';
        
        // Test
        $result->setMarker($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getMarker());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::getMarker
     */
    public function testGetMarker()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'mymarker';
        $result->setMarker($expected);
        
        // Test
        $actual = $result->getMarker();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::setMaxResults
     */
    public function testSetMaxResults()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 3;
        
        // Test
        $result->setMaxResults($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getMaxResults());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::getMaxResults
     */
    public function testGetMaxResults()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 3;
        $result->setMaxResults($expected);
        
        // Test
        $actual = $result->getMaxResults();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::setContainerName
     */
    public function testSetContainerName()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'name';
        
        // Test
        $result->setContainerName($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getContainerName());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::getContainerName
     */
    public function testGetContainerName()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'name';
        $result->setContainerName($expected);
        
        // Test
        $actual = $result->getContainerName();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::setDelimiter
     */
    public function testSetDelimiter()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'mydelimiter';
        
        // Test
        $result->setDelimiter($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getDelimiter());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobsResult::getDelimiter
     */
    public function testGetDelimiter()
    {
        // Setup
        $result = new ListBlobsResult();
        $expected = 'mydelimiter';
        $result->setDelimiter($expected);
        
        // Test
        $actual = $result->getDelimiter();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}


