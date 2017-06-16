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
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Holds result of calling CopyFileResult wrapper
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CopyFileResult
{
    private $lastModified;
    private $etag;
    private $copyID;
    private $copyStatus;
    
    /**
     * Creates CopyFileResult object from parsed response header.
     *
     * @param array $headers HTTP response headers
     *
     * @internal
     *
     * @return CopyFileResult
     */
    public static function create(array $headers)
    {
        $result  = new CopyFileResult();
        $headers = array_change_key_case($headers);
        
        $date          = $headers[Resources::LAST_MODIFIED];
        $date          = Utilities::rfc1123ToDateTime($date);

        $result->setCopyStatus($headers[Resources::X_MS_COPY_STATUS]);
        $result->setCopyID($headers[Resources::X_MS_COPY_ID]);
        $result->setETag($headers[Resources::ETAG]);
        $result->setLastModified($date);
        
        return $result;
    }
    
    /**
     * Gets file lastModified.
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Sets file lastModified.
     *
     * @param \DateTime $lastModified value.
     *
     * @return void
     */
    protected function setLastModified(\DateTime $lastModified)
    {
        Validate::isDate($lastModified);
        $this->lastModified = $lastModified;
    }

    /**
     * Gets file etag.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * Sets file etag.
     *
     * @param string $etag value.
     *
     * @return void
     */
    protected function setETag($etag)
    {
        Validate::isString($etag, 'etag');
        $this->etag = $etag;
    }
    
    /**
     * Gets file copyID.
     *
     * @return string
     */
    public function getCopyID()
    {
        return $this->copyID;
    }

    /**
     * Sets file copyID.
     *
     * @param string $copyID value.
     *
     * @return void
     */
    protected function setCopyID($copyID)
    {
        Validate::isString($copyID, 'copyID');
        $this->copyID = $copyID;
    }
    
    /**
     * Gets copyStatus
     *
     * @return string
     */
    public function getCopyStatus()
    {
        return $this->copyStatus;
    }
    
    /**
     * Sets copyStatus
     *
     * @param string $copyStatus copyStatus to set
     *
     * @return void
     */
    protected function setCopyStatus($copyStatus)
    {
        Validate::isString($copyStatus, 'copyStatus');
        $this->copyStatus = $copyStatus;
    }
}
