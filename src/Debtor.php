<?php

namespace Hageman\REST;

/**
 * Class Debtor
 * @package Hageman\REST
 *
 * This class is an extension of the Model class.
 * Used to handle requests specifically for the collection that matches the class name.
 */
class Debtor extends Model
{
    private const SINGULAR = 'debtor';
    private const MULTIPLE = 'debtors';

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
     * Get a list of debtors, allows filter and sort parameters
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
     * Get a specific debtor based on debtor number, or when null return the list function
     *
     * @param string|null $debtorNumber
     * @return mixed
     */
    public function read(string $debtorNumber = null)
    {
        if(is_null($debtorNumber)) return $this->list();

        $this->paging = false;
        return $this->get(self::SINGULAR . '/' . $debtorNumber);
    }
}
