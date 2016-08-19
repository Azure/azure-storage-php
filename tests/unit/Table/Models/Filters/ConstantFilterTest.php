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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Table\Models\Filters;
use MicrosoftAzure\Storage\Table\Models\Filters\ConstantFilter;
use MicrosoftAzure\Storage\Table\Models\EdmType;

/**
 * Unit tests for class ConstantFilter
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ConstantFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Filters\ConstantFilter::__construct
     * @covers MicrosoftAzure\Storage\Table\Models\Filters\ConstantFilter::getValue
     */
    public function testGetValue()
    {
        // Setup
        $expected = 'x';
        $filter = new ConstantFilter(null, $expected);
        
        // Assert
        $this->assertEquals($expected, $filter->getValue());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\Filters\ConstantFilter::__construct
     * @covers MicrosoftAzure\Storage\Table\Models\Filters\ConstantFilter::getEdmType
     */
    public function testGetEdmType()
    {
        // Setup
        $expected = EdmType::BINARY;
        $filter = new ConstantFilter($expected, '1234');
        
        // Assert
        $this->assertEquals($expected, $filter->getEdmType());
    }
}


