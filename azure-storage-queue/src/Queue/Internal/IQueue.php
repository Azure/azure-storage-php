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

use MicrosoftAzure\Storage\Queue\Models as QueueModels;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Models\ServiceOptions;
use MicrosoftAzure\Storage\Common\Models\GetServiceStatsResult;

/**
 * This interface has all REST APIs provided by Windows Azure for queue service
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 * @see       http://msdn.microsoft.com/en-us/library/windowsazure/dd179363.aspx
 */
interface IQueue
{
    /**
     * Gets the properties of the service.
     *
     * @param ServiceOptions $options The optional parameters.
     *
     * @return \MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult
     */
    public function getServiceProperties(
        ServiceOptions $options = null
    );

    /**
     * Creates promise to get the properties of the service.
     *
     * @param ServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getServicePropertiesAsync(
        ServiceOptions $options = null
    );

    /**
     * Sets the properties of the service.
     *
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     *
     * @param ServiceProperties $serviceProperties The new service properties.
     * @param ServiceOptions    $options           The optional parameters.
     *
     * @return void
     */
    public function setServiceProperties(
        ServiceProperties $serviceProperties,
        ServiceOptions $options = null
    );

    /**
     * Creates promise to set the properties of the service.
     *
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     *
     * @param ServiceProperties $serviceProperties The new service properties.
     * @param ServiceOptions    $options           The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function setServicePropertiesAsync(
        ServiceProperties $serviceProperties,
        ServiceOptions $options = null
    );

    /**
     * Retieves statistics related to replication for the service. The operation
     * will only be sent to secondary location endpoint.
     *
     * @param  ServiceOptions|null $options The options this operation sends with.
     *
     * @return GetServiceStatsResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-queue-service-stats
     */
    public function getServiceStats(ServiceOptions $options = null);

    /**
     * Creates promise that retrieves statistics related to replication for the
     * service. The operation will only be sent to secondary location endpoint.
     *
     * @param  ServiceOptions|null $options The options this operation sends with.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see  https://docs.microsoft.com/en-us/rest/api/storageservices/get-queue-service-stats
     */
    public function getServiceStatsAsync(ServiceOptions $options = null);

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
     * Creates promise to create a new queue under the storage account.
     *
     * @param string                     $queueName The queue name.
     * @param QueueModels\CreateQueueOptions  $options   The Optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createQueueAsync(
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
     * Creates promise to delete a queue.
     *
     * @param string                          $queueName The queue name.
     * @param QueueModels\QueueServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteQueueAsync(
        $queueName,
        QueueModels\QueueServiceOptions $options = null
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
     * Creates promise to list all queues in the storage account.
     *
     * @param QueueModels\ListQueuesOptions $options The optional list queue options.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listQueuesAsync(QueueModels\ListQueuesOptions $options = null);

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
     * Creates promise to return queue properties, including user-defined metadata.
     *
     * @param string                          $queueName The queue name.
     * @param QueueModels\QueueServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getQueueMetadataAsync(
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
     * Creates promise to set user-defined metadata on the queue. To delete
     * queue metadata, call this API without specifying any metadata in $metadata.
     *
     * @param string                          $queueName The queue name.
     * @param array                           $metadata  The metadata array.
     * @param QueueModels\QueueServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function setQueueMetadataAsync(
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
     * @return QueueModels\CreateMessageResult
     */
    public function createMessage(
        $queueName,
        $messageText,
        QueueModels\CreateMessageOptions $options = null
    );

    /**
     * Creates promise to add a message to the queue and optionally sets a
     * visibility timeout for the message.
     *
     * @param string                           $queueName   The name of the queue.
     * @param string                           $messageText The message contents.
     * @param QueueModels\CreateMessageOptions $options     The optional
     *                                                      parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createMessageAsync(
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
     * Creates promise to update the visibility timeout of a message and/or the
     * message contents.
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
     * @param QueueModels\QueueServiceOptions $options        The optional
     * parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function updateMessageAsync(
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
     *                                                    value returned
     *                                                    from an earlier call to
     *                                                    the Get Messages or
     *                                                    update Message operation.
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
     * Creates promise to delete a specified message from the queue.
     *
     * @param string                          $queueName  The name of the queue.
     * @param string                          $messageId  The id of the message.
     * @param string                          $popReceipt The valid pop receipt
     *                                                    value returned
     *                                                    from an earlier call to
     *                                                    the Get Messages or
     *                                                    update Message operation.
     * @param QueueModels\QueueServiceOptions $options    The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteMessageAsync(
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
     * Creates promise to list all messages in the queue.
     *
     * @param string              $queueName The queue name.
     * @param QueueModels\ListMessagesOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listMessagesAsync(
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
     * Creates promise to retrieve a message from the front of the queue,
     * without changing the message visibility.
     *
     * @param string                          $queueName The queue name.
     * @param QueueModels\PeekMessagesOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function peekMessagesAsync(
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

    /**
     * Creates promise to clear all messages from the queue.
     *
     * If a queue contains a large number of messages, Clear Messages may time out
     * before all messages have been deleted. In this case the Queue service will
     * return status code 500 (Internal Server Error), with the additional error
     * code OperationTimedOut. If the operation times out, the client should
     * continue to retry Clear Messages until it succeeds, to ensure that all
     * messages have been deleted.
     *
     * @param string                          $queueName The name of the queue.
     * @param QueueModels\QueueServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function clearMessagesAsync(
        $queueName,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Gets the access control list (ACL)
     *
     * @param string                          $queue   The queue name.
     * @param QueueModels\QueueServiceOptions $options The optional parameters.
     *
     * @return QueueModels\QueueACL
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/get-queue-acl
     */
    public function getQueueAcl(
        $queue,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Creates the promise to gets the access control list (ACL)
     *
     * @param string                          $queue   The queue name.
     * @param QueueModels\QueueServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/get-queue-acl
     */
    public function getQueueAclAsync(
        $queue,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Sets the ACL.
     *
     * @param string                          $queue   name
     * @param QueueModels\QueueACL            $acl     access control list
     * @param QueueModels\QueueServiceOptions $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/set-queue-acl
     */
    public function setQueueAcl(
        $queue,
        QueueModels\QueueACL $acl,
        QueueModels\QueueServiceOptions $options = null
    );

    /**
     * Creates promise to set the ACL
     *
     * @param string                     $queue   name
     * @param QueueModels\QueueACL            $acl     access control list
     * @param QueueModels\QueueServiceOptions $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/set-queue-acl
     */
    public function setQueueAclAsync(
        $queue,
        QueueModels\QueueACL $acl,
        QueueModels\QueueServiceOptions $options = null
    );
}
