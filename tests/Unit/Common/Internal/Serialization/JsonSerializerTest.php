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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Serialization
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Serialization;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
use MicrosoftAzure\Storage\Common\Internal\Serialization\JsonSerializer;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Unit tests for class XmlSerializer
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Serialization
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class JsonSerializerTest extends \PHPUnit\Framework\TestCase
{
    public function testObjectSerialize()
    {
        // Setup
        $testData = TestResources::getSimpleJson();
        $rootName = 'testRoot';
        $expected = "{\"{$rootName}\":{$testData['jsonObject']}}";

        // Test
        $actual = JsonSerializer::objectSerialize($testData['dataObject'], $rootName);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeArray()
    {
        // Setup
        $jsonSerializer = new JsonSerializer();
        $testData = TestResources::getSimpleJson();
        $expected = $testData['dataArray'];

        // Test
        $actual = $jsonSerializer->unserialize($testData['jsonArray']);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeObject()
    {
        // Setup
        $jsonSerializer = new JsonSerializer();
        $testData = TestResources::getSimpleJson();
        $expected = $testData['dataObject'];

        // Test
        $actual = $jsonSerializer->unserialize($testData['jsonObject']);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeEmptyString()
    {
        // Setup
        $jsonSerializer = new JsonSerializer();
        $testData = "";
        $expected = null;

        // Test
        $actual = $jsonSerializer->unserialize($testData);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeInvalidString()
    {
        // Setup
        $jsonSerializer = new JsonSerializer();
        $testData = "{]{{test]";
        $expected = null;

        // Test
        $actual = $jsonSerializer->unserialize($testData);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSerialize()
    {
        // Setup
        $jsonSerializer = new JsonSerializer();
        $testData = TestResources::getSimpleJson();
        $expected = $testData['jsonArray'];

        // Test
        $actual = $jsonSerializer->serialize($testData['dataArray']);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSerializeNull()
    {
        // Setup
        $jsonSerializer = new JsonSerializer();
        $testData = null;
        $expected = "";
        $this->setExpectedException('MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException', sprintf(Resources::INVALID_PARAM_MSG, 'array', 'array'));

        // Test
        $actual = $jsonSerializer->serialize($testData);
    }
}
