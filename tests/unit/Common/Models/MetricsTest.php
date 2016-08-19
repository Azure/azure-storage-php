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
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;

/**
 * Unit tests for class Metrics
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class MetricsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::create
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        
        // Test
        $actual = Metrics::create($sample['HourMetrics']);
        
        // Assert
        $this->assertEquals(Utilities::toBoolean($sample['HourMetrics']['Enabled']), $actual->getEnabled());
        $this->assertEquals(Utilities::toBoolean($sample['HourMetrics']['IncludeAPIs']), $actual->getIncludeAPIs());
        $this->assertEquals(RetentionPolicy::create($sample['HourMetrics']['RetentionPolicy']), $actual->getRetentionPolicy());
        $this->assertEquals($sample['HourMetrics']['Version'], $actual->getVersion());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::getRetentionPolicy
     */
    public function testGetRetentionPolicy()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = new Metrics();
        $expected = RetentionPolicy::create($sample['HourMetrics']['RetentionPolicy']);
        $metrics->setRetentionPolicy($expected);
        
        // Test
        $actual = $metrics->getRetentionPolicy();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::setRetentionPolicy
     */
    public function testSetRetentionPolicy()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = new Metrics();
        $expected = RetentionPolicy::create($sample['HourMetrics']['RetentionPolicy']);
        
        // Test
        $metrics->setRetentionPolicy($expected);
        
        // Assert
        $actual = $metrics->getRetentionPolicy();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::getVersion
     */
    public function testGetVersion()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = new Metrics();
        $expected = $sample['HourMetrics']['Version'];
        $metrics->setVersion($expected);
        
        // Test
        $actual = $metrics->getVersion();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::setVersion
     */
    public function testSetVersion()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = new Metrics();
        $expected = $sample['HourMetrics']['Version'];
        
        // Test
        $metrics->setVersion($expected);
        
        // Assert
        $actual = $metrics->getVersion();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::getEnabled
     */
    public function testGetEnabled()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = new Metrics();
        $expected = Utilities::toBoolean($sample['HourMetrics']['Enabled']);
        $metrics->setEnabled($expected);
        
        // Test
        $actual = $metrics->getEnabled();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::setEnabled
     */
    public function testSetEnabled()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = new Metrics();
        $expected = Utilities::toBoolean($sample['HourMetrics']['Enabled']);
        
        // Test
        $metrics->setEnabled($expected);
        
        // Assert
        $actual = $metrics->getEnabled();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::getIncludeAPIs
     */
    public function testGetIncludeAPIs()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = new Metrics();
        $expected = Utilities::toBoolean($sample['HourMetrics']['IncludeAPIs']);
        $metrics->setIncludeAPIs($expected);
        
        // Test
        $actual = $metrics->getIncludeAPIs();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::setIncludeAPIs
     */
    public function testSetIncludeAPIs()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = new Metrics();
        $expected = Utilities::toBoolean($sample['HourMetrics']['IncludeAPIs']);
        
        // Test
        $metrics->setIncludeAPIs($expected);
        
        // Assert
        $actual = $metrics->getIncludeAPIs();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::toArray
     */
    public function testToArray()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = Metrics::create($sample['HourMetrics']);
        $expected = array(
            'Version'         => $sample['HourMetrics']['Version'],
            'Enabled'         => $sample['HourMetrics']['Enabled'],
            'IncludeAPIs'     => $sample['HourMetrics']['IncludeAPIs'],
            'RetentionPolicy' => $metrics->getRetentionPolicy()->toArray()
        );
        
        // Test
        $actual = $metrics->toArray();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\Metrics::toArray
     */
    public function testToArrayWithNotEnabled()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $sample['HourMetrics']['Enabled'] = 'false';
        $metrics = Metrics::create($sample['HourMetrics']);
        $expected = array(
            'Version'         => $sample['HourMetrics']['Version'],
            'Enabled'         => $sample['HourMetrics']['Enabled'],
            'RetentionPolicy' => $metrics->getRetentionPolicy()->toArray()
        );
        
        // Test
        $actual = $metrics->toArray();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}


