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
 * @package   MicrosoftAzure\Storage\Blob
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Blob;

use MicrosoftAzure\Storage\Common\Internal\HttpFormatter;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy;
use MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult;
use MicrosoftAzure\Storage\Blob\Internal\IBlob;
use MicrosoftAzure\Storage\Blob\Models\BlobServiceOptions;
use MicrosoftAzure\Storage\Blob\Models\ListContainersOptions;
use MicrosoftAzure\Storage\Blob\Models\ListContainersResult;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult;
use MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult;
use MicrosoftAzure\Storage\Blob\Models\SetContainerMetadataOptions;
use MicrosoftAzure\Storage\Blob\Models\DeleteContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsResult;
use MicrosoftAzure\Storage\Blob\Models\BlobType;
use MicrosoftAzure\Storage\Blob\Models\Block;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\BlobProperties;
use MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesResult;
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesResult;
use MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult;
use MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataOptions;
use MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult;
use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobResult;
use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\LeaseMode;
use MicrosoftAzure\Storage\Blob\Models\AcquireLeaseOptions;
use MicrosoftAzure\Storage\Blob\Models\LeaseBlobResult;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult;
use MicrosoftAzure\Storage\Blob\Models\PageWriteOption;
use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions;
use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobBlockOptions;
use MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions;
use MicrosoftAzure\Storage\Blob\Models\BlockList;
use MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksOptions;
use MicrosoftAzure\Storage\Blob\Models\ContainerACL;
use MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult;
use MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobSnapshotOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobSnapshotResult;
use MicrosoftAzure\Storage\Blob\Models\PageRange;
use MicrosoftAzure\Storage\Blob\Models\CopyBlobResult;
use MicrosoftAzure\Storage\Blob\Models\BreakLeaseResult;
use MicrosoftAzure\Storage\Blob\Models\PutBlockResult;
use MicrosoftAzure\Storage\Blob\Models\PutBlobResult;
use MicrosoftAzure\Storage\Common\Internal\ServiceFunctionThread;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7;

