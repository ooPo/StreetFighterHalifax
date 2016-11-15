<?php require ( 'include.php' ); $season = ""; $event = $argument; $matchesPlayed = 0; ?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
<html>
  <head>
    <title>Street Fighter Halifax | <?= $event ?></title>
    <link type='text/css' rel='stylesheet' href='style.css'>
    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    <script type='text/javascript'><?php require ( 'include.js.php' ); ?>  </script>
  </head>
  <body>
    <h1><a href='index.php'>Street Fighter Halifax</a></h1>
    <h2>Monday Night Ranking Battles<br>Event Rankings | <?= $season . " " . $event ?></h2>
    <h3>Rankings</h3>
    <div class='rankings'><div id='rankingsTable'></div></div>
    <h3>ELO Change</h3>
    <div class='eloChange'><div id='eloChangeChart'></div></div>
    <h3>Match Log</h3>
    <div class='matchLog'><div id='matchLogTable'></div></div>
    </table>
  </body>
</html>
