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
namespace MicrosoftAzure\Storage\Tests\unit\Blob\Models;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Blob\Models\PutBlockResult;

/**
 * Unit tests for class PutBlockResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.12.1
 * @link      https://github.com/azure/azure-storage-php
 */
class PutBlockResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\PutBlockResult::create
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        $expected = $sample['Blobs']['Blob']['Properties'];
        $expectedDate = Utilities::rfc1123ToDateTime($expected['Last-Modified']);
        
        // Test
        $actual = PutBlockResult::create($expected);
        
        // Assert
        $this->assertEquals($expected['Content-MD5'], $actual->getContentMD5());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\PutBlockResult::setContentMD5
     * @covers MicrosoftAzure\Storage\Blob\Models\PutBlockResult::getContentMD5
     */
    public function testSetContentMD5()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $result = new PutBlockResult();
        $result->setContentMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $result->getContentMD5());
    }
}
