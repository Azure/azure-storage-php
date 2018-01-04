2018.01 - version 1.0.0

* Created `QueueSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateQueueServiceSharedAccessSignatureToken()` into `QueueSharedAccessSignatureHelper`.
* Added static builder methods `createQueueService` into `QueueRestProxy`.
* Removed `dataSerializer` parameter from `QueueRextProxy` constructor.
* Deprecated PHP 5.5 support.