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
 * @package   MicrosoftAzure\Storage\Tests\Functional\Table
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\Table;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Table\Internal\TableResources as Resources;
use MicrosoftAzure\Storage\Table\Models\DeleteEntityOptions;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesOptions;
use MicrosoftAzure\Storage\Table\Models\TableServiceOptions;
use MicrosoftAzure\Storage\Table\Models\GetTableOptions;
use MicrosoftAzure\Storage\Table\Models\GetEntityOptions;
use MicrosoftAzure\Storage\Table\Models\Filters\Filter;

class TableServiceFunctionalParametersTest extends FunctionalTestBase
{
    public function testGetServicePropertiesNullOptions()
    {
        try {
            $this->restProxy->getServiceProperties(null);
            $this->assertFalse($this->isEmulated(), 'Should fail if and only if in emulator');
        } catch (ServiceException $e) {
            // Expect failure when run this test with emulator, as v1.6 doesn't support this method
            if ($this->isEmulated()) {
                // Properties are not supported in emulator
                $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }
    }

    public function testSetServicePropertiesNullOptions1()
    {
        try {
            $this->restProxy->setServiceProperties(new ServiceProperties());
            $this->fail('Expect default service properties to cause service to error');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'Expect 400:BadRequest when sending default service properties to server');
        }
    }

