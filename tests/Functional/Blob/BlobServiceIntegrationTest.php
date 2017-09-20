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
 * @package   MicrosoftAzure\Storage\Tests\Functional\Blob
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\Blob;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;
use MicrosoftAzure\Storage\Blob\Models\BlobBlockType;
use MicrosoftAzure\Storage\Blob\Models\Block;
use MicrosoftAzure\Storage\Blob\Models\BlockList;
use MicrosoftAzure\Storage\Blob\Models\ContainerACL;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobSnapshotOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\ListContainersOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Internal\Utilities;

class BlobServiceIntegrationTest extends IntegrationTestBase
{
    private static $_testContainersPrefix = 'sdktest-';
    private static $_createableContainersPrefix = 'csdktest-';
    private static $_blob_for_root_container = 'sdktestroot';
    private static $_creatable_container_1;
    private static $_creatable_container_2;
    private static $_creatable_container_3;
    private static $_creatable_container_4;
    private static $_test_container_for_blobs;
    private static $_test_container_for_blobs_2;
    private static $_test_container_for_listing;
    private static $_creatableContainers;
    private static $_testContainers;

    private static $isOneTimeSetup = false;

    public function setUp()
    {
        parent::setUp();
        if (!self::$isOneTimeSetup) {
            $this->doOneTimeSetup();
            self::$isOneTimeSetup = true;
        }
    }

    private function doOneTimeSetup()
    {
        // Setup container names array (list of container names used by
        // integration tests)
        $rint = mt_rand(0, 1000000);
        self::$_testContainers = array();
        for ($i = 0; $i < 10; $i++) {
            self::$_testContainers[$i] = self::$_testContainersPrefix . ($rint + $i);
        }

        self::$_creatableContainers = array();
        for ($i = 0; $i < 10; $i++) {
            self::$_creatableContainers[$i] = self::$_createableContainersPrefix . ($rint + $i);
        }

        self::$_creatable_container_1 = self::$_creatableContainers[0];
        self::$_creatable_container_2 = self::$_creatableContainers[1];
        self::$_creatable_container_3 = self::$_creatableContainers[2];
        self::$_creatable_container_4 = self::$_creatableContainers[3];

        self::$_test_container_for_blobs = self::$_testContainers[0];
        self::$_test_container_for_blobs_2 = self::$_testContainers[1];
        self::$_test_container_for_listing = self::$_testContainers[2];

        // Create all test containers and their content
        $this->createContainers(self::$_testContainers, self::$_testContainersPrefix);
    }

    public static function tearDownAfterClass()
    {
        if (self::$isOneTimeSetup) {
            $inst = new IntegrationTestBase();
            $inst->setUp();
            $inst->deleteContainers(self::$_testContainers, self::$_testContainersPrefix);
            $inst->deleteContainers(self::$_creatableContainers, self::$_createableContainersPrefix);
            self::$isOneTimeSetup = false;
        }
        parent::tearDownAfterClass();
    }
    
    protected function tearDown()
    {
        // tearDown of parent will delete the container created in setUp
        // Do nothing here
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getServiceProperties
     */
    public function testGetServicePropertiesWorks()
    {
        // Act
        $shouldReturn = false;
        try {
            $props = $this->restProxy->getServiceProperties()->getValue();
            $this->assertTrue(!$this->isEmulated(), 'Should succeed if and only if not running in emulator');
        } catch (ServiceException $e) {
            // Expect failure in emulator, as v1.6 doesn't support this method
            if ($this->isEmulated()) {
                $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
                $shouldReturn = true;
            } else {
                throw $e;
            }
        }
        if ($shouldReturn) {
            return;
        }

        // Assert
        $this->assertNotNull($props, '$props');
        $this->assertNotNull($props->getLogging(), '$props->getLogging');
        $this->assertNotNull($props->getLogging()->getRetentionPolicy(), '$props->getLogging()->getRetentionPolicy');
        $this->assertNotNull($props->getLogging()->getVersion(), '$props->getLogging()->getVersion');
        $this->assertNotNull($props->getHourMetrics()->getRetentionPolicy(), '$props->getHourMetrics()->getRetentionPolicy');
        $this->assertNotNull($props->getHourMetrics()->getVersion(), '$props->getHourMetrics()->getVersion');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getServiceProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setServiceProperties
     */
    public function testSetServicePropertiesWorks()
    {
        // Act
        $shouldReturn = false;
        try {
            $props = $this->restProxy->getServiceProperties()->getValue();
            $this->assertTrue(!$this->isEmulated(), 'Should succeed if and only if not running in emulator');
        } catch (ServiceException $e) {
            // Expect failure in emulator, as v1.6 doesn't support this method
            if ($this->isEmulated()) {
                $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
                $shouldReturn = true;
            } else {
                throw $e;
            }
        }
        if ($shouldReturn) {
            return;
        }

        $props->getLogging()->setRead(true);
        $this->restProxy->setServiceProperties($props);

        $props = $this->restProxy->getServiceProperties()->getValue();

        // Assert
        $this->assertNotNull($props, '$props');
        $this->assertNotNull($props->getLogging(), '$props->getLogging');
        $this->assertNotNull($props->getLogging()->getRetentionPolicy(), '$props->getLogging()->getRetentionPolicy');
        $this->assertNotNull($props->getLogging()->getVersion(), '$props->getLogging()->getVersion');
        $this->assertTrue($props->getLogging()->getRead(), '$props->getLogging()->getRead');
        $this->assertNotNull($props->getHourMetrics()->getRetentionPolicy(), '$props->getHourMetrics()->getRetentionPolicy');
        $this->assertNotNull($props->getHourMetrics()->getVersion(), '$props->getHourMetrics()->getVersion');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
     */
    public function testCreateContainerWorks()
    {
        // Act
        $this->restProxy->createContainer(self::$_creatable_container_1);

        // Assert
        $opts = new ListContainersOptions();
        $opts->setPrefix(self::$_creatable_container_1);
        $results = $this->restProxy->listContainers($opts);

        $this->assertNotNull($results, '$results');
        $this->assertEquals(1, count($results->getContainers()), 'count($results->getContainers())');
        $container0 = $results->getContainers();
        $container0 = $container0[0];
        $this->assertEquals(
            self::$_creatable_container_1,
            $container0->getName(),
            '$results->getContainers()[0]->getName'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getContainerACL
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getContainerMetadata
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getContainerProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
     */
    public function testCreateContainerWithMetadataWorks()
    {
        // Act
        $opts = new CreateContainerOptions();
        $opts->setPublicAccess('blob');
        $opts->addMetadata('test', 'bar');
        $opts->addMetadata('blah', 'bleah');
        $this->restProxy->createContainer(self::$_creatable_container_2, $opts);

        $prop = $this->restProxy->getContainerMetadata(
            self::$_creatable_container_2
        );
        $prop2 = $this->restProxy->getContainerProperties(
            self::$_creatable_container_2
        );
        $acl = $this->restProxy->getContainerACL(
            self::$_creatable_container_2
        )->getContainerACL();

        $opts = new ListContainersOptions();
        $opts->setPrefix(self::$_creatable_container_2);
        $opts->setIncludeMetadata(true);
        $results2 = $this->restProxy->listContainers($opts);

        $this->restProxy->deleteContainer(self::$_creatable_container_2);

        // Assert
        $this->assertNotNull($prop, '$prop');
        $this->assertNotNull($prop->getETag(), '$prop->getETag()');
        $this->assertNotNull($prop->getLastModified(), '$prop->getLastModified()');
        $this->assertNotNull($prop->getMetadata(), '$prop->getMetadata()');
        $this->assertEquals(
            2,
            count($prop->getMetadata()),
            'count($prop->getMetadata())'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive(
                'test',
                $prop->getMetadata()
            ),
            'Utilities::arrayKeyExistsInsensitive(\'test\', $prop->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bar', $prop->getMetadata()) === false),
            '!(array_search(\'bar\', $prop->getMetadata()) === FALSE)'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive('blah', $prop->getMetadata()),
            'Utilities::arrayKeyExistsInsensitive(\'blah\', $prop->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bleah', $prop->getMetadata()) === false),
            '!(array_search(\'bleah\', $prop->getMetadata()) === FALSE)'
        );

        $this->assertNotNull($prop2, '$prop2');
        $this->assertNotNull($prop2->getETag(), '$prop2->getETag()');
        $this->assertNotNull(
            $prop2->getLastModified(),
            '$prop2->getLastModified()'
        );
        $this->assertNotNull($prop2->getMetadata(), '$prop2->getMetadata()');
        $this->assertEquals(
            2,
            count($prop2->getMetadata()),
            'count($prop2->getMetadata())'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive(
                'test',
                $prop2->getMetadata()
            ),
            'Utilities::arrayKeyExistsInsensitive(\'test\',
            $prop2->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bar', $prop2->getMetadata()) === false),
            '!(array_search(\'bar\', $prop2->getMetadata()) === FALSE)'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive('blah', $prop2->getMetadata()),
            'Utilities::arrayKeyExistsInsensitive(\'blah\', $prop2->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bleah', $prop2->getMetadata()) === false),
            '!(array_search(\'bleah\', $prop2->getMetadata()) === FALSE)'
        );

