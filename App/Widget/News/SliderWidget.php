<?php

namespace App\Widget\News;

use App\Model\News\NewsFactory;
use System\App\View\Template;
use System\App\Widget;

class SliderWidget extends Widget
{

    private $_grid = 10;
    private $_maxSlides = 5;
    /**
     * @var $_newsFactory NewsFactory
     */
    private $_newsFactory;

    /**
     * @return Template
     */
    protected function getTemplate(): Template
    {
        return $this->getView()->createTemplate("news/Slider.tpl.php");
    }

    /**
     * @return array
     */
    protected function getCSSFiles(): array
    {
        return [
            "news/Slider"
        ];
    }

    /**
     * @return array
     */
    protected function getJSFiles(): array
    {
        return [
            "news/Slider"
        ];
    }

    public function setNewsFactory(NewsFactory $newsFactory): void
    {
        $this->_newsFactory = $newsFactory;
    }

    public function setGrid(int $grid): void
    {
        $this->_grid = $grid;
    }

    public function setMaxSlides(int $maxSlides): void
    {
        $this->_maxSlides = $maxSlides;
    }

    public function getGrid(): int
    {
        return $this->_grid;
    }

    /**
     * @throws \Exception
     */
    protected function onDisplay(): void
    {
        $news = $this->_newsFactory->getLatestByLimit($this->_maxSlides);
        $this->set('grid', $this->_grid);
        $this->set('news', $news);
        $this->set('newsCount', count($news));
    }
}