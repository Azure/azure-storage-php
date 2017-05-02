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
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
 
namespace MicrosoftAzure\Storage\Common\Internal;

use MicrosoftAzure\Storage\Common\Internal\ConnectionStringParser;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * Represents the settings used to sign and access a request against the storage
 * service. For more information about storage service connection strings check this
 * page: http://msdn.microsoft.com/en-us/library/ee758697
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class StorageServiceSettings extends ServiceSettings
{
    private $_name;
    private $_key;
    private $_sas;
    private $_blobEndpointUri;
    private $_queueEndpointUri;
    private $_tableEndpointUri;
    private $_blobSecondaryEndpointUri;
    private $_queueSecondaryEndpointUri;
    private $_tableSecondaryEndpointUri;

    private static $_devStoreAccount;
    private static $_useDevelopmentStorageSetting;
    private static $_developmentStorageProxyUriSetting;
    private static $_defaultEndpointsProtocolSetting;
    private static $_accountNameSetting;
    private static $_accountKeySetting;
    private static $_sasTokenSetting;
    private static $_blobEndpointSetting;
    private static $_queueEndpointSetting;
    private static $_tableEndpointSetting;

    /**
     * If initialized or not
     * @internal
     */
    protected static $isInitialized = false;
    
    /**
     * Valid setting keys
     * @internal
     */
    protected static $validSettingKeys = array();
    
    /**
     * Initializes static members of the class.
     *
     * @return void
     */
    protected static function init()
    {
        self::$_useDevelopmentStorageSetting = self::setting(
            Resources::USE_DEVELOPMENT_STORAGE_NAME,
            'true'
        );
        
        self::$_developmentStorageProxyUriSetting = self::settingWithFunc(
            Resources::DEVELOPMENT_STORAGE_PROXY_URI_NAME,
            Validate::getIsValidUri()
        );
        
        self::$_defaultEndpointsProtocolSetting = self::setting(
            Resources::DEFAULT_ENDPOINTS_PROTOCOL_NAME,
            'http',
            'https'
        );
        
        self::$_accountNameSetting = self::setting(Resources::ACCOUNT_NAME_NAME);
        
        self::$_accountKeySetting = self::settingWithFunc(
            Resources::ACCOUNT_KEY_NAME,
            // base64_decode will return false if the $key is not in base64 format.
            function ($key) {
                $isValidBase64String = base64_decode($key, true);
                if ($isValidBase64String) {
                    return true;
                } else {
                    throw new \RuntimeException(
                        sprintf(Resources::INVALID_ACCOUNT_KEY_FORMAT, $key)
                    );
                }
            }
        );

        self::$_sasTokenSetting = self::setting(Resources::SAS_TOKEN_NAME);
        
        self::$_blobEndpointSetting = self::settingWithFunc(
            Resources::BLOB_ENDPOINT_NAME,
            Validate::getIsValidUri()
        );
        
        self::$_queueEndpointSetting = self::settingWithFunc(
            Resources::QUEUE_ENDPOINT_NAME,
            Validate::getIsValidUri()
        );
        
        self::$_tableEndpointSetting = self::settingWithFunc(
            Resources::TABLE_ENDPOINT_NAME,
            Validate::getIsValidUri()
        );
        
        self::$validSettingKeys[] = Resources::USE_DEVELOPMENT_STORAGE_NAME;
        self::$validSettingKeys[] = Resources::DEVELOPMENT_STORAGE_PROXY_URI_NAME;
        self::$validSettingKeys[] = Resources::DEFAULT_ENDPOINTS_PROTOCOL_NAME;
        self::$validSettingKeys[] = Resources::ACCOUNT_NAME_NAME;
        self::$validSettingKeys[] = Resources::ACCOUNT_KEY_NAME;
        self::$validSettingKeys[] = Resources::SAS_TOKEN_NAME;
        self::$validSettingKeys[] = Resources::BLOB_ENDPOINT_NAME;
        self::$validSettingKeys[] = Resources::QUEUE_ENDPOINT_NAME;
        self::$validSettingKeys[] = Resources::TABLE_ENDPOINT_NAME;
    }
    
    /**
     * Creates new storage service settings instance.
     *
     * @param string $name                      The storage service name.
     * @param string $key                       The storage service key.
     * @param string $blobEndpointUri           The storage service blob
     *                                          endpoint.
     * @param string $queueEndpointUri          The storage service queue
     *                                          endpoint.
     * @param string $tableEndpointUri          The storage service table
     *                                          endpoint.
     * @param string $blobSecondaryEndpointUri  The storage service secondary
     *                                          blob endpoint.
     * @param string $queueSecondaryEndpointUri The storage service secondary
     *                                          queue endpoint.
     * @param string $tableSecondaryEndpointUri The storage service secondary
     *                                          table endpoint.
     * @param string $sas                       The storage service SAS token.
     */
    public function __construct(
        $name,
        $key,
        $blobEndpointUri,
        $queueEndpointUri,
        $tableEndpointUri,
        $blobSecondaryEndpointUri = null,
        $queueSecondaryEndpointUri = null,
        $tableSecondaryEndpointUri = null,
        $sas = null
    ) {
        $this->_name                      = $name;
        $this->_key                       = $key;
        $this->_sas                       = $sas;
        $this->_blobEndpointUri           = $blobEndpointUri;
        $this->_queueEndpointUri          = $queueEndpointUri;
        $this->_tableEndpointUri          = $tableEndpointUri;
        $this->_blobSecondaryEndpointUri  = $blobSecondaryEndpointUri;
        $this->_queueSecondaryEndpointUri = $queueSecondaryEndpointUri;
        $this->_tableSecondaryEndpointUri = $tableSecondaryEndpointUri;
    }
    
    /**
     * Returns a StorageServiceSettings with development storage credentials using
     * the specified proxy Uri.
     *
     * @param string $proxyUri The proxy endpoint to use.
     *
     * @return StorageServiceSettings
     */
    private static function _getDevelopmentStorageAccount($proxyUri)
    {
        if (is_null($proxyUri)) {
            return self::developmentStorageAccount();
        }
        
        $scheme = parse_url($proxyUri, PHP_URL_SCHEME);
        $host   = parse_url($proxyUri, PHP_URL_HOST);
        $prefix = $scheme . "://" . $host;
        
        return new StorageServiceSettings(
            Resources::DEV_STORE_NAME,
            Resources::DEV_STORE_KEY,
            $prefix . ':10000/devstoreaccount1/',
            $prefix . ':10001/devstoreaccount1/',
            $prefix . ':10002/devstoreaccount1/',
            null
        );
    }
    
    /**
     * Gets a StorageServiceSettings object that references the development storage
     * account.
     *
     * @return StorageServiceSettings
     */
    public static function developmentStorageAccount()
    {
        if (is_null(self::$_devStoreAccount)) {
            self::$_devStoreAccount = self::_getDevelopmentStorageAccount(
                Resources::DEV_STORE_URI
            );
        }
        
        return self::$_devStoreAccount;
    }
    
    /**
     * Gets the default service endpoint using the specified protocol and account
     * name.
     *
     * @param string $scheme      The scheme of the service end point.
     * @param string $accountName The account name of the service.
     * @param string $dns         The service DNS.
     * @param bool   $isSecondary If generating secondary endpoint.
     *
     * @return string
     */
    private static function getServiceEndpoint(
        $scheme,
        $accountName,
        $dns,
        $isSecondary = false
    ) {
        if ($isSecondary) {
            $accountName .= Resources::SECONDARY_STRING;
        }
        return sprintf(
            Resources::SERVICE_URI_FORMAT,
            $scheme,
            $accountName,
            $dns
        );
    }
    
    /**
     * Creates StorageServiceSettings object given endpoints uri.
     *
     * @param array  $settings                  The service settings.
     * @param string $blobEndpointUri           The blob endpoint uri.
     * @param string $queueEndpointUri          The queue endpoint uri.
     * @param string $tableEndpointUri          The table endpoint uri.
     * @param string $blobSecondaryEndpointUri  The blob secondary endpoint uri.
     * @param string $queueSecondaryEndpointUri The queue secondary endpoint uri.
     * @param string $tableSecondaryEndpointUri The table secondary endpoint uri.
     *
     * @return StorageServiceSettings
     */
    private static function _createStorageServiceSettings(
        array $settings,
        $blobEndpointUri = null,
        $queueEndpointUri = null,
        $tableEndpointUri = null,
        $blobSecondaryEndpointUri = null,
        $queueSecondaryEndpointUri = null,
        $tableSecondaryEndpointUri = null
    ) {
        $blobEndpointUri  = Utilities::tryGetValueInsensitive(
            Resources::BLOB_ENDPOINT_NAME,
            $settings,
            $blobEndpointUri
        );
        $queueEndpointUri = Utilities::tryGetValueInsensitive(
            Resources::QUEUE_ENDPOINT_NAME,
            $settings,
            $queueEndpointUri
        );
        $tableEndpointUri = Utilities::tryGetValueInsensitive(
            Resources::TABLE_ENDPOINT_NAME,
            $settings,
            $tableEndpointUri
        );
        $accountName      = Utilities::tryGetValueInsensitive(
            Resources::ACCOUNT_NAME_NAME,
            $settings
        );
        $accountKey       = Utilities::tryGetValueInsensitive(
            Resources::ACCOUNT_KEY_NAME,
            $settings
        );
        $sasToken         = Utilities::tryGetValueInsensitive(
            Resources::SAS_TOKEN_NAME,
            $settings
        );
            
        return new StorageServiceSettings(
            $accountName,
            $accountKey,
            $blobEndpointUri,
            $queueEndpointUri,
            $tableEndpointUri,
            $blobSecondaryEndpointUri,
            $queueSecondaryEndpointUri,
            $tableSecondaryEndpointUri,
            $sasToken
        );
    }

    /**
     * Creates a StorageServiceSettings object from the given connection string.
     *
     * @param string $connectionString The storage settings connection string.
     *
     * @return StorageServiceSettings
     */
    public static function createFromConnectionString($connectionString)
    {
        $tokenizedSettings = self::parseAndValidateKeys($connectionString);
        
        // Devstore case
        $matchedSpecs = self::matchedSpecification(
            $tokenizedSettings,
            self::allRequired(self::$_useDevelopmentStorageSetting),
            self::optional(self::$_developmentStorageProxyUriSetting)
        );
        if ($matchedSpecs) {
            $proxyUri = Utilities::tryGetValueInsensitive(
                Resources::DEVELOPMENT_STORAGE_PROXY_URI_NAME,
                $tokenizedSettings
            );
            
            return self::_getDevelopmentStorageAccount($proxyUri);
        }
        
        // Automatic case
        $matchedSpecs = self::matchedSpecification(
            $tokenizedSettings,
            self::allRequired(
                self::$_defaultEndpointsProtocolSetting,
                self::$_accountNameSetting,
                self::$_accountKeySetting
            ),
            self::optional(
                self::$_blobEndpointSetting,
                self::$_queueEndpointSetting,
                self::$_tableEndpointSetting
            )
        );
        if ($matchedSpecs) {
            $scheme      = Utilities::tryGetValueInsensitive(
                Resources::DEFAULT_ENDPOINTS_PROTOCOL_NAME,
                $tokenizedSettings
            );
            $accountName = Utilities::tryGetValueInsensitive(
                Resources::ACCOUNT_NAME_NAME,
                $tokenizedSettings
            );
            return self::_createStorageServiceSettings(
                $tokenizedSettings,
                self::getServiceEndpoint(
                    $scheme,
                    $accountName,
                    Resources::BLOB_BASE_DNS_NAME
                ),
                self::getServiceEndpoint(
                    $scheme,
                    $accountName,
                    Resources::QUEUE_BASE_DNS_NAME
                ),
                self::getServiceEndpoint(
                    $scheme,
                    $accountName,
                    Resources::TABLE_BASE_DNS_NAME
                ),
                self::getServiceEndpoint(
                    $scheme,
                    $accountName,
                    Resources::BLOB_BASE_DNS_NAME,
                    true
                ),
                self::getServiceEndpoint(
                    $scheme,
                    $accountName,
                    Resources::QUEUE_BASE_DNS_NAME,
                    true
                ),
                self::getServiceEndpoint(
                    $scheme,
                    $accountName,
                    Resources::TABLE_BASE_DNS_NAME,
                    true
                )
            );
        }
        
        // Explicit case for AccountName/AccountKey combination
        $matchedSpecs = self::matchedSpecification(
            $tokenizedSettings,
            self::atLeastOne(
                self::$_blobEndpointSetting,
                self::$_queueEndpointSetting,
                self::$_tableEndpointSetting
            ),
            self::allRequired(
                self::$_accountNameSetting,
                self::$_accountKeySetting
            )
        );
        if ($matchedSpecs) {
            return self::_createStorageServiceSettings($tokenizedSettings);
        }

        // Explicit case for SAS token
        $matchedSpecs = self::matchedSpecification(
            $tokenizedSettings,
            self::atLeastOne(
                self::$_blobEndpointSetting,
                self::$_queueEndpointSetting,
                self::$_tableEndpointSetting
            ),
            self::allRequired(
                self::$_sasTokenSetting
            )
        );
        if ($matchedSpecs) {
            return self::_createStorageServiceSettings($tokenizedSettings);
        }
        
        self::noMatch($connectionString);
    }
    
    /**
     * Gets storage service name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets storage service key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Checks if there is a SAS token.
     *
     * @return boolean
     */
    public function hasSasToken()
    {
        return !empty($this->_sas);
    }

    /**
     * Gets storage service SAS token.
     *
     * @return string
     */
    public function getSasToken()
    {
        return $this->_sas;
    }
    
    /**
     * Gets storage service blob endpoint uri.
     *
     * @return string
     */
    public function getBlobEndpointUri()
    {
        return $this->_blobEndpointUri;
    }
    
    /**
     * Gets storage service queue endpoint uri.
     *
     * @return string
     */
    public function getQueueEndpointUri()
    {
        return $this->_queueEndpointUri;
    }

    /**
     * Gets storage service table endpoint uri.
     *
     * @return string
     */
    public function getTableEndpointUri()
    {
        return $this->_tableEndpointUri;
    }

    /**
     * Gets storage service secondary blob endpoint uri.
     *
     * @return string
     */
    public function getBlobSecondaryEndpointUri()
    {
        return $this->_blobSecondaryEndpointUri;
    }
    
    /**
     * Gets storage service secondary queue endpoint uri.
     *
     * @return string
     */
    public function getQueueSecondaryEndpointUri()
    {
        return $this->_queueSecondaryEndpointUri;
    }

    /**
     * Gets storage service secondary table endpoint uri.
     *
     * @return string
     */
    public function getTableSecondaryEndpointUri()
    {
        return $this->_tableSecondaryEndpointUri;
    }
}
