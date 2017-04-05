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

use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Holds result of listBlobBlocks
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListBlobBlocksResult
{
    private $_lastModified;
    private $_etag;
    private $_contentType;
    private $_contentLength;
    private $_committedBlocks;
    private $_uncommittedBlocks;
    
    /**
     * Gets block entries from parsed response
     *
     * @param array  $parsed HTTP response
     * @param string $type   Block type
     *
     * @return array
     */
    private static function _getEntries(array $parsed, $type)
    {
        $entries = array();
        
        if (is_array($parsed)) {
            $rawEntries = array();
         
            if (array_key_exists($type, $parsed)
                &&     is_array($parsed[$type])
                &&     !empty($parsed[$type])
            ) {
                $rawEntries = Utilities::getArray($parsed[$type]['Block']);
            }
            
            foreach ($rawEntries as $value) {
                $entries[$value['Name']] = $value['Size'];
            }
        }
        
        return $entries;
    }
    
    /**
     * Creates ListBlobBlocksResult from given response headers and parsed body
     *
     * @param array $headers HTTP response headers
     * @param array $parsed  HTTP response body in array representation
     *
     * @internal
     *
     * @return ListBlobBlocksResult
     */
    public static function create(array $headers, array $parsed)
    {
        $result = new ListBlobBlocksResult();
        $clean  = array_change_key_case($headers);
        
        $result->setETag(Utilities::tryGetValue($clean, Resources::ETAG));
        $date = Utilities::tryGetValue($clean, Resources::LAST_MODIFIED);
        if (!is_null($date)) {
            $date = Utilities::rfc1123ToDateTime($date);
            $result->setLastModified($date);
        }
        $result->setContentLength(
            intval(
                Utilities::tryGetValue($clean, Resources::X_MS_BLOB_CONTENT_LENGTH)
            )
        );
        $result->setContentType(
            Utilities::tryGetValue($clean, Resources::CONTENT_TYPE)
        );
        
        $result->_uncommittedBlocks = self::_getEntries(
            $parsed,
            'UncommittedBlocks'
        );
        $result->_committedBlocks   = self::_getEntries($parsed, 'CommittedBlocks');
        
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
    protected function setLastModified(\DateTime $lastModified)
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
    protected function setETag($etag)
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
    protected function setContentType($contentType)
    {
        $this->_contentType = $contentType;
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
    protected function setContentLength($contentLength)
    {
        Validate::isInteger($contentLength, 'contentLength');
        $this->_contentLength = $contentLength;
    }
    
    /**
     * Gets uncommitted blocks
     *
     * @return array
     */
    public function getUncommittedBlocks()
    {
        return $this->_uncommittedBlocks;
    }
    
    /**
     * Sets uncommitted blocks
     *
     * @param array $uncommittedBlocks The uncommitted blocks entries
     *
     * @return void
     */
    protected function setUncommittedBlocks(array $uncommittedBlocks)
    {
        $this->_uncommittedBlocks = $uncommittedBlocks;
    }
    
    /**
     * Gets committed blocks
     *
     * @return array
     */
    public function getCommittedBlocks()
    {
        return $this->_committedBlocks;
    }
    
    /**
     * Sets committed blocks
     *
     * @param array $committedBlocks The committed blocks entries
     *
     * @return void
     */
    protected function setCommittedBlocks(array $committedBlocks)
    {
        $this->_committedBlocks = $committedBlocks;
    }
}
