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
 * @package   MicrosoftAzure\Storage\Table\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Table\Internal;

use MicrosoftAzure\Storage\Table\Models as TableModels;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Models\ServiceOptions;
use MicrosoftAzure\Storage\Common\Models\GetServiceStats;

/**
 * This interface has all REST APIs provided by Windows Azure for Table service.
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 * @see       http://msdn.microsoft.com/en-us/library/windowsazure/dd179423.aspx
 */
interface ITable
{
    /**
    * Gets the properties of the service.
    *
    * @param ServiceOptions $options optional table service options.
    *
    * @return \MicrosoftAzure\Storage\Common\Models\GetServicePropertiesResult
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452238.aspx
    */
    public function getServiceProperties(
        ServiceOptions $options = null
    );

    /**
     * Creates promise to get the properties of the service.
     *
     * @param ServiceOptions $options optional table service options.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452238.aspx
     */
    public function getServicePropertiesAsync(
        ServiceOptions $options = null
    );

    /**
    * Sets the properties of the service.
    *
    * @param ServiceProperties $serviceProperties new service properties
    * @param ServiceOptions    $options           optional parameters
    *
    * @return void
    *
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452240.aspx
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
     * @param ServiceProperties $serviceProperties new service properties
     * @param ServiceOptions    $options           optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452240.aspx
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
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-table-service-stats
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
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/get-table-service-stats
     */
    public function getServiceStatsAsync(ServiceOptions $options = null);
    
    /**
     * Quries tables in the given storage account.
     *
     * @param TableModels\QueryTablesOptions|string|TableModels\Filters\Filter $options
     * Could be optional parameters, table prefix or filter to apply.
     *
     * @return TableModels\QueryTablesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/query-tables
     */
    public function queryTables($options = null);

    /**
     * Creates promise to query the tables in the given storage account.
     *
     * @param TableModels\QueryTablesOptions|string|Models\Filters\Filter $options
     * Could be optional parameters, table prefix or filter to apply.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/query-tables
     */
    public function queryTablesAsync($options = null);
    
    /**
     * Creates new table in the storage account
     *
     * @param string                                $table   The name of the table.
     * @param TableModels\TableServiceCreateOptions $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-table
     */
    public function createTable(
        $table,
        TableModels\TableServiceCreateOptions $options = null
    );

    /**
     * Creates promise to create new table in the storage account
     *
     * @param string                                $table   The name of the table.
     * @param TableModels\TableServiceCreateOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/create-table
     */
    public function createTableAsync(
        $table,
        TableModels\TableServiceCreateOptions $options = null
    );
    
    /**
     * Gets the table.
     *
     * @param string                      $table   The The name of the table..
     * @param TableModels\GetTableOptions $options The optional parameters.
     *
     * @return TableModels\GetTableResult
     */
    public function getTable(
        $table,
        TableModels\GetTableOptions $options = null
    );

