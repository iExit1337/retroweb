<?= $navigation ?>
<div class="grid_11">
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>admin/homepage/alerts/add">
                <div style="float: right" class="button green">Hinzuf&uuml;gen</div>
            </a>
            <div class="title">Homepage Alerts</div>
            <br/>
            <div class="label">Aktiv</div>
            <?php foreach ($activeAlerts as $alert) { ?>
                <div class="alert">
                    <div class="text">
                        <i class="fa <?= $alert->getIcon() ?>" aria-hidden="true"></i> <?= $this->filter($alert->get("text")) ?>
                    </div>

                    <div class="change_status">
                        <a href="<?= $config->get("site", "url") ?>admin/homepage/alerts/toggle/<?= $alert->getInt("id") ?>/<?= \System\Security\CSRF::getToken() ?>">
                            <div class="button red" style="width: 100%">Deaktivieren</div>
                        </a>
                    </div>
                    <div style="clear: both"></div>
                </div>
            <?php } ?>
            <div class="label" style="margin-top: 10px">Inaktiv</div>
            <?php foreach ($inactiveAlerts as $alert) { ?>
                <div class="alert">
                    <div class="text">
                        <i class="fa <?= $alert->getIcon() ?>" aria-hidden="true"></i> <?= $this->filter($alert->get("text")) ?>
                    </div>
                    <div class="delete">
                        <a href="<?= $config->get("site", "url") ?>admin/homepage/alerts/delete/<?= $alert->getInt("id") ?>/<?= \System\Security\CSRF::getToken() ?>">
                            <div class="button red" style="width: 100%"><i class="fa fa-trash" aria-hidden="true"></i>
                            </div>
                        </a>
                    </div>
                    <div class="change_status">
                        <a href="<?= $config->get("site", "url") ?>admin/homepage/alerts/toggle/<?= $alert->getInt("id") ?>/<?= \System\Security\CSRF::getToken() ?>">
                            <div class="button green" style="width: 100%">Aktivieren</div>
                        </a>
                    </div>
                    <div style="clear: both"></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
