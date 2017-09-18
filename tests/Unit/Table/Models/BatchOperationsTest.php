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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Table\Models;

use MicrosoftAzure\Storage\Table\Models\BatchOperations;
use MicrosoftAzure\Storage\Table\Models\BatchOperation;
use MicrosoftAzure\Storage\Table\Models\Entity;

/**
 * Unit tests for class BatchOperations
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BatchOperationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::__construct
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::getOperations
     */
    public function testConstruct()
    {
        // Test
        $operations = new BatchOperations();
        
        // Assert
        $this->assertCount(0, $operations->getOperations());
        
        return $operations;
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::setOperations
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::getOperations
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addOperation
     * @depends testConstruct
     */
    public function testSetOperations($operations)
    {
        // Setup
        $operation = new BatchOperation();
        $expected = array($operation);
        $operations->addOperation($operation);
        
        // Test
        $operations->setOperations($expected);
        
        // Assert
        $this->assertEquals($expected, $operations->getOperations());
    }
    
    /**
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addInsertEntity
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addOperation
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::getOperations
     */
    public function testAddInsertEntity()
    {
        // Setup
        $table = 'mytable';
        $entity = new Entity();
        $operations = new BatchOperations();
        
        // Test
        $operations->addInsertEntity($table, $entity);
        
        // Assert
        $this->assertCount(1, $operations->getOperations());
    }
    
    /**
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addUpdateEntity
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addOperation
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::getOperations
     */
    public function testAddUpdateEntity()
    {
        // Setup
        $table = 'mytable';
        $entity = new Entity();
        $operations = new BatchOperations();
        
        // Test
        $operations->addUpdateEntity($table, $entity);
        
        // Assert
        $this->assertCount(1, $operations->getOperations());
    }
    
    /**
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addMergeEntity
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addOperation
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::getOperations
     */
    public function testAddMergeEntity()
    {
        // Setup
        $table = 'mytable';
        $entity = new Entity();
        $operations = new BatchOperations();
        
        // Test
        $operations->addMergeEntity($table, $entity);
        
        // Assert
        $this->assertCount(1, $operations->getOperations());
    }
    
    /**
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addInsertOrReplaceEntity
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addOperation
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::getOperations
     */
    public function testAddInsertOrReplaceEntity()
    {
        // Setup
        $table = 'mytable';
        $entity = new Entity();
        $operations = new BatchOperations();
        
        // Test
        $operations->addInsertOrReplaceEntity($table, $entity);
        
        // Assert
        $this->assertCount(1, $operations->getOperations());
    }
    
    /**
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addInsertOrMergeEntity
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addOperation
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::getOperations
     */
    public function testAddInsertOrMergeEntity()
    {
        // Setup
        $table = 'mytable';
        $entity = new Entity();
        $operations = new BatchOperations();
        
        // Test
        $operations->addInsertOrMergeEntity($table, $entity);
        
        // Assert
        $this->assertCount(1, $operations->getOperations());
    }
    
    /**
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addDeleteEntity
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::addOperation
     *  @covers MicrosoftAzure\Storage\Table\Models\BatchOperations::getOperations
     */
    public function testAddDeleteEntity()
    {
        // Setup
        $table = 'mytable';
        $partitionKey = '123';
        $rowKey= '456';
        $etag = 'W/datetime:2009';
        $operations = new BatchOperations();
        
        // Test
        $operations->addDeleteEntity($table, $partitionKey, $rowKey, $etag);
        
        // Assert
        $this->assertCount(1, $operations->getOperations());
    }
}
