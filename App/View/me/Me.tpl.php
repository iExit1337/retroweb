<?php if (isset($received_daily_bonus) && $received_daily_bonus) { ?>
    <script>
        swal({
            title: "Login-Boni!",
            html: "Du hast soeben Taler, Pixel und Punkte<br/>für deinen heutigen Besuch erhalten.",
            imageUrl: "<?= $config->get("site", "url") ?>public/images/login_bonus.gif",
            width: 350
        });
    </script>
<?php } ?>
<?= $sliderWidget ?>
<div class="grid_<?= 16 - $sliderWidget->getGrid() ?>">
    <div class="user_box box">
        <div class="check_in">Einchecken &raquo;</div>
        <div class="avatar"
             style="background-image: url(https://www.habbo.nl/habbo-imaging/avatarimage?figure=<?= $myUser->get("look") ?>&size=l&head_direction=4&direction=4)"></div>
    </div>
    <div class="user_purse box">
        <div class="data">
            <div class="icon credits"></div>
            <div class="value"><?= $myUser->getCreditsAsString() ?></div>
        </div>

        <div class="data">
            <div class="icon pixels"></div>
            <div class="value"><?= $myUser->getPixelsAsString() ?></div>
        </div>

        <div class="data">
            <div class="icon points"></div>
            <div class="value"><?= $myUser->getPointsAsString() ?></div>
        </div>
    </div>
</div>
<script>
	$(".check_in").click(function() {
		window.open(
			'<?=$config->get("site", "url")?>client',
			'<?=$config->get("site", "name")?>: Client',
			"width=" + ( $(document).width() - 200 ) + ", height=" + ( $(document).height() - 200 )
		);
	});
</script>
<?= $listWidget ?>
<?= $campaignWidget ?>
