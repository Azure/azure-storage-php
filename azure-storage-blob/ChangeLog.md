2019.04 - version 1.4.0
* Added support for OAuth authentication.
* Resolved some issues on Linux platform.

2019.03 - version 1.3.0
* Fixed a bug where blob name '0' cannot be created.
* Documentation refinement.
* `ListContainer` now can have ETag more robustly fetched from response header.

2018.08 - version 1.2.0

* Updated Azure Storage API version from 2016-05-31 to 2017-04-17.
* Added method `setBlobTier` method in `BlobRestProxy` to set blob tiers.
* Added support setting or getting blob tiers related properties when creating blobs, listing blobs, getting blob properties and copying blobs.
* Set the `getBlobUrl()` method in `BlobRestProxy` visibility to public.

2018.04 - version 1.1.0

* Private method BlobRestProxy::getBlobUrl now preserves primary URI path when exists.
* MD files are modified for better readability and formatting.
* CACERT can now be set when creating RestProxies using `$options` parameter.
* Added a sample in `BlobSamples.php` to list all blobs with certain prefix. This is a recommended implementation of using continuation token to list all the blobs.
* Removed unnecessary trailing spaces.
* Assertions are re-factored in test cases.
* Now the test framework uses `PHPUnit\Framework\TestCase` instead of `PHPUnit_Framework_TestCase`.

2018.01 - version 1.0.0

* Created `BlobSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateBlobServiceSharedAccessSignatureToken()` into `BlobSharedAccessSignatureHelper`.
* Added static builder methods `createBlobService` and `createContainerAnonymousAccess` into `BlobRestProxy`.
* Removed `dataSerializer` parameter from `BlobRestProxy` constructor.
* Added `setUseTransactionalMD5` method for options of `BlobRestProxy::CreateBlockBlob` and `BlobRestProxy::CreatePageBlobFromContent`. Default false, enabling transactional MD5 validation will take more cpu and memory resources.
* Fixed a bug that CopyBlobFromURLOptions not found.
* Deprecated PHP 5.5 support.