<?php

$accepts = request();

return response()->json([
    'accepts' => $accepts,
    'media' => [
        'text/html' => $request->accepts('media', 'text/html'),
        'html' => $request->accepts('media', 'html'),
        'application/xml' => $request->accepts('media', 'application/xml'),
        'xml' => $request->accepts('media', 'xml'),
        'application/json' => $request->accepts('media', 'application/json'),
        'json' => $request->accepts('media', 'json'),
    ],
    'lang' => [
        'en_GB' => $request->accepts('lang', 'en_GB'),
        'en_gb' => $request->accepts('lang', 'en_gb'),
        'en-GB' => $request->accepts('lang', 'en-GB'),
        'en-gb' => $request->accepts('lang', 'en-gb'),
        'EN' => $request->accepts('lang', 'EN'),
        'en' => $request->accepts('lang', 'en'),
        'PT' => $request->accepts('lang', 'PT'),
        'pt' => $request->accepts('lang', 'pt'),
        'PT-BR' => $request->accepts('lang', 'PT-BR'),
        'pt_br' => $request->accepts('lang', 'pt_br'),
    ],
    'language' => [
        'en_GB' => $request->accepts('language', 'en_GB'),
        'en_gb' => $request->accepts('language', 'en_gb'),
        'en-GB' => $request->accepts('language', 'en-GB'),
        'en-gb' => $request->accepts('language', 'en-gb'),
        'EN' => $request->accepts('language', 'EN'),
        'en' => $request->accepts('language', 'en'),
        'PT' => $request->accepts('language', 'PT'),
        'pt' => $request->accepts('language', 'pt'),
        'PT-BR' => $request->accepts('language', 'PT-BR'),
        'pt_br' => $request->accepts('language', 'pt_br'),
    ],
]);
