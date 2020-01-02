2020.01 - version 1.4.1
* Changed to perform override existence instead of value check for ‘$options[‘verify’]’ in ‘ServiceRestProxy’.

2019.04 - version 1.4.0
* Added support for OAuth authentication.
* Resolved some issues on Linux platform.

2019.03 - version 1.3.0
* Documentation refinement.

2018.08 - version 1.2.0

* Fixed a bug `generateCanonicalResource` returns an empty string if `$resource` starts with "/".
* Supported optional middleware retry on connection failures.
* Fixed a typo of `DEAFULT_RETRY_INTERVAL`.

2018.04 - version 1.1.0

* MD files are modified for better readability and formatting.
* CACERT can now be set when creating RestProxies using `$options` parameter.
* Removed unnecessary trailing spaces.
* Assertions are re-factored in test cases.
* Now the test framework uses `PHPUnit\Framework\TestCase` instead of `PHPUnit_Framework_TestCase`.

2018.01 - version 1.0.0

* Removed `ServiceBuilder.php`, moved static builder methods into `BlobRestProxy`, `TableRestProxy`, `QueueRestProxy` and `FileRestProxy`.
* Moved method `SharedAccessSignatureHelper::generateBlobServiceSharedAccessSignatureToken()` into `BlobSharedAccessSignatureHelper`.
* Moved method `SharedAccessSignatureHelper::generateTableServiceSharedAccessSignatureToken()` into `TableSharedAccessSignatureHelper`.
* Moved method `SharedAccessSignatureHelper::generateQueueServiceSharedAccessSignatureToken()` into `QueueSharedAccessSignatureHelper`.
* Moved method `SharedAccessSignatureHelper::generateFileServiceSharedAccessSignatureToken()` into `FileSharedAccessSignatureHelper`.
* `CommonMiddleWare` constructor requires storage service version as parameter now.
* `AccessPolicy` class is now an abstract class, added children classes `BlobAccessPolicy`, `ContainerAccessPolicy`, `TableAccessPolicy`, `QueueAccessPolicy`, `FileAccessPolicy` and `ShareAccessPolicy`.
* Fixed a bug that `Utilities::allZero()` will return true for non-zero data chunks.
* Deprecated PHP 5.5 support.