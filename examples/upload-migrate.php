<?php

require_once 'include.php';

if ($_FILES) {
	$upload = new \mjohnson\transit\Uploader($_FILES['file']);
	$upload->setDirectory(__DIR__ . '/tmp/');

	if ($file = $upload->upload()) {
		$file->rename(function($name) {
			return md5($name);
		});

		$file->move(__DIR__ . '/img/', false);

		debug($file);
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Upload + Move + Rename</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit">Upload</button>
	</form>
</body>