    public function testSetServicePropertiesNullOptions2()
    {
        try {
            $this->restProxy->setServiceProperties(new ServiceProperties(), null);
            $this->fail('Expect default service properties to cause service to error');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'Expect 400:BadRequest when sending default service properties to server');
        }
    }

    public function testQueryTablesNullOptions()
    {
        $this->restProxy->queryTables(null);
        $this->assertTrue(true, 'Null options should be fine.');
    }

    public function testCreateTableNullOptions()
    {
        try {
            $this->restProxy->createTable(null);
            $this->fail('Expect null table to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
    }

    public function testDeleteTableNullOptions()
    {
        try {
            $this->restProxy->deleteTable(null);
            $this->fail('Expect null table to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
    }

    public function testInsertEntityOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        $this->restProxy->insertEntity($table, TableServiceFunctionalTestData::getSimpleEntity(), null);
        $this->clearTable($table);
        $this->assertTrue(true, 'Null options should be fine.');
    }

    public function testInsertEntityEmptyPartitionKey()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        $e = new Entity();
        $e->setPartitionKey('normalRowKey');
        $e->setRowKey('');
        $this->restProxy->insertEntity($table, $e);
        $this->clearTable($table);
        $this->assertTrue(true, 'Should be fine.');
    }

    public function testInsertEntityEmptyRowKey()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        $e = new Entity();
        $e->setPartitionKey('normalPartitionKey');
        $e->setRowKey('');
        $this->restProxy->insertEntity($table, $e);
        $this->clearTable($table);
        $this->assertTrue(true, 'Should be fine.');
    }

    public function testInsertStringWithAllAsciiCharacters()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        $e = new Entity();
        $e->setPartitionKey('foo');
        $e->setRowKey(TableServiceFunctionalTestData::getNewKey());

        // ASCII code points in the following ranges are valid in XML 1.0 documents
        // - 0x09, 0x0A, 0x0D, 0x20-0x7F
        // Note: 0x0D gets mapped to 0x0A by the server.

        $k = '';
        for ($b = 0x20; $b < 0x30; $b++) {
            $k .= chr($b);
        }
        $k .= chr(0x09);
        for ($b = 0x30; $b < 0x40; $b++) {
            $k .= chr($b);
        }
        $k .= chr(0x0A);
        for ($b= 0x40; $b < 0x50; $b++) {
            $k .= chr($b);
        }
        $k .= chr(0x0A);
        for ($b = 0x50; $b < 0x80; $b++) {
            $k .= chr($b);
        }

        $e->addProperty('foo', EdmType::STRING, $k);

        $ret = $this->restProxy->insertEntity($table, $e);
        $this->assertNotNull($ret, '$ret');
        $this->assertNotNull($ret->getEntity(), '$ret->getEntity');

        $l = $ret->getEntity()->getPropertyValue('foo');
        $this->assertEquals($k, $l, '$ret->getEntity()->getPropertyValue(\'foo\')');
        $this->clearTable($table);
    }

    public function testGetEntityPartKeyNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->getEntity($table, null, TableServiceFunctionalTestData::getNewKey());
            $this->fail('Expect null options to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testGetEntityRowKeyNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->getEntity($table, TableServiceFunctionalTestData::getNewKey(), null);
            $this->assertTrue(true, 'Expect null row key to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testGetEntityKeysNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->getEntity($table, null, null);
            $this->fail('Expect null partition and row keys to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testGetEntityTableAndKeysNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->getEntity(null, null, null);
            $this->fail('Expect null table name to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testGetEntityTableNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->getEntity(null, TableServiceFunctionalTestData::getNewKey(), TableServiceFunctionalTestData::getNewKey());
            $this->fail('Expect null table name to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testGetEntityKeysAndOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->getEntity($table, null, null, null);
            $this->fail('Expect keys to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testGetEntityKeysNullWithOptions()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];
        $ent = TableServiceFunctionalTestData::getSimpleEntity();

        try {
            $this->restProxy->insertEntity($table, $ent);
            $this->restProxy->getEntity($table, null, null, new GetEntityOptions());
            $this->fail('Expect null keys to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testGetEntityOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];
        $ent = TableServiceFunctionalTestData::getSimpleEntity();

        $this->restProxy->insertEntity($table, $ent);
        $this->restProxy->getEntity($table, $ent->getPartitionKey(), $ent->getRowKey(), null);
        $this->clearTable($table);
        $this->assertTrue(true, 'Null options should be fine.');
    }

    public function testDeleteEntityPartKeyNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->deleteEntity($table, null, TableServiceFunctionalTestData::getNewKey());
            $this->fail('Expect null partition key to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testDeleteEntityRowKeyNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->deleteEntity($table, TableServiceFunctionalTestData::getNewKey(), null);
            $this->fail('Expect null row key to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testDeleteEntityKeysNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->deleteEntity($table, null, null);
            $this->fail('Expect null keys to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testDeleteEntityTableAndKeysNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->deleteEntity(null, null, null);
            $this->fail('Expect null table name to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testDeleteEntityTableNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->deleteEntity(null, TableServiceFunctionalTestData::getNewKey(), TableServiceFunctionalTestData::getNewKey());
            $this->fail('Expect null table name to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testDeleteEntityKeysAndOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->deleteEntity($table, null, null, null);
            $this->fail('Expect null keys to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testDeleteEntityKeysNullWithOptions()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];
        $ent = TableServiceFunctionalTestData::getSimpleEntity();

        try {
            $this->restProxy->insertEntity($table, $ent);
            $this->restProxy->deleteEntity($table, null, null, new DeleteEntityOptions());
            $this->fail('Expect null keys to throw');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(Resources::NULL_TABLE_KEY_MSG, $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testDeleteEntityOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];
        $ent = TableServiceFunctionalTestData::getSimpleEntity();

        $this->restProxy->insertEntity($table, $ent);
        $this->restProxy->deleteEntity($table, $ent->getPartitionKey(), $ent->getRowKey(), null);
        $this->assertTrue(true, 'Expect null options to be fine');
        $this->clearTable($table);
    }

    public function testDeleteEntityTroublesomePartitionKey()
    {
        // The service does not allow the following common characters in keys:
        // 35 '#'
        // 47 '/'
        // 63 '?'
        // 92 '\'
        // In addition, the following values are not allowed, as they make the URL bad:
        // 0-31, 127-159
        // That still leaves several options for making troublesome keys
        // * spaces
        // * single quotes
        // * Unicode
        // These need to be properly encoded when passed on the URL, else there will be trouble

        $table = TableServiceFunctionalTestData::$testTableNames[0];

        $e = new Entity();
        $e->setPartitionKey('partition\'Key\'');
        $e->setRowKey('niceKey');
        $this->restProxy->insertEntity($table, $e);
        $this->restProxy->deleteEntity($table, $e->getPartitionKey(), $e->getRowKey());
        $qopts = new QueryEntitiesOptions();
        $qopts->setFilter(Filter::applyEq(Filter::applyPropertyName('PartitionKey'), Filter::applyConstant($e->getRowKey(), EdmType::STRING)));
        $queryres = $this->restProxy->queryEntities($table, $qopts);
        $this->assertEquals(0, count($queryres->getEntities()), 'entities returned');

        $e = new Entity();
        $e->setPartitionKey('partition Key');
        $e->setRowKey('niceKey');
        $this->restProxy->insertEntity($table, $e);
        $this->restProxy->deleteEntity($table, $e->getPartitionKey(), $e->getRowKey());
        $qopts = new QueryEntitiesOptions();
        $qopts->setFilter(Filter::applyEq(Filter::applyPropertyName('PartitionKey'), Filter::applyConstant($e->getRowKey(), EdmType::STRING)));
        $queryres = $this->restProxy->queryEntities($table, $qopts);
        $this->assertEquals(0, count($queryres->getEntities()), 'entities returned');

        $e = new Entity();
        $e->setPartitionKey('partition '. TableServiceFunctionalTestData::getUnicodeString());
        $e->setRowKey('niceKey');
        $this->restProxy->insertEntity($table, $e);
        $this->restProxy->deleteEntity($table, $e->getPartitionKey(), $e->getRowKey());
        $qopts = new QueryEntitiesOptions();
        $qopts->setFilter(Filter::applyEq(Filter::applyPropertyName('PartitionKey'), Filter::applyConstant($e->getRowKey(), EdmType::STRING)));
        $queryres = $this->restProxy->queryEntities($table, $qopts);
        $this->assertEquals(0, count($queryres->getEntities()), 'entities returned');

        $this->clearTable($table);
    }

    public function testDeleteEntityTroublesomeRowKey()
    {
        // The service does not allow the following common characters in keys:
        // 35 '#'
        // 47 '/'
        // 63 '?'
        // 92 '\'
        // In addition, the following values are not allowed, as they make the URL bad:
        // 0-31, 127-159
        // That still leaves several options for making troublesome keys
        // spaces
        // single quotes
        // Unicode
        // These need to be properly encoded when passed on the URL, else there will be trouble

        $table = TableServiceFunctionalTestData::$testTableNames[0];

        $e = new Entity();
        $e->setRowKey('row\'Key\'');
        $e->setPartitionKey('niceKey');
        $this->restProxy->insertEntity($table, $e);
        $this->restProxy->deleteEntity($table, $e->getPartitionKey(), $e->getRowKey());
        $qopts = new QueryEntitiesOptions();
        $qopts->setFilter(Filter::applyEq(Filter::applyPropertyName('RowKey'), Filter::applyConstant($e->getRowKey(), EdmType::STRING)));
        $queryres = $this->restProxy->queryEntities($table, $qopts);
        $this->assertEquals(0, count($queryres->getEntities()), 'entities returned');

        $e = new Entity();
        $e->setRowKey('row Key');
        $e->setPartitionKey('niceKey');
        $this->restProxy->insertEntity($table, $e);
        $this->restProxy->deleteEntity($table, $e->getPartitionKey(), $e->getRowKey());
        $qopts = new QueryEntitiesOptions();
        $qopts->setFilter(Filter::applyEq(Filter::applyPropertyName('RowKey'), Filter::applyConstant($e->getRowKey(), EdmType::STRING)));
        $queryres = $this->restProxy->queryEntities($table, $qopts);
        $this->assertEquals(0, count($queryres->getEntities()), 'entities returned');

        $e = new Entity();
        $e->setRowKey('row ' . TableServiceFunctionalTestData::getUnicodeString());
        $e->setPartitionKey('niceKey');
        $this->restProxy->insertEntity($table, $e);
        $this->restProxy->deleteEntity($table, $e->getPartitionKey(), $e->getRowKey());
        $qopts = new QueryEntitiesOptions();
        $qopts->setFilter(Filter::applyEq(Filter::applyPropertyName('RowKey'), Filter::applyConstant($e->getRowKey(), EdmType::STRING)));
        $queryres = $this->restProxy->queryEntities($table, $qopts);
        $this->assertEquals(0, count($queryres->getEntities()), 'entities returned');

        $this->clearTable($table);
    }

    public function testMergeEntityOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->mergeEntity($table, TableServiceFunctionalTestData::getSimpleEntity(), null);
            $this->fail('Expect 404:NotFound when merging with non-existant entity');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_NOT_FOUND, $e->getCode(), 'Expect 404:NotFound when merging with non-existant entity');
        }
        $this->clearTable($table);
    }

    public function testUpdateEntityOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->updateEntity($table, TableServiceFunctionalTestData::getSimpleEntity(), null);
            $this->fail('Expect 404:NotFound when updating non-existant entity');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_NOT_FOUND, $e->getCode(), 'Should be 404:NotFound for update nonexistant entity');
        }
        $this->clearTable($table);
    }

    public function testInsertOrMergeEntityOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->insertOrMergeEntity($table, TableServiceFunctionalTestData::getSimpleEntity(), null);
            $this->assertFalse($this->isEmulated(), 'Should fail if and only if in emulator');
        } catch (ServiceException $e) {
            // Expect failure when run this test with emulator, as v1.6 doesn't support this method
            if ($this->isEmulated()) {
                $this->assertEquals(TestResources::STATUS_NOT_FOUND, $e->getCode(), 'getCode');
            }
        }
        $this->clearTable($table);
    }

    public function testInsertOrReplaceEntityTableNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->insertOrReplaceEntity(null, new Entity());
            $this->fail('Expect to throw for null table name');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testInsertOrReplaceEntityOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->insertOrReplaceEntity($table, TableServiceFunctionalTestData::getSimpleEntity(), null);
            $this->assertFalse($this->isEmulated(), 'Should fail if and only if in emulator');
        } catch (ServiceException $e) {
            // Expect failure when run this test with emulator, as v1.6 doesn't support this method
            if ($this->isEmulated()) {
                $this->assertEquals(TestResources::STATUS_NOT_FOUND, $e->getCode(), 'getCode');
            }
        }
        $this->clearTable($table);
    }

    public function testQueryEntitiesTableNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->queryEntities(null);
            $this->fail('Expect to throw for null table name');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testQueryEntitiesTableNullOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->queryEntities(null, null);
            $this->fail('Expect to throw for null table name');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testQueryEntitiesTableNullWithOptions()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        try {
            $this->restProxy->queryEntities(null, new QueryEntitiesOptions());
            $this->fail('Expect to throw for null table name');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(sprintf(Resources::NULL_OR_EMPTY_MSG, 'table'), $e->getMessage(), 'Expect error message');
            $this->assertEquals(0, $e->getCode(), 'Expected error code');
        }
        $this->clearTable($table);
    }

    public function testQueryEntitiesOptionsNull()
    {
        $table = TableServiceFunctionalTestData::$testTableNames[0];

        $this->restProxy->queryEntities($table, null);
        $this->clearTable($table);
        $this->assertTrue(true, 'Null options should be fine.');
    }
}
