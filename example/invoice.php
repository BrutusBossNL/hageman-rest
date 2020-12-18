<?php

use Hageman\REST\Invoice;

/**
 * Creating an invoice can be done in a few ways. Which are described below.
 * An invoice's entry value can be changed at any moment before the save() function is called.
 * The save() function has to be called to send the invoice towards the API.
 *
 * Initialise the model without data.
 * An instance will be created with default values for the invoice.
 */
$invoice = new Invoice();

/**
 * Or with data from the start.
 * The data array may be in dotted format or a multidimensional array, even both!
 */
$invoice = new Invoice( $data = array(
    'prefix' => 'TEST',
    'reference' => 'TEST ORDER #1',
    'date' => array(
        'invoice' => '2020-01-01 08:00:00'
    ),
    'debtor.credit.vatNumber' => '123456789-B01'
) );

/**
 * It's possible to reinitialise a loaded model.
 * Again, data may be given from the start.
 */
$invoice->new( $data = array() );

/**
 * Set some values before saving to the database.
 * It's possible to set a value of a levelled entry by using a double underscore.
 */
$invoice->prefix = 'TEST';
$invoice->reference = 'TEST ORDER #1';
$invoice->date__invoice = '2020-01-01 08:00:00';
$invoice->debtor__credit__vatNumber = '123456789-B01';

/**
 * Extend an older value of an entry.
 */
$invoice->reference = $invoice->reference . '_change';

/**
 * The instance can be echoed to get a json formatted string.
 */
echo $invoice;

/**
 * Tell the model to write in/read from the correct administration, or an error will be returned.
 */
$invoice->administration( 990001 );

/**
 * Save the instance.
 * - returns the saved invoice.
 */
$savedInvoice = $invoice->save();

/**
 * There is a possibility to do a secured save.
 * The function will first check for existence before saving (no duplicates).
 * If the specific date (and time) is known, the date (and time) can be part of the 'unique' check.
 * - Returns the saved/existing invoice.
 */
$savedInvoice = $invoice->saveIfNotExists( $reference = 'TEST ORDER #1_change', $date = '2020-01-01' );

/**
 * It is possible to list invoices.
 * The list function always uses paged format, default: page 1, pageSize 10.
 * A list can hold $filter and $sort parameters, please see the docs (navigate to the base URL of the REST API) for usage and methods.
 * - Returns a paged response. The data will be available in the 'rows' entry of the response, if there is no error.
 * - Don't forget to tell the model to use the correct administration!
 */
$listedInvoices = $invoice->administration( 990001 )->list( $filter = array(), $sort = array() );

/**
 * Or with custom paging.
 */
$listedInvoices = $invoice->administration( 990001 )->paging($page = 1, $pageSize = 50)->list( $filter = array(), $sort = array() );

/**
 * Just not into paging, then try a custom query.
 * Keep in mind that there is a bottleneck, the larger the data the larger the timeout!
 * Keep the queries short and precise to fetch only the data that's needed.
 * - Don't forget to tell the model to use the correct administration!
 */
$receivedInvoices = $invoice->administration( 990001 )->query( $filter = array(), $sort = array() )->all();

/**
 * Or only the first entry.
 * - Returns a SINGLE model instead of an array of models (BATCH) like the all() function.
 */
$receivedInvoice = $invoice->administration( 990001 )->query( $filter = array(), $sort = array() )->one();

/**
 * Or even a specific invoice, by invoice number.
 */
$specificInvoice = $invoice->administration( 990001 )->read( $invoiceNumber = 'TEST202000001' );
