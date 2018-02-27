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

use MicrosoftAzure\Storage\Blob\Models\BlobPrefix;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class BlobPrefix
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BlobPrefixTest extends \PHPUnit\Framework\TestCase
{
    public function testSetName()
    {
        // Setup
        $blob = new BlobPrefix();
        $expected = TestResources::QUEUE1_NAME;

        // Test
        $blob->setName($expected);

        // Assert
        $this->assertEquals($expected, $blob->getName());
    }

    public function testGetName()
    {
        // Setup
        $blob = new BlobPrefix();
        $expected = TestResources::QUEUE1_NAME;
        $blob->setName($expected);

        // Test
        $actual = $blob->getName();

        // Assert
        $this->assertEquals($expected, $actual);
    }
}
