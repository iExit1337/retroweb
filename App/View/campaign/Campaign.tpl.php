<div class="grid_<?= $grid ?>">
    <div class="box">
        <div class="innerbox">
            <div class="small_title">Neuigkeiten zum <?= $config->get("site", "name"); ?> Hotels</div>
            <div class="desc">Was ist derzeit im Hotel angesagt?</div>
        </div>

        <?php
        if (count($campaigns) > 0) {
            for ($i = 0; $i < count($campaigns); $i++) {
                $campaign = $campaigns[$i];
                ?>
                <a<?= $campaign->url != null ? ' href="' . $campaign->url . '"' : '' ?>>
                    <div class="campaignArea">
                        <div class="campaignImage"
                             style="background:url('<?= $this->filter($campaign->image); ?>');"></div>
                        <div class="campaignInfo">
                            <div class="campaignTitle"><?= $this->filter($campaign->title); ?></div>
                            <div class="campaignDesc"><?= $this->filter($campaign->desc); ?></div>
                        </div>
                    </div>
                </a>

            <?php }
        } ?>
    </div>
</div>