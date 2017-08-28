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
 * @package   MicrosoftAzure\Storage\Tests\Unit\File
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\unit\File;

use MicrosoftAzure\Storage\Tests\Framework\VirtualFileSystem;
use MicrosoftAzure\Storage\Tests\Framework\FileServiceRestProxyTestBase;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\File\Models\AppendBlockOptions;
use MicrosoftAzure\Storage\File\Models\ListSharesOptions;
use MicrosoftAzure\Storage\File\Models\ListSharesResult;
use MicrosoftAzure\Storage\File\Models\CreateShareOptions;
use MicrosoftAzure\Storage\File\Models\GetSharePropertiesResult;
use MicrosoftAzure\Storage\File\Models\ShareAcl;
use MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesResult;
use MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesOptions;
use MicrosoftAzure\Storage\File\Models\ListFileBlocksOptions;
use MicrosoftAzure\Storage\File\Models\CreateFileOptions;
use MicrosoftAzure\Storage\File\Models\CreateDirectoryOptions;
use MicrosoftAzure\Storage\File\Models\SetFilePropertiesOptions;
use MicrosoftAzure\Storage\File\Models\GetFileMetadataResult;
use MicrosoftAzure\Storage\File\Models\SetFileMetadataResult;
use MicrosoftAzure\Storage\File\Models\GetFileResult;
use MicrosoftAzure\Storage\File\Models\FileType;
use MicrosoftAzure\Storage\File\Models\PageRange;
use MicrosoftAzure\Storage\File\Models\CreateFilePagesResult;
use MicrosoftAzure\Storage\File\Models\BlockList;
use MicrosoftAzure\Storage\File\Models\FileBlockType;
use MicrosoftAzure\Storage\File\Models\GetFileOptions;
use MicrosoftAzure\Storage\File\Models\Block;
use MicrosoftAzure\Storage\File\Models\CopyFileOptions;
use MicrosoftAzure\Storage\File\Models\FileProperties;

