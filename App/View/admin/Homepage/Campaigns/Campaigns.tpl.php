<?= $navigation ?>
<div class="grid_11">
    <div class="box">
        <div class="innerbox">
            <a href="<?= $config->get("site", "url") ?>admin/homepage/campaigns/add">
                <div style="float: right" class="button green">Hinzuf&uuml;gen</div>
            </a>
            <div class="title">Homepage Campaigns</div>
            <br/>
            <?php
            if (count($campaigns) > 0) {
                for ($i = 0; $i < count($campaigns); $i++) {
                    $campaign = $campaigns[$i];
                    ?>
                    <div class="campaignArea">
                        <div class="campaignImage"
                             style="background:url('<?= $this->filter($campaign->image); ?>');"></div>
                        <div class="delete" style="float:right; margin-right:25px; margin-top:15px; ">
                            <a href="<?= $config->get("site", "url") ?>admin/homepage/campaigns/delete/<?= $campaign->getInt("id") ?>/<?= \System\Security\CSRF::getToken() ?>">
                                <div class="button red" style="width: 100%"><i class="fa fa-trash" aria-hidden="true"></i>
                                </div>
                            </a>
                        </div>
                        <div class="campaignInfo">
                            <div class="campaignTitle"><?= $this->filter($campaign->title); ?></div>
                            <div class="campaignDesc"><?= $this->filter($campaign->desc); ?></div>

                        </div>
                    </div>

                <?php }
            } ?>
        </div>
    </div>
</div>