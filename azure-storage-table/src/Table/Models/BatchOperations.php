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
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Table\Models;

use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Table\Internal\TableResources as Resources;

/**
 * Holds batch operation change set.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BatchOperations
{
    private $_operations;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->_operations = array();
    }

    /**
     * Gets the batch operations.
     *
     * @return array
     */
    public function getOperations()
    {
        return $this->_operations;
    }

    /**
     * Sets the batch operations.
     *
     * @param array $operations The batch operations.
     *
     * @return void
     */
    public function setOperations(array $operations)
    {
        $this->_operations = array();
        foreach ($operations as $operation) {
            $this->addOperation($operation);
        }
    }

    /**
     * Adds operation to the batch operations.
     *
     * @param mixed $operation The operation to add.
     *
     * @return void
     */
    public function addOperation($operation)
    {
        Validate::isTrue(
            $operation instanceof BatchOperation,
            Resources::INVALID_BO_TYPE_MSG
        );

        $this->_operations[] = $operation;
    }

    /**
     * Adds insertEntity operation.
     *
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     *
     * @return void
     */
    public function addInsertEntity($table, Entity $entity)
    {
        Validate::canCastAsString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');

        $operation = new BatchOperation();
        $type      = BatchOperationType::INSERT_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }

    /**
     * Adds updateEntity operation.
     *
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     *
     * @return void
     */
    public function addUpdateEntity($table, Entity $entity)
    {
        Validate::canCastAsString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');

        $operation = new BatchOperation();
        $type      = BatchOperationType::UPDATE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }

    /**
     * Adds mergeEntity operation.
     *
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     *
     * @return void
     */
    public function addMergeEntity($table, Entity $entity)
    {
        Validate::canCastAsString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');

        $operation = new BatchOperation();
        $type      = BatchOperationType::MERGE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }

    /**
     * Adds insertOrReplaceEntity operation.
     *
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     *
     * @return void
     */
    public function addInsertOrReplaceEntity($table, Entity $entity)
    {
        Validate::canCastAsString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');

        $operation = new BatchOperation();
        $type      = BatchOperationType::INSERT_REPLACE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }

    /**
     * Adds insertOrMergeEntity operation.
     *
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     *
     * @return void
     */
    public function addInsertOrMergeEntity($table, Entity $entity)
    {
        Validate::canCastAsString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');

        $operation = new BatchOperation();
        $type      = BatchOperationType::INSERT_MERGE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }

    /**
     * Adds deleteEntity operation.
     *
     * @param string $table        The table name.
     * @param string $partitionKey The entity partition key.
     * @param string $rowKey       The entity row key.
     * @param string $etag         The entity etag.
     *
     * @return void
     */
    public function addDeleteEntity($table, $partitionKey, $rowKey, $etag = null)
    {
        Validate::canCastAsString($table, 'table');
        Validate::isTrue(!is_null($partitionKey), Resources::NULL_TABLE_KEY_MSG);
        Validate::isTrue(!is_null($rowKey), Resources::NULL_TABLE_KEY_MSG);

        $operation = new BatchOperation();
        $type      = BatchOperationType::DELETE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ROW_KEY, $rowKey);
        $operation->addParameter(BatchOperationParameterName::BP_ETAG, $etag);
        $operation->addParameter(
            BatchOperationParameterName::BP_PARTITION_KEY,
            $partitionKey
        );
        $this->addOperation($operation);
    }
}
