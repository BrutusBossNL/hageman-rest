<?php

namespace Hageman\REST;

/**
 * Class Administration
 * @package Hageman\REST
 *
 * This class is an extension of the Model class.
 * Used to handle requests specifically for the collection that matches the class name.
 */
class Administration extends Model
{
    private const SINGULAR = 'administration';
    private const MULTIPLE = 'administrations';

    /**
     * Administration constructor.
     */
    function __construct()
    {
        /* Run the parent constructor */
        parent::__construct();

        /* Tell the class that an administration IS NOT required for each request */
        $this->administration = false;
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
     * Get a list of administration, allows filter and sort parameters
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
}
