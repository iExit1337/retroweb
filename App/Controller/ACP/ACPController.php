<?php

namespace App\Controller\ACP;

use App\Controller\WebsiteController;
use System\App\View\Template;
use System\Navigation\Point;

class ACPController extends WebsiteController
{
    public function onRegistration(): void
    {
        if ($this->minRank($this->getMinRank("acp_tab"))) {
            $navigation = $this->getNavigation();
            if ($this->getNavigationPoint() == null) {
                $adminPoint = new Point("ACP", "Admin", $this->getApp()->getConfig()->get("site", "url") . "admin", 0);

                $navigation->add($adminPoint);
            }
        }
    }

    /**
     * @param string $permission
     * @return int
     */
    public function getMinRank(string $permission): int
    {
        return (int)$this->getApp()->getConfig()->get("acp_min_ranks", $permission);
    }

    /**
     * @return Point|null
     */
    public function getNavigationPoint(): ?Point
    {
        return $this->getNavigation()->getById("ACP");
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle(string $pageTitle): void
    {
        parent::setPageTitle($pageTitle . " - Housekeeping");
    }

    /**
     * @param string $permission
     */
    public function onlyWithPermission(string $permission): void
    {
        if (!$this->minRank($this->getMinRank($permission))) {
            $this->redirect("");
        }
    }
}