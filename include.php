<?php

##
## include.php
## by Naomi Peori (naomi@peori.ca)
##

$arguments = explode ( "&", urldecode ( $_SERVER['QUERY_STRING'] ) );
$argument = $arguments[ 0 ];

##
## Configuration
##

$matchesFile = "data/matches.csv";

##
## CSV Functions
##

function GetCSV ( $filename )
{
  $csvRows = array ( );

  if ( $file = fopen ( $filename, "r" ) )
  {
    while ( ! feof ( $file ) )
    {
      if ( $values = fgetcsv ( $file ) )
      {
        array_push ( $csvRows, array_combine ( array ( "season", "event", "winner", "winnerElo", "loser", "loserElo", "eloChange" ), $values ) );
      }
    }

   fclose ( $file );
  }

  return $csvRows;
}

$csvRows = GetCSV ( $matchesFile );

##
## Row Functions
##

function GetRows ( $season = "", $event = "" )
{
  $rows = array ( );

  global $csvRows;

  foreach ( $csvRows as $row )
  {
    if ( $row != "" )
    {
      if ( $season == "" || $row[ "season" ] == $season )
      {
        if ( $event == "" || $row[ "event" ] == $event )
        {
          array_push ( $rows, $row );
        }
      }
    }
  }

  return $rows;
}

function SortRows ( $rows, $sortby, $reverse = "false" )
{
  usort ( $rows, function ( $a, $b ) use ( $sortby ) { return $a[ $sortby ] <=> $b[ $sortby ]; } );
  if ( $reverse ) { rsort ( $rows ); }
}

##
## Value Functions
##

function GetValues ( $fields, $season = "", $event = "" )
{
  $values = array ( );

  foreach ( GetRows ( $season, $event ) as $row )
  {
    if ( is_scalar ( $fields ) )
    {
      array_push ( $values, $row[ $fields ] );
    }
    else
    {
      foreach ( $fields as $field )
      {
        array_push ( $values, $row[ $field ] );
      }
    }
  }

  return $values;
}

function GetUniqueValues ( $fields, $season = "", $event = "" )
{
  return array_unique ( GetValues ( $fields, $season, $event ) );
}

##
## Helper Functions
##

function GetRanks ( $season = "", $event = "" )
{
  $ranks = array ( );

  foreach ( GetUniqueValues ( array ( "winner", "loser" ), $season, $event ) as $player )
  {
    $ranks[ $player ] = array ( 'name' => $player, 'wins' => 0, 'losses' => 0, 'total' => 0, 'percentage' => 0, 'elo' => 0, 'eloChange' => 0 );
  }

  foreach ( GetRows ( $season, $event ) as $row )
  {
    $winner = $row[ "winner" ];
    $loser  = $row[ "loser"  ];

    $ranks[ $winner ][ "wins" ] += 1;

    $ranks[ $loser  ][ "losses" ] += 1;

    $ranks[ $winner ][ "total" ] += 1;
    $ranks[ $loser  ][ "total" ] += 1;

    $ranks[ $winner ][ "percentage" ] = ( $ranks[ $winner ][ "wins" ] / $ranks[ $winner ][ "total" ] ) * 100;
    $ranks[ $loser  ][ "percentage" ] = ( $ranks[ $loser  ][ "wins" ] / $ranks[ $loser  ][ "total" ] ) * 100;

    $ranks[ $winner ][ "eloChange" ] += $row[ "eloChange" ];
    $ranks[ $loser  ][ "eloChange" ] -= $row[ "eloChange" ];

    if ( $season != "" )
    {
      $ranks[ $winner ][ "elo" ] = 1200 + $ranks[ $winner ][ "eloChange" ];
      $ranks[ $loser  ][ "elo" ] = 1200 + $ranks[ $loser  ][ "eloChange" ];
    }
    else if ( $event != "" )
    {
      $ranks[ $winner ][ "elo" ] = $row [ "winnerElo" ] + $ranks[ $winner ][ "eloChange" ];
      $ranks[ $loser  ][ "elo" ] = $row [ "loserElo"  ] + $ranks[ $loser  ][ "eloChange" ];
    }
  }

  return $ranks;
}

function GetAttendances ( $season )
{
  $attendances = array ( );

  $events = GetUniqueValues ( "event", $season );

  foreach ( $events as $event )
  {
    $attendances[ $event ][ "season"  ] = $season;
    $attendances[ $event ][ "event"   ] = $event;
    $attendances[ $event ][ "players" ] = count ( GetUniqueValues ( array ( "winner", "loser" ), $season, $event ) );
    $attendances[ $event ][ "matches" ] = count ( GetRows ( $season, $event ) );
    $attendances[ $event ][ "average" ] = ( $attendances[ $event ][ "matches" ] / $attendances[ $event ][ "players" ] * 2 );
  }

  return $attendances;
}

##
## Utility Functions
##

function GetRange ( $rows, $field, $offset = 0 )
{
  $range = 0;

  foreach ( $rows as $row )
  {
    $value = abs ( $row[ $field ] - $offset );

    if ( $value > $range )
    {
      $range = $value;
    }
  }

  return $range;
}

function GetColour ( $value, $offset, $range )
{
  $value = ( $value - $offset ) / $range;

  if ( $value < -1 ) { $value = -1; }
  if ( $value >  1 ) { $value =  1; }

  $red   = ( $value < 0 ) ? 255 : 255 - (  $value * 255 );
  $green = ( $value > 0 ) ? 255 : 255 - ( -$value * 255 ); 
  $blue  = 0;

  return sprintf ( "#%02X%02X%02X", $red, $green, $blue );
}

?>
