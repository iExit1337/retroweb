<?php

namespace App\Controller;

use App\Model\Campaign\CampaignFactory;
use App\Model\Event\EventFactory;
use App\Model\News\NewsFactory;
use App\Widget\Campaign\CampaignWidget;
use App\Widget\Events\ListWidget;
use App\Widget\News\SliderWidget;
use System\App\View\Template;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;

class MeController extends WebsiteController implements IRoutable
{
    /**
     * @var Point
     */
    private $_meTab;
    /**
     * @var Point
     */
    private $_homeTab;

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {

        $routes = [];

        if ($this->minRank(1)) {
            $routes[] = new Route(RequestType::GET, '/me', 'me-page');
        }

        return $routes;
    }

    public function onRegistration(): void
    {

        if (($user = $this->getSession()->getUser()) != null) {
            $this->_meTab = new Point("Me", $user->get("username"), $this->getApp()
                    ->getConfig()
                    ->get("site", "url") . "me", 9999);
            $this->_homeTab = new Point("Me.Me", "Home", $this->getApp()
                    ->getConfig()
                    ->get("site", "url") . "me", 9999);

            $this->_meTab->add($this->_homeTab);
            $this->getNavigation()->add($this->_meTab);
        }
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {

        $this->_meTab->setActive(true);
        $this->_homeTab->setActive(true);

        $user = $this->getSession()->getUser();

        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("me/Me.tpl.php");

        if (date('Ymd', (int)$user->get("last_daily_timestamp")) < date('Ymd', time())) {

            $receivedDailyBonus = $user->receiveDailyBonus();
            if ($receivedDailyBonus) {
                $template->received_daily_bonus = true;
            }
        }

        $this->addCSSFile("me/Me");

        $this->setPageTitle($this->getSession()->getUser()->get("username"));

        /**
         * @var $sliderWidget SliderWidget
         */
        $sliderWidget = $this->getWidget(SliderWidget::class);
        $sliderWidget->setGrid(6);

        /**
         * @var $newsFactory NewsFactory
         */
        $newsFactory = $this->getFactoryManager()->get(NewsFactory::class);

        $sliderWidget->setNewsFactory($newsFactory);
        $sliderWidget->setMaxSlides(5);

        /**
         * @var $listWidget ListWidget
         */
        $listWidget = $this->getWidget(ListWidget::class);
        $listWidget->setGrid(8);
        /**
         * @var $eventFactory EventFactory
         */
        $eventFactory = $this->getFactoryManager()->get(EventFactory::class);
        $listWidget->setEventFactory($eventFactory);

        /**
         * @var $campaignWidget CampaignWidget
         */
        $campaignWidget = $this->getWidget(CampaignWidget::class);
        $campaignWidget->setGrid(8);
        /**
         * @var $campaignFactory CampaignFactory
         */
        $campaignFactory = $this->getFactoryManager()->get(CampaignFactory::class);
        $campaignWidget->setCampaignFactory($campaignFactory);

        $template->campaignWidget = $campaignWidget;
        $template->listWidget = $listWidget;
        $template->sliderWidget = $sliderWidget;

        $this->display($template);
    }
}