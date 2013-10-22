<?php

require_once 'include.php';

if ($_POST) {
    $transit = new Transit\Transit(__DIR__ . '/img/scott-pilgrim.jpg');
    $transit->setDirectory(__DIR__ . '/tmp/');

    try {
        if ($transit->importFromLocal()) {
            debug($transit->getOriginalFile()->toArray());
        }
    } catch (Exception $e) {
        debug($e->getMessage());
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