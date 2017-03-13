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
 * @package   MicrosoftAzure\Storage\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
use MicrosoftAzure\Storage\Common\Internal\Serialization\XmlSerializer;
use MicrosoftAzure\Storage\Common\Internal\Authentication\SharedAccessSignatureAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\Authentication\SharedKeyAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\Authentication\TableSharedKeyLiteAuthScheme;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Queue\QueueRestProxy;
use MicrosoftAzure\Storage\Table\TableRestProxy;
use MicrosoftAzure\Storage\Table\Internal\AtomReaderWriter;
use MicrosoftAzure\Storage\Table\Internal\MimeReaderWriter;
use MicrosoftAzure\Storage\Common\Internal\Middlewares\CommonRequestMiddleware;

/**
 * Builds azure service objects.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ServicesBuilder
{
    private static $_instance = null;

    /**
     * Gets the serializer used in the REST services construction.
     *
     * @internal
     *
     * @return Internal\Serialization\ISerializer
     */
    protected function serializer()
    {
        return new XmlSerializer();
    }

    /**
     * Gets the MIME serializer used in the REST services construction.
     *
     * @internal
     *
     * @return \MicrosoftAzure\Storage\Table\Internal\IMimeReaderWriter
     */
    protected function mimeSerializer()
    {
        return new MimeReaderWriter();
    }

    /**
     * Gets the Atom serializer used in the REST services construction.
     *
     * @internal
     *
     * @return \MicrosoftAzure\Storage\Table\Internal\IAtomReaderWriter
     */
    protected function atomSerializer()
    {
        return new AtomReaderWriter();
    }

    /**
     * Gets the Queue authentication scheme.
     *
     * @param string $accountName The account name.
     * @param string $accountKey  The account key.
     *
     * @internal
     *
     * @return \MicrosoftAzure\Storage\Common\Internal\Authentication\SharedKeyAuthScheme
     */
    protected function queueAuthenticationScheme($accountName, $accountKey)
    {
        return new SharedKeyAuthScheme($accountName, $accountKey);
    }

    /**
     * Gets the Blob authentication scheme.
     *
     * @param string $accountName The account name.
     * @param string $accountKey  The account key.
     *
     * @internal
     *
     * @return \MicrosoftAzure\Storage\Common\Internal\Authentication\SharedKeyAuthScheme
     */
    protected function blobAuthenticationScheme($accountName, $accountKey)
    {
        return new SharedKeyAuthScheme($accountName, $accountKey);
    }

    /**
     * Gets the Table authentication scheme.
     *
     * @param string $accountName The account name.
     * @param string $accountKey  The account key.
     *
     * @internal
     *
     * @return TableSharedKeyLiteAuthScheme
     */
    protected function tableAuthenticationScheme($accountName, $accountKey)
    {
        return new TableSharedKeyLiteAuthScheme($accountName, $accountKey);
    }

    /**
     * Gets the SAS authentication scheme.
     *
     * @param string $sasToken The SAS token.
     *
     * @internal
     *
     * @return \MicrosoftAzure\Storage\Common\Internal\Authentication\SharedAccessSignatureAuthScheme
     */
    protected function sasAuthenticationScheme($sasToken)
    {
        return new SharedAccessSignatureAuthScheme($sasToken);
    }

    /**
     * Builds a queue service object, it accepts the following
     * options:
     * 
     * - http: (array) the underlying guzzle options. refer to
     *   http://docs.guzzlephp.org/en/latest/request-options.html for detailed available options
     * - middlewares: (mixed) the middleware should be either an instance of a sub-class that
     *   implements {@see MicrosoftAzure\Storage\Common\Middlewares\IMiddleware}, or a
     *   `callable` that follows the Guzzle middleware implementation convention 
     *
     * @param string $connectionString The configuration connection string.
     * @param array  $options          Array of options to pass to the service
     *
     * @return \MicrosoftAzure\Storage\Queue\Internal\IQueue
     */
    public function createQueueService($connectionString, array $options = [])
    {
        $settings = StorageServiceSettings::createFromConnectionString(
            $connectionString
        );

        $serializer = $this->serializer();
        $uri        = Utilities::tryAddUrlScheme(
            $settings->getQueueEndpointUri()
        );

        $queueWrapper = new QueueRestProxy(
            $uri,
            $settings->getName(),
            $serializer,
            $options
        );

        // Getting authentication scheme
        if ($settings->hasSasToken()) {
            $authScheme = $this->sasAuthenticationScheme(
                $settings->getSasToken()
            );
        } else {
            $authScheme = $this->queueAuthenticationScheme(
                $settings->getName(),
                $settings->getKey()
            );
        }

        // Adding common request middleware
        $commonRequestMiddleware = new CommonRequestMiddleware($authScheme);
        $queueWrapper->pushMiddleware($commonRequestMiddleware);

        return $queueWrapper;
    }

    /**
     * Builds a blob service object, it accepts the following
     * options:
     * 
     * - http: (array) the underlying guzzle options. refer to
     *   http://docs.guzzlephp.org/en/latest/request-options.html for detailed available options
     * - middlewares: (mixed) the middleware should be either an instance of a sub-class that
     *   implements {@see MicrosoftAzure\Storage\Common\Middlewares\IMiddleware}, or a
     *   `callable` that follows the Guzzle middleware implementation convention 
     *
     * @param string $connectionString The configuration connection string.
     * @param array  $options          Array of options to pass to the service
     * @return \MicrosoftAzure\Storage\Blob\Internal\IBlob
     */
    public function createBlobService($connectionString, array $options = [])
    {
        $settings = StorageServiceSettings::createFromConnectionString(
            $connectionString
        );

        $serializer = $this->serializer();
        $uri        = Utilities::tryAddUrlScheme(
            $settings->getBlobEndpointUri()
        );

        $blobWrapper = new BlobRestProxy(
            $uri,
            $settings->getName(),
            $serializer,
            $options
        );

        // Getting authentication scheme
        if ($settings->hasSasToken()) {
            $authScheme = $this->sasAuthenticationScheme(
                $settings->getSasToken()
            );
        } else {
            $authScheme = $this->blobAuthenticationScheme(
                $settings->getName(),
                $settings->getKey()
            );
        }

        // Adding common request middleware
        $commonRequestMiddleware = new CommonRequestMiddleware($authScheme);
        $blobWrapper->pushMiddleware($commonRequestMiddleware);

        return $blobWrapper;
    }

    /**
     * Builds a table service object, it accepts the following
     * options:
     * 
     * - http: (array) the underlying guzzle options. refer to
     *   http://docs.guzzlephp.org/en/latest/request-options.html for detailed available options
     * - middlewares: (mixed) the middleware should be either an instance of a sub-class that
     *   implements {@see MicrosoftAzure\Storage\Common\Middlewares\IMiddleware}, or a
     *   `callable` that follows the Guzzle middleware implementation convention 
     *
     * @param string $connectionString The configuration connection string.
     * @param array  $options          Array of options to pass to the service
     *
     * @return \MicrosoftAzure\Storage\Table\Internal\ITable
     */
    public function createTableService($connectionString, array $options = [])
    {
        $settings = StorageServiceSettings::createFromConnectionString(
            $connectionString
        );

        $atomSerializer = $this->atomSerializer();
        $mimeSerializer = $this->mimeSerializer();
        $serializer     = $this->serializer();
        $uri            = Utilities::tryAddUrlScheme(
            $settings->getTableEndpointUri()
        );

        $tableWrapper = new TableRestProxy(
            $uri,
            $atomSerializer,
            $mimeSerializer,
            $serializer,
            $options
        );

        // Adding headers filter
        $headers               = array();
        $currentVersion        = Resources::DATA_SERVICE_VERSION_VALUE;
        $maxVersion            = Resources::MAX_DATA_SERVICE_VERSION_VALUE;
        $accept                = Resources::ACCEPT_HEADER_VALUE;
        $acceptCharset         = Resources::ACCEPT_CHARSET_VALUE;

        $headers[Resources::DATA_SERVICE_VERSION]     = $currentVersion;
        $headers[Resources::MAX_DATA_SERVICE_VERSION] = $maxVersion;
        $headers[Resources::ACCEPT_HEADER]            = $accept;
        $headers[Resources::ACCEPT_CHARSET]           = $acceptCharset;

        // Getting authentication scheme
        if ($settings->hasSasToken()) {
            $authScheme = $this->sasAuthenticationScheme(
                $settings->getSasToken()
            );
        } else {
            $authScheme = $this->tableAuthenticationScheme(
                $settings->getName(),
                $settings->getKey()
            );
        }
        
        // Adding common request middleware
        $commonRequestMiddleware = new CommonRequestMiddleware($authScheme, $headers);
        $tableWrapper->pushMiddleware($commonRequestMiddleware);

        return $tableWrapper;
    }

    /**
     * Gets the static instance of this class.
     *
     * @return ServicesBuilder
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new ServicesBuilder();
        }

        return self::$_instance;
    }
}
