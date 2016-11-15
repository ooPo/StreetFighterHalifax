

//
// include.js.php
// by Naomi Peori (naomi@peori.ca)
//

google.charts.load('current', { packages: ['corechart', 'table'], callback: draw });

function draw()
{
  //
  // Ranking Data
  //

  var rankingData = new google.visualization.DataTable();

  if ( document.getElementById('rankingsTable') || document.getElementById('eloPointsChart') || document.getElementById('eloChangeChart') )
  {
    rankingData.addColumn('string', 'Name');
    rankingData.addColumn('number', 'Wins');
    rankingData.addColumn('number', 'Losses');
    rankingData.addColumn('number', 'Total');
    rankingData.addColumn('number', 'Win Percentage');
    rankingData.addColumn('number', 'ELO Points');
    rankingData.addColumn('number', 'ELO Change');
    rankingData.addColumn({ type: 'string', role: 'style' });
    rankingData.addColumn({ type: 'string', role: 'style' });

<?php $ranks = GetRanks ( $season, $event ); ?>
<?php $eloRange = GetRange ( $ranks, "elo", 1200 ); ?>
<?php $eloChangeRange = GetRange ( $ranks, "eloChange", 0 ); ?>
<?php $style = "opacity: 0.8; stroke-color: #000000; stroke-width: 0.5;"; ?>
<?php foreach ( $ranks as $rank ) { extract ( $rank ); if ( $total >= $matchesPlayed ) { ?>    rankingData.addRow([ '<?= $name ?>', <?= $wins ?>, <?= $losses ?>, <?= $total ?>, <?= $percentage ?>, <?= $elo ?>, <?= $eloChange ?>, "fill-color: <?= GetColour ( $elo, 1200, $eloRange ) ?>; <?= $style ?>", "fill-color: <?= GetColour ( $eloChange, 0, $eloChangeRange ) ?>; <?= $style ?>" ]); <?php print "\n"; } } ?>

    var formatter = new google.visualization.NumberFormat({ fractionDigits: 2, suffix: '%' });
    formatter.format(rankingData, 4);

    var formatter = new google.visualization.NumberFormat({ fractionDigits: 2, groupingSymbol: '' });
    formatter.format(rankingData, 5);
    formatter.format(rankingData, 6);
  }

  //
  // Match Data
  //

  var matchData = new google.visualization.DataTable();

  if ( document.getElementById('matchLogTable') )
  {
    matchData.addColumn('string', 'Winner');
    matchData.addColumn('number', 'ELO Points');
    matchData.addColumn('string', 'Loser');
    matchData.addColumn('number', 'ELO Points');
    matchData.addColumn('number', 'ELO Change');

<?php $matches = GetRows ( $season, $event ); SortRows ( $matches, "winner" ); ?>
<?php foreach ( $matches as $match ) { extract ( $match ); ?>    matchData.addRow([ '<?= $winner ?>', <?= $winnerElo ?>, '<?= $loser ?>', <?= $loserElo ?>, <?= $eloChange ?> ]); <?php print "\n"; } ?>

    var formatter = new google.visualization.NumberFormat({ fractionDigits: 2 });
    formatter.format( matchData, 1 );
    formatter.format( matchData, 3 );
    formatter.format( matchData, 4 );
  }

  //
  // Attendance Data
  //

  var attendanceData = new google.visualization.DataTable();

  if ( document.getElementById('attendanceChart') || document.getElementById('averageMatchesPlayedChart' ) )
  {
    attendanceData.addColumn('string', 'Season');
    attendanceData.addColumn('string', 'Event');
    attendanceData.addColumn('number', 'Total Players');
    attendanceData.addColumn('number', 'Matches Played');
    attendanceData.addColumn('number', 'Average Matches Played');

<?php $attendances = GetAttendances ( $season ); ?>
<?php foreach ( $attendances as $attendance ) { extract ( $attendance ); ?>    attendanceData.addRow([ '<?= $season ?>', '<?= $event ?>', <?= $players ?>, <?= $matches ?>, <?= $average ?> ]); <?php print "\n"; } ?>

    var formatter = new google.visualization.NumberFormat({ fractionDigits: 2 });
    formatter.format( attendanceData, 4 );
  }

  //
  // Tables and Charts
  //

  if ( document.getElementById('rankingsTable') )
  {
    var view = new google.visualization.DataView(rankingData);
    var table = new google.visualization.Table(document.getElementById('rankingsTable'));
    var tableOptions = { showRowNumber: true, sortAscending: false, sortColumn: 5 };
    view.setColumns([ 0, 1, 2, 3, 4, 5, 6 ]);
    table.draw(view, tableOptions);
  }

  if ( document.getElementById('eloPointsChart') )
  {
    rankingData.sort([{ column: 5, desc: true }]);
    var view = new google.visualization.DataView(rankingData);
    var chart = new google.visualization.BarChart(document.getElementById('eloPointsChart'));
    var chartOptions = { chartArea: { height: '90%', left: 140, top: 20, width: '75%' }, legend: { position: 'none' }, hAxis: { gridlines: { count: 10 }, minValue: 800 }, height: 400 };
    view.setColumns([ 0, 5, 7 ]);
    chart.draw(view, chartOptions);
  }

  if ( document.getElementById('eloChangeChart') )
  {
    rankingData.sort([{ column: 6, desc: true }]);
    var view = new google.visualization.DataView(rankingData);
    var chart = new google.visualization.BarChart(document.getElementById('eloChangeChart'));
    var chartOptions = { chartArea: { height: '87%', left: 140, top: 20, width: '75%' }, legend: { position: 'none' }, hAxis: { gridlines: { count: 10 } }, height: 400 };
    view.setColumns([ 0, 6, 8 ]);
    chart.draw(view, chartOptions);
  }

  if ( document.getElementById('matchLogTable') )
  {
    matchData.sort([{ column: 1 }]);
    matchData.sort([{ column: 0 }]);
    var table = new google.visualization.Table(document.getElementById('matchLogTable'));
    var tableOptions = { height: '100%', showRowNumber: true, sortColumn: 0, width: '650px'};
    table.draw(matchData, tableOptions);
  }

  if ( document.getElementById('attendanceChart') )
  {
    var view = new google.visualization.DataView(attendanceData);
    var chart = new google.visualization.ColumnChart(document.getElementById('attendanceChart'));
    var chartOptions = { chartArea: { height: '80%', left: 50, width: '90%' }, hAxis: { textPosition: 'none' }, legend: { position: 'none' }, vAxis: { minValue: 0 } };
    view.setColumns([ 1, 2 ]);
    chart.draw(view, chartOptions);
  }

  if ( document.getElementById('averageMatchesPlayedChart') )
  {
    var view = new google.visualization.DataView(attendanceData);
    var chart = new google.visualization.ColumnChart(document.getElementById('averageMatchesPlayedChart'));
    var chartOptions = { chartArea: { height: '80%', left: 50, width: '90%' }, hAxis: { textPosition: 'none' }, legend: { position: 'none' }, vAxis: { minValue: 0 } };
    view.setColumns([ 1, 4 ]);
    chart.draw(view, chartOptions);
  }
}
