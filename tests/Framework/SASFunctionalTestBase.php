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
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Tests\Framework;

use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

/**
 * Test base for SAS functional tests.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class SASFunctionalTestBase extends \PHPUnit_Framework_TestCase
{
    protected $connectionString;
    protected $xmlSerializer;
    protected $builder;
    protected $serviceSettings;
    protected $createdContainer;
    protected $createdTable;
    protected $createdQueue;
    protected $createdShare;
    protected $blobRestProxy;
    protected $tableRestProxy;
    protected $queueRestProxy;
    protected $fileRestProxy;

    public function __construct()
    {
        $this->xmlSerializer = new XmlSerializer();
        $this->builder = new ServicesBuilder();
        $this->connectionString = TestResources::getWindowsAzureStorageServicesConnectionString();
        $this->serviceSettings =
            StorageServiceSettings::createFromConnectionString(
                $this->connectionString
            );
    }

    protected function setUp()
    {
        parent::setUp();
        $this->createdContainer = array();
        $this->createdTable     = array();
        $this->createdQueue     = array();
        $this->createdShare     = array();
        $this->blobRestProxy    = null;
        $this->tableRestProxy   = null;
        $this->queueRestProxy   = null;
        $this->fileRestProxy    = null;
    }

    protected function setUpWithConnectionString($connectionString)
    {
        $this->blobRestProxy  =
            $this->builder->createBlobService($connectionString);
        $this->queueRestProxy =
            $this->builder->createQueueService($connectionString);
        $this->tableRestProxy =
            $this->builder->createTableService($connectionString);
        $this->fileRestProxy =
            $this->builder->createFileService($connectionString);
    }

    protected function tearDown()
    {
        $this->blobRestProxy  =
            $this->builder->createBlobService($this->connectionString);
        $this->queueRestProxy =
            $this->builder->createQueueService($this->connectionString);
        $this->tableRestProxy =
            $this->builder->createTableService($this->connectionString);
        $this->fileRestProxy =
            $this->builder->createFileService($this->connectionString);

        foreach ($this->createdContainer as $container) {
            $this->safeDeleteContainer($container);
        }
        foreach ($this->createdTable as $table) {
            $this->safeDeleteTable($table);
        }
        foreach ($this->createdQueue as $queue) {
            $this->safeDeleteQueue($queue);
        }
        foreach ($this->createdShare as $share) {
            $this->safeDeleteShare($share);
        }

        $this->blobRestProxy    = null;
        $this->tableRestProxy   = null;
        $this->queueRestProxy   = null;
        $this->fileRestProxy    = null;
    }

    protected function initializeProxiesWithSASandAccountName($sas, $accountName)
    {
        $connectionString = Resources::BLOB_ENDPOINT_NAME .
                             '='.
                             'https://' .
                             $accountName .
                             '.' .
                             Resources::BLOB_BASE_DNS_NAME .
                             ';';
        $connectionString .= Resources::QUEUE_ENDPOINT_NAME .
                             '='.
                             'https://' .
                             $accountName .
                             '.' .
                             Resources::QUEUE_BASE_DNS_NAME .
                             ';';
        $connectionString .= Resources::TABLE_ENDPOINT_NAME .
                             '='.
                             'https://' .
                             $accountName .
                             '.' .
                             Resources::TABLE_BASE_DNS_NAME .
                             ';';
        $connectionString .= Resources::FILE_ENDPOINT_NAME .
                             '='.
                             'https://' .
                             $accountName .
                             '.' .
                             Resources::FILE_BASE_DNS_NAME .
                             ';';
        $connectionString .= Resources::SAS_TOKEN_NAME .
                             '='.
                             $sas;

        $this->setUpWithConnectionString($connectionString);
    }

    protected function createProxyWithSAS($sas, $accountName, $signedResource)
    {
        $connectionString = Resources::SAS_TOKEN_NAME .
                             '='.
                             $sas;
        switch ($signedResource) {
            case Resources::RESOURCE_TYPE_BLOB:
            case Resources::RESOURCE_TYPE_CONTAINER:
                $connectionString = Resources::BLOB_ENDPOINT_NAME .
                                    '='.
                                    'https://' .
                                    $accountName .
                                    '.' .
                                    Resources::BLOB_BASE_DNS_NAME .
                                    ';' .
                                    $connectionString;
                return $this->builder->createBlobService($connectionString);
                break;
            case Resources::RESOURCE_TYPE_QUEUE:
                $connectionString = Resources::QUEUE_ENDPOINT_NAME .
                                    '='.
                                    'https://' .
                                    $accountName .
                                    '.' .
                                    Resources::QUEUE_BASE_DNS_NAME .
                                    ';' .
                                    $connectionString;
                return $this->builder->createQueueService($connectionString);
                break;
            case Resources::RESOURCE_TYPE_TABLE:
                $connectionString = Resources::TABLE_ENDPOINT_NAME .
                                    '='.
                                    'https://' .
                                    $accountName .
                                    '.' .
                                    Resources::TABLE_BASE_DNS_NAME .
                                    ';' .
                                    $connectionString;
                return $this->builder->createTableService($connectionString);
                break;
            case Resources::RESOURCE_TYPE_FILE:
            case Resources::RESOURCE_TYPE_SHARE:
                $connectionString = Resources::FILE_ENDPOINT_NAME .
                                    '='.
                                    'https://' .
                                    $accountName .
                                    '.' .
                                    Resources::FILE_BASE_DNS_NAME .
                                    ';' .
                                    $connectionString;
                return $this->builder->createFileService($connectionString);
                break;
            default:
                $this->assertTrue(false);// Given signed resource not valid.
                break;
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
     */
    protected function safeDeleteContainer($name)
    {
        try {
            $this->blobRestProxy->deleteContainer($name);
            $this->createdContainer = array_diff($this->createdContainer, [$name]);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     */
    protected function safeCreateContainer($name)
    {
        try {
            $this->blobRestProxy->createContainer($name);
            $this->createdContainer[] = $name;
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::deleteQueue
     */
    protected function safeDeleteQueue($name)
    {
        try {
            $this->queueRestProxy->deleteQueue($name);
            $this->createdQueue = array_diff($this->createdQueue, [$name]);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Queue\QueueRestProxy::createQueue
     */
    protected function safeCreateQueue($name)
    {
        try {
            $this->queueRestProxy->createQueue($name);
            $this->createdQueue[] = $name;
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::deleteTable
     */
    protected function safeDeleteTable($name)
    {
        try {
            $this->tableRestProxy->deleteTable($name);
            $this->createdTable = array_diff($this->createdTable, [$name]);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Table\TableRestProxy::createTable
     */
    protected function safeCreateTable($name)
    {
        try {
            $this->tableRestProxy->createTable($name);
            $this->createdTable[] = $name;
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     */
    protected function safeDeleteShare($name)
    {
        try {
            $this->fileRestProxy->deleteShare($name);
            $this->createdShare = array_diff($this->createdShare, [$name]);
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\File\FileRestProxy::createShare
     */
    protected function safeCreateShare($name)
    {
        try {
            $this->fileRestProxy->createShare($name);
            $this->createdShare[] = $name;
        } catch (ServiceException $e) {
            error_log($e->getMessage());
        }
    }

    protected function validateServiceExceptionErrorMessage(
        $errorMsg,
        callable $callable,
        $failureMessage = ''
    ) {
        $message = '';
        try {
            call_user_func($callable);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains($errorMsg, $message, $failureMessage);
    }
}
