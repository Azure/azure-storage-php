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
 * @package   MicrosoftAzure\Storage\Queue
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Queue;

use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Queue\Internal\QueueResources as Resources;

/**
 * Provides methods to generate Azure Storage Shared Access Signature
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Queue
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2017 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class QueueSharedAccessSignatureHelper extends SharedAccessSignatureHelper
{
    /**
     * Constructor.
     *
     * @param string $accountName the name of the storage account.
     * @param string $accountKey the shared key of the storage account
     *
     */
    public function __construct($accountName, $accountKey)
    {
        parent::__construct($accountName, $accountKey);
    }

    /**
     * Generates a queue service shared access signature.
     *
     * This only supports version 2015-04-05 and later.
     *
     * @param  string           $queueName          The name of the queue.
     * @param  string           $signedPermissions  Signed permissions.
     * @param  \Datetime|string $signedExpiry       Signed expiry date.
     * @param  \Datetime|string $signedStart        Signed start date.
     * @param  string           $signedIdentifier   Signed identifier.
     * @param  string           $signedIP           Signed IP address.
     * @param  string           $signedProtocol     Signed protocol.
     *
     * @see Constructing an service SAS at
     * https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
     * @return string
     */
    public function generateQueueServiceSharedAccessSignatureToken(
        $queueName,
        $signedPermissions,
        $signedExpiry,
        $signedStart = "",
        $signedIP = "",
        $signedProtocol = "",
        $signedIdentifier = ""
    ) {
        // check that queue name is valid.
        Validate::notNullOrEmpty($queueName, 'queueName');
        Validate::canCastAsString($queueName, 'queueName');

        // validate and sanitize signed permissions
        $this->validateAndSanitizeStringWithArray(
            strtolower($signedPermissions),
            Resources::ACCESS_PERMISSIONS[Resources::RESOURCE_TYPE_QUEUE]
        );

        // check that expiry is valid
        if ($signedExpiry instanceof \Datetime) {
            $signedExpiry = Utilities::isoDate($signedExpiry);
        }
        Validate::notNullOrEmpty($signedExpiry, 'signedExpiry');
        Validate::canCastAsString($signedExpiry, 'signedExpiry');
        Validate::isDateString($signedExpiry, 'signedExpiry');

        // check that signed start is valid
        if ($signedStart instanceof \Datetime) {
            $signedStart = Utilities::isoDate($signedStart);
        }
        Validate::canCastAsString($signedStart, 'signedStart');
        if (strlen($signedStart) > 0) {
            Validate::isDateString($signedStart, 'signedStart');
        }

        // check that signed IP is valid
        Validate::canCastAsString($signedIP, 'signedIP');
        // validate and sanitize signed protocol
        $signedProtocol = $this->validateAndSanitizeSignedProtocol($signedProtocol);
        // check that signed identifier is valid
        Validate::canCastAsString($signedIdentifier, 'signedIdentifier');
        Validate::isTrue(
            strlen($signedIdentifier) <= 64,
            sprintf(Resources::INVALID_STRING_LENGTH, 'signedIdentifier', 'maximum 64')
        );

        // construct an array with the parameters to generate the shared access signature at the account level
        $parameters = array();
        $parameters[] = $signedPermissions;
        $parameters[] = $signedStart;
        $parameters[] = $signedExpiry;
        $parameters[] = static::generateCanonicalResource(
            $this->accountName,
            Resources::RESOURCE_TYPE_QUEUE,
            $queueName
        );
        $parameters[] = $signedIdentifier;
        $parameters[] = $signedIP;
        $parameters[] = $signedProtocol;
        $parameters[] = Resources::STORAGE_API_LATEST_VERSION;

        // implode the parameters into a string
        $stringToSign = utf8_encode(implode("\n", $parameters));
        // decode the account key from base64
        $decodedAccountKey = base64_decode($this->accountKey);
        // create the signature with hmac sha256
        $signature = hash_hmac("sha256", $stringToSign, $decodedAccountKey, true);
        // encode the signature as base64
        $sig = urlencode(base64_encode($signature));

        $buildOptQueryStr = function ($string, $abrv) {
            return $string === '' ? '' : $abrv . $string;
        };
        //adding all the components for account SAS together.
        $sas  = 'sv='    . Resources::STORAGE_API_LATEST_VERSION;
        $sas .= $buildOptQueryStr($signedStart, '&st=');
        $sas .= '&se='   . $signedExpiry;
        $sas .= '&sp='   . $signedPermissions;
        $sas .= $buildOptQueryStr($signedIP, '&sip=');
        $sas .= $buildOptQueryStr($signedProtocol, '&spr=');
        $sas .= $buildOptQueryStr($signedIdentifier, '&si=');
        $sas .= '&sig='  . $sig;

        // return the signature
        return $sas;

    }
}
