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

use MicrosoftAzure\Storage\Common\Internal\Validate;

/**
 * Optional parameters for createShare API
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateShareOptions extends FileServiceOptions
{
    private $quota;
    private $metadata;

    /**
     * Gets share quota.
     *
     * @return int
     */
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * Specifies the maximum size of the share, in gigabytes.
     * Must be greater than 0, and less than or equal to 5TB (5120)
     *
     * @param int $quota quota for the share
     *
     * @return void
     */
    public function setQuota($quota)
    {
        Validate::isInteger($quota, 'quota');
        $this->quota = $quota;
    }

    /**
     * Gets user defined metadata.
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Sets user defined metadata. This metadata should be added without the header
     * prefix (x-ms-meta-*).
     *
     * @param array $metadata user defined metadata object in array form.
     *
     * @return void
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Adds new metadata element. This element should be added without the header
     * prefix (x-ms-meta-*).
     *
     * @param string $key   metadata key element.
     * @param string $value metadata value element.
     *
     * @return void
     */
    public function addMetadata($key, $value)
    {
        $this->metadata[$key] = $value;
    }
}
