<?php

require_once 'include.php';

if ($_FILES) {
	$upload = new \mjohnson\transit\Uploader($_FILES['file']);
	$upload->setDirectory(__DIR__ . '/tmp/');

	if ($file = $upload->upload()) {
		debug($file);

		$t1 = new \mjohnson\transit\transformers\ResizeTransformer($file, array('width' => 100, 'height' => 100));
		debug($t1->transform());

		$t2 = new \mjohnson\transit\transformers\FlipTransformer($file);
		debug($t2->transform());
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Upload + Transforms</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit">Upload</button>
	</form>
</body>