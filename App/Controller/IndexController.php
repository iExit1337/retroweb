<?php

namespace App\Controller;

use App\Model\User\Connections\ConnectionTypes;
use App\Model\User\User;
use App\Model\User\UserFactory;
use App\Service\SteamService;
use System\Helpers\Hash\Hash;
use System\Helpers\Hash\RawText;
use System\Helpers\LightOpenID;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;

class IndexController extends WebsiteController implements IRoutable
{

    public function onRegistration(): void
    {

        if ($this->minRank(-1)) {
            $this->getNavigation()->add(new Point('Login', 'Einloggen / Registrieren', $this->getApp()
                ->getConfig()
                ->get("site", "url"), 10000));
        }
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {

        return [
            new Route(RequestType::GET, '/index', 'index'),
            new Route(RequestType::GET, '/', 'index'),
            new Route([
                RequestType::GET,
                RequestType::POST
            ], '/index/login', 'login'),
            new Route(RequestType::GET, '/index/login/not_connected', 'not-connected'),
            new Route(RequestType::GET, '/login/steam', 'steam-login'),
            new Route(RequestType::GET, '/steamlogin', 'steam-login'),
            new Route(RequestType::GET, '/index/login/error/{info}', 'error')
        ];
    }

    private function steamLogin(): void
    {

        /**
         * @var $steamService SteamService
         */
        $steamService = $this->getServiceCollector()->get(SteamService::class);
        $steamService->setReturnUrl($this->getApp()->getConfig()->get("site", "url") . "index.php?" . $this->getApp()
                ->getConfig()
                ->get("site", "token") . "=/steamlogin");
        $steamService->onCancel(function (SteamService $service) {

            $this->redirect("settings/connections");
        });

        $steamService->onValidate(function (SteamService $service) {

            $id = $service->getLightOpenID()->identity;
            $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
            preg_match($ptn, $id, $matches);

            $steamId = $matches[1];
            $userFactory = $this->getFactoryManager()->get(UserFactory::class);
            $user = $userFactory->getByConnection(ConnectionTypes::STEAM, ["steam_id" => $steamId]);

            if ($user != null) {
                $this->loginByUser($user);
            } else {
                $this->redirect("index/login/not_connected");
            }
        }, function (SteamService $service) {
            $this->redirect("index/login/error/" . base64_encode(serialize(json_encode((object)["message" => 'Validierung konnte nicht erfolgreich abgeschlossen werden.']))));

        });

        $steamService->onInit(function (SteamService $service) {

            $service->getLightOpenID()->identity = 'http://steamcommunity.com/openid/?l=english';
            header('Location: ' . $service->getLightOpenID()->authUrl());
            exit;
        });

        $steamService->execute();
    }

    public function loginByUser(User $user): void
    {

        $userBan = $user->getLatestBan();
        if ($userBan == null || $userBan->get("ban_expire") <= time()) {
            $this->getApp()->getSession()->set("habbo_username", $user->get("username"));
            $sessionKey = $this->getApp()->getSession()->generateSessionKey();
            $this->getApp()->getSession()->set("session_key", (new RawText($sessionKey))->getHash()->getHash());

            $user->set("session_key", $sessionKey);
            $user->set("last_login", time());
            $user->set("ip_current", $_SERVER["REMOTE_ADDR"]);

            $this->redirect("me");
        }

        $this->redirect("index/login/error/" . base64_encode(serialize(json_encode((object)["message" => 'Du wurdest bis zum ' . date('d.m.Y - H:i:s', $userBan->get("ban_expire")) . ' gebannt! Grund: ' . $userBan->get("ban_reason")]))));

    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {

        if (!$this->minRank(-1)) {
            $this->redirect("me");
        }

        if ($route->getHandler() == 'steam-login') {
            $this->steamLogin();

            return;
        }

        $this->includeHeader(false);
        $this->includeFooter(false);

        $this->addCSSFile("index/Index");

        $template = $this->getView()->createTemplate("index/Index.tpl.php");

        $username = "";

        if ($route->getHandler() == 'login') {
            $username = $request->getPost("username");
            $password = $request->getPost("password");

            $message = (object)[
                'type' => 'error',
                'text' => 'Du hast nicht alle Felder ausgefüllt!'
            ];

            if ($username != null && $password != null) {
                $userFactory = $this->getFactoryManager()->get(UserFactory::class);

                /**
                 * @var $user User|null
                 */
                $user = filter_var($username, FILTER_VALIDATE_EMAIL) ?
                    $userFactory->getByColumn("mail", $username) :
                    $userFactory->getByColumn("username", $username);


                if ($user != null) {
                    $userPassword = new Hash($user->get("password"));
                    $inputPassword = new RawText($password);
                    if ($userPassword->equals($inputPassword) && $inputPassword->equals($userPassword)) {
                        $this->loginByUser($user);
                    } else {
                        $message->text = 'Das angegebene Passwort ist falsch.';
                    }
                } else {
                    $message->text = 'Es wurde kein User mit diesem/dieser Usernamen/E-Mail gefunden.';
                }
            }

            $template->message = $message;
        }

        if ($route->getHandler() == 'not-connected') {
            $template->message = (object)[
                'type' => 'error',
                'text' => 'Dieser Account ist nicht verbunden.'
            ];
        }

        if ($route->getHandler() == 'error') {
            $info = json_decode(unserialize(base64_decode($vars['info'])));
            $template->message = (object)[
                'type' => 'error',
                'text' => $info->message
            ];
        }

        $template->username = $username;

        $template->title = "Neue und alte Freunde finden - nur im " . $this->getApp()
                ->getConfig()
                ->get("site", "name") . " Hotel!";

        $this->display($template);
    }
}