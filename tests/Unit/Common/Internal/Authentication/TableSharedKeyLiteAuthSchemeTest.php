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

use MicrosoftAzure\Storage\Tests\Mock\Common\Internal\Authentication\TableSharedKeyLiteAuthSchemeMock;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for TableSharedKeyLiteAuthScheme class.
 *
 * @package    MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Authentication
 * @author     Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright  2016 Microsoft Corporation
 * @license    https://github.com/azure/azure-storage-php/LICENSE
 * @link       https://github.com/azure/azure-storage-php
 */
class TableSharedKeyLiteAuthSchemeTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers MicrosoftAzure\Storage\Common\Internal\Authentication\TableSharedKeyLiteAuthScheme::__construct
    */
    public function testConstruct()
    {
        $expected = array();
        $expected[] = Resources::DATE;

        $mock = new TableSharedKeyLiteAuthSchemeMock(TestResources::ACCOUNT_NAME, TestResources::KEY4);

        $this->assertEquals($expected, $mock->getIncludedHeaders());
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\Internal\Authentication\TableSharedKeyLiteAuthScheme::computeSignature
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
        $expected = "\n/$accountName" . parse_url($url, PHP_URL_PATH) . "?comp=list";
        $mock = new TableSharedKeyLiteAuthSchemeMock($accountName, TestResources::KEY4);

        $actual = $mock->computeSignatureMock($headers, $url, $queryParams, $httpMethod);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Authentication\TableSharedKeyLiteAuthScheme::getAuthorizationHeader
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
        $expected = 'SharedKeyLite ' . $accountName . ':KB+TK3FPHLADYwd0/b3PcZgK/fYXUSlwsoOIf80l2co=';

        $mock = new TableSharedKeyLiteAuthSchemeMock($accountName, $accountKey);

        $actual = $mock->getAuthorizationHeader($headers, $url, $queryParams, $httpMethod);

        $this->assertEquals($expected, $actual);
    }
}
