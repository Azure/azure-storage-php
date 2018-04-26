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
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\Blob;

use MicrosoftAzure\Storage\Tests\Framework\SASFunctionalTestBase;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Tests for service SAS proxy tests.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BlobServiceSASFunctionalTest extends SASFunctionalTestBase
{
    public function testBlobServiceSAS()
    {
        $helper = new BlobSharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //setup the proxies for creating containers
        $this->setUpWithConnectionString($this->connectionString);

        $containerProxies = array();
        $containers = array();
        $containers[] = TestResources::getInterestingName('con');
        $this->safeCreateContainer($containers[0]);
        $containers[] = TestResources::getInterestingName('con');
        $this->safeCreateContainer($containers[1]);

        //Full permission for container0
        $containerProxies[] = $this->createProxyWithBlobSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'racwdl',
                Resources::RESOURCE_TYPE_CONTAINER,
                $containers[0]
            )
        );
        //Full permission for container1
        $containerProxies[] = $this->createProxyWithBlobSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'racwdl',
                Resources::RESOURCE_TYPE_CONTAINER,
                $containers[1]
            )
        );

        //Validate the permission for each of the proxy/container pair
        for ($i = 0; $i < 2; ++$i) {
            $proxy = $containerProxies[$i];
            $container = $containers[$i];
            //c
            $blob0 = TestResources::getInterestingName('blob');
            $proxy->createAppendBlob($container, $blob0);
            //l
            $result = $proxy->listBlobs($container);
            $this->assertEquals($blob0, $result->getBlobs()[0]->getName());
            //a
            $content = \openssl_random_pseudo_bytes(1024);
            $proxy->appendBlock($container, $blob0, $content);
            //w
            $blob1 = TestResources::getInterestingName('blob');
            $proxy->createBlockBlob($container, $blob1, $content);
            //r
            $actualContent = \stream_get_contents(
                $proxy->getBlob($container, $blob0)->getContentStream()
            );
            $this->assertEquals($content, $actualContent);
            $actualContent = \stream_get_contents(
                $proxy->getBlob($container, $blob1)->getContentStream()
            );
            $this->assertEquals($content, $actualContent);
            //d
            $proxy->deleteBlob($container, $blob0);
            $proxy->deleteBlob($container, $blob1);
            $result = $proxy->listBlobs($container);
            $this->assertEquals(0, \count($result->getBlobs()));
        }
        //Validate that a cross access with wrong proxy/container pair
        //would not be successful
        for ($i= 0; $i < 2; ++$i) {
            $proxy = $containerProxies[$i];
            $container = $containers[1 - $i];
            $blob0 = TestResources::getInterestingName('blob');
            //c
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $container, $blob0) {
                    $proxy->createAppendBlob($container, $blob0);
                }
            );
            //l
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $container) {
                    $proxy->listBlobs($container);
                }
            );
            //w
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $container) {
                    $proxy->createBlockBlob($container, 'myblob', 'testcontent');
                }
            );
        }

        //No list permission
        $containerProxy = $this->createProxyWithBlobSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'racwd',
                Resources::RESOURCE_TYPE_CONTAINER,
                $containers[0]
            )
        );
        $container = $containers[0];
        //l
        $this->validateServiceExceptionErrorMessage(
            'Server failed to authenticate the request.',
            function () use ($proxy, $container) {
                $proxy->listBlobs($container);
            }
        );
        //can c and d
        $blob0 = TestResources::getInterestingName('blob');
        $containerProxy->createAppendBlob($container, $blob0);
        $containerProxy->deleteBlob($container, $blob0);

        //blob permission
        $blob = TestResources::getInterestingName('blob');
        $blobProxy = $this->createProxyWithBlobSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'racwd',
                Resources::RESOURCE_TYPE_BLOB,
                $container . '/' . $blob
            )
        );
        //l cannot be performed
        $this->validateServiceExceptionErrorMessage(
            'The specified signed resource is not allowed for the this resource level',
            function () use ($blobProxy, $container) {
                $blobProxy->listBlobs($container);
            }
        );
        $content = \openssl_random_pseudo_bytes(20);
        //rcwd can be performed.
        $blobProxy->createBlockBlob($container, $blob, $content);
        $actual = stream_get_contents($blobProxy->getBlob($container, $blob)->getContentStream());
        $this->assertEquals($content, $actual);
        $blobProxy->deleteBlob($container, $blob);
    }

    private function createProxyWithBlobSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
            $testCase['signedResource'],
            $testCase['resourceName'],
            $testCase['signedPermissions'],
            $testCase['signedExpiry'],
            $testCase['signedStart'],
            $testCase['signedIP'],
            $testCase['signedProtocol'],
            $testCase['signedIdentifier'],
            $testCase['cacheControl'],
            $testCase['contentDisposition'],
            $testCase['contentEncoding'],
            $testCase['contentLanguage'],
            $testCase['contentType']
        );

        $accountName = $helper->getAccountName();

        return $this->createProxyWithSAS($sas, $accountName, $testCase['signedResource']);
    }
}
