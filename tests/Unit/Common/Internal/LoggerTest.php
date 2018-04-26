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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Internal;

use MicrosoftAzure\Storage\Common\Logger;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\VirtualFileSystem;

/**
 * Unit tests for class Logger
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class LoggerTest extends \PHPUnit\Framework\TestCase
{
    public function testLogWithArray()
    {
        // Setup
        $virtualPath = VirtualFileSystem::newFile(Resources::EMPTY_STRING);
        $tip = 'This is array';
        $expected = "$tip\nArray\n(\n)\n";
        Logger::setLogFile($virtualPath);

        // Test
        Logger::log(array(), $tip);

        // Assert
        $actual = file_get_contents($virtualPath);
        $this->assertEquals($expected, $actual);
    }

    public function testLogWithString()
    {
        // Setup
        $virtualPath = VirtualFileSystem::newFile(Resources::EMPTY_STRING);
        $tip = 'This is string';
        $expected = "$tip\nI'm a string\n";
        Logger::setLogFile($virtualPath);

        // Test
        Logger::log('I\'m a string', $tip);

        // Assert
        $actual = file_get_contents($virtualPath);
        $this->assertEquals($expected, $actual);
    }
}
