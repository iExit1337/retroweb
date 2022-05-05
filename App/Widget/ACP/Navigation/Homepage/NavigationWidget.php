<?php


namespace App\Widget\ACP\Navigation\Homepage;


use App\Widget\ACP\Navigation\ACPNavigationWidget;
use System\App\View\Template;
use System\Navigation\Point;

class NavigationWidget extends ACPNavigationWidget
{

    protected function onConstruct(): void
    {
        $url = $this->getConfig()->get("site", "url");
        $this->addPoint(new Point("ACP.Homepage.Homepage", "Homepage", $url . "admin/homepage", 9999));
        $this->addPoint(new Point("ACP.Homepage.News", "News", $url . "admin/homepage/news", 9999));
        $this->addPoint(new Point("ACP.Homepage.Alerts", "Alerts", $url . "admin/homepage/alerts", 9999));
        $this->addPoint(new Point("ACP.Homepage.Events", "Events", $url . "admin/homepage/events", 9999));
        $this->addPoint(new Point("ACP.Homepage.Campaigns", "Campaigns", $url . "admin/homepage/campaigns", 9999));
    }

    /**
     * @return Template
     */
    protected function getTemplate(): Template
    {
        return $this->getView()->createTemplate("admin/Navigation.tpl.php");
    }

    /**
     * @return array
     */
    protected function getCSSFiles(): array
    {
        return [
            "admin/Navigation"
        ];
    }

    /**
     * @return array
     */
    protected function getJSFiles(): array
    {
        return [];
    }

    protected function onDisplay(): void
    {
        $this->set('navigation_points', $this->getNavigationPoints());
    }
}