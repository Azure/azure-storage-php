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
use MicrosoftAzure\Storage\Table\Models\BatchOperation;
use MicrosoftAzure\Storage\Table\Models\BatchOperationType;
use MicrosoftAzure\Storage\Table\Models\BatchOperationParameterName;

/**
 * Unit tests for class BatchOperation
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class BatchOperationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperation::setType
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperation::getType
     */
    public function testSetType()
    {
        // Setup
        $batchOperation = new BatchOperation();
        $expected = BatchOperationType::DELETE_ENTITY_OPERATION;
        
        // Test
        $batchOperation->setType($expected);
        
        // Assert
        $this->assertEquals($expected, $batchOperation->getType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperation::addParameter
     * @covers MicrosoftAzure\Storage\Table\Models\BatchOperation::getParameter
     */
    public function testAddParameter()
    {
        // Setup
        $batchOperation = new BatchOperation();
        $expected = 'param zeta';
        $name = BatchOperationParameterName::BP_ENTITY;
        
        // Test
        $batchOperation->addParameter($name, $expected);
        
        // Assert
        $this->assertEquals($expected, $batchOperation->getParameter($name));
    }
}


