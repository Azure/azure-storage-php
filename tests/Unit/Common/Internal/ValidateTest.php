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
class ValidateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isArray
     */
    public function testIsArrayWithArray()
    {
        Validate::isArray(array(), 'array');

        $this->assertTrue(true);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isArray
     */
    public function testIsArrayWithNonArray()
    {
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        Validate::isArray(123, 'array');
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::canCastAsString
     */
    public function testIsStringWithString()
    {
        Validate::canCastAsString('I\'m a string', 'string');

        $this->assertTrue(true);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::canCastAsString
     */
    public function testIsStringWithNonString()
    {
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        Validate::canCastAsString(new \DateTime(), 'string');
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isBoolean
     */
    public function testIsBooleanWithBoolean()
    {
        Validate::isBoolean(true);

        $this->assertTrue(true);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInteger
     */
    public function testIsIntegerWithInteger()
    {
        Validate::isInteger(123, 'integer');

        $this->assertTrue(true);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInteger
     */
    public function testIsIntegerWithNonInteger()
    {
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        Validate::isInteger(new \DateTime(), 'integer');
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isTrue
     */
    public function testIsTrueWithTrue()
    {
        Validate::isTrue(true, Resources::EMPTY_STRING);

        $this->assertTrue(true);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isTrue
     */
    public function testIsTrueWithFalse()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Validate::isTrue(false, Resources::EMPTY_STRING);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isDate
     */
    public function testIsDateWithDate()
    {
        $date = Utilities::rfc1123ToDateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        Validate::isDate($date);

        $this->assertTrue(true);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isDate
     */
    public function testIsDateWithNonDate()
    {
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('DateTime')));
        Validate::isDate('not date');
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::notNullOrEmpty
     */
    public function testNotNullOrEmptyWithNonEmpty()
    {
        Validate::notNullOrEmpty(1234, 'not null');

        $this->assertTrue(true);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::notNullOrEmpty
     */
    public function testNotNullOrEmptyWithEmpty()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Validate::notNullOrEmpty(Resources::EMPTY_STRING, 'variable');
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::notNull
     */
    public function testNotNullWithNull()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Validate::notNullOrEmpty(null, 'variable');
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInstanceOf
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInstanceOf
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInstanceOf
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInstanceOf
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInstanceOf
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInstanceOf
     */
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


    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isInstanceOf
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isDouble
     */
    public function testIsDoubleSuccess()
    {
        // Setup
        $value = 3.14159265;

        // Test
        Validate::isDouble($value, 'value');

        // Assert
        $this->assertTrue(true);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isDouble
     */
    public function testIsDoubleFail()
    {
        // Setup
        $this->setExpectedException('\InvalidArgumentException');
        $value = 'testInvalidDoubleValue';

        // Test
        Validate::isDouble($value, 'value');

        // Assert
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isDouble
     */
    public function testGetValidateUri()
    {
        // Test
        $function = Validate::getIsValidUri();

        // Assert
        $this->assertInternalType('object', $function);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isValidUri
     */
    public function testIsValidUriPass()
    {
        // Setup
        $value = 'http://test.com';

        // Test
        $result = Validate::isValidUri($value);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isValidUri
     */
    public function testIsValidUriNull()
    {
        // Setup
        $this->setExpectedException(get_class(new \RuntimeException('')));
        $value = null;

        // Test
        $result = Validate::isValidUri($value);

        // Assert
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isValidUri
     */
    public function testIsValidUriNotUri()
    {
        // Setup
        $this->setExpectedException(get_class(new \RuntimeException('')));
        $value = 'test string';

        // Test
        $result = Validate::isValidUri($value);

        // Assert
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isObject
     */
    public function testIsObjectPass()
    {
        // Setup
        $value = new \stdClass();

        // Test
        $result = Validate::isObject($value, 'value');

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isObject
     */
    public function testIsObjectNull()
    {
        // Setup
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        $value = null;

        // Test
        $result = Validate::isObject($value, 'value');

        // Assert
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isObject
     */
    public function testIsObjectNotObject()
    {
        // Setup
        $this->setExpectedException(get_class(new InvalidArgumentTypeException('')));
        $value = 'test string';

        // Test
        $result = Validate::isObject($value, 'value');

        // Assert
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isA
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isA
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isA
     */
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

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isA
     */
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

//     /**
//      * @covers MicrosoftAzure\Storage\Common\Internal\Validate::methodExists
//      */
//     public function testMethodExistsIfExists(){

//         // Setup
//         $asset = new Asset(Asset::OPTIONS_NONE);
//         $method = 'getState';

//         // Test
//         $result = Validate::methodExists($asset, $method, 'MicrosoftAzure\Storage\MediaServices\Models\Asset');

//         // Assert
//         $this->assertTrue($result);
//     }

//     /**
//      * @covers MicrosoftAzure\Storage\Common\Internal\Validate::methodExists
//      */
//     public function testMethodExistsIfNotExists(){

//         // Setup
//         $this->setExpectedException('\InvalidArgumentException');
//         $asset = new Asset(Asset::OPTIONS_NONE);
//         $method = 'setCreated';

//         // Test
//         $result = Validate::methodExists($asset, $method, 'MicrosoftAzure\Storage\MediaServices\Models\Asset');

//         // Assert
//     }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isDateString
     */
    public function testIsDateStringValid()
    {

        // Setup
        $value = '2013-11-25';

        // Test
        $result = Validate::isDateString($value, 'name');

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Validate::isDateString
     */
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
