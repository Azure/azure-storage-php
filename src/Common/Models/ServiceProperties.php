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
 * @package   MicrosoftAzure\Storage\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Common\Models;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\CORS;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;

/**
 * Encapsulates service properties
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ServiceProperties
{
    private $logging;
    private $metrics;
    private $corses;
    
    private static $xmlRootName = 'StorageServiceProperties';
    
    /**
     * Creates ServiceProperties object from parsed XML response.
     *
     * @internal
     * @param array $parsedResponse XML response parsed into array.
     *
     * @return ServiceProperties.
     */
    public static function create(array $parsedResponse)
    {
        $result = new ServiceProperties();
        $result->setLogging(Logging::create($parsedResponse[Resources::XTAG_LOGGING]));
        $result->setMetrics(Metrics::create($parsedResponse[Resources::XTAG_HOUR_METRICS]));
        if (array_key_exists(Resources::XTAG_CORS, $parsedResponse) &&
            $parsedResponse[Resources::XTAG_CORS] != null) {
            //There could be multiple CORS rules, so need to extract them all.
            $corses = array();
            $corsArray =
                $parsedResponse[Resources::XTAG_CORS][Resources::XTAG_CORS_RULE];
            if (count(array_filter(array_keys($corsArray), 'is_string')) > 0) {
                //single cors rule
                $corses[] = CORS::create($corsArray);
            } else {
                //multiple cors rule
                foreach ($corsArray as $cors) {
                    $corses[] = CORS::create($cors);
                }
            }
            
            $result->setCorses($corses);
        } else {
            $result->setCorses(array());
        }

        return $result;
    }
    
    /**
     * Gets logging element.
     *
     * @return Logging
     */
    public function getLogging()
    {
        return $this->logging;
    }
    
    /**
     * Sets logging element.
     *
     * @param Logging $logging new element.
     *
     * @return void
     */
    public function setLogging(Logging $logging)
    {
        $this->logging = clone $logging;
    }
    
    /**
     * Gets metrics element.
     *
     * @return Metrics
     */
    public function getMetrics()
    {
        return $this->metrics;
    }
    
    /**
     * Sets metrics element.
     *
     * @param Metrics $metrics new element.
     *
     * @return void
     */
    public function setMetrics(Metrics $metrics)
    {
        $this->metrics = clone $metrics;
    }

    /**
     * Gets corses element.
     *
     * @return CORS[]
     */
    public function getCorses()
    {
        return $this->corses;
    }
    
    /**
     * Sets corses element.
     *
     * @param CORS[] $corses new elements.
     *
     * @return void
     */
    public function setCorses(array $corses)
    {
        $this->corses = $corses;
    }
    
    /**
     * Converts this object to array with XML tags
     *
     * @internal
     * @return array
     */
    public function toArray()
    {
        $corsesArray = $this->getCorsesArray();
        return array(
            Resources::XTAG_LOGGING
                => !empty($this->getLogging()) ?
                    $this->getLogging()->toArray() : null,
            Resources::XTAG_HOUR_METRICS
                => !empty($this->getMetrics()) ?
                    $this->getMetrics()->toArray() : null,
            Resources::XTAG_CORS
                => !empty($corsesArray) ? $corsesArray : null

        );
    }

    /**
     * Gets the array that contains all the CORSes.
     *
     * @return array
     */
    private function getCorsesArray()
    {
        $corsesArray = array();
        if (count($this->getCorses()) == 1) {
            $corsesArray = array(
                Resources::XTAG_CORS_RULE => $this->getCorses()[0]->toArray()
            );
        } elseif ($this->getCorses() != array()) {
            foreach ($this->getCorses() as $cors) {
                $corsesArray[] = [Resources::XTAG_CORS_RULE => $cors->toArray()];
            }
        }
        
        return $corsesArray;
    }
    
    /**
     * Converts this current object to XML representation.
     *
     * @internal
     * @param XmlSerializer $xmlSerializer The XML serializer.
     *
     * @return string
     */
    public function toXml(XmlSerializer $xmlSerializer)
    {
        $properties = array(XmlSerializer::ROOT_NAME => self::$xmlRootName);
        return $xmlSerializer->serialize($this->toArray(), $properties);
    }
}
