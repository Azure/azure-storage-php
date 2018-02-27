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
 * @package   MicrosoftAzure\Storage\Tests\Unit\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\File\Models;

use MicrosoftAzure\Storage\File\Models\ShareProperties;
use MicrosoftAzure\Storage\File\Models\Share;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Unit tests for class Share
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ShareTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $responseArray = TestResources::getInterestingShareArray();
        $share = Share::create($responseArray);
        $expectedMeta = Utilities::tryGetValue($responseArray, Resources::QP_METADATA, array());
        $expectedName = $responseArray[Resources::QP_NAME];
        $expectedProperties = ShareProperties::create(
            $responseArray[Resources::QP_PROPERTIES]
        );

        $this->assertEquals($expectedMeta, $share->getMetadata());
        $this->assertEquals($expectedName, $share->getName());
        $this->assertEquals($expectedProperties, $share->getProperties());
    }
}
