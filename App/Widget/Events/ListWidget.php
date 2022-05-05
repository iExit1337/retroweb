<?php

namespace App\Widget\Events;


use App\Model\Event\EventFactory;
use System\App\View\Template;
use System\App\Widget;

class ListWidget extends Widget
{

    private $_grid = 16;
    /**
     * @var $_eventFactory EventFactory
     */
    private $_eventFactory;

    /**
     * @return Template
     */
    protected function getTemplate(): Template
    {
        return $this->getView()->createTemplate("events/List.tpl.php");
    }

    /**
     * @return array
     */
    protected function getCSSFiles(): array
    {
        return [
            "event/List"
        ];
    }

    /**
     * @return array
     */
    protected function getJSFiles(): array
    {
        return [

        ];
    }

    /**
     * @return int
     */
    public function getGrid(): int
    {
        return $this->_grid;
    }

    /**
     * @param EventFactory $eventFactory
     */
    public function setEventFactory(EventFactory $eventFactory): void
    {
        $this->_eventFactory = $eventFactory;
    }

    /**
     * @param int $grid
     */
    public function setGrid(int $grid): void
    {
        $this->_grid = $grid;
    }

    protected function onDisplay(): void
    {
        $this->set('activeEvents', $this->_eventFactory->getActiveEvents());
        $this->set('upcomingEvents', $this->_eventFactory->getUpcomingEvents());
        $this->set("grid", $this->_grid);
    }
}