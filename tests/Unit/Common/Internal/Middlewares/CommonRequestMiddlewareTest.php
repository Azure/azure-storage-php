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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Middlewares
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Middlewares;

use MicrosoftAzure\Storage\Common\Internal\Middlewares\CommonRequestMiddleware;
use MicrosoftAzure\Storage\Common\Internal\Authentication\SharedKeyAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\ReflectionTestBase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

/**
 * Unit tests for class CommonRequestMiddleware
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Middlewares
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CommonRequestMiddlewareTest extends ReflectionTestBase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Middlewares\CommonRequestMiddleware::onRequest
     * @covers MicrosoftAzure\Storage\Common\Internal\Middlewares\CommonRequestMiddleware::__construct
     */
    public function testOnRequest()
    {
        // Setup
        $beginTime = time();
        $headers = self::getTestHeaderArray();
        $authScheme = new SharedKeyAuthScheme('accountname', 'accountkey');
        // Construct
        $middleware = new CommonRequestMiddleware($authScheme, $headers);
        $onRequest = self::getMethod('onRequest', $middleware);
        $request = new Request('GET', 'http://www.bing.com');
        // Apply middleware
        $newRequest = $onRequest->invokeArgs($middleware, array($request));
        // Prepare expected
        $savedHeaders = array();
        foreach ($newRequest->getHeaders() as $key => $value) {
            $savedHeaders[$key] = $value[0];
        }
        $requestToSign = $newRequest->withoutHeader(Resources::AUTHENTICATION);
        $signedRequest = $authScheme->signRequest($requestToSign);
        
        // Assert
        $this->assertTrue(
            (array_intersect($savedHeaders, $headers) === $headers),
            'Did not add proper headers.'
        );
        $this->assertTrue(
            $signedRequest->getHeaders() === $newRequest->getHeaders(),
            'Failed to create same signed request.'
        );
        $endTime = time();
        $requestTime = strtotime($newRequest->getHeaders()[Resources::DATE][0]);
        $this->assertTrue(
            $requestTime >= $beginTime && $requestTime <= $endTime,
            'Did not add proper date header.'
        );
    }

    private static function getTestHeaderArray()
    {
        return array(
            'testKey1' => 'testValue1',
            'testKey2' => 'testValue2',
            'testKey3' => 'testValue3',
        );
    }
}
