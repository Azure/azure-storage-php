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
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;
use MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions;

/**
 * Unit tests for class CopyBlobBlobOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CopyBlobOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::setMetadata
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::getMetadata
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::setMetadata
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::getMetadata
     */
    public function testSetMetadata()
    {
        $copyBlobOptions = new CopyBlobOptions();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $copyBlobOptions->setMetadata($expected);
        
        $this->assertEquals(
            $expected,
            $copyBlobOptions->getMetadata()
        );
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::setAccessConditions
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::getAccessConditions
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::setAccessConditions
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::getAccessConditions
     */
    public function testSetAccessConditions()
    {
        $copyBlobOptions = new CopyBlobOptions();
        $expected = AccessCondition::ifMatch("12345");
        $copyBlobOptions->setAccessConditions($expected);
        
        $this->assertEquals(
            $expected,
            $copyBlobOptions->getAccessConditions()[0]
        );
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::setSourceAccessConditions
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::getSourceAccessConditions
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::setSourceAccessConditions
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::getSourceAccessConditions
     */
    public function testSetSourceAccessConditions()
    {
        $copyBlobOptions = new CopyBlobOptions();
        $expected = AccessCondition::IfMatch("x");
        $copyBlobOptions->setSourceAccessConditions($expected);
        
        $this->assertEquals(
            $expected,
            $copyBlobOptions->getSourceAccessConditions()[0]
        );
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::getLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::setLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::getLeaseId
     */
    public function testSetLeaseId()
    {
        $expected = '0x8CAFB82EFF70C46';
        $options = new CopyBlobOptions();
        
        $options->setLeaseId($expected);
        $this->assertEquals($expected, $options->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::setSourceLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::getSourceLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::setSourceLeaseId
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::getSourceLeaseId
     */
    public function testSetSourceLeaseId()
    {
        $expected = '0x8CAFB82EFF70C46';
        $options = new CopyBlobOptions();
        
        $options->setSourceLeaseId($expected);
        $this->assertEquals($expected, $options->getSourceLeaseId());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::setIsIncrementalCopy
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobFromURLOptions::getIsIncrementalCopy
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::setIsIncrementalCopy
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::getIsIncrementalCopy
     */
    public function testSetIsIncrementalCopy()
    {
        $expected = true;
        $options = new CopyBlobOptions();

        $options->setIsIncrementalCopy($expected);
        $this->assertEquals($expected, $options->getIsIncrementalCopy());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::setSourceSnapshot
     * @covers MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions::getSourceSnapshot
     */
    public function testSetSourceSnapshot()
    {
        $expected = '2017-09-19T10:39:36.8401215Z';
        $options = new CopyBlobOptions();

        $options->setSourceSnapshot($expected);
        $this->assertEquals($expected, $options->getSourceSnapshot());
    }
}
