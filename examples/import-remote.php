<?php

require_once 'include.php';

if (!empty($_POST['url'])) {
	$import = new \mjohnson\transit\handlers\ImportHandler();
	$import->setDirectory(__DIR__ . '/tmp/');

	if ($file = $import->fromRemote($_POST['url'])) {
		debug($file->toArray());
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Import Remote</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="text" name="url" placeholder="URL">
		<button type="submit">Import</button>
	</form>
</body>