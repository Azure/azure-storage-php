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
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;

/**
 * Unit tests for class ServiceProperties
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ServicePropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\ServiceProperties::create
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = Logging::create($sample['Logging']);
        $metrics = Metrics::create($sample['HourMetrics']);
        
        // Test
        $result = ServiceProperties::create($sample);
        
        // Assert
        $this->assertEquals($logging, $result->getLogging());
        $this->assertEquals($metrics, $result->getMetrics());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\ServiceProperties::setLogging
     */
    public function testSetLogging()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = Logging::create($sample['Logging']);
        $result = new ServiceProperties();
        
        // Test
        $result->setLogging($logging);
        
        // Assert
        $this->assertEquals($logging, $result->getLogging());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\ServiceProperties::getLogging
     */
    public function testGetLogging()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $logging = Logging::create($sample['Logging']);
        $result = new ServiceProperties();
        $result->setLogging($logging);
        
        // Test
        $actual = $result->getLogging($logging);
        
        // Assert
        $this->assertEquals($logging, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\ServiceProperties::setMetrics
     */
    public function testSetMetrics()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = Metrics::create($sample['HourMetrics']);
        $result = new ServiceProperties();
        
        // Test
        $result->setMetrics($metrics);
        
        // Assert
        $this->assertEquals($metrics, $result->getMetrics());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\ServiceProperties::getMetrics
     */
    public function testGetMetrics()
    {
        // Setup
        $sample = TestResources::getServicePropertiesSample();
        $metrics = Metrics::create($sample['HourMetrics']);
        $result = new ServiceProperties();
        $result->setMetrics($metrics);
        
        // Test
        $actual = $result->getMetrics($metrics);
        
        // Assert
        $this->assertEquals($metrics, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\ServiceProperties::toArray
     */
    public function testToArray()
    {
        // Setup
        $properties = ServiceProperties::create(TestResources::getServicePropertiesSample());
        $expected = array(
            'Logging' => $properties->getLogging()->toArray(),
            'HourMetrics' => $properties->getMetrics()->toArray()
        );
        
        // Test
        $actual = $properties->toArray();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\Models\ServiceProperties::toXml
     */
    public function testToXml()
    {
        // Setup
        $properties = ServiceProperties::create(TestResources::getServicePropertiesSample());
        $xmlSerializer = new XmlSerializer();
        
        // Test
        $actual = $properties->toXml($xmlSerializer);
        
        // Assert
        $actualParsed = Utilities::unserialize($actual);
        $actualProperties = GetServicePropertiesResult::create($actualParsed);
        $this->assertEquals($actualProperties->getValue(), $properties);
    }
}


