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
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Samples
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2019 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

require_once "./vendor/autoload.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\File\FileRestProxy;
use MicrosoftAzure\Storage\Table\TableRestProxy;
use MicrosoftAzure\Storage\Queue\QueueRestProxy;

$connectionString = \getenv("AZURE_STORAGE_CONNECTION_STRING");
$blobClient = BlobRestProxy::createBlobService($connectionString);
$queueClient = QueueRestProxy::createQueueService($connectionString);
$fileClient = FileRestProxy::createFileService($connectionString);
$tableClient = TableRestProxy::createTableService($connectionString);

//clean up containers
$result = $blobClient->listContainers();

foreach ($result->getContainers() as $container) {
    $blobClient->deleteContainer($container->getName());
}

//clean up queues
$result = $queueClient->listQueues();
foreach ($result->getQueues() as $queue) {
    $queueClient->deleteQueue($queue->getName());
}

//clean up fileshares
$result = $fileClient->listShares();
foreach ($result->getShares() as $share) {
    $fileClient->deleteShare($share->getName());
}

//clean up tables
$result = $tableClient->queryTables();
foreach ($result->getTables() as $table) {
    $tableClient->deleteTable($table);
}

//clean up premium blobs
$connectionString = \getenv("AZURE_STORAGE_CONNECTION_STRING_PREMIUM_ACCOUNT");

if (!empty($connectionString)) {
    $blobClient = BlobRestProxy::createBlobService($connectionString);

    //clean up containers
    $result = $blobClient->listContainers();

    foreach ($result->getContainers() as $container) {
        $blobClient->deleteContainer($container->getName());
    }
}


//clean up blob storage blobs
$connectionString = \getenv("AZURE_STORAGE_CONNECTION_STRING_BLOB_ACCOUNT");

if (!empty($connectionString)) {
    $blobClient = BlobRestProxy::createBlobService($connectionString);

    //clean up containers
    $result = $blobClient->listContainers();

    foreach ($result->getContainers() as $container) {
        $blobClient->deleteContainer($container->getName());
    }
}