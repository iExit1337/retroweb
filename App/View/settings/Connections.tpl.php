<?= $navigation ?>
<div class="grid_12">
    <div class="box">
        <div class="innerbox">
            <div class="title">Verkn&uuml;pfungen</div>
            <div class="desc">Im <?= $config->get("site", "name") ?> Hotel hast du die M&ouml;glichkeit, deinen User mit
                anderen Accounts zu verkn&uuml;pfen,<br/>um dich mit ihnen im <?= $config->get("site", "name") ?> Hotel
                einzuloggen.
            </div>
            <br/>
            <?php if ($steamConnection == null) { ?>
            <a style="float: right" href="<?= $config->get("site", "url") ?>settings/connections/connect/steam"><img
                        src="<?= $config->get("site", "url") ?>public/images/connections/steam_login.png"></a>
            <?php } else { ?>
			<a href="<?= $config->get("site", "url") ?>settings/connections/remove/<?=$steamConnection->getInt("id")?>/<?=\System\Security\CSRF::getToken()?>"><div style="float: right" class="button red"><i class="fa fa-trash" aria-hidden="true"></i></div></a>
			<?php } ?>
			<div class="label">Steam</div>
            <?= $steamConnection == null ? "Dein Account ist noch nicht verbunden!" : "Dein Account ist seit dem <b>" . date('d.m.Y', $steamConnection->getAPIData()->connected_since) . "</b> mit <b>".$this->filter($steamConnection->getAPIData()->data->personaname)."</b> verbunden." ?>
            <br/>
            <?php if ($steamConnection == null) { ?>
                <div class="desc">Klicke auf den Button rechts, um dies zu &auml;ndern.</div>
            <?php } ?>
        </div>
    </div>
</div>