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

use MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Unit tests for class ListDirectoriesAndFilesResult
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\File\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListDirectoriesAndFilesResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::create
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::setDirectories
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::getDirectories
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::setFiles
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::getFiles
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::getMaxResults
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::setMaxResults
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::getAccountName
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::setAccountName
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::setContinuationToken
     * @covers MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult::getContinuationToken
     */
    public function testCreate()
    {
        // Setup
        $samples = array();
        $samples[] =
            TestResources::getInterestingListDirectoriesAndFilesResultArray();
        $samples[] =
            TestResources::getInterestingListDirectoriesAndFilesResultArray(1, 0);
        $samples[] =
            TestResources::getInterestingListDirectoriesAndFilesResultArray(0, 1);
        $samples[] =
            TestResources::getInterestingListDirectoriesAndFilesResultArray(1, 1);
        $samples[] =
            TestResources::getInterestingListDirectoriesAndFilesResultArray(5, 5);
        
        // Test
        $actuals = array();
        $actuals[] = ListDirectoriesAndFilesResult::create($samples[0]);
        $actuals[] = ListDirectoriesAndFilesResult::create($samples[1]);
        $actuals[] = ListDirectoriesAndFilesResult::create($samples[2]);
        $actuals[] = ListDirectoriesAndFilesResult::create($samples[3]);
        $actuals[] = ListDirectoriesAndFilesResult::create($samples[4]);
        
        // Assert
        for ($i = 0; $i < count($samples); ++$i) {
            $sample = $samples[$i];
            $actual = $actuals[$i];
            $entries = $sample[Resources::QP_ENTRIES];
            if (empty($entries)) {
                $this->assertTrue(empty($actual->getDirectories()));
                $this->assertTrue(empty($actual->getFiles()));
            } else {
                if (array_key_exists(Resources::QP_DIRECTORY, $entries)) {
                    $this->assertEquals(
                        count($entries[Resources::QP_DIRECTORY]),
                        count($actual->getDirectories())
                    );
                } else {
                    $this->assertTrue(empty($actual->getDirectories()));
                }
                if (array_key_exists(Resources::QP_FILE, $entries)) {
                    $this->assertEquals(
                        count($entries[Resources::QP_FILE]),
                        count($actual->getFiles())
                    );
                } else {
                    $this->assertTrue(empty($actual->getFiles()));
                }
            }
            $this->assertEquals('myaccount', $actual->getAccountName());
            $this->assertEquals(5, $actual->getMaxResults());
            $this->assertEquals(
                $sample[Resources::QP_NEXT_MARKER],
                $actual->getNextMarker()
            );
        }
    }
}
