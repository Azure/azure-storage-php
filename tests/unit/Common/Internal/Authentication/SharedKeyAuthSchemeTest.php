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
use MicrosoftAzure\Storage\Tests\Mock\Common\Internal\Authentication\SharedKeyAuthSchemeMock;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for SharedKeyAuthScheme class.
 *
 * @package    MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Authentication
 * @author     Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright  2016 Microsoft Corporation
 * @license    https://github.com/azure/azure-storage-php/LICENSE
 * @version    Release: 0.10.2
 * @link       https://github.com/azure/azure-storage-php
 */
class SharedKeyAuthSchemeTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers MicrosoftAzure\Storage\Common\Internal\Authentication\SharedKeyAuthScheme::__construct
    */
    public function test__construct()
    {
        $expected = array();
        $expected[] = Resources::CONTENT_ENCODING;
        $expected[] = Resources::CONTENT_LANGUAGE;
        $expected[] = Resources::CONTENT_LENGTH;
        $expected[] = Resources::CONTENT_MD5;
        $expected[] = Resources::CONTENT_TYPE;
        $expected[] = Resources::DATE;
        $expected[] = Resources::IF_MODIFIED_SINCE;
        $expected[] = Resources::IF_MATCH;
        $expected[] = Resources::IF_NONE_MATCH;
        $expected[] = Resources::IF_UNMODIFIED_SINCE;
        $expected[] = Resources::RANGE;

        $mock = new SharedKeyAuthSchemeMock(TestResources::ACCOUNT_NAME, TestResources::KEY4);

        $this->assertEquals($expected, $mock->getIncludedHeaders());
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\Internal\Authentication\SharedKeyAuthScheme::computeSignature
    */
    public function testComputeSignatureSimple()
    {
        $httpMethod = 'GET';
        $queryParams = array(Resources::QP_COMP => 'list');
        $url = TestResources::URI1;
        $date = TestResources::DATE1;
        $apiVersion = Resources::STORAGE_API_LATEST_VERSION;
        $accountName = TestResources::ACCOUNT_NAME;
        $headers = array(Resources::X_MS_DATE => $date, Resources::X_MS_VERSION => $apiVersion);
        $expected = "GET\n\n\n\n\n\n\n\n\n\n\n\n" . Resources::X_MS_DATE . ":$date\n" . Resources::X_MS_VERSION . 
                ":$apiVersion\n/$accountName" . parse_url($url, PHP_URL_PATH) . "\ncomp:list";
        $mock = new SharedKeyAuthSchemeMock($accountName, TestResources::KEY4);

        $actual = $mock->computeSignatureMock($headers, $url, $queryParams, $httpMethod);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Authentication\SharedKeyAuthScheme::getAuthorizationHeader
     */
    public function testGetAuthorizationHeaderSimple()
    {
        $accountName = TestResources::ACCOUNT_NAME;
        $apiVersion = Resources::STORAGE_API_LATEST_VERSION;
        $accountKey = TestResources::KEY4;
        $url = TestResources::URI2;
        $date1 = TestResources::DATE2;
        $headers = array(Resources::X_MS_VERSION => $apiVersion, Resources::X_MS_DATE => $date1);
        $queryParams = array(Resources::QP_COMP => 'list');
        $httpMethod = 'GET';
        $expected = 'SharedKey ' . $accountName . ':YDjZ61Lqt6HeMx+vv5QzFjW1juW7XEECVXJ4V9/pFgA=';

        $mock = new SharedKeyAuthSchemeMock($accountName, $accountKey);

        $actual = $mock->getAuthorizationHeader($headers, $url, $queryParams, $httpMethod);

        $this->assertEquals($expected, $actual);
    }
}


