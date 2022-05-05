<?php

namespace System\Helpers;

/**
 * Class JSONWriter
 * @package System\Helpers
 */
final class JSONWriter
{

    /**
     * @var array
     */
    private $_val = [];

    /**
     * @var bool
     */
    private $_isObject = true;

    public function __construct(bool $isObject = true)
    {
        $this->_isObject = $isObject;
    }

    /**
     * @param mixed $var
     * @param mixed|null $val
     */
    public function write($var, $val = null): void
    {
        if (!$this->_isObject) {
            $this->_val[] = $var;
        } else {
            $this->_val[$var] = $val;
        }
    }

    /**
     * @param mixed $var
     * @param mixed|null $val
     */
    public function __set($var, $val): void
    {
        $this->write($var, $val);
    }

    /**
     * @param string $var
     * @return mixed
     */
    public function __get(string $var)
    {
        return $this->_val[$var];
    }

    /**
     * @param string $var
     *
     * @return bool
     */
    public function __isset(string $var): bool
    {
        return isset($this->_val[$var]);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        header('Content-type: application/json');
        return $this->getJson();
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return json_encode($this->_val);
    }

    /**
     * @param string $var
     */
    public function remove(string $var): void
    {
        if (isset($this->_val[$var])) {
            unset($this->_val[$var]);
        }
    }
}