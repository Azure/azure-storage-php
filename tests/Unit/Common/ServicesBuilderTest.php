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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\MediaServicesSettings;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\Configuration;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;

/**
 * Unit tests for class ServicesBuilder
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ServicesBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildForQueue()
    {
        // Setup
        $builder = new ServicesBuilder();

        // Test
        $queueRestProxy = $builder->createQueueService(TestResources::getWindowsAzureStorageServicesConnectionString());

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Queue\Internal\IQueue', $queueRestProxy);
    }

    public function testBuildForBlob()
    {
        // Setup
        $builder = new ServicesBuilder();

        // Test
        $blobRestProxy = $builder->createBlobService(TestResources::getWindowsAzureStorageServicesConnectionString());

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Blob\Internal\IBlob', $blobRestProxy);
    }

    public function testBuildForTable()
    {
        // Setup
        $builder = new ServicesBuilder();

        // Test
        $tableRestProxy = $builder->createTableService(TestResources::getWindowsAzureStorageServicesConnectionString());

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Table\Internal\ITable', $tableRestProxy);
    }

    public function testBuildForFile()
    {
        // Setup
        $builder = new ServicesBuilder();

        // Test
        $fileRestProxy = $builder->createFileService(TestResources::getWindowsAzureStorageServicesConnectionString());

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\File\Internal\IFile', $fileRestProxy);
    }

    public function testGetInstance()
    {
        // Test
        $actual = ServicesBuilder::getInstance();

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Common\ServicesBuilder', $actual);
    }

    public function testBuildForAnonymousAccess()
    {
        $builder = new ServicesBuilder();

        $pEndpoint = sprintf(
            '%s://%s%s',
            Resources::HTTP_SCHEME,
            'myaccount.',
            Resources::BLOB_BASE_DNS_NAME
        );
        $sEndpoint = sprintf(
            '%s://%s%s',
            Resources::HTTP_SCHEME,
            'myaccount-secondary.',
            Resources::BLOB_BASE_DNS_NAME
        );

        $blobRestProxy = $builder->createContainerAnonymousAccess(
            $pEndpoint,
            $sEndpoint
        );

        $this->assertInstanceOf('MicrosoftAzure\Storage\Blob\Internal\IBlob', $blobRestProxy);
        $this->assertEquals('myaccount', $blobRestProxy->getAccountName());
    }
}
