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
use MicrosoftAzure\Storage\Blob\Models\AccessPolicy;

/**
 * Unit tests for class AccessPolicy
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class AccessPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessPolicy::getStart 
     */
    public function testGetStart()
    {
        // Setup
        $accessPolicy = new AccessPolicy();
        $expected = new \DateTime('2009-09-28T08:49:37');
        $accessPolicy->setStart($expected);
        
        // Test
        $actual = $accessPolicy->getStart();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessPolicy::setStart 
     */
    public function testSetStart()
    {
        // Setup
        $accessPolicy = new AccessPolicy();
        $expected = new \DateTime('2009-09-28T08:49:37');
        
        // Test
        $accessPolicy->setStart($expected);
        
        // Assert
        $this->assertEquals($expected, $accessPolicy->getStart());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessPolicy::getExpiry 
     */
    public function testGetExpiry()
    {
        // Setup
        $accessPolicy = new AccessPolicy();
        $expected = new \DateTime('2009-09-28T08:49:37');
        $accessPolicy->setExpiry($expected);
        
        // Test
        $actual = $accessPolicy->getExpiry();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessPolicy::setExpiry 
     */
    public function testSetExpiry()
    {
        // Setup
        $accessPolicy = new AccessPolicy();
        $expected = new \DateTime('2009-09-28T08:49:37');
        
        // Test
        $accessPolicy->setExpiry($expected);
        
        // Assert
        $this->assertEquals($expected, $accessPolicy->getExpiry());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessPolicy::getPermission 
     */
    public function testGetPermission()
    {
        // Setup
        $accessPolicy = new AccessPolicy();
        $expected = 'rw';
        $accessPolicy->setPermission($expected);
        
        // Test
        $actual = $accessPolicy->getPermission();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessPolicy::setPermission 
     */
    public function testSetPermission()
    {
        // Setup
        $accessPolicy = new AccessPolicy();
        $expected = 'rw';
        
        // Test
        $accessPolicy->setPermission($expected);
        
        // Assert
        $this->assertEquals($expected, $accessPolicy->getPermission());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\Models\AccessPolicy::toArray
     */
    public function testToArray()
    {
        // Setup
        $accessPolicy = new AccessPolicy();
        $permission = 'rw';
        $start = '2009-09-28T08:49:37.3942040Z';
        $expiry = '2009-10-28T08:49:37.3942040Z';
        $startDate = new \DateTime($start);
        $expiryDate = new \DateTime($expiry);
        $accessPolicy->setPermission($permission);
        $accessPolicy->setStart($startDate);
        $accessPolicy->setExpiry($expiryDate);
        
        // Test
        $actual = $accessPolicy->toArray();
        
        // Assert
        $this->assertEquals($permission, $actual['Permission']);
        $this->assertEquals($start, urldecode($actual['Start']));
        $this->assertEquals($expiry, urldecode($actual['Expiry']));
    }
}


