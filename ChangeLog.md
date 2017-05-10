2017.05 - version 0.16.0

Queue
* Renamed `MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage` to `MicrosoftAzure\Storage\Queue\Models\QueueMessage`

2017.04 - version 0.15.0

All
* Removed `setRequestOptions` for service options, instead, added `middlewares`, `middlewareStack`, `numberOfConcurrency`, `isStreaming`, `locationMode` and `decodeContent` for user to specify the corresponding options.
* Added `MicrosoftAzure\Storage\Common\Middlewares\RetryMiddleware` to support retry from secondary endpoint. Advice to use this instead of Guzzle's retry middleware for secondary endpoint retry support.
* By setting `$locationMode` in `MicrosoftAzure\Storage\Common\Models\ServiceOptions`, user can perform read operations from secondary endpoint.
* Added support for user to use proxies. If `HTTP_PROXY` is set as a system variable, the proxy specified with it will be used for HTTP connections.
* Removed `MicrosoftAzure\Storage\Common\Models\ServiceProperties::getMetrics` and `MicrosoftAzure\Storage\Common\Models\ServiceProperties::setMetrics`. Added following methods to access hour metrics and minute metrics.
```
MicrosoftAzure\Storage\Common\Models\ServiceProperties::getHourMetrics
MicrosoftAzure\Storage\Common\Models\ServiceProperties::setHourMetrics
MicrosoftAzure\Storage\Common\Models\ServiceProperties::getMinuteMetrics
MicrosoftAzure\Storage\Common\Models\ServiceProperties::setMinuteMetrics
```

Blob
* Access condition feature parity:
  - Single `AccessCondition` has been changed to multiple `AccessCondition` for the options which support access conditions.
  - Added `appendPosition`, `maxBlobSize`, `ifSequenceNumberLessThan`, `ifSequenceNumberEqual` and `ifSequenceNumberLessThanOrEqual` to `AccessCondition` class.
  - Added access conditions support for `getContainerProperties`, `setContainerProperties`, `getContainerMetadata` and `setContainerMetadata`.

* Copy blob feature parity:
  - Added new API `abortCopy`.
  - Added `setIncludeCopy` to `ListBlobsOptions` to support getting copy state information when listing blobs.
  - Added properties and getters/setters for `CopyId` and `CopyStatus` to `CopyBlobResult` class.

* Lease feature parity
  - Added lease support for `getContainerProperties`, `setContainerProperties`, `getContainerMetadata`, `setContainerMetadata` and `deleteContainer`.
  - Renamed `LeaseBlobResult` to `LeaseResult` to support container and blob lease.
  - Added container lease support - passing `null` to `$blob` parameter of the lease related APIs.
  - Added new parameters `$proposedLeaseId` and `$leaseDuration` to `acquireLease` API and changed the `$options` parameter from `AcquireLeaseOptions` to `BlobServiceOptions`.
  - Added the API `changeLease` to support changing lease.
  - Added new parameter `$breakPeriod` to  `breakLease` API and removed the `$leaseId` parameter.
  - Added properties and getters/setters for `LeaseStatus`, `LeaseState` and `LeaseDuration` to `ContainerProperties` class.

* Container/Blob properties feature parity:
  - Added properties and getters/setters for `ContentDisposition`, `LeaseState`, `LeaseDuration` and `CopyState` to `BlobProperties` class.

* Refactored Options class:
  - Exracted `getLeaseId`, `setLeaseId`, `getAccessConditions` and `setAccessConditions` to the base options class `BlobServiceOptions`.
  - Refactored the `CreateBlobOptions`, `CommitBlobBlocksOptions` class to remove duplicate options and standardize the content settings related properties like `ContentType`, `ContentMD5`, `ContentEncoding`, `ContentLanguage`, `CacheControl` and `ContentDisposition`.
  
* Blob service properties feature parity:
  - Added `getDefaultServiceVersion`, `setDefaultServiceVersion`, `getMinuteMetrics` and `setMinuteMetrics` to `ServiceProperties` class.

* Changed the return type of API `commitBlobBlocks` from `void` to `PutBlobResult`.
* Removed the useless API `ctrCrypt` from `Utilities` class.
* Added `getServiceStats` and `getServiceStatsAsync` for user to request service statistics from the server's secondary endpoint.

Table
* Removed `MicrosoftAzure\Storage\Table\Models\BatchError`. When batch operation fails, exception is thrown immediately instead. 
* Added `getServiceStats` and `getServiceStatsAsync` for user to request service statistics from the server's secondary endpoint.

