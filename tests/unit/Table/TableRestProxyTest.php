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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Table;

use MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter;
use MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter;
use MicrosoftAzure\Storage\Tests\Framework\TableServiceRestProxyTestBase;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Table\TableRestProxy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Table\Models\QueryTablesOptions;
use MicrosoftAzure\Storage\Table\Models\Query;
use MicrosoftAzure\Storage\Table\Models\Filters\Filter;
use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\Models\TableACL;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesOptions;
use MicrosoftAzure\Storage\Table\Models\BatchOperations;
use MicrosoftAzure\Storage\Table\Models\AcceptJSONContentType;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;

/**
 * Unit tests for class TableRestProxy
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class TableRestProxyTest extends TableServiceRestProxyTestBase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getServiceProperties
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::setServiceProperties
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testSetServiceProperties()
    {
        $this->skipIfEmulated();
        
        // Setup
        $expected = ServiceProperties::create(TestResources::setServicePropertiesSample());
        
        // Test
        $this->setServiceProperties($expected);
        //Add 30s interval to wait for setting to take effect.
        \sleep(30);
        $actual = $this->restProxy->getServiceProperties();
        
        // Assert
        $this->assertEquals($expected->toXml($this->xmlSerializer), $actual->getValue()->toXml($this->xmlSerializer));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::setServiceProperties
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testSetServicePropertiesWithEmptyParts()
    {
        $this->skipIfEmulated();
        
        // Setup
        $xml = TestResources::setServicePropertiesSample();
        $xml['HourMetrics']['RetentionPolicy'] = null;
        $expected = ServiceProperties::create($xml);
        
        // Test
        $this->setServiceProperties($expected);
        $actual = $this->restProxy->getServiceProperties();
        
        // Assert
        $this->assertEquals($expected->toXml($this->xmlSerializer), $actual->getValue()->toXml($this->xmlSerializer));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createTable
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getTable
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testCreateTable()
    {
        // Setup
        $name = 'createtable';
        
        // Test
        $this->createTable($name);
        
        // Assert
        $result = $this->restProxy->queryTables();
        $this->assertCount(1, $result->getTables());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getTable
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTable
     * @covers MicrosoftAzure\Storage\Table\Models\GetTableResult::create
     */
    public function testGetTable()
    {
        // Setup
        $name = 'gettable';
        $this->createTable($name);
        
        // Test
        $result = $this->restProxy->getTable($name);
        
        // Assert
        $this->assertEquals($name, $result->getName());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::deleteTable
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testDeleteTable()
    {
        // Setup
        $name = 'deletetable';
        $this->restProxy->createTable($name);
        
        // Test
        $this->restProxy->deleteTable($name);
        
        // Assert
        $result = $this->restProxy->queryTables();
        $this->assertCount(0, $result->getTables());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryTables
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpression
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpressionRec
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeQueryValue
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesResult::create
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTableEntries
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryTablesSimple()
    {
        // Setup
        $name1 = 'querytablessimple1';
        $name2 = 'querytablessimple2';
        $this->createTable($name1);
        $this->createTable($name2);
        
        // Test
        $result = $this->restProxy->queryTables();
        
        // Assert
        $tables = $result->getTables();
        $this->assertCount(2, $tables);
        $this->assertEquals($name1, $tables[0]);
        $this->assertEquals($name2, $tables[1]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryTables
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpression
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpressionRec
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeQueryValue
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesResult::create
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTableEntries
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryTablesOneTable()
    {
        // Setup
        $name1 = 'mytable1';
        $this->createTable($name1);
        
        // Test
        $result = $this->restProxy->queryTables();
        
        // Assert
        $tables = $result->getTables();
        $this->assertCount(1, $tables);
        $this->assertEquals($name1, $tables[0]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryTables
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpression
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpressionRec
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeQueryValue
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesResult::create
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTableEntries
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryTablesEmpty()
    {
        // Test
        $result = $this->restProxy->queryTables();
        
        // Assert
        $tables = $result->getTables();
        $this->assertCount(0, $tables);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryTables
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpression
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpressionRec
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeQueryValue
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesResult::create
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTableEntries
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryTablesWithPrefix()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name1 = 'wquerytableswithprefix1';
        $name2 = 'querytableswithprefix2';
        $name3 = 'querytableswithprefix3';
        $options = new QueryTablesOptions();
        $options->setPrefix('q');
        $this->createTable($name1);
        $this->createTable($name2);
        $this->createTable($name3);
        
        // Test
        $result = $this->restProxy->queryTables($options);
        
        // Assert
        $tables = $result->getTables();
        $this->assertCount(2, $tables);
        $this->assertEquals($name2, $tables[0]);
        $this->assertEquals($name3, $tables[1]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryTables
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpression
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpressionRec
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeQueryValue
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesResult::create
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTableEntries
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryTablesWithStringOption()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name1 = 'wquerytableswithstringoption1';
        $name2 = 'querytableswithstringoption2';
        $name3 = 'querytableswithstringoption3';
        $prefix = 'q';
        $this->createTable($name1);
        $this->createTable($name2);
        $this->createTable($name3);
        
        // Test
        $result = $this->restProxy->queryTables($prefix);
        
        // Assert
        $tables = $result->getTables();
        $this->assertCount(2, $tables);
        $this->assertEquals($name2, $tables[0]);
        $this->assertEquals($name3, $tables[1]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryTables
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpression
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::buildFilterExpressionRec
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeQueryValue
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesResult::create
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTableEntries
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryTablesWithFilterOption()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name1 = 'wquerytableswithfilteroption1';
        $name2 = 'querytableswithfilteroption2';
        $name3 = 'querytableswithfilteroption3';
        $prefix = 'q';
        $prefixFilter = Filter::applyAnd(
            Filter::applyGe(
                Filter::applyPropertyName('TableName'),
                Filter::applyConstant($prefix, EdmType::STRING)
            ),
            Filter::applyLe(
                Filter::applyPropertyName('TableName'),
                Filter::applyConstant($prefix . '{', EdmType::STRING)
            )
        );
        $this->createTable($name1);
        $this->createTable($name2);
        $this->createTable($name3);
        
        // Test
        $result = $this->restProxy->queryTables($prefixFilter);
        
        // Assert
        $tables = $result->getTables();
        $this->assertCount(2, $tables);
        $this->assertEquals($name2, $tables[0]);
        $this->assertEquals($name3, $tables[1]);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::insertEntity
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructInsertEntityContext
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeValue
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getEntity
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntity
     * @covers MicrosoftAzure\Storage\Table\Models\InsertEntityResult::create
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testInsertEntity()
    {
        // Setup
        $name = 'insertentity';
        $this->createTable($name);
        $expected = TestResources::getTestEntity('123', '456');
        
        // Test
        $result = $this->restProxy->insertEntity($name, $expected);
        
        // Assert
        $actual = $result->getEntity();
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        // Add extra count for the properties because the Timestamp property
        $this->assertCount(count($expected->getProperties()) + 1, $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryEntities
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntities
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryEntitiesWithEmpty()
    {
        // Setup
        $name = 'queryentitieswithempty';
        $this->createTable($name);
        
        // Test
        $result = $this->restProxy->queryEntities($name);
        
        // Assert
        $entities = $result->getEntities();
        $this->assertCount(0, $entities);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryEntities
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntities
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryEntitiesWithOneEntity()
    {
        // Setup
        $name = 'queryentitieswithoneentity';
        $pk1 = '123';
        $e1 = TestResources::getTestEntity($pk1, '1');
        $this->createTable($name);
        $this->restProxy->insertEntity($name, $e1);
        
        // Test
        $result = $this->restProxy->queryEntities($name);
        
        // Assert
        $entities = $result->getEntities();
        $this->assertCount(1, $entities);
        
        $actualEntity = $entities[0];
        $this->assertEquals($pk1, $actualEntity->getPartitionKey());
        $this->assertEquals(EdmType::STRING, $entities[0]->getProperty("CustomerName")->getEdmType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryEntities
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntities
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryEntitiesQueryStringOption()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name = 'queryentitieswithquerystringoption';
        $pk1 = '123';
        $pk2 = '124';
        $pk3 = '125';
        $e1 = TestResources::getTestEntity($pk1, '1');
        $e2 = TestResources::getTestEntity($pk2, '2');
        $e3 = TestResources::getTestEntity($pk3, '3');
        $this->createTable($name);
        $this->restProxy->insertEntity($name, $e1);
        $this->restProxy->insertEntity($name, $e2);
        $this->restProxy->insertEntity($name, $e3);
        $queryString = "PartitionKey eq '123'";
        
        // Test
        $result = $this->restProxy->queryEntities($name, $queryString);
        
        // Assert
        $entities = $result->getEntities();
        $this->assertCount(1, $entities);
        $this->assertEquals($pk1, $entities[0]->getPartitionKey());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryEntities
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntities
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryEntitiesFilterOption()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name = 'queryentitieswithfilteroption';
        $pk1 = '123';
        $pk2 = '124';
        $pk3 = '125';
        $e1 = TestResources::getTestEntity($pk1, '1');
        $e2 = TestResources::getTestEntity($pk2, '2');
        $e3 = TestResources::getTestEntity($pk3, '3');
        $this->createTable($name);
        $this->restProxy->insertEntity($name, $e1);
        $this->restProxy->insertEntity($name, $e2);
        $this->restProxy->insertEntity($name, $e3);
        $queryString = "PartitionKey eq '123'";
        $filter = Filter::applyQueryString($queryString);
        
        // Test
        $result = $this->restProxy->queryEntities($name, $filter);
        
        // Assert
        $entities = $result->getEntities();
        $this->assertCount(1, $entities);
        $this->assertEquals($pk1, $entities[0]->getPartitionKey());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryEntities
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntities
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryEntitiesWithMultipleEntities()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name = 'queryentitieswithmultipleentities';
        $pk1 = '123';
        $pk2 = '124';
        $pk3 = '125';
        // This value is hard coded in TestResources::getTestEntity
        $expected = 890;
        $field = 'CustomerId';
        $e1 = TestResources::getTestEntity($pk1, '1');
        $e2 = TestResources::getTestEntity($pk2, '2');
        $e3 = TestResources::getTestEntity($pk3, '3');
        $this->createTable($name);
        $this->restProxy->insertEntity($name, $e1);
        $this->restProxy->insertEntity($name, $e2);
        $this->restProxy->insertEntity($name, $e3);
        $query = new Query();
        $query->addSelectField('CustomerId');
        $options = new QueryEntitiesOptions();
        $options->setQuery($query);
        
        // Test
        $result = $this->restProxy->queryEntities($name, $options);
        
        // Assert
        $entities = $result->getEntities();
        $this->assertCount(3, $entities);
        $this->assertEquals($expected, $entities[0]->getProperty($field)->getValue());
        $this->assertEquals($expected, $entities[1]->getProperty($field)->getValue());
        $this->assertEquals($expected, $entities[2]->getProperty($field)->getValue());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::queryEntities
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::addOptionalQuery
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValues
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::encodeODataUriValue
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntities
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testQueryEntitiesWithGetTop()
    {
        // Setup
        $name = 'queryentitieswithgettop';
        $pk1 = '123';
        $pk2 = '124';
        $pk3 = '125';
        $e1 = TestResources::getTestEntity($pk1, '1');
        $e2 = TestResources::getTestEntity($pk2, '2');
        $e3 = TestResources::getTestEntity($pk3, '3');
        $this->createTable($name);
        $this->restProxy->insertEntity($name, $e1);
        $this->restProxy->insertEntity($name, $e2);
        $this->restProxy->insertEntity($name, $e3);
        $query = new Query();
        $query->setTop(1);
        $options = new QueryEntitiesOptions();
        $options->setQuery($query);
        
        // Test
        $result = $this->restProxy->queryEntities($name, $options);
        
        // Assert
        $entities = $result->getEntities();
        $this->assertCount(1, $entities);
        $this->assertEquals($pk1, $entities[0]->getPartitionKey());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::updateEntity
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getEntityPath
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::putOrMergeEntityAsyncImpl
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeValue
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getEntity
     * @covers MicrosoftAzure\Storage\Table\Models\UpdateEntityResult::create
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testUpdateEntity()
    {
        // Setup
        $name = 'updateentity';
        $this->createTable($name);
        $expected = TestResources::getTestEntity('123', '456');
        $this->restProxy->insertEntity($name, $expected);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $expected = $entities[0];
        $expected->addProperty('CustomerPlace', EdmType::STRING, 'Redmond');

        // Test
        $result = $this->restProxy->UpdateEntity($name, $expected);
        
        // Assert
        $this->assertNotNull($result);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $actual = $entities[0];
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $this->assertCount(count($expected->getProperties()), $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::insertEntity
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructInsertEntityContext
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeValue
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getEntity
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntity
     * @covers MicrosoftAzure\Storage\Table\Models\InsertEntityResult::create
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testUpdateEntityWithDeleteProperty()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name = 'updateentitywithdeleteproperty';
        $this->createTable($name);
        $expected = TestResources::getTestEntity('123', '456');
        $this->restProxy->insertEntity($name, $expected);
        $expected->setPropertyValue('CustomerId', null);
        
        // Test
        $result = $this->restProxy->updateEntity($name, $expected);
        
        // Assert
        $this->assertNotNull($result);
        $actual = $this->restProxy->getEntity($name, $expected->getPartitionKey(), $expected->getRowKey());
        $this->assertEquals($expected->getPartitionKey(), $actual->getEntity()->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getEntity()->getRowKey());
        // Add +1 to the count to include Timestamp property.
        $this->assertCount(count($expected->getProperties()), $actual->getEntity()->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::mergeEntity
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getEntityPath
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::putOrMergeEntityAsyncImpl
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Models\EdmType::serializeValue
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getEntity
     * @covers MicrosoftAzure\Storage\Table\Models\UpdateEntityResult::create
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testMergeEntity()
    {
        // Setup
        $name = 'mergeentity';
        $this->createTable($name);
        $expected = TestResources::getTestEntity('123', '456');
        $this->restProxy->insertEntity($name, $expected);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $expected = $entities[0];
        $expected->addProperty('CustomerPhone', EdmType::STRING, '99999999');
        
        // Test
        $result = $this->restProxy->mergeEntity($name, $expected);
        
        // Assert
        $this->assertNotNull($result);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $actual = $entities[0];
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $this->assertCount(count($expected->getProperties()), $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::insertOrReplaceEntity
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getEntityPath
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::putOrMergeEntityAsyncImpl
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getEntity
     * @covers MicrosoftAzure\Storage\Table\Models\UpdateEntityResult::create
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testInsertOrReplaceEntity()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name = 'insertorreplaceentity';
        $this->createTable($name);
        $expected = TestResources::getTestEntity('123', '456');
        $this->restProxy->insertEntity($name, $expected);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $expected = $entities[0];
        $expected->addProperty('CustomerPlace', EdmType::STRING, 'Redmond');
        
        // Test
        $result = $this->restProxy->InsertOrReplaceEntity($name, $expected);
        
        // Assert
        $this->assertNotNull($result);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $actual = $entities[0];
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $this->assertCount(count($expected->getProperties()), $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::InsertOrMergeEntity
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getEntityPath
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::putOrMergeEntityAsyncImpl
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getEntity
     * @covers MicrosoftAzure\Storage\Table\Models\UpdateEntityResult::create
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testInsertOrMergeEntity()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name = 'insertormergeentity';
        $this->createTable($name);
        $expected = TestResources::getTestEntity('123', '456');
        $this->restProxy->insertEntity($name, $expected);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $expected = $entities[0];
        $expected->addProperty('CustomerPhone', EdmType::STRING, '99999999');
        
        // Test
        $result = $this->restProxy->InsertOrMergeEntity($name, $expected);
        
        // Assert
        $this->assertNotNull($result);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $actual = $entities[0];
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $this->assertCount(count($expected->getProperties()), $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::deleteEntity
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getEntityPath
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructDeleteEntityContext
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testDeleteEntity()
    {
        // Setup
        $name = 'deleteentity';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $entity = TestResources::getTestEntity($partitionKey, $rowKey);
        $result = $this->restProxy->insertEntity($name, $entity);
        
        // Test
        $this->restProxy->deleteEntity($name, $partitionKey, $rowKey);
        
        // Assert
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $this->assertCount(0, $entities);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::deleteEntity
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getEntityPath
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructDeleteEntityContext
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testDeleteEntityWithSpecialChars()
    {
        // Setup
        $name = 'deleteentitywithspecialchars';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = 'key with spaces';
        $entity = TestResources::getTestEntity($partitionKey, $rowKey);
        $result = $this->restProxy->insertEntity($name, $entity);
        
        // Test
        $this->restProxy->deleteEntity($name, $partitionKey, $rowKey);
        
        // Assert
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $this->assertCount(0, $entities);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getEntity
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntity
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testGetEntity()
    {
        // Setup
        $name = 'getentity';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $expected = TestResources::getTestEntity($partitionKey, $rowKey);
        $this->restProxy->insertEntity($name, $expected);
        
        // Test
        $result = $this->restProxy->getEntity($name, $partitionKey, $rowKey);
        
        // Assert
        $actual = $result->getEntity();
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        // Increase thec properties count to incloude the Timestamp property.
        $this->assertCount(count($expected->getProperties()) + 1, $actual->getProperties());
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getEntity
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntity
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testGetEntityVariousType()
    {
        // Setup
        $name = 'getentityvarioustype';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $expected = TestResources::getVariousTypesEntity();
        $expected->setPartitionKey($partitionKey);
        $expected->setRowKey($rowKey);
        $this->restProxy->insertEntity($name, $expected);
        
        // Test
        $result = $this->restProxy->getEntity($name, $partitionKey, $rowKey);
        
        // Assert
        $actual = $result->getEntity();
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $expectedProperties = $expected->getProperties();
        $actualProperties = $actual->getProperties();
        foreach ($expectedProperties as $key => $property) {
            $this->assertEquals(
                $property->getEdmType(),
                $actualProperties[$key]->getEdmType()
            );
            $this->assertEquals(
                $property->getValue(),
                $actualProperties[$key]->getValue()
            );
        }
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::batch
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createBatchRequestBody
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getOperationContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructInsertEntityContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createOperationsContexts
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::encodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::decodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_constructResponses
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_compareUsingContentId
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testBatchWithInsert()
    {
        // Setup
        $name = 'batchwithinsert';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $expected = TestResources::getTestEntity($partitionKey, $rowKey);
        $operations = new BatchOperations();
        $operations->addInsertEntity($name, $expected);
        
        // Test
        $result = $this->restProxy->batch($operations);
        
        // Assert
        $entries = $result->getEntries();
        $actual = $entries[0]->getEntity();
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        // Increase the properties count to include Timestamp property.
        $this->assertCount(count($expected->getProperties()) + 1, $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::batch
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createBatchRequestBody
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getOperationContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createOperationsContexts
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructDeleteEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::encodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::decodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_constructResponses
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_compareUsingContentId
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testBatchWithDelete()
    {
        // Setup
        $name = 'batchwithdelete';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $expected = TestResources::getTestEntity($partitionKey, $rowKey);
        $this->restProxy->insertEntity($name, $expected);
        $operations = new BatchOperations();
        $operations->addDeleteEntity($name, $partitionKey, $rowKey);
        
        // Test
        $this->restProxy->batch($operations);
        
        // Assert
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $this->assertCount(0, $entities);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::batch
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createBatchRequestBody
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getOperationContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createOperationsContexts
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::encodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::decodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_constructResponses
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_compareUsingContentId
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testBatchWithUpdate()
    {
        // Setup
        $name = 'batchwithupdate';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $expected = TestResources::getTestEntity($partitionKey, $rowKey);
        $this->restProxy->insertEntity($name, $expected);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $expected = $entities[0];
        $expected->addProperty('CustomerPlace', EdmType::STRING, 'Redmond');
        $operations = new BatchOperations();
        $operations->addUpdateEntity($name, $expected);
        
        // Test
        $result = $this->restProxy->batch($operations);
        
        // Assert
        $entries = $result->getEntries();
        $this->assertNotNull($entries[0]->getETag());
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $actual = $entities[0];
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $this->assertCount(count($expected->getProperties()), $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::batch
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createBatchRequestBody
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getOperationContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createOperationsContexts
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::encodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::decodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_constructResponses
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_compareUsingContentId
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testBatchWithMerge()
    {
        // Setup
        $name = 'batchwithmerge';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $expected = TestResources::getTestEntity($partitionKey, $rowKey);
        $this->restProxy->insertEntity($name, $expected);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $expected = $entities[0];
        $expected->addProperty('CustomerPlace', EdmType::STRING, 'Redmond');
        $operations = new BatchOperations();
        $operations->addMergeEntity($name, $expected);
        
        // Test
        $result = $this->restProxy->batch($operations);
        
        // Assert
        $entries = $result->getEntries();
        $this->assertNotNull($entries[0]->getETag());
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $actual = $entities[0];
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $this->assertCount(count($expected->getProperties()), $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::batch
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createBatchRequestBody
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getOperationContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createOperationsContexts
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::encodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::decodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_constructResponses
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_compareUsingContentId
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendAsync
     */
    public function testBatchWithInsertOrReplace()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name = 'batchwithinsertorreplace';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $expected = TestResources::getTestEntity($partitionKey, $rowKey);
        $this->restProxy->insertEntity($name, $expected);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $expected = $entities[0];
        $expected->addProperty('CustomerPlace', EdmType::STRING, 'Redmond');
        $operations = new BatchOperations();
        $operations->addInsertOrReplaceEntity($name, $expected);
        
        // Test
        $result = $this->restProxy->batch($operations);
        
        // Assert
        $entries = $result->getEntries();
        $this->assertNotNull($entries[0]->getETag());
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $actual = $entities[0];
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $this->assertCount(count($expected->getProperties()), $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::batch
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createBatchRequestBody
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getOperationContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createOperationsContexts
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::encodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::decodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_constructResponses
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_compareUsingContentId
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testBatchWithInsertOrMerge()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name = 'batchwithinsertormerge';
        $this->createTable($name);
        $partitionKey = '123';
        $rowKey = '456';
        $expected = TestResources::getTestEntity($partitionKey, $rowKey);
        $this->restProxy->insertEntity($name, $expected);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $expected = $entities[0];
        $expected->addProperty('CustomerPlace', EdmType::STRING, 'Redmond');
        $operations = new BatchOperations();
        $operations->addInsertOrMergeEntity($name, $expected);
        
        // Test
        $result = $this->restProxy->batch($operations);
        
        // Assert
        $entries = $result->getEntries();
        $this->assertNotNull($entries[0]->getETag());
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $actual = $entities[0];
        $this->assertEquals($expected->getPartitionKey(), $actual->getPartitionKey());
        $this->assertEquals($expected->getRowKey(), $actual->getRowKey());
        $this->assertCount(count($expected->getProperties()), $actual->getProperties());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::batch
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createBatchRequestBody
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getOperationContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createOperationsContexts
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::encodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::decodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_constructResponses
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_compareUsingContentId
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     */
    public function testBatchWithMultipleOperations()
    {
        // Setup
        $name = 'batchwithwithmultipleoperations';
        $this->createTable($name);
        $partitionKey = '123';
        $rk1 = '456';
        $rk2 = '457';
        $rk3 = '458';
        $delete = TestResources::getTestEntity($partitionKey, $rk1);
        $insert = TestResources::getTestEntity($partitionKey, $rk2);
        $update = TestResources::getTestEntity($partitionKey, $rk3);
        $this->restProxy->insertEntity($name, $delete);
        $this->restProxy->insertEntity($name, $update);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $delete = $entities[0];
        $update = $entities[1];
        $update->addProperty('CustomerPlace', EdmType::STRING, 'Redmond');
        $operations = new BatchOperations();
        $operations->addInsertEntity($name, $insert);
        $operations->addUpdateEntity($name, $update);
        $operations->addDeleteEntity($name, $delete->getPartitionKey(), $delete->getRowKey(), $delete->getETag());
        
        // Test
        $result = $this->restProxy->batch($operations);
        
        // Assert
        $this->assertTrue(true);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::batch
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createBatchRequestBody
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getOperationContext
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createOperationsContexts
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::constructPutOrMergeEntityContext
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::encodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter::decodeMimeMultipart
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_constructResponses
     * @covers MicrosoftAzure\Storage\Table\Models\BatchResult::_compareUsingContentId
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::sendContextAsync
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage All commands in a batch must operate on same entity group.
     */
    public function testBatchWithDifferentPKFail()
    {
        // Setup
        $name = 'batchwithwithdifferentpkfail';
        $this->createTable($name);
        $partitionKey = '123';
        $rk1 = '456';
        $rk3 = '458';
        $delete = TestResources::getTestEntity($partitionKey, $rk1);
        $update = TestResources::getTestEntity($partitionKey, $rk3);
        $this->restProxy->insertEntity($name, $delete);
        $this->restProxy->insertEntity($name, $update);
        $result = $this->restProxy->queryEntities($name);
        $entities = $result->getEntities();
        $delete = $entities[0];
        $update = $entities[1];
        $update->addProperty('CustomerPlace', EdmType::STRING, 'Redmond');
        $operations = new BatchOperations();
        $operations->addUpdateEntity($name, $update);
        $operations->addDeleteEntity($name, '125', $delete->getRowKey(), $delete->getETag());
        
        // Test
        $result = $this->restProxy->batch($operations);
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getTableAcl
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::getTableAclAsync
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::setTableAcl
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::setTableAclAsync
     */
    public function testGetSetTableAcl()
    {
        // Setup
        $name = self::getTableNameWithPrefix('testGetSetTableAcl');
        $this->createTable($name);
        $sample = TestResources::getTableACLMultipleEntriesSample();
        $acl = TableACL::create($sample['SignedIdentifiers']);
        //because the time is randomized, this should create a different instance
        $negativeSample = TestResources::getTableACLMultipleEntriesSample();
        $negative = TableACL::create($negativeSample['SignedIdentifiers']);

        // Test
        $this->restProxy->setTableAcl($name, $acl);
        $resultAcl = $this->restProxy->getTableAcl($name);

        $this->assertEquals(
            $acl->getSignedIdentifiers(),
            $resultAcl->getSignedIdentifiers()
        );

        $this->assertFalse(
            $resultAcl->getSignedIdentifiers() == $negative->getSignedIdentifiers(),
            'Should not equal to the negative test case'
        );
    }

    /**
     * @covers  \MicrosoftAzure\Storage\Table\TableRestProxy::getServiceStats
     * @covers  \MicrosoftAzure\Storage\Table\TableRestProxy::getServiceStatsAsync
     */
    public function testGetServiceStats()
    {
        $result = $this->restProxy->getServiceStats();

        // Assert
        $this->assertNotNull($result->getStatus());
        $this->assertNotNull($result->getLastSyncTime());
        $this->assertTrue($result->getLastSyncTime() instanceof \DateTime);
    }

    private static function getTableNameWithPrefix($prefix)
    {
        return $prefix . sprintf('%04x', mt_rand(0, 65535));
    }
}
