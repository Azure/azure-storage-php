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
 * @package   MicrosoftAzure\Storage\File\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\File\Internal;

use MicrosoftAzure\Storage\File\Models as FileModels;
use MicrosoftAzure\Storage\Common\Models\ServiceOptions;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Models\Range;

/**
 * This interface has all REST APIs provided by Windows Azure for File service.
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 * @see       https://docs.microsoft.com/en-us/rest/api/storageservices/file-service-rest-api
 */
interface IFile
{
    /**
    * Gets the properties of the service.
    *
    * @param ServiceOptions $options optional service options.
    *
    * @return GetServicePropertiesResult
    *
    * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-service-properties
    */
    public function getServiceProperties(ServiceOptions $options = null);

    /**
     * Creates promise to get the properties of the service.
     *
     * @param ServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-service-properties
     */
    public function getServicePropertiesAsync(ServiceOptions $options = null);

    /**
    * Sets the properties of the service.
    *
    * @param ServiceProperties $serviceProperties new service properties
    * @param ServiceOptions    $options           optional parameters
    *
    * @return void
    *
    * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-service-properties
    */
    public function setServiceProperties(
        ServiceProperties $serviceProperties,
        ServiceOptions    $options = null
    );

    /**
     * Creates the promise to set the properties of the service.
     *
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     *
     * @param ServiceProperties $serviceProperties new service properties.
     * @param ServiceOptions    $options           optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-service-properties
     */
    public function setServicePropertiesAsync(
        ServiceProperties $serviceProperties,
        ServiceOptions    $options = null
    );

    /**
     * Returns a list of the shares under the specified account
     *
     * @param  FileModels\ListSharesOptions|null $options The optional parameters
     *
     * @return FileModels\ListSharesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-shares
     */
    public function listShares(FileModels\ListSharesOptions $options = null);

    /**
     * Create a promise to return a list of the shares under the specified account
     *
     * @param  FileModels\ListSharesOptions|null $options The optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-shares
     */
    public function listSharesAsync(FileModels\ListSharesOptions $options = null);

    /**
     * Creates a new share in the given storage account.
     *
     * @param string                             $share   The share name.
     * @param FileModels\CreateShareOptions|null $options The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-share
     */
    public function createShare(
        $share,
        FileModels\CreateShareOptions $options = null
    );

    /**
     * Creates promise to create a new share in the given storage account.
     *
     * @param string                             $share   The share name.
     * @param FileModels\CreateShareOptions|null $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-share
     */
    public function createShareAsync(
        $share,
        FileModels\CreateShareOptions $options = null
    );

