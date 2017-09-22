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
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Samples;

require_once "../vendor/autoload.php";

use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Blob\Models\ListContainersResult;
use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\ContainerACL;
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

$connectionString = 'DefaultEndpointsProtocol=https;AccountName=<yourAccount>;AccountKey=<yourKey>';
$blobClient = ServicesBuilder::getInstance()->createBlobService($connectionString);

// Get and Set Blob Service Properties
setBlobServiceProperties($blobClient);

// To create a container call createContainer.
createContainerSample($blobClient);

// To get/set container properties
containerProperties($blobClient);

// To get/set container metadata
containerMetadata($blobClient);

// To get/set container ACL
containerAcl($blobClient);

// To upload a file as a blob, use the BlobRestProxy->createBlockBlob method. This operation will
// create the blob if it doesn't exist, or overwrite it if it does. The code example below assumes 
// that the container has already been created and uses fopen to open the file as a stream.
uploadBlobSample($blobClient);

// To download blob into a file, use the BlobRestProxy->getBlob method. The example below assumes
// the blob to download has been already created.
downloadBlobSample($blobClient);

//Generate a blob download link with a generated service level SAS token
generateBlobDownloadLinkWithSAS();

// To list the blobs in a container, use the BlobRestProxy->listBlobs method with a foreach loop to loop
// through the result. The following code outputs the name and URI of each blob in a container.
listBlobsSample($blobClient);

// To get set blob properties
blobProperties($blobClient);

// To get set blob metadata
blobMetadata($blobClient);

// Basic operations for page blob.
pageBlobOperations($blobClient);

// Snap shot operation for blob service.
snapshotOperations($blobClient);

// Basic lease operations.
leaseOperations($blobClient);

//Or to leverage the asynchronous methods provided, the operation can be done in
//a promise pipeline.
$containerName = '';
try {
    $containerName = basicStorageBlobOperationAsync($blobClient)->wait();
} catch (ServiceException $e) {
    $code = $e->getCode();
    $error_message = $e->getMessage();
    echo $code.": ".$error_message.PHP_EOL;
} catch (InvalidArgumentTypeException $e) {
    echo $e->getMessage().PHP_EOL;
}

try {
    cleanUp($blobClient, $containerName)->wait();
} catch (ServiceException $e) {
    $code = $e->getCode();
    $error_message = $e->getMessage();
    echo $code.": ".$error_message.PHP_EOL;
}

function setBlobServiceProperties($blobClient)
{
     // Get blob service properties
    echo "Get Blob Service properties" . PHP_EOL;
    $originalProperties = $blobClient->getServiceProperties();
    // Set blob service properties
    echo "Set Blob Service properties" . PHP_EOL;
    $retentionPolicy = new RetentionPolicy();
    $retentionPolicy->setEnabled(true);
    $retentionPolicy->setDays(10);
    
    $logging = new Logging();
    $logging->setRetentionPolicy($retentionPolicy);
    $logging->setVersion('1.0');
    $logging->setDelete(true);
    $logging->setRead(true);
    $logging->setWrite(true);
    
    $metrics = new Metrics();
    $metrics->setRetentionPolicy($retentionPolicy);
    $metrics->setVersion('1.0');
    $metrics->setEnabled(true);
    $metrics->setIncludeAPIs(true);
    $serviceProperties = new ServiceProperties();
    $serviceProperties->setLogging($logging);
    $serviceProperties->setHourMetrics($metrics);
    $blobClient->setServiceProperties($serviceProperties);
    
    // revert back to original properties
    echo "Revert back to original service properties" . PHP_EOL;
    $blobClient->setServiceProperties($originalProperties->getValue());
    echo "Service properties sample completed" . PHP_EOL;
}

