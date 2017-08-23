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
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Samples
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Samples;

require_once "../vendor/autoload.php";

use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions;

$connectionString = 'DefaultEndpointsProtocol=https;AccountName=<yourAccount>;AccountKey=<yourKey>';
$queueClient = ServicesBuilder::getInstance()->createQueueService($connectionString);

$myqueue = 'myqueue';

// A QueueRestProxy object lets you create a queue with the createQueue method. When creating a queue,
// you can set options on the queue, but doing so is not required.
createQueueSample($queueClient, $myqueue);

// To add a message to a queue, use QueueRestProxy->createMessage. The method takes the queue name,
// the message text, and message options (which are optional). For compatibility with others you may
// need to base64 encode message.
addMessageToQueueSample($queueClient, $myqueue);

// You can peek at a message (or messages) at the front of a queue without removing it from the queue
// by calling QueueRestProxy->peekMessages.
peekNextMessageSample($queueClient, $myqueue);

// Your code removes a message from a queue in two steps. First, you call QueueRestProxy->listMessages,
// which makes the message invisible to any other code reading from the queue. By default, this message 
// will stay invisible for 30 seconds (if the message is not deleted in this time period, it will become
// visible on the queue again). To finish removing the message from the queue, you must call 
// QueueRestProxy->deleteMessage.
dequeueNextMessageSample($queueClient, $myqueue);

// There are two ways that you can customize message retrieval from a queue.
// First, you can get a batch of messages (up to 32). Second, you can set a longer
// or shorter visibility timeout, allowing your code more or less time to fully
// process each message. The following code example uses the getMessages method
// to get 16 messages in one call. Then it processes each message by using a for
// loop. It also sets the invisibility timeout to five minutes for each message.
dequeuingMessagesOptionsSample($queueClient, $myqueue);

// You can get an estimate of the number of messages in a queue.
// The QueueRestProxy->getQueueMetadata method asks the queue service to return
// metadata about the queue. Calling the getApproximateMessageCount method on
// the returned object provides a count of how many messages are in a queue.
// The count is only approximate because messages can be added or removed after
// the queue service responds to your request.
getQueueLengthSample($queueClient, $myqueue);

// To delete a queue and all the messages in it, call the
// QueueRestProxy->deleteQueue method.
deleteQueueSample($queueClient, $myqueue);

// Beginning with version 2015-04-05, Azure Storage supports creating a new type
// of shared access signature (SAS) at the level of the storage account.
// Please refer to samples/BlobSamples.php or samples/FileSamples.php for creating
// SAS token at service level.
createQueueAccountSASSample();

