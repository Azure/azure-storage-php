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
    /**
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::createQueueService
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::serializer
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::queueAuthenticationScheme
     */
    public function testBuildForQueue()
    {
        // Setup
        $builder = new ServicesBuilder();

        // Test
        $queueRestProxy = $builder->createQueueService(TestResources::getWindowsAzureStorageServicesConnectionString());

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Queue\Internal\IQueue', $queueRestProxy);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::createBlobService
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::serializer
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::blobAuthenticationScheme
     */
    public function testBuildForBlob()
    {
        // Setup
        $builder = new ServicesBuilder();

        // Test
        $blobRestProxy = $builder->createBlobService(TestResources::getWindowsAzureStorageServicesConnectionString());

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Blob\Internal\IBlob', $blobRestProxy);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::createTableService
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::serializer
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::mimeSerializer
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::odataSerializer
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::tableAuthenticationScheme
     */
    public function testBuildForTable()
    {
        // Setup
        $builder = new ServicesBuilder();

        // Test
        $tableRestProxy = $builder->createTableService(TestResources::getWindowsAzureStorageServicesConnectionString());

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Table\Internal\ITable', $tableRestProxy);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::createFileService
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::serializer
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::fileAuthenticationScheme
     */
    public function testBuildForFile()
    {
        // Setup
        $builder = new ServicesBuilder();

        // Test
        $fileRestProxy = $builder->createFileService(TestResources::getWindowsAzureStorageServicesConnectionString());

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\File\Internal\IFile', $fileRestProxy);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::getInstance
     */
    public function testGetInstance()
    {
        // Test
        $actual = ServicesBuilder::getInstance();

        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Common\ServicesBuilder', $actual);
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\ServicesBuilder::createContainerAnonymousAccess
     */
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
