2017.12 - version 1.0.0

* Created `FileSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateFileServiceSharedAccessSignatureToken()` into `FileSharedAccessSignatureHelper`.
* Added static builder methods `createFileService` into `FileRestProxy`.
* Removed `dataSerializer` parameter from `FileRextProxy` constructor.
* Deprecated PHP 5.5 support.