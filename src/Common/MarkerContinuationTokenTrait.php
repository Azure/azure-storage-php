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
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Common;

use MicrosoftAzure\Storage\Common\Models\ContinuationToken;

/**
 * Trait implementing logic for continuation tokens that has nextMarker.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
trait MarkerContinuationTokenTrait
{
    private $continuationToken;

    /**
     * Setter for continuationToken
     *
     * @param string $continuationToken the continuation token to be set.
     */
    public function setContinuationToken($continuationToken)
    {
        $this->continuationToken = $continuationToken;
    }

    public function setMarker($marker)
    {
        $this->createContinuationTokenIfNotExist();
        $this->continuationToken->setNextMarker($marker);
    }

    /**
     * Getter for continuationToken
     *
     * @return string
     */
    public function getContinuationToken()
    {
        return $this->continuationToken;
    }

    /**
     * Gets the next marker to list/query items.
     *
     * @return string
     */
    public function getNextMarker()
    {
        $this->createContinuationTokenIfNotExist();
        return $this->continuationToken->getNextMarker();
    }

    /**
     * Gets for location for previous request.
     *
     * @return string
     */
    public function getLocation()
    {
        $this->createContinuationTokenIfNotExist();
        return $this->continuationToken->getLocation();
    }

    public function getLocationMode()
    {
        $this->createContinuationTokenIfNotExist();
        if ($this->continuationToken->getLocation() == '') {
            return parent::getLocationMode();
        } else {
            return $this->getLocation();
        }
    }
}
