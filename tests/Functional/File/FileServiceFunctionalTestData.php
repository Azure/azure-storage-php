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
 * @package   MicrosoftAzure\Storage\Tests\Functional\File
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\File;

use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\File\Models\AccessCondition;
use MicrosoftAzure\Storage\File\Models\ShareACL;
use MicrosoftAzure\Storage\File\Models\GetFileOptions;
use MicrosoftAzure\Storage\File\Models\FileProperties;
use MicrosoftAzure\Storage\File\Models\FileServiceOptions;
use MicrosoftAzure\Storage\File\Models\CreateFileOptions;
use MicrosoftAzure\Storage\File\Models\ListSharesOptions;
use MicrosoftAzure\Storage\File\Models\CreateShareOptions;
use MicrosoftAzure\Storage\File\Models\PutFileRangeOptions;
use MicrosoftAzure\Storage\File\Models\CreateDirectoryOptions;
use MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesOptions;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\CORS;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;

class FileServiceFunctionalTestData
{
    public static $testUniqueId;
    public static $nonExistSharePrefix;
    public static $nonExistFilePrefix;
    public static $testShareNames;
    public static $trackedShareCount;
    private static $accountName;
    private static $badETag = '0x123456789ABCDEF';

    public static function setupData($accountName)
    {
        self::$accountName = $accountName;
        self::$testUniqueId = self::getRandomHexBytes(10);
        self::$nonExistSharePrefix = self::getRandomHexBytes(10) . 'nonshr';
        self::$nonExistFilePrefix = self::getRandomHexBytes(10) . 'nonfile';
        self::$testShareNames = array();
        for ($i = 0; $i < 3; ++$i) {
            self::$testShareNames[] = self::getInterestingShareName();
        }
    }

    public static function getInterestingShareName()
    {
        return self::getInterestingName('shr');
    }

    public static function getInterestingFileName()
    {
        return self::getInterestingName('file');
    }

    public static function getInterestingDirectoryName()
    {
        return self::getInterestingName('dir');
    }

    public static function getInterestingName($midfix)
    {
        $uniqid = self::getRandomHexBytes(10);
        return self::$testUniqueId . $midfix . $uniqid;
    }

    public static function getRandomHexBytes($length)
    {
        if ($length & 1 == 0) {
            return \bin2hex(self::getRandomBytes($length / 2));
        } else {
            return substr(\bin2hex(self::getRandomBytes(($length / 2) + 1)), 1);
        }
    }

    public static function getRandomBytes($length)
    {
        return \openssl_random_pseudo_bytes($length);
    }

    public static function getInterestingTimeoutValues()
    {
        $ret = array();
        array_push($ret, null);
        array_push($ret, -1);
        array_push($ret, 0);
        array_push($ret, 1);
        array_push($ret, -2147483648);
        array_push($ret, 2147483647);
        return $ret;
    }

    public static function diffInTotalSeconds($date1, $date2)
    {
        $diff = $date1->diff($date2);
        $sec = $diff->s
                + 60 * ($diff->i
                + 60 * ($diff->h
                + 24 * ($diff->d
                + 30 * ($diff->m
                + 12 * ($diff->y)))));
        return abs($sec);
    }

    public static function getDefaultServiceProperties()
    {
        // This is the default that comes from the server.
        $rp = new RetentionPolicy();

        $m = new Metrics();
        $m->setRetentionPolicy($rp);
        $m->setVersion('1.0');
        $m->setEnabled(false);
        $m->setIncludeAPIs(null);

        $sp = new ServiceProperties();
        $sp->setHourMetrics($m);

        return $sp;
    }

