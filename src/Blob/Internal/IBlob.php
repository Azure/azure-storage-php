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
 * @package   MicrosoftAzure\Storage\Blob\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Blob\Internal;

use MicrosoftAzure\Storage\Blob\Models as BlobModels;
use MicrosoftAzure\Storage\Common\Models as CommonModels;

/**
 * This interface has all REST APIs provided by Windows Azure for Blob service.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 * @see       http://msdn.microsoft.com/en-us/library/windowsazure/dd135733.aspx
 */
interface IBlob
{
    /**
    * Gets the properties of the Blob service.
    *
    * @param BlobModels\BlobServiceOptions $options optional blob service options.
    *
    * @return CommonModels\GetServicePropertiesResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452239.aspx
    */
    public function getServiceProperties(BlobModels\BlobServiceOptions $options = null);

    /**
     * Creates promise to get the properties of the Blob service.
     *
     * @param BlobModels\BlobServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452239.aspx
     */
    public function getServicePropertiesAsync(
        BlobModels\BlobServiceOptions $options = null
    );

    /**
    * Sets the properties of the Blob service.
    *
    * @param CommonModels\ServiceProperties  $serviceProperties new service properties
    * @param BlobModels\BlobServiceOptions   $options           optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452235.aspx
    */
    public function setServiceProperties(
        CommonModels\ServiceProperties $serviceProperties,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
     * Creates the promise to set the properties of the Blob service.
     *
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     *
     * @param CommonModels\ServiceProperties $serviceProperties new service properties.
     * @param BlobModels\BlobServiceOptions  $options           optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452235.aspx
     */
    public function setServicePropertiesAsync(
        CommonModels\ServiceProperties $serviceProperties,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
    * Lists all of the containers in the given storage account.
    *
    * @param BlobModels\ListContainersOptions $options optional parameters
    *
    * @return BlobModels\ListContainersResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179352.aspx
    */
    public function listContainers(BlobModels\ListContainersOptions $options = null);

    /**
     * Create a promise for lists all of the containers in the given
     * storage account.
     *
     * @param  BlobModels\ListContainersOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listContainersAsync(
        BlobModels\ListContainersOptions $options = null
    );

    /**
    * Creates a new container in the given storage account.
    *
    * @param string                            $container name
    * @param BlobModels\CreateContainerOptions $options   optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
    */
    public function createContainer(
        $container,
        BlobModels\CreateContainerOptions $options = null
    );

    /**
     * Creates a new container in the given storage account.
     *
     * @param string                            $container The container name.
     * @param BlobModels\CreateContainerOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
     */
    public function createContainerAsync(
        $container,
        BlobModels\CreateContainerOptions $options = null
    );

    /**
    * Creates a new container in the given storage account.
    *
    * @param string                            $container name
    * @param BlobModels\DeleteContainerOptions $options   optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179408.aspx
    */
    public function deleteContainer(
        $container,
        BlobModels\DeleteContainerOptions $options = null
    );

    /**
     * Create a promise for deleting a container.
     *
     * @param  string                             $container name of the container
     * @param  BlobModels\DeleteContainerOptions  $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteContainerAsync(
        $container,
        BlobModels\DeleteContainerOptions $options = null
    );

    /**
    * Returns all properties and metadata on the container.
    *
    * @param string                        $container name
    * @param BlobModels\BlobServiceOptions $options   optional parameters
    *
    * @return BlobModels\GetContainerPropertiesResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179370.aspx
    */
    public function getContainerProperties(
        $container,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
     * Create promise to return all properties and metadata on the container.
     *
     * @param string                        $container name
     * @param BlobModels\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179370.aspx
     */
    public function getContainerPropertiesAsync(
        $container,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
    * Returns only user-defined metadata for the specified container.
    *
    * @param string                        $container name
    * @param BlobModels\BlobServiceOptions $options   optional parameters
    *
    * @return BlobModels\GetContainerPropertiesResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691976.aspx
    */
    public function getContainerMetadata(
        $container,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
     * Create promise to return only user-defined metadata for the specified
     * container.
     *
     * @param string                        $container name
     * @param BlobModels\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691976.aspx
     */
    public function getContainerMetadataAsync(
        $container,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
    * Gets the access control list (ACL) and any container-level access policies
    * for the container.
    *
    * @param string                        $container name
    * @param BlobModels\BlobServiceOptions $options   optional parameters
    *
    * @return BlobModels\GetContainerACLResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179469.aspx
    */
    public function getContainerAcl(
        $container,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
     * Creates the promise to get the access control list (ACL) and any
     * container-level access policies for the container.
     *
     * @param string                        $container The container name.
     * @param BlobModels\BlobServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179469.aspx
     */
    public function getContainerAclAsync(
        $container,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
    * Sets the ACL and any container-level access policies for the container.
    *
    * @param string                        $container name
    * @param BlobModels\ContainerACL       $acl       access control list for container
    * @param BlobModels\BlobServiceOptions $options   optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179391.aspx
    */
    public function setContainerAcl(
        $container,
        BlobModels\ContainerACL $acl,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
     * Creates promise to set the ACL and any container-level access policies
     * for the container.
     *
     * @param string                        $container name
     * @param BlobModels\ContainerACL       $acl       access control list for container
     * @param BlobModels\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179391.aspx
     */
    public function setContainerAclAsync(
        $container,
        BlobModels\ContainerACL $acl,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
    * Sets metadata headers on the container.
    *
    * @param string                              $container name
    * @param array                               $metadata  metadata key/value pair.
    * @param BlobModels\SetContainerMetadataOptions $options   optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179362.aspx
    */
    public function setContainerMetadata(
        $container,
        array $metadata,
        BlobModels\SetContainerMetadataOptions $options = null
    );

    /**
     * Sets metadata headers on the container.
     *
     * @param string                                 $container name
     * @param array                                  $metadata  metadata key/value pair.
     * @param BlobModels\SetContainerMetadataOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179362.aspx
     */
    public function setContainerMetadataAsync(
        $container,
        array $metadata,
        BlobModels\SetContainerMetadataOptions $options = null
    );

    /**
    * Lists all of the blobs in the given container.
    *
    * @param string                      $container name
    * @param BlobModels\ListBlobsOptions $options   optional parameters
    *
    * @return BlobModels\ListBlobsResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135734.aspx
    */
    public function listBlobs(
        $container,
        BlobModels\ListBlobsOptions $options = null
    );

    /**
     * Creates promise to list all of the blobs in the given container.
     *
     * @param string                      $container The container name.
     * @param BlobModels\ListBlobsOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135734.aspx
     */
    public function listBlobsAsync(
        $container,
        BlobModels\ListBlobsOptions $options = null
    );

    /**
    * Creates a new page blob. Note that calling createPageBlob to create a page
    * blob only initializes the blob.
    * To add content to a page blob, call createBlobPages method.
    *
    * @param string                       $container name of the container
    * @param string                       $blob      name of the blob
    * @param int                          $length    specifies the maximum size
    * for the page blob, up to 1 TB. The page blob size must be aligned to
    * a 512-byte boundary.
    * @param BlobModels\CreateBlobOptions $options   optional parameters
    *
    * @return BlobModels\CopyBlobResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
    */
    public function createPageBlob(
        $container,
        $blob,
        $length,
        BlobModels\CreateBlobOptions $options = null
    );

    /**
     * Creates promise to create a new page blob. Note that calling
     * createPageBlob to create a page blob only initializes the blob.
     * To add content to a page blob, call createBlobPages method.
     *
     * @param string                       $container The container name.
     * @param string                       $blob      The blob name.
     * @param integer                      $length    Specifies the maximum size
     *                                                for the page blob, up to
     *                                                1 TB. The page blob size
     *                                                must be aligned to a
     *                                                512-byte boundary.
     * @param BlobModels\CreateBlobOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createPageBlobAsync(
        $container,
        $blob,
        $length,
        BlobModels\CreateBlobOptions $options = null
    );

    /**
    * Creates a new block blob or updates the content of an existing block blob.
    * Updating an existing block blob overwrites any existing metadata on the blob.
    * Partial updates are not supported with createBlockBlob; the content of the
    * existing blob is overwritten with the content of the new blob. To perform a
    * partial update of the content of a block blob, use the createBlockList method.
    *
    * @param string                       $container name of the container
    * @param string                       $blob      name of the blob
    * @param string                       $content   content of the blob
    * @param BlobModels\CreateBlobOptions $options   optional parameters
    *
    * @return BlobModels\CopyBlobResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
    */
    public function createBlockBlob(
        $container,
        $blob,
        $content,
        BlobModels\CreateBlobOptions $options = null
    );

    /**
    * Clears a range of pages from the blob.
    *
    * @param string                            $container name of the container
    * @param string                            $blob      name of the blob
    * @param BlobModels\PageRange              $range     Can be up to the value
    * of the blob's full size.
    * @param BlobModels\CreateBlobPagesOptions $options   optional parameters
    *
    * @return BlobModels\CreateBlobPagesResult.
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
    */
    public function clearBlobPages(
        $container,
        $blob,
        BlobModels\PageRange $range,
        BlobModels\CreateBlobPagesOptions $options = null
    );

    /**
     * Creates promise to clear a range of pages from the blob.
     *
     * @param string                            $container name of the container
     * @param string                            $blob      name of the blob
     * @param BlobModels\PageRange              $range     Can be up to the value
     *                                                     of the blob's full size.
     *                                                     Note that ranges must be
     *                                                     aligned to 512 (0-511,
     *                                                     512-1023)
     * @param BlobModels\CreateBlobPagesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function clearBlobPagesAsync(
        $container,
        $blob,
        BlobModels\PageRange $range,
        BlobModels\CreateBlobPagesOptions $options = null
    );

    /**
    * Creates a range of pages to a page blob.
    *
    * @param string                            $container name of the container
    * @param string                            $blob      name of the blob
    * @param BlobModels\PageRange              $range     Can be up to 4 MB in size
    * @param string                            $content   the blob contents
    * @param BlobModels\CreateBlobPagesOptions $options   optional parameters
    *
    * @return BlobModels\CreateBlobPagesResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
    */
    public function createBlobPages(
        $container,
        $blob,
        BlobModels\PageRange $range,
        $content,
        BlobModels\CreateBlobPagesOptions $options = null
    );

    /**
     * Creates promise to create a range of pages to a page blob.
     *
     * @param string                            $container name of the container
     * @param string                            $blob      name of the blob
     * @param BlobModels\PageRange              $range     Can be up to 4 MB in
     *                                                     size. Note that ranges
     *                                                     must be aligned to 512
     *                                                     (0-511, 512-1023)
     * @param string|resource|StreamInterface   $content   the blob contents.
     * @param BlobModels\CreateBlobPagesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function createBlobPagesAsync(
        $container,
        $blob,
        BlobModels\PageRange $range,
        $content,
        BlobModels\CreateBlobPagesOptions $options = null
    );

    /**
    * Creates a new block to be committed as part of a block blob.
    *
    * @param string                            $container name of the container
    * @param string                            $blob      name of the blob
    * @param string                            $blockId   must be less than or equal to
    * 64 bytes in size. For a given blob, the length of the value specified for the
    * blockid parameter must be the same size for each block.
    * @param string                            $content   the blob block contents
    * @param BlobModels\CreateBlobBlockOptions $options   optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135726.aspx
    */
    public function createBlobBlock(
        $container,
        $blob,
        $blockId,
        $content,
        BlobModels\CreateBlobBlockOptions $options = null
    );

    /**
     * Creates a new block to be committed as part of a block blob.
     *
     * @param string                              $container name of the container
     * @param string                              $blob      name of the blob
     * @param string                              $blockId   must be less than or
     *                                                       equal to 64 bytes in
     *                                                       size. For a given
     *                                                       blob, the length of
     *                                                       the value specified
     *                                                       for the blockid
     *                                                       parameter must
     *                                                       be the same size for
     *                                                       each block.
     * @param resource|string|StreamInterface     $content   the blob block contents
     * @param BlobModels\CreateBlobBlockOptions   $options   optional parameters
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
        BlobModels\CreateBlobBlockOptions $options = null
    );

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
    * @param string                             $container name of the container
    * @param string                             $blob      name of the blob
    * @param BlobModels\BlockList|array         $blockList the block list entries
    * @param BlobModels\CommitBlobBlocksOptions $options   optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179467.aspx
    */
    public function commitBlobBlocks(
        $container,
        $blob,
        $blockList,
        BlobModels\CommitBlobBlocksOptions $options = null
    );

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
     * @param string                             $container The container name.
     * @param string                             $blob      The blob name.
     * @param BlobModels\BlockList|array         $blockList The block entries.
     * @param BlobModels\CommitBlobBlocksOptions $options   The optional
     *                                                      parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179467.aspx
     */
    public function commitBlobBlocksAsync(
        $container,
        $blob,
        $blockList,
        BlobModels\CommitBlobBlocksOptions $options = null
    );

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
    * @param string                           $container name of the container
    * @param string                           $blob      name of the blob
    * @param BlobModels\ListBlobBlocksOptions $options   optional parameters
    *
    * @return BlobModels\ListBlobBlocksResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179400.aspx
    */
    public function listBlobBlocks(
        $container,
        $blob,
        BlobModels\ListBlobBlocksOptions $options = null
    );

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
     * @param string                           $container name of the container
     * @param string                           $blob      name of the blob
     * @param BlobModels\ListBlobBlocksOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179400.aspx
     */
    public function listBlobBlocksAsync(
        $container,
        $blob,
        BlobModels\ListBlobBlocksOptions $options = null
    );

    /**
    * Returns all properties and metadata on the blob.
    *
    * @param string                              $container name of the container
    * @param string                              $blob      name of the blob
    * @param BlobModels\GetBlobPropertiesOptions $options   optional parameters
    *
    * @return BlobModels\GetBlobPropertiesResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179394.aspx
    */
    public function getBlobProperties(
        $container,
        $blob,
        BlobModels\GetBlobPropertiesOptions $options = null
    );

    /**
     * Creates promise to return all properties and metadata on the blob.
     *
     * @param string                              $container name of the container
     * @param string                              $blob      name of the blob
     * @param BlobModels\GetBlobPropertiesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179394.aspx
     */
    public function getBlobPropertiesAsync(
        $container,
        $blob,
        BlobModels\GetBlobPropertiesOptions $options = null
    );

    /**
    * Returns all properties and metadata on the blob.
    *
    * @param string                            $container name of the container
    * @param string                            $blob      name of the blob
    * @param BlobModels\GetBlobMetadataOptions $options   optional parameters
    *
    * @return BlobModels\GetBlobMetadataResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179350.aspx
    */
    public function getBlobMetadata(
        $container,
        $blob,
        BlobModels\GetBlobMetadataOptions $options = null
    );

    /**
     * Creates promise to return all properties and metadata on the blob.
     *
     * @param string                            $container name of the container
     * @param string                            $blob      name of the blob
     * @param BlobModels\GetBlobMetadataOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179350.aspx
     */
    public function getBlobMetadataAsync(
        $container,
        $blob,
        BlobModels\GetBlobMetadataOptions $options = null
    );

    /**
    * Returns a list of active page ranges for a page blob. Active page ranges are
    * those that have been populated with data.
    *
    * @param string                               $container name of the container
    * @param string                               $blob      name of the blob
    * @param BlobModels\ListPageBlobRangesOptions $options   optional parameters
    *
    * @return BlobModels\ListPageBlobRangesResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691973.aspx
    */
    public function listPageBlobRanges(
        $container,
        $blob,
        BlobModels\ListPageBlobRangesOptions $options = null
    );

    /**
     * Creates promise to return a list of active page ranges for a page blob.
     * Active page ranges are those that have been populated with data.
     *
     * @param string                               $container name of the
     *                                                        container
     * @param string                               $blob      name of the blob
     * @param BlobModels\ListPageBlobRangesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691973.aspx
     */
    public function listPageBlobRangesAsync(
        $container,
        $blob,
        BlobModels\ListPageBlobRangesOptions $options = null
    );

    /**
    * Sets system properties defined for a blob.
    *
    * @param string                              $container name of the container
    * @param string                              $blob      name of the blob
    * @param BlobModels\SetBlobPropertiesOptions $options   optional parameters
    *
    * @return BlobModels\SetBlobPropertiesResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691966.aspx
    */
    public function setBlobProperties(
        $container,
        $blob,
        BlobModels\SetBlobPropertiesOptions $options = null
    );

    /**
     * Creates promise to set system properties defined for a blob.
     *
     * @param string                              $container name of the container
     * @param string                              $blob      name of the blob
     * @param BlobModels\SetBlobPropertiesOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691966.aspx
     */
    public function setBlobPropertiesAsync(
        $container,
        $blob,
        BlobModels\SetBlobPropertiesOptions $options = null
    );

    /**
    * Sets metadata headers on the blob.
    *
    * @param string                         $container name of the container
    * @param string                         $blob      name of the blob
    * @param array                          $metadata  key/value pair representation
    * @param BlobModels\SetBlobMetadataOptions $options   optional parameters
    *
    * @return BlobModels\SetBlobMetadataResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179414.aspx
    */
    public function setBlobMetadata(
        $container,
        $blob,
        array $metadata,
        BlobModels\SetBlobMetadataOptions $options = null
    );

    /**
     * Creates promise to set metadata headers on the blob.
     *
     * @param string                            $container name of the container
     * @param string                            $blob      name of the blob
     * @param array                             $metadata  key/value pair representation
     * @param BlobModels\SetBlobMetadataOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179414.aspx
     */
    public function setBlobMetadataAsync(
        $container,
        $blob,
        array $metadata,
        BlobModels\SetBlobMetadataOptions $options = null
    );

    /**
    * Reads or downloads a blob from the system, including its metadata and
    * properties.
    *
    * @param string                    $container name of the container
    * @param string                    $blob      name of the blob
    * @param BlobModels\GetBlobOptions $options   optional parameters
    *
    * @return BlobModels\GetBlobResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179440.aspx
    */
    public function getBlob(
        $container,
        $blob,
        BlobModels\GetBlobOptions $options = null
    );

    /**
     * Creates promise to read or download a blob from the system, including its
     * metadata and properties.
     *
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param BlobModels\GetBlobOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179440.aspx
     */
    public function getBlobAsync(
        $container,
        $blob,
        BlobModels\GetBlobOptions $options = null
    );

    /**
     * Deletes a blob or blob snapshot.
     *
     * Note that if the snapshot entry is specified in the $options then only this
     * blob snapshot is deleted. To delete all blob snapshots, do not set Snapshot
     * and just set getDeleteSnaphotsOnly to true.
     *
     * @param string                       $container name of the container
     * @param string                       $blob      name of the blob
     * @param BlobModels\DeleteBlobOptions $options   optional parameters
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179413.aspx
     */
    public function deleteBlob(
        $container,
        $blob,
        BlobModels\DeleteBlobOptions $options = null
    );

    /**
     * Creates promise to delete a blob or blob snapshot.
     *
     * Note that if the snapshot entry is specified in the $options then only this
     * blob snapshot is deleted. To delete all blob snapshots, do not set Snapshot
     * and just set getDeleteSnaphotsOnly to true.
     *
     * @param string                       $container name of the container
     * @param string                       $blob      name of the blob
     * @param BlobModels\DeleteBlobOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179413.aspx
     */
    public function deleteBlobAsync(
        $container,
        $blob,
        BlobModels\DeleteBlobOptions $options = null
    );

    /**
    * Creates a snapshot of a blob.
    *
    * @param string                               $container name of the container
    * @param string                               $blob      name of the blob
    * @param BlobModels\CreateBlobSnapshotOptions $options   optional parameters
    *
    * @return BlobModels\CreateBlobSnapshotResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691971.aspx
    */
    public function createBlobSnapshot(
        $container,
        $blob,
        BlobModels\CreateBlobSnapshotOptions $options = null
    );

    /**
     * Creates promise to create a snapshot of a blob.
     *
     * @param string                               $container The name of the
     *                                                        container.
     * @param string                               $blob      The name of the
     *                                                        blob.
     * @param BlobModels\CreateBlobSnapshotOptions $options   The optional
     *                                                        parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691971.aspx
     */
    public function createBlobSnapshotAsync(
        $container,
        $blob,
        BlobModels\CreateBlobSnapshotOptions $options = null
    );

    /**
    * Copies a source blob to a destination blob within the same storage account.
    *
    * @param string                     $destinationContainer name of container
    * @param string                     $destinationBlob      name of blob
    * @param string                     $sourceContainer      name of container
    * @param string                     $sourceBlob           name of blob
    * @param BlobModels\CopyBlobOptions $options              optional parameters
    *
    * @return BlobModels\CopyBlobResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd894037.aspx
    */
    public function copyBlob(
        $destinationContainer,
        $destinationBlob,
        $sourceContainer,
        $sourceBlob,
        BlobModels\CopyBlobOptions $options = null
    );

    /**
     * Creates promise to copy a source blob to a destination blob within the
     * same storage account.
     *
     * @param string                     $destinationContainer name of the
     *                                                         destination
     *                                                         container
     * @param string                     $destinationBlob      name of the
     *                                                         destination blob
     * @param string                     $sourceContainer      name of the source
     *                                                         container
     * @param string                     $sourceBlob           name of the source
     *                                                         blob
     * @param BlobModels\CopyBlobOptions $options              optional parameters
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
        BlobModels\CopyBlobOptions $options = null
    );

    /**
    * Establishes an exclusive one-minute write lock on a blob. To write to a locked
    * blob, a client must provide a lease ID.
    *
    * @param string                         $container name of the container
    * @param string                         $blob      name of the blob
    * @param BlobModels\AcquireLeaseOptions $options   optional parameters
    *
    * @return BlobModels\AcquireLeaseResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
    */
    public function acquireLease(
        $container,
        $blob,
        BlobModels\AcquireLeaseOptions $options = null
    );

    /**
     * Creates promise to establish an exclusive one-minute write lock on a blob.
     * To write to a locked blob, a client must provide a lease ID.
     *
     * @param string                         $container name of the container
     * @param string                         $blob      name of the blob
     * @param BlobModels\AcquireLeaseOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function acquireLeaseAsync(
        $container,
        $blob,
        BlobModels\AcquireLeaseOptions $options = null
    );

    /**
    * Renews an existing lease
    *
    * @param string                        $container name of the container
    * @param string                        $blob      name of the blob
    * @param string                        $leaseId   lease id when acquiring
    * @param BlobModels\BlobServiceOptions $options   optional parameters
    *
    * @return BlobModels\AcquireLeaseResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
    */
    public function renewLease(
        $container,
        $blob,
        $leaseId,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
     * Creates promise to renew an existing lease
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param string                        $leaseId   lease id when acquiring
     * @param BlobModels\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function renewLeaseAsync(
        $container,
        $blob,
        $leaseId,
        BlobModels\BlobServiceOptions $options = null
    );


    /**
    * Frees the lease if it is no longer needed so that another client may
    * immediately acquire a lease against the blob.
    *
    * @param string                        $container name of the container
    * @param string                        $blob      name of the blob
    * @param string                        $leaseId   lease id when acquiring
    * @param BlobModels\BlobServiceOptions $options   optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
    */
    public function releaseLease(
        $container,
        $blob,
        $leaseId,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
     * Creates promise to free the lease if it is no longer needed so that
     * another client may immediately acquire a lease against the blob.
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param string                        $leaseId   lease id when acquiring
     * @param BlobModels\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function releaseLeaseAsync(
        $container,
        $blob,
        $leaseId,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
    * Ends the lease but ensure that another client cannot acquire a new lease until
    * the current lease period has expired.
    *
    * @param string                        $container name of the container
    * @param string                        $blob      name of the blob
    * @param string                        $leaseId   lease id when acquiring
    * @param BlobModels\BlobServiceOptions $options   optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
    */
    public function breakLease(
        $container,
        $blob,
        $leaseId,
        BlobModels\BlobServiceOptions $options = null
    );

    /**
     * Creates promise to end the lease but ensure that another client cannot
     * acquire a new lease until the current lease period has expired.
     *
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param string                        $leaseId   lease id when acquiring
     * @param BlobModels\BlobServiceOptions $options   optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function breakLeaseAsync(
        $container,
        $blob,
        $leaseId,
        BlobModels\BlobServiceOptions $options = null
    );
}
