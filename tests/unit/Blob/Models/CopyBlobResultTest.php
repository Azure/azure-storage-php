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
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Blob\Models\CopyBlobResult;

/**
 * Unit tests for class SnapshotBlobResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class CopyBlobResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobResult::getETag
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobResult::setETag
     */
    public function testSetETag()
    {
        $createBlobSnapshotResult = new CopyBlobResult();
        $expected = "12345678";
        $createBlobSnapshotResult->setETag($expected);
        
        $this->assertEquals(
            $expected,
            $createBlobSnapshotResult->getETag()
            );
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobResult::getLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobResult::setLastModified
     */
    public function testSetLastModified()
    {
        $createBlobSnapshotResult = new CopyBlobResult();
        $expected = new \DateTime("2008-8-8");
        $createBlobSnapshotResult->setLastModified($expected);
        
        $this->assertEquals(
            $expected,
            $createBlobSnapshotResult->getLastModified()
            );
        
    }
}

