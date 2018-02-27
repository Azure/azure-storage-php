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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Queue\Models;

use MicrosoftAzure\Storage\Queue\Models\ListQueuesOptions;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class ListQueuesOptions
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Queue\Models
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ListQueuesOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testSetPrefix()
    {
        // Setup
        $options = new ListQueuesOptions();
        $expected = 'myprefix';

        // Test
        $options->setPrefix($expected);

        // Assert
        $this->assertEquals($expected, $options->getPrefix());
    }

    public function testGetPrefix()
    {
        // Setup
        $options = new ListQueuesOptions();
        $expected = 'myprefix';
        $options->setPrefix($expected);

        // Test
        $actual = $options->getPrefix();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetMarker()
    {
        // Setup
        $options = new ListQueuesOptions();
        $expected = 'mymarker';

        // Test
        $options->setMarker($expected);

        // Assert
        $this->assertEquals($expected, $options->getNextMarker());
    }

    public function testSetMaxResults()
    {
        // Setup
        $options = new ListQueuesOptions();
        $expected = '3';

        // Test
        $options->setMaxResults($expected);

        // Assert
        $this->assertEquals($expected, $options->getMaxResults());
    }

    public function testGetMaxResults()
    {
        // Setup
        $options = new ListQueuesOptions();
        $expected = '3';
        $options->setMaxResults($expected);

        // Test
        $actual = $options->getMaxResults();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testSetIncludeMetadata()
    {
        // Setup
        $options = new ListQueuesOptions();
        $expected = true;

        // Test
        $options->setIncludeMetadata($expected);

        // Assert
        $this->assertEquals($expected, $options->getIncludeMetadata());
    }

    public function testGetIncludeMetadata()
    {
        // Setup
        $options = new ListQueuesOptions();
        $expected = true;
        $options->setIncludeMetadata($expected);

        // Test
        $actual = $options->getIncludeMetadata();

        // Assert
        $this->assertEquals($expected, $actual);
    }
}
