<?php

declare(strict_types=1);

namespace Phico\Http\Client;

use BadMethodCallException;
use CURLFile;
use CURLStringFile;
use InvalidArgumentException;
use RuntimeException;

class HttpClient
{
    private string $method;
    private string $url;
    private string $content_type;
    private array $cookies;
    private array $files;
    private array $headers;
    private array $options;
    private array $params;
    private array|object $body;
    // @TODO add csv, text, xml etc..
    private array $response_headers;
    private array $handlers;

    public function __construct()
    {
        $this->reset();
    }
    private function reset(): void
    {
        $this->method = 'GET';
        $this->url = '';

        $this->cookies = [];
        $this->files = [];
        $this->headers = [];
        $this->options = [
            \CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; Phico/1.0; +https://phico-php.net)',
            \CURLINFO_HEADER_OUT => true,
            \CURLOPT_HEADER => false,
            \CURLOPT_RETURNTRANSFER => true,
        ];
        $this->params = [];
        $this->body = [];
        $this->response_headers = [];

        $this->handlers = [];
    }
    // enable verbose mode
    public function debug(): self
    {
        $this->options[CURLOPT_VERBOSE] = true;
        return $this;
    }
    // set a curl option
    public function option(int $option, mixed $value): self
    {
        if ($option === CURLOPT_HTTPHEADER) {
            throw new InvalidArgumentException('Cannot set headers via options() use headers() instead', 6000);
        }
        $this->options[$option] = $value;

        return $this;
    }
    // set content handlers
    public function handler(string $type, callable $handler)
    {
        $this->handlers[$type] = $handler;
    }

    // request object methods

