<?php


namespace System\App\View;


class View
{

    /**
     * @var string
     */
    private $_dir;

    /**
     * @var Template[]
     */
    private $_templates = [];

    /**
     * @param string $dir
     */
    public function setDirectory(string $dir): void
    {
        $this->_dir = $dir . ($dir[strlen($dir) - 1] == '/' ? '' : '/');
    }

    /**
     * @param string $filePath
     *
     * @return Template
     */
    public function getTemplate(string $filePath): Template
    {
        return $this->_templates[$filePath] ?? $this->createTemplate($filePath);
    }

    /**
     * @param string $filePath
     *
     * @return Template
     */
    public function createTemplate(string $filePath): Template
    {
        $template = new Template($this, $this->_dir . $filePath);
        $this->_templates[] = $template;

        return $template;
    }

    /**
     * @param string $string
     *
     * @return string|null
     */
    public function filter(?string $string): string
    {
        return htmlspecialchars($string, ENT_COMPAT, 'utf-8');
    }
}