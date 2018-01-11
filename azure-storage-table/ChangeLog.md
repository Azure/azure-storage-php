2018.01 - version 1.0.0

* Created `TableSharedAccessSignatureHelper` and moved method `SharedAccessSignatureHelper::generateTableServiceSharedAccessSignatureToken()` into `TableSharedAccessSignatureHelper`.
* Added static builder methods `createTableService` into `TableRestProxy`.
* Removed `dataSerializer` parameter from `TableRextProxy` constructor.
* Will change variable type according to EdmType specified when serializing table entity values.
* Deprecated PHP 5.5 support.