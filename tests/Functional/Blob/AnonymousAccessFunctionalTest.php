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

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

/**
 * Tests for account SAS proxy tests.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class AnonymousAccessFunctionalTest extends \PHPUnit\Framework\TestCase
{
    private $containerName;
    private static $blobRestProxy;
    private static $accountName;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $connectionString = TestResources::getWindowsAzureStorageServicesConnectionString();
        self::$blobRestProxy = BlobRestProxy::createBlobService($connectionString);
        self::$accountName = self::$blobRestProxy->getAccountName();
    }

    public function setUp()
    {
        parent::setUp();
        $this->containerName = TestResources::getInterestingName('con');
        self::$blobRestProxy->createContainer($this->containerName);
    }

    public function tearDown()
    {
        self::$blobRestProxy->deleteContainer($this->containerName);
        parent::tearDown();
    }

    public function testPublicAccessContainerAndBlob()
    {
        $acl = self::$blobRestProxy->getContainerAcl($this->containerName)->getContainerAcl();
        $acl->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
        self::$blobRestProxy->setContainerAcl($this->containerName, $acl);

        $pEndpoint = sprintf(
            '%s://%s.%s',
            Resources::HTTPS_SCHEME,
            self::$accountName,
            Resources::BLOB_BASE_DNS_NAME
        );

        $proxy = BlobRestProxy::createContainerAnonymousAccess(
            $pEndpoint
        );

        $result = $proxy->listBlobs($this->containerName);

        $this->assertCount(0, $result->getBlobs());

        $blob = TestResources::getInterestingName('b');
        self::$blobRestProxy->createPageBlob($this->containerName, $blob, 512);
        $result = $proxy->listBlobs($this->containerName);
        $this->assertCount(1, $result->getBlobs());
        self::$blobRestProxy->deleteBlob($this->containerName, $blob);
        $result = $proxy->listBlobs($this->containerName);
        $this->assertCount(0, $result->getBlobs());
    }

    /**
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage 404
     */
    public function testPublicAccessBlobOnly()
    {
        $acl = self::$blobRestProxy->getContainerAcl($this->containerName)->getContainerAcl();
        $acl->setPublicAccess(PublicAccessType::BLOBS_ONLY);
        self::$blobRestProxy->setContainerAcl($this->containerName, $acl);

        $pHost = self::$accountName . '.' . Resources::BLOB_BASE_DNS_NAME;
        $sHost = self::$accountName . '-secondary' . '.' . Resources::BLOB_BASE_DNS_NAME;
        $scheme = Resources::HTTPS_SCHEME;

        $pEndpoint = sprintf(
            '%s://%s.%s',
            Resources::HTTPS_SCHEME,
            self::$accountName,
            Resources::BLOB_BASE_DNS_NAME
        );

        $proxy = BlobRestProxy::createContainerAnonymousAccess(
            $pEndpoint
        );

        $result = self::$blobRestProxy->listBlobs($this->containerName);
        $this->assertCount(0, $result->getBlobs());
        $blob = TestResources::getInterestingName('b');
        self::$blobRestProxy->createBlockBlob($this->containerName, $blob, 'test content');
        $result = self::$blobRestProxy->listBlobs($this->containerName);
        $this->assertCount(1, $result->getBlobs());
        $content = stream_get_contents($proxy->getBlob($this->containerName, $blob)->getContentStream());
        $this->assertEquals('test content', $content);
        self::$blobRestProxy->deleteBlob($this->containerName, $blob);
        $result = self::$blobRestProxy->listBlobs($this->containerName);
        $this->assertCount(0, $result->getBlobs());
        //The following line will generate ServiceException with 404.
        $result = $proxy->listBlobs($this->containerName);
    }
}
