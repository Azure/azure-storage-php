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

use MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Unit tests for class UpdateMessageResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class UpdateMessageResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::getPopReceipt
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::setPopReceipt
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::getTimeNextVisible
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::setTimeNextVisible
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::create
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::getUpdateMessageResultSampleHeaders();
        $expectedDate = Utilities::rfc1123ToDateTime(
            $sample[Resources::X_MS_TIME_NEXT_VISIBLE]
        );

        // Test
        $result = UpdateMessageResult::create($sample);
        
        // Assert
        $this->assertEquals(
            $sample[Resources::X_MS_POPRECEIPT],
            $result->getPopReceipt()
        );
        $this->assertEquals($expectedDate, $result->getTimeNextVisible());
    }
}
