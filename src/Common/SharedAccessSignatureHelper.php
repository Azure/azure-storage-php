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
 * @package   MicrosoftAzure\Storage\Common\Internal\Authentication
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common;

use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Validate;

/**
 * Provides methods to generate Azure Storage Shared Access Signature
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal\Authentication
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class SharedAccessSignatureHelper
{
    protected $accountName;
    protected $accountKey;

    /**
     * Constructor.
     *
     * @param string $accountName the name of the storage account.
     * @param string $accountKey the shared key of the storage account
     *
     * @return
     * MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper
     */
    public function __construct($accountName, $accountKey)
    {
        Validate::isString($accountName, 'accountName');
        Validate::notNullOrEmpty($accountName, 'accountName');

        Validate::isString($accountKey, 'accountKey');
        Validate::notNullOrEmpty($accountKey, 'accountKey');

        $this->accountName = urldecode($accountName);
        $this->accountKey = $accountKey;
    }

    /**
     * Generates a shared access signature at the account level.
     *
     * @param string $signedVersion         Specifies the signed version to use.
     * @param string $signedPermissions     Specifies the signed permissions for
     *                                      the account SAS.
     * @param string $signedService         Specifies the signed services
     *                                      accessible with the account SAS.
     * @param string $signedResourceType    Specifies the signed resource types
     *                                      that are accessible with the account
     *                                      SAS.
     * @param string $signedExpiracy        The time at which the shared access
     *                                      signature becomes invalid, in an ISO
     *                                      8601 format.
     * @param string $signedStart           The time at which the SAS becomes
     *                                      valid, in an ISO 8601 format.
     * @param string $signedIP              Specifies an IP address or a range
     *                                      of IP addresses from which to accept
     *                                      requests.
     * @param string $signedProtocol        Specifies the protocol permitted for
     *                                      a request made with the account SAS.
     *
     * @see Constructing an account SAS at
     *      https://docs.microsoft.com/en-us/rest/api/storageservices/fileservices/constructing-an-account-sas
     *
     * @return string
     */
    public function generateAccountSharedAccessSignatureToken(
        $signedVersion,
        $signedPermissions,
        $signedService,
        $signedResourceType,
        $signedExpiracy,
        $signedStart = "",
        $signedIP = "",
        $signedProtocol = ""
    ) {
        // check that version is valid
        Validate::isString($signedVersion, 'signedVersion');
        Validate::notNullOrEmpty($signedVersion, 'signedVersion');
        Validate::isDateString($signedVersion, 'signedVersion');

        // validate and sanitize signed service
        $signedService = $this->validateAndSanitizeSignedService($signedService);

        // validate and sanitize signed resource type
        $signedResourceType = $this->validateAndSanitizeSignedResourceType($signedResourceType);
        
        // validate and sanitize signed permissions
        $signedPermissions = $this->validateAndSanitizeSignedPermissions($signedPermissions);

        // check that expiracy is valid
        Validate::isString($signedExpiracy, 'signedExpiracy');
        Validate::notNullOrEmpty($signedExpiracy, 'signedExpiracy');
        Validate::isDateString($signedExpiracy, 'signedExpiracy');

        // check that signed start is valid
        Validate::isString($signedStart, 'signedStart');
        if (strlen($signedStart) > 0) {
            Validate::isDateString($signedStart, 'signedStart');
        }

        // check that signed IP is valid
        Validate::isString($signedIP, 'signedIP');

        // validate and sanitize signed protocol
        $signedProtocol = $this->validateAndSanitizeSignedProtocol($signedProtocol);

        // construct an array with the parameters to generate the shared access signature at the account level
        $parameters = array();
        $parameters[] = $this->accountName;
        $parameters[] = urldecode($signedPermissions);
        $parameters[] = urldecode($signedService);
        $parameters[] = urldecode($signedResourceType);
        $parameters[] = urldecode($signedStart);
        $parameters[] = urldecode($signedExpiracy);
        $parameters[] = urldecode($signedIP);
        $parameters[] = urldecode($signedProtocol);
        $parameters[] = urldecode($signedVersion);

        // implode the parameters into a string
        $stringToSign = utf8_encode(implode("\n", $parameters) . "\n");

        // decode the account key from base64
        $decodedAccountKey = base64_decode($this->accountKey);
        
        // create the signature with hmac sha256
        $signature = hash_hmac("sha256", $stringToSign, $decodedAccountKey, true);

        // encode the signature as base64 and url encode.
        $sig = urlencode(base64_encode($signature));

        //adding all the components for account SAS together.
        $sas  = 'sv=' . $signedVersion;
        $sas .= '&ss=' . $signedService;
        $sas .= '&srt=' . $signedResourceType;
        $sas .= '&sp=' . $signedPermissions;
        $sas .= '&se=' . $signedExpiracy;
        $sas .= $signedStart === ''? '' : '&st=' . $signedStart;
        $sas .= $signedIP === ''? '' : '&sip=' . $signedIP;
        $sas .= '&spr=' . $signedProtocol;
        $sas .= '&sig=' . $sig;

        // return the signature
        return $sas;
    }

    /**
     * Validates and sanitizes the signed service parameter
     *
     * @param string $signedService Specifies the signed services accessible
     *                              with the account SAS.
     *
     * @return string
     */
    private function validateAndSanitizeSignedService($signedService)
    {
        // validate signed service is not null or empty
        Validate::isString($signedService, 'signedService');
        Validate::notNullOrEmpty($signedService, 'signedService');

        // The signed service should only be a combination of the letters b(lob) q(ueue) t(able) or f(ile)
        $validServices = ['b', 'q', 't', 'f'];

        return $this->validateAndSanitizeStringWithArray(
            strtolower($signedService),
            $validServices
        );
    }

    /**
     * Validates and sanitizes the signed resource type parameter
     *
     * @param string $signedResourceType    Specifies the signed resource types
     *                                      that are accessible with the account
     *                                      SAS.
     *
     * @return string
     */
    private function validateAndSanitizeSignedResourceType($signedResourceType)
    {
        // validate signed resource type is not null or empty
        Validate::isString($signedResourceType, 'signedResourceType');
        Validate::notNullOrEmpty($signedResourceType, 'signedResourceType');

        // The signed resource type should only be a combination of the letters s(ervice) c(container) or o(bject)
        $validResourceTypes = ['s', 'c', 'o'];

        return $this->validateAndSanitizeStringWithArray(
            strtolower($signedResourceType),
            $validResourceTypes
        );
    }

    /**
     * Validates and sanitizes the signed permissions parameter
     *
     * @param string $signedPermissions Specifies the signed permissions for the
     *                                  account SAS.

     * @return string
     */
    private function validateAndSanitizeSignedPermissions($signedPermissions)
    {
        // validate signed permissions are not null or empty
        Validate::isString($signedPermissions, 'signedPermissions');
        Validate::notNullOrEmpty($signedPermissions, 'signedPermissions');

        $validPermissions = ['r', 'w', 'd', 'l', 'a', 'c', 'u', 'p'];

        return $this->validateAndSanitizeStringWithArray(
            strtolower($signedPermissions),
            $validPermissions
        );
    }

    /**
     * Validates and sanitizes the signed protocol parameter
     *
     * @param string $signedProtocol Specifies the signed protocol for the
     *                               account SAS.

     * @return string
     */
    private function validateAndSanitizeSignedProtocol($signedProtocol)
    {
        Validate::isString($signedProtocol, 'signedProtocol');
        // sanitize string
        $sanitizedSignedProtocol = strtolower($signedProtocol);
        if (strlen($sanitizedSignedProtocol) > 0) {
            if (strcmp($sanitizedSignedProtocol, "https") != 0 && strcmp($sanitizedSignedProtocol, "https,http") != 0) {
                throw new \InvalidArgumentException(Resources::SIGNED_PROTOCOL_INVALID_VALIDATION_MSG);
            }
        }

        return $sanitizedSignedProtocol;
    }

    /**
     * Checks if a string contains an other string
     *
     * @param string $input        The input to test.
     * @param string $toFind       The string to find in the input.

     * @return bool
     */
    private function strcontains($input, $toFind)
    {
        return strpos($input, $toFind) !== false;
    }

    /**
     * Removes duplicate characters from a string
     *
     * @param string $input        The input string.

     * @return string
     */
    private function validateAndSanitizeStringWithArray($input, array $array)
    {
        $result = '';
        foreach ($array as $value) {
            if (strpos($input, $value) !== false) {
                //append the valid permission to result.
                $result .= $value;
                //remove all the character that represents the permission.
                $input = str_replace(
                    $value,
                    '',
                    $input
                );
            }
        }

        Validate::isTrue(
            strlen($input) == '',
            sprintf(
                Resources::STRING_NOT_WITH_GIVEN_COMBINATION,
                implode(', ', $array)
            )
        );
        return $result;
    }
}
