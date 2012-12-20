<?php

require_once 'include.php';

if ($_POST) {
	$import = new \mjohnson\transit\Importer();
	$import->setDirectory(__DIR__ . '/tmp/');

	if ($file = $import->fromLocal(__DIR__ . '/img/scott-pilgrim.jpg')) {
		debug($file);
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Import Local</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="import" value="true">
		<button type="submit">Import</button>
	</form>
</body>