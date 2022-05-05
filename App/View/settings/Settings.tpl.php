<style>
	.ui-button {
		width: 100px;
		font-weight: 300;
		font-family: Ubuntu;
		font-size: 13px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		border: 1px solid rgba(0, 0, 0, 0.2);
	}

	.ui-state-active {
		font-weight: bold !important;
		font-size: 14px;
		font-family: Ubuntu;
	}

	.buttonset {
		float: right;
	}
</style>
<?= $navigation ?>
<div class="grid_12">
	<?php if ($changedMottoSuccessfully && !$changedMottoError) { ?>
		<div class="msg success">Dein Motto wurde erfolgreich gespeichert.</div>
	<?php } else if(is_string($changedMottoError)) { ?>
		<div class="msg error"><?= $changedMottoError ?></div>
	<?php } ?>
	<?php if ($changedParamsSuccessfully) { ?>
		<div class="msg success">Deine Einstellungen wurden erfolgreich gespeichert.</div>
	<?php } ?>
	<div class="box">
		<div class="innerbox">
			<div class="title">Account Einstellungen</div>
			<form method="post">
				<br />
				<div class="label">Motto</div>
				<input class="input" name="motto" type="text" value="<?= $this->filter($motto) ?>">
				<div class="desc">Dein Motto wird im Hotel unter deinem Avatar angezeigt.</div>
				<br />
				<div class="label">Verfolgungs-Einstellungen</div>
				D&uuml;rfen dir andere <?= $config->get("site", "name") ?>s im Hotel folgen?
				<div id="btnset_following" class="buttonset">
					<input type="radio" value="0" id="btnset_following1"
							name="block_following"<?= !$blockFollowing ? " checked" : "" ?>><label
							for="btnset_following1">Ja</label>
					<input type="radio" value="1" id="btnset_following3"
							name="block_following"<?= $blockFollowing ? " checked" : "" ?>><label
							for="btnset_following3">Nein</label>
				</div>
				<br /><br />
				<div class="label">Freundschaftsanfragen</div>
				D&uuml;rfen dir andere <?= $config->get("site", "name") ?>s Freundschaftsanfragen schicken?
				<div id="btnset_requests" class="buttonset">
					<input type="radio" value="0" id="btnset_requests1"
							name="block_friendrequests"<?= !$blockFriendrequests ? " checked" : "" ?>><label
							for="btnset_requests1">Ja</label>
					<input type="radio" value="1" id="btnset_requests3"
							name="block_friendrequests"<?= $blockFriendrequests ? " checked" : "" ?>><label
							for="btnset_requests3">Nein</label>
				</div>
				<br /><br />
				<div class="label">Einladungen</div>
				D&uuml;rfen dir andere <?= $config->get("site", "name") ?>s Einladungen in R&auml;ume schicken?
				<div id="btnset_invites" class="buttonset">
					<input type="radio" value="0" id="btnset_invites1"
							name="block_roominvites"<?= !$blockRoominvites ? " checked" : "" ?>><label
							for="btnset_invites1">Ja</label>
					<input type="radio" value="1" id="btnset_invites3"
							name="block_roominvites"<?= $blockRoominvites ? " checked" : "" ?>><label
							for="btnset_invites3">Nein</label>
				</div>
				<br /><br />
				<div class="label">Tauschen</div>
				D&uuml;rfen andere <?= $config->get("site", "name") ?>s mit dir Handeln?
				<div id="btnset_trade" class="buttonset">
					<input type="radio" value="1" id="btnset_trade1"
							name="can_trade"<?= $canTrade ? " checked" : "" ?>><label
							for="btnset_trade1">Ja</label>
					<input type="radio" value="0" id="btnset_trade3"
							name="can_trade"<?= !$canTrade ? " checked" : "" ?>><label
							for="btnset_trade3">Nein</label>
				</div>
				<br /><br />
				<div class="label">Alerts</div>
				M&ouml;chtest du Alerts im Hotel erhalten?
				<div id="btnset_alerts" class="buttonset">
					<input type="radio" value="0" id="btnset_alerts1"
							name="block_alerts"<?= !$blockAlerts ? " checked" : "" ?>><label
							for="btnset_alerts1">Ja</label>
					<input type="radio" value="1" id="btnset_alerts3"
							name="block_alerts"<?= $blockAlerts ? " checked" : "" ?>><label
							for="btnset_alerts3">Nein</label>
				</div>
				<br /><br />
				<div class="label">Ignoriere Bot-Chats</div>
				M&ouml;chtest du Nachrichten von Bots im Hotel ignorieren?
				<div id="btnset_bots" class="buttonset">
					<input type="radio" value="1" id="btnset_bots1"
							name="ignore_bots"<?= $ignoreBots ? " checked" : "" ?>><label
							for="btnset_bots1">Ja</label>
					<input type="radio" value="0" id="btnset_bots3"
							name="ignore_bots"<?= !$ignoreBots ? " checked" : "" ?>><label
							for="btnset_bots3">Nein</label>
				</div>
				<br /><br />
				<div class="label">Ignoriere Haustier-Chats</div>
				M&ouml;chtest du Nachrichten von Haustieren im Hotel ignorieren?
				<div id="btnset_pets" class="buttonset">
					<input type="radio" value="1" id="btnset_pets1"
							name="ignore_pets"<?= $ignorePets ? " checked" : "" ?>><label
							for="btnset_pets1">Ja</label>
					<input type="radio" value="0" id="btnset_pets3"
							name="ignore_pets"<?= !$ignorePets ? " checked" : "" ?>><label
							for="btnset_pets3">Nein</label>
				</div>
				<div style="clear: both"></div>
				<br />
				<?= \System\Security\CSRF::getField() ?>
				<div style="float: right" class="button green" onclick="$('form').submit()">Speichern</div>
				<div style="clear: both"></div>
			</form>
		</div>
	</div>
</div>
<script>
	$(function () {
		$(".buttonset").each(function () {
			$(this).buttonset();
		});
	});
</script>
