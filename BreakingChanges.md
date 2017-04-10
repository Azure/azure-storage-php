Tracking Breaking changes in 0.15.0

Blob
* Lease feature parity
  - Renamed `LeaseBlobResult` to `LeaseResult` to support container and blob lease.
  - Added container lease support - passing `null` to `$blob` parameter of the lease related APIs.
  - Added new parameters `$proposedLeaseId` and `$leaseDuration` to `acquireLease` API and changed the `$options` parameter from `AcquireLeaseOptions` to `BlobServiceOptions`.
  - Added new parameter `$breakPeriod` to  `breakLease` API and removed the `$leaseId` parameter.

* Refactored Options class:
  - Exracted `getLeaseId`, `setLeaseId`, `getAccessConditions` and `setAccessConditions` to the base options class `BlobServiceOptions`.
  - Refactored the `CreateBlobOptions`, `CommitBlobBlocksOptions` class to remove duplicate options and standardize the content settings related properties like `ContentType`, `ContentMD5`, `ContentEncoding`, `ContentLanguage`, `CacheControl` and `ContentDisposition`.

* Blob service properties feature parity:
  - Renamed `getMetrics` to `getHourMetrics` and `setMetrics` to `setHourMetrics` of `ServiceProperties` class.
  
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
