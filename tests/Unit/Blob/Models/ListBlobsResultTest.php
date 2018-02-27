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
 * @link      https://github.com/azure/azure-storage-php
 */
class ListBlobsResultTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateWithEmpty()
    {
        // Setup
        $sample = TestResources::listBlobsEmpty();

        // Test
        $actual = ListBlobsResult::create($sample);

        // Assert
        $this->assertCount(0, $actual->getBlobs());
        $this->assertCount(0, $actual->getBlobPrefixes());
        $this->assertEquals(0, $actual->getMaxResults());
    }

    public function testCreateWithOneEntry()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();

        // Test
        $actual = ListBlobsResult::create($sample);

        // Assert
        $this->assertCount(1, $actual->getBlobs());
        $this->assertEquals($sample['@attributes']['ContainerName'], $actual->getContainerName());
        $this->assertCount(1, $actual->getBlobPrefixes());
        $this->assertEquals($sample['Marker'], $actual->getMarker());
        $this->assertEquals(intval($sample['MaxResults']), $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
        $this->assertEquals($sample['Delimiter'], $actual->getDelimiter());
        $this->assertEquals($sample['Prefix'], $actual->getPrefix());
    }

    public function testCreateWithMultipleEntries()
    {
        // Setup
        $sample = TestResources::listBlobsMultipleEntries();

        // Test
        $actual = ListBlobsResult::create($sample);

        // Assert
        $this->assertCount(2, $actual->getBlobs());
        $this->assertCount(2, $actual->getBlobPrefixes());
        $this->assertEquals($sample['@attributes']['ContainerName'], $actual->getContainerName());
        $this->assertEquals($sample['Marker'], $actual->getMarker());
        $this->assertEquals(intval($sample['MaxResults']), $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());

        return $actual;
    }

    public function testCreateWithIsSecondary()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();

        // Test
        $actual = ListBlobsResult::create($sample, 'SecondaryOnly');

        // Assert
        $this->assertCount(1, $actual->getBlobs());
        $this->assertEquals($sample['@attributes']['ContainerName'], $actual->getContainerName());
        $this->assertCount(1, $actual->getBlobPrefixes());
        $this->assertEquals($sample['Marker'], $actual->getMarker());
        $this->assertEquals(intval($sample['MaxResults']), $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
        $this->assertEquals($sample['Delimiter'], $actual->getDelimiter());
        $this->assertEquals($sample['Prefix'], $actual->getPrefix());
        $this->assertEquals('SecondaryOnly', $actual->getLocation());
    }
}
