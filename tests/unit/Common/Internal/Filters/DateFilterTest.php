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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Filters;
use MicrosoftAzure\Storage\Common\Internal\Filters\DateFilter;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

/**
 * Unit tests for class DateFilter
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class DateFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Filters\DateFilter::handleRequest
     */
    public function testHandleRequest()
    {
        // Setup
        $uri = new Uri('http://microsoft.com');
        $request = new Request('Get', $uri, array(), NULL);
        $filter = new DateFilter();
        
        // Test
        $request = $filter->handleRequest($request);
        
        // Assert
        $this->assertArrayHasKey(Resources::DATE, $request->getHeaders());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Filters\DateFilter::handleResponse
     */
    public function testHandleResponse()
    {
        // Setup
        $uri = new Uri('http://microsoft.com');
        $request = new Request('Get', $uri, array(), NULL);
        $response = null;
        $filter = new DateFilter();
        
        // Test
        $response = $filter->handleResponse($request, $response);
        
        // Assert
        $this->assertNull($response);
    }
}


