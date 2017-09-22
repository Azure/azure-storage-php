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
 * @package   Tests\Unit\MicrosoftAzure\Storage\Table\internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace Tests\Unit\MicrosoftAzure\Storage\Table\Models\internal;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Table\Models\Entity;

/**
 * Unit tests for class JsonODataReaderWriter
 *
 * @category  Microsoft
 * @package   Tests\Unit\MicrosoftAzure\Storage\Table\internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class JsonODataReaderWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getTable
     */
    public function testGetTable()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $tablename = 'mytable';
        $expected = TestResources::getTableJSONFormat($tablename);

        // Test
        $actual = $serializer->getTable($tablename);
        
        // Assert
        $this->assertEquals($expected, $actual);
        
        return $actual;
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getEntity
     */
    public function testGetEntity()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $entity = TestResources::getTestEntity('123', '456');
        $entity->addProperty('Cost', EdmType::DOUBLE, 12.45);
        $expected = TestResources::ENTITY_JSON_STRING;
        
        // Test
        $actual = $serializer->getEntity($entity);
        
        // Assert
        $this->assertEquals($expected, $actual);
        
        return $actual;
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTableEntries
     */
    public function testParseTable()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $expected = 'mytable';
        $tableJSON = TestResources::getTableEntryMinimalMetaResult();
        
        // Test
        $actual = $serializer->parseTable($tableJSON);
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseTableEntries
     */
    public function testParseTables()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $expected = array('mytable1', 'mytable2', 'mytable3', 'mytable4', 'mytable5');
        $tableJSON0 = TestResources::getTableEntriesMinimalMetaResult();
        $tableJSON1 = TestResources::getTableEntriesNoMetaResult();
        $tableJSON2 = TestResources::getTableEntriesFullMetaResult();
        
        // Test
        $actual0 = $serializer->parseTableEntries($tableJSON0);
        $actual1 = $serializer->parseTableEntries($tableJSON1);
        $actual2 = $serializer->parseTableEntries($tableJSON2);
        
        // Assert
        $this->assertEquals($expected, $actual0);
        $this->assertEquals($expected, $actual1);
        $this->assertEquals($expected, $actual2);
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntity
     */
    public function testParseEntity()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $expected = TestResources::getExpectedTestEntity('123', '456');
        $json = TestResources::getEntityMinimalMetaResult('123', '456');
        
        // Test
        $actual = $serializer->parseEntity($json);
        
        // Assert
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
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntities
     */
    public function testParseEntities()
    {
        // Setup
        $serializer = new JsonODataReaderWriter();
        $pk1 = '123';
        $pk2 = '124';
        $pk3 = '125';
        $e1 = TestResources::getExpectedTestEntity($pk1, '1');
        $e2 = TestResources::getExpectedTestEntity($pk2, '2');
        $e3 = TestResources::getExpectedTestEntity($pk3, '3');
        $e1->setETag('W/"datetime\'2012-05-17T00%3A59%3A32.1131734Z\'"');
        $e2->setETag('W/"datetime\'2012-05-17T00%3A59%3A32.4252358Z\'"');
        $e3->setETag('W/"datetime\'2012-05-17T00%3A59%3A32.7533014Z\'"');
        $e1->setTimestamp(Utilities::convertToDateTime('2012-05-17T00:59:32.1131734Z'));
        $e2->setTimestamp(Utilities::convertToDateTime('2012-05-17T00:59:32.4252358Z'));
        $e3->setTimestamp(Utilities::convertToDateTime('2012-05-17T00:59:32.7533014Z'));
        $expected = array($e1, $e2, $e3);
        $entitiesJSON = TestResources::getEntitiesMinimalMetaResult();
        
        // Test
        $actual = $serializer->parseEntities($entitiesJSON);
        
        // Assert
        for ($i = 0; $i < 3; ++$i) {
            $expectedProperties = $expected[$i]->getProperties();
            $actualProperties = $actual[$i]->getProperties();
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
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::parseEntity
     * @covers MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter::getEntity
     */
    public function testVariousTypes()
    {
        $serializer = new JsonODataReaderWriter();
        $e = TestResources::getVariousTypesEntity();

        $jsonString = $serializer->getEntity($e);

        $a = $serializer->parseEntity($jsonString);

        $this->assertEquals($e, $a);
    }
}
