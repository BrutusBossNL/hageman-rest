<?php

namespace Hageman\REST;

/**
 * Class Invoice
 * @package Hageman\REST
 *
 * This class is an extension of the API class that handles requests towards the Hageman REST API.
 * Used to handle requests specifically for the collection that matches the class name.
 */
class Invoice extends Model
{
    private const SINGULAR = 'invoice';
    private const MULTIPLE = 'invoices';

    /**
     * Invoice constructor.
     *
     * @param array $data
     */
    function __construct(array $data = [])
    {
        parent::__construct();
        $this->make($this::init(), $data);
    }

    /**
     * Return the default instance of this class
     *
     * @return array
     */
    private static function init() :array
    {
        return [
            'prefix' => null,
            'reference' => null,
            'subject' => null,
            'currency' => 'EUR',
            'language' => 'nl',
            'digital' => false,
            'sent' => false,
            'date.invoice' => date('Y-m-d H:i:s'),
            'date.payment' => null,
            'heartbeat.reminder' => 14,
            'heartbeat.exhortation' => 7,
            'heartbeat.notice' => 7,
            'paid' => false,
            'payment.transactionId' => null,
            'payment.provider' => null,
            'payment.reference' => null,
            'payment.url' => null,
            'payment.iban' => null,
            'payment.bic' => null,
            'debtor.debtorNumber' => null,
            'debtor.name' => null,
            'debtor.contact' => null,
            'debtor.phoneNumber' => null,
            'debtor.emailAddress' => null,
            'debtor.credit.vatNumber' => null,
            'debtor.address.street' => null,
            'debtor.address.streetNumber' => null,
            'debtor.address.addition' => null,
            'debtor.address.address2' => null,
            'debtor.address.postalCode' => null,
            'debtor.address.city' => null,
            'debtor.address.country' => null,
            'items.0.type' => null,
            'items.0.number' => null,
            'items.0.description' => null,
            'items.0.information' => null,
            'items.0.quantity' => 0,
            'items.0.price' => 0,
            'items.0.vat' => 0,
        ];
    }

    /**
     * Return all rows, or response if the rows entry is not available
     *
     * @return mixed
     */
    public function all()
    {
        return $this->getAll(self::MULTIPLE);
    }

    /**
     * Get a list of invoices, allows filter and sort parameters.
     *
     * @param array|null $filter
     * @param array|null $sort
     * @return mixed
     */
    public function list(array $filter = null, array $sort = null)
    {
        return $this->query($filter, $sort)->get(self::MULTIPLE);
    }

    /**
     * Create new instance.
     *
     * @param array $data
     * @return Invoice|Model
     */
    public function new(array $data = [])
    {
        return $this->make($this::init(), $data);
    }

    /**
     * Return one row of data.
     *
     * @return mixed
     */
    public function one()
    {
        $this->paging = false;
        return $this->get(self::MULTIPLE)->rows[0] ?? $this->get(self::MULTIPLE);
    }

    /**
     * Get a specific invoice based on invoice number, or when null return the list function
     *
     * @param string|null $invoiceNumber
     * @return mixed
     */
    public function read(string $invoiceNumber = null)
    {
        if(is_null($invoiceNumber)) return $this->list();

        $this->paging = false;
        return $this->get(self::SINGULAR . '/' . $invoiceNumber);
    }

    /**
     * @return mixed
     */
    public function save()
    {
        return $this->post(self::SINGULAR);
    }

    /**
     * @param string $reference
     * @param string|null $date
     * @return mixed
     */
    public function saveIfNotExists(string $reference, string $date = null)
    {
        $filter = ['reference' => $reference];
        if(!is_null($date)) $filter['date.invoice'] = ['matches' => "%{$date}%"];

        $existing = $this->query($filter)->one();
        if($existing) return $existing;

        return $this->post(self::SINGULAR);
    }
}
