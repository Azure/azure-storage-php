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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Queue\Models;
use MicrosoftAzure\Storage\Queue\Models\CreateMessageOptions;

/**
 * Unit tests for class CreateMessageOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateMessageOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\CreateMessageOptions::getVisibilityTimeoutInSeconds
     */
    public function testGetVisibilityTimeoutInSeconds()
    {
        // Setup
        $createMessageOptions = new CreateMessageOptions();
        $expected = 1000;
        $createMessageOptions->setVisibilityTimeoutInSeconds($expected);
        
        // Test
        $actual = $createMessageOptions->getVisibilityTimeoutInSeconds();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\CreateMessageOptions::setVisibilityTimeoutInSeconds
     */
    public function testSetVisibilityTimeoutInSeconds()
    {
        // Setup
        $createMessageOptions = new CreateMessageOptions();
        $expected = 1000;
        
        // Test
        $createMessageOptions->setVisibilityTimeoutInSeconds($expected);
        
        // Assert
        $actual = $createMessageOptions->getVisibilityTimeoutInSeconds();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\CreateMessageOptions::getTimeToLiveInSeconds
     */
    public function testGetTimeToLiveInSeconds()
    {
        // Setup
        $createMessageOptions = new CreateMessageOptions();
        $expected = 20;
        $createMessageOptions->setTimeToLiveInSeconds($expected);
        
        // Test
        $actual = $createMessageOptions->getTimeToLiveInSeconds();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\CreateMessageOptions::setTimeToLiveInSeconds
     */
    public function testSetTimeToLiveInSeconds()
    {
        // Setup
        $createMessageOptions = new CreateMessageOptions();
        $expected = 20;
        
        // Test
        $createMessageOptions->setTimeToLiveInSeconds($expected);
        
        // Assert
        $actual = $createMessageOptions->getTimeToLiveInSeconds();
        $this->assertEquals($expected, $actual);
    }
}


