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
use MicrosoftAzure\Storage\Blob\Models\GetContainerAclResult;
use MicrosoftAzure\Storage\Blob\Models\ContainerAcl;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class GetContainerAclResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class GetContainerAclResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerAclResult::create
     */
    public function testCreate()
    {
        // Setup
        $sample = Resources::EMPTY_STRING;
        $expectedETag = '0x8CAFB82EFF70C46';
        $expectedDate = new \DateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $expectedPublicAccess = 'container';
        
        // Test
        $result = GetContainerAclResult::create($expectedPublicAccess, $expectedETag, 
            $expectedDate, $sample);
        
        // Assert
        $obj = $result->getContainerAcl();
        $this->assertEquals($expectedPublicAccess, $obj->getPublicAccess());
        $this->assertCount(0, $obj->getSignedIdentifiers());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerAclResult::getContainerAcl
     */
    public function testGetContainerAcl()
    {
        // Setup
        $expected = new ContainerAcl();
        $obj = new GetContainerAclResult();
        
        // Test
        $obj->setContainerAcl($expected);
        
        // Assert
        $this->assertCount(0, $obj->getContainerAcl()->getSignedIdentifiers());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerAclResult::setContainerAcl
     */
    public function testSetContainerAcl()
    {
        // Setup
        $expected = new ContainerAcl();
        $obj = new GetContainerAclResult();
        $obj->setContainerAcl($expected);
        
        // Test
        $actual = $obj->getContainerAcl();
        
        // Assert
        $this->assertCount(0, $actual->getSignedIdentifiers());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerAclResult::setLastModified
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerAclResult::getLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $expected = new \DateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $obj = new GetContainerAclResult();
        $obj->setLastModified($expected);
        
        // Test
        $obj->setLastModified($expected);
        
        // Assert
        $this->assertEquals($expected, $obj->getLastModified());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerAclResult::setETag
     * @covers MicrosoftAzure\Storage\Blob\Models\GetContainerAclResult::getETag
     */
    public function testSetETag()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $obj = new GetContainerAclResult();
        $obj->setETag($expected);
        
        // Test
        $obj->setETag($expected);
        
        // Assert
        $this->assertEquals($expected, $obj->getETag());
    }
}


