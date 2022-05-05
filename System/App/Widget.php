<?php


namespace System\App;


use System\App\View\Template;
use System\App\View\View;
use System\Config;

abstract class Widget
{

    /**
     * @var View
     */
    private $_view;

    /**
     * @var Config
     */
    private $_config;

    /**
     * @return Template
     */
    abstract protected function getTemplate(): Template;

    /**
     * @return array
     */
    abstract protected function getCSSFiles(): array;

    /**
     * @return array
     */
    abstract protected function getJSFiles(): array;

    abstract protected function onDisplay(): void;

    /**
     * Widget constructor.
     *
     * @param View $view
     * @param Config $config
     */
    public function __construct(View $view, Config $config)
    {
        $this->_view = $view;
        $this->_config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->_config;
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
        $this->onDisplay();

        $files = ["css" => [], "js" => []];
        foreach ($this->getCSSFiles() as $file) {
            $files["css"][] = $this->getConfig()->get("site", "url") . 'public/css/' . $file . '.css?v=' . $this->getConfig()->get('site', 'version');
        }

        foreach ($this->getJSFiles() as $file) {
            $files["js"][] = $this->getConfig()->get("site", "url") . 'public/js/' . $file . '.js?v=' . $this->getConfig()->get('site', 'version');
        }

        $template = $this->getTemplate();
        $filesTemplate = $this->getView()->createTemplate("widgets/PreTemplate.tpl.php");
        $filesTemplate->set('files', $files);

        foreach ($this->_data as $var => $val) {
            $template->set($var, $val);
        }

        $template->set('config', $this->getConfig());

        $filesTemplate->display();
        $template->display();
    }

    /**
     * @var array
     */
    private $_data = [];

    /**
     * @param string $var
     * @param        $val
     */
    public function set(string $var, $val): void
    {
        $this->_data[$var] = $val;
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
     *
     * @return mixed
     */
    public function __get(string $var)
    {
        return $this->_data[$var];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->display();
        return "";
    }
}