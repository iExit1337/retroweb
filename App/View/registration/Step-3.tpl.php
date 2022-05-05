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
                <div class="title">Registration: Geburtsdatum</div>
                <br/>
                <form method="post">
                    <div class="label">Geburtsdatum</div>
                    <?php if ($errors['birth'] != null) { ?>
                        <div class="msg error"><?= $errors['birth'] ?></div>
                    <?php } ?>

                    Zu guter Letzt brauchen wir noch dein Geburtsdatum um herauszufinden, wie alt du bist. Des Weiteren
                    ben&ouml;tigen wir es f&uuml;r weitere Dinge &#x1F609;.
                    <br/>
                    <div style="width: 100%; position: relative">
                        <select style="width: 20%" name="day" class="input">
                            <option selected value="-1">Tag</option>
                            <?php for ($i = 1; $i <= 31; $i++) { ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php } ?>
                        </select>
                        <select style="width: 58%" name="month" class="input">
                            <option selected value="-1">Monat</option>
                            <option value="1">Januar</option>
                            <option value="2">Februar</option>
                            <option value="3">M&auml;rz</option>
                            <option value="4">April</option>
                            <option value="5">Mai</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Dezember</option>
                        </select>
                        <select style="width: 20%" name="year" class="input">
                            <option selected value="-1">Jahr</option>
                            <?php for ($i = date('Y')-1; $i >= 1950; $i--) { ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <br/> <br/>

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