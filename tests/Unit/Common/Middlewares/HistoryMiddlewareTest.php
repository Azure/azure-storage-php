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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Middlewares
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Middlewares;

use MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware;
use MicrosoftAzure\Storage\Tests\Framework\ReflectionTestBase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

/**
 * Unit tests for class HistoryMiddleware
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Middlewares
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class HistoryMiddlewareTest extends ReflectionTestBase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::onFulfilled
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::getHistory
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::__construct
     */
    public function testOnFulfilled()
    {
        $middleware = new HistoryMiddleware();
        $onFulfilled = self::getMethod('onFulfilled', $middleware);
        $request = new Request('GET', 'http://www.bing.com');
        $callable = $onFulfilled->invokeArgs($middleware, array($request, array()));
        $response = new Response();
        $newResponse = $callable($response);
        $entry = $middleware->getHistory()[0];
        $this->assertTrue(
            $response === $entry['response'] &&
            $request  === $entry['request'] &&
            array()   === $entry['options'],
            'History does not match the request, response and/or options'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::onRejected
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::getHistory
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::__construct
     */
    public function testOnRejected()
    {
        $middleware = new HistoryMiddleware();
        $onRejected = self::getMethod('onRejected', $middleware);
        $request = new Request('GET', 'http://www.bing.com');
        $callable = $onRejected->invokeArgs($middleware, array($request, array()));
        $reason = new RequestException('test message', $request);
        $promise = $callable($reason);
        $entry = $middleware->getHistory()[0];
        $newReason = null;
        try {
            $promise->wait();
        } catch (RequestException $e) {
            $newReason = $e;
        }
        $this->assertTrue(
            $newReason === $entry['reason'] &&
            $request   === $entry['request'] &&
            array()    === $entry['options'],
            'History does not match the request, reason and/or options'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::addHistory
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::clearHistory
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::getHistory
     * @covers MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware::__construct
     */
    public function testAddGetClearHistory()
    {
        $middleware = new HistoryMiddleware();
        $request = new Request('GET', 'http://www.bing.com');
        $response = new Response();
        $options = array();
        $reason = new RequestException('test message', $request);

        $middleware->addHistory([
            'request'  => $request,
            'response' => $response,
            'options'  => $options
        ]);

        $this->assertTrue(count($middleware->getHistory()) == 1, 'Wrong array size');

        $middleware->addHistory([
            'request' => $request,
            'reason'  => $reason,
            'options' => $options
        ]);

        $this->assertTrue(count($middleware->getHistory()) == 2, 'Wrong array size');

        $middleware->clearHistory();

        $this->assertTrue(count($middleware->getHistory()) == 0, 'Wrong array size');
    }
}
