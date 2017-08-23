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
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Samples;

require_once "../vendor/autoload.php";

use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;
use MicrosoftAzure\Storage\File\Models\CreateShareOptions;
use MicrosoftAzure\Storage\File\Models\ListSharesOptions;

$connectionString = 'DefaultEndpointsProtocol=https;AccountName=<yourAccount>;AccountKey=<yourKey>';
$fileClient = ServicesBuilder::getInstance()->createFileService($connectionString);

//Operations for File service properties;
setFileServiceProperties($fileClient);

//Create share
createShare($fileClient);

//List share
listShare($fileClient);

//Delete share
deleteShare($fileClient);

//Create directory
createDirectory($fileClient);

//Delete directory
deleteDirectory($fileClient);

//Create file
createFile($fileClient);

//Generate a file download link with a generated service level SAS token
generateFileDownloadLinkWithSAS($fileClient);

//Delete file
deleteFile($fileClient);

//List directories and files
listDirectoriesAndFiles($fileClient);

//Put file range
putFileRanges($fileClient);

//Clear file range
clearFileRanges($fileClient);

//List file range
listFileRanges($fileClient);

//Copy file
copyFile($fileClient);

//Clean up
cleanUp($fileClient);

function setFileServiceProperties($fileClient)
{
    $originalProperties = $fileClient->getServiceProperties();
    $retentionPolicy = new RetentionPolicy();
    $retentionPolicy->setEnabled(true);
    $retentionPolicy->setDays(10);
    
    $metrics = new Metrics();
    $metrics->setRetentionPolicy($retentionPolicy);
    $metrics->setVersion('1.0');
    $metrics->setEnabled(true);
    $metrics->setIncludeAPIs(true);
    $serviceProperties = new ServiceProperties();
    $serviceProperties->setHourMetrics($metrics);
    $fileClient->setServiceProperties($serviceProperties);
    // revert back to original properties
    $fileClient->setServiceProperties($originalProperties->getValue());
}

