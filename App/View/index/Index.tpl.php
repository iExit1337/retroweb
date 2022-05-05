<!DOCTYPE html>
<html>
<head>
    <title><?= $config->get("site", "name") ?>: <?= $title ?></title>

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
<div class="container_16" style="margin: 0 auto; padding-top: 100px;">
    <div class="grid_4" style="height: 1px"></div>
    <div class="grid_8">
        <div id="logo" style="background-position: center; width: 100%"></div>
        <?php if (isset($message)) { ?>
            <div class="msg <?= $message->type ?>"><?= $message->text ?></div>
        <?php } ?>
        <div class="box" style="position: relative; z-index: 1000; ">
            <div class="innerbox">
                <div class="title">Anmelden</div>
                <div class="desc">Und der Community beitreten!</div>
                <br/>
                <form method="post" action="<?= $config->get("site", "url") ?>index/login">
                    <div class="label">E-Mail Adresse oder Benutzername</div>
                    <input value="<?= $username ?>" type="text" name="username" class="input">
                    <br/><br/>
                    <div class="label">Passwort</div>
                    <input type="password" name="password" class="input">
                    <br/><br/>
                    <input type="submit" class="button blue" value="Anmelden">
                </form>
                <div style="margin: 25px 0;" class="break"></div>
                <a href="<?=$config->get("site", "url")?>login/steam" style="text-align:center;display:block;">
                   	<img src="<?=$config->get("site", "url")?>public/images/icons/steam.png?>">
                </a>
                <div style="margin: 25px 0;" class="break"></div>
                <a href="<?= $config->get("site", "url") ?>registration">
                    <div class="button green">Jetzt kostenlos registrieren</div>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="hotelview"></div>
<div class="hotel"></div>
</body>
</html>