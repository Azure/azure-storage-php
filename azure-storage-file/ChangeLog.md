2018.01 - version 1.0.0

* Created `FileSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateFileServiceSharedAccessSignatureToken()` into `FileSharedAccessSignatureHelper`.
* Added static builder methods `createFileService` into `FileRestProxy`.
* Removed `dataSerializer` parameter from `FileRextProxy` constructor.
* Added `setUseTransactionalMD5` method for option of `FileRestProxy::CreateFileFromContent`.  Default false, enabling transactional MD5 validation will take more cpu and memory resources.
* Deprecated PHP 5.5 support.