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
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\unit\Queue\Models;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Queue\Models\CreateMessageResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class CreateMessageResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateMessageResultTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        // Setup
        $sample = TestResources::createMessageSample();

        // Test
        $result = CreateMessageResult::create($sample);

        // Assert
        $actual = $result->getQueueMessage();
        $this->assertNotNull($actual);
        $this->assertEquals($sample['QueueMessage']['MessageId'],
            $actual->getMessageId()
        );
        $this->assertEquals(Utilities::rfc1123ToDateTime(
            $sample['QueueMessage']['InsertionTime']),
            $actual->getInsertionDate()
        );
        $this->assertEquals(Utilities::rfc1123ToDateTime(
            $sample['QueueMessage']['ExpirationTime']),
            $actual->getExpirationDate()
        );
        $this->assertEquals($sample['QueueMessage']['PopReceipt'],
            $actual->getPopReceipt()
        );
        $this->assertEquals(Utilities::rfc1123ToDateTime(
            $sample['QueueMessage']['TimeNextVisible']),
            $actual->getTimeNextVisible()
        );
    }
}
