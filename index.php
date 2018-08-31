<?php require ( 'include.php' ); ?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
<html>
  <head>
    <link type='text/css' rel='stylesheet' href='style.css'>
    <title><?= $title ?> | Rankings</title>
  </head>
  <body>
    <h1><?= $title ?></h1>
    <h2><?= $subtitle ?><br><?= $sponsor ?></h2>
<?php $seasons = GetUniqueValues ( "season" ); rsort ( $seasons ); ?>
<?php foreach ( $seasons as $season ) { ?>
    <h3><?= $season ?></h3>
    <a href='season.php?<?= $season ?>'>Total Season Stats</a><br>
    <br>
<?php   $events = GetUniqueValues ( "event", $season ); rsort ( $events ); ?>
<?php   foreach ( $events as $event ) { ?>
    <a href='event.php?<?= $event ?>'><?= $event ?></a><br>
<?php   } ?>
    <br>
<?php } ?>
  </body>
</html>
