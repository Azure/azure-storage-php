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
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common\Internal\Middlewares;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use MicrosoftAzure\Storage\Common\Internal\IMiddleware;

/**
 * This class provides the base structure of middleware that can be used for
 * doing customized behavior including modifying the request, response or
 * other behaviors like logging, retrying and debugging.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.12.0
 * @link      https://github.com/azure/azure-storage-php
 */
class MiddlewareBase implements IMiddleware
{

    final public function __invoke(callable $handler)
    {
        $reflection = $this;
        return function ($request, $options) use ($handler, $reflection) {
            $request = $reflection->onRequest($request);
            return $handler($request, $options)->then(
                $reflection->onFulfilled($request, $options),
                $reflection->onRejected($request, $options)
            );
        };
    }

    /**
     * This function will be executed before the request is sent.
     *
     * @param  RequestInterface $request the request before altered.
     *
     * @return RequestInterface          the request after altered.
     */
    protected function onRequest(RequestInterface $request)
    {
        //do nothing
        return $request;
    }

    /**
     * This function will be invoked after the request is sent, if
     * the promise is fulfilled.
     *
     * @param  RequestInterface $request the request sent.
     * @param  array            $options the options that the request sent with.
     *
     * @return callable
     */
    protected function onFulfilled(RequestInterface $request, array $options)
    {
        return function (ResponseInterface $response) {
            //do nothing
            return $response;
        };
    }

    /**
     * This function will be executed after the request is sent, if
     * the promise is rejected.
     *
     * @param  RequestInterface $request the request sent.
     * @param  array            $options the options that the request sent with.
     *
     * @return callable
     */
    protected function onRejected(RequestInterface $request, array $options)
    {
        return function ($reason) {
            //do nothing
            return new RejectedPromise($reason);
        };
    }
}
