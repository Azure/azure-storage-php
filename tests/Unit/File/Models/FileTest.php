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

use MicrosoftAzure\Storage\File\Models\File;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Unit tests for class File
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\File\Models\File::create
     * @covers MicrosoftAzure\Storage\File\Models\File::setLength
     * @covers MicrosoftAzure\Storage\File\Models\File::getLength
     * @covers MicrosoftAzure\Storage\File\Models\File::getName
     * @covers MicrosoftAzure\Storage\File\Models\File::setName
     */
    public function testCreate()
    {
        // Setup
        $listArray =
            TestResources::getInterestingListDirectoriesAndFilesResultArray(0, 5);
        $samples = $listArray[Resources::QP_ENTRIES][Resources::QP_FILE];
        
        // Test
        $actuals = array();
        $actuals[] = File::create($samples[0]);
        $actuals[] = File::create($samples[1]);
        $actuals[] = File::create($samples[2]);
        $actuals[] = File::create($samples[3]);
        $actuals[] = File::create($samples[4]);
        
        // Assert
        for ($i = 0; $i < count($samples); ++$i) {
            $sample = $samples[$i];
            $actual = $actuals[$i];

            $this->assertEquals($sample[Resources::QP_NAME], $actual->getName());
            $this->assertEquals(
                $sample[Resources::QP_PROPERTIES][Resources::QP_CONTENT_LENGTH],
                $actual->getLength()
            );
        }
    }
}
