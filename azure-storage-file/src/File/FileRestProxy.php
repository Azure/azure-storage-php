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
 * @package   MicrosoftAzure\Storage\File
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\File;

use MicrosoftAzure\Storage\Common\Internal\Authentication\SharedAccessSignatureAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\Authentication\SharedKeyAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\Middlewares\CommonRequestMiddleware;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\File\Internal\FileResources as Resources;
use MicrosoftAzure\Storage\File\Internal\IFile;
use MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy;
use MicrosoftAzure\Storage\Common\Internal\ServiceRestTrait;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\LocationMode;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\File\Models\CreateFileFromContentOptions;
use MicrosoftAzure\Storage\File\Models\ShareACL;
use MicrosoftAzure\Storage\File\Models\ListSharesOptions;
use MicrosoftAzure\Storage\File\Models\ListSharesResult;
use MicrosoftAzure\Storage\File\Models\CreateShareOptions;
use MicrosoftAzure\Storage\File\Models\CreateDirectoryOptions;
use MicrosoftAzure\Storage\File\Models\FileServiceOptions;
use MicrosoftAzure\Storage\File\Models\GetShareACLResult;
use MicrosoftAzure\Storage\File\Models\GetSharePropertiesResult;
use MicrosoftAzure\Storage\File\Models\GetShareStatsResult;
use MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesOptions;
use MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult;
use MicrosoftAzure\Storage\File\Models\GetDirectoryPropertiesResult;
use MicrosoftAzure\Storage\File\Models\GetDirectoryMetadataResult;
use MicrosoftAzure\Storage\File\Models\GetFileMetadataResult;
use MicrosoftAzure\Storage\File\Models\CreateFileOptions;
use MicrosoftAzure\Storage\File\Models\FileProperties;
use MicrosoftAzure\Storage\File\Models\PutFileRangeOptions;
use MicrosoftAzure\Storage\File\Models\GetFileOptions;
use MicrosoftAzure\Storage\File\Models\GetFileResult;
use MicrosoftAzure\Storage\File\Models\ListFileRangesResult;
use MicrosoftAzure\Storage\File\Models\CopyFileResult;
use MicrosoftAzure\Storage\Common\Internal\Http\HttpFormatter;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7;

