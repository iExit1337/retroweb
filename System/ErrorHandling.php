<?php

set_error_handler(function (int $number, string $message, string $file, int $line, array $context) use ($pdo, $uri, $config) {

	$query = $pdo->prepare('INSERT INTO `cms_errors` SET `timestamp` = :time, `file` = :file, `line` = :line, `message` = :message, `type` = :type, `url` = :url, `info` = :info');
	$query->execute([
		':time' => time(),
		':file' => $file,
		':line' => $line,
		':message' => $message,
		':type' => \System\ErrorTypes::ERROR,
		':info' => json_encode($context),
		':url' => $uri
	]);

	if ($config->getInt("site", "production")) {
		if ($uri != "/error/500") {
			header("Location: " . $config->get("site", "url") . "error/500");

			return;
		}
	} else {
		header("Location: " . $config->get("site", "url") . "error/id/" . $pdo->lastInsertId());

		return;
	}
});

set_exception_handler(function ($e) use ($pdo, $uri, $config): void {

	$query = $pdo->prepare('INSERT INTO `cms_errors` SET `timestamp` = :time, `file` = :file, `line` = :line, `message` = :message, `type` = :type, `url` = :url, `info` = :info, `instance` = :instance');
	$query->execute([
		':time' => time(),
		':file' => $e->getFile(),
		':line' => $e->getLine(),
		':message' => $e->getMessage(),
		':type' => \System\ErrorTypes::EXCEPTION,
		':info' => $e->getTraceAsString(),
		':url' => $uri,
		':instance' => serialize($e)
	]);

	if ($config->getInt("site", "production")) {
		if ($uri != "/error/500") {
			header("Location: " . $config->get("site", "url") . "error/500");

			return;
		}
	} else {
		header("Location: " . $config->get("site", "url") . "error/id/" . $pdo->lastInsertId());

		return;
	}
});