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
use MicrosoftAzure\Storage\Blob\Models\SignedIdentifier;
use MicrosoftAzure\Storage\Blob\Models\AccessPolicy;

/**
 * Unit tests for class SignedIdentifier
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class SignedIdentifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SignedIdentifier::getId 
     */
    public function testGetId()
    {
        // Setup
        $signedIdentifier = new SignedIdentifier();
        $expected = 'MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTI=';
        $signedIdentifier->setId($expected);
        
        // Test
        $actual = $signedIdentifier->getId();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SignedIdentifier::setId 
     */
    public function testSetId()
    {
        // Setup
        $signedIdentifier = new SignedIdentifier();
        $expected = 'MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTI=';
        
        // Test
        $signedIdentifier->setId($expected);
        
        // Assert
        $this->assertEquals($expected, $signedIdentifier->getId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SignedIdentifier::getAccessPolicy 
     */
    public function testGetAccessPolicy()
    {
        // Setup
        $signedIdentifier = new SignedIdentifier();
        $expected = new AccessPolicy();
        $expected->setExpiry(new \DateTime('2009-09-29T08:49:37'));
        $expected->setPermission('rwd');
        $expected->setStart(new \DateTime('2009-09-28T08:49:37'));
        $signedIdentifier->setAccessPolicy($expected);
        
        // Test
        $actual = $signedIdentifier->getAccessPolicy();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SignedIdentifier::setAccessPolicy
     */
    public function testSetAccessPolicy()
    {
        // Setup
        $signedIdentifier = new SignedIdentifier();
        $expected = new AccessPolicy();
        $expected->setExpiry(new \DateTime('2009-09-29T08:49:37'));
        $expected->setPermission('rwd');
        $expected->setStart(new \DateTime('2009-09-28T08:49:37'));
        
        // Test
        $signedIdentifier->setAccessPolicy($expected);
        
        // Assert
        $this->assertEquals($expected, $signedIdentifier->getAccessPolicy());
        
        return $signedIdentifier;
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\SignedIdentifier::toArray
     * @depends testSetAccessPolicy
     */
    public function testToXml($signedIdentifier)
    {
        // Setup
        $id = 'MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTI=';
        $signedIdentifier->setId($id);
        
        // Test
        $array = $signedIdentifier->toArray();
        
        // Assert
        $this->assertEquals($id, $array['SignedIdentifier']['Id']);
        $this->assertArrayHasKey('AccessPolicy', $array['SignedIdentifier']);
    }
}


