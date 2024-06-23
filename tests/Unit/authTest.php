<?php

namespace Phico\Tests\Http;


test('can GET with auth basic', function () {

    $http = http();
    $response = $http
        ->auth('basic', 'bob@example.com', 'password')
        ->get('https://posttestserver.dev/p/2wh0926slgdimnm2/post');

    expect($response)->toBeInstanceOf(\Phico\Http\Client\Response::class);

    expect(strlen($response->body))->toBeGreaterThan(0);

    expect($response->status)->toBe(200);
    expect($response->isSuccess())->toBe(true);
    expect($response->isError())->toBe(false);
    expect($response->isClientError())->toBe(false);
    expect($response->isServerError())->toBe(false);
    expect($response->isInfo())->toBe(false);
});


test('can GET with auth bearer', function () {

    $http = http();
    $response = $http
        ->auth('bearer', md5(time()))
        ->get('https://posttestserver.dev/p/2wh0926slgdimnm2/post');

    expect($response)->toBeInstanceOf(\Phico\Http\Client\Response::class);

    expect(strlen($response->body))->toBeGreaterThan(0);

    expect($response->status)->toBe(200);
    expect($response->isSuccess())->toBe(true);
    expect($response->isError())->toBe(false);
    expect($response->isClientError())->toBe(false);
    expect($response->isServerError())->toBe(false);
    expect($response->isInfo())->toBe(false);
});