/**
 * This class constructs HTTP requests and receive HTTP responses for blob
 * service layer.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BlobRestProxy extends ServiceRestProxy implements IBlob
{
    /**
     * @var int Defaults to 32MB
     */
    private $_SingleBlobUploadThresholdInBytes = Resources::MB_IN_BYTES_32;

    /**
     * Get the value for SingleBlobUploadThresholdInBytes
     *
     * @return int
     */
    public function getSingleBlobUploadThresholdInBytes()
    {
        return $this->_SingleBlobUploadThresholdInBytes;
    }

    /**
     * Set the value for SingleBlobUploadThresholdInBytes, Max 64MB
     *
     * @param int $val The max size to send as a single blob block
     *
     * @return void
     */
    public function setSingleBlobUploadThresholdInBytes($val)
    {
        if ($val > Resources::MB_IN_BYTES_64) {
            // What should the proper action here be?
            $val = Resources::MB_IN_BYTES_64;
        } elseif ($val < 1) {
            // another spot that could use looking at
            $val = Resources::MB_IN_BYTES_32;
        }
        $this->_SingleBlobUploadThresholdInBytes = $val;
    }

    /**
     * Gets the copy blob source name with specified parameters.
     *
     * @param string                 $containerName The name of the container.
     * @param string                 $blobName      The name of the blob.
     * @param Models\CopyBlobOptions $options       The optional parameters.
     *
     * @return string
     */
    private function _getCopyBlobSourceName(
        $containerName,
        $blobName,
        Models\CopyBlobOptions $options
    ) {
        $sourceName = $this->_getBlobUrl($containerName, $blobName);

        if (!is_null($options->getSourceSnapshot())) {
            $sourceName .= '?snapshot=' . $options->getSourceSnapshot();
        }

        return $sourceName;
    }
    
    /**
     * Creates URI path for blob or container.
     *
     * @param string $container The container name.
     * @param string $blob      The blob name.
     *
     * @return string
     */
    private function _createPath($container, $blob = '')
    {
        if (empty($blob)) {
            if (!empty($container)) {
                return $container;
            } else {
                return '/' . $container;
            }
        } else {
            $encodedBlob = urlencode($blob);
            // Unencode the forward slashes to match what the server expects.
            $encodedBlob = str_replace('%2F', '/', $encodedBlob);
            // Unencode the backward slashes to match what the server expects.
            $encodedBlob = str_replace('%5C', '/', $encodedBlob);
            // Re-encode the spaces (encoded as space) to the % encoding.
            $encodedBlob = str_replace('+', '%20', $encodedBlob);
            // Empty container means accessing default container
            if (empty($container)) {
                return $encodedBlob;
            } else {
                return '/' . $container . '/' . $encodedBlob;
            }
        }
    }
    
    /**
     * Creates full URI to the given blob.
     *
     * @param string $container The container name.
     * @param string $blob      The blob name.
     *
     * @return string
     */
    private function _getBlobUrl($container, $blob)
    {
        $encodedBlob = $this->_createPath($container, $blob);

        if (substr($encodedBlob, 0, 1) != '/' &&
            substr($this->getUri(), -1, 1) != '/') {
            $encodedBlob =  '/' .  $encodedBlob;
        } elseif (substr($encodedBlob, 0, 1) == '/' &&
            substr($this->getUri(), -1, 1) == '/') {
            $encodedBlob = substr($encodedBlob, 1);
        }
        return $this->getUri() . $encodedBlob;
    }
      
    /**
     * Helper method to create promise for getContainerProperties API call.
     *
     * @param string                    $container The container name.
     * @param Models\BlobServiceOptions $options   The optional parameters.
     * @param string                    $operation The operation string. Should be
     * 'metadata' to get metadata.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    private function _getContainerPropertiesAsyncImpl(
        $container,
        Models\BlobServiceOptions $options = null,
        $operation = null
    ) {
        Validate::isString($container, 'container');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->_createPath($container);
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            $operation
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
            $options->getRequestOptions()
        )->then(function ($response) {
            $responseHeaders = HttpFormatter::formatHeaders($response->getHeaders());
            return GetContainerPropertiesResult::create($responseHeaders);
        }, null);
    }
    
    /**
     * Adds optional create blob headers.
     *
     * @param CreateBlobOptions $options The optional parameters.
     * @param array             $headers The HTTP request headers.
     *
     * @return array
     */
    private function _addCreateBlobOptionalHeaders(
        CreateBlobOptions $options,
        array $headers
    ) {
        $contentType         = $options->getContentType();
        $metadata            = $options->getMetadata();
        $blobContentType     = $options->getBlobContentType();
        $blobContentEncoding = $options->getBlobContentEncoding();
        $blobContentLanguage = $options->getBlobContentLanguage();
        $blobContentMD5      = $options->getBlobContentMD5();
        $blobCacheControl    = $options->getBlobCacheControl();
        $leaseId             = $options->getLeaseId();
        
        if (!is_null($contentType)) {
            $this->addOptionalHeader(
                $headers,
                Resources::CONTENT_TYPE,
                $options->getContentType()
            );
        } else {
            $this->addOptionalHeader(
                $headers,
                Resources::CONTENT_TYPE,
                Resources::BINARY_FILE_TYPE
            );
        }
        $headers = $this->addMetadataHeaders($headers, $metadata);
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
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
            Resources::CONTENT_MD5,
            $options->getContentMD5()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CACHE_CONTROL,
            $options->getCacheControl()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $leaseId
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_TYPE,
            $blobContentType
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_ENCODING,
            $blobContentEncoding
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LANGUAGE,
            $blobContentLanguage
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_MD5,
            $blobContentMD5
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CACHE_CONTROL,
            $blobCacheControl
        );
        
        return $headers;
    }
    
    /**
     * Adds Range header to the headers array.
     *
     * @param array   $headers The HTTP request headers.
     * @param integer $start   The start byte.
     * @param integer $end     The end byte.
     *
     * @return array
     */
    private function _addOptionalRangeHeader(array $headers, $start, $end)
    {
        if (!is_null($start) || !is_null($end)) {
            $range      = $start . '-' . $end;
            $rangeValue = 'bytes=' . $range;
            $this->addOptionalHeader($headers, Resources::RANGE, $rangeValue);
        }
        
        return $headers;
    }

    /**
     * Get the expected status code of a given lease action.
     *
     * @param  string $leaseAction The given lease action
     *
     * @return string
     */
    private static function getStatusCodeOfLeaseAction($leaseAction)
    {
        $statusCode = Resources::EMPTY_STRING;
        switch ($leaseAction) {
            case LeaseMode::ACQUIRE_ACTION:
                $statusCode = Resources::STATUS_CREATED;
                break;
            case LeaseMode::RENEW_ACTION:
                $statusCode = Resources::STATUS_OK;
                break;
            case LeaseMode::RELEASE_ACTION:
                $statusCode = Resources::STATUS_OK;
                break;
            case LeaseMode::BREAK_ACTION:
                $statusCode = Resources::STATUS_ACCEPTED;
                break;
            default:
                throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
        }

        return $statusCode;
    }

    /**
     * Creates promise that does the actual work for leasing a blob.
     *
     * @param string                    $leaseAction        Lease action string.
     * @param string                    $container          Container name.
     * @param string                    $blob               Blob to lease name.
     * @param string                    $leaseId            Existing lease id.
     * @param string                    $expectedStatusCode Expected status code.
     * @param Models\BlobServiceOptions $options            Optional parameters.
     * @param Models\AccessCondition    $accessCondition    Access conditions.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    private function _putLeaseAsyncImpl(
        $leaseAction,
        $container,
        $blob,
        $leaseId,
        $expectedStatusCode,
        Models\BlobServiceOptions $options,
        Models\AccessCondition $accessCondition = null
    ) {
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isString($container, 'container');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->_createPath($container, $blob);
        
        if ($leaseAction == LeaseMode::ACQUIRE_ACTION) {
            $this->addOptionalHeader(
                $headers,
                Resources::X_MS_LEASE_DURATION,
                -1
            );
        }
        
        if (!is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $accessCondition
        );

        $this->addOptionalHeader($headers, Resources::X_MS_LEASE_ID, $leaseId);
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ACTION,
            $leaseAction
        );
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'lease');
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
            $expectedStatusCode,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        );
    }

    /**
     * Creates promise that does actual work for create and clear blob pages.
     *
     * @param string                 $action    Either clear or create.
     * @param string                 $container The container name.
     * @param string                 $blob      The blob name.
     * @param PageRange              $range     The page ranges.
     * @param string                 $content   The content string.
     * @param CreateBlobPagesOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    private function _updatePageBlobPagesAsyncImpl(
        $action,
        $container,
        $blob,
        PageRange $range,
        $content,
        CreateBlobPagesOptions $options = null
    ) {
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isString($container, 'container');
        Validate::isString($content, 'content');
        Validate::isTrue(
            $range instanceof PageRange,
            sprintf(
                Resources::INVALID_PARAM_MSG,
                'range',
                get_class(new PageRange())
            )
        );
        $body = Psr7\stream_for($content);
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new CreateBlobPagesOptions();
        }
        
        $headers = $this->_addOptionalRangeHeader(
            $headers,
            $range->getStart(),
            $range->getEnd()
        );
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_MD5,
            $options->getContentMD5()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_PAGE_WRITE,
            $action
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'page');
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
            $body,
            $options->getRequestOptions()
        )->then(function ($response) {
            return CreateBlobPagesResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }
    
    /**
     * Gets the properties of the Blob service.
     *
     * @param Models\BlobServiceOptions $options The optional parameters.
     *
     * @return \MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452239.aspx
     */
    public function getServiceProperties(
        Models\BlobServiceOptions $options = null
    ) {
        return $this->getServicePropertiesAsync($options)->wait();
    }

    /**
     * Creates promise to get the properties of the Blob service.
     *
     * @param Models\BlobServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452239.aspx
     */
    public function getServicePropertiesAsync(
        Models\BlobServiceOptions $options = null
    ) {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'service'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'properties'
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
            $options->getRequestOptions()
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return GetServicePropertiesResult::create($parsed);
        }, null);
    }

    /**
     * Sets the properties of the Blob service.
     *
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     *
     * @param ServiceProperties         $serviceProperties The service properties.
     * @param Models\BlobServiceOptions $options           The optional parameters.
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452235.aspx
     */
    public function setServiceProperties(
        ServiceProperties $serviceProperties,
        Models\BlobServiceOptions $options = null
    ) {
        $this->setServicePropertiesAsync($serviceProperties, $options)->wait();
    }

    /**
     * Creates the promise to set the properties of the Blob service.
     *
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     *
     * @param ServiceProperties         $serviceProperties The service properties.
     * @param Models\BlobServiceOptions $options           The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452235.aspx
     */
    public function setServicePropertiesAsync(
        ServiceProperties $serviceProperties,
        Models\BlobServiceOptions $options = null
    ) {
        Validate::isTrue(
            $serviceProperties instanceof ServiceProperties,
            Resources::INVALID_SVC_PROP_MSG
        );
                
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = Resources::EMPTY_STRING;
        $body        = $serviceProperties->toXml($this->dataSerializer);
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
    
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'service'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'properties'
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
            Resources::STATUS_ACCEPTED,
            $body,
            $options->getRequestOptions()
        );
    }
    
    /**
     * Lists all of the containers in the given storage account.
     *
     * @param Models\ListContainersOptions $options The optional parameters.
     *
     * @return Models\ListContainersResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179352.aspx
     */
    public function listContainers(Models\ListContainersOptions $options = null)
    {
        return $this->listContainersAsync($options)->wait();
    }

    /**
     * Create a promise for lists all of the containers in the given
     * storage account.
     *
     * @param  Models\ListContainersOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listContainersAsync(
        Models\ListContainersOptions $options = null
    ) {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new ListContainersOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
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
            $options->getMarker()
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
            $options->getRequestOptions()
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $this->dataSerializer->unserialize($response->getBody());
            return ListContainersResult::create($parsed);
        });
    }
    
    /**
     * Creates a new container in the given storage account.
     *
     * @param string                        $container The container name.
     * @param Models\CreateContainerOptions $options   The optional parameters.
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
     */
    public function createContainer(
        $container,
        Models\CreateContainerOptions $options = null
    ) {
        $this->createContainerAsync($container, $options)->wait();
    }

    /**
     * Creates a new container in the given storage account.
     *
     * @param string                        $container The container name.
     * @param Models\CreateContainerOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
     */
    public function createContainerAsync(
        $container,
        Models\CreateContainerOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::notNullOrEmpty($container, 'container');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array(Resources::QP_REST_TYPE => 'container');
        $path        = $this->_createPath($container);
        
        if (is_null($options)) {
            $options = new CreateContainerOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $metadata = $options->getMetadata();
        $headers  = $this->generateMetadataHeaders($metadata);
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_PUBLIC_ACCESS,
            $options->getPublicAccess()
        );
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        );
    }
    
    /**
     * Deletes a container in the given storage account.
     *
     * @param string                        $container The container name.
     * @param Models\DeleteContainerOptions $options   The optional parameters.
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179408.aspx
     */
    public function deleteContainer(
        $container,
        Models\DeleteContainerOptions $options = null
    ) {
        $this->deleteContainerAsync($container, $options)->wait();
    }

    /**
     * Create a promise for deleting a container.
     *
     * @param  string                             $container name of the container
     * @param  Models\DeleteContainerOptions|null $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteContainerAsync(
        $container,
        Models\DeleteContainerOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::notNullOrEmpty($container, 'container');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container);
        
        if (is_null($options)) {
            $options = new DeleteContainerOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_ACCEPTED,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        );
    }
    
    /**
     * Returns all properties and metadata on the container.
     *
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return Models\GetContainerPropertiesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179370.aspx
     */
    public function getContainerProperties(
        $container,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->getContainerPropertiesAsync($container, $options)->wait();
    }

    /**
     * Create promise to return all properties and metadata on the container.
     *
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179370.aspx
     */
    public function getContainerPropertiesAsync(
        $container,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->_getContainerPropertiesAsyncImpl($container, $options);
    }
    
    /**
     * Returns only user-defined metadata for the specified container.
     *
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return Models\GetContainerPropertiesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691976.aspx
     */
    public function getContainerMetadata(
        $container,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->getContainerMetadataAsync($container, $options)->wait();
    }

    /**
     * Create promise to return only user-defined metadata for the specified
     * container.
     *
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691976.aspx
     */
    public function getContainerMetadataAsync(
        $container,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->_getContainerPropertiesAsyncImpl($container, $options, 'metadata');
    }
    
    /**
     * Gets the access control list (ACL) and any container-level access policies
     * for the container.
     *
     * @param string                    $container The container name.
     * @param Models\BlobServiceOptions $options   The optional parameters.
     *
     * @return Models\GetContainerACLResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179469.aspx
     */
    public function getContainerAcl(
        $container,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->getContainerAclAsync($container, $options)->wait();
    }

    /**
     * Creates the promise to get the access control list (ACL) and any
     * container-level access policies for the container.
     *
     * @param string                    $container The container name.
     * @param Models\BlobServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179469.aspx
     */
    public function getContainerAclAsync(
        $container,
        Models\BlobServiceOptions $options = null
    ) {
        Validate::isString($container, 'container');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container);
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'acl'
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
            $options->getRequestOptions()
        );

        return $promise->then(function ($response) use ($dataSerializer) {
            $responseHeaders = HttpFormatter::formatHeaders($response->getHeaders());
        
            $access = Utilities::tryGetValue(
                $responseHeaders,
                Resources::X_MS_BLOB_PUBLIC_ACCESS
            );
            $etag = Utilities::tryGetValue($responseHeaders, Resources::ETAG);
            $modified = Utilities::tryGetValue(
                $responseHeaders,
                Resources::LAST_MODIFIED
            );
            $modifiedDate = Utilities::convertToDateTime($modified);
            $parsed       = $dataSerializer->unserialize($response->getBody());
                    
            return GetContainerAclResult::create(
                $access,
                $etag,
                $modifiedDate,
                $parsed
            );
        }, null);
    }
    
    /**
     * Sets the ACL and any container-level access policies for the container.
     *
     * @param string                    $container name
     * @param Models\ContainerACL       $acl       access control list for container
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179391.aspx
     */
    public function setContainerAcl(
        $container,
        Models\ContainerACL $acl,
        Models\BlobServiceOptions $options = null
    ) {
        $this->setContainerAclAsync($container, $acl, $options)->wait();
    }

    /**
     * Creates promise to set the ACL and any container-level access policies
     * for the container.
     *
     * @param string                    $container name
     * @param Models\ContainerACL       $acl       access control list for container
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179391.aspx
     */
    public function setContainerAclAsync(
        $container,
        Models\ContainerACL $acl,
        Models\BlobServiceOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::notNullOrEmpty($acl, 'acl');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container);
        $body        = $acl->toXml($this->dataSerializer);
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'acl'
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_PUBLIC_ACCESS,
            $acl->getPublicAccess()
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
            $options->getRequestOptions()
        );
    }
    
    /**
     * Sets metadata headers on the container.
     *
     * @param string                             $container name
     * @param array                              $metadata  metadata key/value pair.
     * @param Models\SetContainerMetadataOptions $options   optional parameters
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179362.aspx
     */
    public function setContainerMetadata(
        $container,
        array $metadata,
        Models\SetContainerMetadataOptions $options = null
    ) {
        $this->setContainerMetadataAsync($container, $metadata, $options)->wait();
    }

    /**
     * Sets metadata headers on the container.
     *
     * @param string                             $container name
     * @param array                              $metadata  metadata key/value pair.
     * @param Models\SetContainerMetadataOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179362.aspx
     */
    public function setContainerMetadataAsync(
        $container,
        array $metadata,
        Models\SetContainerMetadataOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Utilities::validateMetadata($metadata);
        
        $method      = Resources::HTTP_PUT;
        $headers     = $this->generateMetadataHeaders($metadata);
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container);
        
        if (is_null($options)) {
            $options = new SetContainerMetadataOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        );
    }
    
    /**
     * Lists all of the blobs in the given container.
     *
     * @param string                  $container The container name.
     * @param Models\ListBlobsOptions $options   The optional parameters.
     *
     * @return Models\ListBlobsResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135734.aspx
     */
    public function listBlobs($container, Models\ListBlobsOptions $options = null)
    {
        return $this->listBlobsAsync($container, $options)->wait();
    }

    /**
     * Creates promise to list all of the blobs in the given container.
     *
     * @param string                  $container The container name.
     * @param Models\ListBlobsOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135734.aspx
     */
    public function listBlobsAsync(
        $container,
        Models\ListBlobsOptions $options = null
    ) {
        Validate::isString($container, 'container');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container);
        
        if (is_null($options)) {
            $options = new ListBlobsOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'list'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_PREFIX,
            str_replace('\\', '/', $options->getPrefix())
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MARKER,
            $options->getMarker()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_DELIMITER,
            $options->getDelimiter()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MAX_RESULTS,
            $options->getMaxResults()
        );
        
        $includeMetadata         = $options->getIncludeMetadata();
        $includeSnapshots        = $options->getIncludeSnapshots();
        $includeUncommittedBlobs = $options->getIncludeUncommittedBlobs();
        
        $includeValue = static::groupQueryValues(
            array(
                $includeMetadata ? 'metadata' : null,
                $includeSnapshots ? 'snapshots' : null,
                $includeUncommittedBlobs ? 'uncommittedblobs' : null
            )
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_INCLUDE,
            $includeValue
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
            $options->getRequestOptions()
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return ListBlobsResult::create($parsed);
        }, null);
    }
    
    /**
     * Creates a new page blob. Note that calling createPageBlob to create a page
     * blob only initializes the blob.
     * To add content to a page blob, call createBlobPages method.
     *
     * @param string                   $container The container name.
     * @param string                   $blob      The blob name.
     * @param integer                  $length    Specifies the maximum size
     *                                            for the page blob, up to 1 TB.
     *                                            The page blob size must be
     *                                            aligned to a 512-byte
     *                                            boundary.
     * @param Models\CreateBlobOptions $options   The optional parameters.
     *
     * @return Models\PutBlobResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createPageBlob(
        $container,
        $blob,
        $length,
        Models\CreateBlobOptions $options = null
    ) {
        return $this->createPageBlobAsync(
            $container,
            $blob,
            $length,
            $options
        )->wait();
    }

    /**
     * Creates promise to create a new page blob. Note that calling
     * createPageBlob to create a page blob only initializes the blob.
     * To add content to a page blob, call createBlobPages method.
     *
     * @param string                   $container The container name.
     * @param string                   $blob      The blob name.
     * @param integer                  $length    Specifies the maximum size
     *                                            for the page blob, up to 1 TB.
     *                                            The page blob size must be
     *                                            aligned to a 512-byte
     *                                            boundary.
     * @param Models\CreateBlobOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createPageBlobAsync(
        $container,
        $blob,
        $length,
        Models\CreateBlobOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isInteger($length, 'length');
        Validate::notNull($length, 'length');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_CREATED;
        
        if (is_null($options)) {
            $options = new CreateBlobOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_TYPE,
            BlobType::PAGE_BLOB
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LENGTH,
            $length
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_SEQUENCE_NUMBER,
            $options->getSequenceNumber()
        );
        $headers = $this->_addCreateBlobOptionalHeaders($options, $headers);
        
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
            $options->getRequestOptions()
        )->then(function ($response) {
            return PutBlobResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }
    
    /**
     * Creates a new block blob or updates the content of an existing block blob.
     *
     * Updating an existing block blob overwrites any existing metadata on the blob.
     * Partial updates are not supported with createBlockBlob the content of the
     * existing blob is overwritten with the content of the new blob. To perform a
     * partial update of the content of a block blob, use the createBlockList
     * method.
     * Note that the default content type is application/octet-stream.
     *
     * @param string                          $container The name of the container.
     * @param string                          $blob      The name of the blob.
     * @param string|resource|StreamInterface $content   The content of the blob.
     * @param Models\CreateBlobOptions        $options   The optional parameters.
     *
     * @return Models\PutBlobResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createBlockBlob(
        $container,
        $blob,
        $content,
        Models\CreateBlobOptions $options = null
    ) {
        return $this->createBlockBlobAsync(
            $container,
            $blob,
            $content,
            $options
        )->wait();
    }

    /**
     * Creates a promise to create a new block blob or updates the content of
     * an existing block blob.
     *
     * Updating an existing block blob overwrites any existing metadata on the blob.
     * Partial updates are not supported with createBlockBlob the content of the
     * existing blob is overwritten with the content of the new blob. To perform a
     * partial update of the content of a block blob, use the createBlockList
     * method.
     *
     * @param string                          $container The name of the container.
     * @param string                          $blob      The name of the blob.
     * @param string|resource|StreamInterface $content   The content of the blob.
     * @param Models\CreateBlobOptions        $options   The optional parameters.
     *
     * @return Models\PutBlobResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createBlockBlobAsync(
        $container,
        $blob,
        $content,
        Models\CreateBlobOptions $options = null
    ) {
        $body = Psr7\stream_for($content);

        //If the size of the stream is not seekable or larger than the single
        //upload threashold then call concurrent upload. Otherwise call putBlob.
        $promise = null;
        if (!Utilities::isStreamLargerThanSizeOrNotSeekable(
            $body,
            $this->_SingleBlobUploadThresholdInBytes
        )) {
            $promise = $this->createBlockBlobBySingleUploadAsync(
                $container,
                $blob,
                $body,
                $options
            );
        } else {
            // This is for large or failsafe upload
            $promise = $this->createBlockBlobByMultipleUploadAsync(
                $container,
                $blob,
                $body,
                $options
            );
        }

        //return the parsed result, instead of the raw response.
        return $promise->then(
            function ($response) {
                return PutBlobResult::create(
                    HttpFormatter::formatHeaders($response->getHeaders())
                );
            },
            null
        );
    }

    /**
     * Creates promise to create a new block blob or updates the content of an
     * existing block blob. This only supports contents smaller than single
     * upload threashold.
     *
     * Updating an existing block blob overwrites any existing metadata on
     * the blob.
     *
     * @param string                   $container The name of the container.
     * @param string                   $blob      The name of the blob.
     * @param StreamInterface          $content   The content of the blob.
     * @param Models\CreateBlobOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    protected function createBlockBlobBySingleUploadAsync(
        $container,
        $blob,
        $content,
        Models\CreateBlobOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isTrue(
            $options == null ||
            $options instanceof CreateBlobOptions,
            sprintf(
                Resources::INVALID_PARAM_MSG,
                'options',
                get_class(new CreateBlobOptions())
            )
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);

        if (is_null($options)) {
            $options = new CreateBlobOptions();
        }
        
        
        $headers = $this->_addCreateBlobOptionalHeaders($options, $headers);
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_TYPE,
            BlobType::BLOCK_BLOB
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
            $content,
            $options->getRequestOptions()
        );
    }

    /**
     * This method creates the blob blocks. This method will send the request
     * concurrently for better performance.
     *
     * @param  string                   $container  Name of the container
     * @param  string                   $blob       Name of the blob
     * @param  StreamInterface          $content    Content's stream
     * @param  Models\CreateBlobOptions $options    Array that contains
     *                                                     all the option
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function createBlockBlobByMultipleUploadAsync(
        $container,
        $blob,
        $content,
        Models\CreateBlobOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');

        $createBlobBlockOptions = new CreateBlobBlockOptions();
        if (is_null($options)) {
            $options = new CreateBlobOptions();
        }
        
        $method      = Resources::HTTP_PUT;
        $headers     = $this->createBlobBlockHeader($createBlobBlockOptions);
        $postParams  = array();
        $path        = $this->_createPath($container, $blob);

        $blockIds = array();
        // if threshold is lower than 4mb, honor threshold, else use 4mb
        $blockSize = (
            $this->_SingleBlobUploadThresholdInBytes
                < Resources::MB_IN_BYTES_4) ?
            $this->_SingleBlobUploadThresholdInBytes : Resources::MB_IN_BYTES_4;
        $counter = 0;
        //create the generator for requests.
        //this generator also constructs the blockId array on the fly.
        $generator = function () use (
            $content,
            &$blockIds,
            $blockSize,
            $createBlobBlockOptions,
            $method,
            $headers,
            $postParams,
            $path,
            &$counter
        ) {
            //read the content.
            $blockContent = $content->read($blockSize);
            //construct the blockId
            $blockId = base64_encode(
                str_pad($counter++, 6, '0', STR_PAD_LEFT)
            );
            $size = strlen($blockContent);
            if ($size == 0) {
                return null;
            }
            //add the id to array.
            array_push($blockIds, new Block($blockId, 'Uncommitted'));
            $queryParams = $this->createBlobBlockQueryParams(
                $createBlobBlockOptions,
                $blockId
            );
            //return the array of requests.
            return $this->createRequest(
                $method,
                $headers,
                $queryParams,
                $postParams,
                $path,
                $blockContent
            );
        };

        //add number of concurrency if specified int options.
        $requestOptions = $options->getNumberOfConcurrency() == null?
            array() : array($options->getNumberOfConcurrency);

        //Send the request concurrently.
        //Does not need to evaluate the results. If operation not successful,
        //exception will be thrown.
        $putBlobPromise = $this->sendConcurrentAsync(
            array(),
            $generator,
            Resources::STATUS_CREATED,
            $requestOptions
        );

        $selfInstance = $this;
        $commitBlobPromise = $putBlobPromise->then(
            function ($value) use (
                $selfInstance,
                $container,
                $blob,
                &$blockIds,
                $putBlobPromise,
                $options
            ) {
                return $selfInstance->commitBlobBlocksAsync(
                    $container,
                    $blob,
                    $blockIds,
                    CommitBlobBlocksOptions::create($options)
                );
            },
            null
        );

        return $commitBlobPromise;
    }
    
    /**
     * Clears a range of pages from the blob.
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\PageRange              $range     Can be up to the value of
     *                                                 the blob's full size.
     *                                                 Note that ranges must be
     *                                                 aligned to 512 (0-511,
     *                                                 512-1023)
     * @param Models\CreateBlobPagesOptions $options   optional parameters
     *
     * @return Models\CreateBlobPagesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function clearBlobPages(
        $container,
        $blob,
        Models\PageRange $range,
        Models\CreateBlobPagesOptions $options = null
    ) {
        return $this->clearBlobPagesAsync(
            $container,
            $blob,
            $range,
            $options
        )->wait();
    }

    /**
     * Creates promise to clear a range of pages from the blob.
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\PageRange              $range     Can be up to the value of
     *                                                 the blob's full size.
     *                                                 Note that ranges must be
     *                                                 aligned to 512 (0-511,
     *                                                 512-1023)
     * @param Models\CreateBlobPagesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function clearBlobPagesAsync(
        $container,
        $blob,
        Models\PageRange $range,
        Models\CreateBlobPagesOptions $options = null
    ) {
        return $this->_updatePageBlobPagesAsyncImpl(
            PageWriteOption::CLEAR_OPTION,
            $container,
            $blob,
            $range,
            Resources::EMPTY_STRING,
            $options
        );
    }
    
    /**
     * Creates a range of pages to a page blob.
     *
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\PageRange                $range     Can be up to 4 MB in
     *                                                   size. Note that ranges
     *                                                   must be aligned to 512
     *                                                   (0-511, 512-1023)
     * @param string|resource|StreamInterface $content   the blob contents.
     * @param Models\CreateBlobPagesOptions   $options   optional parameters
     *
     * @return Models\CreateBlobPagesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function createBlobPages(
        $container,
        $blob,
        Models\PageRange $range,
        $content,
        Models\CreateBlobPagesOptions $options = null
    ) {
        return $this->createBlobPagesAsync(
            $container,
            $blob,
            $range,
            $content,
            $options
        )->wait();
    }

    /**
     * Creates promise to create a range of pages to a page blob.
     *
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\PageRange                $range     Can be up to 4 MB in
     *                                                   size. Note that ranges
     *                                                   must be aligned to 512
     *                                                   (0-511, 512-1023)
     * @param string|resource|StreamInterface $content   the blob contents.
     * @param Models\CreateBlobPagesOptions   $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function createBlobPagesAsync(
        $container,
        $blob,
        Models\PageRange $range,
        $content,
        Models\CreateBlobPagesOptions $options = null
    ) {
        $contentStream = Psr7\stream_for($content);
        //because the content is at most 4MB long, can retrieve all the data
        //here at once.
        $body = $contentStream->getContents();

        //if the range is not align to 512, throw exception.
        $chunks = (int)($range->getLength() / 512);
        if ($chunks * 512 != $range->getLength()) {
            throw new \RuntimeException(Resources::ERROR_RANGE_NOT_ALIGN_TO_512);
        }

        return $this->_updatePageBlobPagesAsyncImpl(
            PageWriteOption::UPDATE_OPTION,
            $container,
            $blob,
            $range,
            $body,
            $options
        );
    }
    
    /**
     * Creates a new block to be committed as part of a block blob.
     *
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param string                          $blockId   must be less than or
     *                                                   equal to 64 bytes in
     *                                                   size. For a given blob,
     *                                                   the length of the value
     *                                                   specified for the
     *                                                   blockid parameter must
     *                                                   be the same size for
     *                                                   each block.
     * @param resource|string|StreamInterface $content   the blob block contents
     * @param Models\CreateBlobBlockOptions   $options   optional parameters
     *
     * @return Models\PutBlockResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135726.aspx
     */
    public function createBlobBlock(
        $container,
        $blob,
        $blockId,
        $content,
        Models\CreateBlobBlockOptions $options = null
    ) {
        return $this->createBlobBlockAsync(
            $container,
            $blob,
            $blockId,
            $content,
            $options
        )->wait();
    }

    /**
     * Creates a new block to be committed as part of a block blob.
     *
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param string                          $blockId   must be less than or
     *                                                   equal to 64 bytes in
     *                                                   size. For a given blob,
     *                                                   the length of the value
     *                                                   specified for the
     *                                                   blockid parameter must
     *                                                   be the same size for
     *                                                   each block.
     * @param resource|string|StreamInterface $content   the blob block contents
     * @param Models\CreateBlobBlockOptions   $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135726.aspx
     */
    public function createBlobBlockAsync(
        $container,
        $blob,
        $blockId,
        $content,
        Models\CreateBlobBlockOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isString($blockId, 'blockId');
        Validate::notNullOrEmpty($blockId, 'blockId');

        if (is_null($options)) {
            $options = new CreateBlobBlockOptions();
        }
        
        $method         = Resources::HTTP_PUT;
        $headers        = $this->createBlobBlockHeader($options);
        $postParams     = array();
        $queryParams    = $this->createBlobBlockQueryParams($options, $blockId);
        $path           = $this->_createPath($container, $blob);
        $statusCode     = Resources::STATUS_CREATED;
        $contentStream  = Psr7\stream_for($content);
        $body           = $contentStream->getContents();
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            $body,
            $options->getRequestOptions()
        )->then(function ($response) {
            return PutBlockResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        });
    }

    /**
     * create the header for createBlobBlock(s)
     * @param  Models\CreateBlobBlockOptions $options the option of the request
     *
     * @return array
     */
    protected function createBlobBlockHeader(Models\CreateBlobBlockOptions $options = null)
    {
        $headers = array();
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_MD5,
            $options->getContentMD5()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );

        return $headers;
    }

    /**
     * create the query params for createBlobBlock(s)
     * @param  Models\CreateBlobBlockOptions $options the option of the request
     * @param  string                        $blockId the block id of the block.
     *
     * @return array  the constructed query parameters.
     */
    protected function createBlobBlockQueryParams(
        Models\CreateBlobBlockOptions $options,
        $blockId
    ) {
        $queryParams = array();
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'block'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_BLOCKID,
            $blockId
        );

        return $queryParams;
    }

    /**
     * This method writes a blob by specifying the list of block IDs that make up the
     * blob. In order to be written as part of a blob, a block must have been
     * successfully written to the server in a prior createBlobBlock method.
     *
     * You can call Put Block List to update a blob by uploading only those blocks
     * that have changed, then committing the new and existing blocks together.
     * You can do this by specifying whether to commit a block from the committed
     * block list or from the uncommitted block list, or to commit the most recently
     * uploaded version of the block, whichever list it may belong to.
     *
     * @param string                         $container The container name.
     * @param string                         $blob      The blob name.
     * @param Models\BlockList|array         $blockList The block entries.
     * @param Models\CommitBlobBlocksOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Psr7\Response
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179467.aspx
     */
    public function commitBlobBlocks(
        $container,
        $blob,
        $blockList,
        Models\CommitBlobBlocksOptions $options = null
    ) {
        return $this->commitBlobBlocksAsync(
            $container,
            $blob,
            $blockList,
            $options
        )->wait();
    }

    /**
     * This method writes a blob by specifying the list of block IDs that make up the
     * blob. In order to be written as part of a blob, a block must have been
     * successfully written to the server in a prior createBlobBlock method.
     *
     * You can call Put Block List to update a blob by uploading only those blocks
     * that have changed, then committing the new and existing blocks together.
     * You can do this by specifying whether to commit a block from the committed
     * block list or from the uncommitted block list, or to commit the most recently
     * uploaded version of the block, whichever list it may belong to.
     *
     * @param string                         $container The container name.
     * @param string                         $blob      The blob name.
     * @param Models\BlockList|array         $blockList The block entries.
     * @param Models\CommitBlobBlocksOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179467.aspx
     */
    public function commitBlobBlocksAsync(
        $container,
        $blob,
        $blockList,
        Models\CommitBlobBlocksOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isTrue(
            $blockList instanceof BlockList || is_array($blockList),
            sprintf(
                Resources::INVALID_PARAM_MSG,
                'blockList',
                get_class(new BlockList())
            )
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $isArray     = is_array($blockList);
        $blockList   = $isArray ? BlockList::create($blockList) : $blockList;
        $body        = $blockList->toXml($this->dataSerializer);
        
        if (is_null($options)) {
            $options = new CommitBlobBlocksOptions();
        }
        
        $blobContentType     = $options->getBlobContentType();
        $blobContentEncoding = $options->getBlobContentEncoding();
        $blobContentLanguage = $options->getBlobContentLanguage();
        $blobContentMD5      = $options->getBlobContentMD5();
        $blobCacheControl    = $options->getBlobCacheControl();
        $leaseId             = $options->getLeaseId();
        $contentType         = Resources::URL_ENCODED_CONTENT_TYPE;
        
        $metadata = $options->getMetadata();
        $headers  = $this->generateMetadataHeaders($metadata);
        $headers  = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $leaseId
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CACHE_CONTROL,
            $blobCacheControl
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_TYPE,
            $blobContentType
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_ENCODING,
            $blobContentEncoding
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LANGUAGE,
            $blobContentLanguage
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_MD5,
            $blobContentMD5
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            $contentType
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'blocklist'
        );
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            $body,
            $options->getRequestOptions()
        );
    }
    
    /**
     * Retrieves the list of blocks that have been uploaded as part of a block blob.
     *
     * There are two block lists maintained for a blob:
     * 1) Committed Block List: The list of blocks that have been successfully
     *    committed to a given blob with commitBlobBlocks.
     * 2) Uncommitted Block List: The list of blocks that have been uploaded for a
     *    blob using Put Block (REST API), but that have not yet been committed.
     *    These blocks are stored in Windows Azure in association with a blob, but do
     *    not yet form part of the blob.
     *
     * @param string                       $container name of the container
     * @param string                       $blob      name of the blob
     * @param Models\ListBlobBlocksOptions $options   optional parameters
     *
     * @return Models\ListBlobBlocksResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179400.aspx
     */
    public function listBlobBlocks(
        $container,
        $blob,
        Models\ListBlobBlocksOptions $options = null
    ) {
        return $this->listBlobBlocksAsync($container, $blob, $options)->wait();
    }

    /**
     * Creates promise to retrieve the list of blocks that have been uploaded as
     * part of a block blob.
     *
     * There are two block lists maintained for a blob:
     * 1) Committed Block List: The list of blocks that have been successfully
     *    committed to a given blob with commitBlobBlocks.
     * 2) Uncommitted Block List: The list of blocks that have been uploaded for a
     *    blob using Put Block (REST API), but that have not yet been committed.
     *    These blocks are stored in Windows Azure in association with a blob, but do
     *    not yet form part of the blob.
     *
     * @param string                       $container name of the container
     * @param string                       $blob      name of the blob
     * @param Models\ListBlobBlocksOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179400.aspx
     */
    public function listBlobBlocksAsync(
        $container,
        $blob,
        Models\ListBlobBlocksOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new ListBlobBlocksOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_BLOCK_LIST_TYPE,
            $options->getBlockListType()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'blocklist'
        );
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        )->then(function ($response) {
            $parsed = $this->dataSerializer->unserialize($response->getBody());
        
            return ListBlobBlocksResult::create(
                HttpFormatter::formatHeaders($response->getHeaders()),
                $parsed
            );
        }, null);
    }
    
    /**
     * Returns all properties and metadata on the blob.
     *
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\GetBlobPropertiesOptions $options   optional parameters
     *
     * @return Models\GetBlobPropertiesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179394.aspx
     */
    public function getBlobProperties(
        $container,
        $blob,
        Models\GetBlobPropertiesOptions $options = null
    ) {
        return $this->getBlobPropertiesAsync(
            $container,
            $blob,
            $options
        )->wait();
    }
    
    /**
     * Creates promise to return all properties and metadata on the blob.
     *
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\GetBlobPropertiesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179394.aspx
     */
    public function getBlobPropertiesAsync(
        $container,
        $blob,
        Models\GetBlobPropertiesOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_HEAD;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new GetBlobPropertiesOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
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
            $options->getRequestOptions()
        )->then(function ($response) {
            $formattedHeaders = HttpFormatter::formatHeaders($response->getHeaders());
            return GetBlobPropertiesResult::create($formattedHeaders);
        }, null);
    }

    /**
     * Returns all properties and metadata on the blob.
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\GetBlobMetadataOptions $options   optional parameters
     *
     * @return Models\GetBlobMetadataResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179350.aspx
     */
    public function getBlobMetadata(
        $container,
        $blob,
        Models\GetBlobMetadataOptions $options = null
    ) {
        return $this->getBlobMetadataAsync($container, $blob, $options)->wait();
    }

    /**
     * Creates promise to return all properties and metadata on the blob.
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\GetBlobMetadataOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179350.aspx
     */
    public function getBlobMetadataAsync(
        $container,
        $blob,
        Models\GetBlobMetadataOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_HEAD;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new GetBlobMetadataOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        )->then(function ($response) {
            $responseHeaders = HttpFormatter::formatHeaders($response->getHeaders());
            $metadata = Utilities::getMetadataArray($responseHeaders);
            return GetBlobMetadataResult::create($responseHeaders, $metadata);
        });
    }
    
    /**
     * Returns a list of active page ranges for a page blob. Active page ranges are
     * those that have been populated with data.
     *
     * @param string                           $container name of the container
     * @param string                           $blob      name of the blob
     * @param Models\ListPageBlobRangesOptions $options   optional parameters
     *
     * @return Models\ListPageBlobRangesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691973.aspx
     */
    public function listPageBlobRanges(
        $container,
        $blob,
        Models\ListPageBlobRangesOptions $options = null
    ) {
        return $this->listPageBlobRangesAsync(
            $container,
            $blob,
            $options
        )->wait();
    }

    /**
     * Creates promise to return a list of active page ranges for a page blob.
     * Active page ranges are those that have been populated with data.
     *
     * @param string                           $container name of the container
     * @param string                           $blob      name of the blob
     * @param Models\ListPageBlobRangesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691973.aspx
     */
    public function listPageBlobRangesAsync(
        $container,
        $blob,
        Models\ListPageBlobRangesOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new ListPageBlobRangesOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $headers = $this->_addOptionalRangeHeader(
            $headers,
            $options->getRangeStart(),
            $options->getRangeEnd()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'pagelist'
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
            $options->getRequestOptions()
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return ListPageBlobRangesResult::create(
                HttpFormatter::formatHeaders($response->getHeaders()),
                $parsed
            );
        }, null);
    }
    
    /**
     * Sets system properties defined for a blob.
     *
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\SetBlobPropertiesOptions $options   optional parameters
     *
     * @return Models\SetBlobPropertiesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691966.aspx
     */
    public function setBlobProperties(
        $container,
        $blob,
        Models\SetBlobPropertiesOptions $options = null
    ) {
        return $this->setBlobPropertiesAsync(
            $container,
            $blob,
            $options
        )->wait();
    }

    /**
     * Creates promise to set system properties defined for a blob.
     *
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\SetBlobPropertiesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691966.aspx
     */
    public function setBlobPropertiesAsync(
        $container,
        $blob,
        Models\SetBlobPropertiesOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new SetBlobPropertiesOptions();
        }
        
        $blobContentType     = $options->getBlobContentType();
        $blobContentEncoding = $options->getBlobContentEncoding();
        $blobContentLanguage = $options->getBlobContentLanguage();
        $blobContentLength   = $options->getBlobContentLength();
        $blobContentMD5      = $options->getBlobContentMD5();
        $blobCacheControl    = $options->getBlobCacheControl();
        $leaseId             = $options->getLeaseId();
        $sNumberAction       = $options->getSequenceNumberAction();
        $sNumber             = $options->getSequenceNumber();
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $leaseId
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CACHE_CONTROL,
            $blobCacheControl
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_TYPE,
            $blobContentType
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_ENCODING,
            $blobContentEncoding
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LANGUAGE,
            $blobContentLanguage
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LENGTH,
            $blobContentLength
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_MD5,
            $blobContentMD5
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_SEQUENCE_NUMBER_ACTION,
            $sNumberAction
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_SEQUENCE_NUMBER,
            $sNumber
        );

        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'properties');
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
            $options->getRequestOptions()
        )->then(function ($response) {
            return SetBlobPropertiesResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }
    
    /**
     * Sets metadata headers on the blob.
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param array                         $metadata  key/value pair representation
     * @param Models\SetBlobMetadataOptions $options   optional parameters
     *
     * @return Models\SetBlobMetadataResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179414.aspx
     */
    public function setBlobMetadata(
        $container,
        $blob,
        array $metadata,
        Models\SetBlobMetadataOptions $options = null
    ) {
        return $this->setBlobMetadataAsync(
            $container,
            $blob,
            $metadata,
            $options
        )->wait();
    }

    /**
     * Creates promise to set metadata headers on the blob.
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param array                         $metadata  key/value pair representation
     * @param Models\SetBlobMetadataOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179414.aspx
     */
    public function setBlobMetadataAsync(
        $container,
        $blob,
        array $metadata,
        Models\SetBlobMetadataOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Utilities::validateMetadata($metadata);
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new SetBlobMetadataOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        $headers = $this->addMetadataHeaders($headers, $metadata);
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        )->then(function ($response) {
            return SetBlobMetadataResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }

    /**
     * Downloads a blob to a file, the result contains its metadata and
     * properties. The result will not contain a stream pointing to the
     * content of the file.
     *
     * @param string                $path      The path and name of the file
     * @param string                $container name of the container
     * @param string                $blob      name of the blob
     * @param Models\GetBlobOptions $options   optional parameters
     *
     * @return Models\GetBlobResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179440.aspx
     */
    public function saveBlobToFile(
        $path,
        $container,
        $blob,
        Models\GetBlobOptions $options = null
    ) {
        return $this->saveBlobToFileAsync(
            $path,
            $container,
            $blob,
            $options
        )->wait();
    }

    /**
     * Creates promise to download a blob to a file, the result contains its
     * metadata and properties. The result will not contain a stream pointing
     * to the content of the file.
     *
     * @param string                $path      The path and name of the file
     * @param string                $container name of the container
     * @param string                $blob      name of the blob
     * @param Models\GetBlobOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179440.aspx
     */
    public function saveBlobToFileAsync(
        $path,
        $container,
        $blob,
        Models\GetBlobOptions $options = null
    ) {
        $resource = fopen($path, 'w+');
        if ($resource == null) {
            throw new \Exception(Resources::ERROR_FILE_COULD_NOT_BE_OPENED);
        }
        return $this->getBlobAsync($container, $blob, $options)->then(
            function ($result) use ($path, $resource) {
                $content = $result->getContentStream();
                while (!feof($content)) {
                    fwrite(
                        $resource,
                        stream_get_contents($content, Resources::MB_IN_BYTES_4)
                    );
                }
                
                $content = null;
                fclose($resource);
        
                return $result;
            },
            null
        );
    }
    
    /**
     * Reads or downloads a blob from the system, including its metadata and
     * properties.
     *
     * @param string                $container name of the container
     * @param string                $blob      name of the blob
     * @param Models\GetBlobOptions $options   optional parameters
     *
     * @return Models\GetBlobResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179440.aspx
     */
    public function getBlob(
        $container,
        $blob,
        Models\GetBlobOptions $options = null
    ) {
        return $this->getBlobAsync($container, $blob, $options)->wait();
    }

    /**
     * Creates promise to read or download a blob from the system, including its
     * metadata and properties.
     *
     * @param string                $container name of the container
     * @param string                $blob      name of the blob
     * @param Models\GetBlobOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179440.aspx
     */
    public function getBlobAsync(
        $container,
        $blob,
        Models\GetBlobOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new GetBlobOptions();
        }
        
        $getMD5  = $options->getComputeRangeMD5();
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        $headers = $this->_addOptionalRangeHeader(
            $headers,
            $options->getRangeStart(),
            $options->getRangeEnd()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_RANGE_GET_CONTENT_MD5,
            $getMD5 ? 'true' : null
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );

        $requestOptions = $options->getRequestOptions();
        //setting stream to true to enable streaming
        $requestOptions['stream'] = true;
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            array(Resources::STATUS_OK, Resources::STATUS_PARTIAL_CONTENT),
            Resources::EMPTY_STRING,
            $requestOptions
        )->then(function ($response) {
            $metadata = Utilities::getMetadataArray(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        
            return GetBlobResult::create(
                HttpFormatter::formatHeaders($response->getHeaders()),
                $response->getBody(),
                $metadata
            );
        });
    }
    
    /**
     * Deletes a blob or blob snapshot.
     *
     * Note that if the snapshot entry is specified in the $options then only this
     * blob snapshot is deleted. To delete all blob snapshots, do not set Snapshot
     * and just set getDeleteSnaphotsOnly to true.
     *
     * @param string                   $container name of the container
     * @param string                   $blob      name of the blob
     * @param Models\DeleteBlobOptions $options   optional parameters
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179413.aspx
     */
    public function deleteBlob(
        $container,
        $blob,
        Models\DeleteBlobOptions $options = null
    ) {
        $this->deleteBlobAsync($container, $blob, $options)->wait();
    }

    /**
     * Creates promise to delete a blob or blob snapshot.
     *
     * Note that if the snapshot entry is specified in the $options then only this
     * blob snapshot is deleted. To delete all blob snapshots, do not set Snapshot
     * and just set getDeleteSnaphotsOnly to true.
     *
     * @param string                   $container name of the container
     * @param string                   $blob      name of the blob
     * @param Models\DeleteBlobOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179413.aspx
     */
    public function deleteBlobAsync(
        $container,
        $blob,
        Models\DeleteBlobOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new DeleteBlobOptions();
        }
        
        if (is_null($options->getSnapshot())) {
            $delSnapshots = $options->getDeleteSnaphotsOnly() ? 'only' : 'include';
            $this->addOptionalHeader(
                $headers,
                Resources::X_MS_DELETE_SNAPSHOTS,
                $delSnapshots
            );
        } else {
            $this->addOptionalQueryParam(
                $queryParams,
                Resources::QP_SNAPSHOT,
                $options->getSnapshot()
            );
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
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
            $options->getRequestOptions()
        );
    }
    
    /**
     * Creates a snapshot of a blob.
     *
     * @param string                           $container The name of the container.
     * @param string                           $blob      The name of the blob.
     * @param Models\CreateBlobSnapshotOptions $options   The optional parameters.
     *
     * @return Models\CreateBlobSnapshotResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691971.aspx
     */
    public function createBlobSnapshot(
        $container,
        $blob,
        Models\CreateBlobSnapshotOptions $options = null
    ) {
        return $this->createBlobSnapshotAsync(
            $container,
            $blob,
            $options
        )->wait();
    }

    /**
     * Creates promise to create a snapshot of a blob.
     *
     * @param string                           $container The name of the container.
     * @param string                           $blob      The name of the blob.
     * @param Models\CreateBlobSnapshotOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691971.aspx
     */
    public function createBlobSnapshotAsync(
        $container,
        $blob,
        Models\CreateBlobSnapshotOptions $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method             = Resources::HTTP_PUT;
        $headers            = array();
        $postParams         = array();
        $queryParams        = array();
        $path               = $this->_createPath($container, $blob);
        
        if (is_null($options)) {
            $options = new CreateBlobSnapshotOptions();
        }
        
        $queryParams[Resources::QP_COMP] = 'snapshot';
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        $headers = $this->addMetadataHeaders($headers, $options->getMetadata());
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        )->then(function ($response) {
            return CreateBlobSnapshotResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }
    
    /**
     * Copies a source blob to a destination blob within the same storage account.
     *
     * @param string                 $destinationContainer name of the destination
     * container
     * @param string                 $destinationBlob      name of the destination
     * blob
     * @param string                 $sourceContainer      name of the source
     * container
     * @param string                 $sourceBlob           name of the source
     * blob
     * @param Models\CopyBlobOptions $options              optional parameters
     *
     * @return Models\CopyBlobResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd894037.aspx
     */
    public function copyBlob(
        $destinationContainer,
        $destinationBlob,
        $sourceContainer,
        $sourceBlob,
        Models\CopyBlobOptions $options = null
    ) {
        return $this->copyBlobAsync(
            $destinationContainer,
            $destinationBlob,
            $sourceContainer,
            $sourceBlob,
            $options
        )->wait();
    }

    /**
     * Creates promise to copy a source blob to a destination blob within the
     * same storage account.
     *
     * @param string                 $destinationContainer name of the destination
     * container
     * @param string                 $destinationBlob      name of the destination
     * blob
     * @param string                 $sourceContainer      name of the source
     * container
     * @param string                 $sourceBlob           name of the source
     * blob
     * @param Models\CopyBlobOptions $options              optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd894037.aspx
     */
    public function copyBlobAsync(
        $destinationContainer,
        $destinationBlob,
        $sourceContainer,
        $sourceBlob,
        Models\CopyBlobOptions $options = null
    ) {
        $method              = Resources::HTTP_PUT;
        $headers             = array();
        $postParams          = array();
        $queryParams         = array();
        $destinationBlobPath = $this->_createPath(
            $destinationContainer,
            $destinationBlob
        );
        
        if (is_null($options)) {
            $options = new CopyBlobOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $sourceBlobPath = $this->_getCopyBlobSourceName(
            $sourceContainer,
            $sourceBlob,
            $options
        );
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        
        $headers = $this->addOptionalSourceAccessConditionHeader(
            $headers,
            $options->getSourceAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_COPY_SOURCE,
            $sourceBlobPath
        );
        
        $headers = $this->addMetadataHeaders($headers, $options->getMetadata());
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_SOURCE_LEASE_ID,
            $options->getSourceLeaseId()
        );
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $destinationBlobPath,
            Resources::STATUS_ACCEPTED,
            Resources::EMPTY_STRING,
            $options->getRequestOptions()
        )->then(function ($response) {
            return CopyBlobResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }
        
    /**
     * Establishes an exclusive one-minute write lock on a blob. To write to a locked
     * blob, a client must provide a lease ID.
     *
     * @param string                     $container name of the container
     * @param string                     $blob      name of the blob
     * @param Models\AcquireLeaseOptions $options   optional parameters
     *
     * @return Models\LeaseBlobResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function acquireLease(
        $container,
        $blob,
        Models\AcquireLeaseOptions $options = null
    ) {
        return $this->acquireLeaseAsync($container, $blob, $options)->wait();
    }

    /**
     * Creates promise to establish an exclusive one-minute write lock on a blob.
     * To write to a locked blob, a client must provide a lease ID.
     *
     * @param string                     $container name of the container
     * @param string                     $blob      name of the blob
     * @param Models\AcquireLeaseOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function acquireLeaseAsync(
        $container,
        $blob,
        Models\AcquireLeaseOptions $options = null
    ) {
        return $this->_putLeaseAsyncImpl(
            LeaseMode::ACQUIRE_ACTION,
            $container,
            $blob,
            null /* leaseId */,
            self::getStatusCodeOfLeaseAction(LeaseMode::ACQUIRE_ACTION),
            is_null($options) ? new AcquireLeaseOptions() : $options,
            is_null($options) ? null : $options->getAccessCondition()
        )->then(function ($response) {
            return LeaseBlobResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }
    
    /**
     * Renews an existing lease
     *
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return Models\LeaseBlobResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function renewLease(
        $container,
        $blob,
        $leaseId,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->renewLeaseAsync(
            $container,
            $blob,
            $leaseId,
            $options
        )->wait();
    }

    /**
     * Creates promise to renew an existing lease
     *
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function renewLeaseAsync(
        $container,
        $blob,
        $leaseId,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->_putLeaseAsyncImpl(
            LeaseMode::RENEW_ACTION,
            $container,
            $blob,
            $leaseId,
            self::getStatusCodeOfLeaseAction(LeaseMode::RENEW_ACTION),
            is_null($options) ? new BlobServiceOptions() : $options
        )->then(function ($response) {
            return LeaseBlobResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }

    /**
     * Frees the lease if it is no longer needed so that another client may
     * immediately acquire a lease against the blob.
     *
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function releaseLease(
        $container,
        $blob,
        $leaseId,
        Models\BlobServiceOptions $options = null
    ) {
        $this->releaseLeaseAsync($container, $blob, $leaseId, $options)->wait();
    }
    
    /**
     * Creates promise to free the lease if it is no longer needed so that
     * another client may immediately acquire a lease against the blob.
     *
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function releaseLeaseAsync(
        $container,
        $blob,
        $leaseId,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->_putLeaseAsyncImpl(
            LeaseMode::RELEASE_ACTION,
            $container,
            $blob,
            $leaseId,
            self::getStatusCodeOfLeaseAction(LeaseMode::RELEASE_ACTION),
            is_null($options) ? new BlobServiceOptions() : $options
        );
    }
    
    /**
     * Ends the lease but ensure that another client cannot acquire a new lease until
     * the current lease period has expired.
     *
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return BreakLeaseResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function breakLease(
        $container,
        $blob,
        $leaseId,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->breakLeaseAsync(
            $container,
            $blob,
            $leaseId,
            $options
        )->wait();
    }

    /**
     * Creates promise to end the lease but ensure that another client cannot
     * acquire a new lease until the current lease period has expired.
     *
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function breakLeaseAsync(
        $container,
        $blob,
        $leaseId,
        Models\BlobServiceOptions $options = null
    ) {
        return $this->_putLeaseAsyncImpl(
            LeaseMode::BREAK_ACTION,
            $container,
            $blob,
            $leaseId,
            self::getStatusCodeOfLeaseAction(LeaseMode::BREAK_ACTION),
            is_null($options) ? new BlobServiceOptions() : $options
        )->then(function ($response) {
            return BreakLeaseResult::create(
                HttpFormatter::formatHeaders($response->getHeaders())
            );
        }, null);
    }

    /**
     * Adds optional header to headers if set
     *
     * @param array                  $headers         The array of request headers.
     * @param Models\AccessCondition $accessCondition The access condition object.
     *
     * @return array
     */
    public function addOptionalAccessConditionHeader(
        array $headers,
        Models\AccessCondition $accessCondition = null
    ) {
        if (!is_null($accessCondition)) {
            $header = $accessCondition->getHeader();

            if ($header != Resources::EMPTY_STRING) {
                $value = $accessCondition->getValue();
                if ($value instanceof \DateTime) {
                    $value = gmdate(
                        Resources::AZURE_DATE_FORMAT,
                        $value->getTimestamp()
                    );
                }
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    /**
     * Adds optional header to headers if set
     *
     * @param array                  $headers         The array of request headers.
     * @param Models\AccessCondition $accessCondition The access condition object.
     *
     * @return array
     */
    public function addOptionalSourceAccessConditionHeader(
        array $headers,
        Models\AccessCondition $accessCondition = null
    ) {
        if (!is_null($accessCondition)) {
            $header     = $accessCondition->getHeader();
            $headerName = null;
            if (!empty($header)) {
                switch ($header) {
                    case Resources::IF_MATCH:
                        $headerName = Resources::X_MS_SOURCE_IF_MATCH;
                        break;
                    case Resources::IF_UNMODIFIED_SINCE:
                        $headerName = Resources::X_MS_SOURCE_IF_UNMODIFIED_SINCE;
                        break;
                    case Resources::IF_MODIFIED_SINCE:
                        $headerName = Resources::X_MS_SOURCE_IF_MODIFIED_SINCE;
                        break;
                    case Resources::IF_NONE_MATCH:
                        $headerName = Resources::X_MS_SOURCE_IF_NONE_MATCH;
                        break;
                    default:
                        throw new \Exception(Resources::INVALID_ACH_MSG);
                        break;
                }
            }
            $value = $accessCondition->getValue();
            if ($value instanceof \DateTime) {
                $value = gmdate(
                    Resources::AZURE_DATE_FORMAT,
                    $value->getTimestamp()
                );
            }

            $this->addOptionalHeader($headers, $headerName, $value);
        }

        return $headers;
    }
}
