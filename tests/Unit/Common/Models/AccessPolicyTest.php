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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\Unit\Common\Models;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class AccessPolicy
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
abstract class AccessPolicyTest extends \PHPUnit\Framework\TestCase
{
    abstract protected function createAccessPolicy();
    abstract protected function getResourceType();

    public function testGetStart()
    {
        // Setup
        $accessPolicy = static::createAccessPolicy();
        $expected = new \DateTime('2009-09-28T08:49:37');
        $accessPolicy->setStart($expected);

        // Test
        $actual = $accessPolicy->getStart();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetStart()
    {
        // Setup
        $accessPolicy = static::createAccessPolicy();
        $expected = new \DateTime('2009-09-28T08:49:37');

        // Test
        $accessPolicy->setStart($expected);

        // Assert
        $this->assertEquals($expected, $accessPolicy->getStart());
    }

    public function testGetExpiry()
    {
        // Setup
        $accessPolicy = static::createAccessPolicy();
        $expected = new \DateTime('2009-09-28T08:49:37');
        $accessPolicy->setExpiry($expected);

        // Test
        $actual = $accessPolicy->getExpiry();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetExpiry()
    {
        // Setup
        $accessPolicy = static::createAccessPolicy();
        $expected = new \DateTime('2009-09-28T08:49:37');

        // Test
        $accessPolicy->setExpiry($expected);

        // Assert
        $this->assertEquals($expected, $accessPolicy->getExpiry());
    }

    public function testSetPermission()
    {
        // Setup
        $validPermissions = TestResources::getValidAccessPermission();
        $accessPolicy = static::createAccessPolicy();

        foreach ($validPermissions[static::getResourceType()] as $value) {
            $expected = $value[1];

            // Test
            $accessPolicy->setPermission($value[0]);
            // Assert
            $this->assertEquals($expected, $accessPolicy->getPermission());
        }
    }

    public function testSetPermissionNegative()
    {
        // Setup
        $validPermissions = TestResources::getInvalidAccessPermission();
        $accessPolicy = static::createAccessPolicy();

        foreach ($validPermissions[static::getResourceType()] as $value) {
            // Test
            try {
                $accessPolicy->setPermission($value);
            } catch (\InvalidArgumentException $e) {
                $this->assertStringStartsWith(
                    'Invalid permission provided',
                    $e->getMessage()
                );
                continue;
            }
            $this->assertTrue(false);
        }
    }

    public function testToArray()
    {
        // Setup
        $accessPolicy = static::createAccessPolicy();
        $permission = 'r';
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
