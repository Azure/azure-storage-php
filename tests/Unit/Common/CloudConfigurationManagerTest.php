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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common;

use MicrosoftAzure\Storage\Common\CloudConfigurationManager;
use MicrosoftAzure\Storage\Common\Internal\ConnectionStringSource;

/**
 * Unit tests for class CloudConfigurationManager
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CloudConfigurationManagerTest extends \PHPUnit\Framework\TestCase
{
    private $_key = 'my_connection_string';
    private $_value = 'connection string value';

    public function setUp()
    {
        $isInitialized = new \ReflectionProperty('MicrosoftAzure\Storage\Common\CloudConfigurationManager', '_isInitialized');
        $isInitialized->setAccessible(true);
        $isInitialized->setValue(false);

        $sources = new \ReflectionProperty('MicrosoftAzure\Storage\Common\CloudConfigurationManager', '_sources');
        $sources->setAccessible(true);
        $sources->setValue(array());
    }

    public function testGetConnectionStringFromEnvironmentVariable()
    {
        // Setup
        putenv("$this->_key=$this->_value");

        // Test
        $actual = CloudConfigurationManager::getConnectionString($this->_key);

        // Assert
        $this->assertEquals($this->_value, $actual);

        // Clean
        putenv($this->_key);
    }

    public function testGetConnectionStringDoesNotExist()
    {
        // Test
        $actual = CloudConfigurationManager::getConnectionString('does not exist');

        // Assert
        $this->assertEmpty($actual);
    }

    public function testRegisterSource()
    {
        // Setup
        $expectedKey = $this->_key;
        $expectedValue = $this->_value . "extravalue";

        // Test
        CloudConfigurationManager::registerSource(
            'my_source',
            function ($key) use ($expectedKey, $expectedValue) {
                if ($key == $expectedKey) {
                    return $expectedValue;
                }
            }
        );

        // Assert
        $actual = CloudConfigurationManager::getConnectionString($expectedKey);
        $this->assertEquals($expectedValue, $actual);
    }

    public function testRegisterSourceWithPrepend()
    {
        // Setup
        $expectedKey = $this->_key;
        $expectedValue = $this->_value . "extravalue2";
        putenv("$this->_key=wrongvalue");

        // Test
        CloudConfigurationManager::registerSource(
            'my_source',
            function ($key) use ($expectedKey, $expectedValue) {
                if ($key == $expectedKey) {
                    return $expectedValue;
                }
            },
            true
        );

        // Assert
        $actual = CloudConfigurationManager::getConnectionString($expectedKey);
        $this->assertEquals($expectedValue, $actual);

        // Clean
        putenv($this->_key);
    }

    public function testUnRegisterSource()
    {
        // Setup
        $expectedKey = $this->_key;
        $expectedValue = $this->_value . "extravalue3";
        $name = 'my_source';
        CloudConfigurationManager::registerSource(
            $name,
            function ($key) use ($expectedKey, $expectedValue) {
                if ($key == $expectedKey) {
                    return $expectedValue;
                }
            }
        );

        // Test
        $callback = CloudConfigurationManager::unregisterSource($name);

        // Assert
        $actual = CloudConfigurationManager::getConnectionString($expectedKey);
        $this->assertEmpty($actual);
        $this->assertNotNull($callback);
    }

    public function testRegisterSourceWithDefaultSource()
    {
        // Setup
        $expectedKey = $this->_key;
        $expectedValue = $this->_value . "extravalue5";
        CloudConfigurationManager::unregisterSource(ConnectionStringSource::ENVIRONMENT_SOURCE);
        putenv("$expectedKey=$expectedValue");

        // Test
        CloudConfigurationManager::registerSource(ConnectionStringSource::ENVIRONMENT_SOURCE);

        // Assert
        $actual = CloudConfigurationManager::getConnectionString($expectedKey);
        $this->assertEquals($expectedValue, $actual);

        // Clean
        putenv($expectedKey);
    }

    public function testUnRegisterSourceWithDefaultSource()
    {
        // Setup
        $expectedKey = $this->_key;
        $expectedValue = $this->_value . "extravalue4";
        $name = 'my_source';
        CloudConfigurationManager::registerSource(
            $name,
            function ($key) use ($expectedKey, $expectedValue) {
                if ($key == $expectedKey) {
                    return $expectedValue;
                }
            }
        );

        // Test
        $callback = CloudConfigurationManager::unregisterSource(ConnectionStringSource::ENVIRONMENT_SOURCE);

        // Assert
        $actual = CloudConfigurationManager::getConnectionString($expectedKey);
        $this->assertEquals($expectedValue, $actual);
        $this->assertNotNull($callback);
    }
}
