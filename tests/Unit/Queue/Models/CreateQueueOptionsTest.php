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

use MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions;

/**
 * Unit tests for class CreateQueueOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateQueueOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testSetMetadata()
    {
        // Setup
        $queue = new CreateQueueOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');

        // Test
        $queue->setMetadata($expected);

        // Assert
        $this->assertEquals($expected, $queue->getMetadata());
    }

    public function testGetMetadata()
    {
        // Setup
        $queue = new CreateQueueOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $queue->setMetadata($expected);

        // Test
        $actual = $queue->getMetadata();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testAddMetadata()
    {
        // Setup
        $queue = new CreateQueueOptions();
        $key = 'key1';
        $value = 'value1';
        $expected = array($key => $value);

        // Test
        $queue->addMetadata($key, $value);

        // Assert
        $this->assertEquals($expected, $queue->getMetadata());
    }
}
