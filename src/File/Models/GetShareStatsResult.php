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
 * Holds result of getShareStats.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class GetShareStatsResult
{
    /**
     * The approximate size of the data stored on the share, rounded up to the
     * nearest gigabyte. Note that this value may not include all recently
     * created or recently resized files.
     *
     * @var int
     */
    private $shareUsage;

    /**
     * Gets file shareUsage.
     *
     * @return int
     */
    public function getShareUsage()
    {
        return $this->shareUsage;
    }

    /**
     * Sets file shareUsage.
     *
     * @param int $shareUsage value.
     *
     * @return void
     */
    protected function setShareUsage($shareUsage)
    {
        $this->shareUsage = $shareUsage;
    }
    
    /**
     * Create an instance using the response headers from the API call.
     *
     * @param  array  $parsed          The array contains parsed response body
     *
     * @internal
     *
     * @return GetShareStatsResult
     */
    public static function create(array $parsed)
    {
        $result   = new GetShareStatsResult();

        $result->setShareUsage(\intval(Utilities::tryGetValueInsensitive(
            Resources::XTAG_SHARE_USAGE,
            $parsed
        )));
        
        return $result;
    }
}
