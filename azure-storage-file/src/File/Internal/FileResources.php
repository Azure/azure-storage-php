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

namespace MicrosoftAzure\Storage\File\Internal;

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
class FileResources extends Resources
{
    // @codingStandardsIgnoreStart

    const FILE_SDK_VERSION = '1.1.0';
    const STORAGE_API_LATEST_VERSION = '2016-05-31';

    // Error messages
    const FILE_LOCATION_IS_PRIMARY_ONLY = "Can only specify PRIMARY_ONLY for file service's location mode.";
    const FILE_SHARE_PROPERTIES_OPERATION_INVALID = "The operation is invalid. Can only be 'metadata' or 'properties'.";

    // Headers
    const X_MS_SHARE_QUOTA = 'x-ms-share-quota';
    const FILE_CONTENT_MD5 = 'x-ms-content-md5';

    // Query parameters
    const QP_SHARES = 'Shares';
    const QP_SHARE = 'Share';
    const QP_DIRECTORY = 'Directory';
    const QP_FILE = 'File';

    // Common used XML tags
    const XTAG_SHARE_USAGE = 'ShareUsage';

    // Resource permissions
    const ACCESS_PERMISSIONS = [
        Resources::RESOURCE_TYPE_FILE => ['r', 'c', 'w', 'd'],
        Resources::RESOURCE_TYPE_SHARE => ['r', 'c', 'w', 'd', 'l']
    ];

    // @codingStandardsIgnoreEnd
}
