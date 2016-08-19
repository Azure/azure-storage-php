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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common;
use MicrosoftAzure\Storage\Common\ServiceException;

/**
 * Unit tests for class ServiceException
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.10.2
 * @link      https://github.com/azure/azure-storage-php
 */
class ServiceExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MicrosoftAzure\Storage\Common\ServiceException::__construct
     */
    public function test__construct()
    {
        // Setup
        $code = '210';
        $error = 'Invalid value provided';
        $reason = 'Value can\'t be null';
        
        // Test
        $e = new ServiceException($code, $error, $reason);
        
        // Assert
        $this->assertEquals($code, $e->getCode());
        $this->assertEquals($error, $e->getErrorText());
        $this->assertEquals($reason, $e->getErrorReason());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\ServiceException::getErrorText
     */
    public function testGetErrorText()
    {
        // Setup
        $code = '210';
        $error = 'Invalid value provided';
        $reason = 'Value can\'t be null';
        $e = new ServiceException($code, $error, $reason);
        
        // Test
        $actualError = $e->getErrorText();
        // Assert
        $this->assertEquals($error, $actualError);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Common\ServiceException::getErrorReason
     */
    public function testGetErrorReason()
    {
        // Setup
        $code = '210';
        $error = 'Invalid value provided';
        $reason = 'Value can\'t be null';
        $e = new ServiceException($code, $error, $reason);

        // Test
        $actualErrorReason = $e->getErrorReason();
        
        // Assert
        $this->assertEquals($reason, $actualErrorReason);
    }
}


