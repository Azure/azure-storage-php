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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Http
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Http;

use MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext;

/**
 * Unit tests for class HttpCallContext
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Http
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class HttpCallContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::__construct
     */
    public function testConstruct()
    {
        // Test
        $context = new HttpCallContext();

        // Assert
        $this->assertNull($context->getBody());
        $this->assertNull($context->getMethod());
        $this->assertNull($context->getPath());
        $this->assertNull($context->getUri());
        $this->assertTrue(is_array($context->getHeaders()));
        $this->assertTrue(is_array($context->getQueryParameters()));
        $this->assertTrue(is_array($context->getStatusCodes()));

        return $context;
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getMethod
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::setMethod
     * @depends testConstruct
     */
    public function testSetMethod($context)
    {
        // Setup
        $expected = 'Method';

        // Test
        $context->setMethod($expected);

        // Assert
        $this->assertEquals($expected, $context->getMethod());
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getBody
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::setBody
     * @depends testConstruct
     */
    public function testSetBody($context)
    {
        // Setup
        $expected = 'Body';

        // Test
        $context->setBody($expected);

        // Assert
        $this->assertEquals($expected, $context->getBody());
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getPath
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::setPath
     * @depends testConstruct
     */
    public function testSetPath($context)
    {
        // Setup
        $expected = 'Path';

        // Test
        $context->setPath($expected);

        // Assert
        $this->assertEquals($expected, $context->getPath());
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getUri
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::setUri
     * @depends testConstruct
     */
    public function testSetUri($context)
    {
        // Setup
        $expected = 'http://www.microsoft.com';

        // Test
        $context->setUri($expected);

        // Assert
        $this->assertEquals($expected, $context->getUri());
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getHeaders
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::setHeaders
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::addHeader
     * @depends testConstruct
     */
    public function testSetHeaders($context)
    {
        // Setup
        $expected = array('value1', 'value2', 'value3');

        // Test
        $context->setHeaders($expected);

        // Assert
        $this->assertEquals($expected, $context->getHeaders());
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getQueryParameters
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::setQueryParameters
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::addQueryParameter
     * @depends testConstruct
     */
    public function testSetQueryParameters($context)
    {
        // Setup
        $expected = array('value1', 'value2', 'value3');

        // Test
        $context->setQueryParameters($expected);

        // Assert
        $this->assertEquals($expected, $context->getQueryParameters());
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getStatusCodes
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::setStatusCodes
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::addStatusCode
     * @depends testConstruct
     */
    public function testSetStatusCodes($context)
    {
        // Setup
        $expected = array(1, 2, 3);

        // Test
        $context->setStatusCodes($expected);

        // Assert
        $this->assertEquals($expected, $context->getStatusCodes());
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getHeader
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::addHeader
     * @depends testConstruct
     */
    public function testAddHeader($context)
    {
        // Setup
        $expected = 'value';
        $key = 'key';

        // Test
        $context->addHeader($key, $expected);

        // Assert
        $this->assertEquals($expected, $context->getHeader($key));
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::removeHeader
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::getHeaders
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::addHeader
     * @depends testConstruct
     */
    public function testRemoveHeader($context)
    {
        // Setup
        $value = 'value';
        $key = 'key';
        $context->addHeader($key, $value);

        // Test
        $context->removeHeader($key);

        // Assert
        $this->assertFalse(array_key_exists($key, $context->getHeaders()));
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext::__toString
     * @depends testConstruct
     */
    public function testToString($context)
    {
        // Setup
        $headers = array('h1' => 'v1', 'h2' => 'v2');
        $method = 'GET';
        $uri = 'http://microsoft.com';
        $path = 'windowsazure/services';
        $body = 'The request body';
        $expected = "GET http://microsoft.com/windowsazure/services HTTP/1.1\nh1: v1\nh2: v2\n\nThe request body";
        $context->setHeaders($headers);
        $context->setMethod($method);
        $context->setUri($uri);
        $context->setPath($path);
        $context->setBody($body);

        // Test
        $actual = $context->__toString();

        // Assert
        $this->assertEquals($expected, $actual);
    }
}
