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
 * @package   MicrosoftAzure\Storage\Tests\Unit\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\unit\Common\Internal\Authentication;

use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Tests\framework\TestResources;
use MicrosoftAzure\Storage\Tests\Framework\ReflectionTestBase;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;

/**
* Unit tests for class SharedAccessSignatureHelper
*
* @category  Microsoft
* @package   MicrosoftAzure\Storage\Tests\Unit\Common
* @author    Azure Storage PHP SDK <dmsh@microsoft.com>
* @copyright 2017 Microsoft Corporation
* @license   https://github.com/azure/azure-storage-php/LICENSE
* @link      https://github.com/azure/azure-storage-php
*/
class SharedAccessSignatureHelperTest extends ReflectionTestBase
{
    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    */
    public function testConstruct()
    {
        // Setup
        $accountName = TestResources::ACCOUNT_NAME;
        $accountKey = TestResources::KEY4;

        // Test
        $sasHelper = new SharedAccessSignatureHelper($accountName, $accountKey);

        // Assert
        $this->assertNotNull($sasHelper);

        return $sasHelper;
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::validateAndSanitizeSignedService
     */
    public function testValidateAndSanitizeSignedService()
    {
        // Setup
        $sasHelper = $this->testConstruct();
        $validateAndSanitizeSignedService = self::getMethod('validateAndSanitizeSignedService', $sasHelper);

        $authorizedSignedService = array();
        $authorizedSignedService[] = "BqtF";
        $authorizedSignedService[] = "bQtF";
        $authorizedSignedService[] = "fqTb";
        $authorizedSignedService[] = "ffqq";
        $authorizedSignedService[] = "BbbB";
        
        $expected = array();
        $expected[] = "bqtf";
        $expected[] = "bqtf";
        $expected[] = "bqtf";
        $expected[] = "qf";
        $expected[] = "b";
        
        for ($i = 0; $i < count($authorizedSignedService); $i++) {
            // Test
            $actual = $validateAndSanitizeSignedService->invokeArgs($sasHelper, array($authorizedSignedService[$i]));

            // Assert
            $this->assertEquals($expected[$i], $actual);
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::validateAndSanitizeSignedService
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The string should only be a combination of
     */
    public function testValidateAndSanitizeSignedServiceThrowsException()
    {
        // Setup
        $sasHelper = $this->testConstruct();
        $validateAndSanitizeSignedService = self::getMethod('validateAndSanitizeSignedService', $sasHelper);
        $unauthorizedSignedService = "BqTfG";

        // Test: should throw an InvalidArgumentException
        $validateAndSanitizeSignedService->invokeArgs($sasHelper, array($unauthorizedSignedService));
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::validateAndSanitizeSignedResourceType
     */
    public function testValidateAndSanitizeSignedResourceType()
    {
        // Setup
        $sasHelper = $this->testConstruct();
        $validateAndSanitizeSignedResourceType = self::getMethod('validateAndSanitizeSignedResourceType', $sasHelper);

        $authorizedSignedResourceType = array();
        $authorizedSignedResourceType[] = "sCo";
        $authorizedSignedResourceType[] = "Ocs";
        $authorizedSignedResourceType[] = "OOsCc";
        $authorizedSignedResourceType[] = "OOOoo";
        
        $expected = array();
        $expected[] = "sco";
        $expected[] = "sco";
        $expected[] = "sco";
        $expected[] = "o";
        
        for ($i = 0; $i < count($authorizedSignedResourceType); $i++) {
            // Test
            $actual = $validateAndSanitizeSignedResourceType->invokeArgs(
                $sasHelper,
                array($authorizedSignedResourceType[$i])
            );

            // Assert
            $this->assertEquals($expected[$i], $actual);
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::validateAndSanitizeSignedResourceType
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The string should only be a combination of
     */
    public function testValidateAndSanitizeSignedResourceTypeThrowsException()
    {
        // Setup
        $sasHelper = $this->testConstruct();
        $validateAndSanitizeSignedResourceType = self::getMethod('validateAndSanitizeSignedResourceType', $sasHelper);

        $unauthorizedSignedResourceType = "oscB";

        // Test: should throw an InvalidArgumentException
        $validateAndSanitizeSignedResourceType->invokeArgs($sasHelper, array($unauthorizedSignedResourceType));
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::validateAndSanitizeSignedProtocol
     */
    public function testValidateAndSanitizeSignedProtocol()
    {
        // Setup
        $sasHelper = $this->testConstruct();
        $validateAndSanitizeSignedProtocol = self::getMethod('validateAndSanitizeSignedProtocol', $sasHelper);

        $authorizedSignedProtocol = array();
        $authorizedSignedProtocol[] = "hTTpS";
        $authorizedSignedProtocol[] = "httpS,hTtp";
        
        $expected = array();
        $expected[] = "https";
        $expected[] = "https,http";
        
        for ($i = 0; $i < count($authorizedSignedProtocol); $i++) {
            // Test
            $actual = $validateAndSanitizeSignedProtocol->invokeArgs($sasHelper, array($authorizedSignedProtocol[$i]));

            // Assert
            $this->assertEquals($expected[$i], $actual);
        }
    }

    /**
     * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::validateAndSanitizeSignedProtocol
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is invalid
     */
    public function testValidateAndSanitizeSignedProtocolThrowsException()
    {
        // Setup
        $sasHelper = $this->testConstruct();
        $validateAndSanitizeSignedProtocol = self::getMethod('validateAndSanitizeSignedProtocol', $sasHelper);
        
        $unauthorizedSignedProtocol = "htTp";

        // Test: should throw an InvalidArgumentException
        $validateAndSanitizeSignedProtocol->invokeArgs($sasHelper, array($unauthorizedSignedProtocol));
    }

    /**
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::__construct
    * @covers MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper::generateAccountSharedAccessSignatureToken
    */
    public function testGenerateAccountSharedAccessSignatureToken()
    {
        // Setup
        $accountName = TestResources::ACCOUNT_NAME;
        $accountKey = TestResources::KEY4;

        // Test
        $sasHelper = new SharedAccessSignatureHelper($accountName, $accountKey);

        // create the test cases
        $testCases = TestResources::getSASInterestingUTCases();
        
        foreach ($testCases as $testCase) {
            // test
            $actualSignature = $sasHelper->generateAccountSharedAccessSignatureToken(
                $testCase[0],
                $testCase[1],
                $testCase[2],
                $testCase[3],
                $testCase[4],
                $testCase[5],
                $testCase[6],
                $testCase[7]
            );

            // assert
            $this->assertEquals($testCase[8], urlencode($actualSignature));
        }
    }

    public function testGenerateServiceSharedAccessSignatureToken()
    {
        // Setup
        $accountName = "phptests";
        $accountKey = "WaZvixrkMok53QDmj8Tc99+BV6vO9cCOJNsdm+wD9QVEScwl8c1eYPcQ182ndNFqxX1+SEKs18SmOxh8OpzIUg==";

        // Test
        $sasHelper = new SharedAccessSignatureHelper($accountName, $accountKey);

        // create the test cases
        $testCases = GenerateServiceSASTestCase::BuildTestCases();

        foreach ($testCases as $testCase) {
            // test
            $actualSignature = $sasHelper->generateServiceSharedAccessSignatureToken(
                $testCase->getSignedPermission(),
                $testCase->getSignedService(),
                $testCase->getSignedResource(),
                $testCase->getSignedExpiracy(),
                $testCase->getSignedStart(),
                $testCase->getSignedIdentifier(),
                $testCase->getSignedIP(),
                $testCase->getSignedProtocol(),
                $testCase->getCacheControl(),
                $testCase->getContentDisposition(),
                $testCase->getContentEncoding(),
                $testCase->getContentLanguage(),
                $testCase->getContentType(),
                $testCase->getStartingPartitionKey(),
                $testCase->getStartingRowKey(),
                $testCase->getEndingPartitionKey(),
                $testCase->getEndingRowKey()
            );

            // assert
            $this->assertEquals($testCase->getExpectedSignature(), urlencode($actualSignature));
        }
    }
}

class GenerateAccountSASTestCase {

    protected $signedVersion;
    protected $signedService;
    protected $signedResourceType;
    protected $signedPermission;
    protected $signedExpiracy;
    protected $signedStart;
    protected $signedProtocol;
    protected $signedIP;
    protected $expectedSignature;

    public function __construct(
        $signedVersion,
        $signedService,
        $signedResourceType,
        $signedPermission,
        $signedExpiracy,
        $signedStart,
        $signedProtocol,
        $signedIP,
        $expectedSignature
    ) {
        $this->signedVersion = $signedVersion;
        $this->signedService = $signedService;
        $this->signedResourceType = $signedResourceType;
        $this->signedPermission = $signedPermission;
        $this->signedExpiracy = $signedExpiracy;
        $this->signedStart = $signedStart;
        $this->signedProtocol = $signedProtocol;
        $this->signedIP = $signedIP;
        $this->expectedSignature = $expectedSignature;
    }

    public function getSignedVersion() {
        return $this->signedVersion;
    }

    public function getSignedService() {
        return $this->signedService;
    }
    
    public function getSignedResourceType() {
        return $this->signedResourceType;
    }
    
    public function getSignedPermission() {
        return $this->signedPermission;
    }
    
    public function getSignedExpiracy() {
        return $this->signedExpiracy;
    }
    
    public function getSignedStart() {
        return $this->signedStart;
    }
    
    public function getSignedProtocol() {
        return $this->signedProtocol;
    }
    
    public function getSignedIP() {
        return $this->signedIP;
    }
    
    public function getExpectedSignature() {
        return $this->expectedSignature;
    }

    public static function BuildTestCases() {
        $testCases = array();

        // ?sv=2016-05-31&ss=bfqt&srt=sco&sp=rwdlacup&se=2017-03-24T21:14:01Z&st=2017-03-17T13:14:01Z&spr=https&sig=ZpEYbkT%2B9NJTYyMIuFnXQ9RzOehYF1mjnsk00B%2FX1nw%3D
        $testCases[] = new GenerateAccountSASTestCase(
            "2016-05-31", // signedVersion
            "bfqt", // signedService
            "sco", // signedResourceType
            "rwdlacup", // signedPermission
            "2017-03-24T21:14:01Z", // signedExpiracy
            "2017-03-17T13:14:01Z", // signedStart
            "https", // signedProtocol
            "", // signedIP
            "ZpEYbkT%2B9NJTYyMIuFnXQ9RzOehYF1mjnsk00B%2FX1nw%3D" // expectedSignature
        );

        // ?sv=2016-05-31&ss=bfqt&srt=sco&sp=rwdlacup&se=2017-03-24T21:14:01Z&st=2017-03-17T13:14:01Z&sip=168.1.5.65&spr=https,http&sig=GZcWRjLJk%2FJSbM9zKb1XufTt2OueTSSgwsa03nYn5yM%3D
        $testCases[] = new GenerateAccountSASTestCase(
            "2016-05-31", // signedVersion
            "bfqt", // signedService
            "sco", // signedResourceType
            "rwdlacup", // signedPermission
            "2017-03-24T21:14:01Z", // signedExpiracy
            "2017-03-17T13:14:01Z", // signedStart
            "https,http", // signedProtocol
            "168.1.5.65", // signedIP
            "GZcWRjLJk%2FJSbM9zKb1XufTt2OueTSSgwsa03nYn5yM%3D" // expectedSignature
        );

        // ?sv=2016-05-31&ss=bf&srt=s&sp=rw&se=2017-03-24T00:00:00Z&st=2017-03-17T00:00:00Z&spr=https&sig=1%2BAozefG5VZDx9XorEGrAjOiTS8dX%2BJelK5SW91Zvq0%3D
        $testCases[] = new GenerateAccountSASTestCase(
            "2016-05-31", // signedVersion
            "bf", // signedService
            "s", // signedResourceType
            "rw", // signedPermission
            "2017-03-24T00:00:00Z", // signedExpiracy
            "2017-03-17T00:00:00Z", // signedStart
            "https", // signedProtocol
            "", // signedIP
            "1%2BAozefG5VZDx9XorEGrAjOiTS8dX%2BJelK5SW91Zvq0%3D" // expectedSignature
        );

        // ?sv=2016-05-31&ss=q&srt=o&sp=up&se=2017-03-24T00:00:00Z&st=2017-03-17T00:00:00Z&spr=https&sig=k1BKI65TdXs7rdAJiqDSJ6wYHjfJD0CJgplvOyqBK7Y%3D
        $testCases[] = new GenerateAccountSASTestCase(
            "2016-05-31", // signedVersion
            "q", // signedService
            "o", // signedResourceType
            "up", // signedPermission
            "2017-03-24T00:00:00Z", // signedExpiracy
            "2017-03-17T00:00:00Z", // signedStart
            "https", // signedProtocol
            "", // signedIP
            "k1BKI65TdXs7rdAJiqDSJ6wYHjfJD0CJgplvOyqBK7Y%3D" // expectedSignature
        );

        return $testCases;
    }
}

class GenerateServiceSASTestCase extends GenerateAccountSASTestCase
{
    protected $signedResource;
    protected $signedIdentifier;
    protected $cacheControl;
    protected $contentDisposition;
    protected $contentEncoding;
    protected $contentLanguage;
    protected $contentType;
    protected $startingPartitionKey;
    protected $startingRowKey;
    protected $endingPartitionKey;
    protected $endingRowKey;

    public function __construct(
        $signedPermissions,
        $signedService,
        $signedResource,
        $signedExpiry,
        $signedStart,
        $signedIdentifier,
        $signedIP,
        $signedProtocol,
        $cacheControl,
        $contentDisposition,
        $contentEncoding,
        $contentLanguage,
        $contentType,
        $startingPartitionKey,
        $startingRowKey,
        $endingPartitionKey,
        $endingRowKey,
        $expectedSignature
    ) {
        parent::__construct(
            null,
            $signedService,
            null,
            $signedPermissions,
            $signedExpiry,
            $signedStart,
            $signedProtocol,
            $signedIP,
            $expectedSignature
        );

        $this->signedResource = $signedResource;
        $this->signedIdentifier = $signedIdentifier;
        $this->cacheControl = $cacheControl;
        $this->contentDisposition = $contentDisposition;
        $this->contentEncoding = $contentEncoding;
        $this->contentLanguage = $contentLanguage;
        $this->contentType = $contentType;
        $this->startingPartitionKey = $startingPartitionKey;
        $this->startingRowKey = $startingRowKey;
        $this->endingPartitionKey = $endingPartitionKey;
        $this->endingRowKey = $endingRowKey;
    }

    public function getSignedResource()
    {
        return $this->signedResource;
    }

    public function getSignedIdentifier()
    {
        return $this->signedIdentifier;
    }

    public function getCacheControl()
    {
        return $this->cacheControl;
    }

    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getStartingPartitionKey()
    {
        return $this->startingPartitionKey;
    }

    public function getStartingRowKey()
    {
        return $this->startingRowKey;
    }

    public function getEndingPartitionKey()
    {
        return $this->endingPartitionKey;
    }

    public function getEndingRowKey()
    {
        return $this->endingRowKey;
    }

    public static function BuildTestCases() {
        $testCases = array();

        $testCases[] = new GenerateServiceSASTestCase(
            "rwdlacup", // signedPermission
            "b", // signedService
            "container/blob_dir/blob_name", // signedResource
            "2017-03-24T21:14:01Z", // signedExpiry
            "2017-03-17T13:14:01Z", // signedStart
            "mySignedIdentifier", // signedIdentifier
            "127.0.0.1", // signedIP
            "https,http", // signedProtocol
            "no-cache", // cacheControl
            "attachment", // contentDisposition
            "gzip", // contentEncoding
            "en-CA", // contentLanguage
            "application/json", // contentType
            "", // startingPartitionKey
            "", // startingRowKey
            "", // endingPartitionKey
            "", // endingRowKey
            "NtDAJiCjJbdEAHAz5OpZRrI4bnYCCDzb%2FNiYBHmXOhI%3D" // expectedSignature
        );

        $testCases[] = new GenerateServiceSASTestCase(
            "rwdlacup", // signedPermission
            "f", // signedService
            "shared/file.mp3", // signedResource
            "2017-03-24T21:14:01Z", // signedExpiry
            "2017-03-17T13:14:01Z", // signedStart
            "mySignedIdentifier", // signedIdentifier
            "127.0.0.1", // signedIP
            "https,http", // signedProtocol
            "no-cache", // cacheControl
            "attachment", // contentDisposition
            "gzip", // contentEncoding
            "en-CA", // contentLanguage
            "application/json", // contentType
            "", // startingPartitionKey
            "", // startingRowKey
            "", // endingPartitionKey
            "", // endingRowKey
            "rbvdhGQLCDl0aRmRyY94%2B07G49fFGJe7IFvhAy3t5S8%3D" // expectedSignature
        );

        $testCases[] = new GenerateServiceSASTestCase(
            "rwdlacup", // signedPermission
            "t", // signedService
            "tableName", // signedResource
            "2017-03-24T21:14:01Z", // signedExpiry
            "2017-03-17T13:14:01Z", // signedStart
            "mySignedIdentifier", // signedIdentifier
            "127.0.0.1", // signedIP
            "https,http", // signedProtocol
            "", // cacheControl
            "", // contentDisposition
            "", // contentEncoding
            "", // contentLanguage
            "", // contentType
            "startingPartitionKey", // startingPartitionKey
            "startingRowKey", // startingRowKey
            "endingPartitionKey", // endingPartitionKey
            "endingRowKey", // endingRowKey
            "xN6K8SohzCvRyQ4baOEyzKQUzwsQt%2BysBR5Yrdk1Gro%3D" // expectedSignature
        );

        $testCases[] = new GenerateServiceSASTestCase(
            "rwdlacup", // signedPermission
            "q", // signedService
            "queueName", // signedResource
            "2017-03-24T21:14:01Z", // signedExpiry
            "2017-03-17T13:14:01Z", // signedStart
            "mySignedIdentifier", // signedIdentifier
            "127.0.0.1", // signedIP
            "https,http", // signedProtocol
            "", // cacheControl
            "", // contentDisposition
            "", // contentEncoding
            "", // contentLanguage
            "", // contentType
            "", // startingPartitionKey
            "", // startingRowKey
            "", // endingPartitionKey
            "", // endingRowKey
            "%2BrQ1OVji4IdsOEWsalEvuRf3u6b2jSTsLJG1nq4yv%2B4%3D" // expectedSignature
        );

        return $testCases;
    }
}
