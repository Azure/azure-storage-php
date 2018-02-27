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

use MicrosoftAzure\Storage\Table\Models\Filters\Filter;
use MicrosoftAzure\Storage\Table\Models\EdmType;

/**
 * Unit tests for class Filter
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class FilterTest extends \PHPUnit\Framework\TestCase
{
    public function testApplyAnd()
    {
        // Setup
        $left = Filter::applyPropertyName('test');
        $right = Filter::applyQueryString('raw string');

        // Test
        $actual = Filter::applyAnd($left, $right);

        // Assert
        $this->assertEquals($left, $actual->getLeft());
        $this->assertEquals($right, $actual->getRight());
    }

    public function testApplyNot()
    {
        // Setup
        $operand = Filter::applyConstant('test', EdmType::STRING);

        // Test
        $actual = Filter::applyNot($operand);

        // Assert
        $this->assertEquals($operand, $actual->getOperand());
    }

    public function testApplyOr()
    {
        // Setup
        $left = Filter::applyPropertyName('test');
        $right = Filter::applyQueryString('raw string');

        // Test
        $actual = Filter::applyOr($left, $right);

        // Assert
        $this->assertEquals($left, $actual->getLeft());
        $this->assertEquals($right, $actual->getRight());
    }

    public function testApplyEq()
    {
        // Setup
        $left = Filter::applyPropertyName('test');
        $right = Filter::applyQueryString('raw string');

        // Test
        $actual = Filter::applyEq($left, $right);

        // Assert
        $this->assertEquals($left, $actual->getLeft());
        $this->assertEquals($right, $actual->getRight());
    }

    public function testApplyNe()
    {
        // Setup
        $left = Filter::applyPropertyName('test');
        $right = Filter::applyQueryString('raw string');

        // Test
        $actual = Filter::applyNe($left, $right);

        // Assert
        $this->assertEquals($left, $actual->getLeft());
        $this->assertEquals($right, $actual->getRight());
    }

    public function testApplyGe()
    {
        // Setup
        $left = Filter::applyPropertyName('test');
        $right = Filter::applyQueryString('raw string');

        // Test
        $actual = Filter::applyGe($left, $right);

        // Assert
        $this->assertEquals($left, $actual->getLeft());
        $this->assertEquals($right, $actual->getRight());
    }

    public function testApplyGt()
    {
        // Setup
        $left = Filter::applyPropertyName('test');
        $right = Filter::applyQueryString('raw string');

        // Test
        $actual = Filter::applyGt($left, $right);

        // Assert
        $this->assertEquals($left, $actual->getLeft());
        $this->assertEquals($right, $actual->getRight());
    }

    public function testApplyLt()
    {
        // Setup
        $left = Filter::applyPropertyName('test');
        $right = Filter::applyQueryString('raw string');

        // Test
        $actual = Filter::applyLt($left, $right);

        // Assert
        $this->assertEquals($left, $actual->getLeft());
        $this->assertEquals($right, $actual->getRight());
    }

    public function testApplyLe()
    {
        // Setup
        $left = Filter::applyPropertyName('test');
        $right = Filter::applyQueryString('raw string');

        // Test
        $actual = Filter::applyLe($left, $right);

        // Assert
        $this->assertEquals($left, $actual->getLeft());
        $this->assertEquals($right, $actual->getRight());
    }
}
