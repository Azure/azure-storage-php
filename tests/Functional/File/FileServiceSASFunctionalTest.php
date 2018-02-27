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

namespace MicrosoftAzure\Storage\Tests\Functional\File;

use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\File\Models\CreateFileFromContentOptions;
use MicrosoftAzure\Storage\Tests\Framework\SASFunctionalTestBase;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Tests\Functional\File\FileSharedAccessSignatureHelperMock;

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
class FileServiceSASFunctionalTest extends SASFunctionalTestBase
{
    public function testFileServiceSAS()
    {
        $helper = new FileSharedAccessSignatureHelperMock(
            $this->serviceSettings->getName(),
            $this->serviceSettings->getKey()
        );

        //setup the proxies for creating shares
        $this->setUpWithConnectionString($this->connectionString);

        $shareProxies = array();
        $shares = array();
        $shares[] = TestResources::getInterestingName('sha');
        $this->safeCreateShare($shares[0]);
        $shares[] = TestResources::getInterestingName('sha');
        $this->safeCreateShare($shares[1]);

        //Full permission for share0
        $shareProxies[] = $this->createProxyWithFileSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'rcwdl',
                Resources::RESOURCE_TYPE_SHARE,
                $shares[0]
            )
        );
        //Full permission for share1
        $shareProxies[] = $this->createProxyWithFileSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'rcwdl',
                Resources::RESOURCE_TYPE_SHARE,
                $shares[1]
            )
        );

        //Validate the permission for each of the proxy/share pair
        for ($i = 0; $i < 2; ++$i) {
            $proxy = $shareProxies[$i];
            $share = $shares[$i];
            //cw
            $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_1);
            $file = TestResources::getInterestingName('file');
            $proxy->createFileFromContent($share, $file, $content);
            //l
            $result = $proxy->listDirectoriesAndFiles($share);
            $this->assertEquals($file, $result->getFiles()[0]->getName());
            //r
            $actualContent = \stream_get_contents(
                $proxy->getFile($share, $file)->getContentStream()
            );
            $this->assertEquals($content, $actualContent);
            //d
            $proxy->deleteFile($share, $file);
            $result = $proxy->listDirectoriesAndFiles($share);
            $this->assertEquals(0, \count($result->getFiles()));
        }
        //Validate that a cross access with wrong proxy/share pair
        //would not be successful
        for ($i= 0; $i < 2; ++$i) {
            $proxy = $shareProxies[$i];
            $share = $shares[1 - $i];
            $file = TestResources::getInterestingName('file');
            //c
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $share, $file) {
                    $proxy->createFile($share, $file, Resources::MB_IN_BYTES_1);
                }
            );
            //l
            $this->validateServiceExceptionErrorMessage(
                'Server failed to authenticate the request.',
                function () use ($proxy, $share) {
                    $proxy->listDirectoriesAndFiles($share);
                }
            );
        }

        //No list permission
        $shareProxy = $this->createProxyWithFileSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'rcwd',
                Resources::RESOURCE_TYPE_SHARE,
                $shares[0]
            )
        );
        $share = $shares[0];
        //l
        $this->validateServiceExceptionErrorMessage(
            'Server failed to authenticate the request.',
            function () use ($proxy, $share) {
                $proxy->listDirectoriesAndFiles($share);
            }
        );
        //can c and d
        $file = TestResources::getInterestingName('file');
        $shareProxy->createFile($share, $file, Resources::MB_IN_BYTES_1);
        $shareProxy->deleteFile($share, $file);

        //file permission
        $file = TestResources::getInterestingName('file');
        $fileProxy = $this->createProxyWithFileSASfromArray(
            $helper,
            TestResources::getInterestingBlobOrFileSASTestCase(
                'rcwd',
                Resources::RESOURCE_TYPE_FILE,
                $share . '/' . $file
            )
        );
        //l cannot be performed
        $this->validateServiceExceptionErrorMessage(
            'The specified signed resource is not allowed for the this resource level',
            function () use ($fileProxy, $share) {
                $fileProxy->listDirectoriesAndFiles($share);
            }
        );
        $content = \openssl_random_pseudo_bytes(20);
        //rcwd can be performed.
        $options = new CreateFileFromContentOptions();
        $options->setUseTransactionalMD5(true);
        $fileProxy->createFileFromContent($share, $file, $content, $options);
        $actual = stream_get_contents($fileProxy->getFile($share, $file)->getContentStream());
        $this->assertEquals($content, $actual);
        $fileProxy->deleteFile($share, $file);
    }

    private function createProxyWithFileSASfromArray($helper, $testCase)
    {
        $sas = $helper->generateFileServiceSharedAccessSignatureToken(
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
