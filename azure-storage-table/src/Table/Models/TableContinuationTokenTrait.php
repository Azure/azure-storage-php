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
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Table\Models;

/**
 * Trait implementing logic for Table continuation tokens.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
trait TableContinuationTokenTrait
{
    /** @var TableContinuationToken $continuationToken */
    private $continuationToken;

    /**
     * Setter for continuationToken
     *
     * @param TableContinuationToken $continuationToken the continuation token to be set.
     */
    public function setContinuationToken($continuationToken)
    {
        $this->continuationToken = $continuationToken;
    }

    /**
     * Getter for continuationToken
     *
     * @return TableContinuationToken
     */
    public function getContinuationToken()
    {
        return $this->continuationToken;
    }

    /**
     * Gets for location for previous request.
     *
     * @return string
     */
    public function getLocation()
    {
        if ($this->continuationToken == null) {
            return null;
        }
        return $this->continuationToken->getLocation();
    }

    public function getLocationMode()
    {
        if ($this->continuationToken == null) {
            return parent::getLocationMode();
        } elseif ($this->continuationToken->getLocation() == '') {
            return parent::getLocationMode();
        } else {
            return $this->getLocation();
        }
    }

    /**
     * Gets nextTableName
     *
     * @return string
     */
    public function getNextTableName()
    {
        if ($this->continuationToken == null) {
            return null;
        }
        return $this->continuationToken->getNextTableName();
    }

    /**
     * Gets entity next partition key.
     *
     * @return string
     */
    public function getNextPartitionKey()
    {
        if ($this->continuationToken == null) {
            return null;
        }
        return $this->continuationToken->getNextPartitionKey();
    }

    /**
     * Gets entity next row key.
     *
     * @return string
     */
    public function getNextRowKey()
    {
        if ($this->continuationToken == null) {
            return null;
        }
        return $this->continuationToken->getNextRowKey();
    }

    /**
     * Sets entity next row key.
     *
     * @param string $nextRowKey The entity next row key value.
     *
     * @return void
     */
    public function setNextRowKey($nextRowKey)
    {
        if ($this->continuationToken == null) {
            $this->setContinuationToken(new TableContinuationToken());
        }
        $this->continuationToken->setNextRowKey($nextRowKey);
    }

    /**
     * Sets entity next partition key.
     *
     * @param string $nextPartitionKey The entity next partition key value.
     *
     * @return void
     */
    public function setNextPartitionKey($nextPartitionKey)
    {
        if ($this->continuationToken == null) {
            $this->setContinuationToken(new TableContinuationToken());
        }
        $this->continuationToken->setNextPartitionKey($nextPartitionKey);
    }

    /**
     * Sets nextTableName
     *
     * @param string $nextTableName value
     *
     * @return void
     */
    public function setNextTableName($nextTableName)
    {
        if ($this->continuationToken == null) {
            $this->setContinuationToken(new TableContinuationToken());
        }
        $this->continuationToken->setNextTableName($nextTableName);
    }
}
