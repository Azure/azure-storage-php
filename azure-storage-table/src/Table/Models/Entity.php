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

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Table\Internal\TableResources as Resources;

/**
 * Represents entity object used in tables
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class Entity
{
    private $_etag;
    private $_properties;

    /**
     * Validates if properties is valid or not.
     *
     * @param mixed $properties The properties array.
     *
     * @return void
     */
    private function _validateProperties($properties)
    {
        Validate::isArray($properties, 'entity properties');

        foreach ($properties as $key => $value) {
            Validate::canCastAsString($key, 'key');
            Validate::isTrue(
                $value instanceof Property,
                Resources::INVALID_PROP_MSG
            );
            Validate::isTrue(
                EdmType::validateEdmValue(
                    $value->getEdmType(),
                    $value->getValue(),
                    $condition
                ),
                sprintf(Resources::INVALID_PROP_VAL_MSG, $key, $condition)
            );
        }
    }

    /**
     * Gets property value and if the property name is not found return null.
     *
     * @param string $name The property name.
     *
     * @return mixed
     */
    public function getPropertyValue($name)
    {
        $p = Utilities::tryGetValue($this->_properties, $name);
        return is_null($p) ? null : $p->getValue();
    }

    /**
     * Sets property value.
     *
     * Note that if the property doesn't exist, it doesn't add it. Use addProperty
     * to add new properties.
     *
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     *
     * @return mixed
     */
    public function setPropertyValue($name, $value)
    {
        $p = Utilities::tryGetValue($this->_properties, $name);
        if (!is_null($p)) {
            $p->setValue($value);
        }
    }

    /**
     * Gets entity etag.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->_etag;
    }

    /**
     * Sets entity etag.
     *
     * @param string $etag The entity ETag value.
     *
     * @return void
     */
    public function setETag($etag)
    {
        $this->_etag = $etag;
    }

    /**
     * Gets entity PartitionKey.
     *
     * @return string
     */
    public function getPartitionKey()
    {
        return $this->getPropertyValue('PartitionKey');
    }

    /**
     * Sets entity PartitionKey.
     *
     * @param string $partitionKey The entity PartitionKey value.
     *
     * @return void
     */
    public function setPartitionKey($partitionKey)
    {
        $this->addProperty('PartitionKey', EdmType::STRING, $partitionKey);
    }

    /**
     * Gets entity RowKey.
     *
     * @return string
     */
    public function getRowKey()
    {
        return $this->getPropertyValue('RowKey');
    }

    /**
     * Sets entity RowKey.
     *
     * @param string $rowKey The entity RowKey value.
     *
     * @return void
     */
    public function setRowKey($rowKey)
    {
        $this->addProperty('RowKey', EdmType::STRING, $rowKey);
    }

    /**
     * Gets entity Timestamp.
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->getPropertyValue('Timestamp');
    }

    /**
     * Sets entity Timestamp.
     *
     * @param \DateTime $timestamp The entity Timestamp value.
     *
     * @return void
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->addProperty('Timestamp', EdmType::DATETIME, $timestamp);
    }

    /**
     * Gets the entity properties array.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Sets the entity properties array.
     *
     * @param array $properties The entity properties.
     *
     * @return void
     */
    public function setProperties(array $properties)
    {
        $this->_validateProperties($properties);
        $this->_properties = $properties;
    }

    /**
     * Gets property object from the entity properties.
     *
     * @param string $name The property name.
     *
     * @return Property|null
     */
    public function getProperty($name)
    {
        return Utilities::tryGetValue($this->_properties, $name);
    }

    /**
     * Sets entity property.
     *
     * @param string   $name     The property name.
     * @param Property $property The property object.
     *
     * @return void
     */
    public function setProperty($name, $property)
    {
        Validate::isTrue($property instanceof Property, Resources::INVALID_PROP_MSG);
        $this->_properties[$name] = $property;
    }

    /**
     * Creates new entity property.
     *
     * @param string $name     The property name.
     * @param string $edmType  The property edm type.
     * @param mixed  $value    The property value.
     * @param string $rawValue The raw value of the property.
     */
    public function addProperty($name, $edmType, $value, $rawValue = '')
    {
        $p = new Property();
        $p->setEdmType($edmType);
        $p->setValue($value);
        $p->setRawValue($rawValue);
        $this->setProperty($name, $p);
    }

    /**
     * Checks if the entity object is valid or not.
     * Valid means the partition and row key exists for this entity along with the
     * timestamp.
     *
     * @param string &$msg The error message.
     *
     * @internal
     *
     * @return boolean
     */
    public function isValid(&$msg = null)
    {
        try {
            $this->_validateProperties($this->_properties);
        } catch (\Exception $exc) {
            $msg = $exc->getMessage();
            return false;
        }

        if (is_null($this->getPartitionKey())
            || is_null($this->getRowKey())
        ) {
            $msg = Resources::NULL_TABLE_KEY_MSG;
            return false;
        } else {
            return true;
        }
    }
}
