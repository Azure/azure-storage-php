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
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Queue\Models;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;

/**
 * Holds data for single queue message.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueueMessage
{
    private $messageId;
    private $insertionDate;
    private $expirationDate;
    private $popReceipt;
    private $timeNextVisible;
    private $dequeueCount;
    private $_messageText;
    private static $xmlRootName = 'QueueMessage';
    
    /**
     * Creates QueueMessage object from parsed XML response of
     * ListMessages.
     *
     * @param array $parsedResponse XML response parsed into array.
     *
     * @internal
     *
     * @return QueueMessage
     */
    public static function createFromListMessages(array $parsedResponse)
    {
        $timeNextVisible = $parsedResponse['TimeNextVisible'];
        
        $msg  = self::createFromPeekMessages($parsedResponse);
        $date = Utilities::rfc1123ToDateTime($timeNextVisible);
        $msg->setTimeNextVisible($date);
        $msg->setPopReceipt($parsedResponse['PopReceipt']);
        
        return $msg;
    }
    
    /**
     * Creates QueueMessage object from parsed XML response of
     * PeekMessages.
     *
     * @param array $parsedResponse XML response parsed into array.
     *
     * @internal
     *
     * @return QueueMessage
     */
    public static function createFromPeekMessages(array $parsedResponse)
    {
        $msg            = new QueueMessage();
        $expirationDate = $parsedResponse['ExpirationTime'];
        $insertionDate  = $parsedResponse['InsertionTime'];
        
        $msg->setDequeueCount(intval($parsedResponse['DequeueCount']));
        
        $date = Utilities::rfc1123ToDateTime($expirationDate);
        $msg->setExpirationDate($date);
        
        $date = Utilities::rfc1123ToDateTime($insertionDate);
        $msg->setInsertionDate($date);
        
        $msg->setMessageId($parsedResponse['MessageId']);
        $msg->setMessageText($parsedResponse['MessageText']);
        
        return $msg;
    }

    /**
     * Creates QueueMessage object from parsed XML response of
     * createMessage.
     *
     * @param array $parsedResponse XML response parsed into array.
     *
     * @internal
     *
     * @return QueueMessage
     */
    public static function createFromCreateMessage(array $parsedResponse)
    {
        $msg = new QueueMessage();
        
        $expirationDate  = $parsedResponse['ExpirationTime'];
        $insertionDate   = $parsedResponse['InsertionTime'];
        $timeNextVisible = $parsedResponse['TimeNextVisible'];

        $date = Utilities::rfc1123ToDateTime($expirationDate);
        $msg->setExpirationDate($date);

        $date = Utilities::rfc1123ToDateTime($insertionDate);
        $msg->setInsertionDate($date);

        $date = Utilities::rfc1123ToDateTime($timeNextVisible);
        $msg->setTimeNextVisible($date);

        $msg->setMessageId($parsedResponse['MessageId']);
        $msg->setPopReceipt($parsedResponse['PopReceipt']);

        return $msg;
    }
    
    /**
     * Gets message text field.
     *
     * @return string
     */
    public function getMessageText()
    {
        return $this->_messageText;
    }
    
    /**
     * Sets message text field.
     *
     * @param string $messageText message contents.
     *
     * @return void
     */
    public function setMessageText($messageText)
    {
        $this->_messageText = $messageText;
    }
    
    /**
     * Gets messageId field.
     *
     * @return integer
     */
    public function getMessageId()
    {
        return $this->messageId;
    }
    
    /**
     * Sets messageId field.
     *
     * @param string $messageId message contents.
     *
     * @return void
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }
    
    /**
     * Gets insertionDate field.
     *
     * @return \DateTime
     */
    public function getInsertionDate()
    {
        return $this->insertionDate;
    }
    
    /**
     * Sets insertionDate field.
     *
     * @param \DateTime $insertionDate message contents.
     *
     * @internal
     *
     * @return void
     */
    public function setInsertionDate(\DateTime $insertionDate)
    {
        $this->insertionDate = $insertionDate;
    }
    
    /**
     * Gets expirationDate field.
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
    
    /**
     * Sets expirationDate field.
     *
     * @param \DateTime $expirationDate the expiration date of the message.
     *
     * @return void
     */
    public function setExpirationDate(\DateTime $expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }
    
    /**
     * Gets timeNextVisible field.
     *
     * @return \DateTime
     */
    public function getTimeNextVisible()
    {
        return $this->timeNextVisible;
    }
    
    /**
     * Sets timeNextVisible field.
     *
     * @param \DateTime $timeNextVisible next visibile time for the message.
     *
     * @return void
     */
    public function setTimeNextVisible($timeNextVisible)
    {
        $this->timeNextVisible = $timeNextVisible;
    }
    
    /**
     * Gets popReceipt field.
     *
     * @return string
     */
    public function getPopReceipt()
    {
        return $this->popReceipt;
    }
    
    /**
     * Sets popReceipt field.
     *
     * @param string $popReceipt used when deleting the message.
     *
     * @return void
     */
    public function setPopReceipt($popReceipt)
    {
        $this->popReceipt = $popReceipt;
    }
    
    /**
     * Gets dequeueCount field.
     *
     * @return integer
     */
    public function getDequeueCount()
    {
        return $this->dequeueCount;
    }
    
    /**
     * Sets dequeueCount field.
     *
     * @param integer $dequeueCount number of dequeues for that message.
     *
     * @internal
     *
     * @return void
     */
    public function setDequeueCount($dequeueCount)
    {
        $this->dequeueCount = $dequeueCount;
    }
    
    /**
     * Converts this current object to XML representation.
     *
     * @param XmlSerializer $xmlSerializer The XML serializer.
     *
     * @internal
     *
     * @return string
     */
    public function toXml(XmlSerializer $xmlSerializer)
    {
        $array      = array('MessageText' => $this->_messageText);
        $properties = array(XmlSerializer::ROOT_NAME => self::$xmlRootName);
        
        return $xmlSerializer->serialize($array, $properties);
    }
}