function createQueueSample($queueClient, $queueName)
{
    $createQueueOptions = new CreateQueueOptions();
    $createQueueOptions->addMetaData("key1", "value1");
    $createQueueOptions->addMetaData("key2", "value2");
    
    try {
        // Create queue.
        $queueClient->createQueue($queueName, $createQueueOptions);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function addMessageToQueueSample($queueClient, $queueName)
{
    try {
        // Create message.
        $msg = "Hello World!";
        // optional: $msg = base64_encode($msg);
        $queueClient->createMessage($queueName, $msg);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function peekNextMessageSample($queueClient, $queueName)
{
    // OPTIONAL: Set peek message options.
    $message_options = new PeekMessagesOptions();
    $message_options->setNumberOfMessages(1); // Default value is 1.
    
    try {
        $peekMessagesResult = $queueClient->peekMessages($queueName, $message_options);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
    
    $messages = $peekMessagesResult->getQueueMessages();
    
    // View messages.
    $messageCount = count($messages);
    if ($messageCount <= 0) {
        echo "There are no messages.".PHP_EOL;
    } else {
        foreach ($messages as $message) {
            echo "Peeked message:".PHP_EOL;
            echo "Message Id: ".$message->getMessageId().PHP_EOL;
            echo "Date: ".date_format($message->getInsertionDate(), 'Y-m-d').PHP_EOL;
            $msg = $message->getMessageText();
            // optional: $msg = base64_decode($msg);
            echo "Message text: ".$msg.PHP_EOL.PHP_EOL;
        }
    }
}

function dequeueNextMessageSample($queueClient, $queueName)
{
    // Get message.
    $listMessagesResult = $queueClient->listMessages($queueName);
    $messages = $listMessagesResult->getQueueMessages();
    $message = $messages[0];
    
    // Process message
    
    // Get message Id and pop receipt.
    $messageId = $message->getMessageId();
    $popReceipt = $message->getPopReceipt();
    
    try {
        // Delete message.
        $queueClient->deleteMessage($queueName, $messageId, $popReceipt);
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
}

function updateMessageSample($queueClient, $queueName)
{
    // Get message.
    $listMessagesResult = $queueClient->listMessages($queueName);
    $messages = $listMessagesResult->getQueueMessages();
    $message = $messages[0];
    
    // Define new message properties.
    $new_message_text = "New message text.";
    $new_visibility_timeout = 5; // Measured in seconds.
    
    // Get message ID and pop receipt.
    $messageId = $message->getMessageId();
    $popReceipt = $message->getPopReceipt();
    
    try {
        // Update message.
        $queueClient->updateMessage(
            "myqueue",
            $messageId,
            $popReceipt,
            $new_message_text,
            $new_visibility_timeout
        );
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179446.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}

function dequeuingMessagesOptionsSample($queueClient, $queueName)
{
    // Set list message options.
    $message_options = new ListMessagesOptions();
    $message_options->setVisibilityTimeoutInSeconds(300);
    $message_options->setNumberOfMessages(16);
    
    // Get messages.
    try {
        $listMessagesResult = $queueClient->listMessages(
            $queueName,
            $message_options
        );
        $messages = $listMessagesResult->getQueueMessages();
    
        foreach ($messages as $message) {
            /* ---------------------
                Process message.
            --------------------- */
    
            // Get message Id and pop receipt.
            $messageId = $message->getMessageId();
            $popReceipt = $message->getPopReceipt();
    
            // Delete message.
            $queueClient->deleteMessage($queueName, $messageId, $popReceipt);
        }
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179446.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}

function getQueueLengthSample($queueClient, $queueName)
{
    try {
        // Get queue metadata.
        $queue_metadata = $queueClient->getQueueMetadata($queueName);
        $approx_msg_count = $queue_metadata->getApproximateMessageCount();
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179446.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    
    echo $approx_msg_count;
}

function deleteQueueSample($queueClient, $queueName)
{
    try {
        // Delete queue.
        $queueClient->deleteQueue($queueName);
    } catch (ServiceException $e) {
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179446.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}

function createQueueAccountSASSample()
{
    global $connectionString;

    $settings = StorageServiceSettings::createFromConnectionString($connectionString);
    $accountName = $settings->getName();
    $accountKey = $settings->getKey();

    $helper = new SharedAccessSignatureHelper(
        $accountName,
        $accountKey
    );

    // Refer to following link for full candidate values to construct an account level SAS
    // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-an-account-sas
    $sas = $helper->generateAccountSharedAccessSignatureToken(
        '2016-05-31',              // Signed storage service version
        'rwdlacup',                // Read, Write, Delete, List, Add, Create, Update, Process
        'q',                       // Queue
        'sco',                     // Service, container and object level resources
        '2020-01-01T08:30:00Z',    // A valid ISO 8601 time format
        '2016-01-01T12:00:00Z',    // A valid ISO 8601 time format
        '1.1.1.1-255.255.255.255', // An IP or IP ranges
        'https,http'               // Protocol permitted for requests
    );

    $connectionStringWithSAS = Resources::QUEUE_ENDPOINT_NAME .
        '='.
        'https://' .
        $accountName .
        '.' .
        Resources::QUEUE_BASE_DNS_NAME .
        ';' .
        Resources::SAS_TOKEN_NAME .
        '=' .
        $sas;

    $queueClientWithSAS = ServicesBuilder::getInstance()->createQueueService(
        $connectionStringWithSAS
    );

    $newQueue = 'newqueue';

    createQueueSample($queueClientWithSAS, $newQueue);
    addMessageToQueueSample($queueClientWithSAS, $newQueue);
    peekNextMessageSample($queueClientWithSAS, $newQueue);
    dequeueNextMessageSample($queueClientWithSAS, $newQueue);
    dequeuingMessagesOptionsSample($queueClientWithSAS, $newQueue);
    getQueueLengthSample($queueClientWithSAS, $newQueue);
    deleteQueueSample($queueClientWithSAS, $newQueue);
}
