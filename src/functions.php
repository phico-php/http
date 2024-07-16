<?php

declare(strict_types=1);

if (!function_exists('http')) {
    function http(): \Phico\Http\Client\HttpClient
    {
        return new \Phico\Http\Client\HttpClient;
    }
}
