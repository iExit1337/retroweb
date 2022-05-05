<?php

namespace App\Controller\ACP;

use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;

class AdminController extends ACPController implements IRoutable
{
    /**
     * @var $_adminTab Point
     */
    private $_adminTab;

    public function onRegistration(): void
    {
        parent::onRegistration();

        if ($this->minRank($this->getMinRank("acp_tab"))) {
            $this->_adminTab = new Point("ACP.Admin", "Startseite", $this->getApp()->getConfig()->get("site", "url") . "admin", 9999);
            $this->getNavigationPoint()->add($this->_adminTab);
        }
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            new Route(RequestType::GET, '/admin', 'admin')
        ];
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {
        $this->onlyWithPermission("acp");

        $this->getNavigationPoint()->setActive(true);
        $this->_adminTab->setActive(true);

        $this->setPageTitle("Admin");

        $template = $this->getView()->createTemplate('admin/Admin.tpl.php');

        $this->display($template);
    }
}