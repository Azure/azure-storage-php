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
 * @package   MicrosoftAzure\Storage\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Blob\Models;

use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Represents blob properties
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BlobProperties
{
    private $_lastModified;
    private $_etag;
    private $_contentType;
    private $_contentLength;
    private $_contentEncoding;
    private $_contentLanguage;
    private $_contentMD5;
    private $_contentRange;
    private $_cacheControl;
    private $_contentDisposition;
    private $_blobType;
    private $_leaseStatus;
    private $_leaseState;
    private $_leaseDuration;
    private $_sequenceNumber;
    private $_committedBlockCount;
    private $_copyState;
    
    /**
     * Creates BlobProperties object from $parsed response in array representation of XML elements
     *
     * @param array $parsed parsed response in array format.
     *
     * @internal
     *
     * @return BlobProperties
     */
    public static function createFromXml(array $parsed)
    {
        $result = new BlobProperties();
        $clean  = array_change_key_case($parsed);
        
        $result->setCommonBlobProperties($clean);
        $result->setLeaseStatus(Utilities::tryGetValue($clean, 'leasestatus'));
        $result->setLeaseState(Utilities::tryGetValue($clean, 'leasestate'));
        $result->setLeaseDuration(Utilities::tryGetValue($clean, 'leaseduration'));
        $result->setCopyState(CopyState::createFromXml($clean));
        
        return $result;
    }

    /**
     * Creates BlobProperties object from $parsed response in array representation of http headers
     *
     * @param array $parsed parsed response in array format.
     *
     * @internal
     *
     * @return BlobProperties
     */
    public static function createFromHttpHeaders(array $parsed)
    {
        $result = new BlobProperties();
        $clean  = array_change_key_case($parsed);

        $result->setCommonBlobProperties($clean);
        
        $result->setBlobType(Utilities::tryGetValue($clean, Resources::X_MS_BLOB_TYPE));
        $result->setLeaseStatus(Utilities::tryGetValue($clean, Resources::X_MS_LEASE_STATUS));
        $result->setLeaseState(Utilities::tryGetValue($clean, Resources::X_MS_LEASE_STATE));
        $result->setLeaseDuration(Utilities::tryGetValue($clean, Resources::X_MS_LEASE_DURATION));
        $result->setCommittedBlockCount(
            intval(Utilities::tryGetValue($clean, Resources::X_MS_BLOB_COMMITTED_BLOCK_COUNT))
        );
        $result->setCopyState(CopyState::createFromHttpHeaders($clean));

        return $result;
    }

    /**
     * Gets blob lastModified.
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * Sets blob lastModified.
     *
     * @param \DateTime $lastModified value.
     *
     * @return void
     */
    public function setLastModified(\DateTime $lastModified)
    {
        Validate::isDate($lastModified);
        $this->_lastModified = $lastModified;
    }

    /**
     * Gets blob etag.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->_etag;
    }

    /**
     * Sets blob etag.
     *
     * @param string $etag value.
     *
     * @return void
     */
    public function setETag($etag)
    {
        $this->_etag = $etag;
    }
    
    /**
     * Gets blob contentType.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * Sets blob contentType.
     *
     * @param string $contentType value.
     *
     * @return void
     */
    public function setContentType($contentType)
    {
        $this->_contentType = $contentType;
    }
    
    /**
     * Gets blob contentRange.
     *
     * @return string
     */
    public function getContentRange()
    {
        return $this->_contentRange;
    }

    /**
     * Sets blob contentRange.
     *
     * @param string $contentRange value.
     *
     * @return void
     */
    public function setContentRange($contentRange)
    {
        $this->_contentRange = $contentRange;
    }
    
    /**
     * Gets blob contentLength.
     *
     * @return integer
     */
    public function getContentLength()
    {
        return $this->_contentLength;
    }

    /**
     * Sets blob contentLength.
     *
     * @param integer $contentLength value.
     *
     * @return void
     */
    public function setContentLength($contentLength)
    {
        Validate::isInteger($contentLength, 'contentLength');
        $this->_contentLength = $contentLength;
    }
    
    /**
     * Gets blob contentEncoding.
     *
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->_contentEncoding;
    }

    /**
     * Sets blob contentEncoding.
     *
     * @param string $contentEncoding value.
     *
     * @return void
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->_contentEncoding = $contentEncoding;
    }
    
    /**
     * Gets blob contentLanguage.
     *
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->_contentLanguage;
    }

    /**
     * Sets blob contentLanguage.
     *
     * @param string $contentLanguage value.
     *
     * @return void
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->_contentLanguage = $contentLanguage;
    }
    
    /**
     * Gets blob contentMD5.
     *
     * @return string
     */
    public function getContentMD5()
    {
        return $this->_contentMD5;
    }

    /**
     * Sets blob contentMD5.
     *
     * @param string $contentMD5 value.
     *
     * @return void
     */
    public function setContentMD5($contentMD5)
    {
        $this->_contentMD5 = $contentMD5;
    }
    
    /**
     * Gets blob cacheControl.
     *
     * @return string
     */
    public function getCacheControl()
    {
        return $this->_cacheControl;
    }

    /**
     * Sets blob cacheControl.
     *
     * @param string $cacheControl value.
     *
     * @return void
     */
    public function setCacheControl($cacheControl)
    {
        $this->_cacheControl = $cacheControl;
    }
    
    /**
     * Gets blob contentDisposition.
     *
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->_contentDisposition;
    }

    /**
     * Sets blob contentDisposition.
     *
     * @param string $contentDisposition value.
     *
     * @return void
     */
    public function setContentDisposition($contentDisposition)
    {
        $this->_contentDisposition = $contentDisposition;
    }
    
    /**
     * Gets blob blobType.
     *
     * @return string
     */
    public function getBlobType()
    {
        return $this->_blobType;
    }

    /**
     * Sets blob blobType.
     *
     * @param string $blobType value.
     *
     * @return void
     */
    public function setBlobType($blobType)
    {
        $this->_blobType = $blobType;
    }
    
    /**
     * Gets blob leaseStatus.
     *
     * @return string
     */
    public function getLeaseStatus()
    {
        return $this->_leaseStatus;
    }

    /**
     * Sets blob leaseStatus.
     *
     * @param string $leaseStatus value.
     *
     * @return void
     */
    public function setLeaseStatus($leaseStatus)
    {
        $this->_leaseStatus = $leaseStatus;
    }
    
    /**
     * Gets blob lease state.
     *
     * @return string
     */
    public function getLeaseState()
    {
        return $this->_leaseState;
    }

    /**
     * Sets blob lease state.
     *
     * @param string $leaseState value.
     *
     * @return void
     */
    public function setLeaseState($leaseState)
    {
        $this->_leaseState = $leaseState;
    }
    
    /**
     * Gets blob lease duration.
     *
     * @return string
     */
    public function getLeaseDuration()
    {
        return $this->_leaseDuration;
    }

    /**
     * Sets blob leaseStatus.
     *
     * @param string $leaseDuration value.
     *
     * @return void
     */
    public function setLeaseDuration($leaseDuration)
    {
        $this->_leaseDuration = $leaseDuration;
    }
    
    /**
     * Gets blob sequenceNumber.
     *
     * @return int
     */
    public function getSequenceNumber()
    {
        return $this->_sequenceNumber;
    }

    /**
     * Sets blob sequenceNumber.
     *
     * @param int $sequenceNumber value.
     *
     * @return void
     */
    public function setSequenceNumber($sequenceNumber)
    {
        Validate::isInteger($sequenceNumber, 'sequenceNumber');
        $this->_sequenceNumber = $sequenceNumber;
    }

    /**
     * Gets the number of committed blocks present in the blob.
     *
     * @return int
     */
    public function getCommittedBlockCount()
    {
        return $this->_committedBlockCount;
    }

    /**
     * Sets the number of committed blocks present in the blob.
     *
     * @param int $committedBlockCount the number of committed blocks present in the blob.
     *
     * @return void
     */
    public function setCommittedBlockCount($committedBlockCount)
    {
        $this->_committedBlockCount = $committedBlockCount;
    }

    /**
     * Gets copy state of the blob.
     *
     * @return CopyState
     */
    public function getCopyState()
    {
        return $this->_copyState;
    }

    /**
     * Sets the copy state of the blob.
     *
     * @param CopyState $copyState the copy state of the blob.
     *
     * @return void
     */
    public function setCopyState($copyState)
    {
        $this->_copyState = $copyState;
    }

    private function setCommonBlobProperties(array $clean)
    {
        $date = Utilities::tryGetValue($clean, Resources::LAST_MODIFIED);
        if (!is_null($date)) {
            $date = Utilities::rfc1123ToDateTime($date);
            $this->setLastModified($date);
        }

        $this->setBlobType(Utilities::tryGetValue($clean, 'blobtype'));

        $this->setContentLength(intval($clean[Resources::CONTENT_LENGTH]));
        $this->setETag(Utilities::tryGetValue($clean, Resources::ETAG));
        $this->setSequenceNumber(
            intval(
                Utilities::tryGetValue($clean, Resources::X_MS_BLOB_SEQUENCE_NUMBER)
            )
        );
        $this->setContentRange(
            Utilities::tryGetValue($clean, Resources::CONTENT_RANGE)
        );
        $this->setCacheControl(
            Utilities::tryGetValue($clean, Resources::CACHE_CONTROL)
        );
        $this->setContentDisposition(
            Utilities::tryGetValue($clean, Resources::CONTENT_DISPOSITION)
        );
        $this->setContentEncoding(
            Utilities::tryGetValue($clean, Resources::CONTENT_ENCODING)
        );
        $this->setContentLanguage(
            Utilities::tryGetValue($clean, Resources::CONTENT_LANGUAGE)
        );
        $this->setContentMD5(
            Utilities::tryGetValue($clean, Resources::CONTENT_MD5)
        );
        $this->setContentType(
            Utilities::tryGetValue($clean, Resources::CONTENT_TYPE)
        );
    }
}
