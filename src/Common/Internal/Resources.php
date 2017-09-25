<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common\Internal;

/**
 * Project resources.
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class Resources
{
    // @codingStandardsIgnoreStart

    // Connection strings
    const USE_DEVELOPMENT_STORAGE_NAME = 'UseDevelopmentStorage';
    const DEVELOPMENT_STORAGE_PROXY_URI_NAME = 'DevelopmentStorageProxyUri';
    const DEFAULT_ENDPOINTS_PROTOCOL_NAME = 'DefaultEndpointsProtocol';
    const ACCOUNT_NAME_NAME = 'AccountName';
    const ACCOUNT_KEY_NAME = 'AccountKey';
    const SAS_TOKEN_NAME = 'SharedAccessSignature';
    const BLOB_ENDPOINT_NAME = 'BlobEndpoint';
    const QUEUE_ENDPOINT_NAME = 'QueueEndpoint';
    const TABLE_ENDPOINT_NAME = 'TableEndpoint';
    const FILE_ENDPOINT_NAME = 'FileEndpoint';
    const SHARED_ACCESS_SIGNATURE_NAME = 'SharedAccessSignature';
    const DEV_STORE_NAME = 'devstoreaccount1';
    const DEV_STORE_KEY = 'Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==';
    const BLOB_BASE_DNS_NAME = 'blob.core.windows.net';
    const QUEUE_BASE_DNS_NAME = 'queue.core.windows.net';
    const TABLE_BASE_DNS_NAME = 'table.core.windows.net';
    const FILE_BASE_DNS_NAME = 'file.core.windows.net';
    const DEV_STORE_CONNECTION_STRING = 'BlobEndpoint=127.0.0.1:10000;QueueEndpoint=127.0.0.1:10001;TableEndpoint=127.0.0.1:10002;AccountName=devstoreaccount1;AccountKey=Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==';
    const SUBSCRIPTION_ID_NAME = 'SubscriptionID';
    const CERTIFICATE_PATH_NAME = 'CertificatePath';
    const SECONDARY_STRING = '-secondary';
    const PRIMARY_STRING = '-primary';

    // Messages
    const INVALID_FUNCTION_NAME = 'The class %s does not have a function named %s.';
    const INVALID_TYPE_MSG = 'The provided variable should be of type: ';
    const INVALID_META_MSG = 'Metadata cannot contain newline characters.';
    const AZURE_ERROR_MSG = "Fail:\nCode: %s\nValue: %s\ndetails (if any): %s.";
    const NOT_IMPLEMENTED_MSG = 'This method is not implemented.';
    const NULL_OR_EMPTY_MSG = "'%s' can't be NULL or empty.";
    const NULL_MSG = "'%s' can't be NULL.";
    const INVALID_URL_MSG = 'Provided URL is invalid.';
    const INVALID_HT_MSG = 'The header type provided is invalid.';
    const INVALID_EDM_MSG = 'The provided EDM type is invalid.';
    const INVALID_PROP_MSG = 'One of the provided properties is not an instance of class Property';
    const INVALID_ENTITY_MSG = 'The provided entity object is invalid.';
    const INVALID_VERSION_MSG = 'Server does not support any known protocol versions.';
    const INVALID_BO_TYPE_MSG = 'Batch operation name is not supported or invalid.';
    const INVALID_BO_PN_MSG = 'Batch operation parameter is not supported.';
    const INVALID_OC_COUNT_MSG = 'Operations and contexts must be of same size.';
    const INVALID_EXC_OBJ_MSG = 'Exception object type should be ServiceException.';
    const NULL_TABLE_KEY_MSG = 'Partition and row keys can\'t be NULL.';
    const BATCH_ENTITY_DEL_MSG = 'The entity was deleted successfully.';
    const INVALID_PROP_VAL_MSG = "'%s' property value must satisfy %s.";
    const INVALID_PARAM_MSG = "The provided variable '%s' should be of type '%s'";
    const INVALID_VALUE_MSG = "The provided variable '%s' has unexpected value. Reason: '%s'";
    const INVALID_STRING_LENGTH = "The provided variable '%s' should be of %s characters long";
    const INVALID_BTE_MSG = "The blob block type must exist in %s";
    const INVALID_BLOB_PAT_MSG = 'The provided access type is invalid.';
    const INVALID_SVC_PROP_MSG = 'The provided service properties is invalid.';
    const UNKNOWN_SRILZER_MSG = 'The provided serializer type is unknown';
    const INVALID_CREATE_SERVICE_OPTIONS_MSG = 'Must provide valid location or affinity group.';
    const INVALID_UPDATE_SERVICE_OPTIONS_MSG = 'Must provide either description or label.';
    const INVALID_CONFIG_MSG = 'Config object must be of type Configuration';
    const INVALID_ACH_MSG = 'The provided access condition header is invalid';
    const INVALID_RECEIVE_MODE_MSG = 'The receive message option is in neither RECEIVE_AND_DELETE nor PEEK_LOCK mode.';
    const INVALID_CONFIG_URI = "The provided URI '%s' is invalid. It has to pass the check 'filter_var(<user_uri>, FILTER_VALIDATE_URL)'.";
    const INVALID_CONFIG_VALUE = "The provided config value '%s' does not belong to the valid values subset:\n%s";
    const INVALID_ACCOUNT_KEY_FORMAT = "The provided account key '%s' is not a valid base64 string. It has to pass the check 'base64_decode(<user_account_key>, true)'.";
    const MISSING_CONNECTION_STRING_SETTINGS = "The provided connection string '%s' does not have complete configuration settings.";
    const INVALID_CONNECTION_STRING_SETTING_KEY = "The setting key '%s' is not found in the expected configuration setting keys:\n%s";
    const INVALID_CERTIFICATE_PATH = "The provided certificate path '%s' is invalid.";
    const INSTANCE_TYPE_VALIDATION_MSG = 'The type of %s is %s but is expected to be %s.';
    const INVALID_MESSAGE_OBJECT_TO_SERIALIZE = 'The given object does not have required methods, so it could not be serialized.';
    const MISSING_CONNECTION_STRING_CHAR = "Missing %s character";
    const ERROR_PARSING_STRING = "'%s' at position %d.";
    const INVALID_CONNECTION_STRING = "Argument '%s' is not a valid connection string: '%s'";
    const ERROR_CONNECTION_STRING_MISSING_KEY = 'Missing key name';
    const ERROR_CONNECTION_STRING_EMPTY_KEY = 'Empty key name';
    const ERROR_CONNECTION_STRING_MISSING_CHARACTER = "Missing %s character";
    const ERROR_EMPTY_SETTINGS = 'No keys were found in the connection string';
    const MISSING_LOCK_LOCATION_MSG = 'The lock location of the brokered message is missing.';
    const INVALID_SLOT = "The provided deployment slot '%s' is not valid. Only 'staging' and 'production' are accepted.";
    const INVALID_DEPLOYMENT_LOCATOR_MSG = 'A slot or deployment name must be provided.';
    const INVALID_CHANGE_MODE_MSG = "The change mode must be 'Auto' or 'Manual'. Use Mode class constants for that purpose.";
    const INVALID_DEPLOYMENT_STATUS_MSG = "The change mode must be 'Running' or 'Suspended'. Use DeploymentStatus class constants for that purpose.";
    const ERROR_OAUTH_GET_ACCESS_TOKEN = 'Unable to get oauth access token for endpoint \'%s\', account name \'%s\'';
    const ERROR_OAUTH_SERVICE_MISSING = 'OAuth service missing for account name \'%s\'';
    const ERROR_METHOD_NOT_FOUND = 'Method \'%s\' not found in object class \'%s\'';
    const ERROR_INVALID_DATE_STRING = 'Parameter \'%s\' is not a date formatted string \'%s\'';
    const ERROR_TOO_LARGE_FOR_BLOCK_BLOB = 'Error: Exceeds the uppper limit of the blob.';
    const ERROR_RANGE_NOT_ALIGN_TO_512 = 'Error: Range of the page blob must be align to 512';
    const ERROR_FILE_COULD_NOT_BE_OPENED = 'Error: file with given path could not be opened or created.';
    const ERROR_CONTAINER_NOT_EXIST = 'The specified container does not exist';
    const ERROR_BLOB_NOT_EXIST = 'The specified blob does not exist';
    const INVALID_PARAM_GENERAL = 'The provided parameter \'%s\' is invalid';
    const INVALID_NEGATIVE_PARAM = 'The provided parameter \'%s\' should be positive number.';
    const SIGNED_SERVICE_INVALID_VALIDATION_MSG = 'The signed service should only be a combination of the letters b(lob) q(ueue) t(able) or f(ile).';
    const SIGNED_RESOURCE_TYPE_INVALID_VALIDATION_MSG = 'The signed resource type should only be a combination of the letters s(ervice) c(container) or o(bject).';
    const STRING_NOT_WITH_GIVEN_COMBINATION = 'The string should only be a combination of the letters %s.';
    const SIGNED_PROTOCOL_INVALID_VALIDATION_MSG = 'The signed protocol is invalid: possible values are https or https,http.';
    const ERROR_RESOURCE_TYPE_NOT_SUPPORTED = 'The given resource type cannot be recognized or is not supported.';
    const ERROR_TOO_MANY_SIGNED_IDENTIFIERS = 'There can be at most 5 signed identifiers at the same time.';
    const INVALID_PERMISSION_PROVIDED = 'Invalid permission provided, the permission of resource type \'%s\' can only be of \'%s\'';
    const INVALID_RESOURCE_TYPE = 'Provided resource type is invalid.';
    const ERROR_KEY_NOT_EXIST = "The key '%s' does not exist in the given array.";
    const FILE_LOCATION_IS_PRIMARY_ONLY = "Can only specify PRIMARY_ONLY for file service's location mode.";
    const FILE_SHARE_PROPERTIES_OPERATION_INVALID = "The operation is invalid. Can only be 'metadata' or 'properties'.";
    const RESOURCE_RANGE_LENGTH_MUST_SET = "The start and end/length of the range must be set.";
    const INVALID_ACCEPT_CONTENT_TYPE = "The given accept content type is not valid.";
    const ERROR_CANNOT_PARSE_XML = "Cannot parse XML, reasons: %s";
    const INVALID_SCHEME = 'HTTP scheme can only be string \'http\' or \'https\'.';
    const CONTENT_SIZE_TOO_LARGE = 'The content is too large for the selected blob type.';

    // HTTP Headers
    const X_MS_HEADER_PREFIX                 = 'x-ms-';
    const X_MS_META_HEADER_PREFIX            = 'x-ms-meta-';
    const X_MS_APPROXIMATE_MESSAGES_COUNT    = 'x-ms-approximate-messages-count';
    const X_MS_POPRECEIPT                    = 'x-ms-popreceipt';
    const X_MS_TIME_NEXT_VISIBLE             = 'x-ms-time-next-visible';
    const X_MS_BLOB_PUBLIC_ACCESS            = 'x-ms-blob-public-access';
    const X_MS_VERSION                       = 'x-ms-version';
    const X_MS_DATE                          = 'x-ms-date';
    const X_MS_BLOB_SEQUENCE_NUMBER          = 'x-ms-blob-sequence-number';
    const X_MS_BLOB_SEQUENCE_NUMBER_ACTION   = 'x-ms-sequence-number-action';
    const X_MS_BLOB_TYPE                     = 'x-ms-blob-type';
    const X_MS_BLOB_CONTENT_TYPE             = 'x-ms-blob-content-type';
    const X_MS_BLOB_CONTENT_ENCODING         = 'x-ms-blob-content-encoding';
    const X_MS_BLOB_CONTENT_LANGUAGE         = 'x-ms-blob-content-language';
    const X_MS_BLOB_CONTENT_MD5              = 'x-ms-blob-content-md5';
    const X_MS_BLOB_CACHE_CONTROL            = 'x-ms-blob-cache-control';
    const X_MS_BLOB_CONTENT_DISPOSITION      = 'x-ms-blob-content-disposition';
    const X_MS_BLOB_CONTENT_LENGTH           = 'x-ms-blob-content-length';
    const X_MS_BLOB_CONDITION_MAXSIZE        = 'x-ms-blob-condition-maxsize';
    const X_MS_BLOB_CONDITION_APPENDPOS      = 'x-ms-blob-condition-appendpos';
    const X_MS_BLOB_APPEND_OFFSET            = 'x-ms-blob-append-offset';
    const X_MS_BLOB_COMMITTED_BLOCK_COUNT    = 'x-ms-blob-committed-block-count';
    const X_MS_SHARE_QUOTA                   = 'x-ms-share-quota';
    const X_MS_COPY_ACTION                   = 'x-ms-copy-action';
    const X_MS_COPY_ID                       = 'x-ms-copy-id';
    const X_MS_COPY_COMPLETION_TIME          = 'x-ms-copy-completion-time';
    const X_MS_COPY_STATUS                   = 'x-ms-copy-status';
    const X_MS_COPY_STATUS_DESCRIPTION       = 'x-ms-copy-status-description';
    const X_MS_COPY_SOURCE                   = 'x-ms-copy-source';
    const X_MS_COPY_PROGRESS                 = 'x-ms-copy-progress';
    const X_MS_RANGE                         = 'x-ms-range';
    const X_MS_RANGE_GET_CONTENT_MD5         = 'x-ms-range-get-content-md5';
    const X_MS_LEASE_DURATION                = 'x-ms-lease-duration';
    const X_MS_LEASE_ID                      = 'x-ms-lease-id';
    const X_MS_LEASE_TIME                    = 'x-ms-lease-time';
    const X_MS_LEASE_STATUS                  = 'x-ms-lease-status';
    const X_MS_LEASE_STATE                   = 'x-ms-lease-state';
    const X_MS_LEASE_ACTION                  = 'x-ms-lease-action';
    const X_MS_PROPOSED_LEASE_ID             = 'x-ms-proposed-lease-id';
    const X_MS_LEASE_BREAK_PERIOD            = 'x-ms-lease-break-period';
    const X_MS_DELETE_SNAPSHOTS              = 'x-ms-delete-snapshots';
    const X_MS_PAGE_WRITE                    = 'x-ms-page-write';
    const X_MS_REQUEST_SERVER_ENCRYPTED      = 'x-ms-request-server-encrypted';
    const X_MS_SERVER_ENCRYPTED              = 'x-ms-server-encrypted';
    const X_MS_INCREMENTAL_COPY              = 'x-ms-incremental-copy';
    const X_MS_COPY_DESTINATION_SNAPSHOT     = 'x-ms-copy-destination-snapshot';
    const X_MS_SNAPSHOT                      = 'x-ms-snapshot';
    const X_MS_SOURCE_IF_MODIFIED_SINCE      = 'x-ms-source-if-modified-since';
    const X_MS_SOURCE_IF_UNMODIFIED_SINCE    = 'x-ms-source-if-unmodified-since';
    const X_MS_SOURCE_IF_MATCH               = 'x-ms-source-if-match';
    const X_MS_SOURCE_IF_NONE_MATCH          = 'x-ms-source-if-none-match';
    const X_MS_SOURCE_LEASE_ID               = 'x-ms-source-lease-id';
    const X_MS_CONTINUATION_NEXTTABLENAME    = 'x-ms-continuation-nexttablename';
    const X_MS_CONTINUATION_NEXTPARTITIONKEY = 'x-ms-continuation-nextpartitionkey';
    const X_MS_CONTINUATION_NEXTROWKEY       = 'x-ms-continuation-nextrowkey';
    const X_MS_REQUEST_ID                    = 'x-ms-request-id';
    const X_MS_CONTINUATION_LOCATION_MODE    = 'x-ms-continuation-location-mode';
    const X_MS_TYPE                          = 'x-ms-type';
    const X_MS_CONTENT_LENGTH                = 'x-ms-content-length';
    const X_MS_CACHE_CONTROL                 = 'x-ms-cache-control';
    const X_MS_CONTENT_TYPE                  = 'x-ms-content-type';
    const X_MS_CONTENT_MD5                   = 'x-ms-content-md5';
    const X_MS_CONTENT_ENCODING              = 'x-ms-content-encoding';
    const X_MS_CONTENT_LANGUAGE              = 'x-ms-content-language';
    const X_MS_CONTENT_DISPOSITION           = 'x-ms-content-disposition';
    const X_MS_WRITE                         = 'x-ms-write';
    const ETAG                               = 'etag';
    const LAST_MODIFIED                      = 'last-modified';
    const DATE                               = 'date';
    const AUTHENTICATION                     = 'authorization';
    const WRAP_AUTHORIZATION                 = 'WRAP access_token="%s"';
    const CONTENT_ENCODING                   = 'content-encoding';
    const CONTENT_LANGUAGE                   = 'content-language';
    const CONTENT_LENGTH                     = 'content-length';
    const CONTENT_LENGTH_NO_SPACE            = 'contentlength';
    const CONTENT_MD5                        = 'content-md5';
    const CONTENT_TYPE                       = 'content-type';
    const CONTENT_ID                         = 'content-id';
    const CONTENT_RANGE                      = 'content-range';
    const CACHE_CONTROL                      = 'cache-control';
    const CONTENT_DISPOSITION                = 'content-disposition';
    const IF_MODIFIED_SINCE                  = 'if-modified-since';
    const IF_MATCH                           = 'if-match';
    const IF_NONE_MATCH                      = 'if-none-match';
    const IF_UNMODIFIED_SINCE                = 'if-unmodified-since';
    const MAX_BLOB_SIZE                      = 'x-ms-blob-condition-maxsize';
    const MAX_APPEND_POSITION                = 'x-ms-blob-condition-appendpos';
    const SEQUENCE_NUMBER_LESS_THAN_OR_EQUAL = 'x-ms-if-sequence-number-le';
    const SEQUENCE_NUMBER_LESS_THAN          = 'x-ms-if-sequence-number-lt';
    const SEQUENCE_NUMBER_EQUAL              = 'x-ms-if-sequence-number-eq';
    const RANGE                              = 'range';
    const DATA_SERVICE_VERSION               = 'dataserviceversion';
    const MAX_DATA_SERVICE_VERSION           = 'maxdataserviceversion';
    const ACCEPT_HEADER                      = 'accept';
    const ACCEPT_CHARSET                     = 'accept-charset';
    const USER_AGENT                         = 'User-Agent';
    const PREFER                             = 'Prefer';
    const BLOB_CONTENT_MD5                   = 'x-ms-blob-content-md5';
    const FILE_CONTENT_MD5                   = 'x-ms-content-md5';

    // Type
    const QUEUE_TYPE_NAME              = 'IQueue';
    const BLOB_TYPE_NAME               = 'IBlob';
    const TABLE_TYPE_NAME              = 'ITable';

    // WRAP
    const WRAP_ACCESS_TOKEN            = 'wrap_access_token';
    const WRAP_ACCESS_TOKEN_EXPIRES_IN = 'wrap_access_token_expires_in';
    const WRAP_NAME                    = 'wrap_name';
    const WRAP_PASSWORD                = 'wrap_password';
    const WRAP_SCOPE                   = 'wrap_scope';

    // HTTP Methods
    const HTTP_GET    = 'GET';
    const HTTP_PUT    = 'PUT';
    const HTTP_POST   = 'POST';
    const HTTP_HEAD   = 'HEAD';
    const HTTP_DELETE = 'DELETE';
    const HTTP_MERGE  = 'MERGE';

    // Misc
    const EMPTY_STRING           = '';
    const SEPARATOR              = ',';
    const AZURE_DATE_FORMAT      = 'D, d M Y H:i:s T';
    const TIMESTAMP_FORMAT       = 'Y-m-d H:i:s';
    const EMULATED               = 'EMULATED';
    const EMULATOR_BLOB_URI      = '127.0.0.1:10000';
    const EMULATOR_QUEUE_URI     = '127.0.0.1:10001';
    const EMULATOR_TABLE_URI     = '127.0.0.1:10002';
    const ASTERISK               = '*';
    const SERVICE_MANAGEMENT_URL = 'https://management.core.windows.net';
    const HTTP_SCHEME            = 'http';
    const HTTPS_SCHEME           = 'https';
    const SETTING_NAME = 'SettingName';
    const SETTING_CONSTRAINT = 'SettingConstraint';
    const DEV_STORE_URI = 'http://127.0.0.1';
    const SERVICE_URI_FORMAT = "%s://%s.%s";
    const WRAP_ENDPOINT_URI_FORMAT = "https://%s-sb.accesscontrol.windows.net/WRAPv0.9";
    const MB_IN_BYTES_1       = 1048576;
    const MB_IN_BYTES_4       = 4194304;
    const MB_IN_BYTES_32      = 33554432;
    const MB_IN_BYTES_64      = 67108864;
    const MB_IN_BYTES_128     = 134217728;
    const MB_IN_BYTES_256     = 268435456;
    const MB_IN_BYTES_100     = 104857600;
    const GB_IN_BYTES         = 1073741824;
    const GB_IN_BYTES_200     = 214748364800;
    const MAX_BLOB_BLOCKS     = 50000;
    const MAX_BLOCK_BLOB_SIZE = 5242880000000;
    const RETURN_CONTENT      = 'return-content';

    // Xml Namespaces
    const WA_XML_NAMESPACE   = 'http://schemas.microsoft.com/windowsazure';
    const ATOM_XML_NAMESPACE = 'http://www.w3.org/2005/Atom';
    const DS_XML_NAMESPACE   = 'http://schemas.microsoft.com/ado/2007/08/dataservices';
    const DSM_XML_NAMESPACE  = 'http://schemas.microsoft.com/ado/2007/08/dataservices/metadata';
    const XSI_XML_NAMESPACE  = 'http://www.w3.org/2001/XMLSchema-instance';
    const NUMBER_OF_CONCURRENCY = 25;//Guzzle's default value
    const DEFAULT_NUMBER_OF_RETRIES = 3;
    const DEAFULT_RETRY_INTERVAL = 1000;//Milliseconds

    // Header values
    const SDK_VERSION                        = '0.19.1';
    const STORAGE_API_LATEST_VERSION         = '2016-05-31';
    const DATA_SERVICE_VERSION_VALUE         = '3.0';
    const MAX_DATA_SERVICE_VERSION_VALUE     = '3.0;NetFx';
    const ACCEPT_HEADER_VALUE                = 'application/json';
    const JSON_FULL_METADATA_CONTENT_TYPE    = 'application/json;odata=fullmetadata';
    const JSON_MINIMAL_METADATA_CONTENT_TYPE = 'application/json;odata=minimalmetadata';
    const JSON_NO_METADATA_CONTENT_TYPE      = 'application/json;odata=nometadata';
    const ACCEPT_CHARSET_VALUE               = 'utf-8';
    const INT32_MAX                          = 2147483647;
    const INT32_MIN                          = -2147483648;

    // Query parameter names
    const QP_PREFIX             = 'Prefix';
    const QP_MAX_RESULTS        = 'MaxResults';
    const QP_METADATA           = 'Metadata';
    const QP_MARKER             = 'Marker';
    const QP_NEXT_MARKER        = 'NextMarker';
    const QP_COMP               = 'comp';
    const QP_VISIBILITY_TIMEOUT = 'visibilitytimeout';
    const QP_POPRECEIPT         = 'popreceipt';
    const QP_NUM_OF_MESSAGES    = 'numofmessages';
    const QP_PEEK_ONLY          = 'peekonly';
    const QP_MESSAGE_TTL        = 'messagettl';
    const QP_INCLUDE            = 'include';
    const QP_TIMEOUT            = 'timeout';
    const QP_DELIMITER          = 'Delimiter';
    const QP_REST_TYPE          = 'restype';
    const QP_SNAPSHOT           = 'snapshot';
    const QP_PRE_SNAPSHOT       = 'prevsnapshot';
    const QP_BLOCKID            = 'blockid';
    const QP_BLOCK_LIST_TYPE    = 'blocklisttype';
    const QP_SELECT             = '$select';
    const QP_TOP                = '$top';
    const QP_SKIP               = '$skip';
    const QP_FILTER             = '$filter';
    const QP_NEXT_TABLE_NAME    = 'NextTableName';
    const QP_NEXT_PK            = 'NextPartitionKey';
    const QP_NEXT_RK            = 'NextRowKey';
    const QP_ACTION             = 'action';
    const QP_EMBED_DETAIL       = 'embed-detail';
    const QP_QUEUE_MESSAGE      = 'QueueMessage';
    const QP_COPY_ID            = 'copyid';
    const QP_SHARES             = 'Shares';
    const QP_SHARE              = 'Share';
    const QP_NAME               = 'Name';
    const QP_PROPERTIES         = 'Properties';
    const QP_LAST_MODIFIED      = 'Last-Modified';
    const QP_ETAG               = 'Etag';
    const QP_QUOTA              = 'Quota';
    const QP_ENTRIES            = 'Entries';
    const QP_DIRECTORY          = 'Directory';
    const QP_FILE               = 'File';
    const QP_CONTENT_LENGTH     = 'Content-Length';

    // Query parameter values
    const QPV_REGENERATE = 'regenerate';
    const QPV_CONFIG     = 'config';
    const QPV_STATUS     = 'status';
    const QPV_UPGRADE    = 'upgrade';
    const QPV_WALK_UPGRADE_DOMAIN = 'walkupgradedomain';
    const QPV_REBOOT = 'reboot';
    const QPV_REIMAGE = 'reimage';
    const QPV_ROLLBACK = 'rollback';

    // Request body content types
    const URL_ENCODED_CONTENT_TYPE = 'application/x-www-form-urlencoded';
    const XML_CONTENT_TYPE         = 'application/xml';
    const JSON_CONTENT_TYPE        = 'application/json';
    const BINARY_FILE_TYPE         = 'application/octet-stream';
    const HTTP_TYPE                = 'application/http';
    const MULTIPART_MIXED_TYPE     = 'multipart/mixed';

    // Common used XML tags
    const XTAG_ATTRIBUTES                   = '@attributes';
    const XTAG_NAMESPACE                    = '@namespace';
    const XTAG_LABEL                        = 'Label';
    const XTAG_NAME                         = 'Name';
    const XTAG_DESCRIPTION                  = 'Description';
    const XTAG_LOCATION                     = 'Location';
    const XTAG_AFFINITY_GROUP               = 'AffinityGroup';
    const XTAG_HOSTED_SERVICES              = 'HostedServices';
    const XTAG_STORAGE_SERVICES             = 'StorageServices';
    const XTAG_STORAGE_SERVICE              = 'StorageService';
    const XTAG_DISPLAY_NAME                 = 'DisplayName';
    const XTAG_SERVICE_NAME                 = 'ServiceName';
    const XTAG_URL                          = 'Url';
    const XTAG_ID                           = 'ID';
    const XTAG_STATUS                       = 'Status';
    const XTAG_HTTP_STATUS_CODE             = 'HttpStatusCode';
    const XTAG_CODE                         = 'Code';
    const XTAG_MESSAGE                      = 'Message';
    const XTAG_STORAGE_SERVICE_PROPERTIES   = 'StorageServiceProperties';
    const XTAG_SERVICE_ENDPOINT             = 'ServiceEndpoint';
    const XTAG_ENDPOINT                     = 'Endpoint';
    const XTAG_ENDPOINTS                    = 'Endpoints';
    const XTAG_PRIMARY                      = 'Primary';
    const XTAG_SECONDARY                    = 'Secondary';
    const XTAG_KEY_TYPE                     = 'KeyType';
    const XTAG_STORAGE_SERVICE_KEYS         = 'StorageServiceKeys';
    const XTAG_ERROR                        = 'Error';
    const XTAG_HOSTED_SERVICE               = 'HostedService';
    const XTAG_HOSTED_SERVICE_PROPERTIES    = 'HostedServiceProperties';
    const XTAG_CREATE_HOSTED_SERVICE        = 'CreateHostedService';
    const XTAG_CREATE_STORAGE_SERVICE_INPUT = 'CreateStorageServiceInput';
    const XTAG_UPDATE_STORAGE_SERVICE_INPUT = 'UpdateStorageServiceInput';
    const XTAG_CREATE_AFFINITY_GROUP        = 'CreateAffinityGroup';
    const XTAG_UPDATE_AFFINITY_GROUP        = 'UpdateAffinityGroup';
    const XTAG_UPDATE_HOSTED_SERVICE        = 'UpdateHostedService';
    const XTAG_PACKAGE_URL                  = 'PackageUrl';
    const XTAG_CONFIGURATION                = 'Configuration';
    const XTAG_START_DEPLOYMENT             = 'StartDeployment';
    const XTAG_TREAT_WARNINGS_AS_ERROR      = 'TreatWarningsAsError';
    const XTAG_CREATE_DEPLOYMENT            = 'CreateDeployment';
    const XTAG_DEPLOYMENT_SLOT              = 'DeploymentSlot';
    const XTAG_PRIVATE_ID                   = 'PrivateID';
    const XTAG_ROLE_INSTANCE_LIST           = 'RoleInstanceList';
    const XTAG_UPGRADE_DOMAIN_COUNT         = 'UpgradeDomainCount';
    const XTAG_ROLE_LIST                    = 'RoleList';
    const XTAG_SDK_VERSION                  = 'SdkVersion';
    const XTAG_INPUT_ENDPOINT_LIST          = 'InputEndpointList';
    const XTAG_LOCKED                       = 'Locked';
    const XTAG_ROLLBACK_ALLOWED             = 'RollbackAllowed';
    const XTAG_UPGRADE_STATUS               = 'UpgradeStatus';
    const XTAG_UPGRADE_TYPE                 = 'UpgradeType';
    const XTAG_CURRENT_UPGRADE_DOMAIN_STATE = 'CurrentUpgradeDomainState';
    const XTAG_CURRENT_UPGRADE_DOMAIN       = 'CurrentUpgradeDomain';
    const XTAG_ROLE_NAME                    = 'RoleName';
    const XTAG_INSTANCE_NAME                = 'InstanceName';
    const XTAG_INSTANCE_STATUS              = 'InstanceStatus';
    const XTAG_INSTANCE_UPGRADE_DOMAIN      = 'InstanceUpgradeDomain';
    const XTAG_INSTANCE_FAULT_DOMAIN        = 'InstanceFaultDomain';
    const XTAG_INSTANCE_SIZE                = 'InstanceSize';
    const XTAG_INSTANCE_STATE_DETAILS       = 'InstanceStateDetails';
    const XTAG_INSTANCE_ERROR_CODE          = 'InstanceErrorCode';
    const XTAG_OS_VERSION                   = 'OsVersion';
    const XTAG_ROLE_INSTANCE                = 'RoleInstance';
    const XTAG_ROLE                         = 'Role';
    const XTAG_INPUT_ENDPOINT               = 'InputEndpoint';
    const XTAG_VIP                          = 'Vip';
    const XTAG_PORT                         = 'Port';
    const XTAG_DEPLOYMENT                   = 'Deployment';
    const XTAG_DEPLOYMENTS                  = 'Deployments';
    const XTAG_REGENERATE_KEYS              = 'RegenerateKeys';
    const XTAG_SWAP                         = 'Swap';
    const XTAG_PRODUCTION                   = 'Production';
    const XTAG_SOURCE_DEPLOYMENT            = 'SourceDeployment';
    const XTAG_CHANGE_CONFIGURATION         = 'ChangeConfiguration';
    const XTAG_MODE                         = 'Mode';
    const XTAG_UPDATE_DEPLOYMENT_STATUS     = 'UpdateDeploymentStatus';
    const XTAG_ROLE_TO_UPGRADE              = 'RoleToUpgrade';
    const XTAG_FORCE                        = 'Force';
    const XTAG_UPGRADE_DEPLOYMENT           = 'UpgradeDeployment';
    const XTAG_UPGRADE_DOMAIN               = 'UpgradeDomain';
    const XTAG_WALK_UPGRADE_DOMAIN          = 'WalkUpgradeDomain';
    const XTAG_ROLLBACK_UPDATE_OR_UPGRADE   = 'RollbackUpdateOrUpgrade';
    const XTAG_CONTAINER_NAME               = 'ContainerName';
    const XTAG_ACCOUNT_NAME                 = 'AccountName';
    const XTAG_LOGGING                      = 'Logging';
    const XTAG_HOUR_METRICS                 = 'HourMetrics';
    const XTAG_MINUTE_METRICS               = 'MinuteMetrics';
    const XTAG_CORS                         = 'Cors';
    const XTAG_CORS_RULE                    = 'CorsRule';
    const XTAG_ALLOWED_ORIGINS              = 'AllowedOrigins';
    const XTAG_ALLOWED_METHODS              = 'AllowedMethods';
    const XTAG_ALLOWED_HEADERS              = 'AllowedHeaders';
    const XTAG_EXPOSED_HEADERS              = 'ExposedHeaders';
    const XTAG_MAX_AGE_IN_SECONDS           = 'MaxAgeInSeconds';
    const XTAG_SIGNED_IDENTIFIERS           = 'SignedIdentifiers';
    const XTAG_SIGNED_IDENTIFIER            = 'SignedIdentifier';
    const XTAG_ACCESS_POLICY                = 'AccessPolicy';
    const XTAG_SIGNED_START                 = 'Start';
    const XTAG_SIGNED_EXPIRY                = 'Expiry';
    const XTAG_SIGNED_PERMISSION            = 'Permission';
    const XTAG_SIGNED_ID                    = 'Id';
    const XTAG_DEFAULT_SERVICE_VERSION      = 'DefaultServiceVersion';
    const XTAG_GEO_REPLICATION              = 'GeoReplication';
    const XTAG_LAST_SYNC_TIME               = 'LastSyncTime';
    const XTAG_SHARE_USAGE                  = 'ShareUsage';
    const XTAG_PAGE_RANGE                   = 'PageRange';
    const XTAG_CLEAR_RANGE                  = 'ClearRange';
    const XTAG_RANGE_START                  = 'Start';
    const XTAG_RANGE_END                    = 'End';

    //JSON Tags
    const JSON_TABLE_NAME        = 'TableName';
    const JSON_VALUE             = 'value';
    const JSON_ODATA_METADATA    = 'odata.metadata';
    const JSON_ODATA_TYPE        = 'odata.type';
    const JSON_ODATA_ID          = 'odata.id';
    const JSON_ODATA_EDITLINK    = 'odata.editLink';
    const JSON_ODATA_TYPE_SUFFIX = '@odata.type';
    const JSON_ODATA_ETAG        = 'odata.etag';
    const JSON_PARTITION_KEY     = 'PartitionKey';
    const JSON_ROW_KEY           = 'RowKey';
    const JSON_TIMESTAMP         = 'Timestamp';
    const JSON_CUSTOMER_SINCE    = 'CustomerSince';

    // PHP URL Keys
    const PHP_URL_SCHEME   = 'scheme';
    const PHP_URL_HOST     = 'host';
    const PHP_URL_PORT     = 'port';
    const PHP_URL_USER     = 'user';
    const PHP_URL_PASS     = 'pass';
    const PHP_URL_PATH     = 'path';
    const PHP_URL_QUERY    = 'query';
    const PHP_URL_FRAGMENT = 'fragment';

    // Status Codes
    const STATUS_OK                = 200;
    const STATUS_CREATED           = 201;
    const STATUS_ACCEPTED          = 202;
    const STATUS_NO_CONTENT        = 204;
    const STATUS_PARTIAL_CONTENT   = 206;
    const STATUS_MOVED_PERMANENTLY = 301;

    // Resource Types
    const RESOURCE_TYPE_BLOB      = 'b';
    const RESOURCE_TYPE_CONTAINER = 'c';
    const RESOURCE_TYPE_QUEUE     = 'q';
    const RESOURCE_TYPE_TABLE     = 't';
    const RESOURCE_TYPE_SHARE     = 's';
    const RESOURCE_TYPE_FILE      = 'f';

    // Request Options String
    const ROS_LOCATION_MODE  = 'location_mode';
    const ROS_SECONDARY_URI  = 'secondary_uri';
    const ROS_PRIMARY_URI    = 'primary_uri';
    const ROS_DECODE_CONTENT = 'decode_content';
    const ROS_STREAM         = 'stream';
    const ROS_HANDLER        = 'requestHandler';

    // @codingStandardsIgnoreEnd
}
