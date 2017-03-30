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

use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\RetryMiddlewareFactory;
use MicrosoftAzure\Storage\Common\Internal\Http\HttpCallContext;
use MicrosoftAzure\Storage\Common\Internal\Middlewares\MiddlewareBase;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for all services rest proxies.
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class ServiceRestProxy extends RestProxy
{
    private $_accountName;
    private $_psrUri;
    private $_options;

    /**
     * Initializes new ServiceRestProxy object.
     *
     * @param string                    $uri            The storage account uri.
     * @param string                    $accountName    The name of the account.
     * @param Serialization\ISerializer $dataSerializer The data serializer.
     * @param array                     $options        Array of options for
     *                                                  the service
     */
    public function __construct(
        $uri,
        $accountName,
        Serialization\ISerializer $dataSerializer,
        array $options = []
    ) {
        if ($uri[strlen($uri)-1] != '/') {
            $uri = $uri . '/';
        }

        parent::__construct($dataSerializer, $uri);

        $this->_accountName = $accountName;
        $this->_psrUri = new \GuzzleHttp\Psr7\Uri($uri);
        $this->_options = array_merge(array('http' => array()), $options);
    }

    /**
     * Gets the account name.
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->_accountName;
    }

    /**
     * Static helper function to create a usable client for the proxy.
     * The requestOptions can contain the following keys that will affect
     * the way retry handler is created and applied.
     * handler:               HandlerStack, if set, this function will not
     *                        create a handler stack.
     * @param  array $requestOptions Added options for client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function createClient(array $requestOptions)
    {
        //First, extract or create the handler stack for the client.
        $requestOptions['handler'] = $this->createHandlerStack($requestOptions);

        return (new \GuzzleHttp\Client(
            array_merge(
                $this->_options['http'],
                array(
                    "defaults" => array(
                        "allow_redirects" => true,
                        "exceptions" => true,
                        "decode_content" => true,
                    ),
                    'cookies' => true,
                    'verify' => true,
                    // For testing with Fiddler
                    //'proxy' => "localhost:8888",
                ),
                $requestOptions
            )
        ));
    }
    
    /**
     * Create a handler stack with given middleware. If the given handler is not
     * a type of HandlerStack and middleware is to pushed in, exception might be
     * thrown due to some of the handler stack type does not implement push()
     * method. e.g. MockHandler.
     *
     * @param  array  $requestOptions The options user passed in.
     *
     * @return HandlerStack          The HandlerStack that contains all the
     *                               middleware specified in the $requestOptions.
     */
    protected function createHandlerStack(array $requestOptions)
    {
        //If handler stack is not defined by the user, create a default
        //handler stack.
        $stack = null;
        if (array_key_exists('handler', $this->_options['http'])) {
            $stack = $this->_options['http']['handler'];
        } elseif (array_key_exists('handler', $requestOptions)) {
            $stack = $requestOptions['handler'];
        } else {
            $stack = HandlerStack::create();
        }

        //Push all the middlewares specified in the $requestOptions to the
        //handlerstack.
        if (array_key_exists('middlewares', $requestOptions)) {
            foreach ($requestOptions['middlewares'] as $middleware) {
                $stack->push($middleware);
            }
        }

        //Push all the middlewares specified in the $_options to the
        //handlerstack.
        if (array_key_exists('middlewares', $this->_options)) {
            foreach ($this->_options['middlewares'] as $middleware) {
                $stack->push($middleware);
            }
        }

        //Push all the middlewares specified in $this->middlewares to the
        //handlerstack.
        foreach ($this->getMiddlewares() as $middleware) {
            $stack->push($middleware);
        }

        return $stack;
    }

    /**
     * Send the requests concurrently. Number of concurrency can be modified
     * by inserting a new key/value pair with the key 'number_of_concurrency'
     * into the $requestOptions. Return only the promise.
     *
     * @param  array    $requests              An array holding all the
     *                                         initialized requests. If empty,
     *                                         the first batch will be created
     *                                         using the generator.
     * @param  callable $generator             the generator function to
     *                                         generate request upon fullfilment
     * @param  int      $expectedStatusCode    The expected status code for each
     *                                         of the request.
     * @param  array    $requestOptions         an array of additional options
     *                                         for the client.
     *
     * @return array
     */
    protected function sendConcurrentAsync(
        array $requests,
        callable $generator,
        $expectedStatusCode,
        array $requestOptions = []
    ) {
        //set the number of concurrency to default value if not defined
        //in the array.
        $numberOfConcurrency = Resources::NUMBER_OF_CONCURRENCY;
        if (array_key_exists('number_of_concurrency', $requestOptions)) {
            $numberOfConcurrency = $requestOptions['number_of_concurrency'];
            unset($requestOptions['number_of_concurrency']);
        }
        //creates the client
        $client = $this->createClient($requestOptions);

        $promises = \call_user_func(
            function () use ($requests, $generator, $client) {
                $sendAsync = function ($request) use ($client) {
                    $options = $request->getMethod() == 'HEAD'?
                        array('decode_content' => false) : array();
                    return $client->sendAsync($request, $options);
                };
                foreach ($requests as $request) {
                    yield $sendAsync($request);
                }
                while (is_callable($generator) && ($request = $generator())) {
                    yield $sendAsync($request);
                }
            }
        );

        $eachPromise = new EachPromise($promises, [
            'concurrency' => $numberOfConcurrency,
            'fulfilled' => function ($response, $index) use ($expectedStatusCode) {
                //the promise is fulfilled, evaluate the response
                self::throwIfError(
                    $response,
                    $expectedStatusCode
                );
            },
            'rejected' => function ($reason, $index) {
                //Still rejected even if the retry logic has been applied.
                //Throwing exception.
                throw $reason;
            }
        ]);
        
        return $eachPromise->promise();
    }


    /**
     * Create the request to be sent.
     *
     * @param  string $method         The method of the HTTP request
     * @param  array $headers        The header field of the request
     * @param  array $queryParams    The query parameter of the request
     * @param  array $postParameters The HTTP POST parameters
     * @param  string $path           URL path
     * @param  string $body           Request body
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createRequest(
        $method,
        array $headers,
        array $queryParams,
        array $postParameters,
        $path,
        $body = Resources::EMPTY_STRING
    ) {
        // add query parameters into headers
        $uri = $this->_psrUri;
        //Append the path, not replacing it.
        if ($path != null) {
            $exPath = $uri->getPath();
            if ($exPath != '') {
                //Remove the duplicated slash in the path.
                if ($path != '' && $path[0] == '/') {
                    $path = $exPath . substr($path, 1);
                } else {
                    $path = $exPath . $path;
                }
            }
            $uri = $uri->withPath($path);
        }

        if ($queryParams != null) {
            $queryString = Psr7\build_query($queryParams);
            $uri = $uri->withQuery($queryString);
        }

        // add post parameters into bodys
        $actualBody = null;
        if (empty($body)) {
            if (empty($headers['content-type'])) {
                $headers['content-type'] = 'application/x-www-form-urlencoded';
                $actualBody = Psr7\build_query($postParameters);
            }
        } else {
            $actualBody = $body;
        }

        $request = new Request(
            $method,
            $uri,
            $headers,
            $actualBody
        );

        //add content-length to header
        $bodySize = $request->getBody()->getSize();
        if ($bodySize > 0) {
            $request = $request->withHeader('content-length', $bodySize);
        }
        return $request;
    }

    /**
     * Create promise of sending HTTP request with the specified parameters.
     *
     * @param  string       $method         HTTP method used in the request
     * @param  array        $headers        HTTP headers.
     * @param  array        $queryParams    URL query parameters.
     * @param  array        $postParameters The HTTP POST parameters.
     * @param  string       $path           URL path
     * @param  array|int    $expected       Expected Status Codes.
     * @param  string       $body           Request body
     * @param  array        $requestOptions Guzzle Client options
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function sendAsync(
        $method,
        array $headers,
        array $queryParams,
        array $postParameters,
        $path,
        $expected = Resources::STATUS_OK,
        $body = Resources::EMPTY_STRING,
        array $requestOptions = []
    ) {
        $request = $this->createRequest(
            $method,
            $headers,
            $queryParams,
            $postParameters,
            $path,
            $body
        );
        $client = $this->createClient($requestOptions);

        $options = $request->getMethod() == 'HEAD'?
            array('decode_content' => false) : array();

        $promise = $client->sendAsync($request, $options);

        return $promise->then(
            function ($response) use ($expected) {
                self::throwIfError(
                    $response,
                    $expected
                );
                return $response;
            },
            function ($reason) use ($expected) {
                if (!($reason instanceof RequestException)) {
                    throw $reason;
                }
                $response = $reason->getResponse();
                if ($response != null) {
                    self::throwIfError(
                        $response,
                        $expected
                    );
                } else {
                    //if could not get response but promise rejected, throw reason.
                    throw $reason;
                }
                return $response;
            }
        );
    }

    /**
     * Sends the context.
     *
     * @param  HttpCallContext $context The context of the request.
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function sendContext(HttpCallContext $context)
    {
        return $this->sendContextAsync($context)->wait();
    }

    /**
     * Creates the promise to send the context.
     *
     * @param  HttpCallContext $context The context of the request.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function sendContextAsync(HttpCallContext $context)
    {
        return $this->sendAsync(
            $context->getMethod(),
            $context->getHeaders(),
            $context->getQueryParameters(),
            $context->getPostParameters(),
            $context->getPath(),
            $context->getStatusCodes(),
            $context->getBody(),
            $context->getRequestOptions()
        );
    }

    /**
     * Throws ServiceException if the recieved status code is not expected.
     *
     * @param ResponseInterface $response The response received
     * @param array|int         $expected The expected status codes.
     *
     * @return void
     *
     * @throws ServiceException
     */
    public static function throwIfError(ResponseInterface $response, $expected)
    {
        $expectedStatusCodes = is_array($expected) ? $expected : array($expected);

        if (!in_array($response->getStatusCode(), $expectedStatusCodes)) {
            throw new ServiceException($response);
        }
    }
    
    /**
     * Adds HTTP POST parameter to the specified
     *
     * @param array  $postParameters An array of HTTP POST parameters.
     * @param string $key            The key of a HTTP POST parameter.
     * @param string $value          the value of a HTTP POST parameter.
     *
     * @return array
     */
    public function addPostParameter(
        array $postParameters,
        $key,
        $value
    ) {
        Validate::isArray($postParameters, 'postParameters');
        $postParameters[$key] = $value;
        return $postParameters;
    }

    /**
     * Groups set of values into one value separated with Resources::SEPARATOR
     *
     * @param array $values array of values to be grouped.
     *
     * @return string
     */
    public static function groupQueryValues(array $values)
    {
        Validate::isArray($values, 'values');
        $joined = Resources::EMPTY_STRING;

        sort($values);

        foreach ($values as $value) {
            if (!is_null($value) && !empty($value)) {
                $joined .= $value . Resources::SEPARATOR;
            }
        }

        return trim($joined, Resources::SEPARATOR);
    }

    /**
     * Adds metadata elements to headers array
     *
     * @param array $headers  HTTP request headers
     * @param array $metadata user specified metadata
     *
     * @return array
     */
    protected function addMetadataHeaders(array $headers, array $metadata = null)
    {
        Utilities::validateMetadata($metadata);

        $metadata = $this->generateMetadataHeaders($metadata);
        $headers  = array_merge($headers, $metadata);

        return $headers;
    }

    /**
     * Generates metadata headers by prefixing each element with 'x-ms-meta'.
     *
     * @param array $metadata user defined metadata.
     *
     * @return array
     */
    public function generateMetadataHeaders(array $metadata = null)
    {
        $metadataHeaders = array();

        if (is_array($metadata) && !is_null($metadata)) {
            foreach ($metadata as $key => $value) {
                $headerName = Resources::X_MS_META_HEADER_PREFIX;
                if (strpos($value, "\r") !== false
                    || strpos($value, "\n") !== false
                ) {
                    throw new \InvalidArgumentException(Resources::INVALID_META_MSG);
                }

                // Metadata name is case-presrved and case insensitive
                $headerName                     .= $key;
                $metadataHeaders[$headerName] = $value;
            }
        }

        return $metadataHeaders;
    }
}
