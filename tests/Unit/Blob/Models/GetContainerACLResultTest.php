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

use MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult;
use MicrosoftAzure\Storage\Blob\Models\ContainerACL;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class GetContainerACLResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class GetContainerACLResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult::create
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult::getContainerAcl
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult::setContainerAcl
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult::setLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult::getLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult::setETag
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult::getETag
     */
    public function testCreate()
    {
        // Setup
        $sample = array();
        $expectedETag = '0x8CAFB82EFF70C46';
        $expectedDate = new \DateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $expectedPublicAccess = 'container';
        $expectedContainerACL = ContainerACL::create($expectedPublicAccess, $sample);
        
        // Test
        $result = GetContainerACLResult::create(
            $expectedPublicAccess,
            $expectedETag,
            $expectedDate,
            $sample
        );
        
        // Assert
        $this->assertEquals($expectedContainerACL, $result->getContainerAcl());
        $this->assertEquals($expectedDate, $result->getLastModified());
        $this->assertEquals($expectedETag, $result->getETag());
    }
}
