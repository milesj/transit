<?php

require_once 'include.php';

if ($_FILES) {
	$upload = new \mjohnson\transit\handlers\UploadHandler($_FILES['file']);
	$upload->setDirectory(__DIR__ . '/tmp/');

	if ($file = $upload->upload()) {
		debug($file->toArray());
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Upload</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit">Upload</button>
	</form>
</body>