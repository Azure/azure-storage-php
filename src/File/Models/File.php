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
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\File\Models;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Represents windows azure file object
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class File
{
    private $name;
    private $length;

    /**
     * Gets file name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets file name.
     *
     * @param string $name value.
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Gets file length.
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Sets file length.
     *
     * @param int $length value.
     *
     * @return void
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * Creates a File object using the parsed array.
     *
     * @param  array $parsed The parsed array that contains the object information.
     *
     * @return File
     */
    public static function create(array $parsed)
    {
        $result = new File();
        $name = Utilities::tryGetValue($parsed, Resources::QP_NAME);
        $result->setName($name);
        $properties = Utilities::tryGetValue($parsed, Resources::QP_PROPERTIES);
        $length = \intval(Utilities::tryGetValue($properties, Resources::QP_CONTENT_LENGTH));
        $result->setLength($length);
        return $result;
    }
}
