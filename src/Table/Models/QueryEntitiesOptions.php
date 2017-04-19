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

use MicrosoftAzure\Storage\Table\Models\TableContinuationToken;
use MicrosoftAzure\Storage\Table\Models\TableContinuationTokenTrait;

/**
 * Holds optional parameters for queryEntities API
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueryEntitiesOptions extends TableServiceOptions
{
    use TableContinuationTokenTrait;

    private $_query;
    
    /**
     * Constructs new QueryEntitiesOptions object.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_query = new Query();
    }
    
    /**
     * Gets query.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->_query;
    }
    
    /**
     * Sets query.
     *
     * You can either sets the whole query *or* use the individual query functions
     * like (setTop).
     *
     * @param string $query The query instance.
     *
     * @return void
     */
    public function setQuery($query)
    {
        $this->_query = $query;
    }
    
    /**
     * Gets filter.
     *
     * @return Filters\Filter
     */
    public function getFilter()
    {
        return $this->_query->getFilter();
    }

    /**
     * Sets filter.
     *
     * You can either use this individual function or use setQuery to set the whole
     * query object.
     *
     * @param Filters\Filter $filter value.
     *
     * @return void
     */
    public function setFilter(Filters\Filter $filter)
    {
        $this->_query->setFilter($filter);
    }
    
    /**
     * Gets top.
     *
     * @return integer
     */
    public function getTop()
    {
        return $this->_query->getTop();
    }

    /**
     * Sets top.
     *
     * You can either use this individual function or use setQuery to set the whole
     * query object.
     *
     * @param integer $top value.
     *
     * @return void
     */
    public function setTop($top)
    {
        $this->_query->setTop($top);
    }
    
    /**
     * Adds a field to select fields.
     *
     * You can either use this individual function or use setQuery to set the whole
     * query object.
     *
     * @param string $field The value of the field.
     *
     * @return void
     */
    public function addSelectField($field)
    {
        $this->_query->addSelectField($field);
    }
    
    /**
     * Gets selectFields.
     *
     * @return array
     */
    public function getSelectFields()
    {
        return $this->_query->getSelectFields();
    }

    /**
     * Sets selectFields.
     *
     * You can either use this individual function or use setQuery to set the whole
     * query object.
     *
     * @param array $selectFields value.
     *
     * @return void
     */
    public function setSelectFields(array $selectFields = null)
    {
        $this->_query->setSelectFields($selectFields);
    }
}