function createContainerSample($blobClient)
{
    // OPTIONAL: Set public access policy and metadata.
    // Create container options object.
    $createContainerOptions = new CreateContainerOptions();

    // Set public access policy. Possible values are
    // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
    // CONTAINER_AND_BLOBS: full public read access for container and blob data.
    // BLOBS_ONLY: public read access for blobs. Container data not available.
    // If this value is not specified, container data is private to the account owner.
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

    // Set container metadata
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");

    try {
        // Create container.
        $blobClient->createContainer("mycontainer", $createContainerOptions);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function containerProperties($blobClient)
{
    $containerName = "mycontainer" . generateRandomString();
    
    echo "Create container " . $containerName . PHP_EOL;
    // Create container options object.
    $createContainerOptions = new CreateContainerOptions();
    // Set public access policy. Possible values are
    // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
    // CONTAINER_AND_BLOBS: full public read access for container and blob data.
    // BLOBS_ONLY: public read access for blobs. Container data not available.
    // If this value is not specified, container data is private to the account owner.
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
    // Set container metadata
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");
    // Create container.
    $blobClient->createContainer($containerName, $createContainerOptions);
    echo "Get container properties:" . PHP_EOL;
    // Get container properties
    $properties = $blobClient->getContainerProperties($containerName);
    echo 'Last modified: ' . $properties->getLastModified()->format('Y-m-d H:i:s') . PHP_EOL;
    echo 'ETAG: ' . $properties->getETag() . PHP_EOL;
    echo "Delete container" . PHP_EOL;
    $blobClient->deleteContainer($containerName) . PHP_EOL;
}

function containerMetadata($blobClient)
{
    $containerName = "mycontainer" . generateRandomString();
    
    echo "Create container " . $containerName . PHP_EOL;
    // Create container options object.
    $createContainerOptions = new CreateContainerOptions();
    // Set container metadata
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");
    // Create container.
    $blobClient->createContainer($containerName, $createContainerOptions);
    echo "Get container metadata" . PHP_EOL;
    // Get container properties
    $properties = $blobClient->getContainerProperties($containerName);
    foreach ($properties->getMetadata() as $key => $value) {
        echo $key . ": " . $value . PHP_EOL;
    }
    echo "Delete container" . PHP_EOL;
    $blobClient->deleteContainer($containerName);
}

function containerAcl($blobClient)
{
    // Create container
    $container = "mycontainer" . generateRandomString();
    echo "Create container " . $container . PHP_EOL;
    $blobClient->createContainer($container);
    // Set container ACL
    $past = new \DateTime("01/01/2010");
    $future = new \DateTime("01/01/2020");
    $acl = new ContainerACL();
    $acl->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
    $acl->addSignedIdentifier('123', $past, $future, 'rw');
    $blobClient->setContainerACL($container, $acl);
    // Get container ACL
    echo "Get container access policy" . PHP_EOL;
    $acl = $blobClient->getContainerACL($container);
    echo 'Public access: ' . $acl->getContainerACL()->getPublicAccess() . PHP_EOL;
    echo 'Signed Identifiers: ' . PHP_EOL;
    echo ' Id: ' . $acl->getContainerACL()->getSignedIdentifiers()[0]->getId() . PHP_EOL;
    echo ' Start: '. $acl->getContainerACL()->getSignedIdentifiers()[0]->getAccessPolicy()->getStart()->format('Y-m-d H:i:s') .PHP_EOL;
    echo ' Expiry: '. $acl->getContainerACL()->getSignedIdentifiers()[0]->getAccessPolicy()->getExpiry()->format('Y-m-d H:i:s') .PHP_EOL;
    echo ' Permission: '. $acl->getContainerACL()->getSignedIdentifiers()[0]->getAccessPolicy()->getPermission() .PHP_EOL;
    echo "Delete container" . PHP_EOL;
    $blobClient->deleteContainer($container);
}

function blobProperties($blobClient)
{
    // Create container
    $container = "mycontainer" . generateRandomString();
    echo "Create container " . $container . PHP_EOL;
    $blobClient->createContainer($container);
    // Create blob
    $blob = 'blob' . generateRandomString();
    echo "Create blob " . PHP_EOL;
    $blobClient->createPageBlob($container, $blob, 4096);
    // Set blob properties
    echo "Set blob properties" . PHP_EOL;
    $opts = new SetBlobPropertiesOptions();
    $opts->setCacheControl('test');
    $opts->setContentEncoding('UTF-8');
    $opts->setContentLanguage('en-us');
    $opts->setContentLength(512);
    $opts->setContentMD5(null);
    $opts->setContentType('text/plain');
    $opts->setSequenceNumberAction('increment');
    $blobClient->setBlobProperties($container, $blob, $opts);
    // Get blob properties
    echo "Get blob properties" . PHP_EOL;
    $result = $blobClient->getBlobProperties($container, $blob);
   
    $props = $result->getProperties();
    echo 'Cache control: ' . $props->getCacheControl() . PHP_EOL;
    echo 'Content encoding: ' . $props->getContentEncoding() . PHP_EOL;
    echo 'Content language: ' . $props->getContentLanguage() . PHP_EOL;
    echo 'Content type: ' . $props->getContentType() . PHP_EOL;
    echo 'Content length: ' . $props->getContentLength() . PHP_EOL;
    echo 'Content MD5: ' . $props->getContentMD5() . PHP_EOL;
    echo 'Last modified: ' . $props->getLastModified()->format('Y-m-d H:i:s') . PHP_EOL;
    echo 'Blob type: ' . $props->getBlobType() . PHP_EOL;
    echo 'Lease status: ' . $props->getLeaseStatus() . PHP_EOL;
    echo 'Sequence number: ' . $props->getSequenceNumber() . PHP_EOL;
    echo "Delete blob" . PHP_EOL;
    $blobClient->deleteBlob($container, $blob);
    echo "Delete container" . PHP_EOL;
    $blobClient->deleteContainer($container);
}

function blobMetadata($blobClient)
{
    // Create container
    $container = "mycontainer" . generateRandomString();
    echo "Create container " . $container . PHP_EOL;
    $blobClient->createContainer($container);
    // Create blob
    $blob = 'blob' . generateRandomString();
    echo "Create blob " . PHP_EOL;
    $blobClient->createPageBlob($container, $blob, 4096);
    // Set blob metadata
    echo "Set blob metadata" . PHP_EOL;
    $metadata = array(
        'key' => 'value',
        'foo' => 'bar',
        'baz' => 'boo');
    $blobClient->setBlobMetadata($container, $blob, $metadata);
    // Get blob metadata
    echo "Get blob metadata" . PHP_EOL;
    $result = $blobClient->getBlobMetadata($container, $blob);
    
    $retMetadata = $result->getMetadata();
    foreach ($retMetadata as $key => $value) {
        echo $key . ': ' . $value . PHP_EOL;
    }
    echo "Delete blob" . PHP_EOL;
    $blobClient->deleteBlob($container, $blob);
    echo "Delete container" . PHP_EOL;
    $blobClient->deleteContainer($container);
}

function uploadBlobSample($blobClient)
{
    $content = fopen("myfile.txt", "r");
    $blob_name = "myblob";
    
    try {
        //Upload blob
        $blobClient->createBlockBlob("mycontainer", $blob_name, $content);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function downloadBlobSample($blobClient)
{
    try {
        $getBlobResult = $blobClient->getBlob("mycontainer", "myblob");
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
    
    file_put_contents("output.txt", $getBlobResult->getContentStream());
}

function generateBlobDownloadLinkWithSAS()
{
    global $connectionString;

    $settings = StorageServiceSettings::createFromConnectionString($connectionString);
    $accountName = $settings->getName();
    $accountKey = $settings->getKey();

    $helper = new SharedAccessSignatureHelper(
        $accountName,
        $accountKey
    );

    // Refer to following link for full candidate values to construct a service level SAS
    // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
    $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
        Resources::RESOURCE_TYPE_BLOB,
        'mycontainer/myblob',
        'r',                            // Read
        '2018-01-01T08:30:00Z'//,       // A valid ISO 8601 format expiry time
        //'2016-01-01T08:30:00Z',       // A valid ISO 8601 format expiry time
        //'0.0.0.0-255.255.255.255'
        //'https,http'
    );

    $connectionStringWithSAS = Resources::BLOB_ENDPOINT_NAME .
        '='.
        'https://' .
        $accountName .
        '.' .
        Resources::BLOB_BASE_DNS_NAME .
        ';' .
        Resources::SAS_TOKEN_NAME .
        '=' .
        $sas;

    $blobClientWithSAS = ServicesBuilder::getInstance()->createBlobService(
        $connectionStringWithSAS
    );

    // We can download the blob with PHP Client Library
    // downloadBlobSample($blobClientWithSAS);

    // Or generate a temporary readonly download URL link
    $blobUrlWithSAS = sprintf(
        '%s%s?%s',
        (string)$blobClientWithSAS->getPsrPrimaryUri(),
        'mycontainer/myblob',
        $sas
    );

    file_put_contents("outputBySAS.txt", fopen($blobUrlWithSAS, 'r'));

    return $blobUrlWithSAS;
}


function listBlobsSample($blobClient)
{
    try {
        // List blobs.
        $blob_list = $blobClient->listBlobs("mycontainer");
        $blobs = $blob_list->getBlobs();
    
        foreach ($blobs as $blob) {
            echo $blob->getName().": ".$blob->getUrl().PHP_EOL;
        }
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function basicStorageBlobOperationAsync($blobClient)
{
    // Create the options for creating containers.
    $createContainerOptions = new CreateContainerOptions();

    // Set public access policy. Possible values are
    // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
    // CONTAINER_AND_BLOBS: full public read access for container and blob data.
    // BLOBS_ONLY: public read access for blobs. Container data not available.
    // If this value is not specified, container data is private to the account owner.
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

    // Set container metadata
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");

    // Construct the container name
    $containerName = "mycontainer" . sprintf('-%04x', mt_rand(0, 65535));

    return $blobClient->createContainerAsync(
        $containerName,
        $createContainerOptions
    )->then(
        function ($response) use ($blobClient, $containerName) {
            // Successfully created the container, now upload a blob to the
            // container.
            echo "Container named {$containerName} created.\n";

            $content = fopen("myfile.txt", "r");
            $blob_name = "myblob";
            return $blobClient->createBlockBlobAsync(
                $containerName,
                $blob_name,
                $content
            );
        },
        null
    )->then(
        function ($putBlobResult) use ($blobClient, $containerName) {
            // Successfully created the blob, then download the blob.
            echo "Blob successfully created.\n";
            return $blobClient->saveBlobToFileAsync(
                "output.txt",
                $containerName,
                "myblob"
            );
        },
        null
    )->then(
        function ($getBlobResult) use ($blobClient, $containerName) {
            // Successfully saved the blob, now list the blobs.
            echo "Blob successfully downloaded.\n";
            return $blobClient->listBlobsAsync($containerName);
        },
        null
    )->then(
        function ($listBlobsResult) use ($containerName) {
            // Successfully get the blobs list.
            $blobs = $listBlobsResult->getBlobs();
            foreach ($blobs as $blob) {
                echo $blob->getName().": ".$blob->getUrl().PHP_EOL;
            }
            return $containerName;
        },
        null
    );
}

function pageBlobOperations($blobClient)
{
    $blobName = "HelloPageBlobWorld";
    $containerName = 'mycontainer';
      
    # Create a page blob
    echo "Create Page Blob with name {$blobName}".PHP_EOL;
    $blobClient->createPageBlob($containerName, $blobName, 2560);
    # Create pages in a page blob
    echo "Create pages in a page blob".PHP_EOL;
    
    $blobClient->createBlobPages(
        $containerName,
        $blobName,
        new Range(0, 511),
        generateRandomString(512)
    );
    $blobClient->createBlobPages(
        $containerName,
        $blobName,
        new Range(512, 1023),
        generateRandomString(512)
    );
    # List page blob ranges
    $listPageBlobRangesOptions = new ListPageBlobRangesOptions();
    $listPageBlobRangesOptions->setRange(new Range(0, 1023));
    echo "List Page Blob Ranges".PHP_EOL;
    $listPageBlobRangesResult = $blobClient->listPageBlobRanges(
        $containerName,
        $blobName,
        $listPageBlobRangesOptions
    );
    
    foreach ($listPageBlobRangesResult->getRanges() as $range) {
        echo "Range:".$range->getStart()."-".$range->getEnd().PHP_EOL;
        $getBlobOptions = new GetBlobOptions();
        $getBlobOptions->setRange($range);
        $getBlobResult = $blobClient->getBlob($containerName, $blobName, $getBlobOptions);
        file_put_contents("PageContent.txt", $getBlobResult->getContentStream());
    }
    # Clean up after the sample
      echo "Delete Blob".PHP_EOL;
    $blobClient->deleteBlob($containerName, $blobName);
}

function snapshotOperations($blobClient)
{
    $blobName = "HelloWorld";
    $containerName = 'mycontainer';

    # Upload file as a block blob
    echo "Uploading BlockBlob".PHP_EOL;
      
    $content = 'test content hello hello world';
    $blobClient->createBlockBlob($containerName, $blobName, $content);
    # Create a snapshot
    echo "Create a Snapshot".PHP_EOL;
    $snapshotResult = $blobClient->createBlobSnapshot($containerName, $blobName);
    # Retrieve snapshot
    echo "Retrieve Snapshot".PHP_EOL;
    $getBlobOptions = new GetBlobOptions();
    $getBlobOptions->setSnapshot($snapshotResult->getSnapshot());
    $getBlobResult = $blobClient->getBlob($containerName, $blobName, $getBlobOptions);
    file_put_contents("HelloWorldSnapshotCopy.png", $getBlobResult->getContentStream());
    # Clean up after the sample
    echo "Delete Blob and snapshot".PHP_EOL;
    $deleteBlobOptions = new DeleteBlobOptions();
    $deleteBlobOptions->setDeleteSnaphotsOnly(false);
    $blobClient->deleteBlob($containerName, $blobName, $deleteBlobOptions);
}

function cleanUp($blobClient, $containerName)
{
    return $blobClient->listContainersAsync()->then(
        function ($listContainersResult) use ($blobClient, $containerName) {
            $containerNames = array();
            foreach ($listContainersResult->getContainers() as $container) {
                $containerNames[] = $container->getName();
            }
            if (in_array($containerName, $containerNames)) {
                $blobClient->deleteContainerAsync($containerName)->wait();
            }
            if (in_array('mycontainer', $containerNames)) {
                $blobClient->deleteContainerAsync('mycontainer')->wait();
            }
            if (file_exists('output.txt')) {
                unlink('output.txt');
            }
            if (file_exists('outputBySAS.txt')) {
                unlink('outputBySAS.txt');
            }
            if (file_exists('myblob.txt')) {
                unlink('myblob.txt');
            }
            if (file_exists('PageContent.txt')) {
                unlink('PageContent.txt');
            }
            if (file_exists('HelloWorldSnapshotCopy.png')) {
                unlink('HelloWorldSnapshotCopy.png');
            }
            echo "Successfully cleaned up\n";
            return $blobClient->listContainersAsync();
        },
        null
    );
}

function leaseOperations($blobClient)
{
    // Create container
    $container = "mycontainer" . generateRandomString();
    echo "Create container " . $container . PHP_EOL;
    $blobClient->createContainer($container);
    // Create Blob
    $blob = 'Blob' . generateRandomString();
    echo "Create blob " . $blob . PHP_EOL;
    $contentType = 'text/plain; charset=UTF-8';
    $options = new CreateBlobOptions();
    $options->setContentType($contentType);
    $blobClient->createBlockBlob($container, $blob, 'Hello world', $options);
    
    // Acquire lease
    $result = $blobClient->acquireLease($container, $blob);
    try {
        echo "Try delete blob without lease" . PHP_EOL;
        $blobClient->deleteBlob($container, $blob);
    } catch (ServiceException $e) {
        echo "Delete blob with lease" . PHP_EOL;
        $blobOptions = new DeleteBlobOptions();
        $blobOptions->setLeaseId($result->getLeaseId());
        $blobClient->deleteBlob($container, $blob, $blobOptions);
    }
    echo "Delete container" . PHP_EOL;
    $blobClient->deleteContainer($container);
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
