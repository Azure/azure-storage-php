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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal;

use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class ValidateTest
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ValidateTest extends \PHPUnit\Framework\TestCase
{
    public function testIsArrayWithArray()
    {
        Validate::isArray(array(), 'array');

        $this->assertTrue(true);
    }

    public function testIsArrayWithNonArray()
    {
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        Validate::isArray(123, 'array');
    }

    public function testIsStringWithString()
    {
        Validate::canCastAsString('I\'m a string', 'string');

        $this->assertTrue(true);
    }

    public function testIsStringWithNonString()
    {
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        Validate::canCastAsString(new \DateTime(), 'string');
    }

    public function testIsBooleanWithBoolean()
    {
        Validate::isBoolean(true);

        $this->assertTrue(true);
    }

    public function testIsIntegerWithInteger()
    {
        Validate::isInteger(123, 'integer');

        $this->assertTrue(true);
    }

    public function testIsIntegerWithNonInteger()
    {
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        Validate::isInteger(new \DateTime(), 'integer');
    }

    public function testIsTrueWithTrue()
    {
        Validate::isTrue(true, Resources::EMPTY_STRING);

        $this->assertTrue(true);
    }

    public function testIsTrueWithFalse()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Validate::isTrue(false, Resources::EMPTY_STRING);
    }

    public function testIsDateWithDate()
    {
        $date = Utilities::rfc1123ToDateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        Validate::isDate($date);

        $this->assertTrue(true);
    }

    public function testIsDateWithNonDate()
    {
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('DateTime')));
        Validate::isDate('not date');
    }

    public function testNotNullOrEmptyWithNonEmpty()
    {
        Validate::notNullOrEmpty(1234, 'not null');

        $this->assertTrue(true);
    }

    public function testNotNullOrEmptyWithEmpty()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Validate::notNullOrEmpty(Resources::EMPTY_STRING, 'variable');
    }

    public function testNotNullWithNull()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Validate::notNullOrEmpty(null, 'variable');
    }

    public function testIsInstanceOfStringPasses()
    {
        // Setup
        $value = 'testString';
        $stringObject = 'stringObject';

        // Test
        $result = Validate::isInstanceOf($value, $stringObject, 'value');

        // Assert
        $this->assertTrue($result);
    }

    public function testIsInstanceOfStringFail()
    {
        // Setup
        $this->setExpectedException('\InvalidArgumentException');
        $value = 'testString';
        $arrayObject = array();

        // Test
        $result = Validate::isInstanceOf($value, $arrayObject, 'value');

        // Assert
    }

    public function testIsInstanceOfArrayPasses()
    {
        // Setup
        $value = array();
        $arrayObject = array();

        // Test
        $result = Validate::isInstanceOf($value, $arrayObject, 'value');

        // Assert
        $this->assertTrue($result);
    }

    public function testIsInstanceOfArrayFail()
    {
        // Setup
        $this->setExpectedException('\InvalidArgumentException');
        $value = array();
        $stringObject = 'testString';

        // Test
        $result = Validate::isInstanceOf($value, $stringObject, 'value');

        // Assert
    }

    public function testIsInstanceOfIntPasses()
    {
        // Setup
        $value = 38;
        $intObject = 83;

        // Test
        $result = Validate::isInstanceOf($value, $intObject, 'value');

        // Assert
        $this->assertTrue($result);
    }

    public function testIsInstanceOfIntFail()
    {
        // Setup
        $this->setExpectedException('\InvalidArgumentException');
        $value = 38;
        $stringObject = 'testString';

        // Test
        $result = Validate::isInstanceOf($value, $stringObject, 'value');

        // Assert
    }


    public function testIsInstanceOfNullValue()
    {
        // Setup
        $value = null;
        $arrayObject = array();

        // Test
        $result = Validate::isInstanceOf($value, $arrayObject, 'value');

        // Assert
        $this->assertTrue($result);
    }

    public function testIsDoubleSuccess()
    {
        // Setup
        $value = 3.14159265;

        // Test
        Validate::isDouble($value, 'value');

        // Assert
        $this->assertTrue(true);
    }

    public function testIsDoubleFail()
    {
        // Setup
        $this->setExpectedException('\InvalidArgumentException');
        $value = 'testInvalidDoubleValue';

        // Test
        Validate::isDouble($value, 'value');

        // Assert
    }

    public function testGetValidateUri()
    {
        // Test
        $function = Validate::getIsValidUri();

        // Assert
        $this->assertInternalType('object', $function);
    }

    public function testIsValidUriPass()
    {
        // Setup
        $value = 'http://test.com';

        // Test
        $result = Validate::isValidUri($value);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsValidUriNull()
    {
        // Setup
        $this->setExpectedException(get_class(new \RuntimeException('')));
        $value = null;

        // Test
        $result = Validate::isValidUri($value);

        // Assert
    }

    public function testIsValidUriNotUri()
    {
        // Setup
        $this->setExpectedException(get_class(new \RuntimeException('')));
        $value = 'test string';

        // Test
        $result = Validate::isValidUri($value);

        // Assert
    }

    public function testIsObjectPass()
    {
        // Setup
        $value = new \stdClass();

        // Test
        $result = Validate::isObject($value, 'value');

        // Assert
        $this->assertTrue($result);
    }

    public function testIsObjectNull()
    {
        // Setup
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        $value = null;

        // Test
        $result = Validate::isObject($value, 'value');

        // Assert
    }

    public function testIsObjectNotObject()
    {
        // Setup
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        $value = 'test string';

        // Test
        $result = Validate::isObject($value, 'value');

        // Assert
    }

    public function testIsAResourcesPasses()
    {
        // Setup
        $value = new Resources();
        $type = 'MicrosoftAzure\Storage\Common\Internal\Resources';

        // Test
        $result = Validate::isA($value, $type, 'value');

        // Assert
        $this->assertTrue($result);
    }

    public function testIsANull()
    {
        // Setup
        $this->setExpectedException('\InvalidArgumentException');
        $value = null;
        $type = 'MicrosoftAzure\Storage\Common\Internal\Resources';

        // Test
        $result = Validate::isA($value, $type, 'value');

        // Assert
    }

    public function testIsAInvalidClass()
    {
        // Setup
        $this->setExpectedException('\InvalidArgumentException');
        $value = new Resources();
        $type = 'Some\Invalid\Class';

        // Test
        $result = Validate::isA($value, $type, 'value');

        // Assert
    }

    public function testIsANotAClass()
    {
        // Setup
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        $value = 'test string';
        $type = 'MicrosoftAzure\Storage\Common\Internal\Resources';

        // Test
        $result = Validate::isA($value, $type, 'value');

        // Assert
    }

    public function testIsDateStringValid()
    {

        // Setup
        $value = '2013-11-25';

        // Test
        $result = Validate::isDateString($value, 'name');

        // Assert
        $this->assertTrue($result);
    }

    public function testIsDateStringNotValid()
    {

        // Setup
        $this->setExpectedException('\InvalidArgumentException');
        $value = 'not a date';

        // Test
        $result = Validate::isDateString($value, 'name');

        // Assert
    }
}
