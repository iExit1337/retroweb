<div class="grid_<?= $grid ?>">
    <div class="box">
        <div class="innerbox">
            <div class="small_title">Events</div>
            <div class="desc">Hier siehst du alle geplanten Events des <?= $config->get("site", "name") ?> Hotels</div>

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
                            <div class="event_date">Endet am
                                <b><?= $this->filter(date("d.m.Y", $event->end_time)); ?></b> um
                                <b><?= $this->filter(date("H:i", $event->end_time)); ?> Uhr</b></div>
                            <div style="clear: both"></div>
                            <div class="event_desc">
                                <?= $this->filter($event->desc); ?>
                            </div>
                        </div>

                        <div style="clear: both"></div>
                    </div>
                <?php }
            } ?>

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
                            <div class="event_date">Startet am
                                <b><?= $this->filter(date("d.m.Y", $event->start_time)); ?></b> um
                                <b><?= $this->filter(date("H:i", $event->start_time)); ?> Uhr</b></div>
                            <div style="clear: both"></div>
                            <div class="event_desc">
                                <?= $this->filter($event->desc); ?>
                            </div>
                        </div>

                        <div style="clear: both"></div>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>
