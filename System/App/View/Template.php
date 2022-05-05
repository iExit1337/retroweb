<?php


namespace System\App\View;


class Template
{

	/**
	 * @var View
	 */
    private $_view;

	/**
	 * @var string
	 */
    private $_file;

	/**
	 * @var array
	 */
    private $_variables = [];

	/**
	 * @var array
	 */
    private $_functions = [];

	/**
	 * Template constructor.
	 *
	 * @param View   $view
	 * @param string $file
	 */
    public function __construct(View $view, string $file)
    {
        $this->_view = $view;
        $this->_file = $file;
    }

	/**
	 * @param string $var
	 * @param mixed $val
	 */
    public function __set(string $var, $val): void
    {
        $this->set($var, $val);
    }

	/**
	 * @param string $var
	 * @param mixed $val
	 *
	 * @return Template
	 */
    public function set(string $var, $val): Template
    {
        if (is_callable($val)) {
            $this->_functions[$var] = $val;
        } else {
            $this->_variables[$var] = $val;
        }

        return $this;
    }

	/**
	 * @param string $name
	 * @param array $arguments
	 *
	 * @return mixed
	 */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array($this->_functions[$name], $arguments);
    }

	/**
	 * @param string $string
	 *
	 * @return string|null
	 */
    public function filter(?string $string): string
    {
        return $this->_view->filter($string);
    }

    /**
     * @return View
     */
    public function getView(): View
    {
        return $this->_view;
    }

    public function display(): void
    {
        foreach ($this->_variables as $variable => $value) {
            ${$variable} = $value;
        }

        require $this->_file;
    }
}