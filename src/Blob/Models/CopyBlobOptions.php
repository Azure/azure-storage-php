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

/**
 * optional parameters for CopyBlobOptions wrapper
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class CopyBlobOptions extends BlobServiceOptions
{
    private $_sourceLeaseId;
    private $_sourceAccessConditions;
    private $_metadata;
    private $_sourceSnapshot;
    
    /**
     * Gets source access condition
     *
     * @return AccessCondition[]
     */
    public function getSourceAccessConditions()
    {
        return $this->_sourceAccessConditions;
    }
    
    /**
     * Sets source access condition
     *
     * @param array $sourceAccessCondition value to use.
     *
     * @return void
     */
    public function setSourceAccessConditions($sourceAccessConditions)
    {
        if (!is_null($sourceAccessConditions) &&
            is_array($sourceAccessConditions)) {
            $this->_sourceAccessConditions = $sourceAccessConditions;
        } else {
            $this->_sourceAccessConditions = [$sourceAccessConditions];
        }
    }
    
    /**
     * Gets metadata.
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * Sets metadata.
     *
     * @param array $metadata value.
     *
     * @return void
     */
    public function setMetadata(array $metadata)
    {
        $this->_metadata = $metadata;
    }
    
    /**
     * Gets source snapshot.
     *
     * @return string
     */
    public function getSourceSnapshot()
    {
        return $this->_sourceSnapshot;
    }
       
    /**
     * Sets source snapshot.
     *
     * @param string $sourceSnapshot value.
     *
     * @return void
     */
    public function setSourceSnapshot($sourceSnapshot)
    {
        $this->_sourceSnapshot = $sourceSnapshot;
    }
    
    /**
     * Gets source lease ID.
     *
     * @return string
     */
    public function getSourceLeaseId()
    {
        return $this->_sourceLeaseId;
    }

    /**
     * Sets source lease ID.
     *
     * @param string $sourceLeaseId value.
     *
     * @return void
     */
    public function setSourceLeaseId($sourceLeaseId)
    {
        $this->_sourceLeaseId = $sourceLeaseId;
    }
}
