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

/**
 * The optional parameters for setBlobMetadata API.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.12.1
 * @link      https://github.com/azure/azure-storage-php
 */
class SetBlobMetadataOptions extends BlobServiceOptions
{
    /**
     * @var string
     */
    private $_leaseId;
    
    /**
     * @var AccessCondition
     */
    private $_accessCondition;
    
    /**
     * Gets lease Id for the blob
     *
     * @return string
     */
    public function getLeaseId()
    {
        return $this->_leaseId;
    }
    
    /**
     * Sets lease Id for the blob
     *
     * @param string $leaseId the blob lease id.
     *
     * @return void
     */
    public function setLeaseId($leaseId)
    {
        $this->_leaseId = $leaseId;
    }
    
    /**
     * Gets access condition
     *
     * @return AccessCondition
     */
    public function getAccessCondition()
    {
        return $this->_accessCondition;
    }
    
    /**
     * Sets access condition
     *
     * @param AccessCondition $accessCondition value to use.
     *
     * @return void
     */
    public function setAccessCondition(AccessCondition $accessCondition)
    {
        $this->_accessCondition = $accessCondition;
    }
}
