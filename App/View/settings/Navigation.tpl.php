<div class="grid_4">
    <div id="settings-navigation">
        <?php
        foreach ($navigation_points as $point) {
            $isActive = $point->isActive();
            ?>
            <div class="navigation-point<?php if($isActive) {?> active<?php } ?>" <?php if(!$isActive) { ?>onclick="window.location.href='<?=$point->getUrl()?>'"<?php }?>>
                <?= $point->getText() ?>
            </div>
        <?php } ?>
    </div>
</div>