    public static function getInterestingServiceProperties()
    {
        $ret = array();

        {
            // This is the default that comes from the server.
            array_push($ret, self::getDefaultServiceProperties());
        }

        {
            $rp = new RetentionPolicy();
            $rp->setEnabled(true);
            $rp->setDays(10);

            $m = new Metrics();
            $m->setRetentionPolicy($rp);
            $m->setVersion('1.0');
            $m->setEnabled(true);
            $m->setIncludeAPIs(true);

            $c = CORS::create(TestResources::getCORSSingle());

            $sp = new ServiceProperties();
            $sp->setHourMetrics($m);
            $sp->setCorses(array($c));

            array_push($ret, $sp);
        }

        {
            $rp = new RetentionPolicy();
            // The service does not accept setting days when enabled is false.
            $rp->setEnabled(false);
            $rp->setDays(null);

            $m = new Metrics();
            $m->setRetentionPolicy($rp);
            $m->setVersion('1.0');
            $m->setEnabled(true);
            $m->setIncludeAPIs(true);

            $csArray =
                TestResources::getServicePropertiesSample()[Resources::XTAG_CORS];
            $c0 = CORS::create($csArray[Resources::XTAG_CORS_RULE][0]);
            $c1 = CORS::create($csArray[Resources::XTAG_CORS_RULE][1]);

            $sp = new ServiceProperties();
            $sp->setHourMetrics($m);
            $sp->setCorses(array($c0, $c1));

            array_push($ret, $sp);
        }

        {
            $rp = new RetentionPolicy();
            $rp->setEnabled(true);
            // Days has to be 0 < days <= 365
            $rp->setDays(364);

            $m = new Metrics();
            $m->setVersion('1.0');
            $m->setEnabled(false);
            $m->setIncludeAPIs(null);
            $m->setRetentionPolicy($rp);

            $csArray =
                TestResources::getServicePropertiesSample()[Resources::XTAG_CORS];
            $c0 = CORS::create($csArray[Resources::XTAG_CORS_RULE][0]);
            $c1 = CORS::create($csArray[Resources::XTAG_CORS_RULE][1]);

            $sp = new ServiceProperties();
            $sp->setHourMetrics($m);
            $sp->setCorses(array($c0, $c1));

            array_push($ret, $sp);
        }

        return $ret;
    }

    public static function getInterestingListSharesOptions()
    {
        $ret = array();


        $options = new ListSharesOptions();
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $marker = '/' . self::$accountName . '/' . self::$testShareNames[0];
        $options->setMarker($marker);
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $marker = '/' . self::$accountName . '/' . self::$nonExistSharePrefix;
        $options->setMarker($marker);
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $maxResults = 2;
        $options->setMaxResults($maxResults);
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $prefix = self::$testUniqueId;
        $options->setPrefix($prefix);
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $prefix = self::$nonExistSharePrefix;
        $options->setPrefix($prefix);
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $prefix = self::$testShareNames[0];
        $options->setPrefix($prefix);
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $timeout = -1;
        $options->setTimeout($timeout);
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $timeout = 60;
        $options->setTimeout($timeout);
        array_push($ret, $options);

        $options = new ListSharesOptions();
        $includeMetadata = true;
        $options->setIncludeMetadata($includeMetadata);
        array_push($ret, $options);

        return $ret;
    }

    public static function getInterestingMetadata()
    {
        $ret = array();

        $metadata = array();
        array_push($ret, $metadata);

        array_push($ret, self::getNiceMetadata());

        // Some metadata that HTTP will not like.
        $metadata = array('<>000' => '::::value');
        array_push($ret, $metadata);

        return $ret;
    }

    public static function getNiceMetadata()
    {
        return array(
            'key' => 'value',
            'foo' => 'bar',
            'baz' => 'boo');
    }

    public static function getInterestingCreateFileOptions()
    {
        $ret = array();

        $options = new CreateFileOptions();
        array_push($ret, $options);

        $options = new CreateFileOptions();
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new CreateFileOptions();
        $options->setTimeout(-10);
        array_push($ret, $options);

        $options = new CreateFileOptions();
        $metadata = array(
            'foo' => 'bar',
            'foo2' => 'bar2',
            'foo3' => 'bar3');
        $options->setMetadata($metadata);
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new CreateFileOptions();
        $metadata = array('foo' => 'bar');
        $options->setMetadata($metadata);
        $options->setTimeout(-10);
        array_push($ret, $options);

        return $ret;
    }

