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
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Framework;

use MicrosoftAzure\Storage\File\FileRestProxy;
use MicrosoftAzure\Storage\Tests\Framework\ServiceRestProxyTestBase;
use MicrosoftAzure\Storage\File\Models\CreateShareOptions;
use MicrosoftAzure\Storage\File\Models\CreateDirectoryOptions;
use MicrosoftAzure\Storage\File\Models\ListSharesOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * TestBase class for each unit test class.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class FileServiceRestProxyTestBase extends ServiceRestProxyTestBase
{
    protected $createdShares;
    protected $createdDirectories;

    public function setUp()
    {
        parent::setUp();
        $fileRestProxy = FileRestProxy::createFileService($this->connectionString);
        parent::setProxy($fileRestProxy);
        $this->createdShares = array();
        $this->createdDirectories = array();
    }

    public function createShare($shareName, $options = null)
    {
        if (is_null($options)) {
            $options = new CreateShareOptions();
        }

        $this->restProxy->createShare($shareName, $options);
        $this->createdShares[] = $shareName;
    }

    public function createDirectory($shareName, $path, $options = null)
    {
        if (is_null($options)) {
            $options = new CreateDirectoryOptions();
        }

        $this->restProxy->createDirectory($shareName, $path, $options);

        $this->createdDirectories[] = [$shareName, $path];
    }

    public function createShareWithRetry(
        $shareName,
        $options = null,
        $retryCount = 6
    ) {
        // Shares cannot be recreated within a minute of them being
        // deleted; the service will give response of 409:Conflict.
        // So, if get that error, wait a bit then retry.

        $ok = false;
        $counter = 0;
        do {
            try {
                $this->createShare($shareName, $options);
                $ok = true;
            } catch (ServiceException $e) {
                if ($e->getCode() != TestResources::STATUS_CONFLICT ||
                        $counter > $retryCount) {
                    throw $e;
                }
                sleep(10);
                $counter++;
            }
        } while (!$ok);
    }

    public function createShares($shareList, $sharePrefix = null)
    {
        $shares = $this->listShares($sharePrefix);
        foreach ($shareList as $share) {
            if (array_search($share, $shares) === false) {
                $this->createShare($share);
            } else {
                $listResults = $this->restProxy->listFiles($share);
                $blobs = $listResults->getFiles();
                foreach ($blobs as $blob) {
                    try {
                        $this->restProxy->deleteFile($share, $blob->getName());
                    } catch (\Exception $e) {
                        // Ignore exception and continue.
                        error_log($e->getMessage());
                    }
                }
            }
        }
    }

    public function deleteShare($shareName)
    {
        if (($key = array_search($shareName, $this->createdShares)) !== false) {
            unset($this->createdShares[$key]);
        }
        $this->restProxy->deleteShare($shareName);
    }

    public function deleteDirectory($shareName, $path)
    {
        if (($key = array_search([$shareName, $path], $this->createdDirectories)) !== false) {
            unset($this->createdDirectories[$key]);
        }
        $this->restProxy->deleteDirectory($shareName, $path);
    }

    public function deleteShares($shareList, $sharePrefix = null)
    {
        $shares = $this->listShares($sharePrefix);
        foreach ($shareList as $share) {
            if ((array_search($share, $shares) === true)) {
                $this->deleteShare($share);
            }
        }
    }

    public function listShares($sharePrefix = null)
    {
        $result = array();
        $opts = new ListSharesOptions();
        if (!is_null($sharePrefix)) {
            $opts->setPrefix($sharePrefix);
        }

        $list = $this->restProxy->listShares($opts);
        foreach ($list->getShares() as $item) {
            array_push($result, $item->getName());
        }

        return $result;
    }

    protected function tearDown()
    {
        parent::tearDown();

        foreach ($this->createdShares as $value) {
            try {
                $this->deleteShare($value);
            } catch (\Exception $e) {
                // Ignore exception and continue, will assume that this share doesn't exist in the sotrage account
                error_log($e->getMessage());
            }
        }

        $reverseDirectories = array_reverse($this->createdDirectories);
        foreach ($reverseDirectories as $value) {
            try {
                $this->deleteDirectory($value[0], $value[1]);
            } catch (\Exception $e) {
                // Ignore exception and continue, will assume that this share doesn't exist in the sotrage account
                error_log($e->getMessage());
            }
        }
    }
}