/**
 * This class constructs HTTP requests and receive HTTP responses for File
 * service layer.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class FileRestProxy extends ServiceRestProxy implements IFile
{
    use ServiceRestTrait;

    /**
     * Builds a file service object, it accepts the following
     * options:
     *
     * - http: (array) the underlying guzzle options. refer to
     *   http://docs.guzzlephp.org/en/latest/request-options.html for detailed available options
     * - middlewares: (mixed) the middleware should be either an instance of a sub-class that
     *   implements {@see MicrosoftAzure\Storage\Common\Middlewares\IMiddleware}, or a
     *   `callable` that follows the Guzzle middleware implementation convention
     *
     * Please refer to
     * https://azure.microsoft.com/en-us/documentation/articles/storage-configure-connection-string
     * for how to construct a connection string with storage account name/key, or with a shared
     * access signature (SAS Token).
     *
     * @param string $connectionString The configuration connection string.
     * @param array  $options          Array of options to pass to the service
     * @return FileRestProxy
     */
    public static function createFileService(
        $connectionString,
        array $options = []
    ) {
        $settings = StorageServiceSettings::createFromConnectionString(
            $connectionString
        );

        $primaryUri = Utilities::tryAddUrlScheme(
            $settings->getFileEndpointUri()
        );

        $secondaryUri = Utilities::tryAddUrlScheme(
            $settings->getFileSecondaryEndpointUri()
        );

        $fileWrapper = new FileRestProxy(
            $primaryUri,
            $secondaryUri,
            $settings->getName(),
            $options
        );

        // Getting authentication scheme
        if ($settings->hasSasToken()) {
            $authScheme = new SharedAccessSignatureAuthScheme(
                $settings->getSasToken()
            );
        } else {
            $authScheme = new SharedKeyAuthScheme(
                $settings->getName(),
                $settings->getKey()
            );
        }

        // Adding common request middleware
        $commonRequestMiddleware = new CommonRequestMiddleware(
            $authScheme,
            Resources::STORAGE_API_LATEST_VERSION,
            Resources::FILE_SDK_VERSION
        );
        $fileWrapper->pushMiddleware($commonRequestMiddleware);

        return $fileWrapper;
    }

    /**
     * Creates URI path for file or share.
     *
     * @param string $share      The share name.
     * @param string $directory  The directory name.
     *
     * @return string
     */
    private function createPath($share, $directory = '')
    {
        if (empty($directory)) {
            if (!empty($share)) {
                return $share;
            } else {
                return '/' . $share;
            }
        } else {
            $encodedFile = urlencode($directory);
            // Unencode the forward slashes to match what the server expects.
            $encodedFile = str_replace('%2F', '/', $encodedFile);
            // Unencode the backward slashes to match what the server expects.
            $encodedFile = str_replace('%5C', '/', $encodedFile);
            // Re-encode the spaces (encoded as space) to the % encoding.
            $encodedFile = str_replace('+', '%20', $encodedFile);
            // Empty share means accessing default share
            if (empty($share)) {
                return $encodedFile;
            } else {
                return '/' . $share . '/' . $encodedFile;
            }
        }
    }

    /**
     * Helper method to create promise for getShareProperties API call.
     *
     * @param string             $share     The share name.
     * @param FileServiceOptions $options   The optional parameters.
     * @param string             $operation The operation string. Should be
     *                                      'metadata' to set metadata,
     *                                      and 'properties' to set
     *                                      properties.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    private function getSharePropertiesAsyncImpl(
        $share,
        FileServiceOptions $options = null,
        $operation = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::isTrue(
            $operation == 'properties' || $operation == 'metadata',
            Resources::FILE_SHARE_PROPERTIES_OPERATION_INVALID
        );

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->createPath($share);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'share'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        if ($operation == 'metadata') {
            $this->addOptionalQueryParam(
                $queryParams,
                Resources::QP_COMP,
                $operation
            );
        }

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) {
            $responseHeaders = HttpFormatter::formatHeaders($response->getHeaders());
            return GetSharePropertiesResult::create($responseHeaders);
        }, null);
    }

    /**
     * Helper method to create promise for setShareProperties API call.
     *
     * @param string             $share      The share name.
     * @param array              $properties The array that contains
     *                                       either the properties or
     *                                       the metadata to be set.
     * @param FileServiceOptions $options    The optional parameters.
     * @param string             $operation  The operation string. Should be
     *                                       'metadata' to set metadata,
     *                                       and 'properties' to set
     *                                       properties.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    private function setSharePropertiesAsyncImpl(
        $share,
        array $properties,
        FileServiceOptions $options = null,
        $operation = 'properties'
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::isTrue(
            $operation == 'properties' || $operation == 'metadata',
            Resources::FILE_SHARE_PROPERTIES_OPERATION_INVALID
        );
        Validate::canCastAsString($share, 'share');

        $headers = array();
        if ($operation == 'properties') {
            $headers[Resources::X_MS_SHARE_QUOTA] =
                $properties[Resources::X_MS_SHARE_QUOTA];
        } else {
            Utilities::validateMetadata($properties);
            $headers = $this->generateMetadataHeaders($properties);
        }

        $method      = Resources::HTTP_PUT;
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'share'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            $operation
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Creates promise to write range of bytes (more than 4MB) to a file.
     *
     * @param  string                   $share   The share name.
     * @param  string                   $path    The path of the file.
     * @param  StreamInterface          $content The content to be uploaded.
     * @param  Range                    $range   The range in the file to be put.
     *                                           4MB length min.
     * @param  PutFileRangeOptions|null $options The optional parameters.
     * @param  boolean                  $useTransactionalMD5
     *                                           Optional. Whether enable transactional
     *                                           MD5 validation during uploading.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/put-range
     *
     */
    private function multiplePutRangeConcurrentAsync(
        $share,
        $path,
        $content,
        Range $range,
        PutFileRangeOptions $options = null,
        $useTransactionalMD5 = false
    ) {
        $queryParams  = array();
        $headers      = array();
        $path         = $this->createPath($share, $path);
        $selfInstance = $this;

        if ($options == null) {
            $options = new PutFileRangeOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'range'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_WRITE,
            'Update'
        );

        $counter = 0;
        //create the generator for requests.
        $generator = function () use (
            $headers,
            $path,
            $content,
            &$counter,
            $queryParams,
            $range,
            $useTransactionalMD5,
            $selfInstance
        ) {
            $size = 0;
            $chunkContent = '';
            $start = 0;

            do {
                $start = $range->getStart() + (Resources::MB_IN_BYTES_4 * $counter++);
                $end = $range->getEnd();
                if ($end != null && $start >= $end) {
                    return null;
                }
                $chunkContent = $content->read(Resources::MB_IN_BYTES_4);
                $size = strlen($chunkContent);
                if ($size == 0) {
                    return null;
                }
            } while (Utilities::allZero($chunkContent));

            $chunkRange = new Range($start);
            $chunkRange->setLength($size);

            $selfInstance->addOptionalHeader(
                $headers,
                Resources::X_MS_RANGE,
                $chunkRange->getRangeString()
            );

            $this->addOptionalHeader(
                $headers,
                Resources::CONTENT_LENGTH,
                $size
            );

            if ($useTransactionalMD5) {
                $contentMD5 = base64_encode(md5($chunkContent, true));
                $this->addOptionalHeader(
                    $headers,
                    Resources::CONTENT_MD5,
                    $contentMD5
                );
            }

            return $selfInstance->createRequest(
                Resources::HTTP_PUT,
                $headers,
                $queryParams,
                array(),
                $path,
                LocationMode::PRIMARY_ONLY,
                $chunkContent
            );
        };

        return $this->sendConcurrentAsync(
            $generator,
            Resources::STATUS_CREATED,
            $options
        );
    }


    /**
     * Returns a list of the shares under the specified account
     *
     * @param  ListSharesOptions|null $options The optional parameters
     *
     * @return ListSharesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-shares
     */
    public function listShares(ListSharesOptions $options = null)
    {
        return $this->listSharesAsync($options)->wait();
    }

    /**
     * Create a promise to return a list of the shares under the specified account
     *
     * @param  ListSharesOptions|null $options The optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-shares
     */
    public function listSharesAsync(ListSharesOptions $options = null)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = Resources::EMPTY_STRING;

        if (is_null($options)) {
            $options = new ListSharesOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'list'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_PREFIX,
            $options->getPrefix()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MARKER,
            $options->getNextMarker()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MAX_RESULTS,
            $options->getMaxResults()
        );
        $isInclude = $options->getIncludeMetadata();
        $isInclude = $isInclude ? 'metadata' : null;
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_INCLUDE,
            $isInclude
        );

        $dataSerializer = $this->dataSerializer;

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return ListSharesResult::create(
                $parsed,
                Utilities::getLocationFromHeaders($response->getHeaders())
            );
        });
    }

    /**
     * Creates a new share in the given storage account.
     *
     * @param string                  $share   The share name.
     * @param CreateShareOptions|null $options The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-share
     */
    public function createShare(
        $share,
        CreateShareOptions $options = null
    ) {
        $this->createShareAsync($share, $options)->wait();
    }

    /**
     * Creates promise to create a new share in the given storage account.
     *
     * @param string                  $share   The share name.
     * @param CreateShareOptions|null $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-share
     */
    public function createShareAsync(
        $share,
        CreateShareOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::notNullOrEmpty($share, 'share');

        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array(Resources::QP_REST_TYPE => 'share');
        $path        = $this->createPath($share);

        if (is_null($options)) {
            $options = new CreateShareOptions();
        }

        $metadata = $options->getMetadata();
        $headers  = $this->generateMetadataHeaders($metadata);
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_SHARE_QUOTA,
            $options->getQuota()
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Deletes a share in the given storage account.
     *
     * @param string                  $share   name of the share
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-share
     */
    public function deleteShare(
        $share,
        FileServiceOptions $options = null
    ) {
        $this->deleteShareAsync($share, $options)->wait();
    }

    /**
     * Create a promise for deleting a share.
     *
     * @param string                  $share   name of the share
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-share
     */
    public function deleteShareAsync(
        $share,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::notNullOrEmpty($share, 'share');

        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'share'
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_ACCEPTED,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Returns all properties and metadata on the share.
     *
     * @param string                  $share   name
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return GetSharePropertiesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-properties
     */
    public function getShareProperties(
        $share,
        FileServiceOptions $options = null
    ) {
        return $this->getSharePropertiesAsync($share, $options)->wait();
    }

    /**
     * Create promise to return all properties and metadata on the share.
     *
     * @param string                  $share   name
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-properties
     */
    public function getSharePropertiesAsync(
        $share,
        FileServiceOptions $options = null
    ) {
        return $this->getSharePropertiesAsyncImpl($share, $options, 'properties');
    }

    /**
     * Sets quota of the share.
     *
     * @param string                  $share   name
     * @param int                     $quota   quota of the share
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-properties
     */
    public function setShareProperties(
        $share,
        $quota,
        FileServiceOptions $options = null
    ) {
        $this->setSharePropertiesAsync($share, $quota, $options)->wait();
    }

    /**
     * Creates promise to set quota the share.
     *
     * @param string                  $share   name
     * @param int                     $quota   quota of the share
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-properties
     */
    public function setSharePropertiesAsync(
        $share,
        $quota,
        FileServiceOptions $options = null
    ) {
        return $this->setSharePropertiesAsyncImpl(
            $share,
            [Resources::X_MS_SHARE_QUOTA => $quota],
            $options,
            'properties'
        );
    }

    /**
     * Returns only user-defined metadata for the specified share.
     *
     * @param string                  $share   name
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return GetSharePropertiesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-metadata
     */
    public function getShareMetadata(
        $share,
        FileServiceOptions $options = null
    ) {
        return $this->getShareMetadataAsync($share, $options)->wait();
    }

    /**
     * Create promise to return only user-defined metadata for the specified
     * share.
     *
     * @param string                  $share   name
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-metadata
     */
    public function getShareMetadataAsync(
        $share,
        FileServiceOptions $options = null
    ) {
        return $this->getSharePropertiesAsyncImpl($share, $options, 'metadata');
    }

    /**
     * Updates metadata of the share.
     *
     * @param string                  $share    name
     * @param array                   $metadata metadata key/value pair.
     * @param FileServiceOptions|null $options optional  parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-metadata
     */
    public function setShareMetadata(
        $share,
        array $metadata,
        FileServiceOptions $options = null
    ) {
        $this->setShareMetadataAsync($share, $metadata, $options)->wait();
    }

    /**
     * Creates promise to update metadata headers on the share.
     *
     * @param string                  $share    name
     * @param array                   $metadata metadata key/value pair.
     * @param FileServiceOptions|null $options optional  parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-metadata
     */
    public function setShareMetadataAsync(
        $share,
        array $metadata,
        FileServiceOptions $options = null
    ) {
        return $this->setSharePropertiesAsyncImpl(
            $share,
            $metadata,
            $options,
            'metadata'
        );
    }

    /**
     * Gets the access control list (ACL) for the share.
     *
     * @param string                  $share The share name.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return GetShareACLResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-acl
     */
    public function getShareAcl(
        $share,
        FileServiceOptions $options = null
    ) {
        return $this->getShareAclAsync($share, $options)->wait();
    }

    /**
     * Creates the promise to get the access control list (ACL) for the share.
     *
     * @param string                  $share The share name.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-acl
     */
    public function getShareAclAsync(
        $share,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share);
        $statusCode  = Resources::STATUS_OK;

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'share'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'acl'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $dataSerializer = $this->dataSerializer;

        $promise = $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        );

        return $promise->then(function ($response) use ($dataSerializer) {
            $responseHeaders = HttpFormatter::formatHeaders($response->getHeaders());

            $etag = Utilities::tryGetValue($responseHeaders, Resources::ETAG);
            $modified = Utilities::tryGetValue(
                $responseHeaders,
                Resources::LAST_MODIFIED
            );
            $modifiedDate = Utilities::convertToDateTime($modified);
            $parsed       = $dataSerializer->unserialize($response->getBody());

            return GetShareAclResult::create(
                $etag,
                $modifiedDate,
                $parsed
            );
        }, null);
    }

    /**
     * Sets the ACL and any share-level access policies for the share.
     *
     * @param string                  $share   name
     * @param ShareACL                $acl     access control list for share
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-acl
     */
    public function setShareAcl(
        $share,
        ShareACL $acl,
        FileServiceOptions $options = null
    ) {
        $this->setShareAclAsync($share, $acl, $options)->wait();
    }

    /**
     * Creates promise to set the ACL and any share-level access policies
     * for the share.
     *
     * @param string                  $share   name
     * @param ShareACL                $acl     access control list for share
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-acl
     */
    public function setShareAclAsync(
        $share,
        ShareACL $acl,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::notNullOrEmpty($acl, 'acl');

        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share);
        $body        = $acl->toXml($this->dataSerializer);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'share'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'acl'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            $body,
            $options
        );
    }

    /**
     * Get the statistics related to the share.
     *
     * @param  string                  $share   The name of the share.
     * @param  FileServiceOptions|null $options The request options.
     *
     * @return GetShareStatsResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-stats
     */
    public function getShareStats($share, FileServiceOptions $options = null)
    {
        return $this->getShareStatsAsync($share, $options)->wait();
    }

    /**
     * Get the statistics related to the share.
     *
     * @param  string                  $share   The name of the share.
     * @param  FileServiceOptions|null $options The request options.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-stats
     */
    public function getShareStatsAsync($share, FileServiceOptions $options = null)
    {
        Validate::canCastAsString($share, 'share');

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->createPath($share);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'share'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'stats'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $dataSerializer = $this->dataSerializer;

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return GetShareStatsResult::create($parsed);
        }, null);
    }

    /**
     * List directories and files under specified path.
     *
     * @param  string                              $share   The share that
     *                                                      contains all the
     *                                                      files and directories.
     * @param  string                              $path    The path to be listed.
     * @param  ListDirectoriesAndFilesOptions|null $options Optional parameters.
     *
     * @return ListDirectoriesAndFilesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-directories-and-files
     */
    public function listDirectoriesAndFiles(
        $share,
        $path = '',
        ListDirectoriesAndFilesOptions $options = null
    ) {
        return $this->listDirectoriesAndFilesAsync($share, $path, $options)->wait();
    }

    /**
     * Creates promise to list directories and files under specified path.
     *
     * @param  string                              $share   The share that
     *                                                      contains all the
     *                                                      files and directories.
     * @param  string                              $path    The path to be listed.
     * @param  ListDirectoriesAndFilesOptions|null $options Optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-directories-and-files
     */
    public function listDirectoriesAndFilesAsync(
        $share,
        $path = '',
        ListDirectoriesAndFilesOptions $options = null
    ) {
        Validate::notNull($share, 'share');
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new ListDirectoriesAndFilesOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'directory'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'list'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_PREFIX,
            $options->getPrefix()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MARKER,
            $options->getNextMarker()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MAX_RESULTS,
            $options->getMaxResults()
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $dataSerializer = $this->dataSerializer;

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return ListDirectoriesAndFilesResult::create(
                $parsed,
                Utilities::getLocationFromHeaders($response->getHeaders())
            );
        }, null);
    }

    /**
     * Creates a new directory in the given share and path.
     *
     * @param string                      $share     The share name.
     * @param string                      $path      The path to create the directory.
     * @param CreateDirectoryOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-directory
     */
    public function createDirectory(
        $share,
        $path,
        CreateDirectoryOptions $options = null
    ) {
        $this->createDirectoryAsync($share, $path, $options)->wait();
    }

    /**
     * Creates a promise to create a new directory in the given share and path.
     *
     * @param string                      $share     The share name.
     * @param string                      $path      The path to create the directory.
     * @param CreateDirectoryOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-directory
     */
    public function createDirectoryAsync(
        $share,
        $path,
        CreateDirectoryOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');
        Validate::notNullOrEmpty($path, 'path');

        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array(Resources::QP_REST_TYPE => 'directory');
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new CreateDirectoryOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $metadata = $options->getMetadata();
        $headers  = $this->generateMetadataHeaders($metadata);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Deletes a directory in the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the directory.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-directory
     */
    public function deleteDirectory(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        $this->deleteDirectoryAsync($share, $path, $options)->wait();
    }

    /**
     * Creates a promise to delete a new directory in the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the directory.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-directory
     */
    public function deleteDirectoryAsync(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array(Resources::QP_REST_TYPE => 'directory');
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_ACCEPTED,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Gets a directory's properties from the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path of the directory.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return GetDirectoryPropertiesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-directory-properties
     */
    public function getDirectoryProperties(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        return $this->getDirectoryPropertiesAsync($share, $path, $options)->wait();
    }

    /**
     * Creates promise to get a directory's properties from the given share
     * and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path of the directory.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-directory-properties
     */
    public function getDirectoryPropertiesAsync(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array(Resources::QP_REST_TYPE => 'directory');
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) {
            $parsed = HttpFormatter::formatHeaders($response->getHeaders());
            return GetDirectoryPropertiesResult::create($parsed);
        }, null);
    }

    /**
     * Gets a directory's metadata from the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path of the directory.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return GetDirectoryMetadataResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-directory-metadata
     */
    public function getDirectoryMetadata(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        return $this->getDirectoryMetadataAsync($share, $path, $options)->wait();
    }

    /**
     * Creates promise to get a directory's metadata from the given share
     * and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path of the directory.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-directory-metadata
     */
    public function getDirectoryMetadataAsync(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array(Resources::QP_REST_TYPE => 'directory');
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) {
            $parsed = HttpFormatter::formatHeaders($response->getHeaders());
            return GetDirectoryMetadataResult::create($parsed);
        }, null);
    }

    /**
     * Sets a directory's metadata from the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the directory.
     * @param array                   $metadata  The metadata to be set.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-directory-metadata
     */
    public function setDirectoryMetadata(
        $share,
        $path,
        array $metadata,
        FileServiceOptions $options = null
    ) {
        $this->setDirectoryMetadataAsync(
            $share,
            $path,
            $metadata,
            $options
        )->wait();
    }

    /**
     * Creates promise to set a directory's metadata from the given share
     * and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the directory.
     * @param array                   $metadata  The metadata to be set.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-directory-metadata
     */
    public function setDirectoryMetadataAsync(
        $share,
        $path,
        array $metadata,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_PUT;
        $postParams  = array();
        $queryParams = array(Resources::QP_REST_TYPE => 'directory');
        $path        = $this->createPath($share, $path);

        Utilities::validateMetadata($metadata);
        $headers = $this->generateMetadataHeaders($metadata);
        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Create a new file.
     *
     * @param string                 $share   The share name.
     * @param string                 $path    The path and name of the file.
     * @param int                    $size    The size of the file.
     * @param CreateFileOptions|null $options The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-file
     */
    public function createFile(
        $share,
        $path,
        $size,
        CreateFileOptions $options = null
    ) {
        return $this->createFileAsync(
            $share,
            $path,
            $size,
            $options
        )->wait();
    }

    /**
     * Creates promise to create a new file.
     *
     * @param string                 $share   The share name.
     * @param string                 $path    The path and name of the file.
     * @param int                    $size    The size of the file.
     * @param CreateFileOptions|null $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-file
     */
    public function createFileAsync(
        $share,
        $path,
        $size,
        CreateFileOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::notNullOrEmpty($share, 'share');
        Validate::canCastAsString($path, 'path');
        Validate::notNullOrEmpty($path, 'path');
        Validate::isInteger($size, 'size');

        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new CreateFileOptions();
        }

        Utilities::validateMetadata($options->getMetadata());
        $headers = $this->generateMetadataHeaders($options->getMetadata());

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_TYPE,
            'file'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_CONTENT_LENGTH,
            $size
        );

        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            $options->getContentType()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_ENCODING,
            $options->getContentEncoding()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_LANGUAGE,
            $options->getContentLanguage()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::CACHE_CONTROL,
            $options->getCacheControl()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::FILE_CONTENT_MD5,
            $options->getContentMD5()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_DISPOSITION,
            $options->getContentDisposition()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_DISPOSITION,
            $options->getContentDisposition()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Deletes a file in the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the file.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-file2
     */
    public function deleteFile(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        $this->deleteFileAsync($share, $path, $options)->wait();
    }

    /**
     * Creates a promise to delete a new file in the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the file.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-file2
     */
    public function deleteFileAsync(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_ACCEPTED,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Reads or downloads a file from the server, including its metadata and
     * properties.
     *
     * @param string              $share   name of the share
     * @param string              $path    path of the file to be get
     * @param GetFileOptions|null $options optional parameters
     *
     * @return GetFileResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file
     */
    public function getFile(
        $share,
        $path,
        GetFileOptions $options = null
    ) {
        return $this->getFileAsync($share, $path, $options)->wait();
    }

    /**
     * Creates promise to read or download a file from the server, including its
     * metadata and properties.
     *
     * @param string              $share   name of the share
     * @param string              $path    path of the file to be get
     * @param GetFileOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file
     */
    public function getFileAsync(
        $share,
        $path,
        GetFileOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new GetFileOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_RANGE,
            $options->getRangeString() == '' ? null : $options->getRangeString()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_RANGE_GET_CONTENT_MD5,
            $options->getRangeGetContentMD5() ? 'true' : null
        );

        $options->setIsStreaming(true);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            array(Resources::STATUS_OK, Resources::STATUS_PARTIAL_CONTENT),
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) {
            $metadata = Utilities::getMetadataArray(
                HttpFormatter::formatHeaders($response->getHeaders())
            );

            return GetFileResult::create(
                HttpFormatter::formatHeaders($response->getHeaders()),
                $response->getBody(),
                $metadata
            );
        });
    }

    /**
     * Gets a file's properties from the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the file.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return FileProperties
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-properties
     */
    public function getFileProperties(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        return $this->getFilePropertiesAsync($share, $path, $options)->wait();
    }

    /**
     * Creates promise to get a file's properties from the given share
     * and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the file.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-properties
     */
    public function getFilePropertiesAsync(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_HEAD;
        $headers     = array();
        $queryParams  = array();
        $postParams  = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) {
            $parsed = HttpFormatter::formatHeaders($response->getHeaders());
            return FileProperties::createFromHttpHeaders($parsed);
        }, null);
    }

    /**
     * Sets properties on the file.
     *
     * @param string                  $share      share name
     * @param string                  $path       path of the file
     * @param FileProperties          $properties file properties.
     * @param FileServiceOptions|null $options    optional     parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-properties
     */
    public function setFileProperties(
        $share,
        $path,
        FileProperties $properties,
        FileServiceOptions $options = null
    ) {
        $this->setFilePropertiesAsync($share, $path, $properties, $options)->wait();
    }

    /**
     * Creates promise to set properties on the file.
     *
     * @param string                  $share      share name
     * @param string                  $path       path of the file
     * @param FileProperties          $properties file properties.
     * @param FileServiceOptions|null $options    optional     parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-properties
     */
    public function setFilePropertiesAsync(
        $share,
        $path,
        FileProperties $properties,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $headers = array();

        $method      = Resources::HTTP_PUT;
        $postParams  = array();
        $queryParams = array(Resources::QP_COMP => 'properties');
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_CACHE_CONTROL,
            $properties->getCacheControl()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_CONTENT_TYPE,
            $properties->getContentType()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_CONTENT_MD5,
            $properties->getContentMD5()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_CONTENT_ENCODING,
            $properties->getContentEncoding()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_CONTENT_LANGUAGE,
            $properties->getContentLanguage()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_CONTENT_DISPOSITION,
            $properties->getContentDisposition()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_CONTENT_LENGTH,
            $properties->getContentLength()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Gets a file's metadata from the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path of the file.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return GetFileMetadataResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-metadata
     */
    public function getFileMetadata(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        return $this->getFileMetadataAsync($share, $path, $options)->wait();
    }

    /**
     * Creates promise to get a file's metadata from the given share
     * and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path of the file.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-metadata
     */
    public function getFileMetadataAsync(
        $share,
        $path,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) {
            $parsed = HttpFormatter::formatHeaders($response->getHeaders());
            return GetFileMetadataResult::create($parsed);
        }, null);
    }

    /**
     * Sets a file's metadata from the given share and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the file.
     * @param array                   $metadata  The metadata to be set.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-metadata
     */
    public function setFileMetadata(
        $share,
        $path,
        array $metadata,
        FileServiceOptions $options = null
    ) {
        return $this->setFileMetadataAsync(
            $share,
            $path,
            $metadata,
            $options
        )->wait();
    }

    /**
     * Creates promise to set a file's metadata from the given share
     * and path.
     *
     * @param string                  $share     The share name.
     * @param string                  $path      The path to delete the file.
     * @param array                   $metadata  The metadata to be set.
     * @param FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-metadata
     */
    public function setFileMetadataAsync(
        $share,
        $path,
        array $metadata,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');

        $method      = Resources::HTTP_PUT;
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share, $path);

        Utilities::validateMetadata($metadata);
        $headers = $this->generateMetadataHeaders($metadata);
        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Writes range of bytes to a file. Range can be at most 4MB in length.
     *
     * @param  string                          $share   The share name.
     * @param  string                          $path    The path of the file.
     * @param  string|resource|StreamInterface $content The content to be uploaded.
     * @param  Range                           $range   The range in the file to
     *                                                  be put.
     * @param  PutFileRangeOptions|null        $options The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/put-range
     */
    public function putFileRange(
        $share,
        $path,
        $content,
        Range $range,
        PutFileRangeOptions $options = null
    ) {
        $this->putFileRangeAsync(
            $share,
            $path,
            $content,
            $range,
            $options
        )->wait();
    }

    /**
     * Creates promise to write range of bytes to a file. Range can be at most
     * 4MB in length.
     *
     * @param  string                          $share   The share name.
     * @param  string                          $path    The path of the file.
     * @param  string|resource|StreamInterface $content The content to be uploaded.
     * @param  Range                           $range   The range in the file to
     *                                                  be put.
     * @param  PutFileRangeOptions|null        $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/put-range
     *
     */
    public function putFileRangeAsync(
        $share,
        $path,
        $content,
        Range $range,
        PutFileRangeOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');
        Validate::notNullOrEmpty($path, 'path');
        Validate::notNullOrEmpty($share, 'share');
        Validate::notNull($range->getLength(), Resources::RESOURCE_RANGE_LENGTH_MUST_SET);
        $stream = Psr7\stream_for($content);

        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->createPath($share, $path);

        if ($options == null) {
            $options = new PutFileRangeOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_RANGE,
            $range->getRangeString()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_LENGTH,
            $range->getLength()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_WRITE,
            'Update'
        );

        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_MD5,
            $options->getContentMD5()
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'range'
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            $stream,
            $options
        );
    }

    /**
     * Creates a file from a provided content.
     *
     * @param  string                             $share   the share name
     * @param  string                             $path    the path of the file
     * @param  StreamInterface|resource|string    $content the content used to
     *                                                     create the file
     * @param  CreateFileFromContentOptions|null  $options optional parameters
     *
     * @return void
     */
    public function createFileFromContent(
        $share,
        $path,
        $content,
        CreateFileFromContentOptions $options = null
    ) {
        $this->createFileFromContentAsync($share, $path, $content, $options)->wait();
    }

    /**
     * Creates a promise to create a file from a provided content.
     *
     * @param  string                            $share   the share name
     * @param  string                            $path    the path of the file
     * @param  StreamInterface|resource|string   $content the content used to
     *                                                  create the file
     * @param  CreateFileFromContentOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createFileFromContentAsync(
        $share,
        $path,
        $content,
        CreateFileFromContentOptions $options = null
    ) {
        $stream = Psr7\stream_for($content);
        $size = $stream->getSize();

        if ($options == null) {
            $options = new CreateFileFromContentOptions();
        }

        //create the file first
        $promise = $this->createFileAsync($share, $path, $size, $options);

        //then upload the content
        $range = new Range(0, $size - 1);
        $putOptions = new PutFileRangeOptions($options);
        $useTransactionalMD5 = $options->getUseTransactionalMD5();
        if ($size > Resources::MB_IN_BYTES_4) {
            return $promise->then(function ($response) use (
                $share,
                $path,
                $stream,
                $range,
                $putOptions,
                $useTransactionalMD5
            ) {
                return $this->multiplePutRangeConcurrentAsync(
                    $share,
                    $path,
                    $stream,
                    $range,
                    $putOptions,
                    $useTransactionalMD5
                );
            }, null);
        } else {
            return $promise->then(function ($response) use (
                $share,
                $path,
                $stream,
                $range,
                $putOptions
            ) {
                return $this->putFileRangeAsync(
                    $share,
                    $path,
                    $stream,
                    $range,
                    $putOptions
                );
            }, null);
        }
    }

    /**
     * Clears range of bytes of a file. If the specified range is not 512-byte
     * aligned, the operation will write zeros to the start or end of the range
     * that is not 512-byte aligned and free the rest of the range inside that
     * is 512-byte aligned.
     *
     * @param  string                  $share   The share name.
     * @param  string                  $path    The path of the file.
     * @param  Range                   $range   The range in the file to
     *                                          be cleared.
     * @param  FileServiceOptions|null $options The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/put-range
     */
    public function clearFileRange(
        $share,
        $path,
        Range $range,
        FileServiceOptions $options = null
    ) {
        $this->clearFileRangeAsync($share, $path, $range, $options)->wait();
    }

    /**
     * Creates promise to clear range of bytes of a file. If the specified range
     * is not 512-byte aligned, the operation will write zeros to the start or
     * end of the range that is not 512-byte aligned and free the rest of the
     * range inside that is 512-byte aligned.
     *
     * @param  string                  $share   The share name.
     * @param  string                  $path    The path of the file.
     * @param  Range                   $range   The range in the file to
     *                                          be cleared.
     * @param  FileServiceOptions|null $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/put-range
     *
     */
    public function clearFileRangeAsync(
        $share,
        $path,
        Range $range,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');
        Validate::notNullOrEmpty($path, 'path');
        Validate::notNullOrEmpty($share, 'share');

        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_RANGE,
            $range->getRangeString()
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_WRITE,
            'Clear'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'range'
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Lists range of bytes of a file.
     *
     * @param  string                  $share   The share name.
     * @param  string                  $path    The path of the file.
     * @param  Range                   $range   The range in the file to
     *                                          be listed.
     * @param  FileServiceOptions|null $options The optional parameters.
     *
     * @return ListFileRangesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-ranges
     */
    public function listFileRange(
        $share,
        $path,
        Range $range = null,
        FileServiceOptions $options = null
    ) {
        return $this->listFileRangeAsync($share, $path, $range, $options)->wait();
    }

    /**
     * Creates promise to list range of bytes of a file.
     *
     * @param  string                  $share   The share name.
     * @param  string                  $path    The path of the file.
     * @param  Range                   $range   The range in the file to
     *                                          be listed.
     * @param  FileServiceOptions|null $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-ranges
     *
     */
    public function listFileRangeAsync(
        $share,
        $path,
        Range $range = null,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');
        Validate::notNullOrEmpty($path, 'path');
        Validate::notNullOrEmpty($share, 'share');

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        if ($range != null) {
            $this->addOptionalHeader(
                $headers,
                Resources::X_MS_RANGE,
                $range->getRangeString()
            );
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'rangelist'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $dataSerializer = $this->dataSerializer;

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) use ($dataSerializer) {
            $responseHeaders = HttpFormatter::formatHeaders($response->getHeaders());
            $parsed = $dataSerializer->unserialize($response->getBody());
            return ListFileRangesResult::create($responseHeaders, $parsed);
        }, null);
    }

    /**
     * Informs server to copy file from $sourcePath to $path.
     * To copy a file to another file within the same storage account, you may
     * use Shared Key to authenticate the source file. If you are copying a file
     * from another storage account, or if you are copying a blob from the same
     * storage account or another storage account, then you must authenticate
     * the source file or blob using a shared access signature. If the source is
     * a public blob, no authentication is required to perform the copy
     * operation.
     * Here are some examples of source object URLs:
     * https://myaccount.file.core.windows.net/myshare/mydirectorypath/myfile
     * https://myaccount.blob.core.windows.net/mycontainer/myblob?sastoken
     *
     * @param  string                  $share      The share name.
     * @param  string                  $path       The path of the file.
     * @param  string                  $sourcePath The path of the source.
     * @param  array                   $metadata   The metadata of the file.
     *                                             If specified, source metadata
     *                                             will not be copied.
     * @param  FileServiceOptions|null $options    The optional parameters.
     *
     * @return CopyFileResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/copy-file
     */
    public function copyFile(
        $share,
        $path,
        $sourcePath,
        array $metadata = array(),
        FileServiceOptions $options = null
    ) {
        return $this->copyFileAsync(
            $share,
            $path,
            $sourcePath,
            $metadata,
            $options
        )->wait();
    }

    /**
     * Creates promise to inform server to copy file from $sourcePath to $path.
     *
     * To copy a file to another file within the same storage account, you may
     * use Shared Key to authenticate the source file. If you are copying a file
     * from another storage account, or if you are copying a blob from the same
     * storage account or another storage account, then you must authenticate
     * the source file or blob using a shared access signature. If the source is
     * a public blob, no authentication is required to perform the copy
     * operation.
     * Here are some examples of source object URLs:
     * https://myaccount.file.core.windows.net/myshare/mydirectorypath/myfile
     * https://myaccount.blob.core.windows.net/mycontainer/myblob?sastoken
     *
     * @param  string                  $share      The share name.
     * @param  string                  $path       The path of the file.
     * @param  string                  $sourcePath The path of the source.
     * @param  array                   $metadata   The metadata of the file.
     *                                             If specified, source metadata
     *                                             will not be copied.
     * @param  FileServiceOptions|null $options    The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/copy-file
     *
     */
    public function copyFileAsync(
        $share,
        $path,
        $sourcePath,
        array $metadata = array(),
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');
        Validate::canCastAsString($sourcePath, 'sourcePath');
        Validate::notNullOrEmpty($path, 'path');
        Validate::notNullOrEmpty($share, 'share');
        Validate::notNullOrEmpty($sourcePath, 'sourcePath');

        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->createPath($share, $path);

        Utilities::validateMetadata($metadata);
        $headers = $this->generateMetadataHeaders($metadata);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_COPY_SOURCE,
            $sourcePath
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_ACCEPTED,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) {
            $headers = HttpFormatter::formatHeaders($response->getHeaders());
            return CopyFileResult::create($headers);
        }, null);
    }

    /**
     * Abort a file copy operation
     *
     * @param string                  $share   name of the share
     * @param string                  $path    path of the file
     * @param string                  $copyID  copy operation identifier.
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/abort-copy-file
     */
    public function abortCopy(
        $share,
        $path,
        $copyID,
        FileServiceOptions $options = null
    ) {
        return $this->abortCopyAsync(
            $share,
            $path,
            $copyID,
            $options
        )->wait();
    }

    /**
     * Creates promise to abort a file copy operation
     *
     * @param string                  $share   name of the share
     * @param string                  $path    path of the file
     * @param string                  $copyID  copy operation identifier.
     * @param FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/abort-copy-file
     */
    public function abortCopyAsync(
        $share,
        $path,
        $copyID,
        FileServiceOptions $options = null
    ) {
        Validate::canCastAsString($share, 'share');
        Validate::canCastAsString($path, 'path');
        Validate::canCastAsString($copyID, 'copyID');
        Validate::notNullOrEmpty($share, 'share');
        Validate::notNullOrEmpty($path, 'path');
        Validate::notNullOrEmpty($copyID, 'copyID');

        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->createPath($share, $path);

        if (is_null($options)) {
            $options = new FileServiceOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'copy'
        );

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COPY_ID,
            $copyID
        );

        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_COPY_ACTION,
            'abort'
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_NO_CONTENT,
            Resources::EMPTY_STRING,
            $options
        );
    }
}
