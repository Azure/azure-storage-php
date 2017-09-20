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
 * @package   MicrosoftAzure\Storage\Tests\Functional\Queue
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\Queue;

use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;
use MicrosoftAzure\Storage\Queue\Models\CreateMessageOptions;
use MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\ListQueuesOptions;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\QueueServiceOptions;

class QueueServiceFunctionalOptionsTest extends \PHPUnit_Framework_TestCase
{
    const INT_MAX_VALUE = 2147483647;
    const INT_MIN_VALUE = -2147483648;

    public function testCheckQueueServiceOptions()
    {
        $options = new QueueServiceOptions();
        $this->assertNull($options->getTimeout(), 'Default QueueServiceOptions->getTimeout should be null');
        $options->setTimeout(self::INT_MAX_VALUE);
        $this->assertEquals(self::INT_MAX_VALUE, $options->getTimeout(), 'Set QueueServiceOptions->getTimeout');
    }

    public function testCheckRetentionPolicy()
    {
        // Check that the default values of options are reasonable

        $rp = new RetentionPolicy();
        $this->assertNull($rp->getDays(), 'Default RetentionPolicy->getDays should be null');
        $this->assertNull($rp->getEnabled(), 'Default RetentionPolicy->getEnabled should be null');
        $rp->setDays(10);
        $rp->setEnabled(true);
        $this->assertEquals(10, $rp->getDays(), 'Set RetentionPolicy->getDays should be 10');
        $this->assertTrue($rp->getEnabled(), 'Set RetentionPolicy->getEnabled should be true');
    }

    public function testCheckLogging()
    {
        // Check that the default values of options are reasonable
        $rp = new RetentionPolicy();

        $l = new Logging();
        $this->assertNull($l->getRetentionPolicy(), 'Default Logging->getRetentionPolicy should be null');
        $this->assertNull($l->getVersion(), 'Default Logging->getVersion should be null');
        $this->assertNull($l->getDelete(), 'Default Logging->getDelete should be null');
        $this->assertNull($l->getRead(), 'Default Logging->getRead should be false');
        $this->assertNull($l->getWrite(), 'Default Logging->getWrite should be false');
        $l->setRetentionPolicy($rp);
        $l->setVersion('2.0');
        $l->setDelete(true);
        $l->setRead(true);
        $l->setWrite(true);

        $this->assertEquals($rp, $l->getRetentionPolicy(), 'Set Logging->getRetentionPolicy');
        $this->assertEquals('2.0', $l->getVersion(), 'Set Logging->getVersion');
        $this->assertTrue($l->getDelete(), 'Set Logging->getDelete should be true');
        $this->assertTrue($l->getRead(), 'Set Logging->getRead should be true');
        $this->assertTrue($l->getWrite(), 'Set Logging->getWrite should be true');
    }

    public function testCheckMetrics()
    {
        // Check that the default values of options are reasonable
        $rp = new RetentionPolicy();

        $m = new Metrics();
        $this->assertNull($m->getRetentionPolicy(), 'Default Metrics->getRetentionPolicy should be null');
        $this->assertNull($m->getVersion(), 'Default Metrics->getVersion should be null');
        $this->assertNull($m->getEnabled(), 'Default Metrics->getEnabled should be false');
        $this->assertNull($m->getIncludeAPIs(), 'Default Metrics->getIncludeAPIs should be null');
        $m->setRetentionPolicy($rp);
        $m->setVersion('2.0');
        $m->setEnabled(true);
        $m->setIncludeAPIs(true);
        $this->assertEquals($rp, $m->getRetentionPolicy(), 'Set Metrics->getRetentionPolicy');
        $this->assertEquals('2.0', $m->getVersion(), 'Set Metrics->getVersion');
        $this->assertTrue($m->getEnabled(), 'Set Metrics->getEnabled should be true');
        $this->assertTrue($m->getIncludeAPIs(), 'Set Metrics->getIncludeAPIs should be true');
    }

    public function testCheckServiceProperties()
    {
        // Check that the default values of options are reasonable
        $l = new Logging();
        $m = new Metrics();

        $sp = new ServiceProperties();
        $this->assertNull($sp->getLogging(), 'Default ServiceProperties->getLogging should not be null');
        $this->assertNull($sp->getHourMetrics(), 'Default ServiceProperties->getHourMetrics should not be null');

        $sp->setLogging($l);
        $sp->setHourMetrics($m);
        $this->assertEquals($sp->getLogging(), $l, 'Set ServiceProperties->getLogging');
        $this->assertEquals($sp->getHourMetrics(), $m, 'Set ServiceProperties->getHourMetrics');
    }

