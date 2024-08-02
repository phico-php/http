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

    public function cookie(string $name): ?string
    {
        if (!isset($this->headers['set-cookie'])) {
            return null;
        }

        foreach ($this->headers['set-cookie'] as $header) {
            $parts = explode('; ', $header);
            foreach ($parts as $part) {
                if (strpos($part, $name . '=') !== false) {
                    list($name, $value) = explode('=', trim($part), 2);
                    return $value;
                }
            }
        }

        return null;
    }
    public function header(string $name, mixed $default = null): mixed
    {
        return $this->headers[$name] ?? $default;
    }
    public function headers(): array
    {
        return $this->headers;
    }
    public function location(): ?string
    {
        return $this->headers['location'][0] ?? null;
    }
    public function status(): int
    {
        return $this->status;
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
