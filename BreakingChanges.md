**Note: This changelog is deprecated starting with version 1.0.0, please refer to the ChangeLog.md in each package for future change logs.** 

Tracking Breaking changes in 1.0.0

All
* Split azure-storage composer package into azure-storage-blob, azure-storage-table, azure-storage-queue, azure-storage-file and azure-storage-common packages.
* Removed `ServiceBuilder.php`, moved static builder methods into `BlobRestProxy`, `TableRestProxy`, `QueueRestProxy` and `FileRestProxy`.
* Moved method `SharedAccessSignatureHelper::generateBlobServiceSharedAccessSignatureToken()` into `BlobSharedAccessSignatureHelper`.
* Moved method `SharedAccessSignatureHelper::generateTableServiceSharedAccessSignatureToken()` into `TableSharedAccessSignatureHelper`.
* Moved method `SharedAccessSignatureHelper::generateQueueServiceSharedAccessSignatureToken()` into `QueueSharedAccessSignatureHelper`.
* Moved method `SharedAccessSignatureHelper::generateFileServiceSharedAccessSignatureToken()` into `FileSharedAccessSignatureHelper`.
* `CommonMiddleWare` constructor requires storage service version as parameter now.
* `AccessPolicy` class is now an abstract class, added children classes `BlobAccessPolicy`, `ContainerAccessPolicy`, `TableAccessPolicy`, `QueueAccessPolicy`, `FileAccessPolicy` and `ShareAccessPolicy`.
* Deprecated PHP 5.5 support.

Blob
* Removed `dataSerializer` parameter from `BlobRextProxy` constructor.
* Option parameter type of `BlobRestProxy::CreateBlockBlob` and `BlobRestProxy::CreatePageBlobFromContent` changed and added `setUseTransactionalMD5` method.

Table
* Removed `dataSerializer` parameter from `TableRextProxy` constructor.
* Will change variable type according to EdmType specified when serializing table entity values.

Queue
* Removed `dataSerializer` parameter from `QueueRextProxy` constructor.

File
* Removed `dataSerializer` parameter from `FileRextProxy` constructor.
* Option parameter type of `FileRestProxy::CreateFileFromContent` changed and added `setUseTransactionalMD5` method.

Tracking Breaking changes in 0.19.0

Blob
* Populate content MD5 for range gets on Blobs.
  - `MicrosoftAzure\Storage\Blob\Models\BlobProperties::getContentMD5()` will always return the value of the whole blob’s MD5 value.
  - Added `MicrosoftAzure\Storage\Blob\Models\BlobProperties::getRangeContentMD5()` to get MD5 of a blob range.
* `MicrosoftAzure\Storage\Blob\Models\GetBlobOptions` and `MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions` now provide `setRange()` and `getRange()` to accept a `MicrosoftAzure\Storage\Common\Models\Range` object. Following methods are removed:
  - `setRangeStart()`
  - `getRangeStart()`
  - `setRangeEnd()`
  - `getRangeEnd()`
* Renamed 2 methods inside `MicrosoftAzure\Storage\Blob\Models\GetBlobOptions`:
  - `getComputeRangeMD5()` -> `getRangeGetContentMD5()`
  - `setComputeRangeMD5()` -> `setRangeGetContentMD5()`

File
* Populate content MD5 for range gets on Files.
  - `MicrosoftAzure\Storage\File\Models\FileProperties::getContentMD5()` will always return the value of the whole file’s MD5 value.
  - Added `MicrosoftAzure\Storage\File\Models\FileProperties::getRangeContentMD5()` to get MD5 of a file range.

Tracking Breaking changes in 0.17.0

All
* Refined code logic for continuation token. Now continuation token will be null if there are no more instance to be queried/listed.

Blob
* Removed `MicrosoftAzure\Storage\Tests\unit\Blob\Models\BlobContinuationToken`, now use `MicrosoftAzure\Storage\Common\MarkerContinuationToken` instead for better code structure and reuse.

Table
* Deprecated ATOM support for Table service.

Queue
* Removed `MicrosoftAzure\Storage\Tests\unit\Queue\Models\QueueContinuationToken`, now use `MicrosoftAzure\Storage\Common\MarkerContinuationToken` instead for better code structure and reuse.

File
* Removed `MicrosoftAzure\Storage\Tests\unit\File\Models\FileContinuationToken`, now use `MicrosoftAzure\Storage\Common\MarkerContinuationToken` instead for better code structure and reuse.

Tracking Breaking changes in 0.16.0

All
* Renamed and moved `MicrosoftAzure\Storage\Blob\Models\PageRange` to `MicrosoftAzure\Storage\Common\Models\Range`.

