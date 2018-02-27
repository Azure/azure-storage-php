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

use MicrosoftAzure\Storage\Blob\Models\ContainerACL;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;

/**
 * Unit tests for class ContainerACL
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ContainerACLTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateEmpty()
    {
        // Setup
        $sample = array();
        $expectedPublicAccess = 'container';

        // Test
        $acl = ContainerACL::create($expectedPublicAccess, $sample);

        // Assert
        $this->assertEquals($expectedPublicAccess, $acl->getPublicAccess());
        $this->assertCount(0, $acl->getSignedIdentifiers());
    }

    public function testCreateOneEntry()
    {
        // Setup
        $sample = TestResources::getContainerAclOneEntrySample();
        $expectedPublicAccess = 'container';

        // Test
        $acl = ContainerACL::create($expectedPublicAccess, $sample['SignedIdentifiers']);

        // Assert
        $this->assertEquals($expectedPublicAccess, $acl->getPublicAccess());
        $this->assertCount(1, $acl->getSignedIdentifiers());
    }

    public function testCreateMultipleEntries()
    {
        // Setup
        $sample = TestResources::getContainerAclMultipleEntriesSample();
        $expectedPublicAccess = 'container';

        // Test
        $acl = ContainerACL::create($expectedPublicAccess, $sample['SignedIdentifiers']);

        // Assert
        $this->assertEquals($expectedPublicAccess, $acl->getPublicAccess());
        $this->assertCount(2, $acl->getSignedIdentifiers());

        return $acl;
    }

    public function testSetPublicAccess()
    {
        // Setup
        $expected = 'container';
        $acl = new ContainerACL();
        $acl->setPublicAccess($expected);

        // Test
        $acl->setPublicAccess($expected);

        // Assert
        $this->assertEquals($expected, $acl->getPublicAccess());
    }
}
