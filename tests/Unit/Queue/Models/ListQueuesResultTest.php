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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Queue\Models;

use MicrosoftAzure\Storage\Queue\Models\ListQueuesResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class ListQueuesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListQueuesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::create
     */
    public function testCreateWithEmpty()
    {
        // Setup
        $sample = TestResources::listQueuesEmpty();
        
        // Test
        $actual = ListQueuesResult::create($sample);
        
        // Assert
        $this->assertCount(0, $actual->getQueues());
        $this->assertTrue(empty($sample['NextMarker']));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::create
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getQueues
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setQueues
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setContinuationToken
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getContinuationToken
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setMarker
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getMarker
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setMaxResults
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getMaxResults
     */
    public function testCreateWithOneEntry()
    {
        // Setup
        $sample = TestResources::listQueuesOneEntry();
        
        // Test
        $actual = ListQueuesResult::create($sample);
        
        // Assert
        $queues = $actual->getQueues();
        $this->assertCount(1, $queues);
        $this->assertEquals($sample['Queues']['Queue']['Name'], $queues[0]->getName());
        $this->assertEquals($sample['@attributes']['ServiceEndpoint'] . $sample['Queues']['Queue']['Name'], $queues[0]->getUrl());
        $this->assertEquals($sample['Marker'], $actual->getMarker());
        $this->assertEquals($sample['MaxResults'], $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::create
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getQueues
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setQueues
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setPrefix
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getPrefix
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setContinuationToken
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getContinuationToken
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setMaxResults
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getMaxResults
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::setAccountName
     * @covers MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::getAccountName
     */
    public function testCreateWithMultipleEntries()
    {
        // Setup
        $sample = TestResources::listQueuesMultipleEntries();
        
        // Test
        $actual = ListQueuesResult::create($sample);
        
        // Assert
        $queues = $actual->getQueues();
        $this->assertCount(2, $queues);
        $this->assertEquals($sample['Queues']['Queue'][0]['Name'], $queues[0]->getName());
        $this->assertEquals($sample['@attributes']['ServiceEndpoint'] . $sample['Queues']['Queue'][0]['Name'], $queues[0]->getUrl());
        $this->assertEquals($sample['Queues']['Queue'][1]['Name'], $queues[1]->getName());
        $this->assertEquals($sample['@attributes']['ServiceEndpoint'] . $sample['Queues']['Queue'][1]['Name'], $queues[1]->getUrl());
        $this->assertEquals($sample['MaxResults'], $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
        $this->assertEquals($sample['Account'], $actual->getAccountName());
        $this->assertEquals($sample['Prefix'], $actual->getPrefix());
        
        return $actual;
    }
}
