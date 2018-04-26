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

/**
 * Holds share ACL
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class GetShareACLResult
{
    private $shareACL;
    private $lastModified;
    private $etag;

    /**
     * Parses the given array into signed identifiers
     *
     * @param string    $etag         share etag
     * @param \DateTime $lastModified last modification date
     * @param array     $parsed       parsed response into array
     * representation
     *
     * @internal
     *
     * @return self
     */
    public static function create(
        $etag,
        \DateTime $lastModified,
        array $parsed = null
    ) {
        $result = new GetShareAclResult();
        $result->setETag($etag);
        $result->setLastModified($lastModified);
        $acl = ShareACL::create($parsed);
        $result->setShareAcl($acl);

        return $result;
    }

    /**
     * Gets share ACL
     *
     * @return ShareACL
     */
    public function getShareAcl()
    {
        return $this->shareACL;
    }

    /**
     * Sets share ACL
     *
     * @param ShareACL $shareACL value.
     *
     * @return void
     */
    protected function setShareAcl(ShareACL $shareACL)
    {
        $this->shareACL = $shareACL;
    }

    /**
     * Gets share lastModified.
     *
     * @return \DateTime.
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
    protected function setLastModified(\DateTime $lastModified)
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
    protected function setETag($etag)
    {
        $this->etag = $etag;
    }
}
