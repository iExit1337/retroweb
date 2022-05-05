<?= $navigation ?>
<div class="grid_12">
	<div class="box">
		<div class="innerbox">
			<div class="title">Freunde</div>
			<br />
			<?php if (count($friends) == 0) { ?>
				Du hast noch keine Freunde!
			<?php } else {
				foreach ($friends as $friendEntry) {
					$friend = $friendEntry->getUserOne()->getInt("id") != $myUser->getInt("id") ? $friendEntry->getUserOne() : $friendEntry->getUserTwo(); ?>
					<div class="friend <?= $friend->get("online") == 0 ? "offline" : "online"?>">
						<div class="remove" onclick="window.location.href='<?=$config->get("site", "url")?>settings/friends/remove/<?=$friendEntry->getInt("id")?>/<?=\System\Security\CSRF::getToken()?>'"></div>
						<div class="avatar" style="background-image: url(https://www.habbo.nl/habbo-imaging/avatarimage?figure=<?=$this->filter($friend->get("look"))?>)"></div>
                        <div class="friendname"><a href="<?=$config->get("site", "url")?>home/<?=$this->filter($friend->get("username"))?>"><?=$this->filter($friend->get("username"))?></a></div>
					</div>
				<?php } ?>
			<div style="clear: both"></div>
			<?php } ?>
		</div>
	</div>
</div>
