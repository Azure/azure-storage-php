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
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Models;

use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class Logging
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class LoggingTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();

        // Test
        $actual = Logging::create($sample['Logging']);

        // Assert
        $this->assertEquals(Utilities::toBoolean($sample['Logging']['Delete']), $actual->getDelete());
        $this->assertEquals(Utilities::toBoolean($sample['Logging']['Read']), $actual->getRead());
        $this->assertEquals(RetentionPolicy::create($sample['Logging']['RetentionPolicy']), $actual->getRetentionPolicy());
        $this->assertEquals($sample['Logging']['Version'], $actual->getVersion());
        $this->assertEquals(Utilities::toBoolean($sample['Logging']['Write']), $actual->getWrite());
    }

    public function testGetRetentionPolicy()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = RetentionPolicy::create($sample['Logging']['RetentionPolicy']);
        $logging->setRetentionPolicy($expected);

        // Test
        $actual = $logging->getRetentionPolicy();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetRetentionPolicy()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = RetentionPolicy::create($sample['Logging']['RetentionPolicy']);

        // Test
        $logging->setRetentionPolicy($expected);

        // Assert
        $actual = $logging->getRetentionPolicy();
        $this->assertEquals($expected, $actual);
    }

    public function testGetWrite()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = Utilities::toBoolean($sample['Logging']['Write']);
        $logging->setWrite($expected);

        // Test
        $actual = $logging->getWrite();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetWrite()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = Utilities::toBoolean($sample['Logging']['Write']);

        // Test
        $logging->setWrite($expected);

        // Assert
        $actual = $logging->getWrite();
        $this->assertEquals($expected, $actual);
    }

    public function testGetRead()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = Utilities::toBoolean($sample['Logging']['Read']);
        $logging->setRead($expected);

        // Test
        $actual = $logging->getRead();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetRead()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = Utilities::toBoolean($sample['Logging']['Read']);

        // Test
        $logging->setRead($expected);

        // Assert
        $actual = $logging->getRead();
        $this->assertEquals($expected, $actual);
    }

    public function testGetDelete()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = Utilities::toBoolean($sample['Logging']['Delete']);
        $logging->setDelete($expected);

        // Test
        $actual = $logging->getDelete();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetDelete()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = Utilities::toBoolean($sample['Logging']['Delete']);

        // Test
        $logging->setDelete($expected);

        // Assert
        $actual = $logging->getDelete();
        $this->assertEquals($expected, $actual);
    }

    public function testGetVersion()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = $sample['Logging']['Version'];
        $logging->setVersion($expected);

        // Test
        $actual = $logging->getVersion();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetVersion()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = new Logging();
        $expected = $sample['Logging']['Version'];

        // Test
        $logging->setVersion($expected);

        // Assert
        $actual = $logging->getVersion();
        $this->assertEquals($expected, $actual);
    }

    public function testToArray()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = Logging::create($sample['Logging']);
        $expected = array(
            'Version'         => $sample['Logging']['Version'],
            'Delete'          => $sample['Logging']['Delete'],
            'Read'            => $sample['Logging']['Read'],
            'Write'           => $sample['Logging']['Write'],
            'RetentionPolicy' => $logging->getRetentionPolicy()->toArray()
        );

        // Test
        $actual = $logging->toArray();

        // Assert
        $this->assertEquals($expected, $actual);
    }
}
