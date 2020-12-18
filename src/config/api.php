<?php

return [
    'url' => function_exists('env') ? env('HAGEMAN_REST_API_URL') : '',
    'key' => function_exists('env') ? env('HAGEMAN_REST_API_KEY') : '',
    'secret' => function_exists('env') ? env('HAGEMAN_REST_API_SECRET') : '',
];