Queue
* Added `getServiceStats` and `getServiceStatsAsync` for user to request service statistics from the server's secondary endpoint.


2017.04 - version 0.14.0

ALL
* Improved the documentation.
* Restructured the classes based on their intended functionality and visiblity. The changes includes:
  - `MicrosoftAzure\Storage\Common\Internal\InvalidArgumentTypeException` was moved to `MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException`
  - `MicrosoftAzure\Storage\Common\ServiceException` was moved to `MicrosoftAzure\Storage\Exceptions\ServiceException`
  - `MicrosoftAzure\Storage\Common\Internal\HttpFormatter` was moved to `MicrosoftAzure\Storage\Common\Internal\Http\HttpFormatter`
  - `MicrosoftAzure\Storage\Common\ServiceOptionsBase` was moved to `MicrosoftAzure\Storage\Common\Internal\ServiceOptionsBase`
  - `MicrosoftAzure\Storage\Common\Internal\Logger` was moved to `MicrosoftAzure\Storage\Common\Logger`
  - `MicrosoftAzure\Storage\Common\Internal\Middlewares\HistoryMiddleware` was moved to `MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware`
  - `MicrosoftAzure\Storage\Common\Internal\IMiddleware` was moved to `MicrosoftAzure\Storage\Common\Middlewares\IMiddleware`
  - `MicrosoftAzure\Storage\Common\Internal\Middlewares\MiddlewareBase` was moved to `MicrosoftAzure\Storage\Common\Middlewares\MiddlewareBase`
  - `MicrosoftAzure\Storage\Common\Internal\RetryMiddlewareFactory` was moved to `MicrosoftAzure\Storage\Common\Middlewares\RetryMiddlewareFactory`
* Added Cross-Origin Resource Sharing (CORS) support. Now setting service properties can set CORS rules at the same time.
* Added support for account-level Shared Access Signature generation.
* Resolved an error reported from some IDEs about the phpcs.xml.
* Fixed multiple test issues.

Blob
* Added API `createPageBlobFromContent` to support creating page blob directly from contents which includes local file, stream, etc...
* Added support for append blob.
* Added support for Container ACL.

Queue
* Added support for Queue ACL.

Table
* Added support for Table ACL.
* Fixed an issue that user could not set entity type to be double and integer as a value for PHP 7

2017.02 - version 0.13.0

ALL
* The `ServiceException` now provides more detailed information about the request ID and date parsed from the error response.
* Changed the setters in the following class from public to protected to avoid possible misuse of the data structure.
`MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult`
* Removed version tags in each of the files.
* Added support for the SDK to access Azure Storage Emulator.
* Introduced full support for middlewares. The usage manual can be found in [README.md](README.md).
* Turned on the verification of SSL certificate issuer in the client options.

Blob
* Applied a more robust fix for the issue where `createBlockBlob` would fail for some files with size larger than 1MB and smaller than 32MB.
* Changed the setters in the following classes from public to protected to avoid possible misuse of the data structure.
  ```
  MicrosoftAzure\Storage\Blob\Models\BreakLeaseResult
  MicrosoftAzure\Storage\Blob\Models\CopyBlobResult
  MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult
  MicrosoftAzure\Storage\Blob\Models\CreateBlobSnapshotResult
  MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult
  MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesResult
  MicrosoftAzure\Storage\Blob\Models\GetBlobResult
  MicrosoftAzure\Storage\Blob\Models\GetContainerACLResult
  MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult
  MicrosoftAzure\Storage\Blob\Models\LeaseBlobResult
  MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult
  MicrosoftAzure\Storage\Blob\Models\ListBlobsResult
  MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult
  MicrosoftAzure\Storage\Blob\Models\PutBlobResult
  MicrosoftAzure\Storage\Blob\Models\PutBlockResult
  MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult
  MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesResult
  ```

Table
* Changed the setters in the following classes from public to protected to avoid possible misuse of the data structure.
  ```
  MicrosoftAzure\Storage\Table\Models\BatchResult
  MicrosoftAzure\Storage\Table\Models\GetEntityResult
  MicrosoftAzure\Storage\Table\Models\GetTableResult
  MicrosoftAzure\Storage\Table\Models\InsertEntityResult
  MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult
  MicrosoftAzure\Storage\Table\Models\QueryTablesResult
  MicrosoftAzure\Storage\Table\Models\UpdateEntityResult
  ```

Queue
* Changed the setters in the following classes from public to protected to avoid possible misuse of the data structure.
  ```
  MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult
  MicrosoftAzure\Storage\Queue\Models\ListMessagesResult
  MicrosoftAzure\Storage\Queue\Models\ListQueuesResult
  MicrosoftAzure\Storage\Queue\Models\PeekMessagesResult
  MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult
  ```

