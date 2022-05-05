<?= $navigation ?>
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
<div class="grid_12">
	<?php if ($success) { ?>
		<div class="msg success">Einstellungen wurden erfolgreich gespeichert.</div>
	<?php } ?>
	<div class="box">
		<div class="innerbox">
			<div class="title">Profil Einstellungen</div>
			<br />
			<form method="post">
				<div class="label">Nachrichten</div>
				M&ouml;chtest du Nachrichten von Staffs und anderen <?= $config->get("site", "name") ?>s erhalten?
				<div id="btnset_allowmessages" class="buttonset">
					<input type="radio" value="1" id="btnset_allowmessages1"
							name="allow_messages"<?= $allowMessages ? " checked" : "" ?>><label
							for="btnset_allowmessages1">Ja</label>
					<input type="radio" value="0" id="btnset_allowmessages3"
							name="allow_messages"<?= !$allowMessages ? " checked" : "" ?>><label
							for="btnset_allowmessages3">Nein</label>
				</div>
				<br /><br />
				D&uuml;rfen dir nur Freunde eine Nachricht schicken?
				<div id="btnset_allowmessagesfriendsonly" class="buttonset">
					<input type="radio" value="1" id="btnset_allowmessagesfriendsonly1"
							name="allow_messages_friends_only"<?= $allowMessagesFriendsOnly ? " checked" : "" ?>><label
							for="btnset_allowmessagesfriendsonly1">Ja</label>
					<input type="radio" value="0" id="btnset_allowmessagesfriendsonly3"
							name="allow_messages_friends_only"<?= !$allowMessagesFriendsOnly ? " checked" : "" ?>><label
							for="btnset_allowmessagesfriendsonly3">Nein</label>
				</div>
				<br /><br />
				D&uuml;rfen geblockte Nutzer dir eine Nachricht schicken?
				<div id="btnset_allowmessagesblockedusers" class="buttonset">
					<input type="radio" value="1" id="btnset_allowmessagesblockedusers1"
							name="allow_messages_blocked_users"<?= $allowMessagesBlockedUsers ? " checked" : "" ?>><label
							for="btnset_allowmessagesblockedusers1">Ja</label>
					<input type="radio" value="0" id="btnset_allowmessagesblockedusers3"
							name="allow_messages_blocked_users"<?= !$allowMessagesBlockedUsers ? " checked" : "" ?>><label
							for="btnset_allowmessagesblockedusers3">Nein</label>
				</div>
				<br /><br />
				<div class="label">Profilseite</div>
				D&uuml;rfen andere <?= $config->get("site", "name") ?>s deine Profilseite sehen?
				<div id="btnset_home" class="buttonset">
					<input type="radio" value="1" id="btnset_home1"
							name="home_public"<?= $homePublic ? " checked" : "" ?>><label
							for="btnset_home1">Ja</label>
					<input type="radio" value="0" id="btnset_home3"
							name="home_public"<?= !$homePublic ? " checked" : "" ?>><label
							for="btnset_home3">Nein</label>
				</div>
				<br /><br />
				D&uuml;rfen nur deine Freunde deine Seite sehen?
				<div id="btnset_homefriendsonly" class="buttonset">
					<input type="radio" value="1" id="btnset_homefriendsonly1"
							name="home_public_friends_only"<?= $homePublicFriendsOnly ? " checked" : "" ?>><label
							for="btnset_homefriendsonly1">Ja</label>
					<input type="radio" value="0" id="btnset_homefriendsonly3"
							name="home_public_friends_only"<?= !$homePublicFriendsOnly ? " checked" : "" ?>><label
							for="btnset_homefriendsonly3">Nein</label>
				</div>
				<br /><br />
				D&uuml;rfen geblockte Nutzer deine Profilseite sehen?
				<div id="btnset_homeblockedusers" class="buttonset">
					<input type="radio" value="1" id="btnset_homeblockedusers1"
							name="home_public_blocked_users"<?= $homePublicBlockedUsers ? " checked" : "" ?>><label
							for="btnset_homeblockedusers1">Ja</label>
					<input type="radio" value="0" id="btnset_homeblockedusers3"
							name="home_public_blocked_users"<?= !$homePublicBlockedUsers ? " checked" : "" ?>><label
							for="btnset_homeblockedusers3">Nein</label>
				</div>

				<?= \System\Security\CSRF::getField() ?>
				<br /><br />
				<div style="clear: both"></div>
				<div onclick="$('form').submit()" class="button green" style="float: right">Speichern</div>
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
