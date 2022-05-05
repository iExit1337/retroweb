<?= $navigation ?>
<div class="grid_12">
    <?php if (isset($error)) { ?>
        <div class="msg error"><?= $error ?></div>
    <?php } elseif (isset($success)) { ?>
        <div class="msg success">Deine E-Mail Adresse wurde erfolgreich ge&auml;ndert.</div>
    <?php } ?>
    <div class="box">
        <div class="innerbox">
            <div class="title">E-Mail Einstellungen</div>
            <form method="post">
                <br/>
                <div class="label">Deine alte E-Mail Adresse</div>
                <input type="email" class="input" name="old_mail" value="<?= $this->filter($myUser->get("mail")) ?>">
                <div class="desc">Bitte gebe deine alte E-Mail Adresse zur Identifizierung deines Accounts ein, falls
                    keine eingetragen sein sollte.
                </div>
                <div style="margin: 15px -25px; height: 1px; background-color: rgba(0,0,0,0.05)"></div>
                <div class="label">Deine neue E-Mail Adresse</div>
                <input type="email" class="input" name="new_mail">
                <div class="desc">Bitte gebe deine neue E-Mail Adresse ein.</div>
                <br/>
                <div class="label">Neue E-Mail Adresse wiederholen</div>
                <input type="email" class="input" name="new_mail_repeat">
                <div class="desc">Bitte gebe die neue E-Mail Adresse erneut ein, um Schreibfehler zu vermeiden.</div>
                <br/>
                <?= \System\Security\CSRF::getField() ?>
                <div style="float: right" class="button green" onclick="$('form').submit()">Speichern</div>
                <div style="clear: both"></div>
            </form>
        </div>
    </div>
</div>
