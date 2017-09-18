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

use MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions;

/**
 * Unit tests for class ListMessagesOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListMessagesOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions::getVisibilityTimeoutInSeconds
     */
    public function testGetVisibilityTimeoutInSeconds()
    {
        // Setup
        $listMessagesOptions = new ListMessagesOptions();
        $expected = 1000;
        $listMessagesOptions->setVisibilityTimeoutInSeconds($expected);
        
        // Test
        $actual = $listMessagesOptions->getVisibilityTimeoutInSeconds();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions::setVisibilityTimeoutInSeconds
     */
    public function testSetVisibilityTimeoutInSeconds()
    {
        // Setup
        $listMessagesOptions = new ListMessagesOptions();
        $expected = 1000;
        
        // Test
        $listMessagesOptions->setVisibilityTimeoutInSeconds($expected);
        
        // Assert
        $actual = $listMessagesOptions->getVisibilityTimeoutInSeconds();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions::getNumberOfMessages
     */
    public function testGetNumberOfMessages()
    {
        // Setup
        $listMessagesOptions = new ListMessagesOptions();
        $expected = 10;
        $listMessagesOptions->setNumberOfMessages($expected);
        
        // Test
        $actual = $listMessagesOptions->getNumberOfMessages();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions::setNumberOfMessages
     */
    public function testSetNumberOfMessages()
    {
        // Setup
        $listMessagesOptions = new ListMessagesOptions();
        $expected = 10;
        
        // Test
        $listMessagesOptions->setNumberOfMessages($expected);
        
        // Assert
        $actual = $listMessagesOptions->getNumberOfMessages();
        $this->assertEquals($expected, $actual);
    }
}
