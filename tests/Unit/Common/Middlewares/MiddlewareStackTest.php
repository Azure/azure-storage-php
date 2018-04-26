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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Middlewares
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Unit\Common\Middlewares;

use MicrosoftAzure\Storage\Common\Middlewares\MiddlewareStack;

/**
 * Unit tests for class MiddlewareStack
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common\Middlewares
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class MiddlewareStackTest extends \PHPUnit\Framework\TestCase
{
    private $count;

    public function testPushAndApply()
    {
        $middlewares = $this->getInterestingMiddlewares(5);

        $stack = new MiddlewareStack();
        foreach ($middlewares as $middleware) {
            $stack->push($middleware);
        }

        $handler = function ($number, $callable) {
            if ($number != 4) {
                return $callable;
            }
            return $number;
        };

        $this->count = 0;

        $result = $stack->apply($handler);

        $this->assertEquals(4, $result);
        $this->assertEquals(5, $this->count);
    }

    private function getInterestingMiddlewares($count)
    {
        $middlewares = array();
        for ($i = $count; $i > 0; --$i) {
            $callable = function (callable $handler) use ($i) {
                ++$this->count;
                return \call_user_func($handler, $i - 1, $handler);
            };
            $middlewares[] = $callable;
        }

        return $middlewares;
    }
}
