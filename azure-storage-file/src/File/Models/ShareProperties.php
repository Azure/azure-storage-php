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
use MicrosoftAzure\Storage\File\Internal\FileResources as Resources;

/**
 * Holds share properties fields
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ShareProperties
{
    private $lastModified;
    private $etag;
    private $quota;

    /**
     * Creates an instance with given response array.
     *
     * @param  array  $parsedResponse The response array.
     *
     * @return ShareProperties
     */
    public static function create(array $parsedResponse)
    {
        $result = new ShareProperties();
        $date   = $parsedResponse[Resources::QP_LAST_MODIFIED];
        $date   = Utilities::rfc1123ToDateTime($date);
        $result->setLastModified($date);
        $result->setETag($parsedResponse[Resources::QP_ETAG]);
        $result->setQuota($parsedResponse[Resources::QP_QUOTA]);
        return $result;
    }

    /**
     * Gets share lastModified.
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Sets share lastModified.
     *
     * @param \DateTime $lastModified value.
     *
     * @return void
     */
    public function setLastModified(\DateTime $lastModified)
    {
        $this->lastModified = $lastModified;
    }

    /**
     * Gets share etag.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * Sets share etag.
     *
     * @param string $etag value.
     *
     * @return void
     */
    public function setETag($etag)
    {
        $this->etag = $etag;
    }

    /**
     * Gets share quota.
     *
     * @return string
     */
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * Sets share quota.
     *
     * @param string $quota value.
     *
     * @return void
     */
    public function setQuota($quota)
    {
        $this->quota = $quota;
    }
}
