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
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Table\Models;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Table\Internal\TableResources as Resources;

/**
 * QueryTablesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueryTablesResult
{
    use TableContinuationTokenTrait;

    private $_tables;

    /**
     * Creates new QueryTablesResult object
     *
     * @param array $headers The HTTP response headers
     * @param array $entries The table entriess
     *
     * @internal
     *
     * @return QueryTablesResult
     */
    public static function create(array $headers, array $entries)
    {
        $result  = new QueryTablesResult();
        $headers = array_change_key_case($headers);

        $result->setTables($entries);

        $nextTableName = Utilities::tryGetValue(
            $headers,
            Resources::X_MS_CONTINUATION_NEXTTABLENAME
        );

        if ($nextTableName != null) {
            $result->setContinuationToken(
                new TableContinuationToken(
                    $nextTableName,
                    '',
                    '',
                    Utilities::getLocationFromHeaders($headers)
                )
            );
        }

        return $result;
    }

    /**
     * Gets tables
     *
     * @return array
     */
    public function getTables()
    {
        return $this->_tables;
    }

    /**
     * Sets tables
     *
     * @param array $tables value
     *
     * @return void
     */
    protected function setTables(array $tables)
    {
        $this->_tables = $tables;
    }
}