        $this->assertNotNull($results2, '$results2');
        $this->assertEquals(
            1,
            count($results2->getContainers()),
            'count($results2->getContainers())'
        );
        $container0 = $results2->getContainers();
        $container0 = $container0[0];
        // The capitalizaion gets changed.
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive(
                'test',
                $container0->getMetadata()
            ),
            'Utilities::arrayKeyExistsInsensitive(\'test\', $container0->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bar', $container0->getMetadata()) === false),
            '!(array_search(\'bar\', $container0->getMetadata()) === FALSE)'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive(
                'blah',
                $container0->getMetadata()
            ),
            'Utilities::arrayKeyExistsInsensitive(\'blah\', $container0->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bleah', $container0->getMetadata()) === false),
            '!(array_search(\'bleah\', $container0->getMetadata()) === FALSE)'
        );

        $this->assertNotNull($acl, '$acl');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getContainerMetadata
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setContainerMetadata
     */
    public function testSetContainerMetadataWorks()
    {
        // Act
        $this->restProxy->createContainer(self::$_creatable_container_3);

        $metadata = array(
            'test' => 'bar',
            'blah' => 'bleah');
        $this->restProxy->setContainerMetadata(self::$_creatable_container_3, $metadata);
        $prop = $this->restProxy->getContainerMetadata(self::$_creatable_container_3);

        // Assert
        $this->assertNotNull($prop, '$prop');
        $this->assertNotNull($prop->getETag(), '$prop->getETag()');
        $this->assertNotNull($prop->getLastModified(), '$prop->getLastModified()');
        $this->assertNotNull($prop->getMetadata(), '$prop->getMetadata()');
        $this->assertEquals(2, count($prop->getMetadata()), 'count($prop->getMetadata())');
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive(
                'test',
                $prop->getMetadata()
            ),
            'Utilities::arrayKeyExistsInsensitive(\'test\', $prop->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bar', $prop->getMetadata()) === false),
            '!(array_search(\'bar\', $prop->getMetadata()) === FALSE)'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive('blah', $prop->getMetadata()),
            'Utilities::arrayKeyExistsInsensitive(\'blah\', $prop->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bleah', $prop->getMetadata()) === false),
            '!(array_search(\'bleah\', $prop->getMetadata()) === FALSE)'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getContainerACL
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setContainerACL
     */
    public function testSetContainerACLWorks()
    {
        // Arrange
        $container = self::$_creatable_container_4;

        $expiryStartDate = new \DateTime;
        $expiryStartDate->setDate(2010, 1, 1);
        $expiryEndDate = new \DateTime;
        $expiryEndDate->setDate(2020, 1, 1);

        // Act
        $this->restProxy->createContainer($container);
        $acl = new ContainerACL();
        $acl->setPublicAccess(PublicAccessType::BLOBS_ONLY);

        $acl->addSignedIdentifier('test', $expiryStartDate, $expiryEndDate, 'rwd');
        $this->restProxy->setContainerACL($container, $acl);

        $res = $this->restProxy->getContainerACL($container);
        $acl2 = $res->getContainerACL();
        $this->restProxy->deleteContainer($container);

        // Assert
        $this->assertNotNull($acl2, '$acl2');
        $this->assertNotNull($res->getETag(), '$res->getETag()');
        $this->assertNotNull($res->getLastModified(), '$res->getLastModified()');
        $this->assertNotNull($acl2->getPublicAccess(), '$acl2->getPublicAccess()');
        $this->assertEquals(
            PublicAccessType::BLOBS_ONLY,
            $acl2->getPublicAccess(),
            '$acl2->getPublicAccess()'
        );
        $this->assertEquals(1, count($acl2->getSignedIdentifiers()), 'count($acl2->getSignedIdentifiers())');
        $signedids = $acl2->getSignedIdentifiers();
        $this->assertEquals('test', $signedids[0]->getId(), '$signedids[0]->getId()');
        $expiryStartDate = $expiryStartDate->setTimezone(new \DateTimeZone('UTC'));
        $expiryEndDate = $expiryEndDate->setTimezone(new \DateTimeZone('UTC'));
        $this->assertEquals(
            Utilities::convertToDateTime($expiryStartDate),
            Utilities::convertToDateTime(
                $signedids[0]->getAccessPolicy()->getStart()
            ),
            '$signedids[0]->getAccessPolicy()->getStart()'
        );
        $this->assertEquals(
            Utilities::convertToDateTime($expiryEndDate),
            Utilities::convertToDateTime(
                $signedids[0]->getAccessPolicy()->getExpiry()
            ),
            '$signedids[0]->getAccessPolicy()->getExpiry()'
        );
        $this->assertEquals(
            'rwd',
            $signedids[0]->getAccessPolicy()->getPermission(),
            '$signedids[0]->getAccessPolicy()->getPermission()'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
     */
    public function testListContainersWorks()
    {
        // Act
        $results = $this->restProxy->listContainers();

        // Assert
        $this->assertNotNull($results, '$results');
        $this->assertTrue(
            count(self::$_testContainers) <= count($results->getContainers()),
            'count(self::$_testContainers) <= count($results->getContainers())'
        );
        $container0 = $results->getContainers();
        $container0 = $container0[0];
        $this->assertNotNull($container0->getName(), '$container0->getName()');
        $this->assertNotNull($container0->getMetadata(), '$container0->getMetadata()');
        $this->assertNotNull($container0->getProperties(), '$container0->getProperties()');
        $this->assertNotNull($container0->getProperties()->getETag(), '$container0->getProperties()->getETag()');
        $this->assertNotNull(
            $container0->getProperties()->getLastModified(),
            '$container0->getProperties()->getLastModified()'
        );
        $this->assertNotNull($container0->getUrl(), '$container0->getUrl()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
     */
    public function testListContainersWithPaginationWorks()
    {
        // Act
        $opts = new ListContainersOptions();
        $opts->setMaxResults(3);
        $results = $this->restProxy->listContainers($opts);
        $opts2 = new ListContainersOptions();
        $opts2->setMarker($results ->getNextMarker());
        $results2 = $this->restProxy->listContainers($opts2);

        // Assert
        $this->assertNotNull($results, '$results');
        $this->assertEquals(3, count($results->getContainers()), 'count($results->getContainers())');
        $this->assertNotNull($results->getNextMarker(), '$results->getNextMarker()');
        $this->assertEquals(3, $results->getMaxResults(), '$results->getMaxResults()');

        $this->assertNotNull($results2, '$results2');
        $this->assertTrue(
            count(self::$_testContainers) - 3 <= count($results2->getContainers()),
            'count(self::$_testContainers) - 3 <= count($results2->getContainers())'
        );
        $this->assertEquals('', $results2->getNextMarker(), '$results2->getNextMarker()');
        $this->assertEquals(0, $results2->getMaxResults(), '$results2->getMaxResults()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
     */
    public function testListContainersWithPrefixWorks()
    {
        // Act
        $opts = new ListContainersOptions();
        $opts->setPrefix(self::$_testContainersPrefix);
        $opts->setMaxResults(3);
        $results = $this->restProxy->listContainers($opts);
        // Assert
        $this->assertNotNull($results, '$results');
        $this->assertEquals(3, count($results->getContainers()), 'count($results->getContainers())');
        $this->assertNotNull($results->getNextMarker(), '$results->getNextMarker()');
        $this->assertEquals(3, $results->getMaxResults(), '$results->getMaxResults()');

        // Act
        $opts = new ListContainersOptions();
        $opts->setPrefix(self::$_testContainersPrefix);
        $opts->setMarker($results->getNextMarker());
        $results2 = $this->restProxy->listContainers($opts);

        // Assert
        $this->assertNotNull($results2, '$results2');
        $this->assertNull($results2->getNextMarker(), '$results2->getNextMarker()');
        $this->assertEquals(0, $results2->getMaxResults(), '$results2->getMaxResults()');

        // Act
        $opts = new ListContainersOptions();
        $opts->setPrefix(self::$_testContainersPrefix);
        $results3 = $this->restProxy->listContainers($opts);

        // Assert
        $this->assertEquals(
            count($results->getContainers()) + count($results2->getContainers()),
            count($results3->getContainers()),
            'count($results3->getContainers())'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobMetadata
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
     */
    public function testWorkingWithRootContainersWorks()
    {
        // Ensure root container exists
        $this->createContainerWithRetry('$root', new CreateContainerOptions());

        // Work with root container explicitly ('$root')
        {
            // Act
            $this->restProxy->createPageBlob('$root', self::$_blob_for_root_container, 512);
            $list = $this->restProxy->listBlobs('$root');
            $properties = $this->restProxy->getBlobProperties('$root', self::$_blob_for_root_container);
            $metadata = $this->restProxy->getBlobMetadata('$root', self::$_blob_for_root_container);

            // Assert
            $this->assertNotNull($list, '$list');
            $this->assertTrue(1 <= count($list->getBlobs()), '1 <= count($list->getBlobs())');
            $this->assertNotNull($properties, '$properties');
            $this->assertNotNull($metadata, '$metadata');

            // Act
            $this->restProxy->deleteBlob('$root', self::$_blob_for_root_container);
        }

        // Work with root container implicitly ('')
        {
            // Act
            $this->restProxy->createPageBlob('', self::$_blob_for_root_container, 512);
            // '$root' must be explicit when listing blobs in the root container
            $list = $this->restProxy->listBlobs('$root');
            $properties = $this->restProxy->getBlobProperties('', self::$_blob_for_root_container);
            $metadata = $this->restProxy->getBlobMetadata('', self::$_blob_for_root_container);

            // Assert
            $this->assertNotNull($list, '$list');
            $this->assertTrue(1 <= count($list->getBlobs()), '1 <= count($list->getBlobs())');
            $this->assertNotNull($properties, '$properties');
            $this->assertNotNull($metadata, '$metadata');

            // Act
            $this->restProxy->deleteBlob('', self::$_blob_for_root_container);
        }

        // Cleanup.
        $this->restProxy->deleteContainer('$root');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
     */
    public function testListBlobsWorks()
    {
        // Arrange
        $blobNames = array( 'myblob1', 'myblob2', 'other-blob1', 'other-blob2' );
        foreach ($blobNames as $blob) {
            $this->restProxy->createPageBlob(self::$_test_container_for_listing, $blob, 512);
        }

        // Act
        $results = $this->restProxy->listBlobs(self::$_test_container_for_listing);

        foreach ($blobNames as $blob) {
            $this->restProxy->deleteBlob(self::$_test_container_for_listing, $blob);
        }

        // Assert
        $this->assertNotNull($results, '$results');
        $this->assertEquals(4, count($results->getBlobs()), 'count($results->getBlobs())');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
     */
    public function testListBlobsWithPrefixWorks()
    {
        // Arrange
        $blobNames = array( 'myblob1', 'myblob2', 'otherblob1', 'otherblob2' );
        foreach ($blobNames as $blob) {
            $this->restProxy->createPageBlob(self::$_test_container_for_listing, $blob, 512);
        }

        // Act
        $opts = new ListBlobsOptions();
        $opts->setPrefix('myblob');
        $results = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);
        $opts = new ListBlobsOptions();
        $opts->setPrefix('o');
        $results2 = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);

        foreach ($blobNames as $blob) {
            $this->restProxy->deleteBlob(self::$_test_container_for_listing, $blob);
        }

        // Assert
        $this->assertNotNull($results, '$results');
        $this->assertEquals(2, count($results->getBlobs()), 'count($results->getBlobs())');
        $blobs = $results->getBlobs();
        $this->assertEquals('myblob1', $blobs[0]->getName(), '$blobs[0]->getName()');
        $this->assertEquals('myblob2', $blobs[1]->getName(), '$blobs[1]->getName()');

        $this->assertNotNull($results2, '$results2');
        $this->assertEquals(2, count($results2->getBlobs()), 'count($results2->getBlobs())');
        $blobs = $results2->getBlobs();
        $this->assertEquals('otherblob1', $blobs[0]->getName(), '$blobs[0]->getName()');
        $this->assertEquals('otherblob2', $blobs[1]->getName(), '$blobs[1]->getName()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
     */
    public function testListBlobsWithOptionsWorks()
    {
        // Arrange
        $blobNames = array( 'myblob1', 'myblob2', 'otherblob1', 'otherblob2' );
        foreach ($blobNames as $blob) {
            $this->restProxy->createPageBlob(self::$_test_container_for_listing, $blob, 512);
        }

        // Act
        $opts = new ListBlobsOptions();
        $opts->setIncludeMetadata(true);
        $opts->setIncludeSnapshots(true);
        $results = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);

        foreach ($blobNames as $blob) {
            $this->restProxy->deleteBlob(self::$_test_container_for_listing, $blob);
        }

        // Assert
        $this->assertNotNull($results, '$results');
        $this->assertEquals(4, count($results->getBlobs()), 'count($results->getBlobs())');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
     */
    public function testListBlobsWithDelimiterWorks()
    {
        // Arrange
        $blobNames = array( 'myblob1', 'myblob2', 'dir1-blob1', 'dir1-blob2', 'dir2-dir21-blob3', 'dir2-dir22-blob3' );
        foreach ($blobNames as $blob) {
            $this->restProxy->createPageBlob(self::$_test_container_for_listing, $blob, 512);
        }

        // Act
        $opts = new ListBlobsOptions();
        $opts->setDelimiter('-');
        $results = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);
        $opts->setPrefix('dir1-');
        $results2 = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);
        $opts->setPrefix('dir2-');
        $results3 = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);
        $opts->setPrefix('dir2-dir21-');
        $results4 = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);
        $opts->setPrefix('dir2-dir22-');
        $results5 = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);
        $opts->setPrefix('dir2-dir44-');
        $results6 = $this->restProxy->listBlobs(self::$_test_container_for_listing, $opts);

        foreach ($blobNames as $blob) {
            $this->restProxy->deleteBlob(self::$_test_container_for_listing, $blob);
        }

        // Assert
        $this->assertNotNull($results, '$results');
        $this->assertEquals(2, count($results->getBlobs()), 'count($results->getBlobs())');
        $this->assertEquals(2, count($results->getBlobPrefixes()), 'count($results->getBlobPrefixes())');

        $this->assertEquals(2, count($results2->getBlobs()), 'count($results2->getBlobs())');
        $this->assertEquals(0, count($results2->getBlobPrefixes()), 'count($results2->getBlobPrefixes())');

        $this->assertEquals(0, count($results3->getBlobs()), 'count($results3->getBlobs())');
        $this->assertEquals(2, count($results3->getBlobPrefixes()), 'count($results3->getBlobPrefixes())');

        $this->assertEquals(1, count($results4->getBlobs()), 'count($results4->getBlobs())');
        $this->assertEquals(0, count($results4->getBlobPrefixes()), 'count($results4->getBlobPrefixes())');

        $this->assertEquals(1, count($results5->getBlobs()), 'count($results5->getBlobs())');
        $this->assertEquals(0, count($results5->getBlobPrefixes()), 'count($results5->getBlobPrefixes())');

        $this->assertEquals(0, count($results6->getBlobs()), 'count($results6->getBlobs())');
        $this->assertEquals(0, count($results6->getBlobPrefixes()), 'count($results6->getBlobPrefixes())');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     */
    public function testCreatePageBlobWorks()
    {
        // Act
        $this->restProxy->createPageBlob(self::$_test_container_for_blobs, 'test', 512);

        // Assert
        $this->assertTrue(true, 'success');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     */
    public function testCreatePageBlobWithOptionsWorks()
    {
        // Act
        $opts = new CreateBlobOptions();
        $opts->setContentLanguage('en-us');
        // opts->setContentMD5('1234');
        $opts->setContentType('text/plain');
        $opts->setCacheControl('test');
        $opts->setContentDisposition('test');
        $opts->setContentEncoding('UTF-8');
        $this->restProxy->createPageBlob(self::$_test_container_for_blobs, 'test', 512, $opts);

        $result = $this->restProxy->getBlobProperties(self::$_test_container_for_blobs, 'test');

        // Assert
        $this->assertNotNull($result, '$result');

        $this->assertNotNull($result->getMetadata(), '$result->getMetadata()');
        $this->assertEquals(0, count($result->getMetadata()), 'count($result->getMetadata())');

        $props = $result->getProperties();
        $this->assertNotNull($props, '$props');
        $this->assertEquals('test', $props->getCacheControl(), '$props->getCacheControl()');
        $this->assertEquals('test', $props->getContentDisposition(), '$props->getContentDisposition()');
        $this->assertEquals('UTF-8', $props->getContentEncoding(), '$props->getContentEncoding()');
        $this->assertEquals('en-us', $props->getContentLanguage(), '$props->getContentLanguage()');
        $this->assertEquals('text/plain', $props->getContentType(), '$props->getContentType()');
        $this->assertEquals(512, $props->getContentLength(), '$props->getContentLength()');
        $this->assertNotNull($props->getETag(), '$props->getETag()');
        $this->assertNull($props->getContentMD5(), '$props->getContentMD5()');
        $this->assertNotNull($props->getLastModified(), '$props->getLastModified()');
        $this->assertEquals('PageBlob', $props->getBlobType(), '$props->getBlobType()');
        $this->assertEquals('unlocked', $props->getLeaseStatus(), '$props->getLeaseStatus()');
        $this->assertEquals(0, $props->getSequenceNumber(), '$props->getSequenceNumber()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::clearBlobPages
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     */
    public function testClearBlobPagesWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test';
        $this->restProxy->createPageBlob($container, $blob, 512);

        $result = $this->restProxy->clearBlobPages($container, $blob, new Range(0, 511));

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNull($result->getContentMD5(), '$result->getContentMD5()');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertEquals(0, $result->getSequenceNumber(), '$result->getSequenceNumber()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobPages
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     */
    public function testCreateBlobPagesWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test';
        $content = str_pad('', 512);
        $this->restProxy->createPageBlob($container, $blob, 512);

        $result = $this->restProxy->createBlobPages($container, $blob, new Range(0, 511), $content);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNotNull($result->getContentMD5(), '$result->getContentMD5()');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertEquals(0, $result->getSequenceNumber(), '$result->getSequenceNumber()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobPages
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listPageBlobRanges
     */
    public function testListBlobRegionsWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test';
        $content = str_pad('', 512);
        $this->restProxy->createPageBlob($container, $blob, 16384 + 512);

        $this->restProxy->createBlobPages($container, $blob, new Range(0, 511), $content);
        $this->restProxy->createBlobPages($container, $blob, new Range(1024, 1024 + 511), $content);
        $this->restProxy->createBlobPages($container, $blob, new Range(8192, 8192 + 511), $content);
        $this->restProxy->createBlobPages($container, $blob, new Range(16384, 16384 + 511), $content);

//        $result = $this->restProxy->listBlobRegions($container, $blob);
        $result = $this->restProxy->listPageBlobRanges($container, $blob);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertEquals(16384 + 512, $result->getContentLength(), '$result->getContentLength()');
        $this->assertNotNull($result->getRanges(), '$result->getRanges()');
        $this->assertEquals(4, count($result->getRanges()), 'count($result->getRanges())');
        $ranges = $result->getRanges();
        $this->assertEquals(0, $ranges[0]->getStart(), '$ranges[0]->getStart()');
        $this->assertEquals(511, $ranges[0]->getEnd(), '$ranges[0]->getEnd()');
        $this->assertEquals(1024, $ranges[1]->getStart(), '$ranges[1]->getStart()');
        $this->assertEquals(1024 + 511, $ranges[1]->getEnd(), '$ranges[1]->getEnd()');
        $this->assertEquals(8192, $ranges[2]->getStart(), '$ranges[2]->getStart()');
        $this->assertEquals(8192 + 511, $ranges[2]->getEnd(), '$ranges[2]->getEnd()');
        $this->assertEquals(16384, $ranges[3]->getStart(), '$ranges[3]->getStart()');
        $this->assertEquals(16384 + 511, $ranges[3]->getEnd(), '$ranges[3]->getEnd()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobBlocks
     */
    public function testListBlobBlocksOnEmptyBlobWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test13';
        $content = str_pad('', 512);
        $this->restProxy->createBlockBlob($container, $blob, $content);

        $result = $this->restProxy->listBlobBlocks($container, $blob);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertEquals(512, $result->getContentLength(), '$result->getContentLength()');
        $this->assertNotNull($result->getCommittedBlocks(), '$result->getCommittedBlocks()');
        $this->assertEquals(0, count($result->getCommittedBlocks()), 'count($result->getCommittedBlocks())');
        $this->assertNotNull($result->getUncommittedBlocks(), '$result->getUncommittedBlocks()');
        $this->assertEquals(0, count($result->getUncommittedBlocks()), 'count($result->getUncommittedBlocks())');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobBlock
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobBlocks
     */
    public function testListBlobBlocksWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test14';
        $this->restProxy->createBlockBlob($container, $blob, '');
        $this->restProxy->createBlobBlock($container, $blob, base64_encode('123'), str_pad('', 256));
        $this->restProxy->createBlobBlock($container, $blob, base64_encode('124'), str_pad('', 512));
        $this->restProxy->createBlobBlock($container, $blob, base64_encode('125'), str_pad('', 195));

        $opts = new ListBlobBlocksOptions();
        $opts->setIncludeCommittedBlobs(true);
        $opts->setIncludeUncommittedBlobs(true);
        $result = $this->restProxy->listBlobBlocks($container, $blob, $opts);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertEquals(0, $result->getContentLength(), '$result->getContentLength()');
        $this->assertNotNull($result->getCommittedBlocks(), '$result->getCommittedBlocks()');
        $this->assertEquals(0, count($result->getCommittedBlocks()), 'count($result->getCommittedBlocks())');
        $this->assertNotNull($result->getUncommittedBlocks(), '$result->getUncommittedBlocks()');
        $this->assertEquals(3, count($result->getUncommittedBlocks()), 'count($result->getUncommittedBlocks())');
        $uncom = $result->getUncommittedBlocks();
        $keys = array_keys($uncom);
        $this->assertEquals(base64_encode('123'), $keys[0], '$keys[0]');
        $this->assertEquals(256, $uncom[$keys[0]], '$uncom[$keys[0]]');
        $this->assertEquals(base64_encode('124'), $keys[1], '$keys[1]');
        $this->assertEquals(512, $uncom[$keys[1]], '$uncom[$keys[1]]');
        $this->assertEquals(base64_encode('125'), $keys[2], '$keys[2]');
        $this->assertEquals(195, $uncom[$keys[2]], '$uncom[$keys[2]]');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::commitBlobBlocks
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobBlock
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobBlocks
     */
    public function testListBlobBlocksWithOptionsWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test14';
        $this->restProxy->createBlockBlob($container, $blob, '');
        $this->restProxy->createBlobBlock($container, $blob, base64_encode('123'), str_pad('', 256));

        $blockList = new BlockList();
        $blockList->addUncommittedEntry(base64_encode('123'));
        $this->restProxy->commitBlobBlocks($container, $blob, $blockList);

        $this->restProxy->createBlobBlock($container, $blob, base64_encode('124'), str_pad('', 512));
        $this->restProxy->createBlobBlock($container, $blob, base64_encode('125'), str_pad('', 195));

        $opts = new ListBlobBlocksOptions();
        $opts->setIncludeCommittedBlobs(true);
        $opts->setIncludeUncommittedBlobs(true);
        $result1 = $this->restProxy->listBlobBlocks($container, $blob, $opts);
        $opts = new ListBlobBlocksOptions();
        $opts->setIncludeCommittedBlobs(true);
        $result2 = $this->restProxy->listBlobBlocks($container, $blob, $opts);
        $opts = new ListBlobBlocksOptions();
        $opts->setIncludeUncommittedBlobs(true);
        $result3 = $this->restProxy->listBlobBlocks($container, $blob, $opts);

        // Assert
        $this->assertEquals(1, count($result1->getCommittedBlocks()), 'count($result1->getCommittedBlocks())');
        $this->assertEquals(2, count($result1->getUncommittedBlocks()), 'count($result1->getUncommittedBlocks())');

        $this->assertEquals(1, count($result2->getCommittedBlocks()), 'count($result2->getCommittedBlocks())');
        $this->assertEquals(0, count($result2->getUncommittedBlocks()), 'count($result2->getUncommittedBlocks())');

        $this->assertEquals(0, count($result3->getCommittedBlocks()), 'count($result3->getCommittedBlocks())');
        $this->assertEquals(2, count($result3->getUncommittedBlocks()), 'count($result3->getUncommittedBlocks())');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::commitBlobBlocks
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobBlock
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobBlocks
     */
    public function testCommitBlobBlocksWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test14';
        $blockId1 = base64_encode('1fedcba');
        $blockId2 = base64_encode('2abcdef');
        $blockId3 = base64_encode('3zzzzzz');
        $this->restProxy->createBlockBlob($container, $blob, '');
        $this->restProxy->createBlobBlock($container, $blob, $blockId1, str_pad('', 256));
        $this->restProxy->createBlobBlock($container, $blob, $blockId2, str_pad('', 512));
        $this->restProxy->createBlobBlock($container, $blob, $blockId3, str_pad('', 195));

        $blockList = new BlockList();
        $blockList->addUncommittedEntry($blockId1);
        $blockList->addLatestEntry($blockId3);

        $this->restProxy->commitBlobBlocks($container, $blob, $blockList);

        $opts = new ListBlobBlocksOptions();
        $opts->setIncludeCommittedBlobs(true);
        $opts->setIncludeUncommittedBlobs(true);
        $result = $this->restProxy->listBlobBlocks($container, $blob, $opts);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertEquals(256 + 195, $result->getContentLength(), '$result->getContentLength()');

        $this->assertNotNull($result->getCommittedBlocks(), '$result->getCommittedBlocks()');
        $this->assertEquals(2, count($result->getCommittedBlocks()), 'count($result->getCommittedBlocks())');
        $comblk = $result->getCommittedBlocks();
        $keys = array_keys($comblk);
        $this->assertEquals($blockId1, $keys[0], '$keys[0]');
        $this->assertEquals(256, $comblk[$keys[0]], '$comblk[$keys[0]]');
        $this->assertEquals($blockId3, $keys[1], '$keys[1]');
        $this->assertEquals(195, $comblk[$keys[1]], '$comblk[$keys[1]]');

        $this->assertNotNull($result->getUncommittedBlocks(), '$result->getUncommittedBlocks()');
        $this->assertEquals(0, count($result->getUncommittedBlocks()), 'count($result->getUncommittedBlocks())');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::commitBlobBlocks
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobBlock
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobBlocks
     */
    public function testCommitBlobBlocksWithArrayWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test14a';
        $blockId1 = base64_encode('1fedcba');
        $blockId2 = base64_encode('2abcdef');
        $blockId3 = base64_encode('3zzzzzz');
        $this->restProxy->createBlockBlob($container, $blob, '');
        $this->restProxy->createBlobBlock($container, $blob, $blockId1, str_pad('', 256));
        $this->restProxy->createBlobBlock($container, $blob, $blockId2, str_pad('', 512));
        $this->restProxy->createBlobBlock($container, $blob, $blockId3, str_pad('', 195));

        $block1 = new Block();
        $block1->setBlockId($blockId1);
        $block1->setType(BlobBlockType::UNCOMMITTED_TYPE);
        $block3 = new Block();
        $block3->setBlockId($blockId3);
        $block3->setType(BlobBlockType::LATEST_TYPE);
        $blockList = array($block1, $block3);

        $this->restProxy->commitBlobBlocks($container, $blob, $blockList);

        $opts = new ListBlobBlocksOptions();
        $opts->setIncludeCommittedBlobs(true);
        $opts->setIncludeUncommittedBlobs(true);
        $result = $this->restProxy->listBlobBlocks($container, $blob, $opts);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertEquals(256 + 195, $result->getContentLength(), '$result->getContentLength()');

        $this->assertNotNull($result->getCommittedBlocks(), '$result->getCommittedBlocks()');
        $this->assertEquals(2, count($result->getCommittedBlocks()), 'count($result->getCommittedBlocks())');
        $comblk = $result->getCommittedBlocks();
        $keys = array_keys($comblk);
        $this->assertEquals($blockId1, $keys[0], '$keys[0]');
        $this->assertEquals(256, $comblk[$keys[0]], '$comblk[$keys[0]]');
        $this->assertEquals($blockId3, $keys[1], '$keys[1]');
        $this->assertEquals(195, $comblk[$keys[1]], '$comblk[$keys[1]]');

        $this->assertNotNull($result->getUncommittedBlocks(), '$result->getUncommittedBlocks()');
        $this->assertEquals(0, count($result->getUncommittedBlocks()), 'count($result->getUncommittedBlocks())');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobBlock
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testCreateBlobBlockWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test13';
        $content = str_pad('', 512);
        $this->restProxy->createBlockBlob($container, $blob, $content);
        $this->restProxy->createBlobBlock($container, $blob, base64_encode('123'), $content);
        $this->restProxy->createBlobBlock($container, $blob, base64_encode('124'), $content);

        // Assert
        $this->assertTrue(true, 'success');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testCreateBlockBlobWorks()
    {
        // Act
        $this->restProxy->createBlockBlob(self::$_test_container_for_blobs, 'test2', 'some content');

        // Assert
        $this->assertTrue(true, 'success');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     */
    public function testCreateBlockBlobWithOptionsWorks()
    {
        // Act
        $content = 'some $content';
        $opts = new CreateBlobOptions();
        $opts->setContentEncoding('UTF-8');
        $opts->setContentLanguage('en-us');
        // $opts->setContentMD5('1234');
        $opts->setContentType('text/plain');
        $opts->setCacheControl('test');
        $opts->setContentDisposition('test');
        $this->restProxy->createBlockBlob(self::$_test_container_for_blobs, 'test2', $content, $opts);

        $result = $this->restProxy->getBlobProperties(self::$_test_container_for_blobs, 'test2');

        // Assert
        $expectedMD5 = base64_encode(md5($content, true));
        
        $this->assertNotNull($result, '$result');

        $this->assertNotNull($result->getMetadata(), '$result->getMetadata()');
        $this->assertEquals(0, count($result->getMetadata()), 'count($result->getMetadata())');

        $props = $result->getProperties();
        $this->assertNotNull($props, '$props');
        $this->assertEquals('test', $props->getCacheControl(), '$props->getCacheControl()');
        $this->assertEquals('test', $props->getContentDisposition(), '$props->getContentDisposition()');
        $this->assertEquals('UTF-8', $props->getContentEncoding(), '$props->getContentEncoding()');
        $this->assertEquals('en-us', $props->getContentLanguage(), '$props->getContentLanguage()');
        $this->assertEquals('text/plain', $props->getContentType(), '$props->getContentType()');
        $this->assertEquals(strlen($content), $props->getContentLength(), '$props->getContentLength()');
        $this->assertNotNull($props->getETag(), '$props->getETag()');
        $this->assertEquals($expectedMD5, $props->getContentMD5(), '$props->getContentMD5()');
        $this->assertNotNull($props->getLastModified(), '$props->getLastModified()');
        $this->assertEquals('BlockBlob', $props->getBlobType(), '$props->getBlobType()');
        $this->assertEquals('unlocked', $props->getLeaseStatus(), '$props->getLeaseStatus()');
        $this->assertEquals(0, $props->getSequenceNumber(), '$props->getSequenceNumber()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobSnapshot
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testCreateBlobSnapshotWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test3';
        $this->restProxy->createBlockBlob($container, $blob, 'some content');
        $snapshot = $this->restProxy->createBlobSnapshot($container, $blob);

        // Assert
        $this->assertNotNull($snapshot, '$snapshot');
        $this->assertNotNull($snapshot->getETag(), '$snapshot->getETag()');
        $this->assertNotNull($snapshot->getLastModified(), '$snapshot->getLastModified()');
        $this->assertNotNull($snapshot->getSnapshot(), '$snapshot->getSnapshot()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobSnapshot
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     */
    public function testCreateBlobSnapshotWithOptionsWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test3';
        $this->restProxy->createBlockBlob($container, $blob, 'some content');
        $opts = new CreateBlobSnapshotOptions();
        $metadata = array(
            'test' => 'bar',
            'blah' => 'bleah');
        $opts->setMetadata($metadata);
        $snapshot = $this->restProxy->createBlobSnapshot($container, $blob, $opts);

        $opts = new GetBlobPropertiesOptions();
        $opts->setSnapshot($snapshot->getSnapshot());
        $result = $this->restProxy->getBlobProperties($container, $blob, $opts);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertEquals(
            $snapshot->getETag(),
            $result->getProperties()->getETag(),
            '$result->getProperties()->getETag()'
        );
        $this->assertEquals(
            $snapshot->getLastModified(),
            $result->getProperties()->getLastModified(),
            '$result->getProperties()->getLastModified()'
        );
        // The capitalizaion gets changed.
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive('test', $result->getMetadata()),
            'Utilities::arrayKeyExistsInsensitive(\'test\', $result->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bar', $result->getMetadata()) === false),
            '!(array_search(\'bar\', $result->getMetadata()) === FALSE)'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive(
                'blah',
                $result->getMetadata()
            ),
            'Utilities::arrayKeyExistsInsensitive(\'blah\', $result->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bleah', $result->getMetadata()) === false),
            '!(array_search(\'bleah\', $result->getMetadata()) === FALSE)'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     */
    public function testGetBlockBlobWorks()
    {
        // Act
        $content = 'some $content';
        $opts = new CreateBlobOptions();
        $opts->setCacheControl('test');
        $opts->setContentDisposition('test');
        $opts->setContentEncoding('UTF-8');
        $opts->setContentLanguage('en-us');
        // $opts->setContentMD5('1234');
        $opts->setContentType('text/plain');
        $this->restProxy->createBlockBlob(self::$_test_container_for_blobs, 'test2', $content, $opts);

        $result = $this->restProxy->getBlob(self::$_test_container_for_blobs, 'test2');

        // Assert
        $expectedMD5 = base64_encode(md5($content, true));
        
        $this->assertNotNull($result, '$result');

        $this->assertNotNull($result->getMetadata(), '$result->getMetadata()');
        $this->assertEquals(0, count($result->getMetadata()), 'count($result->getMetadata())');

        $props = $result->getProperties();
        $this->assertNotNull($props, '$props');
        $this->assertEquals('test', $props->getCacheControl(), '$props->getCacheControl()');
        $this->assertEquals('test', $props->getContentDisposition(), '$props->getContentDisposition()');
        $this->assertEquals('UTF-8', $props->getContentEncoding(), '$props->getContentEncoding()');
        $this->assertEquals('en-us', $props->getContentLanguage(), '$props->getContentLanguage()');
        $this->assertEquals('text/plain', $props->getContentType(), '$props->getContentType()');
        $this->assertEquals(strlen($content), $props->getContentLength(), '$props->getContentLength()');
        $this->assertNotNull($props->getETag(), '$props->getETag()');
        $this->assertEquals($expectedMD5, $props->getContentMD5(), '$props->getContentMD5()');
        $this->assertNotNull($props->getLastModified(), '$props->getLastModified()');
        $this->assertEquals('BlockBlob', $props->getBlobType(), '$props->getBlobType()');
        $this->assertEquals('unlocked', $props->getLeaseStatus(), '$props->getLeaseStatus()');
        $this->assertEquals(0, $props->getSequenceNumber(), '$props->getSequenceNumber()');
        $this->assertEquals($content, stream_get_contents($result->getContentStream()), '$result->getContentStream()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     */
    public function testGetPageBlobWorks()
    {
        // Act
        $opts = new CreateBlobOptions();
        $opts->setCacheControl('test');
        $opts->setContentDisposition('test');
        $opts->setContentEncoding('UTF-8');
        $opts->setContentLanguage('en-us');
        // $opts->setContentMD5('1234');
        $opts->setContentType('text/plain');
        $this->restProxy->createPageBlob(self::$_test_container_for_blobs, 'test', 4096, $opts);

        $result = $this->restProxy->getBlob(self::$_test_container_for_blobs, 'test');

        // Assert
        $this->assertNotNull($result, '$result');

        $this->assertNotNull($result->getMetadata(), '$result->getMetadata()');
        $this->assertEquals(0, count($result->getMetadata()), 'count($result->getMetadata())');

        $props = $result->getProperties();
        $this->assertEquals('test', $props->getCacheControl(), '$props->getCacheControl()');
        $this->assertEquals('test', $props->getContentDisposition(), '$props->getContentDisposition()');
        $this->assertEquals('UTF-8', $props->getContentEncoding(), '$props->getContentEncoding()');
        $this->assertEquals('en-us', $props->getContentLanguage(), '$props->getContentLanguage()');
        $this->assertEquals('text/plain', $props->getContentType(), '$props->getContentType()');
        $this->assertEquals(4096, $props->getContentLength(), '$props->getContentLength()');
        $this->assertNotNull($props->getETag(), '$props->getETag()');
        $this->assertNull($props->getContentMD5(), '$props->getContentMD5()');
        $this->assertNotNull($props->getLastModified(), '$props->getLastModified()');
        $this->assertEquals('PageBlob', $props->getBlobType(), '$props->getBlobType()');
        $this->assertEquals('unlocked', $props->getLeaseStatus(), '$props->getLeaseStatus()');
        $this->assertEquals(0, $props->getSequenceNumber(), '$props->getSequenceNumber()');
        $this->assertEquals(
            4096,
            strlen(stream_get_contents($result->getContentStream())),
            'strlen($result->getContentStream())'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     */
    public function testGetBlobWithIfMatchETagAccessConditionWorks()
    {
        // Act
        $this->restProxy->createPageBlob(self::$_test_container_for_blobs, 'test', 4096);
        try {
            $opts = new GetBlobOptions();
            $opts->setAccessConditions(AccessCondition::ifMatch('123'));
            $this->restProxy->getBlob(self::$_test_container_for_blobs, 'test', $opts);
            $this->fail('getBlob should throw an exception');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_PRECONDITION_FAILED, $e->getCode(), 'got the expected exception');
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     */
    public function testGetBlobWithIfNoneMatchETagAccessConditionWorks()
    {
        // Act
        $this->restProxy->createPageBlob(self::$_test_container_for_blobs, 'test', 4096);
        $props = $this->restProxy->getBlobProperties(self::$_test_container_for_blobs, 'test');
        try {
            $opts = new GetBlobOptions();
            $opts->setAccessConditions(AccessCondition::ifNoneMatch($props->getProperties()->getETag()));
            $this->restProxy->getBlob(self::$_test_container_for_blobs, 'test', $opts);
            $this->fail('getBlob should throw an exception');
        } catch (ServiceException $e) {
            if (!$this->hasSecureEndpoint() && $e->getCode() == TestResources::STATUS_FORBIDDEN) {
                // Proxies can eat the access condition headers of
                // unsecured (http) requests, which causes the authentication
                // to fail, with a 403:Forbidden. There is nothing much that
                // can be done about this, other than ignore it.
                $this->markTestSkipped('Appears that a proxy ate your access condition');
            } else {
                $this->assertEquals(TestResources::STATUS_NOT_MODIFIED, $e->getCode(), 'got the expected exception');
            }
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     */
    public function testGetBlobWithIfModifiedSinceAccessConditionWorks()
    {
        // Act
        $this->restProxy->createPageBlob(self::$_test_container_for_blobs, 'test', 4096);
        $props = $this->restProxy->getBlobProperties(self::$_test_container_for_blobs, 'test');
        try {
            $opts = new GetBlobOptions();
            $lastMod = $props->getProperties()->getLastModified();
            $opts->setAccessConditions(AccessCondition::ifModifiedSince($lastMod));
            $this->restProxy->getBlob(self::$_test_container_for_blobs, 'test', $opts);
            $this->fail('getBlob should throw an exception');
        } catch (ServiceException $e) {
            if (!$this->hasSecureEndpoint() && $e->getCode() == TestResources::STATUS_FORBIDDEN) {
                // Proxies can eat the access condition headers of
                // unsecured (http) requests, which causes the authentication
                // to fail, with a 403:Forbidden. There is nothing much that
                // can be done about this, other than ignore it.
                $this->markTestSkipped('Appears that a proxy ate your access condition');
            } else {
                $this->assertEquals(TestResources::STATUS_NOT_MODIFIED, $e->getCode(), 'got the expected exception');
            }
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setBlobMetadata
     */
    public function testGetBlobWithIfNotModifiedSinceAccessConditionWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test';
        $this->restProxy->createPageBlob($container, $blob, 4096);
        $props = $this->restProxy->getBlobProperties($container, $blob);

        // To test for "IfNotModifiedSince", we need to make updates to the blob
        // until at least 1 second has passed since the blob creation
        $lastModifiedBase = $props->getProperties()->getLastModified();

        // +1 second
        $lastModifiedNext = clone $lastModifiedBase;
        $lastModifiedNext = $lastModifiedNext->modify("+1 sec");

        while (true) {
            $metadata = array('test' => 'test1');
            $result = $this->restProxy->setBlobMetadata($container, $blob, $metadata);
            if ($result->getLastModified() >= $lastModifiedNext) {
                break;
            }
        }
        try {
            $opts = new GetBlobOptions();
            $opts->setAccessConditions(AccessCondition::ifNotModifiedSince($lastModifiedBase));
            $this->restProxy->getBlob($container, $blob, $opts);
            $this->fail('getBlob should throw an exception');
        } catch (ServiceException $e) {
            $this->assertEquals(TestResources::STATUS_PRECONDITION_FAILED, $e->getCode(), 'got the expected exception');
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     */
    public function testGetBlobPropertiesWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test';
        $this->restProxy->createPageBlob($container, $blob, 4096);
        $result = $this->restProxy->getBlobProperties($container, $blob);

        // Assert
        $this->assertNotNull($result, '$result');

        $this->assertNotNull($result->getMetadata(), '$result->getMetadata()');
        $this->assertEquals(0, count($result->getMetadata()), 'count($result->getMetadata())');

        $props = $result->getProperties();
        $this->assertNotNull($props, '$props');
        $this->assertNull($props->getCacheControl(), '$props->getCacheControl()');
        $this->assertNull($props->getContentEncoding(), '$props->getContentEncoding()');
        $this->assertNull($props->getContentLanguage(), '$props->getContentLanguage()');
        $this->assertEquals('application/octet-stream', $props->getContentType(), '$props->getContentType()');
        $this->assertEquals(4096, $props->getContentLength(), '$props->getContentLength()');
        $this->assertNotNull($props->getETag(), '$props->getETag()');
        $this->assertNull($props->getContentMD5(), '$props->getContentMD5()');
        $this->assertNotNull($props->getLastModified(), '$props->getLastModified()');
        $this->assertEquals('PageBlob', $props->getBlobType(), '$props->getBlobType()');
        $this->assertEquals('unlocked', $props->getLeaseStatus(), '$props->getLeaseStatus()');
        $this->assertEquals(0, $props->getSequenceNumber(), '$props->getSequenceNumber()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobMetadata
     */
    public function testGetBlobMetadataWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test';
        $opts = new CreateBlobOptions();
        $metadata = $opts->getMetadata();
        $metadata['test'] = 'bar';
        $metadata['blah'] = 'bleah';
        $opts->setMetadata($metadata);
        $this->restProxy->createPageBlob($container, $blob, 4096, $opts);
        $props = $this->restProxy->getBlobMetadata($container, $blob);

        // Assert
        $this->assertNotNull($props, '$props');
        $this->assertNotNull($props->getETag(), '$props->getETag()');
        $this->assertNotNull($props->getMetadata(), '$props->getMetadata()');
        $this->assertEquals(2, count($props->getMetadata()), 'count($props->getMetadata())');
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive(
                'test',
                $props->getMetadata()
            ),
            'Utilities::arrayKeyExistsInsensitive(\'test\', $props->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bar', $props->getMetadata()) === false),
            '!(array_search(\'bar\', $props->getMetadata()) === FALSE)'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive('blah', $props->getMetadata()),
            'Utilities::arrayKeyExistsInsensitive(\'blah\', $props->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bleah', $props->getMetadata()) === false),
            '!(array_search(\'bleah\', $props->getMetadata()) === FALSE)'
        );
        $this->assertNotNull($props->getLastModified(), '$props->getLastModified()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setBlobProperties
     */
    public function testSetBlobPropertiesWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test10';
        $this->restProxy->createPageBlob($container, $blob, 4096);
        $opts = new SetBlobPropertiesOptions();
        $opts->setCacheControl('test');
        $opts->setContentDisposition('test');
        $opts->setContentEncoding('UTF-8');
        $opts->setContentLanguage('en-us');
        $opts->setContentLength(512);
        $opts->setContentMD5(null);
        $opts->setContentType('text/plain');
        $opts->setSequenceNumberAction('increment');
        $result = $this->restProxy->setBlobProperties($container, $blob, $opts);

        $getResult = $this->restProxy->getBlobProperties($container, $blob);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');
        $this->assertNotNull($result->getSequenceNumber(), '$result->getSequenceNumber()');
        $this->assertEquals(1, $result->getSequenceNumber(), '$result->getSequenceNumber()');

        $this->assertNotNull($getResult, '$getResult');

        $this->assertNotNull($getResult->getMetadata(), '$getResult->getMetadata()');
        $this->assertEquals(0, count($getResult->getMetadata()), 'count($getResult->getMetadata())');

        $props = $getResult->getProperties();
        $this->assertNotNull($props, '$props');
        $this->assertEquals('test', $props->getCacheControl(), '$props->getCacheControl()');
        $this->assertEquals('test', $props->getContentDisposition(), '$props->getContentDisposition()');
        $this->assertEquals('UTF-8', $props->getContentEncoding(), '$props->getContentEncoding()');
        $this->assertEquals('en-us', $props->getContentLanguage(), '$props->getContentLanguage()');
        $this->assertEquals('text/plain', $props->getContentType(), '$props->getContentType()');
        $this->assertEquals(512, $props->getContentLength(), '$props->getContentLength()');
        $this->assertNull($props->getContentMD5(), '$props->getContentMD5()');
        $this->assertNotNull($props->getLastModified(), '$props->getLastModified()');
        $this->assertEquals('PageBlob', $props->getBlobType(), '$props->getBlobType()');
        $this->assertEquals('unlocked', $props->getLeaseStatus(), '$props->getLeaseStatus()');
        $this->assertEquals(1, $props->getSequenceNumber(), '$props->getSequenceNumber()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setBlobMetadata
     */
    public function testSetBlobMetadataWorks()
    {
        // Act
        $container = self::$_test_container_for_blobs;
        $blob = 'test11';
        $metadata = array(
            'test' => 'bar',
            'blah' => 'bleah');

        $this->restProxy->createPageBlob($container, $blob, 4096);
        $result = $this->restProxy->setBlobMetadata($container, $blob, $metadata);
        $props = $this->restProxy->getBlobProperties($container, $blob);

        // Assert
        $this->assertNotNull($result, '$result');
        $this->assertNotNull($result->getETag(), '$result->getETag()');
        $this->assertNotNull($result->getLastModified(), '$result->getLastModified()');

        $this->assertNotNull($props, '$props');
        $this->assertNotNull($props->getMetadata(), '$props->getMetadata()');
        $this->assertEquals(2, count($props->getMetadata()), 'count($props->getMetadata())');
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive('test', $props->getMetadata()),
            'Utilities::arrayKeyExistsInsensitive(\'test\', $props->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bar', $props->getMetadata()) === false),
            '!(array_search(\'bar\', $props->getMetadata()) === FALSE)'
        );
        $this->assertTrue(
            Utilities::arrayKeyExistsInsensitive('blah', $props->getMetadata()),
            'Utilities::arrayKeyExistsInsensitive(\'blah\', $props->getMetadata())'
        );
        $this->assertTrue(
            !(array_search('bleah', $props->getMetadata()) === false),
            '!(array_search(\'bleah\', $props->getMetadata()) === FALSE)'
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     */
    public function testDeleteBlobWorks()
    {
        // Act
        $content = 'some $content';
        $this->restProxy->createBlockBlob(self::$_test_container_for_blobs, 'test2', $content);

        $this->restProxy->deleteBlob(self::$_test_container_for_blobs, 'test2');

        // Assert
        $this->assertTrue(true, 'success');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::copyBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     */
    public function testCopyBlobWorks()
    {
        // Act
        $content = 'some content2';
        $this->restProxy->createBlockBlob(self::$_test_container_for_blobs, 'test6', $content);
        $this->restProxy->copyBlob(
            self::$_test_container_for_blobs_2,
            'test5',
            self::$_test_container_for_blobs,
            'test6'
        );

        $result = $this->restProxy->getBlob(self::$_test_container_for_blobs_2, 'test5');

        // Assert
        $expectedMD5 = base64_encode(md5($content, true));
        
        $this->assertNotNull($result, '$result');

        $this->assertNotNull($result->getMetadata(), '$result->getMetadata()');
        $this->assertEquals(0, count($result->getMetadata()), 'count($result->getMetadata())');

        $props = $result->getProperties();
        $this->assertNotNull($props, '$props');
        $this->assertEquals(strlen($content), $props->getContentLength(), '$props->getContentLength()');
        $this->assertNotNull($props->getETag(), '$props->getETag()');
        $this->assertEquals($expectedMD5, $props->getContentMD5(), '$props->getContentMD5()');
        $this->assertNotNull($props->getLastModified(), '$props->getLastModified()');
        $this->assertEquals('BlockBlob', $props->getBlobType(), '$props->getBlobType()');
        $this->assertEquals('unlocked', $props->getLeaseStatus(), '$props->getLeaseStatus()');
        $this->assertEquals(0, $props->getSequenceNumber(), '$props->getSequenceNumber()');
        $this->assertEquals($content, stream_get_contents($result->getContentStream()), '$result->getContentStream()');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::acquireLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::releaseLease
     */
    public function testAcquireLeaseWorks()
    {
        // Act
        $content = 'some content2';
        $this->restProxy->createBlockBlob(self::$_test_container_for_blobs, 'test6', $content);
        $leaseId = $this->restProxy->acquireLease(self::$_test_container_for_blobs, 'test6')->getLeaseId();
        $this->restProxy->releaseLease(self::$_test_container_for_blobs, 'test6', $leaseId);

        // Assert
        $this->assertNotNull($leaseId, '$leaseId');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::acquireLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::releaseLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::renewLease
     */
    public function testRenewLeaseWorks()
    {
        // Act
        $content = 'some content2';
        $this->restProxy->createBlockBlob(self::$_test_container_for_blobs, 'test6', $content);
        $leaseId = $this->restProxy->acquireLease(self::$_test_container_for_blobs, 'test6')->getLeaseId();
        $leaseId2 = $this->restProxy->renewLease(self::$_test_container_for_blobs, 'test6', $leaseId)->getLeaseId();
        $this->restProxy->releaseLease(self::$_test_container_for_blobs, 'test6', $leaseId);

        // Assert
        $this->assertNotNull($leaseId, '$leaseId');
        $this->assertNotNull($leaseId2, '$leaseId2');
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::acquireLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::breakLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::releaseLease
     */
    public function testBreakLeaseWorks()
    {
        // Act
        $content = 'some content2';
        $this->restProxy->createBlockBlob(self::$_test_container_for_blobs, 'test6', $content);
        $leaseId = $this->restProxy->acquireLease(self::$_test_container_for_blobs, 'test6')->getLeaseId();
        $this->restProxy->breakLease(self::$_test_container_for_blobs, 'test6', $leaseId);
        $this->restProxy->releaseLease(self::$_test_container_for_blobs, 'test6', $leaseId);

        // Assert
        $this->assertNotNull($leaseId, '$leaseId');
    }

    // Extra tests from Java
    //    public function testRetryPolicyWorks() { }
    //    public function testRetryPolicyCompositionWorks() { }
    //    public function testRetryPolicyThrowsOnInvalidInputStream() { }
    //    public function testRetryPolicyCallsResetOnValidInputStream() { }
}
