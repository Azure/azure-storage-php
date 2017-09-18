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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Models;

use MicrosoftAzure\Storage\Common\Models\GetServiceStatsResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class GetServiceStatsResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class GetServiceStatsResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\GetServiceStatsResult::create
     * @covers MicrosoftAzure\Storage\Common\Models\GetServiceStatsResult::getStatus
     * @covers MicrosoftAzure\Storage\Common\Models\GetServiceStatsResult::getLastSyncTime
     */
    public function testCreate()
    {
        $sample = TestResources::getServiceStatsSample();
        $geo = $sample[Resources::XTAG_GEO_REPLICATION];
        $expectedStatus = $geo[Resources::XTAG_STATUS];
        $expectedSyncTime = Utilities::convertToDateTime($geo[Resources::XTAG_LAST_SYNC_TIME]);
        // Test
        $result = GetServiceStatsResult::create($sample);

        // Assert
        $this->assertEquals($expectedSyncTime, $result->getLastSyncTime());
        $this->assertEquals($expectedStatus, $result->getStatus());
    }
}
