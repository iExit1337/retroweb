<?php


namespace App\Controller;


use App\Model\News\NewsFactory;
use App\Widget\News\SliderWidget;
use System\App\View\Template;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;

class CommunityController extends WebsiteController implements IRoutable
{
    /**
     * @var Point
     */
    private $_communityTab;
    /**
     * @var Point
     */
    private $_subCommunityTab;

    public function onRegistration(): void
    {
        $this->_communityTab = new Point("Community", "Community", $this->getApp()->getConfig()->get("site", "url") . "community", 9998);
        $this->_subCommunityTab = new Point("Community.Community", "Community", $this->getApp()->getConfig()->get("site", "url") . "community", 9999);

        $this->_communityTab->add($this->_subCommunityTab);
        $this->getNavigation()->add($this->_communityTab);
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            new Route(RequestType::GET, '/community', 'community')
        ];
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {
        $this->_communityTab->setActive(true);
        $this->_subCommunityTab->setActive(true);

        $this->setPageTitle("Community");

        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("community/Community.tpl.php");

        /**
         * @var $sliderWidget SliderWidget
         */
        $sliderWidget = $this->getWidget(SliderWidget::class);
        $sliderWidget->setGrid(10);

        /**
         * @var $newsFactory NewsFactory
         */
        $newsFactory = $this->getFactoryManager()->get(NewsFactory::class);

        $sliderWidget->setNewsFactory($newsFactory);
        $sliderWidget->setMaxSlides(5);

        $template->sliderWidget = $sliderWidget;

        $this->display($template);
    }
}