    // set authentication, only basic or bearer are supported
    public function auth(string $type = 'any', ?string $token_or_user = null, ?string $password = null): self
    {
        switch (strtolower($type)) {
            case 'basic':
                $this->options[CURLOPT_HTTPAUTH] = \CURLAUTH_BASIC;
                $this->options[CURLOPT_USERPWD] = "$token_or_user:$password";
                break;
            case 'bearer':
                $this->options[CURLOPT_HTTPAUTH] = \CURLAUTH_BEARER;
                $this->options[CURLOPT_XOAUTH2_BEARER] = $token_or_user;
                break;


            // the following are supported by curl but not tested,
            // if you need it then contributions are welcome

            // case 'any-safe':
            // case 'anysafe':
            // case 'safe':
            //     $this->http_auth = [ 'type' => \CURLAUTH_ANYSAFE ];
            //     break;
            // case 'any':
            //     $this->http_auth = [ 'type' => \CURLAUTH_ANY ];
            //     break;
            // case 'aws':
            // case 'aws-sigv4':
            //     $this->http_auth = [ 'type' => \CURLAUTH_AWS_SIGV4 ];
            //     break;
            // case 'digest-ie':
            // case 'digest_ie':
            //     $this->http_auth = [ 'type' => \CURLAUTH_DIGEST_IE ];
            //     break;
            // case 'digest':
            //     $this->http_auth = [ 'type' => \CURLAUTH_DIGEST ];
            //     break;
            // case 'gss-api':
            // case 'gss_api':
            //     $this->http_auth = [ 'type' => \CURLAUTH_GSSAPI ];
            //     break;
            // case 'gss':
            // case 'gssnegotiate':
            //     $this->http_auth = [ 'type' => \CURLAUTH_GSSNEGOTIATE ];
            //     break;
            // case 'negotiate':
            //     $this->http_auth = [ 'type' => \CURLAUTH_NEGOTIATE ];
            //     break;
            // case 'ntlm-wb':
            // case 'ntlm_wb':
            //     $this->http_auth = [ 'type' => \CURLAUTH_NTLM_WB ];
            //     break;
            // case 'ntlm':
            //     $this->http_auth = [ 'type' => \CURLAUTH_NTLM ];
            //     break;

            default:
                throw new InvalidArgumentException("Unsupported authentication type auth('$type')");
        }

        return $this;
    }
    // set a cookie
    public function cookie(string $name, string $value): self
    {
        $this->cookies[$name] = $value;
        return $this;
    }
    // set a request header
    public function header(string $name, string $value): self
    {
        $name = str()->toTrainCase($name);
        $this->headers[] = "$name: $value";
        return $this;
    }
    // set multiple request headers
    public function headers(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->header($name, (string) $value);
        }
        return $this;
    }
    // attach string as file to form data
    public function asFile(string $content, ?string $name = null, ?string $mime_type = null): self
    {
        $this->files[] = new CURLStringFile($content, $name, $mime_type);
        return $this;
    }
    // attach file to form data
    public function file(string $src, ?string $name = null, ?string $mime_type = null): self
    {
        $this->files[] = new CURLFile(path($src), $mime_type, $name);
        return $this;
    }
    // store form data
    public function form(array $data): self
    {
        if (!empty($this->body)) {
            throw new BadMethodCallException('Cannot call form() as the body has already been set, only one data method form(), json() can be used per request', 6010);
        }
        $this->body = $data;
        $this->content_type = 'form';
        return $this;
    }
    // store json data
    public function json(array|object $data): self
    {
        if (!empty($this->body)) {
            throw new BadMethodCallException('Cannot call json() as the body has already been set, only one data method form(), json() can be used per request', 6020);
        }

        $this->body = $data;
        $this->content_type = 'json';

        return $this;
    }

    // transport methods, these are final (call them last) and return a ClientResponse

    public function delete(string $url, array $params = []): object
    {
        $this->method = 'DELETE';
        $this->url = $url;
        $this->params = $params;

        return $this->exec();
    }
    public function get(string $url, array $params = []): object
    {
        $this->method = 'GET';
        $this->url = $url;
        $this->params = $params;

        return $this->exec();
    }
    public function head(string $url, array $params = []): object
    {
        $this->method = 'HEAD';
        $this->url = $url;
        $this->params = $params;

        return $this->exec();
    }
    public function method(string $method, string $url, array $params = []): object
    {
        $this->method = strtoupper($method);
        $this->url = $url;
        $this->params = $params;

        return $this->exec();
    }
    public function options(string $url, array $params = []): object
    {
        $this->method = 'OPTIONS';
        $this->url = $url;
        $this->params = $params;

        return $this->exec();
    }
    public function patch(string $url, array $params = []): object
    {
        $this->method = 'PATCH';
        $this->url = $url;
        $this->params = $params;

        return $this->exec();
    }
    public function post(string $url, array $params = []): object
    {
        $this->method = 'POST';
        $this->url = $url;
        $this->params = $params;

        return $this->exec();
    }
    public function put(string $url, array $params = []): object
    {
        $this->method = 'PUT';
        $this->url = $url;
        $this->params = $params;

        return $this->exec();
    }

    // private methods
    private function exec(): object
    {
        try {

            // set the url with params
            $ch = \curl_init(
                sprintf(
                    '%s%s',
                    $this->url,
                    (empty($this->params)) ? '' : '?' . \http_build_query($this->params)
                )
            );
            // set the request method
            $this->setMethod($ch);
            // set all curl behaviour options
            curl_setopt_array($ch, $this->options);
            // parse response headers separately
            curl_setopt($ch, \CURLOPT_HEADERFUNCTION, [$this, 'setResponseHeader']);
            // pass request headers to curl
            curl_setopt($ch, \CURLOPT_HTTPHEADER, $this->headers);
            // set cookies
            if (!empty($this->cookies)) {
                curl_setopt($ch, \CURLOPT_COOKIE, \http_build_query($this->cookies, '', '; '));
            }
            // set the request body
            $this->setBody($ch);

            // make the http request
            if (false === $body = \curl_exec($ch)) {
                throw new RuntimeException(\curl_error($ch), \curl_errno($ch));
            }

            // populate the response classes (they are read only)
            $response = new Response([
                'request' => new Request([
                    // @TODO populate this with actual curl values?
                    'method' => $this->method,
                    'url' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),
                    'cookies' => $this->cookies,
                    'headers' => $this->headers,
                    'options' => $this->options,
                    'params' => $this->params,
                    'body' => $this->body,
                ]),
                'status' => intval(curl_getinfo($ch, CURLINFO_HTTP_CODE)),
                'headers' => $this->response_headers,
                'body' => $body,
            ]);

            return $response;

        } catch (\Throwable $th) {

            throw new HttpClientException($th->getMessage(), $th->getCode(), $th);

        } finally {

            // allow re-use
            $this->reset();
            // stop memory leaks referencing 'setResponseHeaders' method
            curl_setopt($ch, \CURLOPT_HEADERFUNCTION, null);
            // free memory
            unset($ch);

        }
    }
    // sets the http verb
    private function setMethod($ch): void
    {
        switch ($this->method) {
            case 'GET':
                curl_setopt($ch, \CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($ch, \CURLOPT_POST, true);
                break;
            case 'PUT':
                curl_setopt($ch, \CURLOPT_PUT, true);
                break;

            case 'DELETE':
            case 'HEAD':
            case 'OPTIONS':
            case 'PATCH':
            default:
                curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, $this->method);
        }
    }
    // @TODO sets the request body depending on content-type and data[]
    private function setBody($ch): void
    {
        if (empty($this->body)) {
            return;
        }

        // switch content-type
        // case form
        // case json
        switch ($this->content_type) {
            case 'form':
                curl_setopt(
                    $ch,
                    \CURLOPT_POSTFIELDS,
                    array_merge(
                        $this->files,
                        $this->body
                    )
                );
                break;

            case 'json':
                // base64 encode the files
                $files = []; // base64_encode('');
                // json encode the body
                $body = json_encode(
                    array_merge(
                        $files,
                        $this->body
                    )
                );
                // set the headers
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($body)
                ]);
                // set the body
                curl_setopt(
                    $ch,
                    \CURLOPT_POSTFIELDS,
                    $body
                );
                break;

            default:
                throw new \ErrorException(sprintf("Cannot handle unsupported content type '%s'", $this->content_type), 6050);
        }

    }
    // set a response header
    private function setResponseHeader($ch, $line): int
    {
        $len = strlen($line);
        $parts = explode(':', $line, 2);
        // ignore invalid headers
        if (count($parts) < 2) {
            return $len;
        }

        // normalise the header key
        $name = strtolower(trim($parts[0]));
        if (!isset($this->response_headers[$name])) {
            $this->response_headers[$name] = [];
        }
        $this->response_headers[$name][] = trim($parts[1]);

        return $len;
    }
}
