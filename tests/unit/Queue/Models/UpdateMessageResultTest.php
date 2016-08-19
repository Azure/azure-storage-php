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
use MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class UpdateMessageResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class UpdateMessageResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::getPopReceipt
     */
    public function testGetPopReceipt()
    {
        // Setup
        $updateMessageResult = new UpdateMessageResult();
        $expected = 'YzQ4Yzg1MDItYTc0Ny00OWNjLTkxYTUtZGM0MDFiZDAwYzEw';
        $updateMessageResult->setPopReceipt($expected);
        
        // Test
        $actual = $updateMessageResult->getPopReceipt();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::setPopReceipt
     */
    public function testSetPopReceipt()
    {
        // Setup
        $updateMessageResult = new UpdateMessageResult();
        $expected = 'YzQ4Yzg1MDItYTc0Ny00OWNjLTkxYTUtZGM0MDFiZDAwYzEw';
        
        // Test
        $updateMessageResult->setPopReceipt($expected);
        
        // Assert
        $actual = $updateMessageResult->getPopReceipt();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::getTimeNextVisible
     */
    public function testGetTimeNextVisible()
    {
        // Setup
        $updateMessageResult = new UpdateMessageResult();
        $expected = Utilities::rfc1123ToDateTime('Fri, 09 Oct 2009 23:29:20 GMT');
        $updateMessageResult->setTimeNextVisible($expected);
        
        // Test
        $actual = $updateMessageResult->getTimeNextVisible();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult::setTimeNextVisible
     */
    public function testSetTimeNextVisible()
    {
        // Setup
        $updateMessageResult = new UpdateMessageResult();
        $expected = Utilities::rfc1123ToDateTime('Fri, 09 Oct 2009 23:29:20 GMT');
        
        // Test
        $updateMessageResult->setTimeNextVisible($expected);
        
        // Assert
        $actual = $updateMessageResult->getTimeNextVisible();
        $this->assertEquals($expected, $actual);
    }
}


