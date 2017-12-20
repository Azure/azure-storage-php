2017.12 - version 1.0.0

* Created `BlobSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateBlobServiceSharedAccessSignatureToken()` into `BlobSharedAccessSignatureHelper`.
* Added static builder methods `createBlobService` and `createContainerAnonymousAccess` into `BlobRestProxy`.
* Removed `dataSerializer` parameter from `BlobRextProxy` constructor.
* Deprecated PHP 5.5 support.