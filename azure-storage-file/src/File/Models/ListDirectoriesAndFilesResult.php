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
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\File\Models;

use MicrosoftAzure\Storage\File\Internal\FileResources as Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Models\MarkerContinuationToken;
use MicrosoftAzure\Storage\Common\MarkerContinuationTokenTrait;

/**
 * Share to hold list directories and files response object.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListDirectoriesAndFilesResult
{
    use MarkerContinuationTokenTrait;

    private $directories;
    private $files;
    private $maxResults;
    private $accountName;
    private $marker;

    /**
     * Creates ListDirectoriesAndFilesResult object from parsed XML response.
     *
     * @param array  $parsedResponse XML response parsed into array.
     * @param string $location       Contains the location for the previous
     *                               request.
     *
     * @internal
     *
     * @return ListDirectoriesAndFilesResult
     */
    public static function create(array $parsedResponse, $location = '')
    {
        $result               = new ListDirectoriesAndFilesResult();
        $serviceEndpoint      = Utilities::tryGetKeysChainValue(
            $parsedResponse,
            Resources::XTAG_ATTRIBUTES,
            Resources::XTAG_SERVICE_ENDPOINT
        );
        $result->setAccountName(Utilities::tryParseAccountNameFromUrl(
            $serviceEndpoint
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

        $result->setMarker(Utilities::tryGetValue(
            $parsedResponse,
            Resources::QP_MARKER
        ));

        $entries = Utilities::tryGetValue(
            $parsedResponse,
            Resources::QP_ENTRIES
        );

        if (empty($entries)) {
            $result->setDirectories(array());
            $result->setFiles(array());
        } else {
            $directoriesArray = Utilities::tryGetValue(
                $entries,
                Resources::QP_DIRECTORY
            );
            $filesArray = Utilities::tryGetValue(
                $entries,
                Resources::QP_FILE
            );

            $directories = array();
            $files = array();

            if ($directoriesArray != null) {
                if (array_key_exists(Resources::QP_NAME, $directoriesArray)) {
                    $directoriesArray = [$directoriesArray];
                }
                foreach ($directoriesArray as $directoryArray) {
                    $directories[] = Directory::create($directoryArray);
                }
            }

            if ($filesArray != null) {
                if (array_key_exists(Resources::QP_NAME, $filesArray)) {
                    $filesArray = [$filesArray];
                }
                foreach ($filesArray as $fileArray) {
                    $files[] = File::create($fileArray);
                }
            }

            $result->setDirectories($directories);
            $result->setFiles($files);
        }

        return $result;
    }

    /**
     * Sets Directories.
     *
     * @param array $directories list of directories.
     *
     * @return void
     */
    protected function setDirectories(array $directories)
    {
        $this->directories = array();
        foreach ($directories as $directory) {
            $this->directories[] = clone $directory;
        }
    }

    /**
     * Gets directories.
     *
     * @return Directory[]
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * Sets files.
     *
     * @param array $files list of files.
     *
     * @return void
     */
    protected function setFiles(array $files)
    {
        $this->files = array();
        foreach ($files as $file) {
            $this->files[] = clone $file;
        }
    }

    /**
     * Gets files.
     *
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
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
