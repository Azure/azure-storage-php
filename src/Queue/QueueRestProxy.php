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
 * @package   MicrosoftAzure\Storage\Queue
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Queue;

use MicrosoftAzure\Storage\Common\Internal\ServiceRestTrait;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy;
use MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\LocationMode;
use MicrosoftAzure\Storage\Queue\Internal\IQueue;
use MicrosoftAzure\Storage\Queue\Models\ListQueuesOptions;
use MicrosoftAzure\Storage\Queue\Models\ListQueuesResult;
use MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions;
use MicrosoftAzure\Storage\Queue\Models\QueueServiceOptions;
use MicrosoftAzure\Storage\Queue\Models\GetQueueMetadataResult;
use MicrosoftAzure\Storage\Queue\Models\CreateMessageOptions;
use MicrosoftAzure\Storage\Queue\Models\QueueACL;
use MicrosoftAzure\Storage\Queue\Models\QueueMessage;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesResult;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesResult;
use MicrosoftAzure\Storage\Queue\Models\UpdateMessageResult;
use MicrosoftAzure\Storage\Common\Internal\Http\HttpFormatter;

/**
 * This class constructs HTTP requests and receive HTTP responses for queue
 * service layer.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueueRestProxy extends ServiceRestProxy implements IQueue
{
    use ServiceRestTrait;

    /**
     * Lists all queues in the storage account.
     *
     * @param ListQueuesOptions $options The optional list queue options.
     *
     * @return ListQueuesResult
     */
    public function listQueues(ListQueuesOptions $options = null)
    {
        return $this->listQueuesAsync($options)->wait();
    }

    /**
     * Creates promise to list all queues in the storage account.
     *
     * @param ListQueuesOptions $options The optional list queue options.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listQueuesAsync(ListQueuesOptions $options = null)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new ListQueuesOptions();
        }
        
        $maxResults = $options->getMaxResults();
        $include    = $options->getIncludeMetadata();
        $include    = $include ? 'metadata' : null;
        $prefix     = $options->getPrefix();
        $marker     = $options->getNextMarker();
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'list');
        $this->addOptionalQueryParam($queryParams, Resources::QP_PREFIX, $prefix);
        $this->addOptionalQueryParam($queryParams, Resources::QP_MARKER, $marker);
        $this->addOptionalQueryParam($queryParams, Resources::QP_INCLUDE, $include);
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MAX_RESULTS,
            $maxResults
        );

        $dataSerializer = $this->dataSerializer;
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return ListQueuesResult::create(
                $parsed,
                Utilities::getLocationFromHeaders($response->getHeaders())
            );
        }, null);
    }

    /**
     * Clears all messages from the queue.
     *
     * If a queue contains a large number of messages, Clear Messages may time out
     * before all messages have been deleted. In this case the Queue service will
     * return status code 500 (Internal Server Error), with the additional error
     * code OperationTimedOut. If the operation times out, the client should
     * continue to retry Clear Messages until it succeeds, to ensure that all
     * messages have been deleted.
     *
     * @param string              $queueName The name of the queue.
     * @param QueueServiceOptions $options   The optional parameters.
     *
     * @return void
     */
    public function clearMessages($queueName, QueueServiceOptions $options = null)
    {
        $this->clearMessagesAsync($queueName, $options)->wait();
    }

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
     * @param string              $queueName The name of the queue.
     * @param QueueServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function clearMessagesAsync(
        $queueName,
        QueueServiceOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName . '/messages';
        $body        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $options->setLocationMode(LocationMode::PRIMARY_ONLY);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_NO_CONTENT,
            $body,
            $options
        );
    }

    /**
     * Adds a message to the queue and optionally sets a visibility timeout
     * for the message.
     *
     * @param string               $queueName   The name of the queue.
     * @param string               $messageText The message contents.
     * @param CreateMessageOptions $options     The optional parameters.
     *
     * @return void
     */
    public function createMessage(
        $queueName,
        $messageText,
        CreateMessageOptions $options = null
    ) {
        $this->createMessageAsync($queueName, $messageText, $options)->wait();
    }

    /**
     * Creates promise to add a message to the queue and optionally sets a
     * visibility timeout for the message.
     *
     * @param string               $queueName   The name of the queue.
     * @param string               $messageText The message contents.
     * @param CreateMessageOptions $options     The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createMessageAsync(
        $queueName,
        $messageText,
        CreateMessageOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        Validate::isString($messageText, 'messageText');
        
        $method      = Resources::HTTP_POST;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName . '/messages';
        $body        = Resources::EMPTY_STRING;
        $message     = new QueueMessage();
        $message->setMessageText($messageText);
        $body = $message->toXml($this->dataSerializer);
        
        
        if (is_null($options)) {
            $options = new CreateMessageOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );
        
        $visibility = $options->getVisibilityTimeoutInSeconds();
        $timeToLive = $options->getTimeToLiveInSeconds();
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_VISIBILITY_TIMEOUT,
            $visibility
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MESSAGE_TTL,
            $timeToLive
        );
        
        $options->setLocationMode(LocationMode::PRIMARY_ONLY);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_CREATED,
            $body,
            $options
        );
    }

    /**
     * Creates a new queue under the storage account.
     *
     * @param string                    $queueName The queue name.
     * @param Models\CreateQueueOptions  $options   The Optional parameters.
     *
     * @return void
     */
    public function createQueue(
        $queueName,
        Models\CreateQueueOptions $options = null
    ) {
        $this->createQueueAsync($queueName, $options)->wait();
    }

    /**
     * Creates promise to create a new queue under the storage account.
     *
     * @param string                     $queueName The queue name.
     * @param Models\CreateQueueOptions  $options   The Optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createQueueAsync(
        $queueName,
        Models\CreateQueueOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName;
        
        if (is_null($options)) {
            $options = new CreateQueueOptions();
        }

        $metadata = $options->getMetadata();
        $headers  = $this->generateMetadataHeaders($metadata);
        
        $options->setLocationMode(LocationMode::PRIMARY_ONLY);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            array(Resources::STATUS_CREATED, Resources::STATUS_NO_CONTENT),
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Deletes a specified message from the queue.
     *
     * @param string              $queueName  The name of the queue.
     * @param string              $messageId  The id of the message.
     * @param string              $popReceipt The valid pop receipt value returned
     * from an earlier call to the Get Messages or Update Message operation.
     * @param QueueServiceOptions $options    The optional parameters.
     *
     * @return void
     */
    public function deleteMessage(
        $queueName,
        $messageId,
        $popReceipt,
        QueueServiceOptions $options = null
    ) {
        $this->deleteMessageAsync(
            $queueName,
            $messageId,
            $popReceipt,
            $options
        )->wait();
    }

    /**
     * Creates promise to delete a specified message from the queue.
     *
     * @param string              $queueName  The name of the queue.
     * @param string              $messageId  The id of the message.
     * @param string              $popReceipt The valid pop receipt value returned
     * from an earlier call to the Get Messages or Update Message operation.
     * @param QueueServiceOptions $options    The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteMessageAsync(
        $queueName,
        $messageId,
        $popReceipt,
        QueueServiceOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        Validate::isString($messageId, 'messageId');
        Validate::notNullOrEmpty($messageId, 'messageId');
        Validate::isString($popReceipt, 'popReceipt');
        Validate::notNullOrEmpty($popReceipt, 'popReceipt');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName . '/messages/' . $messageId;
        $body        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_POPRECEIPT,
            $popReceipt
        );
        
        $options->setLocationMode(LocationMode::PRIMARY_ONLY);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_NO_CONTENT,
            $body,
            $options
        );
    }

    /**
     * Deletes a queue.
     *
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     *
     * @return void
     */
    public function deleteQueue($queueName, QueueServiceOptions $options = null)
    {
        $this->deleteQueueAsync($queueName, $options)->wait();
    }

    /**
     * Creates promise to delete a queue.
     *
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteQueueAsync(
        $queueName,
        QueueServiceOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $options->setLocationMode(LocationMode::PRIMARY_ONLY);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_NO_CONTENT,
            Resources::EMPTY_STRING,
            $options
        );
    }

    /**
     * Returns queue properties, including user-defined metadata.
     *
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     *
     * @return Models\GetQueueMetadataResult
     */
    public function getQueueMetadata($queueName, QueueServiceOptions $options = null)
    {
        return $this->getQueueMetadataAsync($queueName, $options)->wait();
    }

    /**
     * Creates promise to return queue properties, including user-defined metadata.
     *
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getQueueMetadataAsync(
        $queueName,
        QueueServiceOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName;
        $body        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'metadata');
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            $body,
            $options
        )->then(function ($response) {
            $responseHeaders = HttpFormatter::formatHeaders($response->getHeaders());
            $metadata = Utilities::getMetadataArray($responseHeaders);
            $maxCount = intval(
                Utilities::tryGetValue(
                    $responseHeaders,
                    Resources::X_MS_APPROXIMATE_MESSAGES_COUNT
                )
            );
        
            return new GetQueueMetadataResult($maxCount, $metadata);
        }, null);
    }

    /**
     * Lists all messages in the queue.
     *
     * @param string              $queueName The queue name.
     * @param ListMessagesOptions $options   The optional parameters.
     *
     * @return Models\ListMessagesResult
     */
    public function listMessages($queueName, ListMessagesOptions $options = null)
    {
        return $this->listMessagesAsync($queueName, $options)->wait();
    }

    /**
     * Creates promise to list all messages in the queue.
     *
     * @param string              $queueName The queue name.
     * @param ListMessagesOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listMessagesAsync(
        $queueName,
        ListMessagesOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $queueName . '/messages';
        
        if (is_null($options)) {
            $options = new ListMessagesOptions();
        }
        
        $messagesCount = $options->getNumberOfMessages();
        $visibility    = $options->getVisibilityTimeoutInSeconds();
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_NUM_OF_MESSAGES,
            $messagesCount
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_VISIBILITY_TIMEOUT,
            $visibility
        );

        $dataSerializer = $this->dataSerializer;
        
        $options->setLocationMode(LocationMode::PRIMARY_ONLY);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return ListMessagesResult::create($parsed);
        }, null);
    }

    /**
     * Retrieves a message from the front of the queue, without changing
     * the message visibility.
     *
     * @param string              $queueName The queue name.
     * @param PeekMessagesOptions $options   The optional parameters.
     *
     * @return Models\PeekMessagesResult
     */
    public function peekMessages($queueName, PeekMessagesOptions $options = null)
    {
        return $this->peekMessagesAsync($queueName, $options)->wait();
    }

    /**
     * Creates promise to retrieve a message from the front of the queue,
     * without changing the message visibility.
     *
     * @param string              $queueName The queue name.
     * @param PeekMessagesOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function peekMessagesAsync(
        $queueName,
        PeekMessagesOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $queueName . '/messages';
        
        if (is_null($options)) {
            $options = new PeekMessagesOptions();
        }
        
        $messagesCount = $options->getNumberOfMessages();
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_PEEK_ONLY, 'true');
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_NUM_OF_MESSAGES,
            $messagesCount
        );
        
        $dataSerializer = $this->dataSerializer;

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        )->then(function ($response) use ($dataSerializer) {
            $parsed = $dataSerializer->unserialize($response->getBody());
            return PeekMessagesResult::create($parsed);
        }, null);
    }

    /**
     * Sets user-defined metadata on the queue. To delete queue metadata, call
     * this API without specifying any metadata in $metadata.
     *
     * @param string              $queueName The queue name.
     * @param array               $metadata  The metadata array.
     * @param QueueServiceOptions $options   The optional parameters.
     *
     * @return void
     */
    public function setQueueMetadata(
        $queueName,
        array $metadata = null,
        QueueServiceOptions $options = null
    ) {
        $this->setQueueMetadataAsync($queueName, $metadata, $options)->wait();
    }

    /**
     * Creates promise to set user-defined metadata on the queue. To delete
     * queue metadata, call this API without specifying any metadata in $metadata.
     *
     * @param string              $queueName The queue name.
     * @param array               $metadata  The metadata array.
     * @param QueueServiceOptions $options   The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function setQueueMetadataAsync(
        $queueName,
        array $metadata = null,
        QueueServiceOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        Utilities::validateMetadata($metadata);
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $queueName;
        $body        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'metadata');
        
        $metadataHeaders = $this->generateMetadataHeaders($metadata);
        $headers         = $metadataHeaders;
        
        $options->setLocationMode(LocationMode::PRIMARY_ONLY);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_NO_CONTENT,
            $body,
            $options
        );
    }

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
     * @param QueueServiceOptions $options                    The optional
     * parameters.
     *
     * @return Models\UpdateMessageResult
     */
    public function updateMessage(
        $queueName,
        $messageId,
        $popReceipt,
        $messageText,
        $visibilityTimeoutInSeconds,
        QueueServiceOptions $options = null
    ) {
        return $this->updateMessageAsync(
            $queueName,
            $messageId,
            $popReceipt,
            $messageText,
            $visibilityTimeoutInSeconds,
            $options
        )->wait();
    }

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
     * @param QueueServiceOptions $options                    The optional
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
        QueueServiceOptions $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        Validate::isString($messageId, 'messageId');
        Validate::notNullOrEmpty($messageId, 'messageId');
        Validate::isString($popReceipt, 'popReceipt');
        Validate::notNullOrEmpty($popReceipt, 'popReceipt');
        Validate::isString($messageText, 'messageText');
        Validate::isInteger(
            $visibilityTimeoutInSeconds,
            'visibilityTimeoutInSeconds'
        );
        Validate::notNull(
            $visibilityTimeoutInSeconds,
            'visibilityTimeoutInSeconds'
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName . '/messages' . '/' . $messageId;
        $body        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_VISIBILITY_TIMEOUT,
            $visibilityTimeoutInSeconds
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_POPRECEIPT,
            $popReceipt
        );
        
        if (!empty($messageText)) {
            $this->addOptionalHeader(
                $headers,
                Resources::CONTENT_TYPE,
                Resources::URL_ENCODED_CONTENT_TYPE
            );
        
            $message = new QueueMessage();
            $message->setMessageText($messageText);
            $body = $message->toXml($this->dataSerializer);
        }
        
        $options->setLocationMode(LocationMode::PRIMARY_ONLY);

        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_NO_CONTENT,
            $body,
            $options
        )->then(function ($response) {
            $responseHeaders = HttpFormatter::formatHeaders($response->getHeaders());
            return UpdateMessageResult::create($responseHeaders);
        }, null);
    }

    /**
     * Gets the access control list (ACL)
     *
     * @param string                     $queue   The queue name.
     * @param Models\QueueServiceOptions $options The optional parameters.
     *
     * @return Models\QueueACL
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/get-queue-acl
     */
    public function getQueueAcl(
        $queue,
        Models\QueueServiceOptions $options = null
    ) {
        return $this->getQueueAclAsync($queue, $options)->wait();
    }

    /**
     * Creates the promise to gets the access control list (ACL)
     *
     * @param string                     $queue   The queue name.
     * @param Models\QueueServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/get-queue-acl
     */
    public function getQueueAclAsync(
        $queue,
        Models\QueueServiceOptions $options = null
    ) {
        Validate::isString($queue, 'queue');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_OK;
        $path        = $queue;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'acl'
        );

        $dataSerializer = $this->dataSerializer;
        
        $promise = $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_OK,
            Resources::EMPTY_STRING,
            $options
        );

        return $promise->then(function ($response) use ($dataSerializer) {
            $parsed       = $dataSerializer->unserialize($response->getBody());
            return QueueACL::create($parsed);
        }, null);
    }
    
    /**
     * Sets the ACL.
     *
     * @param string                     $queue   name
     * @param Models\QueueACL            $acl     access control list for Queue
     * @param Models\QueueServiceOptions $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/set-queue-acl
     */
    public function setQueueAcl(
        $queue,
        Models\QueueACL $acl,
        Models\QueueServiceOptions $options = null
    ) {
        $this->setQueueAclAsync($queue, $acl, $options)->wait();
    }

    /**
     * Creates promise to set the ACL
     *
     * @param string                     $queue   name
     * @param Models\QueueACL            $acl     access control list for Queue
     * @param Models\QueueServiceOptions $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/set-queue-acl
     */
    public function setQueueAclAsync(
        $queue,
        Models\QueueACL $acl,
        Models\QueueServiceOptions $options = null
    ) {
        Validate::isString($queue, 'queue');
        Validate::notNullOrEmpty($acl, 'acl');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $body        = $acl->toXml($this->dataSerializer);
        $path        = $queue;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'acl'
        );

        $options->setLocationMode(LocationMode::PRIMARY_ONLY);
        
        return $this->sendAsync(
            $method,
            $headers,
            $queryParams,
            $postParams,
            $path,
            Resources::STATUS_NO_CONTENT,
            $body,
            $options
        );
    }
}
