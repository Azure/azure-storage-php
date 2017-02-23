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
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common;

/**
 * This class provides the base structure of service options, granting user to
 * send with different options for each individual API call.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.12.0
 * @link      https://github.com/azure/azure-storage-php
 */
class ServiceOptionsBase
{
    /**
     * @var array
     */
    protected $requestOptions;

    /**
     * @var string
     */
    protected $timeout;

    /**
     * Gets timeout.
     *
     * @return string
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Sets timeout.
     *
     * @param string $timeout value.
     *
     * @return void
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
    
    /**
     * Gets the saved request options
     *
     * @return array
     */
    public function getRequestOptions()
    {
        if ($this->requestOptions == null) {
            $this->requestOptions = [];
        }
        return $this->requestOptions;
    }

    /**
     * Sets the request options
     *
     * @param array $requestOptions the request options to be set.
     *
     * @return void
     */
    public function setRequestOptions(array $requestOptions)
    {
        $this->requestOptions = self::parseOptions($requestOptions);
    }

    /**
     * Parse the options, create new options selecting the useful setting in the
     * input.
     *
     * @param  array  $options the options user passed in.
     *
     * @return array
     */
    protected static function parseOptions(array $options)
    {
        //If guzzle is to be deprecated at some point, modifying this function
        //to what the new dependency use will do the job.
        $result = [];
        if (array_key_exists('middlewares', $options)) {
            $result['middlewares']      = $options['middlewares'];
        }
        if (array_key_exists('handler', $options)) {
            $result['handler']          = $options['handler'];
        }
        return $result;
    }
}
