2018.08 - version 1.1.1

* Fixed a bug in documents that `getMessageId` method should return string instead of integer.

2018.04 - version 1.1.0

* MD files are modified for better readability and formatting.
* CACERT can now be set when creating RestProxies using `$options` parameter.
* Removed unnecessary trailing spaces.
* Assertions are re-factored in test cases.
* Now the test framework uses `PHPUnit\Framework\TestCase` instead of `PHPUnit_Framework_TestCase`.

2018.01 - version 1.0.0

* Created `QueueSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateQueueServiceSharedAccessSignatureToken()` into `QueueSharedAccessSignatureHelper`.
* Added static builder methods `createQueueService` into `QueueRestProxy`.
* Removed `dataSerializer` parameter from `QueueRextProxy` constructor.
* Deprecated PHP 5.5 support.