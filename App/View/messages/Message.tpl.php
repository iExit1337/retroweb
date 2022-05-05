<div class="grid_5">
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>messages">
                <div style="width: 100%" class="button blue">Zur&uuml;ck</div>
            </a>
            <div style="clear: both"></div>
        </div>
    </div>
    <div class="box">
        <div class="innerbox" style="padding-bottom: 0;">
            <div class="title" style="margin-bottom: 25px">Teilnehmer</div>
            <?php $i = 0;
            $count = count($messageTopic->getSubscriberEntries());
            $subscriberEntries = $messageTopic->getSubscriberEntries();
            for ($i = 0; $i < $count; $i++) {
                $subscriberEntry = $subscriberEntries[$i];
                $user = $subscriberEntry->getUser();
                ?>
                <div class="subscriber">
                    <div class="subscriber_avatar"
                         style="background-image: url(https://www.habbo.nl/habbo-imaging/avatarimage?figure=<?= $this->filter($user->get("look")) ?>)"></div>
                    <div class="subscriber_name"><?= $this->filter($user->get("username")) ?></div>
                </div>
                <?php
            } ?>
        </div>
    </div>
</div>
<div class="grid_11">
    <?php if (isset($error)) { ?>
        <div class="msg error"><?= $error ?></div>
    <?php } ?>
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>messages/<?= $messageTopic->getInt("id") ?>/leave/<?= \System\Security\CSRF::getToken() ?>">
                <div style="float: right" class="button red">Verlassen</div>
            </a>
            <div class="title">RE: <?= $this->filter($messageTopic->get("subject")) ?></div>
            <div style="border-bottom: 1px solid rgba(0,0,0,0.05); margin: 25px -25px 0px -25px;"></div>
            <div id="messages">
                <?php foreach ($messageTopic->getMessages() as $message) { ?>

                    <div class="message <?= $message->getUser()->getInt("id") == $myUser->getInt("id") ? 'own' : 'other' ?>">
                        <div class="text">

                            <div class="author"><?= $message->getUser()->getInt("id") == $myUser->getInt("id") ? 'Du hast geschrieben:' : $this->filter($message->getUser()->get("username")) . " schrieb:" ?></div>

                            <?= nl2br($this->filter($message->get("message"))) ?>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                <?php } ?>
            </div>
            <form method="post" style="margin-top: 25px;">
                <input placeholder="Schreibe eine Nachricht" type="text" name="message" class="input">
                <?= \System\Security\CSRF::getField() ?>
                <div onclick="$('form').submit()" style="float: right; margin-top: 10px;" class="button green">
                    Absenden
                </div>
                <div style="clear: both"></div>
            </form>
        </div>
    </div>
</div>
<script>
    $("#messages").perfectScrollbar();
    var objDiv = document.getElementById("messages");
    objDiv.scrollTop = objDiv.scrollHeight;
</script>