<?php

namespace Phico\Tests\Http;

test('can make POST request', function () {

    $body = [
        'name' => 'bob',
        'email' => 'bob@example.com',
        'created_at' => date('c', time())
    ];

    $http = http();
    $response = $http
        ->form($body)
        ->post('https://posttestserver.dev/p/2wh0926slgdimnm2/post');

    expect($response)->toBeInstanceOf(\Phico\Http\Client\Response::class);

    expect($response->status)->toBe(200);
    expect($response->isSuccess())->toBe(true);
    expect($response->isError())->toBe(false);
    expect($response->isClientError())->toBe(false);
    expect($response->isServerError())->toBe(false);
    expect($response->isInfo())->toBe(false);

    $request = $response->request;
    expect($request)->toBeInstanceOf(\Phico\Http\Client\Request::class);
    expect($request->body)->toBe($body);

});

test('can make POST request with params', function () {

    $body = [
        'name' => 'bob',
        'email' => 'bob@example.com',
        'created_at' => date('c', time())
    ];
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
        ->form($body)
        ->post('https://posttestserver.dev/p/2wh0926slgdimnm2/post', $params);

    expect($response)->toBeInstanceOf(\Phico\Http\Client\Response::class);

    expect($response->status)->toBe(200);
    expect($response->isSuccess())->toBe(true);
    expect($response->isError())->toBe(false);
    expect($response->isClientError())->toBe(false);
    expect($response->isServerError())->toBe(false);
    expect($response->isInfo())->toBe(false);

    $request = $response->request;
    expect($request)->toBeInstanceOf(\Phico\Http\Client\Request::class);
    expect($request->body)->toBe($body);
    expect($request->params)->toBe($params);

});