2017.01 - version 0.12.1
Blob
* Fixed an issue where `createBlockBlob` would fail for some files with size larger than 1MB and smaller than 32MB.

2017.01 - version 0.12.0

ALL
* Applied type hinting for the project. The rules are listed below:
    * For class arguments: `ClassName $arguments`
    * For array arguments: `array $arguments`
    * For nullable arguments: `ClassName $argument = null`
    * Try to avoid `mixed` type.
    * Use unions for nullable types. e.g. `ClassName|null`.
    * Use `ClassName[]` instead of array, if the type of array is determined.
* Added support for Guzzle async programming model for all APIs.
* Added support for SAS authentication.
Blob
* Changed the return value of following APIs to be more reasonable.
    ```
    createPageBlob
    createBlockBlob
    createBlobBlock
    renewLease
    acquireLease
    ```
* Merged `StorageAuthScheme` into `SharedKeyAuthScheme` and `TableSharedKeyLiteAuthScheme` now inherits `SharedKeyAuthScheme`. This is because Azure Storage now supports Shared Key authentication and SAS authentication so the name `StorageAuthScheme` was not representative anymore.
* Fixed an issue where the newest Guzzle failed to validate the path passed in when `withPath()` is called.    

2016.11 - version 0.11.0

ALL
* Fix error string when an error occurs while parsing a connection string and is passed to _createException in `MicrosoftAzure\Storage\Common\Internal\ConnectionStringParser`.
* Added support to create Guzzle's customizable retry middleware to handle the request after the response is received. Also added a default retry policy in case a retry policy is not specified.
* Fixed a bug in unit test where getting properties from service failed to match the expected result due to previous settings have not yet taken effect.
* Fixed some coding style issue. This work will be continued in the following serveral releases, and strictly follows PSR-2 coding style.
* Updated the documentation of `setMetadata`, now in the comments of the following methods `$metadata` is an array instead of a string.
```
MicrosoftAzure\Storage\Blob\Models\Blob.setMetadata
MicrosoftAzure\Storage\Blob\Models\CommitBlobBlocksOptions.setMetadata
MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult.setMetadata
MicrosoftAzure\Storage\Blob\Models\GetBlobResult.setMetadata
MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesResult.setMetadata
MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult.setMetadata
MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions.setMetadata
MicrosoftAzure\Storage\Blob\Models\CreateBlobSnapshotOptions.setMetadata
MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions.setMetadata
MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions.setMetadata
MicrosoftAzure\Storage\Blob\Models\Container.setMetadata
MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions.setMetadata
MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult.setMetadata
MicrosoftAzure\Storage\Queue\Models\Queue.setMetadata
```
* Removed test code from composer package.
* `StorageAuthScheme::computeCanonicalizedResource` assumes that the query parameters are already grouped. That is, multi-value query parameters must be assembled using `ServiceRestProxy::groupQueryValues`. This fixes an issue with other single-value query parameters that might contain the separator character in the value.

Blob
* Added support for user to upload large files with minimum memory usage.
* Added concurrent upload for Block Blob.
* Added `MicrosoftAzure\Storage\Blob.saveBlobToFile` for user to download a blob into a file.

2016.08 - version 0.10.2

ALL
* Allow passing an array of options to a service. Currently only Guzzle options are supported via the `http` parameter.

2016.05 - version 0.10.1

Blob
* Fixed the issue that blobs upload with size multiple of 4194304 bytes and larger than 33554432 bytes.
* Fixed the issue that extra / is appended in blob URL.

2016.04 - version 0.10.0

ALL
* Separated Azure Storage APIs in Azure-SDK-for-PHP to establish an independent release cycle.
* Remove all pear dependencies: HTTP_Request2, Mail_mime, and Mail_mimeDecode. Use Guzzle as underlying http client library.
* Update storage REST API version to 2015-04-05.
* Change root namespace from "WindowsAzure" to "MicrosoftAzure/Storage".
* When set metadata operations contains invalid characters, it throws a ServiceException with 400 bad request error instead of Http_Request2_LogicException.

Blob
* Fixed the issue that upload large block blob fails. (https://github.com/Azure/azure-sdk-for-php/pull/757)
* MicrosoftAzure\Storage\Blob\Models\Blocks.setBlockId now requires a base64 encoded string.

Table
* MicrosoftAzure\Storage\Table\Models\Property.getEdmType now returns EdmType::STRING instead of null if the property data type is not set in server.
