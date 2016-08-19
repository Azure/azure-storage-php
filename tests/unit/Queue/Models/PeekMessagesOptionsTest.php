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
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;

/**
 * Unit tests for class PeekMessagesOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class PeekMessagesOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions::getNumberOfMessages
     */
    public function testGetNumberOfMessages()
    {
        // Setup
        $peekMessagesOptions = new PeekMessagesOptions();
        $expected = 10;
        $peekMessagesOptions->setNumberOfMessages($expected);
        
        // Test
        $actual = $peekMessagesOptions->getNumberOfMessages();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions::setNumberOfMessages
     */
    public function testSetNumberOfMessages()
    {
        // Setup
        $peekMessagesOptions = new PeekMessagesOptions();
        $expected = 10;
        
        // Test
        $peekMessagesOptions->setNumberOfMessages($expected);
        
        // Assert
        $actual = $peekMessagesOptions->getNumberOfMessages();
        $this->assertEquals($expected, $actual);
    }
}


