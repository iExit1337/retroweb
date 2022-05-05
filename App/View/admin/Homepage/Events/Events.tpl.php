<?= $navigation ?>
<div class="grid_11">
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>admin/homepage/events/add">
                <div style="float: right" class="button green">Hinzuf&uuml;gen</div>
            </a>
            <div class="title">Homepage Events</div>
            <br/>
            <div class="label">Aktive Events</div>

            <?php if (count($activeEvents) > 0) {
                for ($i = 0; $i < count($activeEvents); $i++) {
                    $event = $activeEvents[$i];
                    ?>

                    <div class="event" style="margin-top: 25px;">
                        <div class="icon">
                            <i class="fa fa-play fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="info">
                            <div class="event_title"><?= $this->filter($event->title); ?></div>
                            <div class="delete">
                                <a href="<?= $config->get("site", "url") ?>admin/homepage/events/delete/<?= $event->getInt("id"); ?>/<?= \System\Security\CSRF::getToken() ?>">
                                    <div class="button red" style="width: 10%; float:right;"><i class="fa fa-trash"
                                                                                                aria-hidden="true"></i>
                                    </div>
                                </a>
                            </div>
                            <div style="clear: both"></div>
                            <div class="event_desc">
                                Event geht vom <?= date("d.m.Y", $event->start_time); ?>
                                bis zum <?= date("d.m.Y", $event->end_time); ?>
                            </div>
                        </div>

                        <div style="clear: both"></div>
                    </div>
                <?php }
            } ?>
            <br/>
            <br/>
            <div class="label">Kommende Events</div>
            <br/>
            <?php if (count($upcomingEvents) > 0) {
                for ($i = 0; $i < count($upcomingEvents); $i++) {
                    $event = $upcomingEvents[$i];
                    ?>

                    <div class="event">
                        <div class="icon">
                            <i class="fa fa-clock-o event_up fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="info">
                            <div class="event_title"><?= $this->filter($event->title); ?></div>
                            <div class="delete">
                                <a href="<?= $config->get("site", "url") ?>admin/homepage/events/delete/<?= $event->getInt("id"); ?>/<?= \System\Security\CSRF::getToken() ?>">
                                    <div class="button red" style="width: 10%; float:right;"><i class="fa fa-trash"
                                                                                                aria-hidden="true"></i>
                                    </div>
                                </a>
                            </div>
                            <div style="clear: both"></div>
                            <div class="event_desc">
                                Event geht vom <?= date("d.m.Y", $event->start_time); ?>
                                bis zum <?= date("d.m.Y", $event->end_time); ?>
                            </div>
                        </div>

                        <div style="clear: both"></div>
                    </div>
                <?php }
            } ?>
            <!-- kommende events -->
        </div>
    </div>
</div>
