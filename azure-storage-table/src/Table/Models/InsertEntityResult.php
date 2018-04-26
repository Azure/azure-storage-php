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
use MicrosoftAzure\Storage\Table\Internal\TableResources as Resources;
use MicrosoftAzure\Storage\Table\Internal\IODataReaderWriter;

/**
 * Holds result of calling insertEntity wrapper
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class InsertEntityResult
{
    private $entity;

    /**
     * Create InsertEntityResult object from HTTP response parts.
     *
     * @param string              $body            The HTTP response body.
     * @param array               $headers         The HTTP response headers.
     * @param IODataReaderWriter  $odataSerializer The OData reader and writer.
     *
     * @internal
     *
     * @return InsertEntityResult
     */
    public static function create($body, $headers, $odataSerializer)
    {
        $result = new InsertEntityResult();
        $entity = $odataSerializer->parseEntity($body);
        $entity->setETag(Utilities::tryGetValue($headers, Resources::ETAG));
        $result->setEntity($entity);

        return $result;
    }

    /**
     * Gets table entity.
     *
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sets table entity.
     *
     * @param Entity $entity The table entity instance.
     *
     * @return void
     */
    protected function setEntity($entity)
    {
        $this->entity = $entity;
    }
}
