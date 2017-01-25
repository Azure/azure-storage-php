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
 * @package   MicrosoftAzure\Storage\Common\Internal\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Common\Internal\Filters;

use MicrosoftAzure\Storage\Common\Internal\Authentication\IAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\IServiceFilter;
use MicrosoftAzure\Storage\Common\Internal\HttpFormatter;
use GuzzleHttp\Psr7;

/**
 * Adds authentication header to the http request object.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.12.1
 * @link      https://github.com/azure/azure-storage-php
 */
class AuthenticationFilter implements IServiceFilter
{
    /**
     * @var MicrosoftAzure\Storage\Common\Internal\Authentication\IAuthScheme
     */
    private $_authenticationScheme;

    /**
     * Creates AuthenticationFilter with the passed scheme.
     *
     * @param IAuthScheme $authenticationScheme The authentication scheme.
     */
    public function __construct(IAuthScheme $authenticationScheme)
    {
        $this->_authenticationScheme = $authenticationScheme;
    }
    
    /**
     * Adds authentication header to the request headers.
     *
     * @param \GuzzleHttp\Psr7\Request $request HTTP request object.
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function handleRequest(\GuzzleHttp\Psr7\Request $request)
    {
        return $this->_authenticationScheme->signRequest($request);
    }
    
    /**
     * Does nothing with the response.
     *
     * @param \GuzzleHttp\Psr7\Request  $request  HTTP request object.
     * @param \GuzzleHttp\Psr7\Response $response HTTP response object.
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function handleResponse(
        \GuzzleHttp\Psr7\Request $request,
        \GuzzleHttp\Psr7\Response $response = null
    ) {
        // Do nothing with the response.
        return $response;
    }
}
