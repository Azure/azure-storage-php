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

namespace MicrosoftAzure\Storage\Queue\Internal;

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
class QueueResources extends Resources
{
    // @codingStandardsIgnoreStart

    const QUEUE_SDK_VERSION = '1.1.1';
    const STORAGE_API_LATEST_VERSION = '2016-05-31';

    // Error messages
    const INVALID_RECEIVE_MODE_MSG = 'The receive message option is in neither RECEIVE_AND_DELETE nor PEEK_LOCK mode.';

    // Headers
    const X_MS_APPROXIMATE_MESSAGES_COUNT = 'x-ms-approximate-messages-count';
    const X_MS_POPRECEIPT = 'x-ms-popreceipt';
    const X_MS_TIME_NEXT_VISIBLE = 'x-ms-time-next-visible';

    // Query parameter names
    const QP_VISIBILITY_TIMEOUT = 'visibilitytimeout';
    const QP_POPRECEIPT = 'popreceipt';
    const QP_NUM_OF_MESSAGES = 'numofmessages';
    const QP_PEEK_ONLY = 'peekonly';
    const QP_MESSAGE_TTL = 'messagettl';
    const QP_QUEUE_MESSAGE = 'QueueMessage';

    // Resource permissions
    const ACCESS_PERMISSIONS = [
        Resources::RESOURCE_TYPE_QUEUE => ['r', 'a', 'u', 'p']
    ];

    // @codingStandardsIgnoreEnd
}
