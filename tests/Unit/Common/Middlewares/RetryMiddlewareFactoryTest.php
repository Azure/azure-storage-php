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
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Middlewares;

use MicrosoftAzure\Storage\Common\Middlewares\RetryMiddlewareFactory;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\ReflectionTestBase;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

class RetryMiddlewareFactoryTest extends ReflectionTestBase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage should be positive number
     */
    public function testCreateWithNegativeNumberOfRetries()
    {
        $stack = RetryMiddlewareFactory::create(
            RetryMiddlewareFactory::GENERAL_RETRY_TYPE,
            -1,
            Resources::DEFAULT_RETRY_INTERVAL,
            RetryMiddlewareFactory::LINEAR_INTERVAL_ACCUMULATION
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage should be positive number
     */
    public function testCreateWithNegativeInterval()
    {
        $stack = RetryMiddlewareFactory::create(
            RetryMiddlewareFactory::GENERAL_RETRY_TYPE,
            Resources::DEFAULT_NUMBER_OF_RETRIES,
            -1,
            RetryMiddlewareFactory::LINEAR_INTERVAL_ACCUMULATION
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is invalid
     */
    public function testCreateWithInvalidType()
    {
        $stack = RetryMiddlewareFactory::create(
            'string that does not make sense',
            Resources::DEFAULT_NUMBER_OF_RETRIES,
            Resources::DEFAULT_RETRY_INTERVAL,
            RetryMiddlewareFactory::LINEAR_INTERVAL_ACCUMULATION
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is invalid
     */
    public function testCreateWithInvalidAccumulationMethod()
    {
        $stack = RetryMiddlewareFactory::create(
            RetryMiddlewareFactory::GENERAL_RETRY_TYPE,
            Resources::DEFAULT_NUMBER_OF_RETRIES,
            Resources::DEFAULT_RETRY_INTERVAL,
            'string that does not make sense'
        );
    }

    public function testCreateRetryDeciderWithGeneralRetryDecider()
    {
        $createRetryDecider = self::getMethod('createRetryDecider', new RetryMiddlewareFactory());
        $generalDecider = $createRetryDecider->invokeArgs(
            null,
            array(RetryMiddlewareFactory::GENERAL_RETRY_TYPE, 3, false)
        );
        $request = new Request('PUT', '127.0.0.1');
        $retryResult_1 = $generalDecider(1, $request, new Response(408));//retry
        $retryResult_2 = $generalDecider(1, $request, new Response(501));//no-retry
        $retryResult_3 = $generalDecider(1, $request, new Response(505));//no-retry
        $retryResult_4 = $generalDecider(1, $request, new Response(200));//no-retry
        $retryResult_5 = $generalDecider(1, $request, new Response(503));//retry
        $retryResult_6 = $generalDecider(4, $request, new Response(503));//no-retry
        $retryResult_7 = $generalDecider(1, $request, null, new ConnectException('message', $request));//no-retry

        //assert
        $this->assertTrue($retryResult_1);
        $this->assertFalse($retryResult_2);
        $this->assertFalse($retryResult_3);
        $this->assertFalse($retryResult_4);
        $this->assertTrue($retryResult_5);
        $this->assertFalse($retryResult_6);
        $this->assertFalse($retryResult_7);
    }

    public function testCreateRetryDeciderWithConnectionRetries()
    {
        $createRetryDecider = self::getMethod('createRetryDecider', new RetryMiddlewareFactory());
        $generalDecider = $createRetryDecider->invokeArgs(
            null,
            array(RetryMiddlewareFactory::GENERAL_RETRY_TYPE, 3, true)
        );
        $request = new Request('PUT', '127.0.0.1');
        $retryResult = $generalDecider(1, $request, null, new ConnectException('message', $request));
        $this->assertTrue($retryResult);
    }

    public function testCreateLinearDelayCalculator()
    {
        $creator = self::getMethod('createLinearDelayCalculator', new RetryMiddlewareFactory());
        $linearDelayCalculator = $creator->invokeArgs(null, array(1000));
        for ($index = 0; $index < 10; ++$index) {
            $this->assertEquals($index * 1000, $linearDelayCalculator($index));
        }
    }

    public function testCreateExponentialDelayCalculator()
    {
        $creator = self::getMethod('createExponentialDelayCalculator', new RetryMiddlewareFactory());
        $exponentialDelayCalculator = $creator->invokeArgs(null, array(1000));
        for ($index = 0; $index < 3; ++$index) {
            $pow = (int)\pow(2, $index);
            $this->assertEquals($pow * 1000, $exponentialDelayCalculator($index));
        }
    }
}
