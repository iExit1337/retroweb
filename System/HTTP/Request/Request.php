<?php


namespace System\HTTP\Request;


class Request
{

	/**
	 * @var array
	 */
    private $_get = [];

	/**
	 * @var array
	 */
    private $_post = [];

	/**
	 * @var array
	 */
    private $_request = [];

	/**
	 * @var string
	 */
    private $_method;

	/**
	 * @var string
	 */
    private $_uri;

	/**
	 * @var int
	 */
    private $_time;

	/**
	 * Request constructor.
	 *
	 * @param array  $get
	 * @param array  $post
	 * @param array  $request
	 * @param string $method
	 * @param string $uri
	 * @param int    $time
	 */
    public function __construct(array $get, array $post, array $request, string $method, string $uri, int $time)
    {
        $this->_get = $get;
        $this->_post = $post;
        $this->_request = $request;
        $this->_uri = $uri;
        $this->_time = $time;

        switch ($method) {
            case RequestType::POST:
                $this->_method = RequestType::POST;
                break;

            case RequestType::GET:
                $this->_method = RequestType::GET;
                break;

            case RequestType::HEAD:
                $this->_method = RequestType::HEAD;
                break;

            case RequestType::PUT:
                $this->_method = RequestType::PUT;
                break;
        }
    }

	/**
	 * @return string
	 */
    public function getMethod(): string
    {
        return $this->_method;
    }

	/**
	 * @return string
	 */
    public function getUri(): string
    {
        return $this->_uri;
    }

	/**
	 * @return int
	 */
    public function getTime(): int
    {
        return $this->_time;
    }

	/**
	 * @param null|string $index
	 *
	 * @return array|null|string
	 */
    public function getParam(?string $index = null)
    {
        return $this->getData(RequestType::GET, $index);
    }

	/**
	 * @param null|string $index
	 *
	 * @return array|null|string
	 */
    public function getPost(?string $index = null)
    {
        return $this->getData(RequestType::POST, $index);
    }

	/**
	 * @param null|string $index
	 *
	 * @return array|null|string
	 */
    public function getRequest(?string $index = null)
    {
        return $this->getData(RequestType::REQUEST, $index);
    }

    /**
     * @param string $type
     * @param string $index
     * @return array|string|null
     */
    public function getData(string $type, string $index)
    {
        $data = [];

        switch ($type) {
            case RequestType::GET:
                $data = $this->_get;
                break;

            case RequestType::POST:
                $data = $this->_post;
                break;

            case RequestType::REQUEST:
                $data = $this->_request;
                break;
        }

        if ($index == null) {
            return $data;
        }

        if (!isset($data[$index])) {
            return null;
        }

        return $data[$index];
    }
}