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
 * @package   MicrosoftAzure\Storage\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Queue\Models;

use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Internal\ACLBase;
use MicrosoftAzure\Storage\Queue\Internal\QueueResources as Resources;

/**
 * Holds queue ACL members.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueueACL extends ACLBase
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        //setting the resource type to a default value.
        $this->setResourceType(Resources::RESOURCE_TYPE_QUEUE);
    }

    /**
     * Parses the given array into signed identifiers and create an instance of
     * QueueACL
     *
     * @param array $parsed The parsed response into array representation.
     *
     * @internal
     *
     * @return QueueACL
     */
    public static function create(array $parsed = null)
    {
        $result = new QueueACL();
        $result->fromXmlArray($parsed);

        return $result;
    }

    /**
     * Validate if the resource type is for the class.
     *
     * @param  string $resourceType the resource type to be validated.
     *
     * @throws \InvalidArgumentException
     *
     * @internal
     *
     * @return void
     */
    protected static function validateResourceType($resourceType)
    {
        Validate::isTrue(
            $resourceType == Resources::RESOURCE_TYPE_QUEUE,
            Resources::INVALID_RESOURCE_TYPE
        );
    }

    /**
     * Create a QueueAccessPolicy object.
     *
     * @return QueueAccessPolicy
     */
    protected static function createAccessPolicy()
    {
        return new QueueAccessPolicy();
    }
}
