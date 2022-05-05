<style>
	body {
		margin: 0 !important;
		padding: 0 !important;
		overflow-x: hidden;
	}
</style>
<!DOCTYPE html>
<html>
<head>

	<title><?= $config->get("site", "name") ?>: Client</title>
	<link rel="shortcut icon" href="<?= $config->get("site", "url"); ?>public/images/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="stylesheet" href="<?= $config->get("site", "url"); ?>public/css/client/client.css" type="text/css">
	<script type="text/javascript" src="<?= $config->get("site", "url"); ?>public/js/client/habboflashclient_c.js"></script>
	<script src="<?= $config->get("site", "url"); ?>public/js/jquery-3.2.1.min.js"></script>
	<script src="<?= $config->get("site", "url"); ?>public/js/jquery-ui.min.js"></script>
	<script src="<?= $config->get("site", "url"); ?>public/js/client/flashdetect.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<script type="text/javascript">
		if (!FlashDetect.installed) {
			alert("<?=$config->get("client", "msg_flash_not_installed")?>");
		}
		var BaseUrl = "<?=$config->get("client", "base_url")?>";
		var flashvars = {
			"client.starting": "<?=str_replace("%username%", $this->filter($myUser->get("username")), $config->get("client", "client_starting"))?>",
			"client.starting.revolving": "<?=str_replace("%username%", $this->filter($myUser->get("username")), $config->get("client", "client_starting_resolving"))?>",
			"client.allow.cross.domain": "<?=$config->get("client", "client_allow_cross_domain")?>",
			"client.notify.cross.domain": "<?=$config->get("client", "client_notify_cross_domain")?>",
			"connection.info.host": "<?=$config->get("client", "connection_info_host")?>",
			"connection.info.port": "<?=$config->get("client", "connection_info_port")?>",
			"site.url": "<?=$config->get("site", "url")?>",
			"url.prefix": "<?=$config->get("client", "url_prefix")?>",
			"client.reload.url": "<?=$config->get("site", "url")?>client",
			"client.fatal.error.url": "<?=$config->get("site", "url")?>client",
			"client.connection.failed.url": "<?=$config->get("site", "url")?>client",
			"external.variables.txt": "<?=$config->get("client", "external_variables")?>",
			"external.texts.txt": "<?=$config->get("client", "external_texts")?>",
			"external.figurepartlist.txt": "<?=$config->get("client", "figurepartlist")?>",
			"external.override.texts.txt": "<?=$config->get("client", "override_texts")?>",
			"external.override.variables.txt": "<?=$config->get("client", "override_variables")?>",
			"productdata.load.url": "<?=$config->get("client", "productdata")?>",
			"furnidata.load.url": "<?=$config->get("client", "furnidata")?>",
			"use.sso.ticket": "1",
			"sso.ticket": "<?=$myUser->getSSOTicket()?>",
			"processlog.enabled": "0",
			"flash.client.url": "<?=$config->get("client", "flash_client_url")?>",
			"flash.client.origin": "popup",
			"nux.lobbies.enabled": "true"
		};
		var params = {
			"base": BaseUrl + "/",
			"allowScriptAccess": "always",
			"menu": "false"
		};
		swfobject.embedSWF(BaseUrl + "<?=$config->get("client", "swf_name")?>", "client", "100%", "100%", "10.0.0", "<?=$config->get("client", "express_install")?>", flashvars, params, null);
	</script>
</head>
<body>
<div id="client"></div>
</body>
</html>