<div class="grid_11">
    <?php
    foreach ($ranks as $rank => $staffs) {
        ?>
        <div class="box" style="margin-bottom: 10px">
            <div class="innerbox">
                <div class="title"><?= $rank; ?></div>
                <?php
                foreach ($staffs as $staff) {
                    ?>
                    <div staff-name="<?= $this->filter($staff->get("username")) ?>" staff-id="<?= $staff->getInt("id") ?>"
                         class="staff_container"
                         onclick="window.location.href='<?= $config->get("site", "url") ?>home/<?= $this->filter($staff->get("username")) ?>'">
                        <div class="avatar_circle"
                             style="background-image: url('https://www.habbo.nl/habbo-imaging/avatarimage?figure=<?= $staff->get("look") ?>')"></div>
                        <div class="status <?php if ($staff->get("online") == 0) { ?>offline<?php } else { ?>online<?php } ?>"></div>
                        <div class="username"><?php echo $this->filter($staff->get("username")); ?></div>
                        <div class="working"><?php echo $this->filter($staff->get("working")); ?></div>
                    </div>

                <?php }
                echo '<div style="clear: both"></div>'; ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<div class="grid_5">
    <div class="box">
        <div class="innerbox">
            <div class="title">Die <?= $config->get("site", "name") ?> Staffs</div>
            Auf dieser Seite kannst Du die Staffs des <?= $config->get("site", "name") ?> Hotels sehen!
        </div>
    </div>
    <div class="box" style="margin-top: 10px">
        <div class="innerbox">
            <div class="title" id="staff_story_title">Staff Story</div>
            <span id="staff_story">Gehe mit der Maus &uuml;ber einen Staff, um genauere Informationen &uuml;ber ihn zu erhalten.</span>
        </div>
    </div>
</div>