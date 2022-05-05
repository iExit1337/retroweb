<div class="grid_5">
    <div id="admin-navigation">
        <?php
        foreach ($navigation_points as $point) {
        $isActive = $point->isActive();
            ?>
            <div class="navigation-point<?php if($isActive) {?> active<?php } ?>" <?php if(!$isActive) { ?>onclick="window.location.href='<?=$point->getUrl()?>'"<?php }?>>
                <?= $this->filter($point->getText()) ?>
            </div>
        <?php } ?>
    </div>
</div>