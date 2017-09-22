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
 * @package   MicrosoftAzure\Storage\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Queue\Models;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Holds results of CreateMessage wrapper.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateMessageResult
{
    private $queueMessage;

    /**
     * Creates CreateMessageResult object from parsed XML response.
     *
     * @param array $parsedResponse XML response parsed into array.
     *
     * @internal
     *
     * @return CreateMessageResult
     */
    public static function create($parsedResponse)
    {
        $result = new CreateMessageResult();

        if (!empty($parsedResponse) &&
            !empty($parsedResponse[Resources::QP_QUEUE_MESSAGE])
        ) {
            $result->setQueueMessage(
                QueueMessage::createFromCreateMessage(
                    $parsedResponse[Resources::QP_QUEUE_MESSAGE]
                )
            );
        }

        return $result;
    }

    /**
     * Gets queueMessage field.
     *
     * @return QueueMessage
     */
    public function getQueueMessage()
    {
        return $this->queueMessage;
    }

    /**
     * Sets queueMessage field.
     *
     * @param QueueMessage $queueMessage value to use.
     *
     * @internal
     *
     * @return void
     */
    protected function setQueueMessage($queueMessage)
    {
        $this->queueMessage = $queueMessage;
    }
}
