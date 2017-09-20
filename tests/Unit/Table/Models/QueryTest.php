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

use MicrosoftAzure\Storage\Table\Models\Query;
use MicrosoftAzure\Storage\Table\Models\Filters\Filter;
use MicrosoftAzure\Storage\Table\Models\EdmType;

/**
 * Unit tests for class Query
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Query::setSelectFields
     * @covers MicrosoftAzure\Storage\Table\Models\Query::getSelectFields
     */
    public function testSetSelectFields()
    {
        // Setup
        $query = new Query();
        $expected = array('customerId', 'customerName');
        
        // Test
        $query->setSelectFields($expected);
        
        // Assert
        $this->assertEquals($expected, $query->getSelectFields());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Query::setTop
     * @covers MicrosoftAzure\Storage\Table\Models\Query::getTop
     */
    public function testSetTop()
    {
        // Setup
        $query = new Query();
        $expected = 123;
        
        // Test
        $query->setTop($expected);
        
        // Assert
        $this->assertEquals($expected, $query->getTop());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Query::setFilter
     * @covers MicrosoftAzure\Storage\Table\Models\Query::getFilter
     */
    public function testSetFilter()
    {
        // Setup
        $query = new Query();
        $expected = Filter::applyConstant('constValue', EdmType::STRING);
        
        // Test
        $query->setFilter($expected);
        
        // Assert
        $this->assertEquals($expected, $query->getFilter());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Query::addSelectField
     * @covers MicrosoftAzure\Storage\Table\Models\Query::getSelectFields
     */
    public function testAddSelectField()
    {
        // Setup
        $query = new Query();
        $field = 'customerId';
        $expected = array($field);
        
        // Test
        $query->addSelectField($field);
        
        // Assert
        $this->assertEquals($expected, $query->getSelectFields());
    }
}
