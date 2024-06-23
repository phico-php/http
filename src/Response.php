<?php

declare(strict_types=1);

namespace Phico\Http\Client;

use ErrorException;

class Response
{
    private int $status;
    private array $headers;
    private mixed $body;
    private Request $request;


    public function __construct(array $state)
    {
        $state = array_merge([
            'status' => 200,
            'headers' => [],
            'body' => '',
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
            throw new ErrorException("Cannot access unknown property '$name' in ClientResponse", 6200);
        }

        return $this->$name;
    }

    public function isError()
    {
        return $this->status >= 400 && $this->status < 600;
    }
    public function isClientError()
    {
        return $this->status >= 400 && $this->status < 500;
    }
    public function isServerError()
    {
        return $this->status >= 500 && $this->status < 600;
    }
    public function isInfo()
    {
        return $this->status >= 100 && $this->status < 200;
    }
    public function isRedirect()
    {
        return $this->status >= 300 && $this->status < 400;
    }
    public function isSuccess()
    {
        return $this->status >= 200 && $this->status < 300;
    }
}
