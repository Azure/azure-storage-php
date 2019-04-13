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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Authentication
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2019 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Authentication;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use MicrosoftAzure\Storage\Common\Internal\Authentication\TokenAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy;
use MicrosoftAzure\Storage\Tests\Framework\ReflectionTestBase;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for TokenAuthScheme class.
 *
 * @package    MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Authentication
 * @author     Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright  2019 Microsoft Corporation
 * @license    https://github.com/azure/azure-storage-php/LICENSE
 * @link       https://github.com/azure/azure-storage-php
 */
class TokenAuthSchemeTest extends ReflectionTestBase
{
    public function testConstruct()
    {
        $bearerToken = '';
        $mock = new TokenAuthScheme($bearerToken, TestResources::TOKEN_CS);

        $this->assertEquals('', $this->getProperty('tokenRef', $mock)->getValue($mock));
        $bearerToken = 'changed';
        $this->assertEquals('changed', $this->getProperty('tokenRef', $mock)->getValue($mock));
    }

    public function testSignRequest()
    {
        $bearerToken = '';
        $mock = new TokenAuthScheme($bearerToken, TestResources::TOKEN_CS);
        $uri = new Uri(TestResources::URI2);
        $request = new Request('Get', $uri, array(), null);
        $actual = $mock->signRequest($request);
        $this->assertArrayHasKey(strtolower(Resources::AUTHENTICATION), $actual->getHeaders());
        $this->assertEquals(
            'Bearer', // Trims the trailing space
            $actual->getHeaders()[strtolower(Resources::AUTHENTICATION)][0]
        );

        $bearerToken = 'changed';
        $request = new Request('Get', $uri, array(), null);
        $actual = $mock->signRequest($request);
        $this->assertArrayHasKey(strtolower(Resources::AUTHENTICATION), $actual->getHeaders());
        $this->assertEquals(
            'Bearer changed',
            $actual->getHeaders()[strtolower(Resources::AUTHENTICATION)][0]
        );
    }
}
