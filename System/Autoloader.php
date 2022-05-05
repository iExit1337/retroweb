<?php


namespace System;

/**
 * Class Autoloader
 * @package System
 */
class Autoloader
{
	/**
	 * @var string
	 */
    private $_path = '/';

	/**
	 * @var array
	 */
    private $_directories = [
        'App' => 'App',
        'App\Controller' => 'App/Controller',
        'App\Model' => 'App/Model',
        'App\View' => 'App/View',
        'App\Widget' => 'App/Widget',

        'System' => 'System',
        'System\App' => 'System/App'
    ];

	/**
	 * @var array
	 */
    private $_ignoreIdentifiers = [
        'FastRoute'
    ];

    /**
     * Autoloader constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->_path = $path . ($path[strlen($path) - 1] == '/' ? '' : '/');
    }

    public function loadCalledClasses(): void
    {
        spl_autoload_register(function ($className) {
            $classNameSplit = explode('\\', $className);
            if (in_array($classNameSplit[0], $this->_ignoreIdentifiers))
                return;

            $classNameSplitLength = count($classNameSplit);

            $namespace = implode('\\', array_slice($classNameSplit, 0, $classNameSplitLength - 1));
            $directory = isset($this->_directories[$namespace]) ? $this->_directories[$namespace] : str_replace('\\', '/', $namespace);
            $classFilePath = $this->_path . $directory . '/' . $classNameSplit[$classNameSplitLength - 1];

            require_once $classFilePath . '.php';
        });
    }
}