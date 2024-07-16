# Http Client

A simple http client wrapping the built in cURL functions.

## Installation

Install via composer

```sh
composer require phico/http-client
```

## Usage

### Making requests

#### GETing remote content

Use `get()` to request a page, the result is stored in the response `body`.

```php
$response = http()->get('https://example.com');
echo $response->body;
```

Send query parameters as the second argument

```php
$response = http()->get('https://example.com/hello', [
    'cache' => false,
    'name' => 'Bob'
]);
echo $response->body;
```

All http methods are supported

- GET
- POST
- DELETE
- PUT
- PATCH
- HEAD
- OPTIONS

Custom verbs can be used via `method()`

```php
$response = http()->method('PURGE', 'https://example.com/cache');
```

#### POSTing Form data

```php
$response = http()
    ->form([
        'user' => 'bob',
        'password' => 't0p-secret'
    ])->post('https://example.com/signin');
```

##### With a file

```php
$response = http()
    ->form([
        'overwrite' => true,
    ])
    ->file('/path/to/file')
    ->post('https://example.com/upload');
```

File also accepts an optional filename and optional mime-type.

```php
$response = http()
    ->file('/path/to/invoice.pdf', 'invoice-2024.pdf')
    ->file('/path/to/readme.txt', 'read-me.txt', 'text/plain')
    ->post('https://example.com/upload');
```

#### POSTing JSON data

```php
$response = http()
    ->json([
        'colour' => 'Blue'
    ])
    ->put('https://example.com/widgets/123');
```

### Responses

Every request returns a structured response containing the request made,
the response cookies, headers, status code and body.

```php
$response = http()
    ->json([
        'colour' => 'Blue'
    ])
    ->post('https://example.com/widgets/123');

echo $response->http_code; // 200, 401, 500 etc..
echo $response->body;
echo join("\n", $response->cookies);  // cookies array
echo join("\n", $response->headers); // headers array

// and weirdly
var_dump($response->request);

```

### Error handling

Exceptions will be thrown on connection or transport errors only, your code
should handle the http status codes appropriately.

```php
try {
    $response = http()
        ->form([
            'user' => '',
            'password' => 't0p-secret'
        ])->post('https://example.com/signin');
} catch (HttpClientException $e) {

   // show exception details
   echo $e->getSummary();
   echo $e->getTraceAsString();

}
```
