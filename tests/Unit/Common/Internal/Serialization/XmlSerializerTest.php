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
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Serialization;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;

/**
 * Unit tests for class XmlSerializer
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Serialization
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class XmlSerializerTest extends \PHPUnit\Framework\TestCase
{
    public function testUnserialize()
    {
        // Setup
        $xmlSerializer = new XmlSerializer();
        $propertiesSample = TestResources::getServicePropertiesSample();
        $properties = ServiceProperties::create($propertiesSample);
        $xml = $properties->toXml($xmlSerializer);
        $expected = $properties->toArray();
        // Test
        $actual = $xmlSerializer->unserialize($xml);

        $this->assertEquals($propertiesSample, $actual);
    }

    public function testSerialize()
    {
        // Setup
        $xmlSerializer = new XmlSerializer();
        $propertiesSample = TestResources::getServicePropertiesSample();
        $properties = ServiceProperties::create($propertiesSample);
        $expected = $properties->toXml($xmlSerializer);
        $array = $properties->toArray();
        $serializerProperties = array(XmlSerializer::ROOT_NAME => "StorageServiceProperties");

        // Test
        $actual = $xmlSerializer->serialize($array, $serializerProperties);

        $this->assertEquals($expected, $actual);
    }

    public function testSerializeAttribute()
    {
        // Setup
        $xmlSerializer = new XmlSerializer();
        $expected = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<Object field1="value1" field2="value2"/>' . "\n";

        $object = array(
            '@attributes' => array(
                'field1' => 'value1',
                'field2' => 'value2'
            )
        );
        $serializerProperties = array(XmlSerializer::ROOT_NAME => 'Object');

        // Test
        $actual = $xmlSerializer->serialize($object, $serializerProperties);

        $this->assertEquals($expected, $actual);
    }

    public function testObjectSerializeSucceess()
    {
        // Setup
        $expected = "<DummyClass/>\n";
        $target = new DummyClass();

        // Test
        $actual = XmlSerializer::objectSerialize($target, 'DummyClass');

        // Assert
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    public function testObjectSerializeSucceessWithAttributes()
    {
        // Setup
        $expected = "<DummyClass testAttribute=\"testAttributeValue\"/>\n";
        $target = new DummyClass();
        $target->addAttribute('testAttribute', 'testAttributeValue');

        // Test
        $actual = XmlSerializer::objectSerialize($target, 'DummyClass');

        // Assert
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    public function testObjectSerializeInvalidObject()
    {
        // Setup
        $this->setExpectedException(get_class(new \InvalidArgumentException()));
        // Test
        $actual = XmlSerializer::objectSerialize(null, null);
        // Assert
    }
}
