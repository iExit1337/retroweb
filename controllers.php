<?php

/**
 * @var $controllerCollector \System\App\Controller\ControllerCollector
 */
$cc = $controllerCollector;

$cc->add(\App\Controller\IndexController::class);
$cc->add(\App\Controller\MeController::class);
$cc->add(\App\Controller\LogoutController::class);
$cc->add(\App\Controller\RegistrationController::class);
$cc->add(\App\Controller\CommunityController::class);
$cc->add(\App\Controller\NewsController::class);
$cc->add(\App\Controller\StaffsController::class);
$cc->add(\App\Controller\MessagesController::class);
$cc->add(\App\Controller\SettingsController::class);
$cc->add(\App\Controller\RulesController::class);
$cc->add(\App\Controller\ClientController::class);
$cc->add(\App\Controller\ErrorController::class);

// ACP
# Admin
$cc->add(\App\Controller\ACP\AdminController::class);
# Homepage
$cc->add(\App\Controller\ACP\HomepageController::class);
$cc->add(\App\Controller\ACP\Homepage\NewsController::class);
$cc->add(\App\Controller\ACP\Homepage\AlertsController::class);
$cc->add(\App\Controller\ACP\Homepage\EventsController::class);
$cc->add(\App\Controller\ACP\Homepage\CampaignsController::class);