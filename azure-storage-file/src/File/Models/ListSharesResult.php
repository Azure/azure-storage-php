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

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Models\MarkerContinuationToken;
use MicrosoftAzure\Storage\Common\MarkerContinuationTokenTrait;
use MicrosoftAzure\Storage\File\Internal\FileResources as Resources;

/**
 * Share to hold list Share response object.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListSharesResult
{
    use MarkerContinuationTokenTrait;

    private $shares;
    private $prefix;
    private $marker;
    private $maxResults;
    private $accountName;

    /**
     * Creates ListSharesResult object from parsed XML response.
     *
     * @param array  $parsedResponse XML response parsed into array.
     * @param string $location       Contains the location for the previous
     *                               request.
     *
     * @internal
     *
     * @return ListSharesResult
     */
    public static function create(array $parsedResponse, $location = '')
    {
        $result               = new ListSharesResult();
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

        $nextMarker = Utilities::tryGetValue(
            $parsedResponse,
            Resources::QP_NEXT_MARKER
        );

        if ($nextMarker != null) {
            $result->setContinuationToken(
                new MarkerContinuationToken(
                    $nextMarker,
                    $location
                )
            );
        }

        $result->setMaxResults(Utilities::tryGetValue(
            $parsedResponse,
            Resources::QP_MAX_RESULTS
        ));
        $shares = array();
        $shareArrays = array();

        if (!empty($parsedResponse[Resources::QP_SHARES])) {
            $array = $parsedResponse[Resources::QP_SHARES][Resources::QP_SHARE];
            $shareArrays    = Utilities::getArray($array);
        }

        foreach ($shareArrays as $shareArray) {
            $shares[] = Share::create($shareArray);
        }

        $result->setShares($shares);
        return $result;
    }

    /**
     * Sets shares.
     *
     * @param array $shares list of shares.
     *
     * @return void
     */
    protected function setShares(array $shares)
    {
        $this->shares = array();
        foreach ($shares as $share) {
            $this->shares[] = clone $share;
        }
    }

    /**
     * Gets shares.
     *
     * @return Share[]
     */
    public function getShares()
    {
        return $this->shares;
    }

    /**
     * Gets prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
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
        $this->prefix = $prefix;
    }

    /**
     * Gets marker.
     *
     * @return string
     */
    public function getMarker()
    {
        return $this->marker;
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
        $this->marker = $marker;
    }

    /**
     * Gets max results.
     *
     * @return string
     */
    public function getMaxResults()
    {
        return $this->maxResults;
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
        $this->maxResults = $maxResults;
    }

    /**
     * Gets account name.
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->accountName;
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
        $this->accountName = $accountName;
    }
}
