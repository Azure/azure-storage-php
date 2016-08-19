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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Filters;
use MicrosoftAzure\Storage\Common\Internal\Filters\ExponentialRetryPolicy;

/**
 * Unit tests for class ExponentialRetryPolicy
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal\Filters
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ExponentialRetryPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Filters\ExponentialRetryPolicy::__construct
     */
    public function test__construct()
    {
        // Setup
        $expectedRetryableStatusCodes = array(200, 201);
        
        // Test
        $actual = new ExponentialRetryPolicy($expectedRetryableStatusCodes);
        
        // Assert
        $this->assertNotNull($actual);
        
        return $actual;
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Filters\ExponentialRetryPolicy::shouldRetry
     * @depends test__construct
     */
    public function testShouldRetryFalse($retryPolicy)
    {
        // Setup
        $expected = false;
        
        // Test
        $actual = $retryPolicy->shouldRetry(1000, null);
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\Filters\ExponentialRetryPolicy::calculateBackoff
     * @depends test__construct
     */
    public function testCalculateBackoff($retryPolicy)
    {
        // Test
        $actual = $retryPolicy->calculateBackoff(2, null);
        
        // Assert
        $this->assertTrue(is_integer($actual));
        
    }
}