    public static function getInterestingListDirectoriesAndFilesOptions()
    {
        $ret = array();

        $options = new ListDirectoriesAndFilesOptions();
        array_push($ret, $options);

        $options = new ListDirectoriesAndFilesOptions();
        $options->setMaxResults(2);
        array_push($ret, $options);

        $options = new ListDirectoriesAndFilesOptions();
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new ListDirectoriesAndFilesOptions();
        $options->setTimeout(-10);
        array_push($ret, $options);


        $options = new ListDirectoriesAndFilesOptions();
        $options->setMaxResults(2);
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new ListDirectoriesAndFilesOptions();
        $options->setMaxResults(3);
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new ListDirectoriesAndFilesOptions();
        $options->setMaxResults(4);
        $options->setTimeout(10);
        array_push($ret, $options);

        return $ret;
    }

    public static function getInterestingCreateShareOptions()
    {
        $ret = array();

        $options = new CreateShareOptions();
        array_push($ret, $options);

        $options = new CreateShareOptions();
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new CreateShareOptions();
        $options->setTimeout(-10);
        array_push($ret, $options);

        $options = new CreateShareOptions();
        $metadata = array(
            'foo' => 'bar',
            'boo' => 'baz',
        );
        $options->setMetadata($metadata);
        array_push($ret, $options);

        return $ret;
    }

    public static function getInterestingDeleteShareOptions()
    {
        $ret = array();

        $past = new \DateTime("01/01/2010");
        $future = new \DateTime("01/01/2020");

        $options = new FileServiceOptions();
        array_push($ret, $options);

        $options = new FileServiceOptions();
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new FileServiceOptions();
        $options->setTimeout(-10);
        array_push($ret, $options);

        return $ret;
    }

    public static function getFileServiceOptions()
    {
        $ret = array();

        $options = new FileServiceOptions();
        array_push($ret, $options);

        $options = new FileServiceOptions();
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new FileServiceOptions();
        $options->setTimeout(-10);
        array_push($ret, $options);

        return $ret;
    }

    public static function getSetFileProperties()
    {
        $ret = array();

        $properties = new FileProperties();
        array_push($ret, $properties);

        $properties = new FileProperties();
        $properties->setCacheControl('setCacheControl');
        array_push($ret, $properties);

        $properties = new FileProperties();
        $properties->setContentEncoding('setContentEncoding');
        array_push($ret, $properties);

        $properties = new FileProperties();
        $properties->setContentLanguage('setContentLanguage');
        array_push($ret, $properties);

        // Note: This is not allowed on block files
        $properties = new FileProperties();
        $properties->setContentLength(2048);
        array_push($ret, $properties);

        $properties = new FileProperties();
        $properties->setContentMD5('d41d8cd98f00b204e9800998ecf8427e');
        array_push($ret, $properties);

        $properties = new FileProperties();
        $properties->setContentType('setContentType');
        array_push($ret, $properties);

        $properties = new FileProperties();
        $properties->setContentDisposition('setContentDisposition');
        array_push($ret, $properties);

        return $ret;
    }

    public static function getInterestingACL()
    {
        $ret = array();

        $past = new \DateTime("01/01/2010");
        $future = new \DateTime("01/01/2020");

        $acl = new ShareACL();
        array_push($ret, $acl);

        $acl = new ShareACL();
        $acl->addSignedIdentifier('123', $past, $future, 'rw');
        array_push($ret, $acl);

        return $ret;
    }

    public static function getGetFileOptions()
    {
        $ret = array();

        $options = new GetFileOptions();
        array_push($ret, $options);

        $options = new GetFileOptions();
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new GetFileOptions();
        $options->setTimeout(-10);
        array_push($ret, $options);

        $options = new GetFileOptions();
        $options->setRange(new Range(50, 200));
        array_push($ret, $options);

        $options = new GetFileOptions();
        $options->setRange(new Range(50, 200));
        $options->setRangeGetContentMD5(true);
        array_push($ret, $options);

        $options = new GetFileOptions();
        $options->setRange(new Range(50));
        array_push($ret, $options);

        $options = new GetFileOptions();
        $options->setRangeGetContentMD5(true);
        array_push($ret, $options);

        return $ret;
    }

