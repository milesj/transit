<?php

require_once 'include.php';

if ($_FILES) {
    $transit = new Transit\Transit($_FILES['file']);
    $transit->setDirectory(__DIR__ . '/tmp/');

    try {
        if ($transit->upload()) {
            $file = $transit->getOriginalFile();

            $file->rename(function($name) {
                return md5($name);
            });

            $file->move(__DIR__ . '/img/', false);

            debug($file->toArray());
        }
    } catch (Exception $e) {
        debug($e->getMessage());
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