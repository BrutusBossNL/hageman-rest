<?php

use Hageman\REST\Administration;

/**
 * Autoloader
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * The administration class only provides the list option
 * - The function is generally created for Wordpress/Woocommerce support. It is possible to select the active administration from the list in the settings
 *
 * Initialise the model without data.
 */
$administration = new Administration();

/**
 * It is possible to list administrations.
 * The list function always uses paged format, default: page 1, pageSize 10.
 * A list can hold $filter and $sort parameters, please see the docs (navigate to the base URL of the REST API) for usage and methods.
 * - Returns a paged response. The data will be available in the 'rows' entry of the response, if there is no error.
 */
$listedAdministrations = $administration->list( $filter = array(), $sort = array() );

/**
 * Or with custom paging.
 */
$listedAdministrations = $administration->paging($page = 1, $pageSize = 50)->list( $filter = array(), $sort = array() );

/**
 * Just not into paging, then try a custom query.
 * Keep in mind that there is a bottleneck, the larger the data the larger the timeout!
 * Keep the queries short and precise to fetch only the data that's needed.
 */
$receivedAdministrations = $administration->query( $filter = array(), $sort = array() )->all();

/**
 * Or only the first entry.
 * - Returns a SINGLE model instead of an array of models (BATCH) like the all() function.
 */
$receivedAdministration = $administration->query( $filter = array(), $sort = array() )->one();
