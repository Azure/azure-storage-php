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
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Authentication;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use MicrosoftAzure\Storage\Common\Internal\Authentication\SharedAccessSignatureAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy;
use MicrosoftAzure\Storage\Tests\Unit\Utilities;
use MicrosoftAzure\Storage\Tests\Mock\Common\Internal\Authentication\SharedAccessSignatureAuthSchemeMock;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Unit tests for SharedAccessSignatureAuthScheme class.
 *
 * @package    MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Authentication
 * @author     Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright  2016 Microsoft Corporation
 * @license    https://github.com/azure/azure-storage-php/LICENSE
 * @link       https://github.com/azure/azure-storage-php
 */
class SharedAccessSignatureAuthSchemeTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers MicrosoftAzure\Storage\Common\Internal\Authentication\SharedAccessSignatureAuthScheme::__construct
    */
    public function testConstruct()
    {
        $mock = new SharedAccessSignatureAuthSchemeMock(TestResources::SAS_TOKEN);
        $this->assertEquals(TestResources::SAS_TOKEN, $mock->getSasToken());
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\Internal\Authentication\SharedAccessSignatureAuthScheme::signRequest
    */
    public function testSignRequest()
    {
        // Setup
        $mock = new SharedAccessSignatureAuthSchemeMock(TestResources::SAS_TOKEN);
        $uri = new Uri(TestResources::URI2);
        $request = new Request('Get', $uri, array(), null);
        $expected = new Uri(TestResources::URI2 . '&' . TestResources::SAS_TOKEN);

        // Test
        $actual = $mock->signRequest($request)->getUri();

        $this->assertEquals($expected, $actual);
    }
}
