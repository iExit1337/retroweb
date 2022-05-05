<?php

namespace App\Model\Alert;


use System\App\Model\AbstractFactoryChildModel;

class Alert extends AbstractFactoryChildModel
{
    public function getIcon(): string
    {
        switch ($this->getInt("type")) {
            case 1: // Aussage
                $icon = "fa-info-circle";
                break;

            case 2: // Frage
                $icon = "fa-question-circle";
                break;

            default:
                $icon = "";
        }

        return $icon;
    }
}