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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\Unit\Blob\Models;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;

/**
 * Unit tests for class CreateBlobPagesOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateBlobPagesOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions::getAccessCondition
     */
    public function testGetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $options = new CreateBlobPagesOptions();
        $options->setAccessCondition($expected);
        
        // Test
        $actual = $options->getAccessCondition();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions::setAccessCondition
     */
    public function testSetAccessCondition()
    {
        // Setup
        $expected = AccessCondition::none();
        $options = new CreateBlobPagesOptions();
        
        // Test
        $options->setAccessCondition($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getAccessCondition());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions::setContentMD5
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions::getContentMD5
     */
    public function testSetContentMD5()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobPagesOptions();
        $options->setContentMD5($expected);
        
        // Test
        $options->setContentMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getContentMD5());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions::getLeaseId
     */
    public function testSetLeaseId()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobPagesOptions();
        $options->setLeaseId($expected);
        
        // Test
        $options->setLeaseId($expected);
        
        // Assert
        $this->assertEquals($expected, $options->getLeaseId());
    }
}


