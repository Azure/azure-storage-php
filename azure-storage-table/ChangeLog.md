2018.04 - version 1.1.0

* MD files are modified for better readability and formatting.
* CACERT can now be set when creating RestProxies using `$options` parameter.
* Removed unnecessary trailing spaces.
* Assertions are re-factored in test cases.
* Now the test framework uses `PHPUnit\Framework\TestCase` instead of `PHPUnit_Framework_TestCase`.

2018.01 - version 1.0.0

* Created `TableSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateTableServiceSharedAccessSignatureToken()` into `TableSharedAccessSignatureHelper`.
* Added static builder methods `createTableService` into `TableRestProxy`.
* Removed `dataSerializer` parameter from `TableRextProxy` constructor.
* Will change variable type according to EdmType specified when serializing table entity values.
* Deprecated PHP 5.5 support.