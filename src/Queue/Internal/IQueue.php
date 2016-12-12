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
 * @package   MicrosoftAzure\Storage\Queue\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Queue\Internal;

use MicrosoftAzure\Storage\Common\Internal\FilterableService;
use MicrosoftAzure\Storage\Queue\Models as QueueModels;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;

/**
 * This interface has all REST APIs provided by Windows Azure for queue service
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.11.0
 * @link      https://github.com/azure/azure-storage-php
 * @see       http://msdn.microsoft.com/en-us/library/windowsazure/dd179363.aspx
 */
interface IQueue extends FilterableService
{
    /**
     * Gets the properties of the Queue service.
     *
     * @param QueueModels\QueueServiceOptions $options The optional parameters.
     *
     * @return \MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult
     */
    public function getServiceProperties(QueueModels\QueueServiceOptions $options = null);

    /**
     * Sets the properties of the Queue service.
     *
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     *
     * @param ServiceProperties               $serviceProperties The new service
     * properties.
     * @param QueueModels\QueueServiceOptions $options           The optional parameters.
     *
     * @return void
     */
    public function setServiceProperties(
        ServiceProperties $serviceProperties,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Creates a new queue under the storage account.
     *
     * @param string                         $queueName The queue name.
     * @param QueueModels\CreateQueueOptions $options   The optional queue create options.
     *
     * @return void
     */
    public function createQueue(
        $queueName,
        QueueModels\CreateQueueOptions $options = null
    );

    /**
     * Deletes a queue.
     *
     * @param string                          $queueName The queue name.
     * @param QueueModels\QueueServiceOptions $options   The optional parameters.
     *
     * @return void
     */
    public function deleteQueue(
        $queueName,
        QueueModels\QueueServiceOptions $options
    );

    /**
     * Lists all queues in the storage account.
     *
     * @param QueueModels\ListQueuesOptions $options The optional parameters.
     *
     * @return QueueModels\ListQueuesResult
     */
    public function listQueues(QueueModels\ListQueuesOptions $options = null);

    /**
     * Returns queue properties, including user-defined metadata.
     *
     * @param string                          $queueName The queue name.
     * @param QueueModels\QueueServiceOptions $options   The optional parameters.
     *
     * @return QueueModels\GetQueueMetadataResult
     */
    public function getQueueMetadata(
        $queueName,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Sets user-defined metadata on the queue. To delete queue metadata, call
     * this API without specifying any metadata in $metadata.
     *
     * @param string                          $queueName The queue name.
     * @param array                           $metadata  The metadata array.
     * @param QueueModels\QueueServiceOptions $options   The optional parameters.
     *
     * @return void
     */
    public function setQueueMetadata(
        $queueName,
        array $metadata = null,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Adds a message to the queue and optionally sets a visibility timeout
     * for the message.
     *
     * @param string                           $queueName   The queue name.
     * @param string                           $messageText The message contents.
     * @param QueueModels\CreateMessageOptions $options     The optional parameters.
     *
     * @return void
     */
    public function createMessage(
        $queueName,
        $messageText,
        QueueModels\CreateMessageOptions $options = null
    );

    /**
     * Updates the visibility timeout of a message and/or the message contents.
     *
     * @param string              $queueName                  The queue name.
     * @param string              $messageId                  The id of the message.
     * @param string              $popReceipt                 The valid pop receipt
     * value returned from an earlier call to the Get Messages or Update Message
     * operation.
     * @param string              $messageText                The message contents.
     * @param int                 $visibilityTimeoutInSeconds Specifies the new
     * visibility timeout value, in seconds, relative to server time.
     * The new value must be larger than or equal to 0, and cannot be larger
     * than 7 days. The visibility timeout of a message cannot be set to a value
     * later than the expiry time. A message can be updated until it has been
     * deleted or has expired.
     * @param QueueModels\QueueServiceOptions $options The optional parameters.
     *
     * @return QueueModels\UpdateMessageResult
     */
    public function updateMessage(
        $queueName,
        $messageId,
        $popReceipt,
        $messageText,
        $visibilityTimeoutInSeconds,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Deletes a specified message from the queue.
     *
     * @param string                          $queueName  The queue name.
     * @param string                          $messageId  The id of the message.
     * @param string                          $popReceipt The valid pop receipt
     * value returned from an earlier call to the Get Messages or Update Message
     * operation.
     * @param QueueModels\QueueServiceOptions $options    The optional parameters.
     *
     * @return void
     */
    public function deleteMessage(
        $queueName,
        $messageId,
        $popReceipt,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Lists all messages in the queue.
     *
     * @param string                          $queueName The queue name.
     * @param QueueModels\ListMessagesOptions $options   The optional parameters.
     *
     * @return QueueModels\ListMessagesResult
     */
    public function listMessages(
        $queueName,
        QueueModels\ListMessagesOptions $options = null
    );

    /**
     * Retrieves a message from the front of the queue, without changing
     * the message visibility.
     *
     * @param string                          $queueName The queue name.
     * @param QueueModels\PeekMessagesOptions $options   The optional parameters.
     *
     * @return QueueModels\PeekMessagesResult
     */
    public function peekMessages(
        $queueName,
        QueueModels\PeekMessagesOptions $options = null
    );

    /**
     * Clears all messages from the queue.
     *
     * @param string                          $queueName The queue name.
     * @param QueueModels\QueueServiceOptions $options   The optional parameters.
     *
     * @return QueueModels\PeekMessagesResult
     */
    public function clearMessages(
        $queueName,
        QueueModels\QueueServiceOptions $options = null
    );
}
