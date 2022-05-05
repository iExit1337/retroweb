<?= $navigation ?>
<div class="grid_11">
    <?php if (isset($error)) { ?>
        <div class="msg error"><?= $error ?></div>
    <?php } ?>
    <?php if (isset($success)) { ?>
        <div class="msg success"><?= $success ?></div>
    <?php } ?>
    <div class="box">
        <div class="innerbox">
            <div style="float: right">
                <a href="<?= $config->get("site", "url") ?>admin/homepage/news/delete/<?= $id ?>/<?= \System\Security\CSRF::getToken() ?>">
                    <div style="margin-right: 10px;" class="button red">L&ouml;schen</div>
                </a>
                <a href="<?= $config->get("site", "url") ?>admin/homepage/news">
                    <div class="button blue">Zur&uuml;ck</div>
                </a>
            </div>
            <div class="title">News bearbeiten (id: #<?=$id?>)</div>
            <br/>
            <form method="post">
                <div class="label">Titel</div>
                <input type="text" value="<?= $title ?>" name="title" class="input">
                <div class="desc">Um was geht es?</div>
                <br/>
                <div class="label">Teaser</div>
                <input type="text" value="<?= $teaser ?>" name="teaser" class="input">
                <div class="desc">Teaser die News in wenigen S&auml;tzen an!</div>
                <br/>
                <div class="label">Bild-Link</div>
                <input type="text" value="<?= empty($image) ? '%path%public/images/news_images/' : $image ?>"
                       name="image" class="input">
                <div class="desc">Teaser die News in wenigen S&auml;tzen an!</div>
                <br/>
                <div class="label">Text</div>
                <textarea id="newstext" name="text"><?= $text ?></textarea>
                <div class="desc">Der Inhalt deines Artikels. Du kannst Variablen wie %username% nutzen!</div>
                <br/>
                <div style="width: 50%;float:left">
                    <div class="label">Kommentare erlauben</div>
                    <div id="radioAC">
                        <input type="radio" value="1" id="radioAC1"
                               name="allow_comments"<?php if ($allow_comments) { ?> checked<?php } ?>><label
                                for="radioAC1">Ja</label>
                        <input type="radio" value="0" id="radioAC3"
                               name="allow_comments"<?php if (!$allow_comments) { ?> checked<?php } ?>><label
                                for="radioAC3">Nein</label>
                    </div>
                </div>
                <div style="width: 50%;float:left">
                    <div class="label">Bewertung erlauben</div>
                    <div id="radioRA">
                        <input type="radio" value="1" id="radioRA1"
                               name="allow_voting"<?php if ($allow_voting) { ?> checked<?php } ?>><label
                                for="radioRA1">Ja</label>
                        <input type="radio" value="0" id="radioRA3"
                               name="allow_voting"<?php if (!$allow_voting) { ?> checked<?php } ?>><label
                                for="radioRA3">Nein</label>
                    </div>
                </div>


                <br/><br/> <br/>
                <?= \System\Security\CSRF::getField() ?>
                <div onclick="$('form').submit()" class="button green" style="float: right">Speichern</div>
                <div style="clear: both"></div>
            </form>
        </div>
    </div>
</div>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>
    $(function () {
        $("#radioAC").buttonset();
        $("#radioRA").buttonset();
    });

    tinymce.init({selector: '#newstext'});
</script>
