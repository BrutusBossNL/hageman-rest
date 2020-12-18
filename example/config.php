<?php

use Hageman\REST\API;

/**
* Autoloader
*/
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * The API config can be rewritten.
 *
 * Set some config
 */
API::config( array(
    'url' => 'https://rest.hageman.nl:8181',
    'key' => 'your_key',
    'secret' => 'your_secret'
) );