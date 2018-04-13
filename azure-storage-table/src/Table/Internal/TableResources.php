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

namespace MicrosoftAzure\Storage\Table\Internal;

use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Project resources.
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class TableResources extends Resources
{
    // @codingStandardsIgnoreStart

    const TABLE_SDK_VERSION = '1.1.0';
    const STORAGE_API_LATEST_VERSION = '2016-05-31';

    const DATA_SERVICE_VERSION_VALUE = '3.0';
    const MAX_DATA_SERVICE_VERSION_VALUE = '3.0;NetFx';
    const ACCEPT_HEADER_VALUE = 'application/json';
    const JSON_FULL_METADATA_CONTENT_TYPE = 'application/json;odata=fullmetadata';
    const JSON_MINIMAL_METADATA_CONTENT_TYPE = 'application/json;odata=minimalmetadata';
    const JSON_NO_METADATA_CONTENT_TYPE = 'application/json;odata=nometadata';
    const ACCEPT_CHARSET_VALUE = 'utf-8';

    // Error messages
    const INVALID_EDM_MSG = 'The provided EDM type is invalid.';
    const INVALID_PROP_MSG = 'One of the provided properties is not an instance of class Property';
    const INVALID_ENTITY_MSG = 'The provided entity object is invalid.';
    const INVALID_BO_TYPE_MSG = 'Batch operation name is not supported or invalid.';
    const INVALID_BO_PN_MSG = 'Batch operation parameter is not supported.';
    const INVALID_OC_COUNT_MSG = 'Operations and contexts must be of same size.';
    const NULL_TABLE_KEY_MSG = 'Partition and row keys can\'t be NULL.';
    const BATCH_ENTITY_DEL_MSG = 'The entity was deleted successfully.';
    const INVALID_PROP_VAL_MSG = "'%s' property value must satisfy %s.";

    // Query parameters
    const QP_SELECT = '$select';
    const QP_TOP = '$top';
    const QP_SKIP = '$skip';
    const QP_FILTER = '$filter';
    const QP_NEXT_TABLE_NAME = 'NextTableName';
    const QP_NEXT_PK = 'NextPartitionKey';
    const QP_NEXT_RK = 'NextRowKey';

    // Request body content types
    const XML_CONTENT_TYPE = 'application/xml';
    const JSON_CONTENT_TYPE = 'application/json';

    //JSON Tags
    const JSON_TABLE_NAME = 'TableName';
    const JSON_VALUE = 'value';
    const JSON_ODATA_METADATA = 'odata.metadata';
    const JSON_ODATA_TYPE = 'odata.type';
    const JSON_ODATA_ID = 'odata.id';
    const JSON_ODATA_EDITLINK = 'odata.editLink';
    const JSON_ODATA_TYPE_SUFFIX = '@odata.type';
    const JSON_ODATA_ETAG = 'odata.etag';
    const JSON_PARTITION_KEY = 'PartitionKey';
    const JSON_ROW_KEY = 'RowKey';
    const JSON_TIMESTAMP = 'Timestamp';
    const JSON_CUSTOMER_SINCE = 'CustomerSince';

    // Resource permissions
    const ACCESS_PERMISSIONS = [
        Resources::RESOURCE_TYPE_TABLE => ['r', 'a', 'u', 'd']
    ];

    // @codingStandardsIgnoreEnd
}
