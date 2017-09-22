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
 * @package   MicrosoftAzure\Storage\Tests\Unit\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\File\Models;

use MicrosoftAzure\Storage\File\Models\ListSharesResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class ListSharesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListSharesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::create
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getShares
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setShares
     */
    public function testCreateWithEmpty()
    {
        // Setup
        $sample = TestResources::getInterestingListShareResultArray();
        
        // Test
        $actual = ListSharesResult::create($sample);
        
        // Assert
        $this->assertCount(0, $actual->getShares());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::create
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getShares
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setShares
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setContinuationToken
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getContinuationToken
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setMarker
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getMarker
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setMaxResults
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getMaxResults
     */
    public function testCreateWithOneEntry()
    {
        // Setup
        $sample = TestResources::getInterestingListShareResultArray(1);
        
        // Test
        $actual = ListSharesResult::create($sample);
        
        // Assert
        $shares = $actual->getShares();
        $this->assertCount(1, $shares);
        $this->assertEquals($sample['Shares']['Share']['Name'], $shares[0]->getName());
        $this->assertEquals(
            Utilities::rfc1123ToDateTime(
                $sample['Shares']['Share']['Properties']['Last-Modified']
            ),
            $shares[0]->getProperties()->getLastModified()
        );
        $this->assertEquals(
            $sample['Shares']['Share']['Properties']['Etag'],
            $shares[0]->getProperties()->getETag()
        );
        $this->assertEquals($sample['Marker'], $actual->getMarker());
        $this->assertEquals($sample['MaxResults'], $actual->getMaxResults());
        $this->assertEquals($sample['NextMarker'], $actual->getNextMarker());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::create
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getShares
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setShares
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setPrefix
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getPrefix
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setContinuationToken
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getContinuationToken
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setMaxResults
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getMaxResults
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::setAccountName
     * @covers MicrosoftAzure\Storage\File\Models\ListSharesResult::getAccountName
     */
    public function testCreateWithMultipleEntries()
    {
        // Setup
        $sample = TestResources::getInterestingListShareResultArray(5);
        
        // Test
        $actual = ListSharesResult::create($sample);
        
        // Assert
        $shares = $actual->getShares();
        $this->assertCount(5, $shares);
        for ($i = 0; $i < 5; ++$i) {
            $this->assertEquals($sample['Shares']['Share'][$i]['Name'], $shares[$i]->getName());
            $this->assertEquals(
                Utilities::rfc1123ToDateTime($sample['Shares']['Share'][$i]['Properties']['Last-Modified']),
                $shares[$i]->getProperties()->getLastModified()
            );
            $this->assertEquals(
                $sample['Shares']['Share'][$i]['Properties']['Etag'],
                $shares[$i]->getProperties()->getETag()
            );
        }
    }
}
