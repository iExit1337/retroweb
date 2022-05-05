<?= $navigation ?>
<div class="grid_11">
    <?php if (isset($error)) { ?>
        <div class="msg error"><?= $error ?></div>
    <?php } ?>
    <?php if (isset($success)) { ?>
        <div class="msg success">Das Event wurde erfolgreich angelegt.</div>
        <script>setTimeout(function () {
                window.location.href = "<?=$config->get("site", "url")?>admin/homepage/campaigns";
            }, 500);</script>
    <?php } ?>
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>admin/homepage/campaigns">
                <div style="float: right" class="button red">Abbrechen</div>
            </a>
            <div class="title">Homepage Events hinzuf&uuml;gen</div>
            <br/>
            <form method="post">
                <?= \System\Security\CSRF::getField() ?>
                <div class="label">Campaign Titel</div>
                <input type="text" value="<?= $this->filter($campaignTitle) ?>" class="input" name="campaign_title">
                <br/><br/>
                <div class="label">Campaign Beschreibung</div>
                <input type="text" value="<?= $this->filter($campaignDesc) ?>" class="input" name="campaign_desc">
                <br/><br/>
                <div class="label">Campaign Weiterleitung</div>
                <input type="text" value="<?= $this->filter($campaignUrl) ?>" class="input" name="campaign_url">
                <div class="desc">Falls keine Vorhanden, frei lassen.</div>
                <br/>
                <div class="label">Campaign Bild</div>
                <input type="text" value="<?= $this->filter($campaignImage) ?>" class="input" name="campaign_image">
                <br/><br/>
                <div onclick="$('form').submit()" class="button green" style="float: right">Hinzuf&uuml;gen</div>

                <div style="clear:both;"></div>
            </form>
        </div>
    </div>
</div>

