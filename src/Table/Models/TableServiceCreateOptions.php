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
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Table\Models;

use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Table\Internal\AcceptOptionTrait;

/**
 * Holds optional parameters for createTable and insertEntity.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class TableServiceCreateOptions extends TableServiceOptions
{
    use AcceptOptionTrait;

    private $doesReturnContent;

    public function __construct()
    {
        parent::__construct();
        $this->doesReturnContent = false;
    }

    /**
     * Sets does return content.
     *
     * @param bool $doesReturnContent if the reponse returns content.
     */
    public function setDoesReturnContent($doesReturnContent)
    {
        Validate::isBoolean($doesReturnContent);
        $this->doesReturnContent = $doesReturnContent;
    }

    /**
     * Gets does return content.
     *
     * @return bool
     */
    public function getDoesReturnContent()
    {
        return $this->doesReturnContent;
    }
}
