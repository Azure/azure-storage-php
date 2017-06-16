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
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Table\Internal;

use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Table\Models\AcceptJSONContentType;

/**
 * Holds code logic for optional option: Accept
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
trait AcceptOptionTrait
{
    private $accept = AcceptJSONContentType::MINIMAL_METADATA;

    /**
     * Sets accept content type.
     * AcceptableJSONContentType::NO_METADATA
     * AcceptableJSONContentType::MINIMAL_METADATA
     * AcceptableJSONContentType::FULL_METADATA
     *
     * @param string $accept The accept content type to be set.
     */
    public function setAccept($accept)
    {
        AcceptJSONContentType::validateAcceptContentType($accept);
        $this->accept = $accept;
    }

    /**
     * Gets accept content type.
     *
     * @return string
     */
    public function getAccept()
    {
        return $this->accept;
    }
}
