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
    <div class="grid_3" style="height: 1px"></div>
    <div class="grid_10">
        <div id="logo" style="background-position: center; width: 100%"></div>
        <div class="box" style="position: relative; z-index: 1000;">
            <div class="innerbox">
                <div class="title">Registration: Username</div>
                <br/>
                <form method="post">
                    <div class="label">Username</div>
                    <?php if($errors['username'] != null) { ?>
                        <div class="msg error"><?=$errors['username']?></div>
                    <?php } ?>

                    Dein Username ist dein Nickname. Diesen wirst du im Verlauf des Spieles <b>nicht</b> &auml;ndern k&ouml;nnen. Sollte der Name gegen unsere Richtlinien versto&szlig;en, wird dieses Benutzerkonto gesperrt.<br /><br />
                    <input type="text" name="username" class="input">

                    <br /> <br />

                    <a href="<?= $config->get("site", "url") ?>registration/abort">
                        <div style="float: left; width: 150px;" class="button red">Abbrechen</div>
                    </a>

                    <input type="submit" value="Weiter" class="button green" style="float: right; width: 150px;">

                    <div style="clear: both"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="hotelview"></div>
<div class="hotel"></div>
</body>
</html>