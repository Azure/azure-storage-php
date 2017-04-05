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

use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Holds results of updateMessage wrapper.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class UpdateMessageResult
{
    private $_popReceipt;
    private $_timeNextVisible;

    /**
     * Creates an instance with the given response headers.
     *
     * @param  array  $headers The response headers used to create the instance.
     *
     * @internal
     *
     * @return UpdateMessageResult
     */
    public static function create(array $headers)
    {
        $result = new UpdateMessageResult();
        $result->setPopReceipt(Utilities::tryGetValueInsensitive(
            Resources::X_MS_POPRECEIPT,
            $headers
        ));
        $timeNextVisible = Utilities::tryGetValueInsensitive(
            Resources::X_MS_TIME_NEXT_VISIBLE,
            $headers
        );
        $date   = Utilities::rfc1123ToDateTime($timeNextVisible);
        $result->setTimeNextVisible($date);

        return $result;
    }

    /**
     * Gets timeNextVisible field.
     *
     * @return \DateTime
     */
    public function getTimeNextVisible()
    {
        return $this->_timeNextVisible;
    }
    
    /**
     * Sets timeNextVisible field.
     *
     * @param \DateTime $timeNextVisible A UTC date/time value that represents when
     * the message will be visible on the queue.
     *
     * @internal
     *
     * @return void
     */
    protected function setTimeNextVisible(\DateTime $timeNextVisible)
    {
        Validate::isDate($timeNextVisible);
        
        $this->_timeNextVisible = $timeNextVisible;
    }
    
    /**
     * Gets popReceipt field.
     *
     * @return string
     */
    public function getPopReceipt()
    {
        return $this->_popReceipt;
    }
    
    /**
     * Sets popReceipt field.
     *
     * @param string $popReceipt The pop receipt of the queue message.
     *
     * @internal
     *
     * @return void
     */
    protected function setPopReceipt($popReceipt)
    {
        Validate::isString($popReceipt, 'popReceipt');
        $this->_popReceipt = $popReceipt;
    }
}
