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
 * Optional parameters for createFile.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CreateFileOptions extends FileServiceOptions
{
    private $contentType;
    private $contentEncoding;
    private $contentLanguage;
    private $contentMD5;
    private $cacheControl;
    private $contentDisposition;
    private $metadata;
    private $contentLength;
    
    /**
     * Gets File contentType.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets File contentType.
     *
     * @param string $contentType value.
     *
     * @return void
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }
    
    /**
     * Gets contentEncoding.
     *
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * Sets contentEncoding.
     *
     * @param string $contentEncoding value.
     *
     * @return void
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->contentEncoding = $contentEncoding;
    }
    
    /**
     * Gets contentLanguage.
     *
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * Sets contentLanguage.
     *
     * @param string $contentLanguage value.
     *
     * @return void
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->contentLanguage = $contentLanguage;
    }
    
    /**
     * Gets contentMD5.
     *
     * @return string
     */
    public function getContentMD5()
    {
        return $this->contentMD5;
    }

    /**
     * Sets contentMD5.
     *
     * @param string $contentMD5 value.
     *
     * @return void
     */
    public function setContentMD5($contentMD5)
    {
        $this->contentMD5 = $contentMD5;
    }
    
    /**
     * Gets cacheControl.
     *
     * @return string
     */
    public function getCacheControl()
    {
        return $this->cacheControl;
    }
    
    /**
     * Sets cacheControl.
     *
     * @param string $cacheControl value to use.
     *
     * @return void
     */
    public function setCacheControl($cacheControl)
    {
        $this->cacheControl = $cacheControl;
    }
    
    /**
     * Gets content disposition.
     *
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }
    
    /**
     * Sets content disposition.
     *
     * @param string $contentDisposition value to use.
     *
     * @return void
     */
    public function setContentDisposition($contentDisposition)
    {
        $this->contentDisposition = $contentDisposition;
    }
    
    /**
     * Gets File metadata.
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Sets File metadata.
     *
     * @param array $metadata value.
     *
     * @return void
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }
    
    /**
     * Gets File contentLength.
     *
     * @return int
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * Sets File contentLength.
     *
     * @param int $contentLength value.
     *
     * @return void
     */
    public function setContentLength($contentLength)
    {
        Validate::isInteger($contentLength, 'contentLength');
        $this->contentLength = $contentLength;
    }
}
