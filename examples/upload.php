<?php

require_once 'include.php';

if ($_FILES) {
    $transit = new Transit\Transit($_FILES['file']);
    $transit->setDirectory(__DIR__ . '/tmp/');

    try {
        if ($transit->upload()) {
            debug($transit->getOriginalFile()->toArray());
        }
    } catch (Exception $e) {
        debug($e->getMessage());
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