<?php

declare(strict_types=1);

namespace Phico\Http\Client;

use ErrorException;

class Request
{
    private string $method;
    private string $url;
    private array $cookies;
    private array $headers;
    private array $options;
    private array $params;
    private array $body;


    public function __construct(array $state)
    {
        $state = array_merge([
            'method' => 'GET',
            'url' => '',
            'cookies' => [],
            'headers' => [],
            'options' => [],
            'params' => [],
            'body' => [],
        ], $state);
        foreach ($state as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) {
            throw new ErrorException("Cannot access unknown property '$name' in ClientRequest", 6100);
        }

        return $this->$name;
    }
}
