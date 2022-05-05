<?php


namespace App\Widget\ACP\Navigation;


use System\App\View\Template;
use System\App\View\View;
use System\App\Widget;
use System\Config;
use System\Navigation\Point;

abstract class ACPNavigationWidget extends Widget
{

	/**
	 * @var $_points Point[]
	 */
    private $_points = [];

    abstract protected function onConstruct(): void;

	/**
	 * @param Point $p
	 */
    public function addPoint(Point $p): void
    {
        $this->_points[] = $p;
    }

    public function __construct(View $view, Config $config)
    {
        parent::__construct($view, $config);

        $this->onConstruct();
    }

    /**
     * @param string $id
     */
    public function setActive(string $id): void
    {
        foreach ($this->_points as $point) {
            if ($point->getId() == $id) {
                $point->setActive(true);
            }
        }
    }

	/**
	 * @return Point[]
	 */
    public function getNavigationPoints(): array
    {
        return $this->_points;
    }
}