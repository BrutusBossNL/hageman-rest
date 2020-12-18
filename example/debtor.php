<?php

use Hageman\REST\Debtor;

/**
 * Autoloader
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * The debtor class does not allow saving new debtors!
 * This is automatically done on invoice creation.
 *
 * Initialise the model without data.
 */
$debtor = new Debtor();

/**
 * It is possible to list debtors.
 * The list function always uses paged format, default: page 1, pageSize 10.
 * A list can hold $filter and $sort parameters, please see the docs (navigate to the base URL of the REST API) for usage and methods.
 * - Returns a paged response. The data will be available in the 'rows' entry of the response, if there is no error.
 * - Don't forget to tell the model to use the correct administration!
 */
$listedDebtors = $debtor->administration( 990001 )->list( $filter = array(), $sort = array() );

/**
 * Or with custom paging.
 */
$listedDebtors = $debtor->administration( 990001 )->paging($page = 1, $pageSize = 50)->list( $filter = array(), $sort = array() );

/**
 * Just not into paging, then try a custom query.
 * Keep in mind that there is a bottleneck, the larger the data the larger the timeout!
 * Keep the queries short and precise to fetch only the data that's needed.
 * - Don't forget to tell the model to use the correct administration!
 */
$receivedDebtors = $debtor->administration( 990001 )->query( $filter = array(), $sort = array() )->all();

/**
 * Or only the first entry.
 * - Returns a SINGLE model instead of an array of models (BATCH) like the all() function.
 */
$receivedDebtor = $debtor->administration( 990001 )->query( $filter = array(), $sort = array() )->one();

/**
 * Or even a specific debtor, by debtor number.
 */
$specificDebtor = $debtor->administration( 990001 )->read( $debtorNumber = '201900001' );
