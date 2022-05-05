<?= $navigation ?>
<style>
    .ui-button {
        width: 100px;
        font-weight: 300;
        font-family: Ubuntu;
        font-size: 13px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid rgba(0, 0, 0, 0.2);
    }

    .ui-state-active {
        font-weight: bold !important;
        font-size: 14px;
        font-family: Ubuntu;
    }
</style>
<div class="grid_11">
    <?php if (isset($error)) { ?>
        <div class="msg error"><?= $error ?></div>
    <?php } ?>
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>admin/homepage/alerts">
                <div style="float: right" class="button red">Abbrechen</div>
            </a>
            <div class="title">Homepage Alert hinzuf&uuml;gen</div>
            <br/>
            <form method="post">
                <div class="label">Text</div>
                <input value="<?= $text ?>" type="text" class="input" name="text">
                <div class="desc">Du kannst <b>%username%</b> nutzen, um den Usernamen des eingeloggten Users anzeigen
                    zu lassen.
                </div>
                <br/>
                <div style="width: 50%;float:left">
                    <div class="label">Aktiv</div>
                    <div id="radioAC">
                        <input type="radio" value="1" id="radioAC1"
                               name="active"<?php if ($active) { ?> checked<?php } ?>><label
                                for="radioAC1">Ja</label>
                        <input type="radio" value="0" id="radioAC3"
                               name="active"<?php if (!$active) { ?> checked<?php } ?>><label
                                for="radioAC3">Nein</label>
                    </div>
                </div>
                <div style="width: 50%;float:left">
                    <div class="label">Icon</div>
                    <div id="radioIC">
                        <input type="radio" value="1" id="radioIC1"
                               name="type"<?php if ($type == 1) { ?> checked<?php } ?>><label
                                for="radioIC1"><i class="fa fa-info-circle" aria-hidden="true"></i></label>
                        <input type="radio" value="2" id="radioIC3"
                               name="type"<?php if ($type == 2) { ?> checked<?php } ?>><label
                                for="radioIC3"><i class="fa fa-question-circle" aria-hidden="true"></i></label>
                    </div>
                </div>
                <br/><br/> <br/>
                <?= \System\Security\CSRF::getField() ?>
                <div onclick="$('form').submit()" class="button green" style="float: right">Hinzuf&uuml;gen</div>
                <div style="clear: both"></div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function () {
        $("#radioAC").buttonset();
        $("#radioIC").buttonset();
    });
</script>
