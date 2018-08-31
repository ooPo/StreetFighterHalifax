<?php require ( 'include.php' ); $season = $argument; $event = ""; ?>
<?php $matchesPlayed = count ( GetUniqueValues ( "event", $season, $event ) ) * 2; if ( $matchesPlayed > 15 ) { $matchesPlayed = 15; } ?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
<html>
  <head>
    <title><?= $title ?> | <?= $season ?></title>
    <link type='text/css' rel='stylesheet' href='style.css'>
    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    <script type='text/javascript'><?php require ( 'include.js.php' ); ?>  </script>
  </head>
  <body>
    <h1><a href='index.php'><?= $title ?></a></h1>
    <h2><?= $subtitle ?><br>Season Rankings | <?= $season ?></h2>
    <h3>Rankings<br>(with <?= $matchesPlayed ?> or more matches played)</h3>
    <div class='rankings'><div id='rankingsTable'></div></div>
    <h3>ELO Points</h3>
    <div class='eloPoints'><div id='eloPointsChart'></div></div>
    <h3>Attendance</h3>
    <div class='attendance'><div id='attendanceChart'></div></div>
    <h3>Average Matches Played</h3>
    <div class='averageMatchesPlayed'><div id='averageMatchesPlayedChart'></div></div>
  </body>
</html>
