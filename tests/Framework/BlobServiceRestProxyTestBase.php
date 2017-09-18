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

use MicrosoftAzure\Storage\Blob\Models\Container;
use MicrosoftAzure\Storage\Tests\Framework\ServiceRestProxyTestBase;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\ListContainersOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

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
class BlobServiceRestProxyTestBase extends ServiceRestProxyTestBase
{
    protected $_createdContainers;
    
    public function setUp()
    {
        parent::setUp();
        $blobRestProxy = $this->builder->createBlobService($this->connectionString);
        parent::setProxy($blobRestProxy);
        $this->_createdContainers = array();
    }

    public function createContainer($containerName, $options = null)
    {
        if (is_null($options)) {
            $options = new CreateContainerOptions();
            $options->setPublicAccess('container');
        }

        $this->restProxy->createContainer($containerName, $options);
        $this->_createdContainers[] = $containerName;
    }

    public function createContainerWithRetry($containerName, $options = null, $retryCount = 6)
    {
        // Containers cannot be recreated within a minute of them being
        // deleted; the service will give response of 409:Conflict.
        // So, if get that error, wait a bit then retry.

        $ok = false;
        $counter = 0;
        do {
            try {
                $this->createContainer($containerName, $options);
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

    public function createContainers($containerList, $containerPrefix = null)
    {
        $containers = $this->listContainers($containerPrefix);
        foreach ($containerList as $container) {
            if (array_search($container, $containers) === false) {
                $this->createContainer($container);
            } else {
                $listResults = $this->restProxy->listBlobs($container);
                $blobs = $listResults->getBlobs();
                foreach ($blobs as $blob) {
                    try {
                        $this->restProxy->deleteBlob($container, $blob->getName());
                    } catch (\Exception $e) {
                        // Ignore exception and continue.
                        error_log($e->getMessage());
                    }
                }
            }
        }
    }

    public function deleteAllStorageContainers()
    {
        $this->deleteContainers($this->listContainers());
    }

    public function existInContainerArray($containerName, $containers)
    {
        foreach ($containers as $container) {
            if ($container->getName() === $containerName) {
                return true;
            }
        }
        return false;
    }

    public function deleteContainer($containerName)
    {
        if (($key = array_search($containerName, $this->_createdContainers)) !== false) {
            unset($this->_createdContainers[$key]);
        }
        $this->restProxy->deleteContainer($containerName);
    }

    public function deleteContainers($containerList, $containerPrefix = null)
    {
        $containers = $this->listContainers($containerPrefix);
        foreach ($containerList as $container) {
            if (in_array($container, $containers)) {
                $this->deleteContainer($container);
            }
        }
    }

    public function listContainers($containerPrefix = null)
    {
        $result = array();
        $opts = new ListContainersOptions();
        if (!is_null($containerPrefix)) {
            $opts->setPrefix($containerPrefix);
        }

        $list = $this->restProxy->listContainers($opts);
        foreach ($list->getContainers() as $item) {
            array_push($result, $item->getName());
        }

        return $result;
    }

    protected function tearDown()
    {
        parent::tearDown();

        foreach ($this->_createdContainers as $value) {
            try {
                $this->deleteContainer($value);
            } catch (\Exception $e) {
                // Ignore exception and continue, will assume that this container doesn't exist in the sotrage account
                error_log($e->getMessage());
            }
        }
    }
}
