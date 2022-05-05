<?php

namespace App\Controller\ACP;

use App\Widget\ACP\Navigation\Homepage\NavigationWidget;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;

class HomepageController extends ACPController implements IRoutable
{

    /**
     * @var $_homepageTab Point
     */
    private $_homepageTab;

    public function onRegistration(): void
    {
        parent::onRegistration();

        if ($this->minRank($this->getMinRank("acp_tab")) && $this->minRank($this->getMinRank("homepage_tab"))) {
            $this->_homepageTab = new Point("ACP.Homepage", "Homepage", $this->getApp()->getConfig()->get("site", "url") . "admin/homepage", 9998);
            $this->getNavigationPoint()->add($this->_homepageTab);
        }
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            new Route(RequestType::GET, '/admin/homepage', 'homepages')
        ];
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {
        $this->onlyWithPermission("homepage");

        $this->getNavigationPoint()->setActive(true);
        $this->_homepageTab->setActive(true);

        $this->setPageTitle("Homepage");

        /**
         * @var $homepageNavigation NavigationWidget
         */
        $homepageNavigation = $this->getWidget(NavigationWidget::class);
        $homepageNavigation->setActive("ACP.Homepage.Homepage");

        $template = $this->getView()->createTemplate("admin/Homepage.tpl.php");
        $template->navigation = $homepageNavigation;

        $this->display($template);
    }
}