function createShare($fileClient)
{
    // OPTIONAL: Set metadata.
    // Create share options object.
    $createShareOptions = new CreateShareOptions();

    // Set share metadata
    $createShareOptions->addMetaData("key1", "value1");
    $createShareOptions->addMetaData("key2", "value2");

    // Set quota of the share
    $createShareOptions->setQuota(1024);

    $shareName = 'myshare' . generateRandomString();

    try {
        // Create share.
        createShareWorker($fileClient, $shareName, $createShareOptions);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function createShareWorker($fileClient, $name, $options = null)
{
    $error_message = '';
    static $createdShares = array();
    try {
        // Create share.
        $fileClient->createShare($name, $options);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
    if ($error_message === '') {
        $createdShares[] = $name;
        return $createdShares;
    }
}

function listShare($fileClient)
{
    // OPTIONAL: set prefix.
    $listShareOptions = new ListSharesOptions();
    $listShareOptions->setPrefix('myshare');

    $shareName = 'myshare' . generateRandomString();

    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // List share.
        return $fileClient->listShares($listShareOptions);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function deleteShare($fileClient)
{
    $shareName = 'myshare' . generateRandomString();

    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Delete share.
        $fileClient->deleteShare($shareName);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function createDirectory($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $directoryName = 'mydirectory' . generateRandomString();
    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create directory.
        $fileClient->createDirectory($shareName, $directoryName);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function deleteDirectory($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $directoryName = 'mydirectory' . generateRandomString();
    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create directory.
        $fileClient->createDirectory($shareName, $directoryName);
        // Delete directory
        $fileClient->deleteDirectory($shareName, $directoryName);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function createFile($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $fileName = 'myfile' . generateRandomString();
    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create file.
        $fileClient->createFile($shareName, $fileName, 1024);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function generateFileDownloadLinkWithSAS($fileClient)
{
    // Create a file
    $shareName = 'myshare' . generateRandomString();
    $fileName = 'myfile' . generateRandomString();
    $content = generateRandomString(512);
    $range = new Range(0, 511);
    try {
        createShareWorker($fileClient, $shareName);
        $fileClient->createFile($shareName, $fileName, 1024);
        $fileClient->putFileRange($shareName, $fileName, $content, $range);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }

    // Create a SharedAccessSignatureHelper
    global $connectionString;

    $settings = StorageServiceSettings::createFromConnectionString($connectionString);
    $accountName = $settings->getName();
    $accountKey = $settings->getKey();

    $helper = new SharedAccessSignatureHelper(
        $accountName,
        $accountKey
    );

    // Generate a file readonly SAS token
    // Refer to following link for full candidate values to construct a service level SAS
    // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
    $sas = $helper->generateFileServiceSharedAccessSignatureToken(
        Resources::RESOURCE_TYPE_FILE,
        "$shareName/$fileName",
        'r',                        // Read
        '2020-01-01T08:30:00Z'      // A valid ISO 8601 format expiry time
    );

    $connectionStringWithSAS = Resources::FILE_ENDPOINT_NAME .
        '='.
        'https://' .
        $accountName .
        '.' .
        Resources::FILE_BASE_DNS_NAME .
        ';' .
        Resources::SAS_TOKEN_NAME .
        '=' .
        $sas;

    $fileClientWithSAS = ServicesBuilder::getInstance()->createFileService(
        $connectionStringWithSAS
    );

    // Get a downloadable file URL
    $fileUrlWithSAS = sprintf(
        '%s%s?%s',
        (string)$fileClientWithSAS->getPsrPrimaryUri(),
        "$shareName/$fileName",
        $sas
    );

    // Download the file from the URL directly
    $downloadFileName = 'outputBySAS.txt';
    file_put_contents($downloadFileName, fopen($fileUrlWithSAS, 'r'));

    // Clean up
    if (file_exists($downloadFileName)) {
        unlink($downloadFileName);
    }

    // Return the temporary readonly download URL link
    return $fileUrlWithSAS;
}

function deleteFile($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $fileName = 'myfile' . generateRandomString();
    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create file.
        $fileClient->createFile($shareName, $fileName, 1024);
        // Delete file
        $fileClient->deleteFile($shareName, $fileName);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function listDirectoriesAndFiles($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $directoryName = 'mydirectory' . generateRandomString();
    $fileName = 'myfile' . generateRandomString();
    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create file.
        $fileClient->createFile($shareName, $fileName, 1024);
        // Create directory.
        $fileClient->createDirectory($shareName, $directoryName);
        // List directories and files.
        return $fileClient->listDirectoriesAndFiles($shareName);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function putFileRanges($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $fileName = 'myfile' . generateRandomString();
    $content = generateRandomString(512);
    $range = new Range(0, 511);
    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create file.
        $fileClient->createFile($shareName, $fileName, 1024);
        // Put ranges.
        $fileClient->putFileRange($shareName, $fileName, $content, $range);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function clearFileRanges($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $fileName = 'myfile' . generateRandomString();
    $content = generateRandomString(512);
    $range = new Range(0, 511);
    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create file.
        $fileClient->createFile($shareName, $fileName, 1024);
        // Put ranges.
        $fileClient->putFileRange($shareName, $fileName, $content, $range);
        // Clear ranges.
        $fileClient->clearFileRange($shareName, $fileName, $range);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function listFileRanges($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $fileName = 'myfile' . generateRandomString();
    $content = generateRandomString(512);
    $range = new Range(0, 511);
    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create file.
        $fileClient->createFile($shareName, $fileName, 1024);
        // Put ranges.
        $fileClient->putFileRange($shareName, $fileName, $content, $range);
        // List ranges.
        return $fileClient->listFileRange($shareName, $fileName);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function copyFile($fileClient)
{
    $shareName = 'myshare' . generateRandomString();
    $srcfileName = 'myfile' . generateRandomString();
    $dstfileName = 'myfile' . generateRandomString();
    $content = generateRandomString(512);
    $range = new Range(0, 511);

    $sourcePath = sprintf(
        '%s%s/%s',
        (string)$fileClient->getPsrPrimaryUri(),
        $shareName,
        $srcfileName
    );

    try {
        // Create share.
        createShareWorker($fileClient, $shareName);
        // Create source file.
        $fileClient->createFile($shareName, $srcfileName, 1024);
        // Create destination file.
        $fileClient->createFile($shareName, $dstfileName, 1024);
        // Put ranges.
        $fileClient->putFileRange($shareName, $srcfileName, $content, $range);
        // Copy file.
        return $fileClient->copyFile($shareName, $dstfileName, $sourcePath);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code . ": " . $error_message . PHP_EOL;
    }
}

function cleanUp($fileClient)
{
    //get created shares
    $createdShares =
        createShareWorker($fileClient, 'myshare' . generateRandomString());
    foreach ($createdShares as $share) {
        $fileClient->deleteShare($share);
    }
}

function generateRandomString($length = 6)
{
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