    /**
     * Deletes a share in the given storage account.
     *
     * @param string                             $share   The share name.
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-share
     */
    public function deleteShare(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Create a promise for deleting a share.
     *
     * @param  string                             $share   name of the share
     * @param  FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-share
     */
    public function deleteShareAsync(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Returns all properties and metadata on the share.
     *
     * @param string                             $share   name
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return FileModels\GetSharePropertiesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-properties
     */
    public function getShareProperties(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Create promise to return all properties and metadata on the share.
     *
     * @param string                             $share   name
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-properties
     */
    public function getSharePropertiesAsync(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Sets quota of the share.
     *
     * @param string                             $share   name
     * @param int                                $quota   quota of the share
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-properties
     */
    public function setShareProperties(
        $share,
        $quota,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to set quota the share.
     *
     * @param string                             $share   name
     * @param int                                $quota   quota of the share
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-properties
     */
    public function setSharePropertiesAsync(
        $share,
        $quota,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Returns only user-defined metadata for the specified share.
     *
     * @param string                             $share   name
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return FileModels\GetSharePropertiesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-metadata
     */
    public function getShareMetadata(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
    * Create promise to return only user-defined metadata for the specified
    * share.
    *
    * @param string                             $share   name
    * @param FileModels\FileServiceOptions|null $options optional parameters
    *
    * @return \GuzzleHttp\Promise\PromiseInterface
    *
    * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-metadata
    */
    public function getShareMetadataAsync(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Updates metadata of the share.
     *
     * @param string                             $share    name
     * @param array                              $metadata metadata key/value pair.
     * @param FileModels\FileServiceOptions|null $options optional  parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-metadata
     */
    public function setShareMetadata(
        $share,
        array $metadata,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to update metadata headers on the share.
     *
     * @param string                             $share    name
     * @param array                              $metadata metadata key/value pair.
     * @param FileModels\FileServiceOptions|null $options optional  parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-metadata
     */
    public function setShareMetadataAsync(
        $share,
        array $metadata,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Gets the access control list (ACL) for the share.
     *
     * @param string                             $share   The share name.
     * @param FileModels\FileServiceOptions|null $options The optional parameters.
     *
     * @return FileModels\GetShareACLResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-acl
     */
    public function getShareAcl(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates the promise to get the access control list (ACL) for the share.
     *
     * @param string                             $share   The share name.
     * @param FileModels\FileServiceOptions|null $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-acl
     */
    public function getShareAclAsync(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Sets the ACL and any share-level access policies for the share.
     *
     * @param string                             $share   name
     * @param FileModels\ShareACL                $acl     access control list
     *                                                    for share
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-acl
     */
    public function setShareAcl(
        $share,
        FileModels\ShareACL $acl,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to set the ACL and any share-level access policies
     * for the share.
     *
     * @param string                             $share   name
     * @param FileModels\ShareACL                $acl     access control list
     * for share
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-share-acl
     */
    public function setShareAclAsync(
        $share,
        FileModels\ShareACL $acl,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Get the statistics related to the share.
     *
     * @param  string                             $share   The name of the share.
     * @param  FileModels\FileServiceOptions|null $options The request options.
     *
     * @return FileModels\GetShareStatsResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-stats
     */
    public function getShareStats(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Get the statistics related to the share.
     *
     * @param  string                             $share   The name of the share.
     * @param  FileModels\FileServiceOptions|null $options The request options.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-share-stats
     */
    public function getShareStatsAsync(
        $share,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * List directories and files under specified path.
     *
     * @param  string                              $share   The share that
     *                                                      contains all the
     *                                                      files and directories.
     * @param  string                              $path    The path to be listed.
     * @param  FileModels\ListDirectoriesAndFilesOptions|null $options Optional parameters.
     *
     * @return FileModels\ListDirectoriesAndFilesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-directories-and-files
     */
    public function listDirectoriesAndFiles(
        $share,
        $path = '',
        FileModels\ListDirectoriesAndFilesOptions $options = null
    );

    /**
     * Creates promise to list directories and files under specified path.
     *
     * @param  string                              $share   The share that
     *                                                      contains all the
     *                                                      files and directories.
     * @param  string                              $path    The path to be listed.
     * @param  FileModels\ListDirectoriesAndFilesOptions|null $options Optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-directories-and-files
     */
    public function listDirectoriesAndFilesAsync(
        $share,
        $path = '',
        FileModels\ListDirectoriesAndFilesOptions $options = null
    );

    /**
     * Creates a new directory in the given share and path.
     *
     * @param string                                 $share     The share name.
     * @param string                                 $path      The path to
     *                                                          create the directory.
     * @param FileModels\CreateDirectoryOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-directory
     */
    public function createDirectory(
        $share,
        $path,
        FileModels\CreateDirectoryOptions $options = null
    );

    /**
     * Creates a promise to create a new directory in the given share and path.
     *
     * @param string                                 $share     The share name.
     * @param string                                 $path      The path to
     *                                                          create the directory.
     * @param FileModels\CreateDirectoryOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-directory
     */
    public function createDirectoryAsync(
        $share,
        $path,
        FileModels\CreateDirectoryOptions $options = null
    );

    /**
     * Deletes a directory in the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete
     *                                                      the directory.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-directory
     */
    public function deleteDirectory(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates a promise to delete a new directory in the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete
     *                                                      the directory.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-directory
     */
    public function deleteDirectoryAsync(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Gets a directory's properties from the given share and path.
     *
     * @param string                            $share     The share name.
     * @param string                            $path      The path of the directory.
     * @param FileModelsFileServiceOptions|null $options   The optional parameters.
     *
     * @return FileModels\GetDirectoryPropertiesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-directory-properties
     */
    public function getDirectoryProperties(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to get a directory's properties from the given share
     * and path.
     *
     * @param string                            $share     The share name.
     * @param string                            $path      The path of the directory.
     * @param FileModelsFileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-directory-properties
     */
    public function getDirectoryPropertiesAsync(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Gets a directory's metadata from the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path of the directory.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return FileModels\GetDirectoryMetadataResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-directory-metadata
     */
    public function getDirectoryMetadata(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to get a directory's metadata from the given share
     * and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path of the directory.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-directory-metadata
     */
    public function getDirectoryMetadataAsync(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Sets a directory's metadata from the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete
     *                                                      the directory.
     * @param array                              $metadata  The metadata to be set.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-directory-metadata
     */
    public function setDirectoryMetadata(
        $share,
        $path,
        array $metadata,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to set a directory's metadata from the given share
     * and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete
     *                                                      the directory.
     * @param array                              $metadata  The metadata to be set.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-directory-metadata
     */
    public function setDirectoryMetadataAsync(
        $share,
        $path,
        array $metadata,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Create a new file.
     *
     * @param string                            $share   The share name.
     * @param string                            $path    The path and name of the file.
     * @param int                               $size    The size of the file.
     * @param FileModels\CreateFileOptions|null $options The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-file
     */
    public function createFile(
        $share,
        $path,
        $size,
        FileModels\CreateFileOptions $options = null
    );

    /**
     * Creates promise to create a new file.
     *
     * @param string                            $share   The share name.
     * @param string                            $path    The path and name of the file.
     * @param int                               $size    The size of the file.
     * @param FileModels\CreateFileOptions|null $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-file
     */
    public function createFileAsync(
        $share,
        $path,
        $size,
        FileModels\CreateFileOptions $options = null
    );

    /**
     * Deletes a file in the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete the file.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-file2
     */
    public function deleteFile(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates a promise to delete a new file in the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete the file.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/delete-file2
     */
    public function deleteFileAsync(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Reads or downloads a file from the server, including its metadata and
     * properties.
     *
     * @param string                         $share   name of the share
     * @param string                         $path    path of the file to be get
     * @param FileModels\GetFileOptions|null $options optional parameters
     *
     * @return FileModels\GetFileResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file
     */
    public function getFile(
        $share,
        $path,
        FileModels\GetFileOptions $options = null
    );

    /**
     * Creates promise to read or download a file from the server, including its
     * metadata and properties.
     *
     * @param string                         $share   name of the share
     * @param string                         $path    path of the file to be get
     * @param FileModels\GetFileOptions|null $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file
     */
    public function getFileAsync(
        $share,
        $path,
        FileModels\GetFileOptions $options = null
    );

    /**
     * Gets a file's properties from the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete the file.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return FileModels\FileProperties
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-properties
     */
    public function getFileProperties(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to get a file's properties from the given share
     * and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete the file.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-properties
     */
    public function getFilePropertiesAsync(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Sets properties on the file.
     *
     * @param string                             $share      share name
     * @param string                             $path       path of the file
     * @param FileModels\FileProperties          $properties file properties.
     * @param FileModels\FileServiceOptions|null $options    optional     parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-properties
     */
    public function setFileProperties(
        $share,
        $path,
        FileModels\FileProperties $properties,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to set properties on the file.
     *
     * @param string                             $share      share name
     * @param string                             $path       path of the file
     * @param FileModels\FileProperties          $properties file properties.
     * @param FileModels\FileServiceOptions|null $options    optional     parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-properties
     */
    public function setFilePropertiesAsync(
        $share,
        $path,
        FileModels\FileProperties $properties,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Gets a file's metadata from the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path of the file.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return FileModels\GetFileMetadataResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-metadata
     */
    public function getFileMetadata(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to get a file's metadata from the given share
     * and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path of the file.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-file-metadata
     */
    public function getFileMetadataAsync(
        $share,
        $path,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Sets a file's metadata from the given share and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete the file.
     * @param array                              $metadata  The metadata to be set.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-metadata
     */
    public function setFileMetadata(
        $share,
        $path,
        array $metadata,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to set a file's metadata from the given share
     * and path.
     *
     * @param string                             $share     The share name.
     * @param string                             $path      The path to delete the file.
     * @param array                              $metadata  The metadata to be set.
     * @param FileModels\FileServiceOptions|null $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/set-file-metadata
     */
    public function setFileMetadataAsync(
        $share,
        $path,
        array $metadata,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Writes range of bytes to a file. Range can be at most 4MB in length.
     *
     * @param  string                              $share   The share name.
     * @param  string                              $path    The path of the file.
     * @param  string|resource|StreamInterface     $content The content to be uploaded.
     * @param  Range                               $range   The range in the file to
     *                                                      be put.
     * @param  FileModels\PutFileRangeOptions|null $options The optional parameters.
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
        FileModels\PutFileRangeOptions $options = null
    );

    /**
     * Creates promise to write range of bytes to a file. Range can be at most
     * 4MB in length.
     *
     * @param  string                              $share   The share name.
     * @param  string                              $path    The path of the file.
     * @param  string|resource|StreamInterface     $content The content to be uploaded.
     * @param  Range                               $range   The range in the file to
     *                                                      be put.
     * @param  FileModels\PutFileRangeOptions|null $options The optional parameters.
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
        FileModels\PutFileRangeOptions $options = null
    );

    /**
     * Creates a file from a provided content.
     *
     * @param  string                            $share   the share name
     * @param  string                            $path    the path of the file
     * @param  StreamInterface|resource|string   $content the content used to
     *                                                    create the file
     * @param  FileModels\CreateFileOptions|null $options optional parameters
     *
     * @return void
     */
    public function createFileFromContent(
        $share,
        $path,
        $content,
        FileModels\CreateFileOptions $options = null
    );

    /**
     * Creates a promise to create a file from a provided content.
     *
     * @param  string                            $share   the share name
     * @param  string                            $path    the path of the file
     * @param  StreamInterface|resource|string   $content the content used to
     *                                                    create the file
     * @param  FileModels\CreateFileOptions|null $options optional parameters
     *
     * @return void
     */
    public function createFileFromContentAsync(
        $share,
        $path,
        $content,
        FileModels\CreateFileOptions $options = null
    );

    /**
     * Clears range of bytes of a file. If the specified range is not 512-byte
     * aligned, the operation will write zeros to the start or end of the range
     * that is not 512-byte aligned and free the rest of the range inside that
     * is 512-byte aligned.
     *
     * @param  string                             $share   The share name.
     * @param  string                             $path    The path of the file.
     * @param  Range                              $range   The range in the file to
     *                                                     be cleared.
     * @param  FileModels\FileServiceOptions|null $options The optional parameters.
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/put-range
     */
    public function clearFileRange(
        $share,
        $path,
        Range $range,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to clear range of bytes of a file. If the specified range
     * is not 512-byte aligned, the operation will write zeros to the start or
     * end of the range that is not 512-byte aligned and free the rest of the
     * range inside that is 512-byte aligned.
     *
     * @param  string                             $share   The share name.
     * @param  string                             $path    The path of the file.
     * @param  Range                              $range   The range in the file to
     *                                                     be cleared.
     * @param  FileModels\FileServiceOptions|null $options The optional parameters.
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
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Lists range of bytes of a file.
     *
     * @param  string                             $share   The share name.
     * @param  string                             $path    The path of the file.
     * @param  Range                              $range   The range in the file to
     *                                                     be listed.
     * @param  FileModels\FileServiceOptions|null $options The optional parameters.
     *
     * @return FileModels\ListFileRangesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/list-ranges
     */
    public function listFileRange(
        $share,
        $path,
        Range $range = null,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to list range of bytes of a file.
     *
     * @param  string                             $share   The share name.
     * @param  string                             $path    The path of the file.
     * @param  Range                              $range   The range in the file to
     *                                                     be listed.
     * @param  FileModels\FileServiceOptions|null $options The optional parameters.
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
        FileModels\FileServiceOptions $options = null
    );

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
     * @param  string                             $share      The share name.
     * @param  string                             $path       The path of the file.
     * @param  string                             $sourcePath The path of the source.
     * @param  array                              $metadata   The metadata of
     *                                                        the file. If
     *                                                        specified, source
     *                                                        metadata will not
     *                                                        be copied.
     * @param  FileModels\FileServiceOptions|null $options    The optional parameters.
     *
     * @return FileModels\CopyFileResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/copy-file
     */
    public function copyFile(
        $share,
        $path,
        $sourcePath,
        array $metadata = array(),
        FileModels\FileServiceOptions $options = null
    );

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
     * @param  string                             $share      The share name.
     * @param  string                             $path       The path of the file.
     * @param  string                             $sourcePath The path of the source.
     * @param  array                              $metadata   The metadata of
     *                                                        the file. If
     *                                                        specified, source
     *                                                        metadata will not
     *                                                        be copied.
     * @param  FileModels\FileServiceOptions|null $options    The optional parameters.
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
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Abort a file copy operation
     *
     * @param string                             $share   name of the share
     * @param string                             $path    path of the file
     * @param string                             $copyID  copy operation identifier.
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/abort-copy-file
     */
    public function abortCopy(
        $share,
        $path,
        $copyID,
        FileModels\FileServiceOptions $options = null
    );

    /**
     * Creates promise to abort a file copy operation
     *
     * @param string                             $share   name of the share
     * @param string                             $path    path of the file
     * @param string                             $copyID  copy operation identifier.
     * @param FileModels\FileServiceOptions|null $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/abort-copy-file
     */
    public function abortCopyAsync(
        $share,
        $path,
        $copyID,
        FileModels\FileServiceOptions $options = null
    );
}
