<?php

namespace App\Controller;

use App\Model\User\Connections\Connection;
use App\Model\User\Connections\ConnectionsFactory;
use App\Model\User\Connections\ConnectionTypes;
use App\Model\User\Friends\Friendship;
use App\Model\User\Friends\FriendshipFactory;
use App\Model\User\User;
use App\Model\User\UserFactory;
use App\Service\SteamService;
use App\Widget\Settings\NavigationWidget;
use System\App\View\Template;
use System\Helpers\Hash\Hash;
use System\Helpers\Hash\RawText;
use System\Helpers\LightOpenID;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;
use System\Security\CSRF;

class SettingsController extends WebsiteController implements IRoutable
{
    /**
     * @var $_homeTab Point
     */
    private $_homeTab;
    /**
     * @var $_settingsTab Point
     */
    private $_settingsTab;

    public function onRegistration(): void
    {

        if ($this->minRank(1)) {
            $this->_homeTab = $this->getNavigation()->getById("Me");
            $this->_settingsTab = new Point('Me.Settings', 'Einstellungen', $this->getApp()
                    ->getConfig()
                    ->get("site", "url") . "settings", 9997);

            $this->_homeTab->add($this->_settingsTab);
        }
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {

        return [
            new Route([
                RequestType::POST,
                RequestType::GET
            ], '/settings', 'settings-page'),
            new Route([
                RequestType::POST,
                RequestType::GET
            ], '/settings/profile', 'profile-page'),
            new Route(RequestType::GET, '/settings/friends', 'friends-page'),
            new Route(RequestType::GET, '/settings/friends/remove/{id:\d+}/{csrf_token}', 'friend-remove'),
            new Route(RequestType::GET, '/settings/connections', 'connections-page'),
            new Route(RequestType::GET, '/steamregistration', 'connect-steam'),
            new Route(RequestType::GET, '/settings/connections/connect/steam', 'connect-steam-redirect'),
            new Route(RequestType::GET, '/settings/connections/remove/{id:\d+}/{csrf_token}', 'remove-connection'),
            new Route([
                RequestType::GET,
                RequestType::POST
            ], '/settings/mail', 'mail-page'),
            new Route([
                RequestType::GET,
                RequestType::POST
            ], '/settings/password', 'password-page')
        ];
    }

    private function settingsAction(Request $request, NavigationWidget $navigationWidget): void
    {
        $this->setPageTitle("Account - Einstellungen");
        $navigationWidget->setActive("Settings.Account");

        $params = [
            "blockFollowing" => "block_following",
            "blockFriendrequests" => "block_friendrequests",
            "blockRoominvites" => "block_roominvites",
            "canTrade" => "can_trade",
            "blockAlerts" => "block_alerts",
            "ignoreBots" => "ignore_bots",
            "ignorePets" => "ignore_pets",
            "motto" => "motto"
        ];

        $accountSettings = $this->getSession()->getUser()->getAccountSettings();

        $values = [];
        foreach ($params as $param => $key) {
            if ($param != "motto") {
                $values[$param] = $accountSettings->getInt($key);
            } else {
                $values[$param] = $this->getSession()->getUser()->get("motto");
            }
        }

        $changedParamsSuccessfully = false;
        $changedMottoSuccessfully = false;
        $changedMottoError = false;

        if ($request->getMethod() == RequestType::POST && CSRF::isValid($request->getPost("csrf_token"))) {
            foreach ($params as $param => $key) {
                if ($param != "motto" && $values[$param] != $request->getPost($key) && ($request->getPost($key) == 1 || $request->getPost($key) == 0)) {
                    $accountSettings->set($key, (int)$request->getPost($key));

                    $values[$param] = (int)$request->getPost($key);

                    $changedParamsSuccessfully = true;
                } else {
                    if ($param == "motto") {
                        if ($values[$param] != $request->getPost("motto")) {
                            $motto = $request->getPost("motto");
                            if (strlen($motto) >= $this->getApp()
                                    ->getConfig()
                                    ->getInt("motto", "min_length") && strlen($motto) <= $this->getApp()
                                    ->getConfig()
                                    ->getInt("motto", "max_length")
                            ) {
                                $this->getSession()->getUser()->set("motto", $motto);
                                $values[$param] = $motto;
                                $changedMottoSuccessfully = true;
                            } else {
                                $changedMottoError = "Dein Motto muss zwischen " . $this->getApp()
                                        ->getConfig()
                                        ->get("motto", "min_length") . " und " . $this->getApp()
                                        ->getConfig()
                                        ->get("motto", "max_length") . " Zeichen lang sein.";
                            }
                        }
                    }
                }
            }
        }

        $template = $this->getView()->createTemplate("settings/Settings.tpl.php");

        foreach ($values as $param => $value) {
            $template->{$param} = $value;
        }

        $template->navigation = $navigationWidget;
        $template->changedParamsSuccessfully = $changedParamsSuccessfully;
        $template->changedMottoSuccessfully = $changedMottoSuccessfully;
        $template->changedMottoError = $changedMottoError;

        $this->display($template);
    }

    private function profileAction(Request $request, NavigationWidget $navigationWidget): void
    {

        $this->setPageTitle("Profil - Einstellungen");
        $navigationWidget->setActive("Settings.Profile");

        $profileSettings = $this->getSession()->getUser()->getProfileSettings();

        $params = [
            "allowMessages" => "allow_messages",
            "allowMessagesBlockedUsers" => "allow_messages_blocked_users",
            "allowMessagesFriendsOnly" => "allow_messages_friends_only",
            "homePublic" => "home_public",
            "homePublicBlockedUsers" => "home_public_blocked_users",
            "homePublicFriendsOnly" => "home_public_friends_only"
        ];

        $values = [];
        foreach ($params as $param => $key) {
            $values[$param] = $profileSettings->getInt($key);
        }

        $success = false;

        if ($request->getMethod() == RequestType::POST && CSRF::isValid($request->getPost("csrf_token"))) {
            foreach ($params as $param => $key) {
                if ($values[$param] != $request->getPost($key) && ($request->getPost($key) == 1 || $request->getPost($key) == 0)) {
                    $profileSettings->set($key, (int)$request->getPost($key));
                    $values[$param] = (int)$request->getPost($key);
                    $success = true;
                }
            }
        }

        $template = $this->getView()->createTemplate("settings/Profile.tpl.php");

        foreach ($values as $param => $value) {
            $template->{$param} = $value;
        }

        $template->navigation = $navigationWidget;
        $template->success = $success;

        $this->display($template);
    }

    private function friendsAction(Request $request, NavigationWidget $navigationWidget): void
    {

        $sessionUser = $this->getSession()->getUser();

        $this->setPageTitle("Freunde - Einstellungen");
        $navigationWidget->setActive("Settings.Friends");

        $this->addCSSFile("settings/Friends");

        $template = $this->getView()->createTemplate("settings/Friends.tpl.php");
        $template->navigation = $navigationWidget;
        $friends = $sessionUser->getFriendships();

        usort($friends, function (Friendship $a, Friendship $b) use ($sessionUser) {

            $friendA = $a->getUserOne()->getInt("id") != $sessionUser->getInt("id") ? $a->getUserOne() : $a->getUserTwo();
            $friendB = $b->getUserOne()->getInt("id") != $sessionUser->getInt("id") ? $b->getUserOne() : $b->getUserTwo();

            if ($friendA->getInt("online") > $friendB->getInt("online")) {
                return -1;
            } elseif ($friendA->getInt("online") < $friendB->getInt("online")) {
                return 1;
            }

            return 0;

        });

        $template->friends = $friends;

        $this->display($template);
    }

    public function connectionsAction(Request $request, NavigationWidget $navigationWidget): void
    {

        $this->setPageTitle("Verknüpfungen - Einstellungen");
        $navigationWidget->setActive("Settings.Connections");

        $template = $this->getView()->createTemplate("settings/Connections.tpl.php");

        $template->steamConnection = $this->getSession()->getUser()->getSteamConnection();
        $template->navigation = $navigationWidget;

        $this->display($template);
    }

    private function connectSteamAction(Request $request): void
    {

        $config = $this->getApp()->getConfig();

        /**
         * @var $steamService SteamService
         */
        $steamService = $this->getServiceCollector()->get(SteamService::class);
        $steamService->setReturnUrl($config->get("site", "url") . "index.php?" . $config->get("site", "token") . "=/steamregistration");

        $steamService->onInit(function (SteamService $service) {

            $service->getLightOpenID()->identity = 'http://steamcommunity.com/openid/?l=english';
            header('Location: ' . $service->getLightOpenID()->authUrl());
            exit;
        });

        $steamService->onCancel(function (SteamService $service) {

            $this->redirect("settings/connections");
        });

        $steamService->onValid(function (SteamService $service) use ($config) {

            $id = $service->getLightOpenID()->identity;
            $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
            preg_match($ptn, $id, $matches);

            $steamId = $matches[1];

            $url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $config->get("steam", "api_key") . '&steamids=' . $steamId;
            $data = json_decode(file_get_contents($url));

            /**
             * @var $userFactory UserFactory
             */
            $userFactory = $this->getFactoryManager()->get(UserFactory::class);
            /**
             * @var $user User|null
             */
            $user = $userFactory->getByConnection(ConnectionTypes::STEAM, ["steam_id" => $steamId]);

            /**
             * @TODO: Errormeldung
             */
            if ($user == null) {
                $sessionUser = $this->getSession()->getUser();
                $steamConnection = $sessionUser->getSteamConnection();
                $apiData = json_encode((object)[
                    "steam_id" => $steamId,
                    "connected_since" => time(),
                    "data" => $data->response->players[0]
                ]);
                if ($steamConnection != null) {
                    $steamConnection->set("api_data", $apiData);
                } else {
                    $connectionsFactory = $this->getFactoryManager()->get(ConnectionsFactory::class);
                    $connectionsFactory->createObject([
                        "api_data" => $apiData,
                        "user_id" => $sessionUser->getInt("id"),
                        "type" => ConnectionTypes::STEAM
                    ]);
                }
            }

            $this->redirect("settings/connections");
        });

        /**
         * @TODO: Errormeldung
         */
        $steamService->onInvalid(function (SteamService $service): void {

            $this->redirect("settings/connections");
        });

        $steamService->execute();
    }

    private function mailAction(Request $request, NavigationWidget $navigationWidget): void
    {

        $this->setPageTitle("E-Mail - Einstellungen");
        $navigationWidget->setActive("Settings.Mail");
        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("settings/Mail.tpl.php");

        if ($request->getMethod() == RequestType::POST && CSRF::isValid()) {

            $sessionUser = $this->getSession()->getUser();

            $oldMail = $request->getPost("old_mail");
            $newMail = $request->getPost("new_mail");
            $newMailRepeat = $request->getPost("new_mail_repeat");

            if ($sessionUser->get("mail") == $oldMail) {
                if (filter_var($newMail, FILTER_VALIDATE_EMAIL)) {
                    $userFactory = $this->getFactoryManager()->get(UserFactory::class);
                    $userByMail = $userFactory->getByColumn("mail", $newMail);
                    if ($userByMail == null) {
                        if ($newMail == $newMailRepeat) {
                            $sessionUser->set("mail", $newMail);
                            $template->success = true;
                        } else {
                            $template->error = "Die E-Mail Adressen stimmen nicht &uuml;berein.";
                        }
                    } else {
                        $template->error = "Deine neue E-Mail Adresse ist bereits vergeben.";
                    }
                } else {
                    $template->error = "Deine neue E-Mail Adresse ist nicht g&uuml;ltig.";
                }
            } else {
                $template->error = "Deine angebene E-Mail Adresse ist nicht deine derzeitige.";
            }
        }

        $template->navigation = $navigationWidget;
        $this->display($template);
    }

    private function passwordAction(Request $request, NavigationWidget $navigationWidget): void
    {

        $this->setPageTitle("Passwort - Einstellungen");
        $navigationWidget->setActive("Settings.Password");
        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("settings/Password.tpl.php");

        if ($request->getMethod() == RequestType::POST && CSRF::isValid()) {
            $config = $this->getApp()->getConfig();

            $sessionUser = $this->getSession()->getUser();

            $password = $request->getPost("password");
            $newPassword = $request->getPost("new_password");
            $newPasswordRepeat = $request->getPost("new_password_repeat");

            if ((new RawText($password))->equals(new Hash($sessionUser->get("password")))) {
                $passwordMinLength = $config->getInt("registration", "min_password_length");
                $passwordMaxLength = $config->getInt("registration", "max_password_length");

                if (strlen($newPassword) >= $passwordMinLength && strlen($newPassword) <= $passwordMaxLength) {
                    if ($newPassword == $newPasswordRepeat) {
                        $sessionUser->set("password", (new RawText($newPassword))->getHash()->getHash());
                        $template->success = true;
                    } else {
                        $template->error = "Die Passw&ouml;rter stimmen nicht &uuml;berein.";
                    }
                } else {
                    $template->error = "Das neue Passwort muss zwischen " . $passwordMinLength . " und " . $passwordMaxLength . " Zeichen lang sein.";
                }
            } else {
                $template->error = "Dein angegebenes Passwort ist falsch.";
            }
        }

        $template->set("navigation", $navigationWidget);

        $this->display($template);

    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {

        if (!$this->minRank(1)) {
            $this->redirect();
        }

        $this->_homeTab->setActive(true);
        $this->_settingsTab->setActive(true);

        /**
         * @var $navigationWidget NavigationWidget
         */
        $navigationWidget = $this->getWidget(NavigationWidget::class);

        switch ($route->getHandler()) {
            case 'settings-page':
                $this->settingsAction($request, $navigationWidget);
                break;

            case 'mail-page':
                $this->mailAction($request, $navigationWidget);
                break;

            case 'profile-page':
                $this->profileAction($request, $navigationWidget);
                break;

            case 'friends-page':
                $this->friendsAction($request, $navigationWidget);
                break;

            case 'friend-remove':
                if (CSRF::isValid($vars['csrf_token'])) {

                    $sessionUser = $this->getSession()->getUser();

                    /**
                     * @var $friendshipFactory FriendshipFactory
                     */
                    $friendshipFactory = $this->getFactoryManager()->get(FriendshipFactory::class);
                    /**
                     * @var $friendship Friendship
                     */
                    $friendship = $friendshipFactory->getById($vars['id']);
                    if ($friendship != null) {
                        if ($friendship->getUserOne() === $sessionUser || $friendship->getUserTwo() === $sessionUser
                        ) {
                            $friendship->delete();
                        }
                    }
                }

                $this->redirect("settings/friends");
                break;

            case 'connections-page':
                $this->connectionsAction($request, $navigationWidget);
                break;

            case 'connect-steam':
                $this->connectSteamAction($request);
                break;

            case 'connect-steam-redirect':
                $this->redirect("/steamregistration");
                break;

            case 'remove-connection':
                if (CSRF::isValid($vars['csrf_token'])) {
                    /**
                     * @var $connectionsFactory ConnectionsFactory
                     */
                    $connectionsFactory = $this->getFactoryManager()->get(ConnectionsFactory::class);
                    /**
                     * @var $connection Connection|null
                     */
                    $connection = $connectionsFactory->getById($vars["id"]);

                    if ($connection != null && $connection->getInt("user_id") == $this->getSession()
                            ->getUser()
                            ->getInt("id")
                    ) {
                        $connection->delete();
                    }
                }

                $this->redirect("settings/connections");
                break;

            case 'password-page':
                $this->passwordAction($request, $navigationWidget);
                break;
        }

    }
}