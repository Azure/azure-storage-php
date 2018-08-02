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

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreatePageBlobOptions;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;
use MicrosoftAzure\Storage\Blob\Models\BlobServiceOptions;
use MicrosoftAzure\Storage\Blob\Models\CopyBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\BlobType;
use MicrosoftAzure\Storage\Blob\Models\BlobBlockType;
use MicrosoftAzure\Storage\Blob\Models\Block;
use MicrosoftAzure\Storage\Blob\Models\BlockList;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobSnapshotOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobMetadataOptions;
use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\AppendBlockOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobPagesOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\ListContainersOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobBlocksOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Middlewares\RetryMiddlewareFactory;
use MicrosoftAzure\Storage\Common\Middlewares\HistoryMiddleware;
use MicrosoftAzure\Storage\Common\LocationMode;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

class BlobServiceFunctionalTest extends FunctionalTestBase
{
    public function testGetServicePropertiesNoOptions()
    {
        $serviceProperties = BlobServiceFunctionalTestData::getDefaultServiceProperties();
        $shouldReturn = false;
        try {
            $this->restProxy->setServiceProperties($serviceProperties);
            $this->assertFalse($this->isEmulated(), 'Should succeed when not running in emulator');
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

        $this->getServicePropertiesWorker(null);
    }

    public function testGetServiceProperties()
    {
        $serviceProperties = BlobServiceFunctionalTestData::getDefaultServiceProperties();

        $shouldReturn = false;
        try {
            $this->restProxy->setServiceProperties($serviceProperties);
            $this->assertFalse($this->isEmulated(), 'Should succeed when not running in emulator');
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

        // Now look at the combos.
        $interestingTimeouts = BlobServiceFunctionalTestData::getInterestingTimeoutValues();
        foreach ($interestingTimeouts as $timeout) {
            $options = new BlobServiceOptions();
            $options->setTimeout($timeout);
            $this->getServicePropertiesWorker($options);
        }
    }

    private function getServicePropertiesWorker($options)
    {
        $effOptions = (is_null($options) ? new BlobServiceOptions() : $options);
        try {
            $ret = (is_null($options) ?
                $this->restProxy->getServiceProperties() :
                $this->restProxy->getServiceProperties($effOptions));

            if (!is_null($effOptions->getTimeout()) && $effOptions->getTimeout() < 1) {
                $this->true('Expect negative timeouts in $options to throw', false);
            } else {
                $this->assertFalse($this->isEmulated(), 'Should succeed when not running in emulator');
            }
            $this->verifyServicePropertiesWorker($ret, null);
        } catch (ServiceException $e) {
            if ($this->isEmulated()) {
                if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                    $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
                } else {
                    // Expect failure in emulator, as v1.6 doesn't support this method
                    $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
                }
            } else {
                if (is_null($effOptions->getTimeout()) || $effOptions->getTimeout() >= 1) {
                    throw $e;
                } else {
                    $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
                }
            }
        }
    }

    private function verifyServicePropertiesWorker($ret, $serviceProperties)
    {
        if (is_null($serviceProperties)) {
            $serviceProperties = BlobServiceFunctionalTestData::getDefaultServiceProperties();
        }

        $sp = $ret->getValue();
        $this->assertNotNull($sp, 'getValue should be non-null');

        $l = $sp->getLogging();
        $this->assertNotNull($l, 'getValue()->getLogging() should be non-null');
        $this->assertEquals(
            $serviceProperties->getLogging()->getVersion(),
            $l->getVersion(),
            'getValue()->getLogging()->getVersion'
        );
        $this->assertEquals(
            $serviceProperties->getLogging()->getDelete(),
            $l->getDelete(),
            'getValue()->getLogging()->getDelete'
        );
        $this->assertEquals(
            $serviceProperties->getLogging()->getRead(),
            $l->getRead(),
            'getValue()->getLogging()->getRead'
        );
        $this->assertEquals(
            $serviceProperties->getLogging()->getWrite(),
            $l->getWrite(),
            'getValue()->getLogging()->getWrite'
        );

        $r = $l->getRetentionPolicy();
        $this->assertNotNull(
            $r,
            'getValue()->getLogging()->getRetentionPolicy should be non-null'
        );
        $this->assertEquals(
            $serviceProperties->getLogging()->getRetentionPolicy()->getDays(),
            $r->getDays(),
            'getValue()->getLogging()->getRetentionPolicy()->getDays'
        );

        $m = $sp->getHourMetrics();
        $this->assertNotNull(
            $m,
            'getValue()->getHourMetrics() should be non-null'
        );
        $this->assertEquals(
            $serviceProperties->getHourMetrics()->getVersion(),
            $m->getVersion(),
            'getValue()->getHourMetrics()->getVersion'
        );
        $this->assertEquals(
            $serviceProperties->getHourMetrics()->getEnabled(),
            $m->getEnabled(),
            'getValue()->getHourMetrics()->getEnabled'
        );
        $this->assertEquals(
            $serviceProperties->getHourMetrics()->getIncludeAPIs(),
            $m->getIncludeAPIs(),
            'getValue()->getHourMetrics()->getIncludeAPIs'
        );

        $r = $m->getRetentionPolicy();
        $this->assertNotNull(
            $r,
            'getValue()->getHourMetrics()->getRetentionPolicy should be non-null'
        );
        $this->assertEquals(
            $serviceProperties->getHourMetrics()->getRetentionPolicy()->getDays(),
            $r->getDays(),
            'getValue()->getHourMetrics()->getRetentionPolicy()->getDays'
        );
    }

    public function testSetServicePropertiesNoOptions()
    {
        $serviceProperties = BlobServiceFunctionalTestData::getDefaultServiceProperties();
        $this->setServicePropertiesWorker($serviceProperties, null);
    }

    public function testSetServiceProperties()
    {
        $interestingServiceProperties = BlobServiceFunctionalTestData::getInterestingServiceProperties();
        foreach ($interestingServiceProperties as $serviceProperties) {
            $interestingTimeouts = BlobServiceFunctionalTestData::getInterestingTimeoutValues();
            foreach ($interestingTimeouts as $timeout) {
                $options = new BlobServiceOptions();
                $options->setTimeout($timeout);
                $this->setServicePropertiesWorker($serviceProperties, $options);
            }
        }

        if (!$this->isEmulated()) {
            $this->restProxy->setServiceProperties($interestingServiceProperties[0]);
        }
    }

    private function setServicePropertiesWorker($serviceProperties, $options)
    {
        try {
            if (is_null($options)) {
                $this->restProxy->setServiceProperties($serviceProperties);
            } else {
                $this->restProxy->setServiceProperties($serviceProperties, $options);
            }

            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            } else {
                $this->assertFalse($this->isEmulated(), 'Should succeed when not running in emulator');
            }

            \sleep(10);

            $ret = (is_null($options) ?
                $this->restProxy->getServiceProperties() :
                $this->restProxy->getServiceProperties($options)
            );
            $this->verifyServicePropertiesWorker($ret, $serviceProperties);
        } catch (ServiceException $e) {
            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if ($this->isEmulated()) {
                if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                    $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
                } else {
                    $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
                }
            } else {
                if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                    $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
                } else {
                    throw $e;
                }
            }
        }
    }

    public function testListContainersNoOptions()
    {
        $this->listContainersWorker(null);
    }

    public function testListContainers()
    {
        $interestingListContainersOptions = BlobServiceFunctionalTestData::getInterestingListContainersOptions();
        foreach ($interestingListContainersOptions as $options) {
            $this->listContainersWorker($options);
        }
    }

    private function listContainersWorker($options)
    {
        $finished = false;
        while (!$finished) {
            try {
                $ret = (is_null($options) ?
                    $this->restProxy->listContainers() :
                    $this->restProxy->listContainers($options)
                );

                if (is_null($options)) {
                    $options = new ListContainersOptions();
                }

                if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                    $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
                }
                $this->verifyListContainersWorker($ret, $options);

                if (strlen($ret->getNextMarker()) == 0) {
                    $finished = true;
                } else {
                    $options->setMarker($ret->getNextMarker());
                }
            } catch (ServiceException $e) {
                $finished = true;
                if (is_null($options->getTimeout()) || $options->getTimeout() >= 1) {
                    throw $e;
                } else {
                    $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
                }
            }
        }
    }

    private function verifyListContainersWorker($ret, $options)
    {
        // Cannot really check the next marker. Just make sure it is not null.
        $this->assertEquals($options->getNextMarker(), $ret->getMarker(), 'getNextMarker');
        $this->assertEquals($options->getMaxResults(), $ret->getMaxResults(), 'getMaxResults');
        $this->assertEquals($options->getPrefix(), $ret->getPrefix(), 'getPrefix');

        $this->assertNotNull($ret->getContainers(), 'getBlobs');
        if ($options->getMaxResults() == 0) {
            $this->assertEquals(
                0,
                strlen($ret->getNextMarker()),
                'When MaxResults is 0, expect getNextMarker (' .
                    strlen($ret->getNextMarker()) . ')to be  '
            );

            if (!is_null($options->getPrefix()) &&
                $options->getPrefix() ==
                    (BlobServiceFunctionalTestData::$nonExistBlobPrefix)) {
                $this->assertEquals(
                    0,
                    count($ret->getContainers()),
                    'when MaxResults=0 and Prefix=(\'' .
                        $options->getPrefix() . '\'), then Blobs length'
                );
            } elseif (!is_null($options->getPrefix()) &&
                $options->getPrefix() ==
                    (BlobServiceFunctionalTestData::$testUniqueId)) {
                $this->assertEquals(
                    count(BlobServiceFunctionalTestData::$testContainerNames),
                    count(
                        $ret->getContainers()
                    ),
                    'when MaxResults=0 and Prefix=(\'' .
                        $options->getPrefix() .
                        '\'), then Blobs length'
                );
            } else {
                // Do not know how many there should be
            }
        } elseif (strlen($ret->getNextMarker()) == 0) {
            $this->assertTrue(
                count($ret->getContainers()) <= $options->getMaxResults(),
                'when NextMarker (\'' . $ret->getNextMarker() . '\')==\'\',
                Blobs length (' . count($ret->getContainers()) .
                ') should be <= MaxResults (' . $options->getMaxResults() .
                ')'
            );

            if (BlobServiceFunctionalTestData::$nonExistBlobPrefix ==
                    $options->getPrefix()) {
                $this->assertEquals(
                    0,
                    count($ret->getContainers()),
                    'when no next marker and Prefix=(\'' .
                        $options->getPrefix() . '\'), then Blobs length'
                );
            } elseif (BlobServiceFunctionalTestData::$testUniqueId ==
                    $options->getPrefix()) {
                // Need to futz with the mod because you are allowed to get MaxResults items returned.
                $expectedCount = count(BlobServiceFunctionalTestData::$testContainerNames) % $options->getMaxResults();
                if (!$this->isEmulated()) {
                    $expectedCount += 1;
                }
                $this->assertEquals(
                    $expectedCount,
                    count($ret->getContainers()),
                    'when no next marker and Prefix=(\'' .
                    $options->getPrefix() . '\'), then Blobs length'
                );
            } else {
                // Do not know how many there should be
            }
        } else {
            $this->assertEquals(
                count($ret->getContainers()),
                $options->getMaxResults(),
                'when NextMarker (' . $ret->getNextMarker() .
                ')!=\'\', Blobs length (' . count($ret->getContainers()) .
                ') should be == MaxResults (' . $options->getMaxResults() .
                ')'
            );
            if (!is_null($options->getPrefix()) &&
                $options->getPrefix() ==
                    BlobServiceFunctionalTestData::$nonExistBlobPrefix) {
                $this->assertTrue(
                    false,
                    'when a next marker and Prefix=(\'' .
                        $options->getPrefix() . '\'), impossible'
                );
            }
        }
    }

    public function testCreateContainerNoOptions()
    {
        $this->createContainerWorker(null);
    }

    public function testCreateContainer()
    {
        $interestingCreateContainerOptions = BlobServiceFunctionalTestData::getInterestingCreateContainerOptions();
        foreach ($interestingCreateContainerOptions as $options) {
            $this->createContainerWorker($options);
        }
    }

    private function createContainerWorker($options)
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();
        $created = false;

        try {
            if (is_null($options)) {
                $this->restProxy->createContainer($container);
            } else {
                $this->restProxy->createContainer($container, $options);
            }
            $created = true;

            if (is_null($options)) {
                $options = new CreateContainerOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            // Now check that the $container was $created correctly.

            // Make sure that the list of all applicable containers is correctly updated.
            $opts = new ListContainersOptions();
            $opts->setPrefix(BlobServiceFunctionalTestData::$testUniqueId);
            $qs = $this->restProxy->listContainers($opts);
            $this->assertEquals(
                count($qs->getContainers()),
                count(BlobServiceFunctionalTestData::$testContainerNames) + 1,
                'After adding one, with Prefix=(\'' . BlobServiceFunctionalTestData::$testUniqueId .
                    '\'), then Containers length'
            );

            // Check the metadata on the container
            $ret = $this->restProxy->getContainerMetadata($container);
            $this->verifyCreateContainerWorker($ret, $options);
            $this->restProxy->deleteContainer($container);
        } catch (ServiceException $e) {
            if (is_null($options)) {
                $options = new CreateContainerOptions();
            }

            if (is_null($options->getTimeout()) || $options->getTimeout() >= 1) {
                throw $e;
            } else {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            }
        }

        if ($created) {
            try {
                $this->restProxy->deleteContainer($container);
            } catch (ServiceException $e) {
                // Ignore.
            }
        }
    }

    private function verifyCreateContainerWorker($ret, $options)
    {
        if (is_null($options->getMetadata())) {
            $this->assertNotNull($ret->getMetadata(), 'container Metadata');
            $this->assertEquals(0, count($ret->getMetadata()), 'count container Metadata');
        } else {
            $this->assertNotNull($ret->getMetadata(), 'container Metadata');
            $this->assertEquals(count($options->getMetadata()), count($ret->getMetadata()), 'Metadata');
            $retMetadata = $ret->getMetadata();
            foreach ($options->getMetadata() as $key => $value) {
                $this->assertEquals($value, $retMetadata[$key], 'Metadata(' . $key . ')');
            }
        }
    }

    public function testDeleteContainerNoOptions()
    {
        $this->deleteContainerWorker(null);
    }

    public function testDeleteContainer()
    {
        $interestingDeleteContainerOptions = BlobServiceFunctionalTestData::getInterestingDeleteContainerOptions();
        foreach ($interestingDeleteContainerOptions as $options) {
            $this->deleteContainerWorker($options);
        }
    }

    private function deleteContainerWorker($options)
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();

        // Make sure there is something to delete.
        $this->restProxy->createContainer($container);

        // Make sure that the list of all applicable containers is correctly updated.
        $opts = new ListContainersOptions();
        $opts->setPrefix(BlobServiceFunctionalTestData::$testUniqueId);
        $qs = $this->restProxy->listContainers($opts);
        $this->assertEquals(
            count($qs->getContainers()),
            count(BlobServiceFunctionalTestData::$testContainerNames) + 1,
            'After adding one, with Prefix=(\'' .
                BlobServiceFunctionalTestData::$testUniqueId .
                '\'), then Containers length'
        );

        $deleted = false;
        try {
            if (is_null($options)) {
                $this->restProxy->deleteContainer($container);
            } else {
                $this->restProxy->deleteContainer($container, $options);
            }

            $deleted = true;

            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (!$this->isEmulated() &&
                    !BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Failing access condition should throw');
            }

            // Make sure that the list of all applicable containers is correctly updated.
            $opts = new ListContainersOptions();
            $opts->setPrefix(BlobServiceFunctionalTestData::$testUniqueId);
            $qs = $this->restProxy->listContainers($opts);
            $this->assertEquals(
                count($qs->getContainers()),
                count(BlobServiceFunctionalTestData::$testContainerNames),
                'After adding then deleting one, with Prefix=(\'' .
                    BlobServiceFunctionalTestData::$testUniqueId .
                    '\'), then Containers length'
            );

            // Nothing else interesting to check for the $options.
        } catch (ServiceException $e) {
            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            } elseif (!$this->isEmulated() &&
                    !BlobServiceFunctionalTestData::passTemporalAccessCondition(
                        $options->getAccessConditions()
                    )) {
                $this->assertEquals(TestResources::STATUS_PRECONDITION_FAILED, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }

        if (!$deleted) {
            // Try again. If it does not work, not much else to try.
            $this->restProxy->deleteContainer($container);
        }
    }

    public function testGetContainerMetadataNoOptions()
    {
        $metadata = BlobServiceFunctionalTestData::getNiceMetadata();
        $this->getContainerMetadataWorker(null, $metadata);
    }

    public function testGetContainerMetadata()
    {
        $interestingTimeouts = BlobServiceFunctionalTestData::getInterestingTimeoutValues();
        $metadata = BlobServiceFunctionalTestData::getNiceMetadata();

        foreach ($interestingTimeouts as $timeout) {
            $options = new BlobServiceOptions();
            $options->setTimeout($timeout);
            $this->getContainerMetadataWorker($options, $metadata);
        }
    }

    private function getContainerMetadataWorker($options, $metadata)
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();

        // Make sure there is something to test
        $this->restProxy->createContainer($container);
        $this->restProxy->setContainerMetadata($container, $metadata);

        try {
            $res = (is_null($options) ?
                $this->restProxy->getContainerMetadata($container) :
                $this->restProxy->getContainerMetadata($container, $options));

            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() <= 0) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $this->verifyGetContainerMetadataWorker($res, $metadata);
        } catch (ServiceException $e) {
            if (is_null($options->getTimeout()) || $options->getTimeout() > 0) {
                throw $e;
            } else {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            }
        }
        // Clean up.
        $this->restProxy->deleteContainer($container);
    }

    private function verifyGetContainerMetadataWorker($ret, $metadata)
    {
        $this->assertNotNull($ret->getMetadata(), 'container Metadata');
        $this->assertNotNull($ret->getETag(), 'container getETag');
        $this->assertNotNull($ret->getLastModified(), 'container getLastModified');

        $this->assertEquals(count($metadata), count($ret->getMetadata()), 'Metadata');
        $md = $ret->getMetadata();
        foreach ($metadata as $key => $value) {
            $this->assertEquals($value, $md[$key], 'Metadata(' . $key . ')');
        }

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $ret->getLastModified(),
                $now
            ) < 10,
            'Last modified date (' .
            $ret->getLastModified()->format(\DateTime::RFC1123) .
                ')'. ' should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testSetContainerMetadataNoOptions()
    {
        $interestingMetadata = BlobServiceFunctionalTestData::getInterestingMetadata();
        foreach ($interestingMetadata as $metadata) {
            $this->setContainerMetadataWorker(null, $metadata);
        }
    }

    private function setContainerMetadataWorker($options, $metadata)
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();

        // Make sure there is something to test
        $this->restProxy->createContainer($container);

        $firstkey = '';
        if (!is_null($metadata) && count($metadata) > 0) {
            $firstkey = array_keys($metadata);
            $firstkey = $firstkey[0];
        }

        try {
            // And put in some metadata
            if (is_null($options)) {
                $this->restProxy->setContainerMetadata($container, $metadata);
            } else {
                $this->restProxy->setContainerMetadata($container, $metadata, $options);
            }

            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            $this->assertFalse(
                Utilities::startsWith($firstkey, '<'),
                'Should get HTTP request error if the metadata is invalid'
            );

            if (! is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            // setMetadata only honors If-Modified-Since
            if (! $this->isEmulated() &&
                !BlobServiceFunctionalTestData::passTemporalAccessCondition(
                    $options->getAccessConditions()
                ) && (!is_null($options->getAccessConditions()) &&
                empty($options->getAccessConditions()) &&
                $options->getAccessConditions()[0]->getHeader() !=
                    Resources::IF_UNMODIFIED_SINCE)) {
                $this->assertTrue(false, 'Expect failing access condition to throw');
            }

            $res = $this->restProxy->getContainerMetadata($container);
            $this->verifyGetContainerMetadataWorker($res, $metadata);
        } catch (ServiceException $e) {
            if (Utilities::startsWith($firstkey, '<')) {
                $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } elseif (!$this->isEmulated() &&
                    !BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions()) &&
                    (!is_null($options->getAccessConditions()) &&
                    !empty($options->getAccessConditions()) &&
                    $options->getAccessConditions()[0]->getHeader() != Resources::IF_UNMODIFIED_SINCE)) {
                // setMetadata only honors If-Modified-Since
                $this->assertEquals(TestResources::STATUS_PRECONDITION_FAILED, $e->getCode(), 'getCode');
            } elseif (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteContainer($container);
    }

    public function testGetContainerPropertiesNoOptions()
    {
        $metadata = BlobServiceFunctionalTestData::getNiceMetadata();
        $this->getContainerPropertiesWorker(null, $metadata);
    }

    public function testGetContainerProperties()
    {
        $interestingTimeouts = BlobServiceFunctionalTestData::getInterestingTimeoutValues();
        $metadata = BlobServiceFunctionalTestData::getNiceMetadata();
        foreach ($interestingTimeouts as $timeout) {
            $options = new BlobServiceOptions();
            $options->setTimeout($timeout);
            $this->getContainerPropertiesWorker($options, $metadata);
        }
    }

    private function getContainerPropertiesWorker($options, $metadata)
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();

        // Make sure there is something to test
        $this->restProxy->createContainer($container);
        $this->restProxy->setContainerMetadata($container, $metadata);

        try {
            $res = (is_null($options) ?
                $this->restProxy->getContainerProperties($container) :
                $this->restProxy->getContainerProperties($container, $options));

            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $this->verifyGetContainerMetadataWorker($res, $metadata);
        } catch (ServiceException $e) {
            if (is_null($options->getTimeout()) || $options->getTimeout() >= 1) {
                throw $e;
            } else {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            }
        }

        // Clean up.
        $this->restProxy->deleteContainer($container);
    }

    public function testGetContainerACLNoOptions()
    {
        $this->getContainerACLWorker(null);
    }

    public function testGetContainerACL()
    {
        $interestingTimeouts = BlobServiceFunctionalTestData::getInterestingTimeoutValues();
        foreach ($interestingTimeouts as $timeout) {
            $options = new BlobServiceOptions();
            $options->setTimeout($timeout);
            $this->getContainerACLWorker($options);
        }
    }

    private function getContainerACLWorker($options)
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();

        // Make sure there is something to test
        $this->restProxy->createContainer($container);

        try {
            $res = (is_null($options) ?
                $this->restProxy->getContainerACL($container) :
                $this->restProxy->getContainerACL($container, $options));

            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $this->verifyGetContainerACLWorker($res);
        } catch (ServiceException $e) {
            if (is_null($options->getTimeout()) || $options->getTimeout() >= 1) {
                throw $e;
            } else {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            }
        }

        // Clean up.
        $this->restProxy->deleteContainer($container);
    }

    private function verifyGetContainerACLWorker($ret)
    {
        $this->assertNotNull($ret->getContainerACL(), '$ret->getContainerACL');
        $this->assertNotNull($ret->getETag(), '$ret->getETag');
        $this->assertNotNull($ret->getLastModified(), '$ret->getLastModified');
        $this->assertNull($ret->getContainerACL()->getPublicAccess(), '$ret->getContainerACL->getPublicAccess');
        $this->assertNotNull(
            $ret->getContainerACL()->getSignedIdentifiers(),
            '$ret->getContainerACL->getSignedIdentifiers'
        );

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $ret->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $ret->getLastModified()->format(\DateTime::RFC1123) .
                ') ' . 'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testSetContainerACLNoOptions()
    {
        $interestingACL = BlobServiceFunctionalTestData::getInterestingACL();
        foreach ($interestingACL as $acl) {
            $this->setContainerACLWorker(null, $acl);
        }
    }

    public function testSetContainerACL()
    {
        $interestingACL = BlobServiceFunctionalTestData::getInterestingACL();
        $interestingTimeouts = BlobServiceFunctionalTestData::getInterestingTimeoutValues();
        foreach ($interestingTimeouts as $timeout) {
            foreach ($interestingACL as $acl) {
                $options = new BlobServiceOptions();
                $options->setTimeout($timeout);
                $this->setContainerACLWorker($options, $acl);
            }
        }
    }

    private function setContainerACLWorker($options, $acl)
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();

        // Make sure there is something to test
        $this->restProxy->createContainer($container);
        $blobContent = uniqid();
        $this->restProxy->createBlockBlob($container, 'test', $blobContent);

        try {
            if (is_null($options)) {
                $this->restProxy->setContainerACL($container, $acl);
                $this->restProxy->setContainerACL($container, $acl);
            } else {
                $this->restProxy->setContainerACL($container, $acl, $options);
                $this->restProxy->setContainerACL($container, $acl, $options);
            }

            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $res = $this->restProxy->getContainerACL($container);
            $this->verifySetContainerACLWorker($res, $container, $acl, $blobContent);
        } catch (ServiceException $e) {
            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteContainer($container);
    }

    private function verifySetContainerACLWorker($ret, $container, $acl, $blobContent)
    {
        $this->assertNotNull($ret->getContainerACL(), '$ret->getContainerACL');
        $this->assertNotNull($ret->getETag(), '$ret->getContainerACL->getETag');
        $now = new \DateTime();
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $ret->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $ret->getLastModified()->format(\DateTime::RFC1123) .
                ') ' . 'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );

        $this->assertNotNull(
            $ret->getContainerACL()->getSignedIdentifiers(),
            '$ret->getContainerACL->getSignedIdentifiers'
        );

        $this->assertEquals(
            (is_null($acl->getPublicAccess()) ? '' : $acl->getPublicAccess()),
            $ret->getContainerACL()->getPublicAccess(),
            '$ret->getContainerACL->getPublicAccess'
        );
        $expIds = $acl->getSignedIdentifiers();
        $actIds = $ret->getContainerACL()->getSignedIdentifiers();
        $this->assertEquals(count($expIds), count($actIds), '$ret->getContainerACL->getSignedIdentifiers');

        for ($i = 0; $i < count($expIds); $i++) {
            $expId = $expIds[$i];
            $actId = $actIds[$i];
            $this->assertEquals($expId->getId(), $actId->getId(), 'SignedIdentifiers[' . $i .']->getId');
            $this->assertEquals(
                $expId->getAccessPolicy()->getPermission(),
                $actId->getAccessPolicy()->getPermission(),
                'SignedIdentifiers['. $i .']->getAccessPolicy->getPermission'
            );
            $this->assertTrue(
                BlobServiceFunctionalTestData::diffInTotalSeconds(
                    $expId->getAccessPolicy()->getStart(),
                    $actId->getAccessPolicy()->getStart()
                ) < 1,
                'SignedIdentifiers[' . $i .']->getAccessPolicy->getStart should match within 1 second, ' .
                    'exp=' . $expId->getAccessPolicy()->getStart()->format(\DateTime::RFC1123) . ', ' .
                    'act=' . $actId->getAccessPolicy()->getStart()->format(\DateTime::RFC1123)
            );
            $this->assertTrue(
                BlobServiceFunctionalTestData::diffInTotalSeconds(
                    $expId->getAccessPolicy()->getExpiry(),
                    $actId->getAccessPolicy()->getExpiry()
                ) < 1,
                'SignedIdentifiers['. $i .']->getAccessPolicy->getExpiry should match within 1 second, ' .
                    'exp=' . $expId->getAccessPolicy()->getExpiry()->format(\DateTime::RFC1123) . ', ' .
                    'act=' . $actId->getAccessPolicy()->getExpiry()->format(\DateTime::RFC1123)
            );
        }

        if (!$this->isEmulated()) {
            $settings = StorageServiceSettings::createFromConnectionString($this->connectionString);
            $containerAddress = $settings->getBlobEndpointUri() . '/' . $container;
            $blobListAddress = $containerAddress . '?restype=container&comp=list';
            $blobAddress = $containerAddress . '/test';

            $canDownloadBlobList = $this->canDownloadFromUrl(
                $blobListAddress,
                "<?xml version=\"1.0\" encoding=\"utf-8\"?" .
                    "><EnumerationResults"
            );
            $canDownloadBlob = $this->canDownloadFromUrl($blobAddress, $blobContent);

            if (!is_null($acl->getPublicAccess()) && $acl->getPublicAccess() == PublicAccessType::CONTAINER_AND_BLOBS) {
                // Full public read access: Container and blob data can be read via anonymous request.
                // Clients can enumerate blobs within the $container via anonymous request,
                // but cannot enumerate containers within the storage account.
                $this->assertTrue($canDownloadBlobList, '$canDownloadBlobList when ' . $acl->getPublicAccess());
                $this->assertTrue($canDownloadBlob, '$canDownloadBlob when ' . $acl->getPublicAccess());
            } elseif (!is_null($acl->getPublicAccess()) && $acl->getPublicAccess() == PublicAccessType::BLOBS_ONLY) {
                // Public read access for blobs only: Blob data within this container
                // can be read via anonymous request, but $container data is not available.
                // Clients cannot enumerate blobs within the $container via anonymous request.
                $this->assertFalse($canDownloadBlobList, '$canDownloadBlobList when ' . $acl->getPublicAccess());
                $this->assertTrue($canDownloadBlob, '$canDownloadBlob when ' . $acl->getPublicAccess());
            } else {
                // No public read access: Container and blob data can be read by the account owner only.
                $this->assertFalse($canDownloadBlobList, '$canDownloadBlobList when ' . $acl->getPublicAccess());
                $this->assertFalse($canDownloadBlob, '$canDownloadBlob when ' . $acl->getPublicAccess());
            }
        }
    }

    private function canDownloadFromUrl($blobAddress, $expectedStartingValue)
    {
        $client = new Client();
        $body = '';
        try {
            $response = $client->request('GET', $blobAddress);
            $body = $response->getBody();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $body = $e->getResponse()->getBody();
            }
        }
        return strpos($body, $expectedStartingValue) !== false;
    }

    public function testListBlobsNoOptions()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $this->listBlobsWorker($container, null);
    }

    public function testListBlobsNoOptionsExplicitRoot()
    {
        $container = '$root';
        $this->listBlobsWorker($container, null);
    }

    public function testListBlobs()
    {
        $interestingListBlobsOptions = BlobServiceFunctionalTestData::getInterestingListBlobsOptions();
        $container = BlobServiceFunctionalTestData::getContainerName();
        foreach ($interestingListBlobsOptions as $options) {
            $this->listBlobsWorker($container, $options);
        }
    }

    private function listBlobsWorker($container, $options)
    {
        $finished = false;
        while (!$finished) {
            try {
                $ret = (is_null($options) ?
                    $this->restProxy->listBlobs($container) :
                    $this->restProxy->listBlobs($container, $options));

                if (is_null($options)) {
                    $options = new ListBlobsOptions();
                }

                if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                    $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
                }
                $this->verifyListBlobsWorker($ret, $options);

                if (strlen($ret->getNextMarker()) == 0) {
                    $finished = true;
                } else {
                    $options->setMarker($ret->getNextMarker());
                }
            } catch (ServiceException $e) {
                $finished = true;
                if (is_null($options->getTimeout()) || $options->getTimeout() >= 1) {
                    throw $e;
                } else {
                    $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
                }
            }
        }
    }

    private function verifyListBlobsWorker($ret, $options)
    {
        $this->assertEquals($options->getNextMarker(), $ret->getMarker(), 'getNextMarker');
        $this->assertEquals($options->getMaxResults(), $ret->getMaxResults(), 'getMaxResults');
        $this->assertEquals($options->getPrefix(), $ret->getPrefix(), 'getPrefix');

        $this->assertNotNull($ret->getBlobs(), 'getBlobs');
        if ($options->getMaxResults() == 0) {
            $this->assertEquals(
                0,
                strlen($ret->getNextMarker()),
                'When MaxResults is 0, expect getNextMarker (' .
                    strlen($ret->getNextMarker()) . ')to be  '
            );

            if (!is_null($options->getPrefix()) &&
                    $options->getPrefix() ==
                        (BlobServiceFunctionalTestData::$nonExistBlobPrefix)) {
                $this->assertEquals(
                    0,
                    count($ret->getBlobs()),
                    'when MaxResults=0 and Prefix=(\'' .
                        $options->getPrefix() . '\'), then Blobs length'
                );
            } elseif (!is_null($options->getPrefix()) &&
                $options->getPrefix() ==
                    (BlobServiceFunctionalTestData::$testUniqueId)) {
                $this->assertEquals(
                    0,
                    count($ret->getBlobs()),
                    'when MaxResults=0 and Prefix=(\'' .
                        $options->getPrefix() . '\'), then Blobs length'
                );
            } else {
                // Do not know how many there should be
            }
        } elseif (strlen($ret->getNextMarker()) == 0) {
            $this->assertTrue(
                count($ret->getBlobs()) <= $options->getMaxResults(),
                'when NextMarker (\'' . $ret->getNextMarker() . '\')==\'\', Blobs length (' . count($ret->getBlobs()) .
                    ') should be <= MaxResults (' .
                    $options->getMaxResults() . ')'
            );

            if (BlobServiceFunctionalTestData::$nonExistBlobPrefix == $options->getPrefix()) {
                $this->assertEquals(
                    0,
                    count($ret->getBlobs()),
                    'when no next marker and Prefix=(\'' .
                        $options->getPrefix() . '\'), then Blobs length'
                );
            } elseif (BlobServiceFunctionalTestData::$testUniqueId == $options->getPrefix()) {
                // Need to futz with the mod because you are allowed to get MaxResults items returned.
                $this->assertEquals(
                    0,
                    count($ret->getBlobs()) % $options->getMaxResults(),
                    'when no next marker and Prefix=(\'' .
                        $options->getPrefix() . '\'), then Blobs length'
                );
            } else {
                // Do not know how many there should be
            }
        } else {
            $this->assertEquals(
                count($ret->getBlobs()),
                $options->getMaxResults(),
                'when NextMarker (' . $ret->getNextMarker() .
                    ')!=\'\', Blobs length (' . count($ret->getBlobs()) .
                    ') should be == MaxResults (' .
                    $options->getMaxResults() .')'
            );

            if (!is_null($options->getPrefix()) &&
                    $options->getPrefix() ==
                        (BlobServiceFunctionalTestData::$nonExistBlobPrefix)) {
                $this->assertTrue(
                    false,
                    'when a next marker and Prefix=(\'' .
                        $options->getPrefix() . '\'), impossible'
                );
            }
        }
    }

    public function testGetBlobMetadataNoOptions()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $this->getBlobMetadataWorker($container, null);
    }

    public function testGetBlobMetadataNoOptionsRoot()
    {
        $container = null;
        $this->getBlobMetadataWorker($container, null);
    }

    public function testGetBlobMetadataNoOptionsExplicitRoot()
    {
        $container = '$root';
        $this->getBlobMetadataWorker($container, null);
    }

    public function testGetBlobMetadata()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $interestingTimeouts = BlobServiceFunctionalTestData::getInterestingTimeoutValues();

        foreach ($interestingTimeouts as $timeout) {
            $options = new GetBlobMetadataOptions();
            $options->setTimeout($timeout);
            $this->getBlobMetadataWorker($container, $options);
        }
    }

    private function getBlobMetadataWorker($container, $options)
    {
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);

        // Make sure there is something to test
        $createBlockBlobResult = $this->restProxy->createBlockBlob($container, $blob, "");

        $properties = BlobServiceFunctionalTestData::getNiceMetadata();
        $this->restProxy->setBlobMetadata($container, $blob, $properties);

        if (!is_null($options)) {
            BlobServiceFunctionalTestData::fixETagAccessCondition(
                $options->getAccessConditions(),
                $createBlockBlobResult->getETag()
            );
        }

        try {
            $res = (is_null($options) ?
                $this->restProxy->getBlobMetadata($container, $blob) :
                $this->restProxy->getBlobMetadata($container, $blob, $options)
            );

            if (is_null($options)) {
                $options = new GetBlobMetadataOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Failing temporal access condition should throw');
            }
            if (!BlobServiceFunctionalTestData::passETagAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Failing etag access condition should throw');
            }

            $this->verifyGetBlobMetadataWorker($res, $properties);
        } catch (ServiceException $e) {
            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'bad timeout: getCode');
            } elseif (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad temporal access condition: getCode'
                );
            } elseif (!BlobServiceFunctionalTestData::passETagAccessCondition($options->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad etag access condition: getCode'
                );
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteBlob($container, $blob);
    }

    private function verifyGetBlobMetadataWorker($res, $metadata)
    {
        $this->assertNotNull($res->getMetadata(), 'blob Metadata');
        $this->assertNotNull($res->getETag(), 'blob getETag');
        $this->assertNotNull($res->getLastModified(), 'blob getLastModified');

        $this->assertEquals(count($metadata), count($res->getMetadata()), 'Metadata');
        $retMetadata = $res->getMetadata();
        foreach ($metadata as $key => $value) {
            $this->assertEquals($value, $retMetadata[$key], 'Metadata(' . $key . ')');
        }

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $res->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $res->getLastModified()->format(\DateTime::RFC1123) .
                ') ' . 'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testSetBlobMetadataNoOptions()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $interestingMetadata = BlobServiceFunctionalTestData::getInterestingMetadata();
        foreach ($interestingMetadata as $properties) {
            $this->setBlobMetadataWorker($container, null, $properties);
        }
    }

    public function testSetBlobMetadataNoOptionsRoot()
    {
        $container = null;
        $interestingMetadata = BlobServiceFunctionalTestData::getInterestingMetadata();
        foreach ($interestingMetadata as $properties) {
            $this->setBlobMetadataWorker($container, null, $properties);
        }
    }

    public function testSetBlobMetadataNoOptionsExplicitRoot()
    {
        $container = '$root';
        $interestingMetadata = BlobServiceFunctionalTestData::getInterestingMetadata();
        foreach ($interestingMetadata as $properties) {
            $this->setBlobMetadataWorker($container, null, $properties);
        }
    }

    public function testSetBlobMetadata()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $interestingSetBlobMetadataOptions = BlobServiceFunctionalTestData::getSetBlobMetadataOptions();
        $interestingMetadata = BlobServiceFunctionalTestData::getInterestingMetadata();

        foreach ($interestingSetBlobMetadataOptions as $options) {
            foreach ($interestingMetadata as $properties) {
                $this->setBlobMetadataWorker($container, $options, $properties);
            }
        }
    }

    private function setBlobMetadataWorker($container, $options, $metadata)
    {
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);

        // Make sure there is something to test
        $createBlockBlobResult = $this->restProxy->createBlockBlob($container, $blob, "");
        if (!is_null($options)) {
            BlobServiceFunctionalTestData::fixETagAccessCondition(
                $options->getAccessConditions(),
                $createBlockBlobResult->getETag()
            );
        }

        $firstkey = '';
        if (!is_null($metadata) && count($metadata) > 0) {
            $firstkey = array_keys($metadata);
            $firstkey = $firstkey[0];
        }

        try {
            // And put in some properties
            $res = (is_null($options) ?
            $this->restProxy->setBlobMetadata($container, $blob, $metadata) :
            $this->restProxy->setBlobMetadata(
                $container,
                $blob,
                $metadata,
                $options
            ));
            if (is_null($options)) {
                $options = new BlobServiceOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Failing access condition should throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing access condition to throw');
            }
            if (Utilities::startsWith($firstkey, '<')) {
                $this->assertTrue(false, 'Should get HTTP request error if the metadata is invalid');
            }

            $this->verifySetBlobMetadataWorker($res);

            $res2 = $this->restProxy->getBlobMetadata($container, $blob);
            $this->verifyGetBlobMetadataWorker($res2, $metadata);
        } catch (ServiceException $e) {
            if (Utilities::startsWith($firstkey, '<')) {
                $this->assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } elseif (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'bad timeout: getCode');
            } elseif (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad temporal access condition: getCode'
                );
            } elseif (!BlobServiceFunctionalTestData::passETagAccessCondition($options->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad etag access condition: getCode'
                );
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteBlob($container, $blob);
    }

    private function verifySetBlobMetadataWorker($res)
    {
        $this->assertNotNull($res->getETag(), 'blob getETag');
        $this->assertNotNull($res->getLastModified(), 'blob getLastModified');

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $res->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
            $res->getLastModified()->format(\DateTime::RFC1123) . ') ' .
                'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testGetBlobPropertiesNoOptions()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $this->getBlobPropertiesWorker($container, null);
    }

    public function testGetBlobPropertiesNoOptionsRoot()
    {
        $container = null;
        $this->getBlobPropertiesWorker($container, null);
    }

    public function testGetBlobPropertiesNoOptionsExplicitRoot()
    {
        $container = '$root';
        $this->getBlobPropertiesWorker($container, null);
    }

    public function testGetBlobProperties()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $interestingGetBlobPropertiesOptions = BlobServiceFunctionalTestData::getGetBlobPropertiesOptions();

        foreach ($interestingGetBlobPropertiesOptions as $options) {
            $this->getBlobPropertiesWorker($container, $options);
        }
    }

    private function getBlobPropertiesWorker($container, $options)
    {
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);

        // Make sure there is something to test
        $createPageBlobResult = $this->restProxy->createPageBlob($container, $blob, 512);

        $metadata = BlobServiceFunctionalTestData::getNiceMetadata();
        $this->restProxy->setBlobMetadata($container, $blob, $metadata);
        // Do not set the properties, there should be default properties.

        if (!is_null($options)) {
            BlobServiceFunctionalTestData::fixETagAccessCondition(
                $options->getAccessConditions(),
                $createPageBlobResult->getETag()
            );
        }

        try {
            $res = (is_null($options) ?
                $this->restProxy->getBlobProperties($container, $blob) :
                $this->restProxy->getBlobProperties($container, $blob, $options)
            );

            if (is_null($options)) {
                $options = new GetBlobPropertiesOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Failing temporal access condition should throw');
            }

            $this->verifyGetBlobPropertiesWorker($res, $metadata, null);
        } catch (ServiceException $e) {
            if (!is_null($options->getAccessConditions()) &&
                    !empty($options->getAccessConditions()) &&
                    !$this->hasSecureEndpoint() &&
                    $e->getCode() == TestResources::STATUS_FORBIDDEN) {
                // Proxies can eat the access condition headers of
                // unsecured (http) requests, which causes the authentication
                // to fail, with a 403:Forbidden. There is nothing much that
                // can be done about this, other than ignore it.
            } elseif (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'bad timeout: getCode');
            } elseif (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                if ($options->getAccessConditions()[0]->getHeader() == Resources::IF_MODIFIED_SINCE) {
                    $this->assertEquals(
                        TestResources::STATUS_NOT_MODIFIED,
                        $e->getCode(),
                        'bad temporal access condition IF_MODIFIED_SINCE:' .
                            'getCode'
                    );
                } else {
                    $this->assertEquals(
                        TestResources::STATUS_PRECONDITION_FAILED,
                        $e->getCode(),
                        'bad temporal access condition: getCode'
                    );
                }
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteBlob($container, $blob);
    }

    private function verifyGetBlobPropertiesWorker($res, $metadata, $properties)
    {
        /* The semantics for updating a blob's properties are as follows:
         *
         *  * A page blob's sequence number is updated only if the request meets either of the
         *    following conditions:
         *
         *     * The request sets the x-ms-sequence-number-action to max or update, and also
         *       specifies a value for the x-ms-blob-sequence-number header.
         *     * The request sets the x-ms-sequence-number-action to increment, indicating that
         *       the service should increment the sequence number by one.
         *
         *  * A page blob's size is modified only if the request specifies a value for the
         *    x-ms-content-length header.
         *
         *  * If a request sets only x-ms-blob-sequence-number and/or x-ms-content-length, and
         *    no other properties, then none of the blob's other properties are modified.
         *
         *  * If any one or more of the following properties is set in the request, then all of
         *    these properties are set together. If a value is not provided for a given property
         *    when at least one of the properties listed below is set, then that property will be
         *    cleared for the blob.
         *
         *     * x-ms-blob-cache-control
         *     * x-ms-blob-content-type
         *     * x-ms-blob-content-md5
         *     * x-ms-blob-content-encoding
         *     * x-ms-blob-content-language
         */

        $this->assertNotNull($res->getMetadata(), 'blob Metadata');
        if (is_null($metadata)) {
            $this->assertEquals(0, count($res->getMetadata()), 'Metadata');
        } else {
            $this->assertEquals(count($metadata), count($res->getMetadata()), 'Metadata');
            $resMetadata = $res->getMetadata();
            foreach ($metadata as $key => $value) {
                $this->assertEquals($value, $resMetadata[$key], 'Metadata(' . $key . ')');
            }
        }

        $this->assertNotNull($res->getProperties(), 'blob Properties');
        $this->assertNotNull($res->getProperties()->getETag(), 'blob getProperties->getETag');
        $this->assertNotNull($res->getProperties()->getLastModified(), 'blob getProperties->getLastModified');
        $this->assertEquals('PageBlob', $res->getProperties()->getBlobType(), 'blob getProperties->getBlobType');
        $this->assertEquals('unlocked', $res->getProperties()->getLeaseStatus(), 'blob getProperties->getLeaseStatus');

        if (is_null($properties) ||
                !is_null($properties->getContentLength()) ||
                !is_null($properties->getSequenceNumber())) {
            $this->assertNull($res->getProperties()->getCacheControl(), 'blob getProperties->getCacheControl');
            $this->assertEquals(
                'application/octet-stream',
                $res->getProperties()->getContentType(),
                'blob getProperties->getContentType'
            );
            $this->assertNull($res->getProperties()->getContentMD5(), 'blob getProperties->getContentMD5');
            $this->assertNull($res->getProperties()->getContentEncoding(), 'blob getProperties->getContentEncoding');
            $this->assertNull($res->getProperties()->getContentLanguage(), 'blob getProperties->getContentLanguage');
        } else {
            $this->assertEquals(
                $properties->getCacheControl(),
                $res->getProperties()->getCacheControl(),
                'blob getProperties->getCacheControl'
            );
            $this->assertEquals(
                $properties->getContentType(),
                $res->getProperties()->getContentType(),
                'blob getProperties->getContentType'
            );
            $this->assertEquals(
                $properties->getContentMD5(),
                $res->getProperties()->getContentMD5(),
                'blob getProperties->getContentMD5'
            );
            $this->assertEquals(
                $properties->getContentEncoding(),
                $res->getProperties()->getContentEncoding(),
                'blob getProperties->getContentEncoding'
            );
            $this->assertEquals(
                $properties->getContentLanguage(),
                $res->getProperties()->getContentLanguage(),
                'blob getProperties->getContentLanguage'
            );
        }

        if (is_null($properties) || is_null($properties->getContentLength())) {
            $this->assertEquals(512, $res->getProperties()->getContentLength(), 'blob getProperties->getContentLength');
        } else {
            $this->assertEquals(
                $properties->getContentLength(),
                $res->getProperties()->getContentLength(),
                'blob getProperties->getContentLength'
            );
        }

        if (is_null($properties) || is_null($properties->getSequenceNumber())) {
            $this->assertEquals(0, $res->getProperties()->getSequenceNumber(), 'blob getProperties->getSequenceNumber');
        } else {
            $this->assertEquals(
                $properties->getSequenceNumber(),
                $res->getProperties()->getSequenceNumber(),
                'blob getProperties->getSequenceNumber'
            );
        }

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $res->getProperties()->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $res->getProperties()->getLastModified()->format(
                    \DateTime::RFC1123
                ) . ') ' .
                    'should be within 10 seconds of $now (' .
                    $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testSetBlobProperties()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $interestingSetBlobPropertiesOptions = BlobServiceFunctionalTestData::getSetBlobPropertiesOptions();

        foreach ($interestingSetBlobPropertiesOptions as $properties) {
            $this->setBlobPropertiesWorker($container, $properties);
        }
    }

    public function testSetBlobPropertiesRoot()
    {
        $container = null;
        $interestingSetBlobPropertiesOptions = BlobServiceFunctionalTestData::getSetBlobPropertiesOptions();
        $this->setBlobPropertiesWorker($container, $interestingSetBlobPropertiesOptions[2]);
    }

    public function testSetBlobPropertiesExplicitRoot()
    {
        $container = '$root';
        $interestingSetBlobPropertiesOptions = BlobServiceFunctionalTestData::getSetBlobPropertiesOptions();
        $this->setBlobPropertiesWorker($container, $interestingSetBlobPropertiesOptions[2]);
    }

    private function setBlobPropertiesWorker($container, $properties)
    {
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);

        // Make sure there is something to test
        $createPageBlobResult = $this->restProxy->createPageBlob($container, $blob, 512);
        BlobServiceFunctionalTestData::fixETagAccessCondition(
            $properties->getAccessConditions(),
            $createPageBlobResult->getETag()
        );

        try {
            // And put in some properties
            $res = $this->restProxy->setBlobProperties($container, $blob, $properties);

            if (!is_null($properties->getTimeout()) && $properties->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in options to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($properties->getAccessConditions())) {
                $this->assertTrue(false, 'Failing access condition should throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($properties->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing access condition to throw');
            }

            $this->verifySetBlobPropertiesWorker($res);

            $res2 = $this->restProxy->getBlobProperties($container, $blob);
            $this->verifyGetBlobPropertiesWorker($res2, null, $properties);
        } catch (ServiceException $e) {
            if (!is_null($properties->getTimeout()) && $properties->getTimeout() < 1) {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'bad timeout: getCode');
            } elseif (!BlobServiceFunctionalTestData::passTemporalAccessCondition($properties->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad temporal access condition: getCode'
                );
            } elseif (!BlobServiceFunctionalTestData::passETagAccessCondition($properties->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad etag access condition: getCode'
                );
            } else {
            }
        }

        // Clean up.
        $this->restProxy->deleteBlob($container, $blob);
    }

    private function verifySetBlobPropertiesWorker($res)
    {
        $this->assertNotNull($res->getETag(), 'blob getETag');
        $this->assertNotNull($res->getLastModified(), 'blob getLastModified');
        $this->assertNotNull($res->getSequenceNumber(), 'blob getSequenceNumber');
        $this->assertEquals(0, $res->getSequenceNumber(), 'blob getSequenceNumber');

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $res->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $res->getLastModified()->format(\DateTime::RFC1123) . ') ' .
                'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testGetBlobNoOptions()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $this->getBlobWorker(null, $container);
    }

    public function testGetBlobNoOptionsExplicitRoot()
    {
        $this->getBlobWorker(null, '$root');
    }

    public function testGetBlobNoOptionsRoot()
    {
        $this->getBlobWorker(null, '');
    }

    public function testGetBlobAllOptions()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $interestingGetBlobOptions = BlobServiceFunctionalTestData::getGetBlobOptions();
        foreach ($interestingGetBlobOptions as $options) {
            $this->getBlobWorker($options, $container);
        }
    }

    private function getBlobWorker($options, $container)
    {
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);

        // Make sure there is something to test
        $dataSize = 512;
        $createBlobOptions = new CreatePageBlobOptions();
        if ($options && $options->getRangeGetContentMD5()) {
            $createBlobOptions->setContentMD5('MDAwMDAwMDA=');
        }
        $this->restProxy->createPageBlob($container, $blob, $dataSize, $createBlobOptions);

        $metadata = BlobServiceFunctionalTestData::getNiceMetadata();
        $sbmd = $this->restProxy->setBlobMetadata($container, $blob, $metadata);

        $snapshot = $this->restProxy->createBlobSnapshot($container, $blob);
        $this->restProxy->createBlobSnapshot($container, $blob);

        if (!is_null($options)) {
            BlobServiceFunctionalTestData::fixETagAccessCondition($options->getAccessConditions(), $sbmd->getETag());
            $options->setSnapshot(is_null($options->getSnapshot()) ? null : $snapshot->getSnapshot());
        }

        try {
            $res = (is_null($options) ?
                $this->restProxy->getBlob($container, $blob) :
                $this->restProxy->getBlob($container, $blob, $options)
            );

            if (is_null($options)) {
                $options = new GetBlobOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing temporal access condition should throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing etag access condition to throw');
            }
            if ($options->getRangeGetContentMD5() && (
                is_null($options->getRange()) || is_null($options->getRange()->getStart())
                )) {
                $this->assertTrue(false, 'Expect compute range MD5 to fail if range not set');
            }

            $this->verifyGetBlobWorker($res, $options, $dataSize, $metadata);
        } catch (ServiceException $e) {
            if (!is_null($options->getAccessConditions()) &&
                    !empty($options->getAccessConditions()) &&
                    !$this->hasSecureEndpoint() &&
                    $e->getCode() == TestResources::STATUS_FORBIDDEN) {
                // Proxies can eat the access condition headers of
                // unsecured (http) requests, which causes the authentication
                // to fail, with a 403:Forbidden. There is nothing much that
                // can be done about this, other than ignore it.
            } elseif (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'bad timeout: getCode');
            } elseif (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                if ($options->getAccessConditions()[0]->getHeader() == Resources::IF_MODIFIED_SINCE) {
                    $this->assertEquals(
                        TestResources::STATUS_NOT_MODIFIED,
                        $e->getCode(),
                        'bad temporal access condition IF_MODIFIED_SINCE:' .
                        ' getCode'
                    );
                } else {
                    $this->assertEquals(
                        TestResources::STATUS_PRECONDITION_FAILED,
                        $e->getCode(),
                        'bad temporal access condition IF_UNMODIFIED_SINCE:' .
                        ' getCode'
                    );
                }
            } elseif (!BlobServiceFunctionalTestData::passETagAccessCondition($options->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad etag access condition: getCode'
                );
            } elseif ($options->getRangeGetContentMD5() && (
                is_null($options->getRange()) || is_null($options->getRange()->getStart())
                )) {
                $this->assertEquals(
                    TestResources::STATUS_BAD_REQUEST,
                    $e->getCode(),
                    'Expect compute range MD5 to fail when range not set:' .
                    ' getCode'
                );
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteBlob($container, $blob);
    }

    private function verifyGetBlobWorker($res, $options, $dataSize, $metadata)
    {
        $this->assertNotNull($res, 'result');

        $content =  stream_get_contents($res->getContentStream());

        $rangeSize = $dataSize;
        $range = $options->getRange();
        if (is_null($range)) {
            $range = new Range(null);
        }

        if (!is_null($range->getEnd())) {
            $rangeSize = (int) $range->getEnd() + 1;
        }
        if (!is_null($range->getStart())) {
            $rangeSize -= $range->getStart();
        } else {
            // One might expect that not specifying the start would just take the
            // first $rangeEnd bytes, but instead the Azure service ignores
            // the malformed Range headers.
            $rangeSize = $dataSize;
        }

        $this->assertEquals($rangeSize, strlen($content), '$content length and range');

        if ($options->getRangeGetContentMD5()) {
            $md5 = 'MDAwMDAwMDA=';
            $this->assertEquals(
                $md5,
                $res->getProperties()->getContentMD5(),
                'asked for MD5, result->getProperties()->getContentMD5'
            );
        } else {
            $this->assertNull(
                $res->getProperties()->getContentMD5(),
                'did not ask for MD5, result->getProperties()->getContentMD5'
            );
        }

        $this->assertNotNull($res->getMetadata(), 'blob Metadata');
        $resMetadata = $res->getMetadata();
        $this->assertEquals(count($metadata), count($resMetadata), 'Metadata');
        foreach ($metadata as $key => $value) {
            $this->assertEquals($value, $resMetadata[$key], 'Metadata(' . $key . ')');
        }

        // Rest of the properties are tested elsewhere.
    }

    public function testDeleteBlobNoOptions()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $this->deleteBlobWorker(null, $container);
    }

    public function testDeleteBlobNoOptionsExplicitRoot()
    {
        $this->deleteBlobWorker(null, '$root');
    }

    public function testDeleteBlobNoOptionsRoot()
    {
        $this->deleteBlobWorker(null, '');
    }

    public function testDeleteBlob()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $interestingDeleteBlobOptions = BlobServiceFunctionalTestData::getDeleteBlobOptions();
        foreach ($interestingDeleteBlobOptions as $options) {
            $this->deleteBlobWorker($options, $container);
        }
    }

    private function deleteBlobWorker($options, $container)
    {
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);

        // Make sure there is something to test
        $dataSize = 512;
        $this->restProxy->createPageBlob($container, $blob, $dataSize);
        $snapshot = $this->restProxy->createBlobSnapshot($container, $blob);
        $this->restProxy->createBlobSnapshot($container, $blob);

        $blobinfo = $this->restProxy->getBlob($container, $blob);
        if (!is_null($options)) {
            BlobServiceFunctionalTestData::fixETagAccessCondition(
                $options->getAccessConditions(),
                $blobinfo->getProperties()->getETag()
            );
            $options->setSnapshot(is_null($options->getSnapshot()) ? null : $snapshot->getSnapshot());
        }

        $deleted = false;
        try {
            if (is_null($options)) {
                $this->restProxy->deleteBlob($container, $blob);
            } else {
                $this->restProxy->deleteBlob($container, $blob, $options);
            }
            $deleted = true;

            if (is_null($options)) {
                $options = new DeleteBlobOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing temporal access condition should throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing etag access condition to throw');
            }

            $listOptions = new ListBlobsOptions();
            $listOptions->setIncludeSnapshots(true);
            $listOptions->setPrefix($blob);
            $listBlobsResult = $this->restProxy->listBlobs($container == '' ? '$root' : $container, $listOptions);
            $blobs = $listBlobsResult->getBlobs();

            $this->verifyDeleteBlobWorker($options, $blobs);
        } catch (ServiceException $e) {
            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(
                    TestResources::STATUS_INTERNAL_SERVER_ERROR,
                    $e->getCode(),
                    'bad timeout: deleteHttpStatusCode'
                );
            } elseif (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad temporal access condition IF_UNMODIFIED_SINCE: ' .
                        'deleteHttpStatusCode'
                );
            } elseif (!BlobServiceFunctionalTestData::passETagAccessCondition($options->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad etag access condition: deleteHttpStatusCode'
                );
            } else {
                throw $e;
            }
        }

        // Clean up.
        if (!$deleted) {
            $this->restProxy->deleteBlob($container, $blob);
        }
    }

    private function verifyDeleteBlobWorker($options, $blobs)
    {
        if (!is_null($options->getSnapshot())) {
            $this->assertEquals(
                2,
                count($blobs),
                'when give a snapshot, $blobs with same name as main blob'
            );
        } elseif ($options->getDeleteSnaphotsOnly()) {
            $this->assertEquals(
                1,
                count($blobs),
                'when getDeleteSnaphotsOnly=true, ' .
                    '$blobs with same name as main blob'
            );
        } else {
            $this->assertEquals(0, count($blobs), 'when getDeleteSnaphotsOnly=false, blob with same name as main blob');
        }
    }


    public function testCreateBlobSnapshotNoOptionsContainer()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $this->createBlobSnapshotWorker(null, $container);
    }

    public function testCreateBlobSnapshotNoOptionsExplicitRoot()
    {
        $this->createBlobSnapshotWorker(null, '$root');
    }

    public function testCreateBlobSnapshotNoOptionsRoot()
    {
        $this->createBlobSnapshotWorker(null, '');
    }

    public function testCreateBlobSnapshotAllOptions()
    {
        $container = BlobServiceFunctionalTestData::getContainerName();
        $interestingCreateBlobSnapshotOptions = BlobServiceFunctionalTestData::getCreateBlobSnapshotOptions();
        foreach ($interestingCreateBlobSnapshotOptions as $options) {
            $this->createBlobSnapshotWorker($options, $container);
        }
    }

    private function createBlobSnapshotWorker($options, $container)
    {
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);

        // Make sure there is something to test
        $dataSize = 512;
        $this->restProxy->createPageBlob($container, $blob, $dataSize);
        $snapshot1 = $this->restProxy->createBlobSnapshot($container, $blob);
        if (!is_null($options)) {
            BlobServiceFunctionalTestData::fixETagAccessCondition(
                $options->getAccessConditions(),
                $snapshot1->getETag()
            );
        }

        try {
            $res = (is_null($options) ?
                $this->restProxy->createBlobSnapshot($container, $blob) :
                $this->restProxy->createBlobSnapshot($container, $blob, $options)
            );

            if (is_null($options)) {
                $options = new CreateBlobSnapshotOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing temporal access condition should throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing etag access condition to throw');
            }

            $listOptions = new ListBlobsOptions();
            $listOptions->setIncludeSnapshots(true);
            $listOptions->setPrefix($blob);
            $listBlobsResult = $this->restProxy->listBlobs($container == '' ? '$root' : $container, $listOptions);
            $blobs = $listBlobsResult->getBlobs();

            $getBlobOptions = new GetBlobOptions();
            $getBlobOptions->setSnapshot($res->getSnapshot());
            $getBlobResult = $this->restProxy->getBlob($container, $blob, $getBlobOptions);

            $this->verifyCreateBlobSnapshotWorker($res, $options, $blobs, $getBlobResult);
        } catch (ServiceException $e) {
            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(
                    TestResources::STATUS_INTERNAL_SERVER_ERROR,
                    $e->getCode(),
                    'bad timeout: deleteHttpStatusCode'
                );
            } elseif (!BlobServiceFunctionalTestData::
                    passTemporalAccessCondition($options->getAccessConditions())
                ) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad temporal access condition IF_UNMODIFIED_SINCE:' .
                    ' deleteHttpStatusCode'
                );
            } elseif (!BlobServiceFunctionalTestData::
                    passETagAccessCondition($options->getAccessConditions())) {
                $this->assertEquals(
                    TestResources::STATUS_PRECONDITION_FAILED,
                    $e->getCode(),
                    'bad etag access condition: deleteHttpStatusCode'
                );
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteBlob($container, $blob);
    }

    private function verifyCreateBlobSnapshotWorker($res, $options, $blobs, $getBlobResult)
    {
        $now = new \DateTime();

        $this->assertNotNull($res->getETag(), 'result etag');

        $snapshotDate = new \DateTime($res->getSnapshot());

        // Make sure the last modified date is within 10 seconds
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $snapshotDate,
                $now
            ) < 10,
            'Last modified date (' . $snapshotDate->format(\DateTime::RFC1123) .
                ')'. ' should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );

        // Make sure the last modified date is within 10 seconds
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $res->getLastModified(),
                $now
            ) < 10,
            'Last modified date (' .
                $res->getLastModified()->format(\DateTime::RFC1123) . ')'.
                ' should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );

        $this->assertEquals(3, count($blobs), 'Should end up with 3 $blobs with same name as main blob');

        $this->assertNotNull($getBlobResult->getMetadata(), 'blob Metadata');
        $this->assertEquals(count($options->getMetadata()), count($getBlobResult->getMetadata()), 'Metadata');
        $retMetadata = $getBlobResult->getMetadata();
        if (!is_null($options->getMetadata())) {
            foreach ($options->getMetadata() as $key => $value) {
                $this->assertEquals($value, $retMetadata[$key], 'Metadata(' . $key . ')');
            }
        }
    }


    public function testCopyBlobNoOptions()
    {
        $sourceContainers = array(
            BlobServiceFunctionalTestData::$testContainerNames[0],
            '$root',
            '');

        $destContainers = array(
            BlobServiceFunctionalTestData::$testContainerNames[1],
            '$root',
            '');

        foreach ($sourceContainers as $sourceContainer) {
            foreach ($destContainers as $destContainer) {
                $this->copyBlobWorker(null, $sourceContainer, $destContainer);
            }
        }
    }

    public function testCopyBlobAllOptions()
    {
        $sourceContainer = BlobServiceFunctionalTestData::$testContainerNames[0];
        $destContainer = BlobServiceFunctionalTestData::$testContainerNames[1];

        $interestingCopyBlobOptions = BlobServiceFunctionalTestData::getCopyBlobOptions();
        foreach ($interestingCopyBlobOptions as $options) {
            $this->copyBlobWorker($options, $sourceContainer, $destContainer);
        }
    }

    private function copyBlobWorker($options, $sourceContainer, $destContainer)
    {
        $sourceBlob = BlobServiceFunctionalTestData::getInterestingBlobName($sourceContainer);
        $destBlob = BlobServiceFunctionalTestData::getInterestingBlobName($destContainer);

        // Make sure there is something to test
        $sourceDataSize = 512;
        $this->restProxy->createPageBlob($sourceContainer, $sourceBlob, $sourceDataSize);

        $destDataSize = 2048;
        $destBlobInfo = $this->restProxy->createPageBlob($destContainer, $destBlob, $destDataSize);
        $this->restProxy->createBlobSnapshot($destContainer, $destBlob);

        $metadata = BlobServiceFunctionalTestData::getNiceMetadata();
        $this->restProxy->setBlobMetadata($sourceContainer, $sourceBlob, $metadata);
        $snapshot = $this->restProxy->createBlobSnapshot($sourceContainer, $sourceBlob);
        if (!is_null($options)) {
            BlobServiceFunctionalTestData::fixETagAccessCondition(
                $options->getSourceAccessConditions(),
                $snapshot->getETag()
            );
            BlobServiceFunctionalTestData::fixETagAccessCondition(
                $options->getAccessConditions(),
                $destBlobInfo->getETag()
            );
            $options->setSourceSnapshot(is_null($options->getSourceSnapshot()) ? null : $snapshot->getSnapshot());
        }

        try {
            if (is_null($options)) {
                $this->restProxy->copyBlob($destContainer, $destBlob, $sourceContainer, $sourceBlob);
            } else {
                $this->restProxy->copyBlob($destContainer, $destBlob, $sourceContainer, $sourceBlob, $options);
            }

            if (is_null($options)) {
                $options = new CopyBlobOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getSourceAccessConditions())) {
                $this->assertTrue(false, 'Expect failing source temporal access condition should throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getSourceAccessConditions())) {
                $this->assertTrue(false, 'Expect failing source etag access condition to throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing dest temporal access condition should throw');
            }
            if (!BlobServiceFunctionalTestData::passTemporalAccessCondition($options->getAccessConditions())) {
                $this->assertTrue(false, 'Expect failing dest etag access condition to throw');
            }

            $listOptions = new ListBlobsOptions();
            $listOptions->setIncludeSnapshots(true);
            $listOptions->setPrefix($destBlob);
            $listBlobsResult = $this->restProxy->listBlobs(
                $destContainer == '' ? '$root' :
                $destContainer,
                $listOptions
            );
            $blobs = $listBlobsResult->getBlobs();

            $getBlobResult = $this->restProxy->getBlob($destContainer, $destBlob);

            $this->verifyCopyBlobWorker($options, $blobs, $getBlobResult, $sourceDataSize, $metadata);
        } catch (ServiceException $e) {
            if (is_null($options)) {
                $options = new CopyBlobOptions();
            }

            if (!is_null($options->getTimeout()) && $options->getTimeout() < 1) {
                $this->assertEquals(500, $e->getCode(), 'bad timeout: deleteHttpStatusCode');
            } elseif (!BlobServiceFunctionalTestData::
                    passTemporalAccessCondition(
                        $options->getSourceAccessConditions()
                    )) {
                $this->assertEquals(
                    412,
                    $e->getCode(),
                    'bad source temporal access condition ' .
                    'IF_UNMODIFIED_SINCE: deleteHttpStatusCode'
                );
            } elseif (!BlobServiceFunctionalTestData::
                    passETagAccessCondition(
                        $options->getSourceAccessConditions()
                    )) {
                $this->assertEquals(
                    412,
                    $e->getCode(),
                    'bad source etag access condition: deleteHttpStatusCode'
                );
            } elseif (!BlobServiceFunctionalTestData::
                    passTemporalAccessCondition(
                        $options->getAccessConditions()
                    )) {
                $this->assertEquals(
                    412,
                    $e->getCode(),
                    'bad dest temporal access condition ' .
                    'IF_UNMODIFIED_SINCE: deleteHttpStatusCode'
                );
            } elseif (!BlobServiceFunctionalTestData::
                    passETagAccessCondition(
                        $options->getAccessConditions()
                    )) {
                $this->assertEquals(
                    412,
                    $e->getCode(),
                    'bad dest etag access condition: deleteHttpStatusCode'
                );
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteBlob($sourceContainer, $sourceBlob);
        $this->restProxy->deleteBlob($destContainer, $destBlob);
    }

    private function verifyCopyBlobWorker($options, $blobs, $getBlobResult, $sourceDataSize, $metadata)
    {
        $this->assertEquals(
            2,
            count($blobs),
            'Should end up with 2 blob with same name as dest blob,' .
            ' snapshot and copied blob'
        );
        $this->assertEquals(
            $sourceDataSize,
            $getBlobResult->getProperties()->getContentLength(),
            'Dest length should be the same as the source length'
        );

        $this->assertNotNull($getBlobResult->getMetadata(), 'blob Metadata');
        $expectedMetadata = (count($options->getMetadata()) == 0 ? $metadata : $options->getMetadata());
        $resMetadata = $getBlobResult->getMetadata();
        $this->assertEquals(count($expectedMetadata), count($resMetadata), 'Metadata');
        foreach ($expectedMetadata as $key => $value) {
            $this->assertEquals($value, $resMetadata[strtolower($key)], 'Metadata(' . $key . ')');
        }

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        $this->assertTrue(
            BlobServiceFunctionalTestData::diffInTotalSeconds(
                $getBlobResult->getProperties()->getLastModified(),
                $now
            ) < 10,
            'Last modified date (' .
                $getBlobResult->getProperties()->getLastModified()->format(
                    \DateTime::RFC1123
                ) . ')'. ' should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testMiddlewares()
    {
        //setup middlewares.
        $historyMiddleware = new HistoryMiddleware();
        $retryMiddleware = RetryMiddlewareFactory::create(
            RetryMiddlewareFactory::GENERAL_RETRY_TYPE,
            3,
            1
        );

        //setup options for the first try.
        $options = new ListContainersOptions();
        $options->setMiddlewares([$historyMiddleware]);
        //get the response of the server.
        $result = $this->restProxy->listContainers($options);
        $response = $historyMiddleware->getHistory()[0]['response'];
        $request = $historyMiddleware->getHistory()[0]['request'];

        //setup the mock handler
        $mock = MockHandler::createWithMiddleware([
            new RequestException(
                'mock 408 exception',
                $request,
                new Response(408, ['test_header' => 'test_header_value'])
            ),
            new Response(500, ['test_header' => 'test_header_value']),
            $response
        ]);
        $restOptions = ['http' => ['handler' => $mock]];
        $mockProxy = BlobRestProxy::createBlobService($this->connectionString, $restOptions);

        //test using mock handler.
        $options = new ListContainersOptions();
        $options->setMiddlewares([$retryMiddleware, $historyMiddleware]);
        $newResult = $mockProxy->listContainers($options);
        $this->assertTrue(
            $result == $newResult,
            'Mock result does not match server behavior'
        );
        $this->assertTrue(
            $historyMiddleware->getHistory()[1]['reason']->getMessage() == 'mock 408 exception',
            'Mock handler does not gave the first 408 exception correctly'
        );
        $this->assertTrue(
            $historyMiddleware->getHistory()[2]['reason']->getCode() == 500,
            'Mock handler does not gave the second 500 response correctly'
        );
    }

    public function testRetryFromSecondary()
    {
        //setup middlewares.
        $historyMiddleware = new HistoryMiddleware();
        $retryMiddleware = RetryMiddlewareFactory::create(
            RetryMiddlewareFactory::GENERAL_RETRY_TYPE,
            3,
            1
        );

        //setup options for the first try.
        $options = new ListContainersOptions();
        $options->setMiddlewares([$historyMiddleware]);
        //get the response of the server.
        $result = $this->restProxy->listContainers($options);
        $response = $historyMiddleware->getHistory()[0]['response'];
        $request = $historyMiddleware->getHistory()[0]['request'];

        //setup the mock handler
        $mock = MockHandler::createWithMiddleware([
            new Response(500, ['test_header' => 'test_header_value']),
            new RequestException(
                'mock 404 exception',
                $request,
                new Response(404, ['test_header' => 'test_header_value'])
            ),
            $response
        ]);
        $restOptions = ['http' => ['handler' => $mock]];
        $mockProxy = BlobRestProxy::createBlobService($this->connectionString, $restOptions);

        //test using mock handler.
        $options = new ListContainersOptions();
        $options->setLocationMode(LocationMode::PRIMARY_THEN_SECONDARY);
        $options->setMiddlewares([$retryMiddleware, $historyMiddleware]);
        $newResult = $mockProxy->listContainers($options);
        $this->assertTrue(
            $result == $newResult,
            'Mock result does not match server behavior'
        );
        $this->assertTrue(
            $historyMiddleware->getHistory()[1]['reason']->getCode() == 500,
            'Mock handler does not gave the second 500 response correctly'
        );
        $this->assertTrue(
            $historyMiddleware->getHistory()[2]['reason']->getMessage() == 'mock 404 exception',
            'Mock handler does not gave the first 404 exception correctly'
        );


        $uri2 = (string)($historyMiddleware->getHistory()[2]['request']->getUri());
        $uri3 = (string)($historyMiddleware->getHistory()[3]['request']->getUri());

        $this->assertTrue(
            strpos($uri2, '-secondary') !== false,
            'Did not retry to secondary uri.'
        );
        $this->assertFalse(
            strpos($uri3, '-secondary'),
            'Did not switch back to primary uri.'
        );
    }

    public function testListRetryWithSecondEndpoint()
    {
        //setup middlewares.
        $historyMiddleware = new HistoryMiddleware();
        $retryMiddleware = RetryMiddlewareFactory::create(
            RetryMiddlewareFactory::GENERAL_RETRY_TYPE,
            3,
            1
        );
        $marker = 'next';
        //Construct response
        $bodyArray = TestResources::listContainersMultipleRandomEntriesBody(5, $marker);
        $bodyString = Utilities::serialize($bodyArray, 'EnumerationResults');
        $mockResponse = new Response(200, array(), $bodyString);
        //setup the mock handler
        $mock = MockHandler::createWithMiddleware([
            new Response(500, ['test_header' => 'test_header_value']),
            $mockResponse
        ]);
        $restOptions = ['http' => ['handler' => $mock]];
        $mockProxy = BlobRestProxy::createBlobService($this->connectionString, $restOptions);
        //test using mock handler.
        $options = new ListContainersOptions();
        $options->setLocationMode(LocationMode::PRIMARY_THEN_SECONDARY);
        $options->setMiddlewares([$retryMiddleware, $historyMiddleware]);
        $result = $mockProxy->listContainers($options);

        $this->assertNotNull($result->getContinuationToken());
        $this->assertEquals(LocationMode::SECONDARY_ONLY, $result->getLocation());
        $request = $historyMiddleware->getHistory()[1]['request'];
        $options = $historyMiddleware->getHistory()[1]['options'];
        $this->assertNotNull(
            strpos(
                (string)$request->getUri(),
                (string)$options[Resources::ROS_SECONDARY_URI]
            )
        );

        //List containers with the continuation token.
        $options = new ListContainersOptions();
        $options->setContinuationToken($result->getContinuationToken());
        $options->setLocationMode(LocationMode::PRIMARY_THEN_SECONDARY);
        $options->setMiddlewares([$retryMiddleware, $historyMiddleware]);
        //make sure the continuation's location overwrites the options.
        $this->assertEquals(LocationMode::SECONDARY_ONLY, $options->getLocationMode());

        $mock = MockHandler::createWithMiddleware([
            $mockResponse
        ]);
        $restOptions = ['http' => ['handler' => $mock]];
        $mockProxy = BlobRestProxy::createBlobService($this->connectionString, $restOptions);
        $newResult = $mockProxy->listContainers($options);


        $this->assertNotNull($newResult->getContinuationToken());
        $this->assertEquals(LocationMode::SECONDARY_ONLY, $newResult->getLocation());
        $request = $historyMiddleware->getHistory()[2]['request'];
        $options = $historyMiddleware->getHistory()[2]['options'];
        $this->assertNotNull(
            strpos(
                (string)$request->getUri(),
                (string)$options[Resources::ROS_SECONDARY_URI]
            )
        );

        //Make sure queried with next marker.
        $this->assertNotNull(
            strpos(
                (string)$request->getUri(),
                'marker=' . $marker
            )
        );
    }

    public function testCreateBlockBlobNormal()
    {
        $attrs = BlobServiceFunctionalTestData::getCreateBlockBlobAttributes();
        $container = BlobServiceFunctionalTestData::getContainerName();

        foreach ($attrs as $attr) {
            $threshold = array_key_exists('threshold', $attr)?
                $attr['threshold'] : Resources::MB_IN_BYTES_32;
            $size = $attr['size'];
            $this->createBlockBlobWorker($container, $threshold, $size);
        }
    }

    private function createBlockBlobWorker($container, $threshold, $size)
    {
        //create a temp file of size $size.
        $cwd = getcwd();
        $uuid = uniqid('test-file-', true);
        $path = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $resource = fopen($path, 'w+');

        $count = $size / Resources::MB_IN_BYTES_32;
        for ($i = 0; $i < $count; ++$i) {
            fwrite($resource, openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_32));
        }
        $remain = $size - (Resources::MB_IN_BYTES_32 * $count);
        fwrite($resource, openssl_random_pseudo_bytes($remain));
        rewind($resource);

        //upload the blob
        $blobName = BlobServiceFunctionalTestData::getInterestingBlobName($container);
        $metadata = array('m1' => 'v1', 'm2' => 'v2');
        $contentType = 'text/plain; charset=UTF-8';
        $options = new CreateBlockBlobOptions();
        $options->setContentType($contentType);
        $options->setMetadata($metadata);
        $options->setUseTransactionalMD5(true);
        $this->restProxy->setSingleBlobUploadThresholdInBytes($threshold);
        $this->restProxy->createBlockBlob(
            $container,
            $blobName,
            $resource,
            $options
        );

        // Test
        $result = $this->restProxy->getBlob($container, $blobName);

        //get the path for the file to be downloaded into.
        $uuid = uniqid('test-file-', true);
        $downloadPath = $cwd.DIRECTORY_SEPARATOR.$uuid.'.txt';
        $downloadResource = fopen($downloadPath, 'w');
        //download the file
        $content = $result->getContentStream();

        while (!feof($content)) {
            fwrite(
                $downloadResource,
                stream_get_contents($content, Resources::MB_IN_BYTES_32)
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

    public function testBlockBlobBlocks()
    {
        //create block blob
        $container = BlobServiceFunctionalTestData::getContainerName();
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);
        $this->restProxy->createBlockBlob($container, $blob, '');

        //create blocks
        $blockIds = array();
        $contents = array();
        for ($i = 0; $i < 5; ++$i) {
            $blockId = BlobServiceFunctionalTestData::getInterestingBlockId();
            $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
            $this->restProxy->createBlobBlock($container, $blob, $blockId, $content);
            $blockIds[] = $blockId;
            $contents[] = $content;
        }
        $this->verifyBlocks($container, $blob, $blockIds, false);
        //commit blocks 1 and 3.
        $latest = BlobBlockType::LATEST_TYPE;
        $committed = BlobBlockType::COMMITTED_TYPE;
        $blockList = [
            new Block($blockIds[1], $latest),
            new Block($blockIds[3], $latest)
        ];
        $this->restProxy->commitBlobBlocks($container, $blob, $blockList);
        //verify MD5 and uncommitted.
        $this->verifyBlobMd5($container, $blob, $contents[1] . $contents[3]);
        $this->verifyBlocks(
            $container,
            $blob,
            [$blockIds[1], $blockIds[3]]
        );

        //update blob with blocks 3 and 4.
        for ($i = 0; $i < 5; ++$i) {
            $this->restProxy->createBlobBlock(
                $container,
                $blob,
                $blockIds[$i],
                $contents[$i]
            );
        }
        $blockList = [
            new Block($blockIds[3], $latest),
            new Block($blockIds[4], $latest),
        ];
        $this->restProxy->commitBlobBlocks($container, $blob, $blockList);
        //verify MD5 and uncommitted.
        $this->verifyBlobMd5($container, $blob, $contents[3] . $contents[4]);
        $this->verifyBlocks(
            $container,
            $blob,
            [$blockIds[3], $blockIds[4]]
        );

        //commit a blob with same id with block 3
        $this->restProxy->createBlobBlock($container, $blob, $blockIds[0], $contents[0]);
        $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
        $this->restProxy->createBlobBlock($container, $blob, $blockIds[3], $content);
        //test BlobBlockType::COMMITTED_TYPE
        $blockList = [
            new Block($blockIds[3], $committed),
            new Block($blockIds[0], $latest),
        ];
        $this->restProxy->commitBlobBlocks($container, $blob, $blockList);
        //verify MD5 and uncommitted.
        $this->verifyBlobMd5($container, $blob, $contents[3] . $contents[0]);
        $this->verifyBlocks(
            $container,
            $blob,
            [$blockIds[0], $blockIds[3]]
        );
        //test BlobBlockType::LATEST_TYPE
        $this->restProxy->createBlobBlock($container, $blob, $blockIds[0], $contents[0]);
        $content = openssl_random_pseudo_bytes(Resources::MB_IN_BYTES_4);
        $this->restProxy->createBlobBlock($container, $blob, $blockIds[3], $content);
        $blockList = [
            new Block($blockIds[3], $latest),
            new Block($blockIds[0], $latest),
        ];
        $this->restProxy->commitBlobBlocks($container, $blob, $blockList);
        //verify MD5 and uncommitted.
        $this->verifyBlobMd5($container, $blob, $content . $contents[0]);
        $this->verifyBlocks(
            $container,
            $blob,
            [$blockIds[3], $blockIds[0]]
        );
    }

    private function verifyBlobMd5($container, $blob, $content)
    {
        $c = stream_get_contents($this->restProxy->getBlob($container, $blob)->getContentStream());
        $expectedMd5 = md5($content);
        $actualMd5 = md5($c);
        $this->assertEquals($expectedMd5, $actualMd5);
    }

    private function verifyBlocks($container, $blob, $list, $isCommitted = true)
    {
        $options = new ListBlobBlocksOptions();
        if ($isCommitted) {
            $options->setIncludeCommittedBlobs(true);
        } else {
            $options->setIncludeUncommittedBlobs(true);
        }
        $result = $this->restProxy->listBlobBlocks($container, $blob, $options);
        $blocks = $isCommitted? $result->getCommittedBlocks() : $result->getUncommittedBlocks();
        foreach ($list as $blockId) {
            $this->assertTrue(array_key_exists($blockId, $blocks));
        }
    }

    public function testPutListClearPageRanges()
    {
        $rangesArray = BlobServiceFunctionalTestData::getRangesArray();
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();
        $this->createContainer($container);
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);
        $this->restProxy->createPageBlob($container, $blob, 2048);
        foreach ($rangesArray as $array) {
            $this->putListClearPageRangesWorker(
                $container,
                $blob,
                $array['putRange'],
                $array['clearRange'],
                $array['listRange'],
                $array['resultListRange']
            );
        }
        $this->deleteContainer($container);
    }

    private function putListClearPageRangesWorker(
        $container,
        $blob,
        $putRange,
        $clearRange,
        $listRange,
        $resultListRange
    ) {
        if ($putRange != null) {
            $length = $putRange->getLength();
            if ($length == null) {
                $length = 2048 - $putRange->getStart();
            }
            $content = \openssl_random_pseudo_bytes($length);
            $options = new CreateBlobPagesOptions();
            //setting the wrong md5.
            $options->setContentMD5(Utilities::calculateContentMD5(''));
            $message = '';
            try {
                $this->restProxy->createBlobPages(
                    $container,
                    $blob,
                    $putRange,
                    $content,
                    $options
                );
            } catch (ServiceException $e) {
                $message = $e->getMessage();
            }
            $this->assertContains('400', $message);
            $this->assertContains(
                'The MD5 value specified in the request did not match with the MD5 value calculated by the server.',
                $message
            );
            //Ends debug code snippet
            // Now set the correct content MD5
            $options->setContentMD5(Utilities::calculateContentMD5($content));
            $this->restProxy->createBlobPages(
                $container,
                $blob,
                $putRange,
                $content,
                $options
            );
            $getOptions = new GetBlobOptions();
            $getOptions->setRange($putRange);
            $getOptions->setRangeGetContentMD5(true);
            $result = $this->restProxy->getBlob($container, $blob, $getOptions);
            $actualContent = stream_get_contents($result->getContentStream());
            $actualMD5 = $result->getProperties()->getRangeContentMD5();
            //Validate
            $this->assertEquals($content, $actualContent);
            $this->assertEquals(Utilities::calculateContentMD5($content), $actualMD5);
        }
        if ($clearRange != null) {
            $this->restProxy->clearBlobPages($container, $blob, $clearRange);
        }
        //Validate result
        $listRangeOptions = new ListPageBlobRangesOptions();
        $listRange = is_null($listRange) ? new Range(null) : $listRange;
        $listRangeOptions->setRange($listRange);
        $listResult =
            $this->restProxy->listPageBlobRanges($container, $blob, $listRangeOptions);
        $this->assertEquals(2048, $listResult->getContentLength());
        $resultRanges = $listResult->getRanges();
        for ($i = 0; $i < count($resultRanges); ++$i) {
            $this->assertEquals($resultListRange[$i], $resultRanges[$i]);
        }
    }

    public function testPutListClearPageRangesDiff()
    {
        $rangesArray = BlobServiceFunctionalTestData::getRangesDiffArray();
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();
        $this->createContainer($container);
        $length = 2048;

        foreach ($rangesArray as $array) {
            $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);
            $this->restProxy->createPageBlob($container, $blob, $length);
            $content = \openssl_random_pseudo_bytes($length);
            $this->restProxy->createBlobPages(
                $container,
                $blob,
                new Range(0, $length - 1),
                $content
            );

            $snapshot = $this->restProxy->createBlobSnapshot($container, $blob)->getSnapshot();

            $this->putListClearPageRangesDiffWorker(
                $container,
                $blob,
                $array['putRange'],
                $array['clearRange'],
                $array['listRange'],
                $array['resultListRange'],
                $snapshot,
                $length
            );
        }
    }

    private function putListClearPageRangesDiffWorker(
        $container,
        $blob,
        $putRange,
        $clearRange,
        $listRange,
        $resultListRange,
        $snapshot,
        $length
    ) {
        if ($putRange != null) {
            $rangeLength = $putRange->getLength();
            if ($rangeLength == null) {
                $rangeLength = $length - $putRange->getStart();
            }
            $content = \openssl_random_pseudo_bytes($rangeLength);

            $this->restProxy->createBlobPages(
                $container,
                $blob,
                $putRange,
                $content
            );
        }
        if ($clearRange != null) {
            $this->restProxy->clearBlobPages($container, $blob, $clearRange);
        }

        //Validate result
        $listRangeOptions = new ListPageBlobRangesOptions();
        $listRange = is_null($listRange) ? new Range(null) : $listRange;
        $listRangeOptions->setRange($listRange);
        $listResult =
            $this->restProxy->listPageBlobRangesDiff($container, $blob, $snapshot, $listRangeOptions);
        $this->assertEquals($length, $listResult->getContentLength());
        $resultRanges = $listResult->getRanges();
        for ($i = 0; $i < count($resultRanges); ++$i) {
            $this->assertEquals($resultListRange[$i], $resultRanges[$i]);
        }
    }

    public function testAppendBlob()
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();
        $this->createContainer($container);
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);
        $this->restProxy->createAppendBlob($container, $blob);

        $setupArrays = BlobServiceFunctionalTestData::getAppendBlockSetup();
        foreach ($setupArrays as $setupArray) {
            $content = openssl_random_pseudo_bytes($setupArray['size']);
            $options = $setupArray['options'];
            $errorMsg = $setupArray['error'];
            $message = '';
            try {
                $this->restProxy->appendBlock(
                    $container,
                    $blob,
                    $content,
                    $options
                );
            } catch (ServiceException $e) {
                $message = $e->getMessage();
            }
            if ($errorMsg == '') {
                $this->assertEquals('', $message);
            } else {
                $this->assertContains($errorMsg, $message);
            }
        }
    }

    public function testLeaseContainer()
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();
        $this->restProxy->createContainer($container);
        $leaseId = Utilities::getGuid();
        $result = $this->restProxy->acquireLease($container, '', $leaseId);
        $this->assertEquals($leaseId, $result->getLeaseId());
        $message = '';
        try {
            $this->restProxy->deleteContainer($container);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains('There is currently a lease on the container and no lease ID was specified in the request', $message);
        $options = new BlobServiceOptions();
        $options->setLeaseId($leaseId);
        $this->restProxy->deleteContainer($container, $options);
    }

    public function testLeaseBlob()
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();
        $this->restProxy->createContainer($container);
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);
        $this->restProxy->createPageBlob($container, $blob, 1024);
        $leaseId = Utilities::getGuid();
        $result = $this->restProxy->acquireLease($container, $blob, $leaseId);
        $this->assertEquals($leaseId, $result->getLeaseId());
        $message = '';
        try {
            $this->restProxy->deleteBlob($container, $blob);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains('There is currently a lease on the blob and no lease ID was specified in the request.', $message);
        $options = new DeleteBlobOptions();
        $options->setLeaseId($leaseId);
        $this->restProxy->deleteBlob($container, $blob, $options);
    }

    public function testLeaseOperations()
    {
        $container = BlobServiceFunctionalTestData::getInterestingContainerName();
        $this->restProxy->createContainer($container);
        //configure
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);
        $this->restProxy->createPageBlob($container, $blob, 1024);
        $leaseId = Utilities::getGuid();

        $message = '';
        //test acquire lease duration no in bound
        try {
            $this->restProxy->acquireLease($container, $blob, $leaseId, 14);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(' The value for one of the HTTP headers is not in the correct format.', $message);
        try {
            $this->restProxy->acquireLease($container, $blob, $leaseId, 61);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains(' The value for one of the HTTP headers is not in the correct format.', $message);
        $result = $this->restProxy->acquireLease($container, $blob, $leaseId, 15);
        $this->assertEquals($leaseId, $result->getLeaseId());
        //test lease duration expire
        \sleep(15);
        $this->restProxy->deleteBlob($container, $blob);

        //re-configure
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);
        $this->restProxy->createPageBlob($container, $blob, 1024);
        $leaseId = Utilities::getGuid();
        $this->restProxy->acquireLease($container, $blob, $leaseId);
        //test change lease
        $newLeaseId = Utilities::getGuid();
        $result = $this->restProxy->changeLease($container, $blob, $leaseId, $newLeaseId);
        $options = new DeleteBlobOptions();
        $options->setLeaseId($newLeaseId);
        $this->restProxy->deleteBlob($container, $blob, $options);

        $result = $this->restProxy->listBlobs($container);
        $this->assertTrue(empty($result->getBlobs()));

        //test renew lease
        //re-configure
        $blob = BlobServiceFunctionalTestData::getInterestingBlobName($container);
        $this->restProxy->createPageBlob($container, $blob, 1024);
        $leaseId = Utilities::getGuid();
        $this->restProxy->acquireLease($container, $blob, $leaseId, 15);
        \sleep(15);
        $this->restProxy->renewLease($container, $blob, $leaseId);
        try {
            $this->restProxy->deleteBlob($container, $blob);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains('There is currently a lease on the blob and no lease ID was specified in the request.', $message);

        //test release lease
        $this->restProxy->releaseLease($container, $blob, $leaseId);
        //acquire a lease immediately after.
        $leaseId = Utilities::getGuid();
        $this->restProxy->acquireLease($container, $blob, $leaseId);
        try {
            $this->restProxy->deleteBlob($container, $blob);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains('There is currently a lease on the blob and no lease ID was specified in the request.', $message);

        //test break lease
        $result = $this->restProxy->breakLease($container, $blob, 10);
        $leaseId = Utilities::getGuid();
        try {
            $this->restProxy->acquireLease($container, $blob, $leaseId);
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        $this->assertContains('There is already a lease present.', $message);
        \sleep(10);
        $this->restProxy->acquireLease($container, $blob, $leaseId);
        $options = new DeleteBlobOptions();
        $options->setLeaseId($leaseId);
        $this->restProxy->deleteBlob($container, $blob, $options);

        $result = $this->restProxy->listBlobs($container);
        $this->assertTrue(empty($result->getBlobs()));
    }
}
