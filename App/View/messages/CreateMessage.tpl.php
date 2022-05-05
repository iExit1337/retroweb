<div class="grid_4">
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>messages">
                <div class="button red" style="width: 100%">Abbrechen</div>
            </a>
            <div style="clear: both"></div>
        </div>
    </div>
</div>
<div class="grid_12">
    <?php if (isset($error)) { ?>
        <div class="msg error"><?= $error ?></div>
    <?php } ?>
    <div class="box">
        <div class="innerbox">
            <div class="title">Neue Nachricht verfassen</div>
            <br/>
            <form method="post">
                <div class="label">Betreff</div>
                <input value="<?= $this->filter($subject) ?>" class="input" name="subject">
                <div class="desc">Um was geht es?</div>
                <br/>
                <div class="label">Teilnehmer</div>
                <input id="select_receivers" name="receivers">
                <div class="desc">Wer soll alles in der Konversation teilnehmen?</div>
                <br/>
                <div class="label">Nachricht</div>
                <textarea name="message" class="input"
                          style="resize: none;height: 100px"><?= $this->filter($message) ?></textarea>
                <div class="desc">Was soll in der Nachricht stehen?</div>
                <?= \System\Security\CSRF::getField() ?>
                <div class="button green" onClick="$('form').submit()" style="float: right">Senden</div>
                <div style="clear: both"></div>
            </form>
        </div>
    </div>
</div>