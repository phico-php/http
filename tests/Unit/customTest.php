<?php

namespace Phico\Tests\Http;


test('can use custom request method', function () {

    $http = http();
    $response = $http
        ->method('CUSTOM', 'https://posttestserver.dev/p/2wh0926slgdimnm2/post');

    expect($response)->toBeInstanceOf(\Phico\Http\Client\Response::class);

    expect(strlen($response->body))->toBeGreaterThan(0);

    expect($response->status)->toBe(200);
    expect($response->isSuccess())->toBe(true);
    expect($response->isError())->toBe(false);
    expect($response->isClientError())->toBe(false);
    expect($response->isServerError())->toBe(false);
    expect($response->isInfo())->toBe(false);

    $request = $response->request;
    expect($request)->toBeInstanceOf(\Phico\Http\Client\Request::class);
    expect($request->method)->toBe("CUSTOM");
});

test('can use custom request method with params', function () {

    $params = [
        'foo' => 'bar',
        'baz' => 12345,
        'cast' => [
            'Kermit',
            'Fozzy Bear',
            'Gonzo'
        ]
    ];

    $http = http();
    $response = $http
        ->method('PURGE', 'https://posttestserver.dev/p/2wh0926slgdimnm2/post', $params);

    expect($response)->toBeInstanceOf(\Phico\Http\Client\Response::class);

    expect(strlen($response->body))->toBeGreaterThan(0);

    expect($response->status)->toBe(200);
    expect($response->isSuccess())->toBe(true);
    expect($response->isError())->toBe(false);
    expect($response->isClientError())->toBe(false);
    expect($response->isServerError())->toBe(false);
    expect($response->isInfo())->toBe(false);

    expect($response->headers)->toBeArray();
    expect(count($response->headers))->toBeGreaterThan(0);

    $request = $response->request;
    expect($request)->toBeInstanceOf(\Phico\Http\Client\Request::class);
    expect($request->method)->toBe("PURGE");
    expect($request->params)->toBe($params);
});
