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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
namespace MicrosoftAzure\Storage\Tests\Unit\Table\Models;

use MicrosoftAzure\Storage\Table\Models\InsertEntityResult;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

/**
 * Unit tests for class InsertEntityResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Table\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class InsertEntityResultTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers MicrosoftAzure\Storage\Table\Models\InsertEntityResult::setEntity
     * @covers MicrosoftAzure\Storage\Table\Models\InsertEntityResult::getEntity
     */
    public function testCreate()
    {
        // Setup
        $sampleBody     = TestResources::getInsertEntitySampleBody();
        $sampleHeaders  = TestResources::getInsertEntitySampleHeaders();
        $serializer     = new JsonODataReaderWriter();
        $expectedEntity = $serializer->parseEntity($sampleBody);
        $expectedEntity->setETag(Utilities::tryGetValue(
            $sampleHeaders,
            Resources::ETAG
        ));
        
        // Test
        $result = InsertEntityResult::create(
            $sampleBody,
            $sampleHeaders,
            $serializer
        );

        // Assert
        $this->assertEquals($expectedEntity, $result->getEntity());
    }
}
