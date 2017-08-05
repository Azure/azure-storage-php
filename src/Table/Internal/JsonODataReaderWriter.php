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

use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Table\Models\Entity;

/**
 * Serializes and unserializes results from table wrapper calls
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class JsonODataReaderWriter implements IODataReaderWriter
{
    /**
     * Constructs JSON representation for table entry.
     *
     * @param string $name The name of the table.
     *
     * @return string
     */
    public function getTable($name)
    {
        return json_encode(array(Resources::JSON_TABLE_NAME => $name));
    }
    
    /**
     * Parses one table entry.
     *
     * @param mixed $body The HTTP response body.
     *
     * @return string
     */
    public function parseTable($body)
    {
        $table = json_decode($body, true);
        return $table[Resources::JSON_TABLE_NAME];
    }
    
    /**
     * Constructs array of tables from HTTP response body.
     *
     * @param string $body The HTTP response body.
     *
     * @return array
     */
    public function parseTableEntries($body)
    {
        $tables     = array();
        $result     = json_decode($body, true);

        $rawEntries = $result[Resources::JSON_VALUE];
        
        foreach ($rawEntries as $entry) {
            $tables[] = $entry[Resources::JSON_TABLE_NAME];
        }
        
        return $tables;
    }
    
    /**
     * Constructs JSON representation for entity.
     *
     * @param Entity $entity The entity instance.
     *
     * @return string
     */
    public function getEntity(Entity $entity)
    {
        $entityProperties = $entity->getProperties();
        $properties       = array();
        
        foreach ($entityProperties as $name => $property) {
            $edmType    = $property->getEdmType();
            $edmValue   = $property->getValue();
            
            if (is_null($edmValue)) {
                // No @odata.type JSON property needed for null value
                $properties[$name] = null;
            } else {
                if (is_null($edmType)) {
                    $edmType = EdmType::propertyType($edmValue);
                }
                
                $value             = EdmType::serializeValue($edmType, $edmValue);
                $properties[$name] = $value;

                if (EdmType::typeRequired($edmType)) {
                    $properties[$name . Resources::JSON_ODATA_TYPE_SUFFIX] = $edmType;
                }
            }
        }
        
        return json_encode($properties);
    }
    
    /**
     * Constructs entity from HTTP response body.
     *
     * @param string $body The HTTP response body.
     *
     * @return Entity
     */
    public function parseEntity($body)
    {
        $rawEntity = json_decode($body, true);
        return $this->parseOneEntity($rawEntity);
    }
    
    /**
     * Constructs array of entities from HTTP response body.
     *
     * @param string $body The HTTP response body.
     *
     * @return array
     */
    public function parseEntities($body)
    {
        $rawEntities = json_decode($body, true);
        $entities   = array();

        foreach ($rawEntities[Resources::JSON_VALUE] as $rawEntity) {
            $entities[] = $this->parseOneEntity($rawEntity);
        }
        
        return $entities;
    }

    private function parseOneEntity($rawEntity)
    {
        $entity = new Entity();
        $timestamp;
        $etag;

        if (array_key_exists(Resources::JSON_TIMESTAMP, $rawEntity)) {
            $rawTimestamp   = $rawEntity[Resources::JSON_TIMESTAMP];
            $timestamp      = EdmType::unserializeQueryValue(EdmType::DATETIME, $rawTimestamp);
            
            $entity->addProperty(
                Resources::JSON_TIMESTAMP,
                EdmType::DATETIME,
                $timestamp
            );
        }

        // Make sure etag is set
        if (array_key_exists(Resources::JSON_ODATA_ETAG, $rawEntity)) {
            $etag = (string)$rawEntity[Resources::JSON_ODATA_ETAG];
        } else {
            $etag = null;
        }
        $entity->setETag($etag);

        foreach ($rawEntity as $key => $value) {
            if ($key === Resources::JSON_TIMESTAMP) {
                continue;
            }

            // Ignore keys end with Resources::JSON_ODATA_TYPE_SUFFIX
            if (strlen($key) > strlen(Resources::JSON_ODATA_TYPE_SUFFIX) &&
                strpos(
                    $key,
                    Resources::JSON_ODATA_TYPE_SUFFIX,
                    strlen($key) - strlen(Resources::JSON_ODATA_TYPE_SUFFIX)
                ) !== false) {
                continue;
            }

            if (strpos($key, "odata.") === 0) {
                continue;
            }

            $edmType;
            if (array_key_exists($key . Resources::JSON_ODATA_TYPE_SUFFIX, $rawEntity)) {
                $edmType = $rawEntity[$key . Resources::JSON_ODATA_TYPE_SUFFIX];
            } else {
                // Guess the property type
                $edmType = EdmType::propertyType($value);
            }
            //Store the raw value of the string representation.
            $rawValue = \is_string($value) ? $value : '';
            $value      = EdmType::unserializeQueryValue((string)$edmType, $value);
            $entity->addProperty(
                (string)$key,
                (string)$edmType,
                $value,
                $rawValue
            );
        }

        return $entity;
    }
}
