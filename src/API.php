<?php

namespace Hageman\REST;

/**
 * Class API
 * @package Hageman\REST
 *
 * This class is used to create requests towards the Hageman REST API.
 */
class API
{
    private const CONFIG_FILE = __DIR__ . '/config/api.php';
    private $url;
    private $validationErrors = [];
    protected $body = null;
    protected $headers = [];
    protected $parameters = [];
    protected $type = null;
    protected $uri = null;
    public $administration = true;
    public $paging;

    /**
     * API constructor.
     *
     * On API initialisation the URL and necessary headers will be set with values from the .env file.
     *
     * @noinspection PhpIncludeInspection
     */
    function __construct()
    {
        $api = require_once self::CONFIG_FILE;
        $this->url = $api['url'] ?? '';
        $this->addHeaders([
            'api-key' => $api['key'] ?? '',
            'api-secret' => $api['secret'] ?? '',
            'api-administration' => null
        ]);
        $this->paging = [
            'page' => 1,
            'pageSize' => 10
        ];
    }

    /**
     * @param array $settings
     */
    public static function config(array $settings) :void
    {
        $contents = ['<?php', '', 'return ['];
        foreach($settings as $k => $v) {
            $contents[] = "\t'{$k}' => '{$v}',";
        }
        $contents[] = '];';
        file_put_contents(self::CONFIG_FILE, implode(PHP_EOL, $contents));
    }

    /**
     * Test the API url and see if the response matches the expected result.
     *
     * @return bool
     */
    private function isListening() :bool
    {
        $endpoint = $this->url . '/status';
        if ($handle = @fopen($endpoint,"r")) {
            $status = stream_get_contents($handle);
            if (list($httpStatus) = get_headers($endpoint)) {
                return stristr($httpStatus, '200') !== false && $status === 'Listening';
            }
        }

        return false;
    }

    /**
     * Add header to the request.
     *
     * @param string $key
     * @param $value
     * @return $this
     */
    protected function addHeader(string $key, $value) :API
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Add multiple headers to the request.
     *
     * @param array $headers
     * @return $this
     */
    protected function addHeaders(array $headers) :API
    {
        foreach($headers as $k => $v) {
            $this->addHeader($k, $v);
        }
        return $this;
    }

    /**
     * Add a body to the request.
     *
     * @param null $content
     * @return API|string
     */
    protected function body($content = null)
    {
        if(is_null($content)) return is_iterable($this->body) ? http_build_query($this->body) : $this->body;

        $this->body = $content;
        return $this;
    }

    /**
     * Get the headers of the request.
     *
     * @param bool $asArray
     * @return array
     */
    protected function headers(bool $asArray = false) :array
    {
        if($asArray) return $this->headers;

        $h = [];
        foreach($this->headers as $k => $v) {
            $h[] = "{$k}: {$v}";
        }
        return $h;
    }

    /**
     * Get the parameters used in the request.
     *
     * @param bool $asArray
     * @return array|string
     */
    protected function parameters(bool $asArray = false)
    {
        return $asArray ? $this->parameters : http_build_query($this->parameters);
    }

    /**
     * Push the request to the API and return the response.
     *
     * @return mixed
     */
    protected function response()
    {
        if(!$this->validate()) return false;

        if($this->paging !== false) {
            $this->addParameters($this->paging);
        }

        $parameters = $this->parameters();
        $endpoint = $this->url . '/' . $this->uri . (!empty($parameters) ? '?' . $parameters : '');

        if($this->type === 'POST') $this->addHeader('Content-Type', 'application/x-www-form-urlencoded');

        $curl = curl_init();
        $curlOptions = [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->type,
            CURLOPT_POSTFIELDS => $this->body(),
            CURLOPT_HTTPHEADER => $this->headers(),
            CURLOPT_CAPATH => __DIR__ . '/certificate',
            CURLOPT_CAINFO => __DIR__ . '/certificate/curl-bundle.pem',
        ];
        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);


        curl_close($curl);

        return json_decode($response);
    }

    /**
     * Validate multiple tests on the API
     *
     * @return bool
     */
    protected function validate() :bool
    {
        if(is_null($this->url)) $this->validationErrors[] = 'REST API URL is missing from the .env file.';
        if(is_null($this->uri)) $this->validationErrors[] = 'Endpoint cannot be empty.';
        if(!$this->isListening()) $this->validationErrors[] = 'REST API URL is not accessible.';
        if($this->administration && empty($this->headers(true)['api-administration'] ?? null)) $this->validationErrors[] = 'Administration is not set, while the request requires one.';

        return empty($this->validationErrors);
    }

    /**
     * Add a parameter to the URL endpoint.
     *
     * @param string $key
     * @param $value
     * @return $this
     */
    public function addParameter(string $key, $value) :API
    {
        $this->parameters[$key] = $value;
        return $this;
    }

    /**
     * Add multiple parameters to the URL endpoint.
     *
     * @param array $parameters
     * @return $this
     */
    public function addParameters(array $parameters) :API
    {
        foreach($parameters as $k => $v) {
            $this->addParameter($k, $v);
        }
        return $this;
    }

    /**
     * Set the administration number in the header of the request.
     *
     * @param int $administration
     * @return $this
     */
    public function administration(int $administration) :API
    {
        $this->addHeader('api-administration', $administration);
        return $this;
    }

    /**
     * Set the paging for the response
     *
     * @param int $page
     * @param int $pageSize
     * @return $this
     */
    public function paging(int $page = 1, int $pageSize = 10) :API
    {
        $this->paging = [
            'page' => $page,
            'pageSize' => $pageSize
        ];

        return $this;
    }

    /**
     * Return the errors generated on validation failure.
     *
     * @return array
     */
    public function validationErrors() :array
    {
        return $this->validationErrors;
    }
}

