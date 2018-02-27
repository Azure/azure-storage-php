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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\Unit\Table\Models;

use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class EdmTypeTest
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class EdmTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testProcessTypeWithNull()
    {
        // Setup
        $expected = EdmType::STRING;

        // Test
        $actual = EdmType::processType(null);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testProcessType()
    {
        // Setup
        $expected = EdmType::BINARY;

        // Test
        $actual = EdmType::processType($expected);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeQueryValueWithString()
    {
        // Setup
        $type = EdmType::STRING;
        $value = '1234';
        $expected = $value;

        // Test
        $actual = EdmType::unserializeQueryValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeQueryValueWithBinary()
    {
        // Setup
        $type = EdmType::BINARY;
        $value = 'MTIzNDU=';
        $expected = base64_decode($value);

        // Test
        $actual = EdmType::unserializeQueryValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeQueryValueWithDate()
    {
        // Setup
        $type = EdmType::DATETIME;
        $value = '2008-10-01T15:26:13Z';

        // Test
        $actual = EdmType::unserializeQueryValue($type, $value);

        // Assert
        $this->assertInstanceOf('\DateTime', $actual);
    }

    public function testUnserializeQueryValueWithInt()
    {
        // Setup
        $type = EdmType::INT64;
        $value = '123';
        $expected = 123;

        // Test
        $actual = EdmType::unserializeQueryValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeQueryValueWithBoolean()
    {
        // Setup
        $type = EdmType::BOOLEAN;
        $value = '1';
        $expected = true;

        // Test
        $actual = EdmType::unserializeQueryValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeQueryValueWithInvalid()
    {
        // Assert
        $this->setExpectedException('\InvalidArgumentException');

        // Test
        EdmType::unserializeQueryValue('7amada', '1233');
    }

    public function testIsValid()
    {
        // Setup
        $expected = true;

        // Test
        $actual = EdmType::isValid(EdmType::STRING);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testIsValidWithInvalid()
    {
        // Setup
        $expected = false;

        // Test
        $actual = EdmType::isValid('hobba');

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testValidateEdmValueWithBinary()
    {
        // Setup
        $type = EdmType::BINARY;
        $value = 'MTIzNDU=';
        $expected = true;

        // Test
        $actual = EdmType::validateEdmValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testValidateEdmValueWithDouble()
    {
        // Setup
        $type      = EdmType::DOUBLE;
        $values    = array();
        $values[]  = 1;
        $values[]  = PHP_INT_MAX;
        $values[]  = pi();
        $values[]  = 1.0;
        $expected  = true;

        // Test
        foreach ($values as $value) {
            $actual = EdmType::validateEdmValue($type, $value);

            // Assert
            $this->assertEquals($expected, $actual);
        }
    }

    public function testValidateEdmValueWithDate()
    {
        // Setup
        $type = EdmType::DATETIME;
        $value = new \DateTime();
        $expected = true;

        // Test
        $actual = EdmType::validateEdmValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testValidateEdmValueWithInt()
    {
        // Setup
        $type = EdmType::INT64;
        $value = '123';
        $expected = true;

        // Test
        $actual = EdmType::validateEdmValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testValidateEdmValueWithBoolean()
    {
        // Setup
        $type = EdmType::BOOLEAN;
        $value = false;
        $expected = true;

        // Test
        $actual = EdmType::validateEdmValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testValidateEdmValueWithInvalid()
    {
        // Assert
        $this->setExpectedException('\InvalidArgumentException');

        // Test
        EdmType::validateEdmValue('7amada', '1233');
    }

    public function testSerializeValueWithBinary()
    {
        // Setup
        $type = EdmType::BINARY;
        $value = '010101010111';
        $expected = base64_encode($value);

        // Test
        $actual = EdmType::serializeValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSerializeValueWithDate()
    {
        // Setup
        $type = EdmType::DATETIME;
        $value = new \DateTime();
        $expected = Utilities::convertToEdmDateTime($value);

        // Test
        $actual = EdmType::serializeValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSerializeValueWithInt()
    {
        // Setup
        $type = EdmType::INT64;
        $value = 123;
        $expected = htmlspecialchars($value);

        // Test
        $actual = EdmType::serializeValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSerializeValueWithIntAsString()
    {
        // Setup
        $type = EdmType::INT32;
        $value = '123';
        $expected = 123;

        // Test
        $actual = EdmType::serializeValue($type, $value);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function testSerializeValueWithStringAsInt()
    {
        // Setup
        $type = EdmType::STRING;
        $value = 123;
        $expected = '123';

        // Test
        $actual = EdmType::serializeValue($type, $value);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function testSerializeValueWithBoolean()
    {
        // Setup
        $type = EdmType::BOOLEAN;
        $value = true;
        $expected = ($value == true ? '1' : '0');

        // Test
        $actual = EdmType::serializeValue($type, $value);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSerializeValueWithInvalid()
    {
        // Assert
        $this->setExpectedException('\InvalidArgumentException');

        // Test
        EdmType::serializeValue('7amada', '1233');
    }
}
