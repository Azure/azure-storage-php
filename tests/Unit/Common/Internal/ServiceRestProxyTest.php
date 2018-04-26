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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Mock\Common\Internal\Filters\SimpleFilterMock;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;

/**
 * Unit tests for class ServiceRestProxy
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ServiceRestProxyTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        // Setup
        $primaryUri       = 'http://www.microsoft.com';
        $secondaryUri     = 'http://www.bing.com';
        $accountName      = 'myaccount';
        $options['https'] = ['verify' => __DIR__ . "/TestFiles/cacert.pem"];

        // Test
        $proxy = new ServiceRestProxy(
            $primaryUri,
            $secondaryUri,
            $accountName,
            $options
        );

        // Assert
        $this->assertNotNull($proxy);
        $this->assertEquals($accountName, $proxy->getAccountName());

        // Auto append an '/' at the end of uri.
        $this->assertEquals($primaryUri . '/', (string)($proxy->getPsrPrimaryUri()));
        $this->assertEquals($secondaryUri . '/', (string)($proxy->getPsrSecondaryUri()));


        return $proxy;
    }

    public function testSettingVerifyOptions()
    {
        // Setup
        $primaryUri       = 'http://www.microsoft.com';
        $secondaryUri     = 'http://www.bing.com';
        $accountName      = 'myaccount';
        $options['http'] = ['verify' => __DIR__ . "/TestFiles/cacert.pem"];

        // Test
        $proxy = new ServiceRestProxy(
            $primaryUri,
            $secondaryUri,
            $accountName,
            $options
        );

        $ref = new \ReflectionProperty(ServiceRestProxy::class, "client");
        $ref->setAccessible(true);
        /** @var Client $client */
        $client = $ref->getValue($proxy);
        self::assertSame($options['http']['verify'], $client->getConfig('verify'));
    }

    /**
     * @depends testConstruct
     */
    public function testGroupQueryValues()
    {
        // Setup
        $values = array('A', 'B', 'C');
        $expected = 'A,B,C';

        // Test
        $actual = ServiceRestProxy::groupQueryValues($values);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testConstruct
     */
    public function testGroupQueryValuesWithUnorderedValues()
    {
        // Setup
        $values = array('B', 'C', 'A');
        $expected = 'A,B,C';

        // Test
        $actual = ServiceRestProxy::groupQueryValues($values);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testGroupQueryValuesWithNulls()
    {
        // Setup
        $values = array(null, '', null);

        // Test
        $actual = ServiceRestProxy::groupQueryValues($values);

        // Assert
        $this->assertEmpty($actual);
    }

    /**
     * @depends testConstruct
     */
    public function testGroupQueryValuesWithMix()
    {
        // Setup
        $values = array(null, 'B', 'C', '');
        $expected = 'B,C';

        // Test
        $actual = ServiceRestProxy::groupQueryValues($values);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    /**
    * @depends testConstruct
    */
    public function testPostParameter($restRestProxy)
    {
        // Setup
        $postParameters = array();
        $key = 'a';
        $expected = 'b';

        // Test
        $processedPostParameters = $restRestProxy->addPostParameter($postParameters, $key, $expected);
        $actual = $processedPostParameters[$key];

        // Assert
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /**
     * @depends testConstruct
     */
    public function testGenerateMetadataHeader($proxy)
    {
        // Setup
        $metadata = array('key1' => 'value1', 'MyName' => 'WindowsAzure', 'MyCompany' => 'Microsoft_');
        $expected = array();
        foreach ($metadata as $key => $value) {
            $expected[Resources::X_MS_META_HEADER_PREFIX . $key] = $value;
        }

        // Test
        $actual = $proxy->generateMetadataHeaders($metadata);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testConstruct
     */
    public function testGenerateMetadataHeaderInvalidNameFail($proxy)
    {
        // Setup
        $metadata = array('key1' => "value1\n", 'MyName' => "\rAzurr", 'MyCompany' => "Micr\r\nosoft_");
        $this->setExpectedException(get_class(new \InvalidArgumentException(Resources::INVALID_META_MSG)));

        // Test
        $proxy->generateMetadataHeaders($metadata);
    }
}
