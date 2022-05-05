</div>
</div>
<footer>
	<div class="container_16" style="margin: 0 auto;">
		<div class="grid_5" style="line-height: 100%; padding-top: 13px">
			&copy; <?= date('Y') ?> <?= $config->get("site", "name") ?> Hotel - <?= $config->get("site", "rawUrl") ?><br />
			<span style="color: rgba(256, 256, 256, 0.4); font-size: 12px">powered by RetroWeb</span>
		</div>
		<div class="grid_11" id="footer-links">
			<a href="<?=$config->get("site", "url")?>articles">News</a> -
			<?php if($myUser != null) { ?>
			<a href="<?=$config->get("site", "url")?>settings">Einstellungen</a> -
			<?php } ?>
			<a href="<?=$config->get("site", "url")?>community/rules">Benimmregeln</a> -
			<a href="#">Impressum</a> -
			<a href="#">Datenschutz</a> -
			<?php if($myUser != null) { ?>
			<a href="<?=$config->get("site", "url")?>logout">Ausloggen</a>
			<?php } else { ?>
				<a href="<?=$config->get("site", "url")?>">Einloggen</a> -
				<a href="<?=$config->get("site", "url")?>registration">Registrieren</a>
			<?php } ?>
		</div>
	</div>
</footer>
</body>
</html>