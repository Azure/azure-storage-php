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
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * The result of calling PutBlock API.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class PutBlockResult
{
    private $_contentMD5;

    /**
     * Creates PutBlockResult object from the response of the put block request.
     *
     * @param array $headers The HTTP response headers in array representation.
     *
     * @internal
     *
     * @return PutBlockResult
     */
    public static function create(array $headers)
    {
        $result = new PutBlockResult();
        if (Utilities::arrayKeyExistsInsensitive(
            Resources::CONTENT_MD5,
            $headers
        )) {
            $result->setContentMD5(
                Utilities::tryGetValueInsensitive(Resources::CONTENT_MD5, $headers)
            );
        }
        
        return $result;
    }

    /**
     * Gets block content MD5.
     *
     * @return string
     */
    public function getContentMD5()
    {
        return $this->_contentMD5;
    }

    /**
     * Sets the content MD5 value.
     *
     * @param string $contentMD5 conent MD5 as a string.
     *
     * @return void
     */
    protected function setContentMD5($contentMD5)
    {
        $this->_contentMD5 = $contentMD5;
    }
}
