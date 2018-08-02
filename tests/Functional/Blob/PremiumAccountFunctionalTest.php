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

use Exception;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreatePageBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Blob\Models\SetBlobTierOptions;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Tests for a premium storage account, such as page blob tier.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Framework
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2018 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class PremiumAccountFunctionalTest extends \PHPUnit\Framework\TestCase
{
    /** @var BlobRestProxy $blobRestProxy */
    private static $blobRestProxy;
    private static $accountName;
    private $containerName;

    public function setUp()
    {
        parent::setUp();

        try {
            $connectionString = TestResources::getWindowsAzureStorageServicesPremiumConnectionString();
        } catch (Exception $e) {
            $this->markTestSkipped('Environment string AZURE_STORAGE_CONNECTION_STRING_PREMIUM_ACCOUNT is not provided.\
                                    Skip premium account required test cases.');
        }

        self::$blobRestProxy = BlobRestProxy::createBlobService($connectionString);
        self::$accountName = self::$blobRestProxy->getAccountName();
        $this->containerName = TestResources::getInterestingName('con');
        self::$blobRestProxy->createContainer($this->containerName);
    }

    public function tearDown()
    {
        if (self::$blobRestProxy) {
            self::$blobRestProxy->deleteContainer($this->containerName);
        }
        parent::tearDown();
    }

    public function testSetBlobTier()
    {
        $blob = TestResources::getInterestingName('b');
        self::$blobRestProxy->createPageBlob($this->containerName, $blob, 512);

        $properties = self::$blobRestProxy->getBlobProperties($this->containerName, $blob);
        $this->assertStringStartsWith('P', $properties->getProperties()->getAccessTier());
        $this->assertTrue($properties->getProperties()->getAccessTierInferred());
        $this->assertNull($properties->getProperties()->getArchiveStatus());
        $this->assertNull($properties->getProperties()->getAccessTierChangeTime());

        $options = new SetBlobTierOptions();
        $options->setAccessTier('P4');
        self::$blobRestProxy->setBlobTier($this->containerName, $blob, $options);

        $properties = self::$blobRestProxy->getBlobProperties($this->containerName, $blob);
        $this->assertEquals($options->getAccessTier(), $properties->getProperties()->getAccessTier());
        $this->assertNull($properties->getProperties()->getAccessTierInferred());
        $this->assertNull($properties->getProperties()->getArchiveStatus());
        // $this->assertNotNull($properties->getProperties()->getAccessTierChangeTime());

        $blobs = self::$blobRestProxy->listblobs($this->containerName);
        $this->assertEquals($options->getAccessTier(), $blobs->getBlobs()[0]->getProperties()->getAccessTier());
        $this->assertNull($blobs->getBlobs()[0]->getProperties()->getAccessTierInferred());
        $this->assertNull($blobs->getBlobs()[0]->getProperties()->getArchiveStatus());
        // $this->assertNotNull($blobs->getBlobs()[0]->getProperties()->getAccessTierChangeTime());
    }

    public function testCreatePageBlobWithTier()
    {
        $blob = TestResources::getInterestingName('b');
        $options = new CreatePageBlobOptions();
        $options->setAccessTier('P10');
        self::$blobRestProxy->createPageBlob($this->containerName, $blob, 512, $options);

        $properties = self::$blobRestProxy->getBlobProperties($this->containerName, $blob);
        $this->assertEquals($options->getAccessTier(), $properties->getProperties()->getAccessTier());
        $this->assertNull($properties->getProperties()->getAccessTierInferred());
        $this->assertNull($properties->getProperties()->getArchiveStatus());
        // $this->assertNotNull($properties->getProperties()->getAccessTierChangeTime());

        $options = new SetBlobTierOptions();
        $options->setAccessTier('P20');
        self::$blobRestProxy->setBlobTier($this->containerName, $blob, $options);

        $properties = self::$blobRestProxy->getBlobProperties($this->containerName, $blob);
        $this->assertEquals($options->getAccessTier(), $properties->getProperties()->getAccessTier());
        $this->assertNull($properties->getProperties()->getAccessTierInferred());
        $this->assertNull($properties->getProperties()->getArchiveStatus());
        // $this->assertNotNull($properties->getProperties()->getAccessTierChangeTime());

        $blobs = self::$blobRestProxy->listblobs($this->containerName);
        $this->assertEquals($options->getAccessTier(), $blobs->getBlobs()[0]->getProperties()->getAccessTier());
        $this->assertNull($blobs->getBlobs()[0]->getProperties()->getAccessTierInferred());
        $this->assertNull($blobs->getBlobs()[0]->getProperties()->getArchiveStatus());
        // $this->assertNotNull($blobs->getBlobs()[0]->getProperties()->getAccessTierChangeTime());
    }

    public function testCopyBlobWithTier()
    {
        $blob = TestResources::getInterestingName('b');
        $options = new CreatePageBlobOptions();
        $options->setAccessTier('P10');
        self::$blobRestProxy->createPageBlob($this->containerName, $blob, 512, $options);

        $destBlob = TestResources::getInterestingName('b2');
        $copyBlobOptions = new CopyBlobOptions();
        $copyBlobOptions->setAccessTier('P30');
        self::$blobRestProxy->copyBlob($this->containerName, $destBlob, $this->containerName, $blob, $copyBlobOptions);

        $properties = self::$blobRestProxy->getBlobProperties($this->containerName, $destBlob);
        $this->assertEquals($copyBlobOptions->getAccessTier(), $properties->getProperties()->getAccessTier());
        $this->assertNull($properties->getProperties()->getAccessTierInferred());
        $this->assertNull($properties->getProperties()->getArchiveStatus());
        // $this->assertNotNull($properties->getProperties()->getAccessTierChangeTime());
    }
}
