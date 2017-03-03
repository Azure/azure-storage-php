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
 * @package   MicrosoftAzure\Storage\Common\Middlewares
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common\Middlewares;

use MicrosoftAzure\Storage\Common\Internal\Validate;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\RejectedPromise;

/**
 * This class provides the functionality to log the requests/options/responses.
 * Logging large number of entries may exhaust the memory.
 *
 * The middleware should be pushed into client options if the logging is
 * intended to persist between different API calls.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Middlewares
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.12.1
 * @link      https://github.com/azure/azure-storage-php
 */
class HistoryMiddleware extends MiddlewareBase
{
    private $history;

    /**
     * Gets the saved paried history.
     *
     * @return array
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $history = array();
    }

    /**
     * Add an entry to history
     *
     * @param array $entry the entry to be added.
     */
    public function addHistory(array $entry)
    {
        Validate::isTrue(
            array_key_exists('request', $entry) &&
            array_key_exists('options', $entry) &&
            (array_key_exists('response', $entry) ||
            array_key_exists('reason', $entry)),
            'Given history entry not in correct format'
        );
        $this->history[] = $entry;
    }

    /**
     * Clear the history
     *
     * @return void
     */
    public function clearHistory()
    {
        $this->history = array();
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
        $reflection = $this;
        return function (ResponseInterface $response) use (
            $reflection,
            $request,
            $options
        ) {
            $reflection->addHistory([
                'request'  => $request,
                'response' => $response,
                'options'  => $options
            ]);
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
        $reflection = $this;
        return function ($reason) use (
            $reflection,
            $request,
            $options
        ) {
            $reflection->addHistory([
                'request' => $request,
                'reason'  => $reason,
                'options' => $options
            ]);
            return new RejectedPromise($reason);
        };
    }
}
