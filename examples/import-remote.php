<?php

require_once 'include.php';

if (!empty($_POST['url'])) {
    $transit = new Transit\Transit($_POST['url']);
    $transit->setDirectory(__DIR__ . '/tmp/');

    try {
        if ($transit->importFromRemote()) {
            debug($transit->getOriginalFile()->toArray());
        }
    } catch (Exception $e) {
        debug($e->getMessage());
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