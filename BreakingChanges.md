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
