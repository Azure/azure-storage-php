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
use MicrosoftAzure\Storage\Common\Models\Range;

/**
 * Optional parameters for getFile.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class GetFileOptions extends FileServiceOptions
{
    private $range = null;
    private $rangeGetContentMD5 = false;

    /**
     * Gets File range.
     *
     * @return Range
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Sets File range.
     *
     * @param Range $range value.
     *
     * @return void
     */
    public function setRange(Range $range)
    {
        $this->range = $range;
    }

    /**
     * Gets File rangeGetContentMD5.
     *
     * @return bool
     */
    public function getRangeGetContentMD5()
    {
        return $this->rangeGetContentMD5;
    }

    /**
     * Sets File rangeGetContentMD5.
     *
     * @param bool $rangeGetContentMD5 value.
     *
     * @return void
     */
    public function setRangeGetContentMD5($rangeGetContentMD5)
    {
        Validate::isBoolean($rangeGetContentMD5);
        $this->rangeGetContentMD5 = (bool)$rangeGetContentMD5;
    }

    public function getRangeString()
    {
        if ($this->range != null) {
            return $this->range->getRangeString();
        } else {
            return null;
        }
    }
}
