<?php

namespace App\Widget\Settings;

use App\Widget\ACP\Navigation\ACPNavigationWidget;
use System\App\View\Template;
use System\Navigation\Point;

class NavigationWidget extends ACPNavigationWidget
{

    protected function onConstruct(): void
    {
        $url = $this->getConfig()->get("site", "url");
        $this->addPoint(new Point("Settings.Account", "Account", $url . "settings", 9999));
		$this->addPoint(new Point("Settings.Mail", "E-Mail", $url . "settings/mail", 9999));
		$this->addPoint(new Point("Settings.Password", "Passwort", $url . "settings/password", 9999));
		$this->addPoint(new Point("Settings.Profile", "Profil", $url . "settings/profile", 9999));
        $this->addPoint(new Point("Settings.Friends", "Freunde", $url . "settings/friends", 9999));
        $this->addPoint(new Point("Settings.Connections", "Verkn&uuml;pfungen", $url . "settings/connections", 9999));
    }

    /**
     * @return Template
     */
    protected function getTemplate(): Template
    {
        return $this->getView()->createTemplate("settings/Navigation.tpl.php");
    }

    /**
     * @return array
     */
    protected function getCSSFiles(): array
    {
        return [
            "settings/Navigation"
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