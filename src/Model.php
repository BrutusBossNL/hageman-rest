<?php

namespace Hageman\REST;

use Hageman\REST\Support\Arr;
use JsonSerializable;

/**
 * Class Model
 * @package Hageman\REST
 *
 * This class is an extension of the API class that handles requests towards the Hageman REST API.
 */
class Model extends API implements JsonSerializable
{
    private $instance = [];
    private $errors = [];

    /**
     * Return all rows, or response if the rows entry is not available
     *
     * @param string $uri
     * @return mixed
     */
    protected function getAll(string $uri)
    {
        ini_set('max_execution_time', -1);

        $pageSize = 100;
        $this->paging(1, $pageSize);

        $response = $this->get($uri);
        if(!isset($response->rows) || !isset($response->_lastPage)) return $response;

        $collection = $response->rows;
        $lastPage = $response->_lastPage;
        if($lastPage > 2) {
            for ($page = 2; $page <= $lastPage; $page++) {
                $this->paging($page, $pageSize);

                $response = $this->get($uri);
                if(!isset($response->rows) || !isset($response->_lastPage)) return $response;

                $collection = array_merge($collection, $response->rows);
            }
        }

        return $collection;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return Arr::get($this->instance, $key, false);
    }

    /**
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value) :void
    {
        $key = str_replace('__', '.', $key);
        if(Arr::get($this->instance, $key, '___NA___') !== '___NA___') {
            Arr::set($this->instance, $key, $value);
        }
    }

    /**
     * @return string
     */
    public function __toString() :string
    {
        return json_encode($this->instance) ?? '';
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function errors() :array
    {
        return $this->errors;
    }

    /**
     * GET response from API
     *
     * @param string $uri
     * @return mixed
     */
    public function get(string $uri)
    {
        $this->type = 'GET';
        $this->uri = $uri;
        return $this->response();
    }

    /**
     * Serialize class to array
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->instance;
    }

    /**
     * Make a new model instance
     *
     * @param array $instance
     * @param array $data
     * @return $this
     */
    public function make(array $instance, $data = []) :Model
    {
        $data = Arr::dot($data);
        $this->instance = [];
        foreach($instance as $entry => $default) {
            Arr::set($this->instance, $entry, Arr::get($data, $entry, $default));
        }

        return $this;
    }

    /**
     * POST instance to the API.
     *
     * @param string $uri
     * @return mixed
     */
    public function post(string $uri)
    {
        $this->paging = false;
        $this->type = 'POST';
        $this->uri = $uri;
        $this->body($this->instance);
        return $this->response();
    }

    /**
     * Adds the necessary parameters to the request to enable filtering and sorting.
     *
     * @param array|null $filter
     * @param array|null $sort
     * @return $this
     */
    public function query(array $filter = null, array $sort = null) :API
    {
        if(!is_null($filter)) $this->addParameter('_filter', $filter);
        if(!is_null($sort)) $this->addParameter('_sort', $sort);
        return $this;
    }
}
