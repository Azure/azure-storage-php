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
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult;

/**
 * Unit tests for class QueryEntitiesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class QueryEntitiesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult::create
     */
    public function testCreate()
    {
        // Test
        $result = QueryEntitiesResult::create(array(), array());
        
        // Assert
        $this->assertCount(0, $result->getEntities());
        $this->assertNull($result->getNextPartitionKey());
        $this->assertNull($result->getNextRowKey());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult::setNextPartitionKey
     * @covers MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult::getNextPartitionKey
     */
    public function testSetNextPartitionKey()
    {
        // Setup
        $result = new QueryEntitiesResult();
        $expected = 'parition';
        
        // Test
        $result->setNextPartitionKey($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getNextPartitionKey());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult::setNextRowKey
     * @covers MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult::getNextRowKey
     */
    public function testSetNextRowKey()
    {
        // Setup
        $result = new QueryEntitiesResult();
        $expected = 'edelo';
        
        // Test
        $result->setNextRowKey($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getNextRowKey());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult::setEntities
     * @covers MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult::getEntities
     */
    public function testSetEntities()
    {
        // Setup
        $result = new QueryEntitiesResult();
        $expected = array();
        
        // Test
        $result->setEntities($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getEntities());
    }
}


