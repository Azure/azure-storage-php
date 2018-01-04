2018.01 - version 1.0.0

* Created `BlobSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateBlobServiceSharedAccessSignatureToken()` into `BlobSharedAccessSignatureHelper`.
* Added static builder methods `createBlobService` and `createContainerAnonymousAccess` into `BlobRestProxy`.
* Removed `dataSerializer` parameter from `BlobRextProxy` constructor.
* Added `setUseTransactionalMD5` method for options of `BlobRestProxy::CreateBlockBlob` and `BlobRestProxy::CreatePageBlobFromContent`. Default false, enabling transactional MD5 validation will take more cpu and memory resources.
* Fixed a bug that CopyBlobFromURLOptions not found.
* Deprecated PHP 5.5 support.