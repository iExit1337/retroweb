<?= $navigation ?>
<div class="grid_12">

	<?php if(isset($error)) { ?>
		<div class="msg error"><?= $error ?></div>
	<?php } ?>
	<?php if(isset($success)) { ?>
		<div class="msg success">Dein Passwort wurde erfolgreich ge&auml;ndert!</div>
	<?php } ?>
	<div class="box">
		<div class="innerbox">
			<div class="title">Passwort Einstellungen</div>
			<br />
			<div class="msg error" style="margin-bottom: 10px;font-weight: bold;">
				Bitte bearbeite niemals dein Passwort, wenn dich ein anderer <?=  $config->get("site", "name") ?> dazu zwingt oder z.B. &bdquo;Talerhacks&rdquo; verspricht, denn solche Maschen funktionieren nicht! Merke dir: Taler sind zu 100% kostenlos!
			</div>
			<br />
			<form method="post">
				<div class="label">Altes Passwort</div>
				<input class="input" type="password" name="password" style="width: 100%">
				<div class="desc">Dies ben&ouml;tigen wir, um sicher zu stellen, dass du der Besitzer des Accounts bist.</div>
				<div style="border-bottom: 1px solid rgba(0,0,0,0.1); margin: 15px -25px"></div>
				<div class="label">Dein neues Passwort</div>
				<input class="input" type="password" name="new_password" style="width: 100%">
				<div class="desc">Dein neues Passwort sollte wenn möglich mehr als 6 Zeichen beinhalten!</div>
				<br />
				<div class="label">Dein neues Passwort wiederholen</div>
				<input class="input" type="password" name="new_password_repeat" style="width: 100%">
				<div class="desc">Um die Richtigkeit des neuen Passworts zu &uuml;berpr&uuml;fen.</div>
				<?= \System\Security\CSRF::getField() ?>
				<div class="button green" style="float: right" onclick="$('form').submit()">Speichern</div>
				<div style="clear: both"></div>
			</form>
		</div>
	</div>
</div>
