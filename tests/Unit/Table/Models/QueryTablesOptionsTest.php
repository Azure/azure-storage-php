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

use MicrosoftAzure\Storage\Table\Models\QueryTablesOptions;
use MicrosoftAzure\Storage\Table\Models\Query;
use MicrosoftAzure\Storage\Table\Models\Filters\Filter;
use MicrosoftAzure\Storage\Table\Models\EdmType;

/**
 * Unit tests for class QueryTablesOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueryTablesOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::setNextTableName
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::getNextTableName
     */
    public function testSetNextTableName()
    {
        // Setup
        $options = new QueryTablesOptions();
        $expected = 'table';
        
        // Test
        $options->setNextTableName($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getNextTableName());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::setPrefix
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::getPrefix
     */
    public function testSetPrefix()
    {
        // Setup
        $options = new QueryTablesOptions();
        $expected = 'prefix';
        
        // Test
        $options->setPrefix($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getPrefix());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::setTop
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::getTop
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::__construct
     */
    public function testSetTop()
    {
        // Setup
        $options = new QueryTablesOptions();
        $expected = 123;
        
        // Test
        $options->setTop($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getTop());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::getQuery
     */
    public function testGetQuery()
    {
        // Setup
        $options = new QueryTablesOptions();
        $expected = new Query();
        
        // Test
        $actual = $options->getQuery();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::setFilter
     * @covers MicrosoftAzure\Storage\Table\Models\QueryTablesOptions::getFilter
     */
    public function testSetFilter()
    {
        // Setup
        $options = new QueryTablesOptions();
        $expected = Filter::applyConstant('constValue', EdmType::STRING);
        
        // Test
        $options->setFilter($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getFilter());
    }
}
