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

namespace MicrosoftAzure\Storage\Common\Internal\Authentication;


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
class SharedAccessSignatureHelper {

    protected $accountName;
    protected $accountKey;


    /**
     * Constructor.
     *
     * @param string $accountName the name of the storage account.
     * @param string $accountKey the shared key of the storage account
     *
     * @return
     * MicrosoftAzure\Storage\Common\Internal\Authentication\SharedAccessSignatureHelper
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
     * @param string $signedPermissions     Specifies the signed permissions for the account SAS.
     * @param string $signedService         Specifies the signed services accessible with the account SAS.
     * @param string $signedResourceType    Specifies the signed resource types that are accessible with the account SAS.
     * @param string $signedExpiracy        The time at which the shared access signature becomes invalid, in an ISO 8601 format.
     * @param string $signedStart           The time at which the SAS becomes valid, in an ISO 8601 format.
     * @param string $signedIP              Specifies an IP address or a range of IP addresses from which to accept requests.
     * @param string $signedProtocol        Specifies the protocol permitted for a request made with the account SAS.
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
        if(strlen($signedStart) > 0)
        {
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
        $stringToSign = utf8_encode(implode("\n", $parameters)."\n");

        // decode the account key from base64
        $decodedAccountKey = base64_decode($this->accountKey);
        
        // create the signature with hmac sha256
        $signature = hash_hmac("sha256", $stringToSign, $decodedAccountKey, true);

        // encode the signature as base64
        $base64Signature = base64_encode($signature);

        // return the signature
        return $base64Signature;
    }

    /**
     * Validates and sanitizes the signed service parameter
     *
     * @param string $signedService         Specifies the signed services accessible with the account SAS.
     *
     * @return string
     */
    private function validateAndSanitizeSignedService($signedService) {
        // validate signed service is not null or empty
        Validate::isString($signedService, 'signedService');
        Validate::notNullOrEmpty($signedService, 'signedService');

        // sanitize signed service
        $sanitizedSignedService = $this->removeDuplicateCharacters(strtolower($signedService));
        
        // The signed service should only be a combination of the letters b(lob) q(ueue) t(able) or f(ile)
        $signedServiceIsValid = preg_match("/^[bqtf]*$/", $sanitizedSignedService);
        if(!$signedServiceIsValid) {
            throw new \InvalidArgumentException(Resources::SIGNED_SERVICE_INVALID_VALIDATION_MSG);
        }

        return $sanitizedSignedService;
    }

    /**
     * Validates and sanitizes the signed resource type parameter
     *
     * @param string $signedResourceType    Specifies the signed resource types that are accessible with the account SAS.
     *
     * @return string
     */
    private function validateAndSanitizeSignedResourceType($signedResourceType) {
        // validate signed resource type is not null or empty
        Validate::isString($signedResourceType, 'signedResourceType');
        Validate::notNullOrEmpty($signedResourceType, 'signedResourceType');

        // sanitize signed resource type
        $sanitizedSignedResourceType = $this->removeDuplicateCharacters(strtolower($signedResourceType));
        
        // The signed resource type should only be a combination of the letters s(ervice) c(container) or o(bject)
        $signedResourceTypeIsValid = preg_match("/^[sco]*$/", $sanitizedSignedResourceType);
        if(!$signedResourceTypeIsValid) {
            throw new \InvalidArgumentException(Resources::SIGNED_RESOURCE_TYPE_INVALID_VALIDATION_MSG);
        }

        return $sanitizedSignedResourceType;
    }

    /**
     * Validates and sanitizes the signed permissions parameter
     *
     * @param string $signedPermissions     Specifies the signed permissions for the account SAS.

     * @return string
     */
    private function validateAndSanitizeSignedPermissions($signedPermissions) {
        // validate signed permissions are not null or empty
        Validate::isString($signedPermissions, 'signedPermissions');
        Validate::notNullOrEmpty($signedPermissions, 'signedPermissions');

        // sanitized signed permissions, service and resource type
        $sanitizedSignedPermissions = $this->removeDuplicateCharacters(strtolower($signedPermissions));

        $validPermissions = ['r', 'w', 'd', 'l', 'a', 'c', 'u', 'p'];
        $result = '';
        foreach ($validPermissions as $validPermission) {
            if (strpos($sanitizedSignedPermissions, $validPermission) !== false) {
                //append the valid permission to result.
                $result .= $validPermission;
                //remove all the character that represents the permission.
                $sanitizedSignedPermissions = str_replace(
                    $validPermission,
                    '',
                    $sanitizedSignedPermissions
                );
            }
        }

        if(strlen($result) == 0) {
            throw new \InvalidArgumentException(Resources::SIGNED_PERMISSIONS_INVALID_VALIDATION_MSG);
        }

        return $result;
    }

    /**
     * Validates and sanitizes the signed protocol parameter
     *
     * @param string $signedProtocol        Specifies the signed protocol for the account SAS.

     * @return string
     */
    private function validateAndSanitizeSignedProtocol($signedProtocol) {
        Validate::isString($signedProtocol, 'signedProtocol');
        // sanitize string
        $sanitizedSignedProtocol = strtolower($signedProtocol);
        if(strlen($sanitizedSignedProtocol) > 0) {
            if(strcmp($sanitizedSignedProtocol,"https") != 0 && strcmp($sanitizedSignedProtocol, "https,http") != 0) {
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
    private function strcontains($input, $toFind) {
        return strpos($input, $toFind) !== false;
    }

    /**
     * Removes duplicate characters from a string
     *
     * @param string $input        The input string.

     * @return string
     */
    private function removeDuplicateCharacters($input) {
        $inputAsArray = str_split($input);
        $deduplicated = array_unique($inputAsArray);
        $output = implode("", $deduplicated);
        return $output;
    }
}
