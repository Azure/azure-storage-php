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

use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * WindowsAzure share object.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class Share
{
    private $name;
    private $metadata;
    private $properties;

    /**
     * Creates an instance with given response array.
     *
     * @param  array  $parsedResponse The response array.
     *
     * @return Share
     */
    public static function create(array $parsedResponse)
    {
        $result = new Share();
        $result->setName($parsedResponse[Resources::QP_NAME]);
        $result->setMetadata(
            Utilities::tryGetValue($parsedResponse, Resources::QP_METADATA, array())
        );
        $result->setProperties(ShareProperties::create(
            $parsedResponse[Resources::QP_PROPERTIES]
        ));
        return $result;
    }

    /**
     * Gets share name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets share name.
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
     * Gets share metadata.
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Sets share metadata.
     *
     * @param array $metadata value.
     *
     * @return void
     */
    public function setMetadata(array $metadata = null)
    {
        $this->metadata = $metadata;
    }
    
    /**
     * Gets share properties
     *
     * @return ShareProperties
     */
    public function getProperties()
    {
        return $this->properties;
    }
    
    /**
     * Sets share properties
     *
     * @param ShareProperties $properties share properties
     *
     * @return void
     */
    public function setProperties(ShareProperties $properties)
    {
        $this->properties = $properties;
    }
}
