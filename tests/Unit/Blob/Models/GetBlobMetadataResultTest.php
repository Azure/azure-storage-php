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

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult;

/**
 * Unit tests for class GetBlobMetadataResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class GetBlobMetadataResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::getETag
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::setETag
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::getLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::setLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::setMetadata
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::getMetadata
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::create
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        $expectedProperties = $sample['Blobs']['Blob']['Properties'];
        $expectedDate = Utilities::rfc1123ToDateTime($expectedProperties['Last-Modified']);
        $expectedProperties['x-ms-meta-test0'] = 'test0';
        $expectedProperties['x-ms-meta-test1'] = 'test1';
        $expectedProperties['x-ms-meta-test2'] = 'test2';
        $expectedProperties['x-ms-meta-test3'] = 'test3';
        
        // Test
        $actual = GetBlobMetadataResult::create($expectedProperties);
        
        // Assert
        $this->assertEquals($expectedDate, $actual->getLastModified());
        $this->assertEquals($expectedProperties['Etag'], $actual->getETag());

        $metadata = $actual->getMetadata();
        $this->assertEquals('test0', $metadata['test0']);
        $this->assertEquals('test1', $metadata['test1']);
        $this->assertEquals('test2', $metadata['test2']);
        $this->assertEquals('test3', $metadata['test3']);
    }
}
