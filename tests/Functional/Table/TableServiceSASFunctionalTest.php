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
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\Table;

use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\SASFunctionalTestBase;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Tests for service SAS proxy tests.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class TableServiceSASFunctionalTest extends SASFunctionalTestBase
{
    public function testTableServiceSAS()
    {
        $helper = new TableSharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //setup the proxies for creating tables
        $this->setUpWithConnectionString($this->connectionString);

        $tableProxies = array();
        $tables = array();
        $tables[] = TestResources::getInterestingName('tbl');
        $this->safeCreateTable($tables[0]);
        $tables[] = TestResources::getInterestingName('tbl');
        $this->safeCreateTable($tables[1]);

        //Full permission for table0
        $tableProxies[] = $this->createProxyWithTableSASfromArray(
            $helper,
            TestResources::getInterestingTableSASTestCase(
                'raud',
                $tables[0]
            )
        );
        //Full permission for table1
        $tableProxies[] = $this->createProxyWithTableSASfromArray(
            $helper,
            TestResources::getInterestingTableSASTestCase(
                'raud',
                $tables[1]
            )
        );

        //Validate the permission for each of the proxy/table pair
        for ($i = 0; $i < 2; ++$i) {
            $proxy = $tableProxies[$i];
            $table = $tables[$i];
            $entity = TestResources::getTestEntity('123', '456');
            //test raud.
            $proxy->insertEntity($table, $entity);
            $actual = $proxy->getEntity($table, '123', '456')->getEntity();
            $this->assertEquals(
                $entity->getPropertyValue('CustomerId'),
                $actual->getPropertyValue('CustomerId')
            );
            $entity->setPropertyValue('CustomerId', 891);
            $proxy->updateEntity($table, $entity);
            $actual = $proxy->getEntity($table, '123', '456')->getEntity();
            $this->assertEquals(
                $entity->getPropertyValue('CustomerId'),
                $actual->getPropertyValue('CustomerId')
            );
            $proxy->deleteEntity($table, '123', '456');
            $result = $proxy->queryEntities($table);
            $this->assertEquals(0, \count($result->getEntities()));
        }
        //Validate that a cross access with wrong proxy/table pair
        //would not be successfull
        for ($i= 0; $i < 2; ++$i) {
            $proxy = $tableProxies[$i];
            $table = $tables[1 - $i];
            $entity = TestResources::getTestEntity('123', '456');
            //a
            $this->validateServiceExceptionErrorMessage(
                'This request is not authorized to perform this operation.',
                function () use ($proxy, $table, $entity) {
                    $proxy->insertEntity($table, $entity);
                }
            );
            //r
            $this->validateServiceExceptionErrorMessage(
                'This request is not authorized to perform this operation.',
                function () use ($proxy, $table) {
                    $proxy->queryEntities($table);
                }
            );
        }

        //test startpk, startrk, endpk, endrk logic
        $tableProxy = $this->createProxyWithTableSASfromArray(
            $helper,
            TestResources::getInterestingTableSASTestCase(
                'raud',
                $tables[0],
                '',
                '',
                '',
                '123',
                '456',
                '123',
                '456'
            )
        );
        $table = $tables[0];
        //test raud.
        $tableProxy->insertEntity($table, $entity);
        $actual = $tableProxy->getEntity($table, '123', '456')->getEntity();
        $this->assertEquals(
            $entity->getPropertyValue('CustomerId'),
            $actual->getPropertyValue('CustomerId')
        );
        $entity->setPropertyValue('CustomerId', 891);
        $tableProxy->updateEntity($table, $entity);
        $actual = $tableProxy->getEntity($table, '123', '456')->getEntity();
        $this->assertEquals(
            $entity->getPropertyValue('CustomerId'),
            $actual->getPropertyValue('CustomerId')
        );
        $tableProxy->deleteEntity($table, '123', '456');
        $result = $tableProxy->queryEntities($table);
        $this->assertEquals(0, \count($result->getEntities()));

        //test out of scope pk cannot be accessed.
        $entity = TestResources::getTestEntity('124', '456');
        $this->validateServiceExceptionErrorMessage(
            'This request is not authorized to perform this operation.',
            function () use ($tableProxy, $table, $entity) {
                $tableProxy->insertEntity($table, $entity);
            }
        );
        //test out of scope rk cannot be accessed.
        $entity = TestResources::getTestEntity('123', '457');
        $this->validateServiceExceptionErrorMessage(
            'This request is not authorized to perform this operation.',
            function () use ($tableProxy, $table, $entity) {
                $tableProxy->insertEntity($table, $entity);
            }
        );
    }

    private function createProxyWithTableSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateTableServiceSharedAccessSignatureToken(
            $testCase['tableName'],
            $testCase['signedPermissions'],
            $testCase['signedExpiry'],
            $testCase['signedStart'],
            $testCase['signedIP'],
            $testCase['signedProtocol'],
            $testCase['signedIdentifier'],
            $testCase['startingPartitionKey'],
            $testCase['startingRowKey'],
            $testCase['endingPartitionKey'],
            $testCase['endingRowKey']
        );

        $accountName = $helper->getAccountName();

        return $this->createProxyWithSAS($sas, $accountName, Resources::RESOURCE_TYPE_TABLE);
    }
}
