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

use MicrosoftAzure\Storage\Queue\Models\PeekMessagesResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class PeekMessagesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class PeekMessagesResultTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listMessagesSample();

        // Test
        $result = PeekMessagesResult::create($sample);

        // Assert
        $actual = $result->getQueueMessages();
        $this->assertCount(1, $actual);
        $this->assertEquals(
            $sample['QueueMessage']['MessageId'],
            $actual[0]->getMessageId()
        );
        $this->assertEquals(
            Utilities::rfc1123ToDateTime(
                $sample['QueueMessage']['InsertionTime']
            ),
            $actual[0]->getInsertionDate()
        );
        $this->assertEquals(
            Utilities::rfc1123ToDateTime(
                $sample['QueueMessage']['ExpirationTime']
            ),
            $actual[0]->getExpirationDate()
        );
        $this->assertEquals(
            intval($sample['QueueMessage']['DequeueCount']),
            $actual[0]->getDequeueCount()
        );
        $this->assertEquals(
            $sample['QueueMessage']['MessageText'],
            $actual[0]->getMessageText()
        );
    }

    public function testCreateMultiple()
    {
        // Setup
        $sample = TestResources::listMessagesMultipleMessagesSample();

        // Test
        $result = PeekMessagesResult::create($sample);

        // Assert
        $actual = $result->getQueueMessages();
        $this->assertCount(2, $actual);
        $this->assertEquals($sample['QueueMessage'][0]['MessageId'], $actual[0]->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][0]['InsertionTime']), $actual[0]->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][0]['ExpirationTime']), $actual[0]->getExpirationDate());
        $this->assertEquals(intval($sample['QueueMessage'][0]['DequeueCount']), $actual[0]->getDequeueCount());
        $this->assertEquals($sample['QueueMessage'][0]['MessageText'], $actual[0]->getMessageText());

        $this->assertEquals($sample['QueueMessage'][1]['MessageId'], $actual[1]->getMessageId());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][1]['InsertionTime']), $actual[1]->getInsertionDate());
        $this->assertEquals(Utilities::rfc1123ToDateTime($sample['QueueMessage'][1]['ExpirationTime']), $actual[1]->getExpirationDate());
        $this->assertEquals(intval($sample['QueueMessage'][1]['DequeueCount']), $actual[1]->getDequeueCount());
        $this->assertEquals($sample['QueueMessage'][1]['MessageText'], $actual[1]->getMessageText());
    }
}
