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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Table\Models;
use MicrosoftAzure\Storage\Table\Models\BatchError;
use MicrosoftAzure\Storage\Common\ServiceException;

/**
 * Unit tests for class BatchError
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class BatchErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\BatchError::create
     * @covers MicrosoftAzure\Storage\Table\Models\BatchError::getError
     * @covers MicrosoftAzure\Storage\Table\Models\BatchError::getContentId
     */
    public function testCreate()
    {
        // Setup
        $error = new ServiceException('200');
        $contentId = 1;
        $headers = array('content-id' => strval($contentId));
        
        // Test
        $batchError = BatchError::create($error, $headers);
        
        // Assert
        $this->assertEquals($error, $batchError->getError());
        $this->assertEquals($contentId, $batchError->getContentId());
        
        return $batchError;
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\BatchError::setError
     * @covers MicrosoftAzure\Storage\Table\Models\BatchError::getError
     * @depends testCreate
     */
    public function testSetError($batchError)
    {
        // Setup
        $error = new ServiceException('100');
        
        // Test
        $batchError->setError($error);
        
        // Assert
        $this->assertEquals($error, $batchError->getError());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Table\Models\BatchError::setContentId
     * @covers MicrosoftAzure\Storage\Table\Models\BatchError::getContentId
     * @depends testCreate
     */
    public function testSetContentId($batchError)
    {
        // Setup
        $contentId = 1;
        
        // Test
        $batchError->setContentId($contentId);
        
        // Assert
        $this->assertEquals($contentId, $batchError->getContentId());
    }
}


