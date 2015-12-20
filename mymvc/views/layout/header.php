<?php header('Content-Type: text/html; charset=utf-8'); ?>
<DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="<?= __SITE_LINK ?>res/styles.css">
    <title><?= $title ?></title>
  </head>
  <body>
    <div id="wrapper">
        <header>
            <a href='<?= route('','') ?>'><p>Home</p></a>
            <a href='<?= route('movies/') ?>'><p>Movies</p></a>
            <a href='<?= route('txtloader/') ?>'><p>Import movies</p></a>
        </header>