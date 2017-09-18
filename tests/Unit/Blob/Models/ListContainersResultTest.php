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

use MicrosoftAzure\Storage\Blob\Models\ListContainersResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class ListContainersResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListContainersResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::create
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getContainers
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setContainers
     */
    public function testCreateWithEmpty()
    {
        // Setup
        $sample = TestResources::listContainersEmpty();
        
        // Test
        $actual = ListContainersResult::create($sample);
        
        // Assert
        $this->assertCount(0, $actual->getContainers());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::create
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getContainers
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setContainers
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setContinuationToken
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getContinuationToken
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setMarker
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getMarker
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setMaxResults
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getMaxResults
     */
    public function testCreateWithOneEntry()
    {
        // Setup
        $sample = TestResources::listContainersOneEntry();
        
        // Test
        $actual = ListContainersResult::create($sample);
        
        // Assert
        $containers = $actual->getContainers();
        $this->assertCount(1, $containers);
        $this->assertEquals($sample['Containers']['Container']['Name'], $containers[0]->getName());
        $this->assertEquals(
            $sample['@attributes']['ServiceEndpoint'] .
                $sample['Containers']['Container']['Name'],
            $containers[0]->getUrl()
        );
        $this->assertEquals(
            Utilities::rfc1123ToDateTime(
                $sample['Containers']['Container']['Properties']['Last-Modified']
            ),
            $containers[0]->getProperties()->getLastModified()
        );
        $this->assertEquals(
            $sample['Containers']['Container']['Properties']['Etag'],
            $containers[0]->getProperties()->getETag()
        );
        $this->assertEquals($sample['Marker'], $actual->getMarker());
        $this->assertEquals($sample['MaxResults'], $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::create
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getContainers
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setContainers
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setPrefix
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getPrefix
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setContinuationToken
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getContinuationToken
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setMaxResults
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getMaxResults
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::setAccountName
     * @covers MicrosoftAzure\Storage\Blob\Models\ListContainersResult::getAccountName
     */
    public function testCreateWithMultipleEntries()
    {
        // Setup
        $sample = TestResources::listContainersMultipleEntries();
        
        // Test
        $actual = ListContainersResult::create($sample);
        
        // Assert
        $containers = $actual->getContainers();
        $this->assertCount(2, $containers);
        $this->assertEquals($sample['Containers']['Container'][0]['Name'], $containers[0]->getName());
        $this->assertEquals(
            $sample['@attributes']['ServiceEndpoint'] .
            $sample['Containers']['Container'][0]['Name'],
            $containers[0]->getUrl()
        );
        $this->assertEquals(
            Utilities::rfc1123ToDateTime($sample['Containers']['Container'][0]['Properties']['Last-Modified']),
            $containers[0]->getProperties()->getLastModified()
        );
        $this->assertEquals(
            $sample['Containers']['Container'][0]['Properties']['Etag'],
            $containers[0]->getProperties()->getETag()
        );
        $this->assertEquals(
            $sample['Containers']['Container'][1]['Name'],
            $containers[1]->getName()
        );
        $this->assertEquals(
            $sample['@attributes']['ServiceEndpoint'] .
            $sample['Containers']['Container'][1]['Name'],
            $containers[1]->getUrl()
        );
        $this->assertEquals(
            Utilities::rfc1123ToDateTime($sample['Containers']['Container'][1]['Properties']['Last-Modified']),
            $containers[1]->getProperties()->getLastModified()
        );
        $this->assertEquals(
            $sample['Containers']['Container'][1]['Properties']['Etag'],
            $containers[1]->getProperties()->getETag()
        );
        $this->assertEquals($sample['MaxResults'], $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
        $this->assertEquals($sample['Prefix'], $actual->getPrefix());
        $this->assertEquals($sample['account'], $actual->getAccountName());
        
        return $actual;
    }
}