/**
 * Unit tests for class FileRestProxy
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Tests\Unit\File
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class FileRestProxyTest extends FileServiceRestProxyTestBase
{
    private function createSuffix()
    {
        return sprintf('-%04x', mt_rand(0, 65535));
    }

    /**
    * @covers MicrosoftAzure\Storage\File\FileRestProxy::getServiceProperties
    * @covers MicrosoftAzure\Storage\File\FileRestProxy::setServiceProperties
    */
    public function testSetServiceProperties()
    {
        $this->skipIfEmulated();
        
        // Setup
        $expected = ServiceProperties::create(TestResources::setFileServicePropertiesSample());
        
        // Test
        $this->setServiceProperties($expected);
        //Add 30s interval to wait for setting to take effect.
        \sleep(30);
        $actual = $this->restProxy->getServiceProperties();
        
        // Assert
        $this->assertEquals($expected->toXml($this->xmlSerializer), $actual->getValue()->toXml($this->xmlSerializer));
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listShares
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listSharesAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     */
    public function testCreateListShare()
    {
        $share1 = 'mysharessimple1' . $this->createSuffix();
        $share2 = 'mysharessimple2' . $this->createSuffix();
        $share3 = 'mysharessimple3' . $this->createSuffix();

        $this->createShare($share1);
        $this->createShare($share2);
        $this->createShare($share3);

        $result = $this->restProxy->listShares();

        //Assert
        $shares = $result->getShares();
        $shareNames = array();
        foreach ($shares as $share) {
            $shareNames[] = $share->getName();
        }
        $this->assertTrue(\in_array($share1, $shareNames));
        $this->assertTrue(\in_array($share2, $shareNames));
        $this->assertTrue(\in_array($share3, $shareNames));
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getShareMetadata
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getShareMetadataAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setShareMetadata
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setShareMetadataAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getShareProperties
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getSharePropertiesAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setShareProperties
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setSharePropertiesAsync
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage 400
     */
    public function testGetSetShareMetadataAndProperties()
    {
        $share1 = 'metaproperties1' . $this->createSuffix();
        $share2 = 'metaproperties2' . $this->createSuffix();
        $share3 = 'metaproperties3' . $this->createSuffix();

        $this->createShare($share1);
        $this->createShare($share2);
        $this->createShare($share3);

        $expected1 = array('name1' => 'MyName1', 'mymetaname' => '12345');
        $expected2 = 5120;
        $expected3 = 5121;

        $this->restProxy->setShareMetadata($share1, $expected1);
        $this->restProxy->setShareProperties($share2, $expected2);

        $result1 = $this->restProxy->getShareMetadata($share1);
        $result2 = $this->restProxy->getShareProperties($share2);

        $this->assertEquals($expected1, $result1->getMetadata());
        $this->assertEquals(5120, $result2->getQuota());

        $this->restProxy->setShareProperties($share3, $expected3);
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getShareAcl
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getShareAclAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setShareAcl
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setShareAclAsync
     */
    public function testGetSetShareAcl()
    {
        $share = 'shareacl' . $this->createSuffix();
        $this->createShare($share);
        $sample = TestResources::getShareAclMultipleEntriesSample();
        $expectedETag = '0x8CAFB82EFF70C46';
        $expectedLastModified = new \DateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $acl = ShareACL::create($sample['SignedIdentifiers']);

        // Test
        $this->restProxy->setShareAcl($share, $acl);
        
        // Assert
        $actual = $this->restProxy->getShareAcl($share);
        $this->assertEquals($acl->getSignedIdentifiers(), $actual->getShareAcl()->getSignedIdentifiers());
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getShareStats
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getShareStatsAsync
     */
    public function testGetShareStats()
    {
        $share = 'sharestats' . $this->createSuffix();
        $this->createShare($share);

        $result = $this->restProxy->getShareStats($share);

        $this->assertEquals(0, $result->getShareUsage());
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listDirectoriesAndFiles
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage can't be NULL.
     */
    public function testListDirectoriesAndFilesWithNull()
    {
        $this->restProxy->listDirectoriesAndFiles(null);
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listDirectoriesAndFiles
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listDirectoriesAndFilesAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createDirectory
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createDirectoryAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     */
    public function testListDirectoriesAndFiles()
    {
        $share = 'listdirectoriesandfiles' . $this->createSuffix();
        $this->createShare($share);

        $testdirectory0 = 'testdirectory0';
        $testdirectory1 = $testdirectory0 . '/' . 'testdirectory1';
        $testdirectory2 = $testdirectory0 . '/' . 'testdirectory2';
        $testfile0      = 'testfile0';
        $testfile1      = $testdirectory0 . '/' . 'testfile1';
        $testfile2      = $testdirectory1 . '/' . 'testfile2';
        $testfile3      = $testdirectory1 . '/' . 'testfile3';
        $testfile4      = $testdirectory1 . '/' . 'testfile4';
        $testfile5      = $testdirectory1 . '/' . 'testfile5';
        $testfile6      = $testdirectory1 . '/' . 'testfile6';
        $testfile7      = $testdirectory1 . '/' . 'testfile7';

        $this->restProxy->createDirectory($share, $testdirectory0);
        $this->restProxy->createDirectory($share, $testdirectory1);
        $this->restProxy->createDirectory($share, $testdirectory2);

        $this->restProxy->createFile(
            $share,
            $testfile0,
            Resources::MB_IN_BYTES_4
        );
        $this->restProxy->createFile(
            $share,
            $testfile1,
            Resources::MB_IN_BYTES_4
        );
        $this->restProxy->createFile(
            $share,
            $testfile2,
            Resources::MB_IN_BYTES_4
        );
        $this->restProxy->createFile(
            $share,
            $testfile3,
            Resources::MB_IN_BYTES_4
        );
        $this->restProxy->createFile(
            $share,
            $testfile4,
            Resources::MB_IN_BYTES_4
        );
        $this->restProxy->createFile(
            $share,
            $testfile5,
            Resources::MB_IN_BYTES_4
        );
        $this->restProxy->createFile(
            $share,
            $testfile6,
            Resources::MB_IN_BYTES_4
        );
        $this->restProxy->createFile(
            $share,
            $testfile7,
            Resources::MB_IN_BYTES_4
        );

        $result = $this->restProxy->listDirectoriesAndFiles($share);
        $result0 = $this->restProxy->listDirectoriesAndFiles($share, $testdirectory0);
        $result1 = $this->restProxy->listDirectoriesAndFiles($share, $testdirectory1);
        $result2 = $this->restProxy->listDirectoriesAndFiles($share, $testdirectory2);

        $validator = function ($resources, $target) {
            $result = false;
            foreach ($resources as $resource) {
                if ($resource->getName() == $target) {
                    $result = true;
                    break;
                }
            }
            return $result;
        };

        $this->assertTrue($validator($result->getDirectories(), 'testdirectory0'));
        $this->assertTrue($validator($result->getFiles(), 'testfile0'));
        $this->assertTrue($validator($result0->getDirectories(), 'testdirectory1'));
        $this->assertTrue($validator($result0->getDirectories(), 'testdirectory2'));
        $this->assertTrue($validator($result0->getFiles(), 'testfile1'));
        $this->assertTrue($validator($result1->getFiles(), 'testfile2'));
        $this->assertTrue($validator($result1->getFiles(), 'testfile3'));
        $this->assertTrue($validator($result1->getFiles(), 'testfile4'));
        $this->assertTrue($validator($result1->getFiles(), 'testfile5'));
        $this->assertTrue($validator($result1->getFiles(), 'testfile6'));
        $this->assertTrue($validator($result1->getFiles(), 'testfile7'));
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listDirectoriesAndFiles
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listDirectoriesAndFilesAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createDirectory
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createDirectoryAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteDirectory
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteDirectoryAsync
     */
    public function testCreateDeleteDirectory()
    {
        $share = 'createdeletedirectory' . $this->createSuffix();
        $this->createShare($share);

        $this->createDirectory($share, 'testdirectory0');
        $this->createDirectory($share, 'testdirectory0/testdirectory00');
        $this->createDirectory($share, 'testdirectory0/testdirectory01');
        $this->createDirectory($share, 'testdirectory1');
        $this->createDirectory($share, 'testdirectory1/testdirectory10');
        $this->createDirectory($share, 'testdirectory0/testdirectory00/testdirectory000');

        $result = $this->restProxy->listDirectoriesAndFiles($share);

        $validator = function ($directories, $target) {
            $result = false;
            foreach ($directories as $directory) {
                if ($directory->getName() == $target) {
                    $result = true;
                    break;
                }
            }
            return $result;
        };

        $this->assertTrue($validator($result->getDirectories(), 'testdirectory1'));
        $this->assertTrue($validator($result->getDirectories(), 'testdirectory0'));
        $result = $this->restProxy->listDirectoriesAndFiles(
            $share,
            'testdirectory0'
        );
        $this->assertTrue($validator($result->getDirectories(), 'testdirectory01'));
        $this->assertTrue($validator($result->getDirectories(), 'testdirectory00'));
        $result = $this->restProxy->listDirectoriesAndFiles(
            $share,
            'testdirectory1'
        );
        $this->assertTrue($validator($result->getDirectories(), 'testdirectory10'));
        $result = $this->restProxy->listDirectoriesAndFiles(
            $share,
            'testdirectory0/testdirectory00'
        );
        $this->assertTrue($validator($result->getDirectories(), 'testdirectory000'));
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createDirectory
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createDirectoryAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteDirectory
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteDirectoryAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getDirectoryProperties
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getDirectoryPropertiesAsync
     */
    public function testGetDirectoryProperties()
    {
        $share = 'getdirectoryproperties' . $this->createSuffix();
        $this->createShare($share);

        $metadata = [
            'testmeta1' => 'testmetacontent1',
            'testmeta2' => 'testmetacontent2',
            'testmeta3' => 'testmetacontent3',
            'testmeta4' => 'testmetacontent4',
            'testmeta5' => 'testmetacontent5',
            'testmeta6' => 'testmetacontent6'
        ];

        $options = new CreateDirectoryOptions();
        $options->setMetadata($metadata);

        $this->createDirectory($share, 'testdirectory', $options);

        $result = $this->restProxy->getDirectoryProperties($share, 'testdirectory');

        $actual = $result->getMetadata();

        foreach ($metadata as $key => $value) {
            $this->assertTrue(array_key_exists($key, $actual));
            $this->assertEquals($value, $actual[$key]);
        }
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createDirectory
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createDirectoryAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteDirectory
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteDirectoryAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getDirectoryMetadata
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getDirectoryMetadataAsync
     */
    public function testGetSetDirectoryMetadata()
    {
        $share = 'getdirectorymetadata' . $this->createSuffix();
        $this->createShare($share);

        $metadata = [
            'testmeta1' => 'testmetacontent1',
            'testmeta2' => 'testmetacontent2',
            'testmeta3' => 'testmetacontent3',
            'testmeta4' => 'testmetacontent4',
            'testmeta5' => 'testmetacontent5',
            'testmeta6' => 'testmetacontent6'
        ];

        $options = new CreateDirectoryOptions();
        $options->setMetadata($metadata);

        $this->createDirectory($share, 'testdirectory', $options);

        $result = $this->restProxy->getDirectoryMetadata($share, 'testdirectory');

        $actual = $result->getMetadata();

        foreach ($metadata as $key => $value) {
            $this->assertTrue(array_key_exists($key, $actual));
            $this->assertEquals($value, $actual[$key]);
        }

        $metadata = [
            'testmeta11' => 'testmetacontent11',
            'testmeta22' => 'testmetacontent22',
            'testmeta33' => 'testmetacontent33',
            'testmeta44' => 'testmetacontent44',
            'testmeta55' => 'testmetacontent55',
            'testmeta66' => 'testmetacontent66'
        ];

        $result = $this->restProxy->setDirectoryMetadata(
            $share,
            'testdirectory',
            $metadata
        );

        $result = $this->restProxy->getDirectoryMetadata($share, 'testdirectory');

        $actual = $result->getMetadata();

        foreach ($metadata as $key => $value) {
            $this->assertTrue(array_key_exists($key, $actual));
            $this->assertEquals($value, $actual[$key]);
        }
    }

    /**
    * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
    * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
    * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
    * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
    * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
    * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
    * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteFile
    * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteFileAsync
    */
    public function testCreateDeleteFile()
    {
        $share = 'createdeletefile' . $this->createSuffix();
        $this->createShare($share);

        $fileName = 'testfile';

        $this->restProxy->createFile($share, $fileName, Resources::GB_IN_BYTES);
        $result = $this->restProxy->listDirectoriesAndFiles($share, '');

        $actualFiles = $result->getFiles();

        $found = false;

        foreach ($actualFiles as $file) {
            if ($file->getName() == $fileName) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        $this->restProxy->deleteFile($share, $fileName);

        $result = $this->restProxy->listDirectoriesAndFiles($share, '');

        $actualFiles = $result->getFiles();

        $found = false;

        foreach ($actualFiles as $file) {
            if ($file->getName() == $fileName) {
                $found = true;
                break;
            }
        }

        $this->assertTrue(!$found);
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setFileProperties
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setFilePropertiesAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFileProperties
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFilePropertiesAsync
     */
    public function testGetSetFileProperties()
    {
        $share = 'getsetfileproperties' . $this->createSuffix();
        $this->createShare($share);
        $fileName = 'testfile';
        $this->restProxy->createFile($share, $fileName, Resources::GB_IN_BYTES);

        $properties = $this->restProxy->getFileProperties($share, $fileName);

        $this->assertEquals(Resources::GB_IN_BYTES, $properties->getContentLength());

        $properties->setCacheControl('no-cache');
        $properties->setContentType('pdf');
        $md5 = \md5('testString');
        $properties->setContentMD5($md5);
        $properties->setContentEncoding('gzip');
        $properties->setContentLanguage('en');
        $properties->setContentDisposition('attachment');
        $properties->setContentLength(Resources::MB_IN_BYTES_1);

        $this->restProxy->setFileProperties($share, $fileName, $properties);


        $newProperties = $this->restProxy->getFileProperties($share, $fileName);

        $this->assertEquals(
            $properties->getCacheControl(),
            $newProperties->getCacheControl()
        );
        $this->assertEquals(
            $properties->getContentType(),
            $newProperties->getContentType()
        );
        $this->assertEquals(
            $properties->getContentMD5(),
            $newProperties->getContentMD5()
        );
        $this->assertEquals(
            $properties->getContentEncoding(),
            $newProperties->getContentEncoding()
        );
        $this->assertEquals(
            $properties->getContentLanguage(),
            $newProperties->getContentLanguage()
        );
        $this->assertEquals(
            $properties->getContentDisposition(),
            $newProperties->getContentDisposition()
        );
        $this->assertEquals(
            $properties->getContentLength(),
            $newProperties->getContentLength()
        );
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setFileMetadata
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::setFileMetadataAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFileMetadata
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFileMetadataAsync
     */
    public function testGetSetFileMetadata()
    {
        $share = 'getsetfilemetadata' . $this->createSuffix();
        $this->createShare($share);
        $fileName = 'testfile';
        $this->restProxy->createFile($share, $fileName, Resources::GB_IN_BYTES);

        $metadata = [
            'testmeta11' => 'testmetacontent11',
            'testmeta22' => 'testmetacontent22',
            'testmeta33' => 'testmetacontent33',
            'testmeta44' => 'testmetacontent44',
            'testmeta55' => 'testmetacontent55',
            'testmeta66' => 'testmetacontent66'
        ];

        $this->restProxy->setFileMetadata($share, $fileName, $metadata);

        $result = $this->restProxy->getFileMetadata($share, $fileName);

        $actual = $result->getMetadata();

        foreach ($metadata as $key => $value) {
            $this->assertTrue(array_key_exists($key, $actual));
            $this->assertEquals($value, $actual[$key]);
        }
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRange
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRangeAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFileAsync
     */
    public function testPutFileRange()
    {
        $share = 'putfilerange' . $this->createSuffix();
        $this->createShare($share);
        $fileName = 'testfile';
        $this->restProxy->createFile($share, $fileName, Resources::MB_IN_BYTES_4);
        $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
        $range = new Range(0, Resources::MB_IN_BYTES_4 - 1);

        $this->restProxy->putFileRange($share, $fileName, $content, $range);

        $result = $this->restProxy->getFile($share, $fileName);

        $actual = \stream_get_contents($result->getContentStream());

        $this->assertTrue($content == $actual);
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRange
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRangeAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::clearFileRange
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::clearFileRangeAsync
     */
    public function testClearFileRange()
    {
        $share = 'clearfilerange' . $this->createSuffix();
        $this->createShare($share);
        $fileName = 'testfile';
        $this->restProxy->createFile($share, $fileName, Resources::MB_IN_BYTES_4);
        $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
        $range = new Range(0, Resources::MB_IN_BYTES_4 - 1);

        $this->restProxy->putFileRange($share, $fileName, $content, $range);

        $result = $this->restProxy->getFile($share, $fileName);

        $actual = \stream_get_contents($result->getContentStream());

        $this->assertEquals($content, $actual);

        $this->restProxy->clearFileRange($share, $fileName, $range);

        $result = $this->restProxy->getFile($share, $fileName);

        $actual = \stream_get_contents($result->getContentStream());

        $this->assertTrue(
            str_pad('', Resources::MB_IN_BYTES_4, "\0", STR_PAD_LEFT) ==
            $actual
        );
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRange
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRangeAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listFileRange
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::listFileRangeAsync
     */
    public function testListFileRange()
    {
        $share = 'listfilerange' . $this->createSuffix();
        $this->createShare($share);
        $fileName = 'testfile';
        $this->restProxy->createFile($share, $fileName, Resources::MB_IN_BYTES_4);
        $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_1);
        $range0 = new Range(0, Resources::MB_IN_BYTES_1 - 1);
        $range1 = new Range(
            Resources::MB_IN_BYTES_1 * 2,
            Resources::MB_IN_BYTES_1 * 3 - 1
        );

        $this->restProxy->putFileRange($share, $fileName, $content, $range0);
        $this->restProxy->putFileRange($share, $fileName, $content, $range1);

        $result = $this->restProxy->listFileRange($share, $fileName);

        $ranges = $result->getRanges();

        $this->assertEquals(0, $ranges[0]->getStart());
        $this->assertEquals(Resources::MB_IN_BYTES_1 - 1, $ranges[0]->getEnd());
        $this->assertEquals(Resources::MB_IN_BYTES_1 * 2, $ranges[1]->getStart());
        $this->assertEquals(Resources::MB_IN_BYTES_1 * 3 - 1, $ranges[1]->getEnd());
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRange
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRangeAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::copyFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::copyFileAsync
     */
    public function testCopyFile()
    {
        $share = 'copyfile' . $this->createSuffix();
        $this->createShare($share);
        $fileName = 'testfile';
        $this->restProxy->createFile($share, $fileName, Resources::MB_IN_BYTES_4);
        $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
        $range = new Range(0, Resources::MB_IN_BYTES_4 - 1);

        $this->restProxy->putFileRange($share, $fileName, $content, $range);
        
        $source = sprintf(
            '%s%s/%s',
            (string)$this->restProxy->getPsrPrimaryUri(),
            $share,
            $fileName
        );

        $destFileName = 'destfile';

        $metadata = [
            'testmeta11' => 'testmetacontent11',
            'testmeta22' => 'testmetacontent22',
            'testmeta33' => 'testmetacontent33',
            'testmeta44' => 'testmetacontent44',
            'testmeta55' => 'testmetacontent55',
            'testmeta66' => 'testmetacontent66'
        ];

        $this->restProxy->copyFile($share, $destFileName, $source, $metadata);

        \sleep(10);

        $result = $this->restProxy->getFile($share, $destFileName);

        $expectedContent = \stream_get_contents($result->getContentStream());
        $expectedMetadata = $result->getMetadata();

        foreach ($metadata as $key => $value) {
            $this->assertTrue(array_key_exists($key, $expectedMetadata));
            $this->assertEquals($value, $expectedMetadata[$key]);
        }

        $this->assertTrue($content == $expectedContent);
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::abortCopy
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::abortCopyAsync
     * @expectedException MicrosoftAzure\Storage\Common\Exceptions\ServiceException
     * @expectedExceptionMessage There is currently no pending copy operation
     */
    public function testAbortCopy()
    {
        $share = 'abortcopy' . $this->createSuffix();
        $this->createShare($share);
        $fileName = 'testfile';
        $this->restProxy->createFile($share, $fileName, Resources::MB_IN_BYTES_4);

        $copyID = 'af6157e2-e79b-4353-a111-87dd8720caf5';
        $this->restProxy->abortCopy($share, $fileName, $copyID);
    }

    /**
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::deleteShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShare
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createShareAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRange
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::putFileRangeAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFile
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::getFileAsync
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileFromContent
     * @covers  \MicrosoftAzure\Storage\File\FileRestProxy::createFileFromContentAsync
     */
    public function testCreateFileFromContent()
    {
        $share = 'createfilefromcontent' . $this->createSuffix();
        $this->createShare($share);
        $content0 = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
        $content1 = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
        $content1 = str_pad($content1, Resources::MB_IN_BYTES_4 * 2, "\0", STR_PAD_RIGHT);
        $content1 .= openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
        $content1 = str_pad($content1, Resources::MB_IN_BYTES_4 * 4, "\0", STR_PAD_RIGHT);
        $content2 = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4 * 4);

        $testfile0 = 'testfile0';
        $testfile1 = 'testfile1';
        $testfile2 = 'testfile2';

        $this->restProxy->createFileFromContent($share, $testfile0, $content0);
        $this->restProxy->createFileFromContent($share, $testfile1, $content1);
        $this->restProxy->createFileFromContent($share, $testfile2, $content2);

        $result = $this->restProxy->getFile($share, $testfile0);
        $actual0 = \stream_get_contents($result->getContentStream());
        $result = $this->restProxy->getFile($share, $testfile1);
        $actual1 = \stream_get_contents($result->getContentStream());
        $result = $this->restProxy->getFile($share, $testfile2);
        $actual2 = \stream_get_contents($result->getContentStream());

        $this->assertTrue($content0 == $actual0);
        $this->assertTrue($content1 == $actual1);
        $this->assertTrue($content2 == $actual2);
    }
}
