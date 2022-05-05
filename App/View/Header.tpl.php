<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $this->filter($pageTitle) ?></title>

    <script>
        var TOKEN = "<?= \System\Security\CSRF::getToken() ?>";
        var PATH = "<?= $config->get("site", "url") ?>";
    </script>

    <?php foreach ($files['css'] as $file) { ?>
        <link href="<?= $file ?>" rel="stylesheet">
    <?php } ?>

    <?php foreach ($files['js'] as $file) { ?>
        <script src="<?= $file ?>" type="text/javascript"></script>
    <?php } ?>

    <link rel="shortcut icon" href="<?= $config->get("site", "url") ?>public/images/favicon.ico"
          type="image/vnd.microsoft.icon">
</head>
<body>
<header>
    <div class="top-bar">
    </div>
    <div class="container_16" style="margin: 0 auto">
        <div class="grid_7">
            <div id="logo"></div>
        </div>
        <div class="grid_3" style="height: 150px;">

        </div>
        <?php if ($myUser != null) { ?>
            <div class="grid_6">
                <div id="flap">
                    <div class="avatar <?php echo $myUser->get("online") == 0 ? "offline" : "online" ?>"
                         style="background-image: url(https://www.habbo.nl/habbo-imaging/avatarimage?figure=<?= $this->filter($myUser->get("look")) ?>)"></div>
                    <div class="user-information">
                        <div class="username">
                            <?= $this->filter($myUser->get("username")) ?>
                        </div>
                        <div class="rank">
                            <?= $this->filter($myUser->getRankAsString()) ?>
                        </div>
                    </div>
                    <div class="links">
                        <a class="flap-link" href="<?= $config->get("site", "url") ?>messages"><i class="fa fa-envelope"
                                                                                                  aria-hidden="true"></i>
                            <?php if($hasUnreadMessages) {?><div class="received-message"></div><?php } ?>
                        </a>
                        <a class="flap-link" href="<?= $config->get("site", "url") ?>settings"><i class="fa fa-cog"
                                                                                                  aria-hidden="true"></i></a>
                        <a class="flap-link" href="<?= $config->get("site", "url") ?>logout"><i class="fa fa-sign-out"
                                                                                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="hotelview"></div>
    <div class="hotel"></div>
</header>
<nav>
    <div class="container_16" style="margin: 0 auto; height: 50px;">
        <div class="grid_16">
            <ul id="navigation">
                <?php
                foreach ($navigation->getNavigationPoints() as $mainPoint) {
                    ?>
                    <li<?php if ($mainPoint->isActive()) { ?> class="active"<?php } ?>><a
                                href="<?= $mainPoint->getUrl() ?>"><?= $this->filter($mainPoint->getText()) ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="subnavigation">
        <div class="container_16" style="margin: 0 auto">
            <div class="grid_16">
                <ul id="subnav">
                    <?php
                    $activeSubnavigationPoint = $navigation->getActive();
                    if ($activeSubnavigationPoint != null) {
                        foreach ($activeSubnavigationPoint->getNavigationPoints() as $subPoint) {
                            ?>
                            <li<?php if ($subPoint->isActive()) { ?> class="active"<?php } ?>><a
                                        href="<?= $subPoint->getUrl() ?>"><?= $this->filter($subPoint->getText()) ?></a>
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div id="main-area">
<div class="container_16" style="margin: 0 auto;">
    <?php
    if ($alert != null && $myUser != null) {

        ?>

        <div class="grid_16">
            <div class="alert-message">
                <div style="float: left; margin-top: 2px;"><i style="font-size: 20px;padding-right: 10px;"
                                                              class="fa <?= $alert->getIcon() ?>" aria-hidden="true"></i>
                </div><?= $this->filter(str_replace("%username%", $myUser->username, $alert->text)); ?></div>
        </div>
    <?php } ?>
