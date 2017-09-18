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
use MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult;

/**
 * Unit tests for class CreateBlobPagesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateBlobPagesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::create
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::setLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::getLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::setETag
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::getETag
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::setContentMD5
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::getContentMD5
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::setSequenceNumber
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::getSequenceNumber
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::setRequestServerEncrypted
     * @covers MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult::getRequestServerEncrypted
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        $expected = $sample['Blobs']['Blob']['Properties'];
        $expectedDate = Utilities::rfc1123ToDateTime($expected['Last-Modified']);
        
        // Test
        $actual = CreateBlobPagesResult::create($expected);
        
        // Assert
        $this->assertEquals($expectedDate, $actual->getLastModified());
        $this->assertEquals($expected['Etag'], $actual->getETag());
        $this->assertEquals($expected['Content-MD5'], $actual->getContentMD5());
        $this->assertEquals(intval($expected['x-ms-blob-sequence-number']), $actual->getSequenceNumber());
        $this->assertEquals(Utilities::toBoolean($expected['x-ms-request-server-encrypted']), $actual->getRequestServerEncrypted());
    }
}
