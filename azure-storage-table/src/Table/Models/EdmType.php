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
 * Basic Windows Azure EDM Types used for table entity properties.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class EdmType
{
    // @codingStandardsIgnoreStart

    const DATETIME = 'Edm.DateTime';
    const BINARY   = 'Edm.Binary';
    const BOOLEAN  = 'Edm.Boolean';
    const DOUBLE   = 'Edm.Double';
    const GUID     = 'Edm.Guid';
    const INT32    = 'Edm.Int32';
    const INT64    = 'Edm.Int64';
    const STRING   = 'Edm.String';

    public static function propertyType($value)
    {
        if (is_int($value)) {
            if ($value <= Resources::INT32_MAX && $value >= Resources::INT32_MIN) {
                return EdmType::INT32;
            } else {
                return EdmType::INT64;
            }
        } elseif (Utilities::isDouble($value)) {
            return EdmType::DOUBLE;
        } elseif (is_bool($value)) {
            return EdmType::BOOLEAN;
        } elseif ($value instanceof \DateTime) {
            return EdmType::DATETIME;
        } else {
            return EdmType::STRING;
        }
    }

    public static function typeRequired($type)
    {
        switch ($type) {
            case EdmType::BINARY:
            case EdmType::INT64:
            case EdmType::DATETIME:
            case EdmType::GUID:
            case EdmType::DOUBLE:
            case EdmType::BOOLEAN:
                return true;

            default:
                return false;
        }
    }

    /**
     * Converts the type to string if it's empty and validates the type.
     *
     * @param string $type The Edm type
     *
     * @internal
     *
     * @return string
     */
    public static function processType($type)
    {
        $type = empty($type) ? self::STRING : $type;
        Validate::isTrue(self::isValid($type), Resources::INVALID_EDM_MSG);

        return $type;
    }

    /**
     * Validates that the value associated with the EDM type is valid.
     *
     * @param string $type       The EDM type.
     * @param mixed  $value      The EDM value.
     * @param string &$condition The error message.
     *
     * @internal
     *
     * @return boolean
     *
     * @throws \InvalidArgumentException
     */
    public static function validateEdmValue($type, $value, &$condition = null)
    {
        // Having null value means that the user wants to remove the property name
        // associated with this value. Leave the value as null so this hold.
        if (is_null($value)) {
            return true;
        } else {
            switch ($type) {
            case EdmType::GUID:
            case EdmType::BINARY:
            case EdmType::STRING:
            case EdmType::INT64:
            case null:
                // NULL also is treated as EdmType::STRING
                $condition = 'is_string';
                return is_string($value);

            case EdmType::DOUBLE:
                $condition = 'is_double or is_string';
                return is_double($value) || is_int($value) || is_string($value);

            case EdmType::INT32:
                $condition = 'is_int or is_string';
                return is_int($value) || is_string($value);

            case EdmType::DATETIME:
                $condition = 'instanceof \DateTime';
                return $value instanceof \DateTime;

            case EdmType::BOOLEAN:
                $condition = 'is_bool';
                return is_bool($value);

            default:
                throw new \InvalidArgumentException();
            }
        }
    }

    /**
     * Serializes EDM value into proper value for sending it to Windows Azure.
     *
     * @param string $type  The EDM type.
     * @param mixed  $value The EDM value.
     *
     * @internal
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function serializeValue($type, $value)
    {
        switch ($type) {
        case null:
            return $value;

        case EdmType::INT32:
            return intval($value);

        case EdmType::INT64:
        case EdmType::GUID:
        case EdmType::STRING:
            return strval($value);

        case EdmType::DOUBLE:
            return strval($value);

        case EdmType::BINARY:
            return base64_encode($value);

        case EdmType::DATETIME:
            return Utilities::convertToEdmDateTime($value);

        case EdmType::BOOLEAN:
            return (is_null($value) ? '' : ($value == true ? true : false));

        default:
            throw new \InvalidArgumentException();
        }
    }

    /**
     * Serializes EDM value into proper value to be used in query.
     *
     * @param string $type  The EDM type.
     * @param mixed  $value The EDM value.
     *
     * @internal
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function serializeQueryValue($type, $value)
    {
        switch ($type) {
        case EdmType::DATETIME:
            $edmDate = Utilities::convertToEdmDateTime($value);
            return 'datetime\'' . $edmDate . '\'';

        case EdmType::BINARY:
            return 'X\'' . implode('', unpack("H*", $value)) . '\'';

        case EdmType::BOOLEAN:
            return ($value ? 'true' : 'false');

        case EdmType::DOUBLE:
        case EdmType::INT32:
            return $value;

        case EdmType::INT64:
            return $value . 'L';

        case EdmType::GUID:
            return 'guid\'' . $value . '\'';

        case null:
        case EdmType::STRING:
            // NULL also is treated as EdmType::STRING
            return '\'' . str_replace('\'', '\'\'', $value) . '\'';

        default:
            throw new \InvalidArgumentException();
        }
    }

    /**
     * Converts the value into its proper type.
     *
     * @param string $type  The edm type.
     * @param string $value The edm value.
     *
     * @internal
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function unserializeQueryValue($type, $value)
    {
        // Having null value means that the user wants to remove the property name
        // associated with this value. Leave the value as null so this hold.
        if (is_null($value)) {
            return null;
        } else {
            switch ($type) {
            case self::GUID:
            case self::STRING:
            case self::INT64:
            case null:
                return strval($value);

            case self::BINARY:
                return base64_decode($value);

            case self::DATETIME:
                return Utilities::convertToDateTime($value);

            case self::BOOLEAN:
                return Utilities::toBoolean($value);

            case self::DOUBLE:
                return doubleval($value);

            case self::INT32:
                return intval($value);

            default:
                throw new \InvalidArgumentException();
            }
        }
    }

    /**
     * Check if the $type belongs to valid header types.
     *
     * @param string $type The type string to check.
     *
     * @internal
     *
     * @return boolean
     */
    public static function isValid($type)
    {
        switch ($type) {
        case $type == self::DATETIME:
        case $type == self::BINARY:
        case $type == self::BOOLEAN:
        case $type == self::DOUBLE:
        case $type == self::GUID:
        case $type == self::INT32:
        case $type == self::INT64:
        case $type == self::STRING:
        case $type == null:
            // NULL also is treated as EdmType::STRING
            return true;

        default:
            return false;

        }
    }

    // @codingStandardsIgnoreEnd
}