    public function testCheckListQueuesOptions()
    {
        $options = new ListQueuesOptions();
        $this->assertNull($options->getIncludeMetadata(), 'Default ListQueuesOptions->getIncludeMetadata');
        $this->assertEquals('', $options->getNextMarker(), 'Default ListQueuesOptions->getNextMarker');
        $this->assertEquals(0, $options->getMaxResults(), 'Default ListQueuesOptions->getMaxResults');
        $this->assertNull($options->getPrefix(), 'Default ListQueuesOptions->getPrefix');
        $this->assertNull($options->getTimeout(), 'Default ListQueuesOptions->getTimeout');
        $options->setIncludeMetadata(true);
        $options->setMarker('foo');
        $options->setMaxResults(-10);
        $options->setPrefix('bar');
        $options->setTimeout(self::INT_MAX_VALUE);
        $this->assertTrue($options->getIncludeMetadata(), 'Set ListQueuesOptions->getIncludeMetadata');
        $this->assertEquals('foo', $options->getNextMarker(), 'Set ListQueuesOptions->getMarker');
        $this->assertEquals(-10, $options->getMaxResults(), 'Set ListQueuesOptions->getMaxResults');
        $this->assertEquals('bar', $options->getPrefix(), 'Set ListQueuesOptions->getPrefix');
        $this->assertEquals(self::INT_MAX_VALUE, $options->getTimeout(), 'Set ListQueuesOptions->getTimeout');
    }

    public function testCheckCreateQueueOptions()
    {
        $options = new CreateQueueOptions();
        $this->assertNull($options->getMetadata(), 'Default CreateQueueOptions->getMetadata');
        $this->assertEquals(0, count($options->getMetadata()), 'Default CreateQueueOptions->getMetadata->size');
        $this->assertNull($options->getTimeout(), 'Default CreateQueueOptions->getTimeout');
        $metadata = array(
            'foo' => 'bar',
            'baz' => 'bat',
        );
        $options->setMetadata($metadata);
        $options->setTimeout(-10);
        $this->assertEquals($options->getMetadata(), $metadata, 'Set CreateQueueOptions->getMetadata');
        $this->assertEquals(2, count($options->getMetadata()), 'Set CreateQueueOptions->getMetadata->size');
        $this->assertEquals(-10, $options->getTimeout(), 'Set CreateQueueOptions->getTimeout');
        $options->addMetadata('aaa', 'bbb');
        $this->assertEquals(3, count($options->getMetadata()), 'Set CreateQueueOptions->getMetadata->size');
    }

    public function testCheckCreateMessageOptions()
    {
        $options = new CreateMessageOptions();
        $this->assertNull($options->getTimeout(), 'Default CreateMessageOptions->getTimeout');
        $this->assertNull($options->getTimeToLiveInSeconds(), 'Default CreateMessageOptions->getTimeToLiveInSeconds');
        $this->assertNull($options->getVisibilityTimeoutInSeconds(), 'Default CreateMessageOptions->getVisibilityTimeoutInSeconds');
        $options->setTimeout(self::INT_MAX_VALUE);
        $options->setTimeToLiveInSeconds(0);
        $options->setVisibilityTimeoutInSeconds(self::INT_MIN_VALUE);
        $this->assertEquals(self::INT_MAX_VALUE, $options->getTimeout(), 'Set CreateMessageOptions->getTimeout');
        $this->assertEquals(0, $options->getTimeToLiveInSeconds(), 'Set CreateMessageOptions->getTimeToLiveInSeconds');
        $this->assertEquals(self::INT_MIN_VALUE, $options->getVisibilityTimeoutInSeconds(), 'Set CreateMessageOptions->getVisibilityTimeoutInSeconds');
    }

    public function testCheckListMessagesOptions()
    {
        $options = new ListMessagesOptions();
        $this->assertNull($options->getTimeout(), 'Default ListMessagesOptions->getTimeout');
        $this->assertNull($options->getNumberOfMessages(), 'Default ListMessagesOptions->getNumberOfMessages');
        $this->assertNull($options->getVisibilityTimeoutInSeconds(), 'Default ListMessagesOptions->getVisibilityTimeoutInSeconds');
        $options->setTimeout(self::INT_MAX_VALUE);
        $options->setNumberOfMessages(0);
        $options->setVisibilityTimeoutInSeconds(self::INT_MIN_VALUE);
        $this->assertEquals(self::INT_MAX_VALUE, $options->getTimeout(), 'Set ListMessagesOptions->getTimeout');
        $this->assertEquals(0, $options->getNumberOfMessages(), 'Set ListMessagesOptions->getNumberOfMessages');
        $this->assertEquals(self::INT_MIN_VALUE, $options->getVisibilityTimeoutInSeconds(), 'Set ListMessagesOptions->getVisibilityTimeoutInSeconds');
    }

    public function testCheckPeekMessagesOptions()
    {
        $options = new PeekMessagesOptions();
        $this->assertNull($options->getTimeout(), 'Default PeekMessagesOptions->getTimeout');
        $this->assertNull($options->getNumberOfMessages(), 'Default PeekMessagesOptions->getNumberOfMessages');
        $options->setTimeout(self::INT_MAX_VALUE);
        $options->setNumberOfMessages(0);
        $this->assertEquals(self::INT_MAX_VALUE, $options->getTimeout(), 'Set PeekMessagesOptions->getTimeout');
        $this->assertEquals(0, $options->getNumberOfMessages(), 'Set PeekMessagesOptions->getNumberOfMessages');
    }
}
