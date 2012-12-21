<?php

require_once 'include.php';

if ($_FILES) {
	$upload = new \mjohnson\transit\handlers\UploadHandler($_FILES['file']);
	$upload->setDirectory(__DIR__ . '/tmp/');

	if ($file = $upload->upload()) {
		$t1 = new \mjohnson\transit\transformers\CropTransformer($file, array('width' => 100, 'height' => 100));
		$file = $t1->transform(true);

		$t2 = new \mjohnson\transit\transformers\ScaleTransformer($file);
		$file = $t2->transform(true);

		debug($file->toArray());
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Upload + Self Transform</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit">Upload</button>
	</form>
</body>