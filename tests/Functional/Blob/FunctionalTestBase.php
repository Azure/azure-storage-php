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
 * @package   MicrosoftAzure\Storage\Tests\Functional\Blob
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\Blob;

use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;

class FunctionalTestBase extends IntegrationTestBase
{
    private static $isOneTimeSetup = false;

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
     */
    public function setUp()
    {
        parent::setUp();
        $settings = StorageServiceSettings::createFromConnectionString($this->connectionString);
        $accountName = $settings->getBlobEndpointUri();
        $firstSlash = strpos($accountName, '/');
        $accountName = substr($accountName, $firstSlash + 2);
        $firstDot = strpos($accountName, '.');
        $accountName = substr($accountName, 0, $firstDot);

        BlobServiceFunctionalTestData::setupData($accountName);

        $hasRoot = false;
        foreach ($this->restProxy->listContainers()->getContainers() as $container) {
            if ($container->getName() == '$root') {
                $hasRoot = true;
                $this->safeDeleteContainerContents('$root');
            } else {
                $this->safeDeleteContainer($container->getName());
            }
        }

        foreach (BlobServiceFunctionalTestData::$testContainerNames as $name) {
            $this->safeCreateContainer($name);
        }

        if (!$hasRoot) {
            $this->safeCreateContainer('$root');
        }

        if (!self::$isOneTimeSetup) {
            self::$isOneTimeSetup = true;
        }
    }

    public function tearDown()
    {
        foreach (BlobServiceFunctionalTestData::$testContainerNames as $name) {
            $this->safeDeleteContainer($name);
        }
        parent::tearDown();
    }

    public static function tearDownAfterClass()
    {
        if (self::$isOneTimeSetup) {
            $tmp = new FunctionalTestBase();
            $tmp->setUp();
            $tmp->safeDeleteContainer('$root');
            self::$isOneTimeSetup = false;
        }
        parent::tearDownAfterClass();
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
     */
    private function safeDeleteContainerContents($name)
    {
        $blobListResult = $this->restProxy->listBlobs($name);
        foreach ($blobListResult->getBlobs() as $blob) {
            try {
                $this->restProxy->deleteBlob($name, $blob->getName());
            } catch (ServiceException $e) {
                error_log($e->getMessage());
            }
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
     */
    private function safeDeleteContainer($name)
    {
        try {
            $this->restProxy->deleteContainer($name);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     */
    private function safeCreateContainer($name)
    {
        try {
            $this->restProxy->createContainer($name);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }
}
