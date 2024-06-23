<?php

namespace Phico\Tests\Http;

test('can request HEAD', function () {

    $http = http();
    $response = $http
        ->head('https://posttestserver.dev/p/2wh0926slgdimnm2/post');

    expect($response)->toBeInstanceOf(\Phico\Http\Client\Response::class);

    expect(strlen($response->body))->toBe(0);

    expect($response->status)->toBe(200);
    expect($response->isSuccess())->toBe(true);
    expect($response->isError())->toBe(false);
    expect($response->isClientError())->toBe(false);
    expect($response->isServerError())->toBe(false);
    expect($response->isInfo())->toBe(false);

    var_dump($response->body);

    $request = $response->request;
    expect($request)->toBeInstanceOf(\Phico\Http\Client\Request::class);
    expect($request->method)->toBe('HEAD');

});

test('can request HEAD with params', function () {

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
        ->head('https://posttestserver.dev/p/2wh0926slgdimnm2/post', $params);

    var_dump($response->body);


    expect($response)->toBeInstanceOf(\Phico\Http\Client\Response::class);

    expect(strlen($response->body))->toBe(0);

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
    expect($request->method)->toBe('HEAD');
    expect($request->params)->toBe($params);

});
