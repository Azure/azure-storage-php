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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\unit\Common;

use MicrosoftAzure\Storage\Common\Internal\ServiceOptionsBase;

/**
 * Unit tests for class ServiceOptionsBase
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ServiceOptionsBaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceOptionsBase::setTimeout
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceOptionsBase::getTimeout
     */
    public function testSetGetTimeout()
    {
        // Setup
        $options = new ServiceOptionsBase();
        $value = 10;
        
        // Test
        $options->setTimeout($value);
        
        // Assert
        $this->assertEquals($value, $options->getTimeout());
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceOptionsBase::setRequestOptions
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceOptionsBase::getRequestOptions
     */
    public function testSetGetRequestOptions()
    {
        // Setup
        $options = new ServiceOptionsBase();
        $requestOptions = array(
            'middlewares' => 'test_middleware',
            'handler' => 'test_handler'
        );
        
        // Test
        $options->setRequestOptions($requestOptions);
        
        // Assert
        $this->assertEquals($requestOptions, $options->getRequestOptions());
    }
}
