<style>
	pre {
		font-size: 12px;
		background-color: rgba(0, 0, 0, 0.05);
		border: 1px solid rgba(0,0,0,0.1);
		border-radius: 4px;
		padding: 0 10px;
		display: block;
		white-space: pre-wrap;
		white-space: -moz-pre-wrap;
		white-space: -pre-wrap;
		white-space: -o-pre-wrap;
		word-wrap: break-word;
	}
</style>

<div class="grid_<?= isset($newsSliderWidget) ? 16 - $newsSliderWidget->getGrid() : 16 ?>">
	<div class="box">
		<div class="innerbox">
			<?php if (!$box['image']['hide']) {
				$image = $box['image']; ?>
				<img src="<?= $image["src"]; ?>" style="float: <?= $image["float"] ?>">
			<?php } ?>
			<div class="title"><?= $box['title'] ?></div>
			<br />
			<?= $box["text"] ?>
			<br /><br />
			<?php if (!$box['button']['hide']) {
				$button = $box['button'];
				?>
				<a href="<?= $button["url"] ?>">
					<div style="float: right" class="button <?= $button["color"] ?>"><?= $button["text"] ?></div>
				</a>
			<?php } ?>
			<div style="clear: both"></div>
		</div>
	</div>
</div>
<?= $newsSliderWidget ?? "" ?>