    public static function getCopyFileMetaOptionsPairs()
    {
        $ret = array();

        $options = new FileServiceOptions();
        $meta = null;
        array_push($ret, ['metadata' => $meta, 'options' => $options]);

        $options = new FileServiceOptions();
        $options->setTimeout(10);
        $meta = null;
        array_push($ret, ['metadata' => $meta, 'options' => $options]);

        $options = new FileServiceOptions();
        $options->setTimeout(-10);
        $meta = null;
        array_push($ret, ['metadata' => $meta, 'options' => $options]);

        $options = new FileServiceOptions();
        $meta = array(
            'Xkey' => 'Avalue',
            'Yfoo' => 'Bbar',
            'Zbaz' => 'Cboo');
        array_push($ret, ['metadata' => $meta, 'options' => $options]);

        return $ret;
    }

    public static function getRangesArray()
    {
        $ret = array();

        $ret[] = [
            'putRange' => new Range(0, 511),
            'clearRange' => null,
            'listRange' => null,
            'resultListRange' => [new Range(0, 511)]
        ];

        $ret[] = [
            'putRange' => new Range(1024, 1535),
            'clearRange' => null,
            'listRange' => null,
            'resultListRange' => [new Range(0, 511), new Range(1024, 1535)]
        ];

        $ret[] = [
            'putRange' => new Range(512, 1023),
            'clearRange' => null,
            'listRange' => null,
            'resultListRange' => [new Range(0, 1535)]
        ];

        $ret[] = [
            'putRange' => null,
            'clearRange' => new Range(1024, 1535),
            'listRange' => null,
            'resultListRange' => [new Range(0, 1023)]
        ];

        $ret[] = [
            'putRange' => null,
            'clearRange' => null,
            'listRange' => new Range(0, 511),
            'resultListRange' => [new Range(0, 511)]
        ];

        $ret[] = [
            'putRange' => new Range(1024, 2047),
            'clearRange' => new Range(378, 1025),
            'listRange' => null,
            'resultListRange' => [new Range(0, 511), new Range(1024, 2047)]
        ];

        $ret[] = [
            'putRange' => null,
            'clearRange' => new Range(0, 2047),
            'listRange' => null,
            'resultListRange' => array()
        ];

        return $ret;
    }

    public static function getDirectoriesAndFilesToCreateOrDelete()
    {
        $ret = array();

        $ret[] = [
            'operation' => 'create',
            'type' => 'dir',
            'path' => 'dir0',
            'error' => ''
        ];

        $ret[] = [
            'operation' => 'create',
            'type' => 'file',
            'path' => 'dir0/file0',
            'error' => ''
        ];

        $ret[] = [
            'operation' => 'create',
            'type' => 'dir',
            'path' => 'dir0/dir00',
            'error' => ''
        ];

        $ret[] = [
            'operation' => 'create',
            'type' => 'dir',
            'path' => 'dir0/dir01',
            'error' => ''
        ];

        $ret[] = [
            'operation' => 'create',
            'type' => 'dir',
            'path' => 'dir0/dir02/dir020',
            'error' => 'The specified parent path does not exist'
        ];

        $ret[] = [
            'operation' => 'create',
            'type' => 'file',
            'path' => 'dir0/dir02/file020',
            'error' => 'The specified parent path does not exist'
        ];

        $ret[] = [
            'operation' => 'create',
            'type' => 'dir',
            'path' => 'dir0/dir00/dir000',
            'error' => ''
        ];

        $ret[] = [
            'operation' => 'create',
            'type' => 'file',
            'path' => 'dir0/dir00/file000',
            'error' => ''
        ];

        $ret[] = [
            'operation' => 'delete',
            'type' => 'dir',
            'path' => 'dir0/dir00',
            'error' => 'The specified directory is not empty.'
        ];

        $ret[] = [
            'operation' => 'delete',
            'type' => 'dir',
            'path' => 'dir0/dir00/dir000',
            'error' => ''
        ];

        $ret[] = [
            'operation' => 'delete',
            'type' => 'file',
            'path' => 'dir0/dir00/file000',
            'error' => ''
        ];

        $ret[] = [
            'operation' => 'delete',
            'type' => 'dir',
            'path' => 'dir0/dir00',
            'error' => ''
        ];

        return $ret;
    }
}
