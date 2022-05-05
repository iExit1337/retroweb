<?php


namespace System\HTTP;


class Route
{
    /**
     * @var array|string
     */
    private $_method;

    /**
     * @var string
     */
    private $_route;

    /**
     * @var string
     */
    private $_handler;

    /**
     * Route constructor.
     *
     * @param array|string $method
     * @param string $route
     * @param string $handler
     */
    public function __construct($method, string $route, string $handler)
    {
        $this->_method = $method;
        $this->_route = $route;
        $this->_handler = $handler;
    }

    /**
     * @return string
     */
    public function getHandler(): string
    {
        return $this->_handler;
    }

    /**
     * @return array|string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->_route;
    }
}