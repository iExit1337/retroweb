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
                <div class="title">Registration: E-Mail & Passwort</div>
                <br/>
                <form method="post">
                    <div class="label">E-Mail Adresse</div>
                    <?php if($errors['mail'] != null) { ?>
                        <div class="msg error"><?=$errors['mail']?></div>
                    <?php } ?>
                    Mit deiner E-Mail Adresse kannst du dich einloggen und, falls du dein Passwort vergessen solltest,
                    dieses zur&uuml;cksetzen lassen.
                    <input value="<?= $this->filter($mail) ?>" type="email" name="email" class="input">
                    <div style="margin: 25px 0;" class="break"></div>
                    <div class="label">Passwort</div>
                    Dies ist der Sicherheitsschl&uuml;ssel f&uuml;r dein Benutzerkonto.<br/>Du wirst es brauchen um dich
                    einzuloggen.
                    <?php if($errors['password'] != null) { ?>
                    <div class="msg error"><?=$errors['password']?></div>
                    <?php } ?>
                    <input type="password" name="password" class="input">
                    <br/><br/>
                    <input type="password" name="password_repeat" class="input">
                    <div class="desc">Du musst es zweimal eingeben, um Rechtschreibfehler zu vermeiden.</div>
                    <br/>

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