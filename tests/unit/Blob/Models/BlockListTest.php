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
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;
use MicrosoftAzure\Storage\Blob\Models\BlockList;
use MicrosoftAzure\Storage\Blob\Models\BlobBlockType;
use MicrosoftAzure\Storage\Blob\Models\Block;

/**
 * Unit tests for class BlockList
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class BlockListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::addEntry
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::getEntry
     */
    public function testAddEntry()
    {
        // Setup
        $expectedId = '1234';
        $expectedType = BlobBlockType::COMMITTED_TYPE;
        $blockList = new BlockList();
        
        // Test
        $blockList->addEntry($expectedId, $expectedType);
        
        // Assert
        $entry = $blockList->getEntry($expectedId);
        $this->assertEquals($expectedType, $entry->getType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::getEntries
     */
    public function testGetEntries()
    {
        // Setup
        $expectedId = '1234';
        $expectedType = BlobBlockType::COMMITTED_TYPE;
        $blockList = new BlockList();
        $blockList->addEntry($expectedId, $expectedType);
        
        // Test
        $entries = $blockList->getEntries();
        
        // Assert
        $this->assertCount(1, $entries);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::addCommittedEntry
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::getEntry
     */
    public function testAddCommittedEntry()
    {
        // Setup
        $expectedId = '1234';
        $expectedType = BlobBlockType::COMMITTED_TYPE;
        $blockList = new BlockList();
        
        // Test
        $blockList->addCommittedEntry($expectedId, $expectedType);
        
        // Assert
        $entry = $blockList->getEntry($expectedId);
        $this->assertEquals($expectedId, $entry->getBlockId());
        $this->assertEquals($expectedType, $entry->getType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::addUncommittedEntry
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::getEntry
     */
    public function testAddUncommittedEntry()
    {
        // Setup
        $expectedId = '1234';
        $expectedType = BlobBlockType::UNCOMMITTED_TYPE;
        $blockList = new BlockList();
        
        // Test
        $blockList->addUncommittedEntry($expectedId, $expectedType);
        
        // Assert
        $entry = $blockList->getEntry($expectedId);
        $this->assertEquals($expectedId, $entry->getBlockId());
        $this->assertEquals($expectedType, $entry->getType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::addLatestEntry
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::getEntry
     */
    public function testAddLatestEntry()
    {
        // Setup
        $expectedId = '1234';
        $expectedType = BlobBlockType::LATEST_TYPE;
        $blockList = new BlockList();
        
        // Test
        $blockList->addLatestEntry($expectedId, $expectedType);
        
        // Assert
        $entry = $blockList->getEntry($expectedId);
        $this->assertEquals($expectedId, $entry->getBlockId());
        $this->assertEquals($expectedType, $entry->getType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::create
     */
    public function testCreate()
    {
        // Setup
        $block1 = new Block();
        $block1->setBlockId('123');
        $block1->setType(BlobBlockType::COMMITTED_TYPE);
        $block2 = new Block();
        $block2->setBlockId('223');
        $block2->setType(BlobBlockType::UNCOMMITTED_TYPE);
        $block3 = new Block();
        $block3->setBlockId('333');
        $block3->setType(BlobBlockType::LATEST_TYPE);
        
        // Test
        $blockList = BlockList::create(array($block1, $block2, $block3));
        
        // Assert
        $this->assertCount(3, $blockList->getEntries());
        $b1 = $blockList->getEntry($block1->getBlockId());
        $b2 = $blockList->getEntry($block2->getBlockId());
        $b3 = $blockList->getEntry($block3->getBlockId());
        $this->assertEquals($block1, $b1);
        $this->assertEquals($block2, $b2);
        $this->assertEquals($block3, $b3);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::toXml
     */
    public function testToXml()
    {
        // Setup
        $blockList = new BlockList();
        $blockList->addLatestEntry(base64_encode('1234'));
        $blockList->addCommittedEntry(base64_encode('1239'));
        $blockList->addLatestEntry(base64_encode('1236'));
        $blockList->addCommittedEntry(base64_encode('1237'));
        $blockList->addUncommittedEntry(base64_encode('1238'));
        $blockList->addLatestEntry(base64_encode('1235'));
        $blockList->addUncommittedEntry(base64_encode('1240'));
        $expected = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                    '<BlockList>' . "\n" .
                    ' <Latest>MTIzNA==</Latest>' . "\n" .
                    ' <Committed>MTIzOQ==</Committed>' . "\n" .
                    ' <Latest>MTIzNg==</Latest>' . "\n" .
                    ' <Committed>MTIzNw==</Committed>' . "\n" .
                    ' <Uncommitted>MTIzOA==</Uncommitted>' . "\n" .
                    ' <Latest>MTIzNQ==</Latest>' . "\n" .
                    ' <Uncommitted>MTI0MA==</Uncommitted>' . "\n" .
                    '</BlockList>' . "\n";
        
        // Test
        $actual = $blockList->toXml(new XmlSerializer());
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}


