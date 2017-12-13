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
 * @package   MicrosoftAzure\Storage\Tests\Functional\File
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\File;

use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;

class FunctionalTestBase extends IntegrationTestBase
{
    private static $isOneTimeSetup = false;

    public function setUp()
    {
        parent::setUp();
        $settings = StorageServiceSettings::createFromConnectionString($this->connectionString);
        $accountName = $settings->getFileEndpointUri();
        $firstSlash = strpos($accountName, '/');
        $accountName = substr($accountName, $firstSlash + 2);
        $firstDot = strpos($accountName, '.');
        $accountName = substr($accountName, 0, $firstDot);

        FileServiceFunctionalTestData::setupData($accountName);

        foreach (FileServiceFunctionalTestData::$testShareNames as $name) {
            $this->safeCreateShare($name);
        }

        FileServiceFunctionalTestData::$trackedShareCount =
            \count($this->listShares(FileServiceFunctionalTestData::$testUniqueId));

        if (!self::$isOneTimeSetup) {
            self::$isOneTimeSetup = true;
        }
    }

    public function tearDown()
    {
        foreach (FileServiceFunctionalTestData::$testShareNames as $name) {
            $this->safeDeleteShare($name);
        }
        parent::tearDown();
    }

    protected function safeDeleteShare($name)
    {
        try {
            $this->deleteShare($name);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    protected function safeCreateShare($name)
    {
        try {
            $this->createShare($name);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }
}
