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
 * @package   MicrosoftAzure\Storage\Tests\Mock\Common\Internal\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Tests\Mock\Common\Internal\Filters;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Alters request headers and response to mock real filter
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Mock\Common\Internal\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class SimpleFilterMock implements \MicrosoftAzure\Storage\Common\Internal\IServiceFilter
{
    private $_headerName;
    private $_data;
    
    public function __construct($headerName, $data)
    {
        $this->_data       = $data;
        $this->_headerName = $headerName;
    }
    
    public function handleRequest($request)
    {
        return $request->withHeader($this->_headerName, $this->_data)
                       ->withHeader('Accept-Encoding', 'identity');
    }
    
    public function handleResponse($request, $response)
    {
        $body = $response->getBody();
        return $response->withBody($body.$this->_data);
    }
}