    /**
     * Creates the promise to get the table.
     *
     * @param string                      $table   The name of the table.
     * @param TableModels\GetTableOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getTableAsync(
        $table,
        TableModels\GetTableOptions $options = null
    );
    
    /**
     * Deletes the specified table and any data it contains.
     *
     * @param string                          $table   The name of the table.
     * @param TableModels\TableServiceOptions $options optional parameters
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179387.aspx
     */
    public function deleteTable(
        $table,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Creates promise to delete the specified table and any data it contains.
     *
     * @param string                          $table   The name of the table.
     * @param TableModels\TableServiceOptions $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179387.aspx
     */
    public function deleteTableAsync(
        $table,
        TableModels\TableServiceOptions$options = null
    );
    
    /**
     * Quries entities for the given table name
     *
     * @param string                                             $table   The name
     * of the table.
     * @param TableModels\QueryEntitiesOptions|string|TableModels\Filters\Filter $options
     * Could be optional parameters, query string or filter to apply.
     *
     * @return TableModels\QueryEntitiesResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/query-entities
     */
    public function queryEntities($table, $options = null);

    /**
     * Quries entities for the given table name
     *
     * @param string                                                   $table   The name of
     * the table.
     * @param Models\QueryEntitiesOptions|string|Models\Filters\Filter $options Coule be
     * optional parameters, query string or filter to apply.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/query-entities
     */
    public function queryEntitiesAsync($table, $options = null);
    
    /**
     * Inserts new entity to the table
     *
     * @param string                                $table   name of the table
     * @param TableModels\Entity                    $entity  table entity
     * @param TableModels\TableServiceCreateOptions $options optional parameters
     *
     * @return TableModels\InsertEntityResult
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/insert-entity
     */
    public function insertEntity(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceCreateOptions $options = null
    );

    /**
     * Inserts new entity to the table.
     *
     * @param string                                $table   name of the table.
     * @param TableModels\Entity                    $entity  table entity.
     * @param TableModels\TableServiceCreateOptions $options optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/insert-entity
     */
    public function insertEntityAsync(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceCreateOptions $options = null
    );
    
    /**
     * Updates an existing entity or inserts a new entity if it does not exist in the
     * table.
     *
     * @param string                          $table   name of the table
     * @param TableModels\Entity              $entity  table entity
     * @param TableModels\TableServiceOptions $options optional parameters
     *
     * @return TableModels\UpdateEntityResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452241.aspx
     */
    public function insertOrMergeEntity(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceOptions $options = null
    );
    
    /**
     * Creates promise to update an existing entity or inserts a new entity if
     * it does not exist in the table.
     *
     * @param string                          $table   name of the table
     * @param TableModels\Entity              $entity  table entity
     * @param TableModels\TableServiceOptions $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452241.aspx
     */
    public function insertOrMergeEntityAsync(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Replaces an existing entity or inserts a new entity if it does not exist in
     * the table.
     *
     * @param string                          $table   name of the table
     * @param TableModels\Entity              $entity  table entity
     * @param TableModels\TableServiceOptions $options optional parameters
     *
     * @return TableModels\UpdateEntityResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452242.aspx
     */
    public function insertOrReplaceEntity(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Creates a promise to replace an existing entity or inserts a new entity if it does not exist in the table.
     *
     * @param string                          $table   name of the table
     * @param TableModels\Entity              $entity  table entity
     * @param TableModels\TableServiceOptions $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452242.aspx
     */
    public function insertOrReplaceEntityAsync(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceOptions $options = null
    );
    
    /**
     * Updates an existing entity in a table. The Update Entity operation replaces
     * the entire entity and can be used to remove properties.
     *
     * @param string                          $table   The table name.
     * @param TableModels\Entity              $entity  The table entity.
     * @param TableModels\TableServiceOptions $options The optional parameters.
     *
     * @return TableModels\UpdateEntityResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179427.aspx
     */
    public function updateEntity(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Creates promise to update an existing entity in a table. The Update Entity
     * operation replaces the entire entity and can be used to remove properties.
     *
     * @param string                          $table   The table name.
     * @param TableModels\Entity              $entity  The table entity.
     * @param TableModels\TableServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179427.aspx
     */
    public function updateEntityAsync(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceOptions $options = null
    );
    
    /**
     * Updates an existing entity by updating the entity's properties. This operation
     * does not replace the existing entity, as the updateEntity operation does.
     *
     * @param string                          $table   The table name.
     * @param TableModels\Entity              $entity  The table entity.
     * @param TableModels\TableServiceOptions $options The optional parameters.
     *
     * @return TableModels\UpdateEntityResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179392.aspx
     */
    public function mergeEntity(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Creates promise to update an existing entity by updating the entity's
     * properties. This operation does not replace the existing entity, as the
     * updateEntity operation does.
     *
     * @param string                          $table   The table name.
     * @param TableModels\Entity              $entity  The table entity.
     * @param TableModels\TableServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179392.aspx
     */
    public function mergeEntityAsync(
        $table,
        TableModels\Entity $entity,
        TableModels\TableServiceOptions $options = null
    );
    
    /**
     * Deletes an existing entity in a table.
     *
     * @param string                          $table        The name of the table.
     * @param string                          $partitionKey The entity partition key.
     * @param string                          $rowKey       The entity row key.
     * @param TableModels\DeleteEntityOptions $options      The optional parameters.
     *
     * @return void
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135727.aspx
     */
    public function deleteEntity(
        $table,
        $partitionKey,
        $rowKey,
        TableModels\DeleteEntityOptions $options = null
    );

    /**
     * Creates promise to delete an existing entity in a table.
     *
     * @param string                          $table        The name of the table.
     * @param string                          $partitionKey The entity partition key.
     * @param string                          $rowKey       The entity row key.
     * @param TableModels\DeleteEntityOptions $options      The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135727.aspx
     */
    public function deleteEntityAsync(
        $table,
        $partitionKey,
        $rowKey,
        TableModels\DeleteEntityOptions $options = null
    );
    
    /**
     * Gets table entity.
     *
     * @param string                            $table        The name of the
     *                                                        table.
     * @param string                            $partitionKey The entity
     *                                                        partition key.
     * @param string                            $rowKey       The entity
     *                                                        row key.
     * @param TableModels\GetEntityOptions|null $options      The optional
     *                                                        parameters.
     *
     * @return TableModels\GetEntityResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179421.aspx
     */
    public function getEntity(
        $table,
        $partitionKey,
        $rowKey,
        TableModels\GetEntityOptions $options = null
    );

    /**
     * Creates promise to get table entity.
     *
     * @param string                            $table        The name of
     *                                                        the table.
     * @param string                            $partitionKey The entity
     *                                                        partition key.
     * @param string                            $rowKey       The entity
     *                                                        row key.
     * @param TableModels\GetEntityOptions|null $options      The optional
     *                                                        parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179421.aspx
     */
    public function getEntityAsync(
        $table,
        $partitionKey,
        $rowKey,
        TableModels\GetEntityOptions $options = null
    );
    
    /**
     * Does batch of operations on given table service.
     *
     * @param TableModels\BatchOperations     $operations the operations to apply
     * @param TableModels\TableServiceOptions $options    optional parameters
     *
     * @return TableModels\BatchResult
     */
    public function batch(
        TableModels\BatchOperations $operations,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Creates promise that does batch of operations on the table service.
     *
     * @param TableModels\BatchOperations     $batchOperations The operations to apply.
     * @param TableModels\TableServiceOptions $options         The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function batchAsync(
        TableModels\BatchOperations $batchOperations,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Gets the access control list (ACL)
     *
     * @param string                          $table   The container name.
     * @param TableModels\TableServiceOptions $options The optional parameters.
     *
     * @return TableModels\TableACL
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/get-table-acl
     */
    public function getTableAcl(
        $table,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Creates the promise to gets the access control list (ACL)
     *
     * @param string                          $table   The container name.
     * @param TableModels\TableServiceOptions $options The optional parameters.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/get-table-acl
     */
    public function getTableAclAsync(
        $table,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Sets the ACL.
     *
     * @param string                          $table   name
     * @param TableModels\TableACL            $acl     access control list
     * @param TableModels\TableServiceOptions $options optional parameters
     *
     * @return void
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/set-table-acl
     */
    public function setTableAcl(
        $table,
        TableModels\TableACL $acl,
        TableModels\TableServiceOptions $options = null
    );

    /**
     * Creates promise to set the ACL
     *
     * @param string                          $table   name
     * @param TableModels\TableACL            $acl     access control list
     * @param TableModels\TableServiceOptions $options optional parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     *
     * @see https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/set-table-acl
     */
    public function setTableAclAsync(
        $table,
        TableModels\TableACL $acl,
        TableModels\TableServiceOptions $options = null
    );
}
