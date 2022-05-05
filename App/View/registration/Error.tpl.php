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
                <img src="<?=$config->get("site", "url")?>public/images/frank_lost_connection.png" align="right">
                <div class="title">Registration: Oops!</div>
                <br/>
                <div class="label">Da ist etwas schiefgelaufen!</div>
                <br/>
                Leider ist ein Fehler bei der Erstellung deines Benutzerkontos aufgetreten.<br/>
                Dies kann daran liegen, dass dein Benutzername w&auml;hrend des Prozesses deiner Registration von einem
                anderen Spieler gew&auml;hlt wurde.
                Das gleiche gilt unter anderem auch f&uuml;r deine E-Mail Adresse.
                <br/><br/>Am Besten versuchst du, dich mit deinen vorhin angegebenen Daten mal einzuloggen. Sollte dies
                nicht funktionieren, raten wir dir, dich erneut zu registrieren.

                <div style="clear: both"></div>
                <br/>

                <a href="<?= $config->get("site", "url") ?>registration/abort">
                    <div style="float: left; width: 150px;" class="button red">Zur&uuml;ck</div>
                </a>

                <a href="<?= $config->get("site", "url") ?>registration">
                    <div class="button green" style="float: right; width: 150px;">Registrieren</div>
                </a>

                <div style="clear: both">
                </div>
            </div>
        </div>
        <div class="hotelview"></div>
        <div class="hotel"></div>
</body>
</html>