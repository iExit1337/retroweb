<style>

    .inputdate {
        display:block;
        border: 1px solid #dcdcdc;
        padding: 10px;
        width:280px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        height: 40px;
        font-family: Ubuntu;
        font-weight: 100;
        letter-spacing: -0.33px;
        outline: none;
        -webkit-transition: box-shadow 0.3s, border 0.3s;
    }

    .datetime {
        display:block;
        width:50%;
        height:70px;
        margin-bottom:20px;
    }
</style>

<?= $navigation ?>
<div class="grid_11">
    <?php if (isset($error)) { ?>
        <div class="msg error"><?= $error ?></div>
    <?php } ?>
    <?php if(isset($success)) { ?>
        <div class="msg success">Das Event wurde erfolgreich angelegt.</div>
        <script>setTimeout(function() {
                window.location.href = "<?=$config->get("site", "url")?>admin/homepage/events";
            }, 500);</script>
    <?php } ?>
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>admin/homepage/events">
                <div style="float: right" class="button red">Abbrechen</div>
            </a>
            <div class="title">Homepage Events hinzuf&uuml;gen</div>
            <br/>
            <form method="post">
                <?=\System\Security\CSRF::getField()?>
                <div class="label">Eventname</div>
                <input value="<?= $this->filter($eventName) ?>" type="text" class="input" name="event_name">
                <br />
                <br />
                <div class="label">Eventbeschreibung</div>
                <input value="<?= $this->filter($eventDesc) ?>" type="text" class="input" name="event_desc">
                <br /><br />

                <div class="datetime" style="float:left;">
                    <div class="label">Startdatum</div>
                    <input type="date" class="inputdate"  value="<?= $this->filter($startDate) ?>" style="float:left;" name="start_date">
                </div>


                <div class="datetime" style="float:right;">
                    <div class="label">Startzeit</div>
                    <input type="time" class="inputdate" value="<?= $this->filter($startTime) ?>" style="float:left; width:295px;" name="start_time">
                </div>
                <br />
                <br />
                <div class="datetime" style="float:left;">
                    <div class="label">Enddatum</div>
                    <input type="date" class="inputdate" value="<?= $this->filter($endDate) ?>" style="float:left;" name="end_date">
                </div>


                <div class="datetime" style="float:right;">
                    <div class="label">Endzeit</div>
                    <input type="time" class="inputdate" value="<?= $this->filter($endTime) ?>" style="float:left; width:295px;" name="end_time">
                </div>

                <div onclick="$('form').submit()" class="button green" style="float: right">Hinzuf&uuml;gen</div>

                <div style="clear:both;"></div>
            </form>
        </div>
    </div>
</div>
