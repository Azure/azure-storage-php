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
use MicrosoftAzure\Storage\Blob\Models\Container;
use MicrosoftAzure\Storage\Tests\Unit\Common\Internal\UtilitiesTest;

/**
 * Container to hold list container response object.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Blob\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListContainersResult
{
    private $_containers;
    private $_prefix;
    private $_marker;
    private $_nextMarker;
    private $_maxResults;
    private $_accountName;

    /**
     * Creates ListBlobResult object from parsed XML response.
     *
     * @param array $parsedResponse XML response parsed into array.
     *
     * @internal
     *
     * @return ListContainersResult
     */
    public static function create(array $parsedResponse)
    {
        $result               = new ListContainersResult();
        $serviceEndpoint      = Utilities::tryGetKeysChainValue(
            $parsedResponse,
            Resources::XTAG_ATTRIBUTES,
            Resources::XTAG_SERVICE_ENDPOINT
        );
        $result->setAccountName(Utilities::tryParseAccountNameFromUrl(
            $serviceEndpoint
        ));
        $result->setPrefix(Utilities::tryGetValue(
            $parsedResponse,
            Resources::QP_PREFIX
        ));
        $result->setMarker(Utilities::tryGetValue(
            $parsedResponse,
            Resources::QP_MARKER
        ));
        $result->setNextMarker(Utilities::tryGetValue(
            $parsedResponse,
            Resources::QP_NEXT_MARKER
        ));
        $result->setMaxResults(Utilities::tryGetValue(
            $parsedResponse,
            Resources::QP_MAX_RESULTS
        ));
        $containers   = array();
        $rawContainer = array();
        
        if (!empty($parsedResponse['Containers'])) {
            $containersArray = $parsedResponse['Containers']['Container'];
            $rawContainer    = Utilities::getArray($containersArray);
        }
        
        foreach ($rawContainer as $value) {
            $container = new Container();
            $container->setName($value['Name']);
            $container->setUrl($serviceEndpoint . $value['Name']);
            $container->setMetadata(
                Utilities::tryGetValue($value, Resources::QP_METADATA, array())
            );
            $properties = new ContainerProperties();
            $date       = $value['Properties']['Last-Modified'];
            $date       = Utilities::rfc1123ToDateTime($date);
            $properties->setLastModified($date);
            $properties->setETag($value['Properties']['Etag']);
            $container->setProperties($properties);
            $containers[] = $container;
        }
        $result->setContainers($containers);
        return $result;
    }

    /**
     * Sets containers.
     *
     * @param array $containers list of containers.
     *
     * @return void
     */
    protected function setContainers(array $containers)
    {
        $this->_containers = array();
        foreach ($containers as $container) {
            $this->_containers[] = clone $container;
        }
    }
    
    /**
     * Gets containers.
     *
     * @return Container[]
     */
    public function getContainers()
    {
        return $this->_containers;
    }

    /**
     * Gets prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Sets prefix.
     *
     * @param string $prefix value.
     *
     * @return void
     */
    protected function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    }

    /**
     * Gets marker.
     *
     * @return string
     */
    public function getMarker()
    {
        return $this->_marker;
    }

    /**
     * Sets marker.
     *
     * @param string $marker value.
     *
     * @return void
     */
    protected function setMarker($marker)
    {
        $this->_marker = $marker;
    }

    /**
     * Gets max results.
     *
     * @return string
     */
    public function getMaxResults()
    {
        return $this->_maxResults;
    }

    /**
     * Sets max results.
     *
     * @param string $maxResults value.
     *
     * @return void
     */
    protected function setMaxResults($maxResults)
    {
        $this->_maxResults = $maxResults;
    }

    /**
     * Gets next marker.
     *
     * @return string
     */
    public function getNextMarker()
    {
        return $this->_nextMarker;
    }

    /**
     * Sets next marker.
     *
     * @param string $nextMarker value.
     *
     * @return void
     */
    protected function setNextMarker($nextMarker)
    {
        $this->_nextMarker = $nextMarker;
    }
    
    /**
     * Gets account name.
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->_accountName;
    }

    /**
     * Sets account name.
     *
     * @param string $accountName value.
     *
     * @return void
     */
    protected function setAccountName($accountName)
    {
        $this->_accountName = $accountName;
    }
}
