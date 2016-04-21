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
 * @package   MicrosoftAzure\Storage\Tests\Functional\Table\Enums
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\Table\Enums;

class MutatePivot
{
    const CHANGE_VALUES   = 'ChangeValues';
    const ADD_PROPERTY    = 'AddProperty';
    const REMOVE_PROPERTY = 'RemoveProperty';
    const NULL_PROPERTY   = 'NullProperty';
    public static function values()
    {
        return array('ChangeValues', 'AddProperty', 'RemoveProperty', 'NullProperty');
    }
}

