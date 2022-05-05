<?php

namespace App\Service;

use System\App\Service\Service;
use System\Helpers\LightOpenID;

class SteamService extends Service
{

    private $_lightOpenID;

    private $_returnUrl = "";

    private $_callbacks = [
        "cancel" => [],
        "valid" => [],
        "invalid" => [],
        "init" => []
    ];

    public function onConstruction(): void
    {

        $this->_returnUrl = $this->getApp()->getConfig()->get("site", "url");
    }

    /**
     * @param string $url
     */
    public function setReturnUrl(string $url): void
    {

        $this->_returnUrl = $url;
    }

    /**
     * @param callable $onCancel
     */
    public function onCancel(callable $onCancel): void
    {

        $this->_callbacks["cancel"][] = $onCancel;
    }

    /**
     * @param callable $onValid
     * @param callable $onInvalid
     */
    public function onValidate(callable $onValid, callable $onInvalid): void
    {

        $this->onValid($onValid);
        $this->onInValid($onInvalid);
    }

    /**
     * @param callable $onValid
     */
    public function onValid(callable $onValid): void
    {

        $this->_callbacks["valid"][] = $onValid;
    }

    /**
     * @param callable $onInvalid
     */
    public function onInvalid(callable $onInvalid): void
    {

        $this->_callbacks["invalid"][] = $onInvalid;
    }

    /**
     * @param callable $onInit
     */
    public function onInit(callable $onInit): void
    {

        $this->_callbacks["init"][] = $onInit;
    }

    /**
     * @return LightOpenID
     */
    public function getLightOpenID(): LightOpenID
    {

        return $this->_lightOpenID;
    }

    /**
     * @param string $key
     */
    private function trigger(string $key): void
    {

        foreach ($this->_callbacks[$key] as $callback) {
            $callback($this);
        }
    }

    public function execute(): void
    {
        $this->_lightOpenID = new LightOpenID($this->_returnUrl);
        $this->_lightOpenID->returnUrl = $this->_returnUrl;

        if (!$this->_lightOpenID->mode) {
            $this->trigger("init");
        } else {
            if ($this->_lightOpenID->mode == 'cancel') {
                $this->trigger("cancel");
            } else {
                if ($this->_lightOpenID->validate()) {
                    $this->trigger("valid");
                } else {
                    $this->trigger("invalid");
                }
            }
        }
    }
}