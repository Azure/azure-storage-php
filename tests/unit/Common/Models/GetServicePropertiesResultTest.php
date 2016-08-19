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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Models;
use MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class GetServicePropertiesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class GetServicePropertiesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult::create
     */
    public function testCreate()
    {
        // Test
        $result = GetServicePropertiesResult::create(TestResources::getServicePropertiesSample());
        
        // Assert
        $this->assertTrue(isset($result));
        
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult::getValue
     */
    public function testGetValue()
    {
        // Setup
        $result = GetServicePropertiesResult::create(TestResources::getServicePropertiesSample());
        $expected = ServiceProperties::create(TestResources::getServicePropertiesSample());
        
        // Test
        $actual = $result->getValue();
        
        // Assert
        $this->assertEquals($expected, $actual);
        
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult::setValue
     */
    public function testSetValue()
    {
        // Setup
        $result = new GetServicePropertiesResult();
        $expected = ServiceProperties::create(TestResources::getServicePropertiesSample());
        
        // Test
        $result->setValue($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getValue());
        
    }
}


