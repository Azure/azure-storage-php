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
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Table\Models;

use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Models\ContinuationToken;

/**
 * Provides functionality and data structure for table continuation token.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class TableContinuationToken extends ContinuationToken
{
    private $nextPartitionKey;
    private $nextRowKey;
    private $nextTableName;

    public function __construct(
        $nextTableName = '',
        $nextPartitionKey = '',
        $nextRowKey = '',
        $location = ''
    ) {
        parent::__construct($location);
        $this->setNextTableName($nextTableName);
        $this->setNextPartitionKey($nextPartitionKey);
        $this->setNextRowKey($nextRowKey);
    }

    /**
     * Gets entity next partition key.
     *
     * @return string
     */
    public function getNextPartitionKey()
    {
        return $this->nextPartitionKey;
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
        Validate::isString($nextPartitionKey, 'nextPartitionKey');
        $this->nextPartitionKey = $nextPartitionKey;
    }
    
    /**
     * Gets entity next row key.
     *
     * @return string
     */
    public function getNextRowKey()
    {
        return $this->nextRowKey;
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
        Validate::isString($nextRowKey, 'nextRowKey');
        $this->nextRowKey = $nextRowKey;
    }

    /**
     * Gets nextTableName
     *
     * @return string
     */
    public function getNextTableName()
    {
        return $this->nextTableName;
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
        Validate::isString($nextTableName, 'nextTableName');
        $this->nextTableName = $nextTableName;
    }
}
