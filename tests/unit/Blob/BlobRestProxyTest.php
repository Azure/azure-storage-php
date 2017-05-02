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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\unit\Blob;

use MicrosoftAzure\Storage\Tests\Framework\VirtualFileSystem;
use MicrosoftAzure\Storage\Tests\Framework\BlobServiceRestProxyTestBase;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Blob\Models\AppendBlockOptions;
use MicrosoftAzure\Storage\Blob\Models\ListContainersOptions;
use MicrosoftAzure\Storage\Blob\Models\ListContainersResult;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult;
use MicrosoftAzure\Storage\Blob\Models\ContainerAcl;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsResult;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult;
use MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult;
use MicrosoftAzure\Storage\Blob\Models\GetBlobResult;
use MicrosoftAzure\Storage\Blob\Models\BlobType;
use MicrosoftAzure\Storage\Blob\Models\PageRange;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesResult;
use MicrosoftAzure\Storage\Blob\Models\BlockList;
use MicrosoftAzure\Storage\Blob\Models\BlobBlockType;
use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\Block;
use MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobSnapshotOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobSnapshotResult;
use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\AccessCondition;

/**
 * Unit tests for class BlobRestProxy
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\Blob
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class BlobRestProxyTest extends BlobServiceRestProxyTestBase
{
    private function createSuffix()
    {
        return sprintf('-%04x', mt_rand(0, 65535));
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getServiceProperties
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setServiceProperties
    */
    public function testSetServiceProperties()
    {
        $this->skipIfEmulated();
        
        // Setup
        $expected = ServiceProperties::create(TestResources::setBlobServicePropertiesSample());
        
        // Test
        $this->setServiceProperties($expected);
        //Add 30s interval to wait for setting to take effect.
        \sleep(30);
        $actual = $this->restProxy->getServiceProperties();
        
        // Assert
        $this->assertEquals($expected->toXml($this->xmlSerializer), $actual->getValue()->toXml($this->xmlSerializer));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::send
     */
    public function testListContainersSimple()
    {
        // Setup
        $container1 = 'listcontainersimple1' . $this->createSuffix();
        $container2 = 'listcontainersimple2' . $this->createSuffix();
        $container3 = 'listcontainersimple3' . $this->createSuffix();

        parent::createContainer($container1);
        parent::createContainer($container2);
        parent::createContainer($container3);
        
        // Test
        $result = $this->restProxy->listContainers();

        // Assert
        $containers = $result->getContainers();
        $this->assertNotNull($result->getAccountName());
        $this->assertEquals($container1, $containers[0]->getName());
        $this->assertEquals($container2, $containers[1]->getName());
        $this->assertEquals($container3, $containers[2]->getName());
    }

    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
    */
    public function testListContainersWithOptions()
    {
        // Setup
        $container1    = 'listcontainerwithoptions1' . $this->createSuffix();
        $container2    = 'listcontainerwithoptions2' . $this->createSuffix();
        $container3    = 'mlistcontainerwithoptions3' . $this->createSuffix();
        $metadataName  = 'Mymetadataname';
        $metadataValue = 'MetadataValue';
        $options = new CreateContainerOptions();
        $options->addMetadata($metadataName, $metadataValue);
        parent::createContainer($container1);
        parent::createContainer($container2, $options);
        parent::createContainer($container3);
        $options = new ListContainersOptions();
        $options->setPrefix('list');
        $options->setIncludeMetadata(true);
        
        // Test
        $result = $this->restProxy->listContainers($options);
        
        // Assert
        $containers   = $result->getContainers();
        $metadata = $containers[1]->getMetadata();
        $this->assertEquals(2, count($containers));
        $this->assertEquals($container1, $containers[0]->getName());
        $this->assertEquals($container2, $containers[1]->getName());
        $this->assertEquals($metadataValue, $metadata[$metadataName]);
    }

    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
    */
    public function testListContainersWithNextMarker()
    {
        // Setup
        $container1 = 'listcontainerswithnextmarker1' . $this->createSuffix();
        $container2 = 'listcontainerswithnextmarker2' . $this->createSuffix();
        $container3 = 'listcontainerswithnextmarker3' . $this->createSuffix();
        parent::createContainer($container1);
        parent::createContainer($container2);
        parent::createContainer($container3);
        $options = new ListContainersOptions();
        $options->setMaxResults('2');
        
        // Test
        $result = $this->restProxy->listContainers($options);
        
        // Assert
        $containers = $result->getContainers();
        $this->assertEquals(2, count($containers));
        $this->assertEquals($container1, $containers[0]->getName());
        $this->assertEquals($container2, $containers[1]->getName());
        
        // Test
        $options->setMarker($result->getNextMarker());
        $result = $this->restProxy->listContainers($options);
        $containers = $result->getContainers();

        // Assert
        $this->assertEquals(1, count($containers));
        $this->assertEquals($container3, $containers[0]->getName());
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
    * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
    * @expectedExceptionMessage 400
    */
    public function testListContainersWithInvalidNextMarkerFail()
    {
        $this->skipIfEmulated();
        
        // Setup
        $container1 = 'listcontainerswithinvalidnextmarker1' . $this->createSuffix();
        $container2 = 'listcontainerswithinvalidnextmarker2' . $this->createSuffix();
        $container3 = 'listcontainerswithinvalidnextmarker3' . $this->createSuffix();
        parent::createContainer($container1);
        parent::createContainer($container2);
        parent::createContainer($container3);
        $options = new ListContainersOptions();
        $options->setMaxResults('2');
        
        // Test
        $this->restProxy->listContainers($options);
        $options->setMarker('wrong marker');
        $this->restProxy->listContainers($options);
    }

    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
    */
    public function testListContainersWithNoContainers()
    {
        // Test
        $result = $this->restProxy->listContainers();
        
        // Assert
        $containers = $result->getContainers();
        $this->assertTrue(empty($containers));
    }

    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listContainers
    */
    public function testListContainersWithOneResult()
    {
        // Setup
        $containerName = 'listcontainerswithoneresult' . $this->createSuffix();
        parent::createContainer($containerName);
        
        // Test
        $result = $this->restProxy->listContainers();
        $containers = $result->getContainers();

        // Assert
        $this->assertEquals(1, count($containers));
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
    */
    public function testCreateContainerSimple()
    {
        // Setup
        $containerName = 'createcontainersimple' . $this->createSuffix();
        
        // Test
        $this->createContainer($containerName);
        
        // Assert
        $result = $this->restProxy->listContainers();
        $containers = $result->getContainers();
        $this->assertEquals(1, count($containers));
        $this->assertEquals($containers[0]->getName(), $containerName);
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
    */
    public function testCreateContainerWithoutOptions()
    {
        // Setup
        $containerName = 'createcontainerwithoutoptions' . $this->createSuffix();
        
        // Test
        $this->createContainer($containerName);
        
        // Assert
        $result = $this->restProxy->listContainers();
        $containers = $result->getContainers();
        $this->assertEquals(1, count($containers));
        $this->assertEquals($containers[0]->getName(), $containerName);
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
    */
    public function testCreateContainerWithMetadata()
    {
        $containerName = 'createcontainerwithmetadata' . $this->createSuffix();
        $metadataName  = 'Name';
        $metadataValue = 'MyName';
        $options = new CreateContainerOptions();
        $options->addMetadata($metadataName, $metadataValue);
        $options->setPublicAccess('blob');
        
        // Test
        $this->createContainer($containerName, $options);

        // Assert
        $options = new ListContainersOptions();
        $options->setIncludeMetadata(true);
        $result   = $this->restProxy->listContainers($options);
        $containers   = $result->getContainers();
        $metadata = $containers[0]->getMetadata();
        $this->assertEquals($metadataValue, $metadata[$metadataName]);
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
        $this->setExpectedException(get_class(new ServiceException('400')));
    * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
    * @expectedExceptionMessage 400
    */
    public function testCreateContainerInvalidNameFail()
    {
        // Setup
        $containerName = 'CreateContainerInvalidNameFail' . $this->createSuffix();
        
        // Test
        $this->createContainer($containerName);
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainer
    * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
    * @expectedExceptionMessage 409
    */
    public function testCreateContainerAlreadyExitsFail()
    {
        // Setup
        $containerName = 'createcontaineralreadyexitsfail' . $this->createSuffix();
        $this->createContainer($containerName);

        // Test
        $this->createContainer($containerName);
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
    */
    public function testDeleteContainer()
    {
        // Setup
        $containerName = 'deletecontainer' . $this->createSuffix();
        $this->createContainer($containerName);
        
        // Test
        $this->restProxy->deleteContainer($containerName);
        
        // Assert
        $result = $this->restProxy->listContainers();
        $containers = $result->getContainers();
        $this->assertTrue(empty($containers));
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteContainer
    * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
    * @expectedExceptionMessage 404
    */
    public function testDeleteContainerFail()
    {
        // Setup
        $containerName = 'deletecontainerfail' . $this->createSuffix();
        
        // Test
        $this->restProxy->deleteContainer($containerName);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getContainerProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getContainerPropertiesImpl
     */
    public function testGetContainerProperties()
    {
        // Setup
        $name = 'getcontainerproperties' . $this->createSuffix();
        $this->createContainer($name);
        
        // Test
        $result = $this->restProxy->getContainerProperties($name);
        
        // Assert
        $this->assertNotNull($result->getETag());
        $this->assertNotNull($result->getLastModified());
        $this->assertCount(0, $result->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getContainerMetadata
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getContainerPropertiesImpl
     */
    public function testGetContainerMetadata()
    {
        // Setup
        $name     = 'getcontainermetadata' . $this->createSuffix();
        $options  = new CreateContainerOptions();
        $expected = array('name1' => 'MyName1', 'mymetaname' => '12345', 'values' => 'Microsoft_');
        $options->setMetadata($expected);
        $this->createContainer($name, $options);
        $result = $this->restProxy->getContainerProperties($name);
        $expectedETag = $result->getETag();
        $expectedLastModified = $result->getLastModified();
        
        // Test
        $result = $this->restProxy->getContainerMetadata($name);
        
        // Assert
        $this->assertEquals($expectedETag, $result->getETag());
        $this->assertEquals($expectedLastModified, $result->getLastModified());
        $this->assertEquals($expected, $result->getMetadata());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getContainerAcl
     */
    public function testGetContainerAcl()
    {
        // Setup
        $name = 'getcontaineracl' . $this->createSuffix();
        $expectedAccess = 'container';
        $this->createContainer($name);
        
        // Test
        $result = $this->restProxy->getContainerAcl($name);
        
        // Assert
        $this->assertEquals($expectedAccess, $result->getContainerAcl()->getPublicAccess());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setContainerAcl
     */
    public function testSetContainerAcl()
    {
        // Setup
        $name = 'setcontaineracl' . $this->createSuffix();
        $this->createContainer($name);
        $sample = TestResources::getContainerAclMultipleEntriesSample();
        $expectedETag = '0x8CAFB82EFF70C46';
        $expectedLastModified = new \DateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $expectedPublicAccess = 'container';
        $acl = ContainerAcl::create($expectedPublicAccess, $sample['SignedIdentifiers']);

        // Test
        $this->restProxy->setContainerAcl($name, $acl);
        
        // Assert
        $actual = $this->restProxy->getContainerAcl($name);
        $this->assertEquals($acl->getPublicAccess(), $actual->getContainerAcl()->getPublicAccess());
        $this->assertEquals($acl->getSignedIdentifiers(), $actual->getContainerAcl()->getSignedIdentifiers());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setContainerMetadata
     */
    public function testSetContainerMetadata()
    {
        // Setup
        $name     = 'setcontainermetadata' . $this->createSuffix();
        $expected = array('name1' => 'MyName1', 'mymetaname' => '12345', 'values' => 'Microsoft_');
        $this->createContainer($name);
        
        // Test
        $this->restProxy->setContainerMetadata($name, $expected);
        
        // Assert
        $result = $this->restProxy->getContainerProperties($name);
        $expectedETag = $result->getETag();
        $expectedLastModified = $result->getLastModified();
        $this->assertEquals($expectedETag, $result->getETag());
        $this->assertEquals($expectedLastModified, $result->getLastModified());
        $this->assertEquals($expected, $result->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::send
     */
    public function testListBlobsSimple()
    {
        // Setup
        $name  = 'listblobssimple' . $this->createSuffix();
        $blob1 = 'blob1';
        $blob2 = 'blob2';
        $blob3 = 'blob3';
        $length = 512;

        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob1, $length);
        $this->restProxy->createPageBlob($name, $blob2, $length);
        $this->restProxy->createPageBlob($name, $blob3, $length);
        
        // Test
        $result = $this->restProxy->listBlobs($name);

        // Assert
        $blobs = $result->getBlobs();
        $this->assertNotNull($result->getContainerName());
        $this->assertEquals($blob1, $blobs[0]->getName());
        $this->assertEquals($blob2, $blobs[1]->getName());
        $this->assertEquals($blob3, $blobs[2]->getName());
        $this->assertNull($blobs[2]->getSnapshot());
        $this->assertNotNull($blobs[2]->getUrl());
        $this->assertCount(0, $blobs[2]->getMetadata());
        $this->assertInstanceOf('MicrosoftAzure\Storage\Blob\Models\BlobProperties', $blobs[2]->getProperties());
    }

    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
    */
    public function testListBlobsWithOptions()
    {
        // Setup
        $name  = 'listblobswithoptions' . $this->createSuffix();
        $blob1 = 'blob1';
        $blob2 = 'blob2';
        $blob3 = 'blob3';
        $blob4 = 'lblob1';
        $blob5 = 'lblob2';
        $blob6 = 'lblob3';
        $length = 512;
        $options = new ListBlobsOptions();
        $options->setIncludeMetadata(true);
        $options->setIncludeSnapshots(true);
        $options->setIncludeUncommittedBlobs(true);
        $options->setMaxResults(10);
        $options->setPrefix('lb');

        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob1, $length);
        $this->restProxy->createPageBlob($name, $blob2, $length);
        $this->restProxy->createPageBlob($name, $blob3, $length);
        $this->restProxy->createPageBlob($name, $blob4, $length);
        $this->restProxy->createPageBlob($name, $blob5, $length);
        $this->restProxy->createPageBlob($name, $blob6, $length);
        
        // Test
        $result = $this->restProxy->listBlobs($name, $options);

        // Assert
        $this->assertCount(3, $result->getBlobs());
        $this->assertCount(0, $result->getBlobPrefixes());
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
    */
    public function testListBlobsWithOptionsWithDelimiter()
    {
        $this->skipIfEmulated();
        
        // Setup
        $name  = 'listblobswithoptionswithdelimiter' . $this->createSuffix();
        $blob1 = 'blob1';
        $blob2 = 'blob2';
        $blob3 = 'blob3';
        $blob4 = 'lblob1';
        $blob5 = 'lblob2';
        $blob6 = 'lblob3';
        $length = 512;
        $options = new ListBlobsOptions();
        $options->setDelimiter('b');
        $options->setIncludeMetadata(true);
        $options->setIncludeUncommittedBlobs(true);
        $options->setMaxResults(10);
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob1, $length);
        $this->restProxy->createPageBlob($name, $blob2, $length);
        $this->restProxy->createPageBlob($name, $blob3, $length);
        $this->restProxy->createPageBlob($name, $blob4, $length);
        $this->restProxy->createPageBlob($name, $blob5, $length);
        $this->restProxy->createPageBlob($name, $blob6, $length);
        
        // Test
        $result = $this->restProxy->listBlobs($name, $options);

        // Assert
        $this->assertCount(0, $result->getBlobs());
        $this->assertCount(2, $result->getBlobPrefixes());
    }

    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
    */
    public function testListBlobsWithNextMarker()
    {
        // Setup
        $name  = 'listblobswithnextmarker' . $this->createSuffix();
        $blob1 = 'blob1';
        $blob2 = 'blob2';
        $blob3 = 'blob3';
        $length = 512;
        $options = new ListBlobsOptions();
        $options->setMaxResults(2);

        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob1, $length);
        $this->restProxy->createPageBlob($name, $blob2, $length);
        $this->restProxy->createPageBlob($name, $blob3, $length);
        
        // Test
        $result = $this->restProxy->listBlobs($name, $options);
        
        // Assert
        $this->assertCount(2, $result->getBlobs());
        
        // Setup
        $options->setMarker($result->getNextMarker());
        
        $result = $this->restProxy->listBlobs($name, $options);

        // Assert
        $this->assertCount(1, $result->getBlobs());
    }

    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
    */
    public function testListBlobsWithNoBlobs()
    {
        // Test
        $name = 'listblobswithnoblobs' . $this->createSuffix();
        $this->createContainer($name);
        $result = $this->restProxy->listBlobs($name);
        
        // Assert
        $this->assertCount(0, $result->getBlobs());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
     */
    public function testListBlobsWithOneResult()
    {
        // Test
        $name = 'listblobswithoneresult' . $this->createSuffix();
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, 'myblob', 512);
        $result = $this->restProxy->listBlobs($name);
        
        // Assert
        $this->assertCount(1, $result->getBlobs());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addCreateBlobOptionalHeaders
     */
    public function testCreatePageBlob()
    {
        // Setup
        $name = 'createpageblob' . $this->createSuffix();
        $this->createContainer($name);
        
        // Test
        $createResult = $this->restProxy->createPageBlob($name, 'myblob', 512);
        
        // Assert
        $result = $this->restProxy->listBlobs($name);
        $this->assertNotNull($createResult->getETag());
        $this->assertInstanceOf('\DateTime', $createResult->getLastModified());
        $this->assertCount(1, $result->getBlobs());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createAppendBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     */
    public function testCreateAppendBlob()
    {
        // Setup
        $name = 'createappendblob' . $this->createSuffix();
        $this->createContainer($name);
        
        // Test
        $createResult = $this->restProxy->createAppendBlob($name, 'myblob');
        
        // Assert
        $this->assertNotNull($createResult->getETag());
        $this->assertInstanceOf('\DateTime', $createResult->getLastModified());

        $appendBlob = $this->restProxy->getBlobProperties($name, 'myblob');
        $this->assertEquals('AppendBlob', $appendBlob->getProperties()->getBlobType());
        $this->assertEquals(0, $appendBlob->getProperties()->getCommittedBlockCount());
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createAppendBlob
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::appendBlock
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobs
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
    */
    public function testAppendBlock()
    {
        // Setup
        $name = 'createappendblob' . $this->createSuffix();
        $this->createContainer($name);
        $textToBeAppended = 'text to be appended';
        
        // Test
        $this->restProxy->createAppendBlob($name, 'myblob');
        $appendResult = $this->restProxy->appendBlock($name, 'myblob', $textToBeAppended);

        // Assert
        $this->assertNotNull($appendResult->getETag());
        $this->assertInstanceOf('\DateTime', $appendResult->getLastModified());
        $this->assertEquals(0, $appendResult->getAppendOffset());
        $this->assertEquals(1, $appendResult->getCommittedBlockCount());

        // List blobs
        $listBlobs = $this->restProxy->listBlobs($name, null)->getBlobs();
        $this->assertCount(1, $listBlobs);
        $this->assertEquals('AppendBlob', $listBlobs[0]->getProperties()->getBlobType());


        // Get append blob properties
        $appendBlob = $this->restProxy->getBlobProperties($name, 'myblob');
        $this->assertEquals('AppendBlob', $appendBlob->getProperties()->getBlobType());
        $this->assertEquals(1, $appendBlob->getProperties()->getCommittedBlockCount());
        $this->assertEquals(strlen($textToBeAppended), $appendBlob->getProperties()->getContentLength());

        // Append again
        $appendResult = $this->restProxy->appendBlock($name, 'myblob', $textToBeAppended);
        $this->assertNotNull($appendResult->getETag());
        $this->assertInstanceOf('\DateTime', $appendResult->getLastModified());
        $this->assertEquals(19, $appendResult->getAppendOffset());
        $this->assertEquals(2, $appendResult->getCommittedBlockCount());

        $appendBlob = $this->restProxy->getBlobProperties($name, 'myblob');
        $this->assertEquals('AppendBlob', $appendBlob->getProperties()->getBlobType());
        $this->assertEquals(2, $appendBlob->getProperties()->getCommittedBlockCount());
        $this->assertEquals(2 * strlen($textToBeAppended), $appendBlob->getProperties()->getContentLength());
    }

    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createAppendBlob
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::appendBlock
    */
    public function testAppendBlockSuccessWithAppendPosition()
    {
        // Setup
        $name = 'appendblockappendpositionsuccess' . $this->createSuffix();
        $this->createContainer($name);
        $textToBeAppended = 'text to be appended';
        $appendBlockOption = new AppendBlockOptions();
        $appendBlockOption->setAppendPosition(0);
        
        // Test
        $this->restProxy->createAppendBlob($name, 'myblob');
        $this->restProxy->appendBlock($name, 'myblob', $textToBeAppended, $appendBlockOption);

        // Append again
        $appendBlockOption->setAppendPosition(strlen($textToBeAppended));
        $appendResult = $this->restProxy->appendBlock($name, 'myblob', $textToBeAppended, $appendBlockOption);
        $this->assertNotNull($appendResult->getETag());
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createAppendBlob
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::appendBlock
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage 412
    */
    public function testAppendBlockConflictBecauseOfAppendPosition()
    {
        // Setup
        $name = 'appendblockappendpositionconflict' . $this->createSuffix();
        $this->createContainer($name);
        $textToBeAppended = 'text to be appended';
        $appendBlockOption = new AppendBlockOptions();
        $appendBlockOption->setAppendPosition(1);
        
        // Test
        $this->restProxy->createAppendBlob($name, 'myblob');
        $this->restProxy->appendBlock($name, 'myblob', $textToBeAppended, $appendBlockOption);
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createAppendBlob
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::appendBlock
    */
    public function testAppendBlockSuccessWithMaxBlobSize()
    {
        // Setup
        $name = 'appendblockmaxblobsizeconflict' . $this->createSuffix();
        $this->createContainer($name);
        $textToBeAppended = 'text to be appended';
        $appendBlockOption = new AppendBlockOptions();
        $appendBlockOption->setMaxBlobSize(1000);
        
        // Test
        $this->restProxy->createAppendBlob($name, 'myblob');
        $appendResult = $this->restProxy->appendBlock($name, 'myblob', $textToBeAppended, $appendBlockOption);
        $this->assertNotNull($appendResult->getETag());
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createAppendBlob
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::appendBlock
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage 412
    */
    public function testAppendBlockConflictBecauseOfMaxBlobSize()
    {
        // Setup
        $name = 'appendblockmaxblobsizeconflict' . $this->createSuffix();
        $this->createContainer($name);
        $textToBeAppended = 'text to be appended';
        $appendBlockOption = new AppendBlockOptions();
        $appendBlockOption->setMaxBlobSize(1);
        
        // Test
        $this->restProxy->createAppendBlob($name, 'myblob');
        $this->restProxy->appendBlock($name, 'myblob', $textToBeAppended, $appendBlockOption);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addCreateBlobOptionalHeaders
     */
    public function testCreatePageBlobWithExtraOptions()
    {
        // Setup
        $name = 'createpageblobwithextraoptions' . $this->createSuffix();
        $this->createContainer($name);
        $metadata = array('Name1' => 'Value1', 'Name2' => 'Value2');
        $contentType = Resources::BINARY_FILE_TYPE;
        $options = new CreateBlobOptions();
        $options->setMetadata($metadata);
        $options->setContentType($contentType);
        
        // Test
        $this->restProxy->createPageBlob($name, 'myblob', 512, $options);
        
        // Assert
        $result = $this->restProxy->listBlobs($name);
        $this->assertCount(1, $result->getBlobs());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addCreateBlobOptionalHeaders
     */
    public function testCreateBlockBlobWithBinary()
    {
        // Setup
        $name = 'createblockblobwithbinary' . $this->createSuffix();
        $this->createContainer($name);
        
        // Test
        $createResult = $this->restProxy->createBlockBlob($name, 'myblob', '123455');
        
        // Assert
        $result = $this->restProxy->listBlobs($name);
        $blobs = $result->getBlobs();
        $blob = $blobs[0];
        $this->assertNotNull($createResult->getETag());
        $this->assertInstanceOf('\DateTime', $createResult->getLastModified());
        $this->assertCount(1, $result->getBlobs());
        $this->assertEquals(Resources::BINARY_FILE_TYPE, $blob->getProperties()->getContentType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addCreateBlobOptionalHeaders
     */
    public function testCreateBlockBlobWithPlainText()
    {
        // Setup
        $name = 'createblockblobwithplaintext' . $this->createSuffix();
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        
        // Test
        $this->restProxy->createBlockBlob($name, 'myblob', 'Hello world', $options);
        
        // Assert
        $result = $this->restProxy->listBlobs($name);
        $blobs = $result->getBlobs();
        $blob = $blobs[0];
        $this->assertCount(1, $result->getBlobs());
        $this->assertEquals($contentType, $blob->getProperties()->getContentType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addCreateBlobOptionalHeaders
     */
    public function testCreateBlockBlobWithStream()
    {
        // Setup
        $name = 'createblockblobwithstream' . $this->createSuffix();
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $fileContents = 'Hello world, I\'m a file';
        $stream = fopen(VirtualFileSystem::newFile($fileContents), 'r');
        
        // Test
        $this->restProxy->createBlockBlob($name, 'myblob', $stream, $options);
        
        // Assert
        $result = $this->restProxy->listBlobs($name);
        $blobs = $result->getBlobs();
        $blob = $blobs[0];
        $this->assertCount(1, $result->getBlobs());
        $this->assertEquals($contentType, $blob->getProperties()->getContentType());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getBlobPropertiesResultFromResponse
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesResult::create
     */
    public function testGetBlobProperties()
    {
        // Setup
        $name = 'getblobproperties' . $this->createSuffix();
        $contentLength = 512;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, 'myblob', $contentLength);
        
        // Test
        $result = $this->restProxy->getBlobProperties($name, 'myblob');
        
        // Assert
        $this->assertEquals($contentLength, $result->getProperties()->getContentLength());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setBlobProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getBlobPropertiesResultFromResponse
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesResult::create
     */
    public function testSetBlobProperties()
    {
        // Setup
        $name = 'setblobproperties' . $this->createSuffix();
        $contentLength = 1024;
        $blob = 'myblob';
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, 'myblob', 512);
        $options = new SetBlobPropertiesOptions();
        $options->setContentLength($contentLength);
        
        // Test
        $this->restProxy->setBlobProperties($name, $blob, $options);
        
        // Assert
        $result = $this->restProxy->getBlobProperties($name, $blob);
        $this->assertEquals($contentLength, $result->getProperties()->getContentLength());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setBlobProperties
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getBlobPropertiesResultFromResponse
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesResult::create
     */
    public function testSetBlobPropertiesWithNoOptions()
    {
        // Setup
        $name = 'setblobpropertieswithnooptions' . $this->createSuffix();
        $blob = 'myblob';
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, 512);
        
        // Test
        $result = $this->restProxy->setBlobProperties($name, $blob);
        
        // Assert
        $this->assertInstanceOf('\DateTime', $result->getLastModified());
        $this->assertTrue(!is_null($result->getETag()));
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobMetadata
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataResult::create
     */
    public function testGetBlobMetadata()
    {
        // Setup
        $name = 'getblobmetadata' . $this->createSuffix();
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $blob = 'myblob';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setMetadata($metadata);
        $this->restProxy->createPageBlob($name, $blob, 512, $options);
        
        // Test
        $result = $this->restProxy->getBlobMetadata($name, $blob);
        
        // Assert
        $this->assertEquals($metadata, $result->getMetadata());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setBlobMetadata
     * @covers MicrosoftAzure\Storage\Blob\Models\SetBlobMetadataResult::create
     * @covers MicrosoftAzure\Storage\Common\Internal\ServiceRestProxy::addMetadataHeaders
     */
    public function testSetBlobMetadata()
    {
        // Setup
        $name = 'setblobmetadata' . $this->createSuffix();
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $blob = 'myblob';
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, 512);
        
        // Test
        $this->restProxy->setBlobMetadata($name, $blob, $metadata);
        
        // Assert
        $result = $this->restProxy->getBlobMetadata($name, $blob);
        $this->assertEquals($metadata, $result->getMetadata());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addOptionalRangeHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::create
     */
    public function testGetBlob()
    {
        // Setup
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $contentStream = 'Hello world';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->createBlockBlob($name, $blob, $contentStream, $options);
        
        // Test
        $result = $this->restProxy->getBlob($name, $blob);
        
        // Assert
        $this->assertEquals(BlobType::BLOCK_BLOB, $result->getProperties()->getBlobType());
        $this->assertEquals($metadata, $result->getMetadata());
        $this->assertEquals(
            $contentStream,
            stream_get_contents($result->getContentStream())
        );
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlobAsync
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage 404
     */
    public function testGetBlobNotExist()
    {
        $name = 'notexistcontainer' . $this->createSuffix();
        $blob = 'notexistblob';

        $promise = $this->restProxy->getBlobAsync($name, $blob);

        $promise->wait();
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createContainerAsync
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage should be of type 'string'
     */
    public function testCreateContainerAsyncWithInvalidParameters()
    {
        $this->restProxy->createContainerAsync(array());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addOptionalRangeHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::create
     */
    public function testGetBlobWithRange()
    {
        // Setup
        $name = '$root';
        $blob = 'myblob';
        $this->createContainer($name);
        $this->_createdContainers[] = '$root';
        $length = 512;
        $range = new PageRange(0, 511);
        $contentStream = Resources::EMPTY_STRING;
        $this->restProxy->createPageBlob('', $blob, $length);
        for ($i = 0; $i < 512; $i++) {
            $contentStream .= 'A';
        }
        $this->restProxy->createBlobPages('', $blob, $range, $contentStream);
        $options = new GetBlobOptions();
        $options->setRangeStart(0);
        $options->setRangeEnd(511);
        
        // Test
        $result = $this->restProxy->getBlob('', $blob, $options);
        
        // Assert
        $this->assertEquals(BlobType::PAGE_BLOB, $result->getProperties()->getBlobType());
        $this->assertEquals(
            $contentStream,
            stream_get_contents($result->getContentStream())
        );
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addOptionalRangeHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::create
     */
    public function testGetBlobWithEndRange()
    {
        // Setup
        $name = 'getblobwithendrange' . $this->createSuffix();
        $blob = 'myblob';
        $this->createContainer($name);
        $length = 512;
        $range = new PageRange(0, 511);
        $contentStream = Resources::EMPTY_STRING;
        $this->restProxy->createPageBlob($name, $blob, $length);
        for ($i = 0; $i < 512; $i++) {
            $contentStream .= 'A';
        }
        $this->restProxy->createBlobPages($name, $blob, $range, $contentStream);
        $options = new GetBlobOptions();
        $options->setRangeStart(null);
        $options->setRangeEnd(511);
        
        // Test
        $result = $this->restProxy->getBlob($name, $blob, $options);
        
        // Assert
        $this->assertEquals(BlobType::PAGE_BLOB, $result->getProperties()->getBlobType());
        $this->assertEquals(
            $contentStream,
            stream_get_contents($result->getContentStream())
        );
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addOptionalRangeHeader
     * @covers MicrosoftAzure\Storage\Blob\Models\GetBlobResult::create
     */
    public function testGetBlobGarbage()
    {
        // Setup
        $name = 'getblobwithgarbage' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $contentStream = chr(0);
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->createBlockBlob($name, $blob, $contentStream, $options);
        
        // Test
        $result = $this->restProxy->getBlob($name, $blob);
        
        // Assert
        $this->assertEquals(BlobType::BLOCK_BLOB, $result->getProperties()->getBlobType());
        $this->assertEquals($metadata, $result->getMetadata());
        $this->assertEquals(
            $contentStream,
            stream_get_contents($result->getContentStream())
        );
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     */
    public function testDeleteBlob()
    {
        // Setup
        $name = 'deleteblob' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        
        // Test
        $this->restProxy->deleteBlob($name, $blob);
        
        // Assert
        $result = $this->restProxy->listBlobs($name);
        $this->assertCount(0, $result->getBlobs());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     */
    public function testDeleteBlobSnapshot()
    {
        // Setup
        $name = 'deleteblobsnapshot' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        $snapshot = $this->restProxy->createBlobSnapshot($name, $blob);
        $options = new DeleteBlobOptions();
        $options->setSnapshot($snapshot->getSnapshot());
        
        // Test
        $this->restProxy->deleteBlob($name, $blob, $options);
        
        // Assert
        $listOptions = new ListBlobsOptions();
        $listOptions->setIncludeSnapshots(true);
        $blobsResult = $this->restProxy->listBlobs($name, $listOptions);
        $blobs = $blobsResult->getBlobs();
        $actualBlob = $blobs[0];
        $this->assertNull($actualBlob->getSnapshot());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::deleteBlob
     */
    public function testDeleteBlobSnapshotsOnly()
    {
        // Setup
        $name = 'deleteblobsnapshotsonly' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        $this->restProxy->createBlobSnapshot($name, $blob);
        $options = new DeleteBlobOptions();
        $options->setDeleteSnaphotsOnly(true);
        
        // Test
        $this->restProxy->deleteBlob($name, $blob, $options);
        
        // Assert
        $listOptions = new ListBlobsOptions();
        $listOptions->setIncludeSnapshots(true);
        $blobsResult = $this->restProxy->listBlobs($name, $listOptions);
        $blobs = $blobsResult->getBlobs();
        $actualBlob = $blobs[0];
        $this->assertNull($actualBlob->getSnapshot());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::acquireLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_putLeaseImpl
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_createPath
     */
    public function testAcquireLease()
    {
        // Setup
        $name = 'acquirelease' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        
        // Test
        $proposedLeaseId = '6c75960f-2837-4c35-9948-e35e87d00edf';
        $result = $this->restProxy->acquireLease($name, $blob, $proposedLeaseId, 20);
        
        // Assert
        $this->assertEquals($proposedLeaseId, $result->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::acquireLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_putLeaseImpl
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_createPath
     */
    public function testAcquireContainerLease()
    {
        // Setup
        $name = 'acquirelease' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        
        // Test
        $proposedLeaseId = '47809df9-8f4a-4243-828b-56243e702a04';
        $result = $this->restProxy->acquireLease($name, null, $proposedLeaseId);
        
        // Assert
        $this->assertEquals($proposedLeaseId, $result->getLeaseId());

        // Break the lease so that the clean-up can delete the container
        $result = $this->restProxy->breakLease($name, null, $result->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::changeLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_putLeaseImpl
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_createPath
     */
    public function testChangeLease()
    {
        // Setup
        $name = 'changelease' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        
        // Test
        $result = $this->restProxy->acquireLease($name, $blob);
        
        $proposedLeaseId = '6c75960f-2837-4c35-9948-e35e87d00edf';
        $result = $this->restProxy->changeLease($name, $blob, $result->getLeaseId(), $proposedLeaseId);
        
        // Assert
        $this->assertEquals($proposedLeaseId, $result->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::renewLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_putLeaseImpl
     */
    public function testRenewLease()
    {
        // Setup
        $name = 'renewlease' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        $result = $this->restProxy->acquireLease($name, $blob);
        
        // Test
        $result = $this->restProxy->renewLease($name, $blob, $result->getLeaseId());
        
        // Assert
        $this->assertNotNull($result->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::releaseLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_putLeaseImpl
     */
    public function testReleaseLease()
    {
        // Setup
        $name = 'releaselease' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        $result = $this->restProxy->acquireLease($name, $blob);
        
        // Test
        $this->restProxy->releaseLease($name, $blob, $result->getLeaseId());
        
        // Assert
        $result = $this->restProxy->acquireLease($name, $blob);
        $this->assertNotNull($result->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::breakLease
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_putLeaseImpl
     */
    public function testBreakLease()
    {
        // Setup
        $name = 'breaklease' . $this->createSuffix();
        $blob = 'myblob';
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, 'Hello world', $options);
        $this->restProxy->acquireLease($name, $blob);
        
        // Test
        $result = $this->restProxy->breakLease($name, $blob, 10);
        
        // Assert
        $this->assertInstanceOf('MicrosoftAzure\Storage\Blob\Models\BreakLeaseResult', $result);
        $this->assertNotNull($result->getLeaseTime());
        $result = $this->restProxy->acquireLease($name, $blob);
        $this->assertNotNull($result->getLeaseId());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobPages
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_updatePageBlobPagesImpl
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addOptionalRangeHeader
     */
    public function testCreateBlobPages()
    {
        // Setup
        $name = 'createblobpages' . $this->createSuffix();
        $blob = 'myblob';
        $length = 512;
        $range = new PageRange(0, 511);
        $content = Resources::EMPTY_STRING;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, $length);
        for ($i = 0; $i < 512; $i++) {
            $content .= 'A';
        }
        
        // Test
        $actual = $this->restProxy->createBlobPages($name, $blob, $range, $content);
        
        // Assert
        $this->assertNotNull($actual->getETag());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::clearBlobPages
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_updatePageBlobPagesImpl
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_addOptionalRangeHeader
     */
    public function testClearBlobPages()
    {
        // Setup
        $name = 'clearblobpages' . $this->createSuffix();
        $blob = 'myblob';
        $length = 512;
        $range = new PageRange(0, 511);
        $content = Resources::EMPTY_STRING;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, $length);
        for ($i = 0; $i < 512; $i++) {
            $content .= 'A';
        }
        $this->restProxy->createBlobPages($name, $blob, $range, $content);
        
        // Test
        $actual = $this->restProxy->clearBlobPages($name, $blob, $range);
        
        // Assert
        $this->assertNotNull($actual->getETag());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listPageBlobRanges
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::create
     */
    public function testListPageBlobRanges()
    {
        // Setup
        $name = 'listpageblobranges' . $this->createSuffix();
        $blob = 'myblob';
        $length = 512;
        $range = new PageRange(0, 511);
        $content = Resources::EMPTY_STRING;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, $length);
        for ($i = 0; $i < 512; $i++) {
            $content .= 'A';
        }
        $this->restProxy->createBlobPages($name, $blob, $range, $content);
        
        // Test
        $result = $this->restProxy->listPageBlobRanges($name, $blob);
        
        // Assert
        $this->assertNotNull($result->getETag());
        $this->assertCount(1, $result->getPageRanges());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listPageBlobRanges
     * @covers MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesResult::create
     */
    public function testListPageBlobRangesEmpty()
    {
        // Setup
        $name = 'listpageblobrangesempty' . $this->createSuffix();
        $blob = 'myblob';
        $length = 512;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, $length);
        
        // Test
        $result = $this->restProxy->listPageBlobRanges($name, $blob);
        
        // Assert
        $this->assertNotNull($result->getETag());
        $this->assertCount(0, $result->getPageRanges());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobBlock
     */
    public function testCreateBlobBlock()
    {
        // Setup
        $name = 'createblobblock' . $this->createSuffix();
        $this->createContainer($name);
        $options = new ListBlobsOptions();
        $options->setIncludeUncommittedBlobs(true);

        // Test
        $this->restProxy->createBlobBlock($name, 'myblob', 'AAAAAA==', 'Hello world');

        // Assert
        $result = $this->restProxy->listBlobs($name, $options);
        $this->assertCount(1, $result->getBlobs());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::commitBlobBlocks
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::toXml
     */
    public function testCommitBlobBlocks()
    {
        // Setup
        $name = 'commitblobblocks' . $this->createSuffix();
        $blob = 'myblob';
        $id1 = 'AAAAAA==';
        $id2 = 'ANAAAA==';
        $this->createContainer($name);
        $this->restProxy->createBlobBlock($name, $blob, $id1, 'Hello world');
        $this->restProxy->createBlobBlock($name, $blob, $id2, 'Hello world');
        $blockList = new BlockList();
        
        $blockList->addEntry($id1, BlobBlockType::LATEST_TYPE);
        $blockList->addEntry($id2, BlobBlockType::LATEST_TYPE);
        
        // Test
        $this->restProxy->commitBlobBlocks($name, $blob, $blockList);
        
        // Assert
        $result = $this->restProxy->listBlobs($name);
        $this->assertCount(1, $result->getBlobs());
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::commitBlobBlocks
     * @covers MicrosoftAzure\Storage\Blob\Models\BlockList::toXml
     */
    public function testCommitBlobBlocksWithArray()
    {
        // Setup
        $name = 'commitblobblockswitharray' . $this->createSuffix();
        $blob = 'myblob';
        $id1 = 'AAAAAA==';
        $id2 = 'ANAAAA==';
        $block1 = new Block();
        $block1->setBlockId($id1);
        $block1->setType(BlobBlockType::LATEST_TYPE);
        $block2 = new Block();
        $block2->setBlockId($id2);
        $block2->setType(BlobBlockType::LATEST_TYPE);
        $blockList = array($block1, $block2);
        $this->createContainer($name);
        $this->restProxy->createBlobBlock($name, $blob, $id1, 'Hello world');
        $this->restProxy->createBlobBlock($name, $blob, $id2, 'Hello world');
        
        // Test
        $this->restProxy->commitBlobBlocks($name, $blob, $blockList);

        // Assert
        $result = $this->restProxy->listBlobs($name);
        $this->assertCount(1, $result->getBlobs());
    }
     
   /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobBlocks
    * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::create
    * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getContentLength
    * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getUncommittedBlocks
    * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::getCommittedBlocks
    */
    public function testListBlobBlocks()
    {
        // Setup
        $name = 'listblobblocks' . $this->createSuffix();
        $blob = 'myblob';
        $id1 = 'AAAAAA==';
        $id2 = 'ANAAAA==';
        $this->createContainer($name);
        $this->restProxy->createBlobBlock($name, $blob, $id1, 'Hello world');
        $this->restProxy->createBlobBlock($name, $blob, $id2, 'Hello world');
 
        // Test
        $result = $this->restProxy->listBlobBlocks($name, $blob);
 
        // Assert
        $this->assertNull($result->getETag());
        $this->assertEquals(0, $result->getContentLength());
        $this->assertCount(2, $result->getUncommittedBlocks());
        $this->assertCount(0, $result->getCommittedBlocks());
    }
      
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::listBlobBlocks
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::create
     * @covers MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksResult::_getEntries
     */
    public function testListBlobBlocksEmpty()
    {
        // Setup
        $name = 'listblobblocksempty' . $this->createSuffix();
        $blob = 'myblob';
        $content = 'Hello world';
        $this->createContainer($name);
        $this->restProxy->createBlockBlob($name, $blob, $content);
        
        // Test
        $result = $this->restProxy->listBlobBlocks($name, $blob);
        
        // Assert
        $this->assertNotNull($result->getETag());
        $this->assertEquals(strlen($content), $result->getContentLength());
        $this->assertCount(0, $result->getUncommittedBlocks());
        $this->assertCount(0, $result->getCommittedBlocks());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::copyBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getCopyBlobSourceName
     */
    public function testCopyBlobDifferentContainer()
    {
        // Setup
        $sourceContainerName = 'copyblobdiffcontainerssource' . $this->createSuffix();
        $sourceBlobName = 'sourceblob';
        $blobValue = 'testBlobValue';
        $destinationContainerName = 'copyblobdiffcontainersdestination' . $this->createSuffix();
        $destinationBlobName = 'destinationblob';
        $this->createContainer($sourceContainerName);
        $this->createContainer($destinationContainerName);
        $this->restProxy->createBlockBlob(
            $sourceContainerName,
            $sourceBlobName,
            $blobValue
        );
        
        // Test
        $result = $this->restProxy->copyBlob(
            $destinationContainerName,
            $destinationBlobName,
            $sourceContainerName,
            $sourceBlobName
        );
        $copyId = $result->getCopyId();
        $copyStatus = $result->getCopyStatus();
        
        // Assert
        $this->assertNotNull($copyId);
        $this->assertNotNull($copyStatus);

        $sourceBlob = $this->restProxy->getBlob($sourceContainerName, $sourceBlobName);
        $destinationBlob = $this->restProxy->getBlob($destinationContainerName, $destinationBlobName);
        $sourceBlobContent = stream_get_contents($sourceBlob->getContentStream());
        $destinationBlobContent =
            stream_get_contents($destinationBlob->getContentStream());
        
        $this->assertEquals($sourceBlobContent, $destinationBlobContent);
        $this->assertNotNull($result->getETag());
        $this->assertInstanceOf('\DateTime', $result->getlastModified());

        $destinationBlobProperties =
            $this->restProxy->getBlobProperties($destinationContainerName, $destinationBlobName);
        $copyState = $destinationBlobProperties->getProperties()->getCopyState();

        $this->assertNotNull($copyState);
        $this->assertNotNull($copyState->getCopyId());
        $this->assertNotNull($copyState->getCompletionTime());
        $this->assertNotNull($copyState->getStatus());
        $this->assertNotNull($copyState->getSource());
        $this->assertNotNull($copyState->getBytesCopied());
        $this->assertNotNull($copyState->getTotalBytes());

        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setIncludeCopy(true);
        $listedDestinationBlobs = $this->restProxy->listBlobs($destinationContainerName, $listBlobsOptions);
        
        $destBlob = $listedDestinationBlobs->getBlobs()[0];
        $copyState = $destBlob->getProperties()->getCopyState();

        $this->assertNotNull($copyState);
        $this->assertNotNull($copyState->getCopyId());
        $this->assertNotNull($copyState->getCompletionTime());
        $this->assertNotNull($copyState->getStatus());
        $this->assertNotNull($copyState->getSource());
        $this->assertNotNull($copyState->getBytesCopied());
        $this->assertNotNull($copyState->getTotalBytes());

        try {
            $this->restProxy->abortCopy($destinationContainerName, $destinationBlobName, $copyId);
        } catch (ServiceException $e) {
            $this->assertEquals(409, $e->getCode());
            $this->assertContains('There is currently no pending copy operation.', $e->getErrorText());
        }
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::copyBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getCopyBlobSourceName
     */
    public function testCopyBlobSameContainer()
    {
        // Setup
        $containerName = 'copyblobsamecontainer' . $this->createSuffix();
        $sourceBlobName = 'sourceblob';
        $blobValue = 'testBlobValue';
        $destinationBlobName = 'destinationblob';
        $this->createContainer($containerName);
        $this->restProxy->createBlockBlob(
            $containerName,
            $sourceBlobName,
            $blobValue
        );
        
        // Test
        $this->restProxy->copyBlob(
            $containerName,
            $destinationBlobName,
            $containerName,
            $sourceBlobName
        );
        
        // Assert
        $sourceBlob = $this->restProxy->getBlob($containerName, $sourceBlobName);
        $destinationBlob = $this->restProxy->getBlob(
            $containerName,
            $destinationBlobName
        );

        $sourceBlobContent =
            stream_get_contents($sourceBlob->getContentStream());
        $destinationBlobContent =
            stream_get_contents($destinationBlob->getContentStream());
        $this->assertEquals($sourceBlobContent, $destinationBlobContent);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::copyBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getCopyBlobSourceName
     */
    public function testCopyBlobExistingBlob()
    {
        // Setup
        $containerName = 'copyblobexistingblob' . $this->createSuffix();
        $sourceBlobName = 'sourceblob';
        $blobValue = 'testBlobValue';
        $oldBlobValue = 'oldBlobValue';
        $destinationBlobName = 'destinationblob';
        $this->createContainer($containerName);
        $this->restProxy->createBlockBlob(
            $containerName,
            $sourceBlobName,
            $blobValue
        );
        $this->restProxy->createBlockBlob(
            $containerName,
            $destinationBlobName,
            $oldBlobValue
        );
        
        // Test
        $this->restProxy->copyBlob(
            $containerName,
            $destinationBlobName,
            $containerName,
            $sourceBlobName
        );
        
        // Assert
        $sourceBlob = $this->restProxy->getBlob($containerName, $sourceBlobName);
        $destinationBlob = $this->restProxy->getBlob($containerName, $destinationBlobName);
        $sourceBlobContent = stream_get_contents($sourceBlob->getContentStream());
        $destinationBlobContent =
            stream_get_contents($destinationBlob->getContentStream());
        
        $this->assertEquals($sourceBlobContent, $destinationBlobContent);
        $this->assertNotEquals($destinationBlobContent, $oldBlobValue);
    }
    
    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::copyBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::_getCopyBlobSourceName
     */
    public function testCopyBlobSnapshot()
    {
        // Setup
        $containerName = 'copyblobsnapshot' . $this->createSuffix();
        $sourceBlobName = 'sourceblob';
        $blobValue = 'testBlobValue';
        $destinationBlobName = 'destinationblob';
        $this->createContainer($containerName);
        $this->restProxy->createBlockBlob($containerName, $sourceBlobName, $blobValue);
        $snapshotResult = $this->restProxy->createBlobSnapshot($containerName, $sourceBlobName);
        $options = new CopyBlobOptions();
        $options->setSourceSnapshot($snapshotResult->getSnapshot());
        
        // Test
        $this->restProxy->copyBlob(
            $containerName,
            $destinationBlobName,
            $containerName,
            $sourceBlobName,
            $options
        );
        
        // Assert
        $sourceBlob = $this->restProxy->getBlob($containerName, $sourceBlobName);
        $destinationBlob = $this->restProxy->getBlob($containerName, $destinationBlobName);
        $sourceBlobContent = stream_get_contents($sourceBlob->getContentStream());
        $destinationBlobContent =
            stream_get_contents($destinationBlob->getContentStream());
        
        $this->assertEquals($sourceBlobContent, $destinationBlobContent);
    }
    
   /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobSnapshot
    * @covers MicrosoftAzure\Storage\Blob\Models\createBlobSnapshotResult::create
    */
    public function testCreateBlobSnapshot()
    {
        // Setup
        $containerName = 'createblobsnapshot' . $this->createSuffix();
        $blobName = 'testBlob';
        $blobValue = 'TestBlobValue';
        $this->createContainer($containerName);
        $this->restProxy->createBlockBlob($containerName, $blobName, $blobValue);
        
        // Test
        $snapshotResult = $this->restProxy->createBlobSnapshot($containerName, $blobName);
        
        // Assert
        $listOptions = new ListBlobsOptions();
        $listOptions->setIncludeSnapshots(true);
        $blobsResult = $this->restProxy->listBlobs($containerName, $listOptions);
        $blobs = $blobsResult->getBlobs();
        $actualBlob = $blobs[0];
        $this->assertNotNull($snapshotResult->getETag());
        $this->assertNotNull($snapshotResult->getLastModified());
        $this->assertNotNull($snapshotResult->getSnapshot());
        $this->assertEquals($snapshotResult->getSnapshot(), $actualBlob->getSnapshot());
    }

    /**
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     */
    public function testSingleBlobUploadZeroBytes()
    {
        // Bug reported for zero byte upload similar to unix touch command failing
        $name = 'createemptyblob' . $this->createSuffix();
        $blob = 'EmptyFile';
        $this->createContainer($name);
        $contentType = 'text/plain; charset=UTF-8';
        $content = "";
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, $blob, $content, $options);
    
        // Now see if we can pick thje file back up.
        $result = $this->restProxy->getBlob($name, $blob);
    
        // Assert
        $this->assertEquals($content, stream_get_contents($result->getContentStream()));
    }
    
    /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::getSingleBlobUploadThresholdInBytes
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::setSingleBlobUploadThresholdInBytes
    */
    public function testSingleBlobUploadThresholdInBytes()
    {
        // Values based on http://msdn.microsoft.com/en-us/library/microsoft.windowsazure.storageclient.cloudblobclient.singleblobuploadthresholdinbytes.aspx
        // Read initial value
        $this->assertEquals($this->restProxy->getSingleBlobUploadThresholdInBytes(), 33554432);

        // Change value
        $this->restProxy->setSingleBlobUploadThresholdInBytes(50);
        $this->assertEquals($this->restProxy->getSingleBlobUploadThresholdInBytes(), 50);

        // Test over limit
        $this->restProxy->setSingleBlobUploadThresholdInBytes(65*1024*1024);
        // Should be truncated to 64M
        $this->assertEquals($this->restProxy->getSingleBlobUploadThresholdInBytes(), 67108864);

        // Under limit
        $this->restProxy->setSingleBlobUploadThresholdInBytes(-50);
        // value can not be less than 1, so reset to default value
        $this->assertEquals($this->restProxy->getSingleBlobUploadThresholdInBytes(), 33554432);

        $this->restProxy->setSingleBlobUploadThresholdInBytes(0);
        // value can not be less than 1, so reset to default value
        $this->assertEquals($this->restProxy->getSingleBlobUploadThresholdInBytes(), 33554432);
    }

   /**
    * @covers MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
    **/
    public function testCreateBlobLargerThanSingleBlock()
    {
        // First step, lets set the value for automagic splitting to somethign very small
        $max_size = 50;
        $this->restProxy->setSingleBlobUploadThresholdInBytes($max_size);
        $this->assertEquals($this->restProxy->getSingleBlobUploadThresholdInBytes(), $max_size);
        $name = 'createbloblargerthansingleblock' . $this->createSuffix();
        $this->createContainer($name);
        $contentType = 'text/plain; charset=UTF-8';
        $content = "This is a really long section of text needed for this test.";
        // Note this grows fast, each loop doubles the last run. Do not make too big
        // This results in a 1888 byte string, divided by 50 results in 38 blocks
        for ($i = 0; $i < 5; $i++) {
            $content .= $content;
        }
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $this->restProxy->createBlockBlob($name, 'little_split', $content, $options);

        // Block specific
        $boptions = new ListBlobBlocksOptions();
        $boptions->setIncludeUncommittedBlobs(true);
        $boptions->setIncludeCommittedBlobs(true);
        $result = $this->restProxy->listBlobBlocks($name, 'little_split', $boptions);
        $blocks = $result->getUnCommittedBlocks();
        $this->assertEquals(count($blocks), 0);
        $blocks = $result->getCommittedBlocks();
        $this->assertEquals(count($blocks), \ceil(strlen($content) / $max_size));
    
        // Setting back to default value for one shot test
        $this->restProxy->setSingleBlobUploadThresholdInBytes(0);
        $this->restProxy->createBlockBlob($name, 'oneshot', $content, $options);
        $result = $this->restProxy->listBlobBlocks($name, 'oneshot', $boptions);
        $blocks = $result->getUnCommittedBlocks();
        $this->assertEquals(count($blocks), 0);
        $blocks = $result->getCommittedBlocks();
        // this one doesn't make sense. On emulator, there is no block created,
        // so relying on content size to be final approval
        $this->assertEquals(count($blocks), 0);
        $this->assertEquals($result->getContentLength(), strlen($content));
    
        // make string even larger for automagic splitting
        // This should result in a string longer than 32M, and force the blob into 2 blocks
        for ($i = 0; $i < 15; $i++) {
            $content .= $content;
        }
        $this->restProxy->createBlockBlob($name, 'bigsplit', $content, $options);
        $result = $this->restProxy->listBlobBlocks($name, 'bigsplit', $boptions);
        $blocks = $result->getUnCommittedBlocks();
        $this->assertEquals(count($blocks), 0);
        $blocks = $result->getCommittedBlocks();
        $this->assertEquals(count($blocks), ceil(strlen($content)/(4*1024*1024)));
    }

    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::saveBlobToFile
     */
    public function testGetBlockBlobToFile()
    {
        // Setup
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $contentStream = 'Hello world';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->createBlockBlob(
            $name,
            $blob,
            $contentStream,
            $options
        );

        //get current working directory for the path to download
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        
        // Test
        $result = $this->restProxy->saveBlobToFile($path, $name, $blob);
        $contents = file_get_contents($path);
        
        // Assert
        $this->assertEquals(BlobType::BLOCK_BLOB, $result->getProperties()->getBlobType());
        $this->assertEquals($metadata, $result->getMetadata());
        $this->assertEquals($contentStream, $contents);

        // Delete file after assertion.
        unlink($path);
    }

    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::saveBlobToFile
     */
    public function testGetPageBlobToFile()
    {
        // Setup
        $name = 'createblobpages' . $this->createSuffix();
        $blob = 'myblob';
        $length = 512;
        $range = new PageRange(0, 511);
        $content = Resources::EMPTY_STRING;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, $length);
        for ($i = 0; $i < 512; $i++) {
            $content .= 'A';
        }
        
        $actual = $this->restProxy->createBlobPages($name, $blob, $range, $content);
        //get current working directory for the path to download
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';

        // Test
        $result = $this->restProxy->saveBlobToFile($path, $name, $blob);
        $contents = file_get_contents($path);

        // Assert
        $this->assertEquals(
            BlobType::PAGE_BLOB,
            $result->getProperties()->getBlobType()
        );
        $this->assertEquals($content, $contents);
        unlink($path);
    }

    /**
     * @group large-scale
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testPutGetLargeBlockBlob()
    {
        // Setup
        //create a temp file that is 2GB in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        $count = 2 * 1024 / 4;
        for ($index = 0; $index < $count; ++$index) {
            fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4));
        }
        rewind($resource);
        //upload the blob
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->createBlockBlob(
            $name,
            $blob,
            $resource,
            $options
        );

        // Test
        $result = $this->restProxy->getBlob($name, $blob);

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $downloadResource = fopen($downloadPath, 'w');
        //download the file
        $content = $result->getContentStream();

        while (!feof($content)) {
            fwrite(
                $downloadResource,
                stream_get_contents($content, Resources::MB_IN_BYTES_4)
            );
        }

        // Assert
        $this->assertEquals(
            BlobType::BLOCK_BLOB,
            $result->getProperties()->getBlobType()
        );
        $this->assertEquals($metadata, $result->getMetadata());
        $originMd5 = md5_file($path);
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        //clean-up.
        if (is_resource($resource)) {
            fclose($resource);
        }
        fclose($downloadResource);
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testPutGet32MBBlockBlob()
    {
        // Setup
        //create a temp file that is 32 in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_32));
        rewind($resource);
        //upload the blob
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->createBlockBlob(
            $name,
            $blob,
            $resource,
            $options
        );

        // Test
        $result = $this->restProxy->getBlob($name, $blob);

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $downloadResource = fopen($downloadPath, 'w');
        //download the file
        $content = $result->getContentStream();

        while (!feof($content)) {
            fwrite(
                $downloadResource,
                stream_get_contents($content, Resources::MB_IN_BYTES_4)
            );
        }

        // Assert
        $this->assertEquals(
            BlobType::BLOCK_BLOB,
            $result->getProperties()->getBlobType()
        );
        $this->assertEquals($metadata, $result->getMetadata());
        $originMd5 = md5_file($path);
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        //clean-up.
        if (is_resource($resource)) {
            fclose($resource);
        }
        fclose($downloadResource);
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testPutGet33MBBlockBlob()
    {
        // Setup
        //create a temp file that is 32 in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_32));
        fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_1));
        rewind($resource);
        //upload the blob
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->createBlockBlob(
            $name,
            $blob,
            $resource,
            $options
        );

        // Test
        $result = $this->restProxy->getBlob($name, $blob);

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $downloadResource = fopen($downloadPath, 'w');
        //download the file
        $content = $result->getContentStream();

        while (!feof($content)) {
            fwrite(
                $downloadResource,
                stream_get_contents($content, Resources::MB_IN_BYTES_4)
            );
        }

        // Assert
        $this->assertEquals(
            BlobType::BLOCK_BLOB,
            $result->getProperties()->getBlobType()
        );
        $this->assertEquals($metadata, $result->getMetadata());
        $originMd5 = md5_file($path);
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        //clean-up.
        if (is_resource($resource)) {
            fclose($resource);
        }
        fclose($downloadResource);
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testPutGet64MBBlockBlob()
    {
        // Setup
        //create a temp file that is 32 in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4));
        rewind($resource);
        //upload the blob
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->setSingleBlobUploadThresholdInBytes(Resources::MB_IN_BYTES_64);
        $this->restProxy->createBlockBlob(
            $name,
            $blob,
            $resource,
            $options
        );
        $this->restProxy->setSingleBlobUploadThresholdInBytes(Resources::MB_IN_BYTES_32);

        // Test
        $result = $this->restProxy->getBlob($name, $blob);

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $downloadResource = fopen($downloadPath, 'w');
        //download the file
        $content = $result->getContentStream();

        while (!feof($content)) {
            fwrite(
                $downloadResource,
                stream_get_contents($content, Resources::MB_IN_BYTES_4)
            );
        }

        // Assert
        $this->assertEquals(
            BlobType::BLOCK_BLOB,
            $result->getProperties()->getBlobType()
        );
        $this->assertEquals($metadata, $result->getMetadata());
        $originMd5 = md5_file($path);
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        //clean-up.
        if (is_resource($resource)) {
            fclose($resource);
        }
        fclose($downloadResource);
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testPutGet65MBBlockBlob()
    {
        // Setup
        //create a temp file that is 32 in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_64));
        fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_1));
        rewind($resource);
        //upload the blob
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->setSingleBlobUploadThresholdInBytes(Resources::MB_IN_BYTES_64);
        $this->restProxy->createBlockBlob(
            $name,
            $blob,
            $resource,
            $options
        );
        $this->restProxy->setSingleBlobUploadThresholdInBytes(Resources::MB_IN_BYTES_32);

        // Test
        $result = $this->restProxy->getBlob($name, $blob);

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $downloadResource = fopen($downloadPath, 'w');
        //download the file
        $content = $result->getContentStream();

        while (!feof($content)) {
            fwrite(
                $downloadResource,
                stream_get_contents($content, Resources::MB_IN_BYTES_4)
            );
        }

        // Assert
        $this->assertEquals(
            BlobType::BLOCK_BLOB,
            $result->getProperties()->getBlobType()
        );
        $this->assertEquals($metadata, $result->getMetadata());
        $originMd5 = md5_file($path);
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        //clean-up.
        if (is_resource($resource)) {
            fclose($resource);
        }
        fclose($downloadResource);
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @group large-scale
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobPages
     */
    public function testGetLargePageBlob()
    {
        //Setup
        //create a temp file that is 2GB in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        $count = 2 * 1024 / 4;
        for ($index = 0; $index < $count; ++$index) {
            fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4));
        }
        rewind($resource);
        //upload the blob
        $name = 'createblobpages' . $this->createSuffix();
        $blob = 'myblob';
        $length = $count * Resources::MB_IN_BYTES_4;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, $length);
        //upload the blob for 4MB a chunk
        $chunkSize = Resources::MB_IN_BYTES_4;
        $uploadCount = $length / $chunkSize;
        for ($chunkIdx = 0; $chunkIdx < $uploadCount; ++$chunkIdx) {
            $range = new PageRange(
                $chunkSize * $chunkIdx,
                ($chunkSize * ($chunkIdx + 1)) - 1
            );
            $body = fread($resource, $chunkSize);
            $actual = $this->restProxy->createBlobPages(
                $name,
                $blob,
                $range,
                $body
            );
        }
        
        // Test
        $result = $this->restProxy->getBlob($name, $blob);

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $downloadResource = fopen($downloadPath, 'w');
        //download the file
        $content = $result->getContentStream();
        while (!feof($content)) {
            fwrite(
                $downloadResource,
                stream_get_contents($content, Resources::MB_IN_BYTES_4)
            );
        }
        
        // Assert
        $this->assertEquals(
            BlobType::PAGE_BLOB,
            $result->getProperties()->getBlobType()
        );
        $originMd5 = md5_file($path);
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        // Delete file after assertion.
        if (is_resource($resource)) {
            fclose($resource);
        }
        fclose($downloadResource);
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @group large-scale
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::getBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlobFromContent
     */
    public function testCreateLargePageBlobFromContent()
    {
        //Setup
        //create a temp file that is 2GB in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        $count = 2 * 1024 / 4;
        for ($index = 0; $index < $count; ++$index) {
            fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4));
        }
        rewind($resource);

        //upload the blob
        $container = 'createpageblobfromcontent' . $this->createSuffix();
        $blob = 'myblob';
        $length = $count * Resources::MB_IN_BYTES_4;
        $this->createContainer($container);
        
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);

        $createPageBlobResult = $this->restProxy->createPageBlobFromContent($container, $blob, $length, $resource, $options);

        $originMd5 = md5_file($path);
        $this->assertEquals("PageBlob", $createPageBlobResult->getProperties()->getBlobType());
        $this->assertEquals($contentType, $createPageBlobResult->getProperties()->getContentType());
        $this->assertEquals(2, count($createPageBlobResult->getMetadata()));
        $this->assertEquals($metadata["m1"], $createPageBlobResult->getMetadata()["m1"]);
        $this->assertEquals($metadata["m2"], $createPageBlobResult->getMetadata()["m2"]);

        // Test
        $result = $this->restProxy->getBlob($container, $blob);

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $downloadResource = fopen($downloadPath, 'w');

        //download the file
        $content = $result->getContentStream();
        while (!feof($content)) {
            fwrite(
                $downloadResource,
                stream_get_contents($content, Resources::MB_IN_BYTES_4)
            );
        }
        
        // Assert
        $this->assertEquals(
            BlobType::PAGE_BLOB,
            $result->getProperties()->getBlobType()
        );
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        // Delete file after assertion.
        if (is_resource($resource)) {
            fclose($resource);
        }
        fclose($downloadResource);
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @group large-scale
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::saveBlobToFile
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlockBlob
     */
    public function testGetLargeBlockBlobToFile()
    {
        // Setup
        //create a temp file that is 2GB in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        $count = 2 * 1024 / 4;
        for ($index = 0; $index < $count; ++$index) {
            fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4));
        }
        rewind($resource);
        //upload the blob
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->createBlockBlob(
            $name,
            $blob,
            $resource,
            $options
        );

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';

        // Test
        $result = $this->restProxy->saveBlobToFile($downloadPath, $name, $blob);
        
        
        // Assert
        $this->assertEquals(
            BlobType::BLOCK_BLOB,
            $result->getProperties()->getBlobType()
        );
        $this->assertEquals($metadata, $result->getMetadata());
        if (is_resource($resource)) {
            fclose($resource);
        }
        $originMd5 = md5_file($path);
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        // Delete file after assertion.
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @group large-scale
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::saveBlobToFile
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createPageBlob
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobPages
     */
    public function testGetLargePageBlobToFile()
    {
        //Setup
        //create a temp file that is 2GB in size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');
        $count = 2 * 1024 / 4;
        for ($index = 0; $index < $count; ++$index) {
            fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4));
        }
        rewind($resource);
        //upload the blob
        $name = 'createblobpages' . $this->createSuffix();
        $blob = 'myblob';
        $length = $count * Resources::MB_IN_BYTES_4;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, $length);
        //upload the blob for 4MB a chunk
        $chunkSize = Resources::MB_IN_BYTES_4;
        $uploadCount = $length / $chunkSize;
        for ($chunkIdx = 0; $chunkIdx < $uploadCount; ++$chunkIdx) {
            $range = new PageRange(
                $chunkSize * $chunkIdx,
                ($chunkSize * ($chunkIdx + 1)) - 1
            );
            $body = fread($resource, $chunkSize);
            $actual = $this->restProxy->createBlobPages(
                $name,
                $blob,
                $range,
                $body
            );
        }

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        
        // Test
        $result = $this->restProxy->saveBlobToFile($downloadPath, $name, $blob);
        
        // Assert
        $this->assertEquals(
            BlobType::PAGE_BLOB,
            $result->getProperties()->getBlobType()
        );
        if (is_resource($resource)) {
            fclose($resource);
        }
        $originMd5 = md5_file($path);
        $downloadMd5 = md5_file($downloadPath);
        $this->assertEquals($originMd5, $downloadMd5);

        // Delete file after assertion.
        unlink($path);
        unlink($downloadPath);
    }

    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobPages
     */
    public function testPageRangeCreationWithInvalidRange()
    {
        $errorMsg = '';
        //upload the blob
        $name = 'createblobpages' . $this->createSuffix();
        $blob = 'myblob';
        $length = 512;
        $this->createContainer($name);
        $this->restProxy->createPageBlob($name, $blob, $length);
        //upload the blob
        $range = new PageRange(0, 255);
        $body = openssl_random_pseudo_bytes(256);
        try {
            $actual = $this->restProxy->createBlobPages(
                $name,
                $blob,
                $range,
                $body
            );
        } catch (\RuntimeException $e) {
            $errorMsg = $e->getMessage();
        }
        $this->assertEquals($errorMsg, Resources::ERROR_RANGE_NOT_ALIGN_TO_512);
    }

    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::saveBlobToFile
     */
    public function testsaveBlobToFileWithInvalidPath()
    {
        $errorMsg = '';
        //Create a random string that is 8MB in size.
        $contentStr = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4 * 2);
        //upload the blob
        $name = 'getblob' . $this->createSuffix();
        $blob = 'myblob';
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $this->createContainer($name);
        $options = new CreateBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $this->restProxy->createBlockBlob(
            $name,
            $blob,
            $contentStr,
            $options
        );
        // Test
        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = 'Zasdf:\\\\\\\\Invalid.PATH'.$uuid.'.txt';
        error_reporting(E_ALL ^ E_WARNING);
        try {
            $result = $this->restProxy->saveBlobToFile($downloadPath, $name, $blob);
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
        } finally {
            error_reporting(E_ALL);
        }
        $this->assertEquals($errorMsg, Resources::ERROR_FILE_COULD_NOT_BE_OPENED);
    }
    
    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::saveBlobToFile
     */
    public function testsaveBlobToFileWithBlobNotExist()
    {
        $errorMsg = '';
        $name = 'getblob' . $this->createSuffix();
        $blob = 'non_existing_blob';
        $this->createContainer($name);
        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = getcwd().DIRECTORY_SEPARATOR.$uuid.'.txt';
        try {
            $result = $this->restProxy->saveBlobToFile($downloadPath, $name, $blob);
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
        }

        $this->assertTrue(strpos($errorMsg, Resources::ERROR_BLOB_NOT_EXIST) != 0);

        if (file_exists($downloadPath)) {
            unlink($downloadPath);
        }
    }
    
    /**
     * @covers \MicrosoftAzure\Storage\Blob\BlobRestProxy::saveBlobToFile
     */
    public function testsaveBlobToFileWithContainerNotExist()
    {
        $errorMsg = '';
        $name = 'nonexistingcontainer';
        $blob = 'non_existing_blob';
        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = getcwd().DIRECTORY_SEPARATOR.$uuid.'.txt';
        try {
            $result = $this->restProxy->saveBlobToFile($downloadPath, $name, $blob);
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
        }
        $this->assertTrue(strpos($errorMsg, Resources::ERROR_CONTAINER_NOT_EXIST) != 0);

        if (file_exists($downloadPath)) {
            unlink($downloadPath);
        }
    }

    /**
     * @covers  \MicrosoftAzure\Storage\Blob\BlobeRestProxy::addOptionalAccessConditionHeader
     */
    public function testAddOptionalAccessContitionHeader()
    {
        // Setup
        $expectedHeader = Resources::IF_MATCH;
        $expectedValue = '0x8CAFB82EFF70C46';
        $accessCondition = AccessCondition::ifMatch($expectedValue);
        $headers = array('Header1' => 'Value1', 'Header2' => 'Value2');

        // Test
        $actual = $this->restProxy->addOptionalAccessConditionHeader($headers, [$accessCondition]);

        // Assert
        $this->assertCount(3, $actual);
        $this->assertEquals($expectedValue, $actual[$expectedHeader]);
    }

    /**
     * @covers  \MicrosoftAzure\Storage\Blob\BlobRestProxy::addOptionalSourceAccessConditionHeader
     */
    public function testAddOptionalSourceAccessContitionHeader()
    {
        // Setup
        $expectedHeader = Resources::X_MS_SOURCE_IF_MATCH;
        $expectedValue = '0x8CAFB82EFF70C46';
        $accessCondition = AccessCondition::ifMatch($expectedValue);
        $headers = array('Header1' => 'Value1', 'Header2' => 'Value2');

        // Test
        $actual = $this->restProxy->addOptionalSourceAccessConditionHeader($headers, [$accessCondition]);

        // Assert
        $this->assertCount(3, $actual);
        $this->assertEquals($expectedValue, $actual[$expectedHeader]);
    }

    /**
     * @covers  \MicrosoftAzure\Storage\Blob\BlobRestProxy::getServiceStats
     * @covers  \MicrosoftAzure\Storage\Blob\BlobRestProxy::getServiceStatsAsync
     */
    public function testGetServiceStats()
    {
        $result = $this->restProxy->getServiceStats();

        // Assert
        $this->assertNotNull($result->getStatus());
        $this->assertNotNull($result->getLastSyncTime());
        $this->assertTrue($result->getLastSyncTime() instanceof \DateTime);
    }
}