Table
* Created new types for the following APIs to support specifying accepted content type of response payload. Payload is now by default `application/json;odata=minimalmetadata`.
  - `MicrosoftAzure\Storage\Table\TableRestProxy::createTable` & `MicrosoftAzure\Storage\Table\TableRestProxy::createTableAsync` now uses `MicrosoftAzure\Storage\Table\Models\TableServiceCreateOptions`.
  - `MicrosoftAzure\Storage\Table\TableRestProxy::insertEntity` & `MicrosoftAzure\Storage\Table\TableRestProxy::insertEntityAsync` now uses `MicrosoftAzure\Storage\Table\Models\TableServiceCreateOptions`.
  - `MicrosoftAzure\Storage\Table\TableRestProxy::getTable` & `MicrosoftAzure\Storage\Table\TableRestProxy::getTableAsync` now uses `MicrosoftAzure\Storage\Table\Models\GetTableOptions`.
  - `MicrosoftAzure\Storage\Table\TableRestProxy::getEntity` & `MicrosoftAzure\Storage\Table\TableRestProxy::getEntityAsync` now uses `MicrosoftAzure\Storage\Table\Models\GetEntityOptions`.
* E-Tag can now be null value since when user specified to return minimal/no metadata, E-Tag will not be returned with response.
* When specifying `NO_METADATA` for querying entities, some Edm type, including Edm.Binary, Edm.DateTime and Edm.Guid could not be determined through the type detection heuristics. For more information, please see [Payload Format for Table Service Operations](https://docs.microsoft.com/en-us/rest/api/storageservices/payload-format-for-table-service-operations).

Queue
* Renamed `MicrosoftAzure\Storage\Queue\Models\MicrosoftAzureQueueMessage` to `MicrosoftAzure\Storage\Queue\Models\QueueMessage`

Tracking Breaking changes in 0.15.0

All
* Removed `setRequestOptions` for service options, instead, added `middlewares`, `middlewareStack`, `numberOfConcurrency`, `isStreaming`, `locationMode` and `decodeContent` for user to specify the corresponding options.
* Added `MicrosoftAzure\Storage\Common\Middlewares\RetryMiddleware` to support retry from secondary endpoint. Advice to use this instead of Guzzle's retry middleware for secondary endpoint retry support.
* Removed `MicrosoftAzure\Storage\Common\Models\ServiceProperties::getMetrics` and `MicrosoftAzure\Storage\Common\Models\ServiceProperties::setMetrics`. Added following methods to access hour metrics and minute metrics.
```
MicrosoftAzure\Storage\Common\Models\ServiceProperties::getHourMetrics
MicrosoftAzure\Storage\Common\Models\ServiceProperties::setHourMetrics
MicrosoftAzure\Storage\Common\Models\ServiceProperties::getMinuteMetrics
MicrosoftAzure\Storage\Common\Models\ServiceProperties::setMinuteMetrics
```

Blob
* Lease feature parity
  - Renamed `LeaseBlobResult` to `LeaseResult` to support container and blob lease.
  - Added container lease support - passing `null` to `$blob` parameter of the lease related APIs.
  - Added new parameters `$proposedLeaseId` and `$leaseDuration` to `acquireLease` API and changed the `$options` parameter from `AcquireLeaseOptions` to `BlobServiceOptions`.
  - Added new parameter `$breakPeriod` to  `breakLease` API and removed the `$leaseId` parameter.

* Refactored Options class:
  - Exracted `getLeaseId`, `setLeaseId`, `getAccessConditions` and `setAccessConditions` to the base options class `BlobServiceOptions`.
  - Refactored the `CreateBlobOptions`, `CommitBlobBlocksOptions` class to remove duplicate options and standardize the content settings related properties like `ContentType`, `ContentMD5`, `ContentEncoding`, `ContentLanguage`, `CacheControl` and `ContentDisposition`.

* Removed the useless API `ctrCrypt` from `Utilities` class.

Table
* Removed `MicrosoftAzure\Storage\Table\Models\BatchError`. When batch operation fails, exception is thrown immediately instead.

Tracking Breaking changes in 0.14.0

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

Tracking Breaking changes in 0.13.0

* Modified the setters of most classes that represent API call result from `public` to `protected` to avoid unwanted corruption of SDK constructed data. If the user is using the setters prior to the release there could be a breaking change.

Tracking Breaking changes in 0.12.0

* Moved `getMetadataArray` and `validateMetadata` from ServiceRestProxy.php to Utilities.php
* Refined return type of the following API calls, to be more reasonable.
    ```
    createPageBlob
    createBlockBlob
    createBlobBlock
    renewLease
    acquireLease
    ```
* Applied strong type for the project. This may break some cases where user use to mis-use the type of some input parameters.

Tracking Breaking Changes in 0.10.0
ALL
* Remove all pear dependencies: HTTP_Request2, Mail_mime, and Mail_mimeDecode. Use Guzzle as underlying http client library.
* Change root namespace from "WindowsAzure" to "MicrosoftAzure/Storage".
* When set metadata operations contains invalid characters, it throws a ServiceException with 400 bad request error instead of Http_Request2_LogicException.

BLOB
* MicrosoftAzure\Storage\Blob\Models\Blocks.setBlockId now requires a base64 encoded string.
