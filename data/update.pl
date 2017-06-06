#!/usr/bin/perl

##
## update.pl
## by Naomi Peori (naomi@peori.ca)
##

use strict;
use warnings;

use JSON;
use Data::Dumper;

use Chess::Elo qw(:all);

##
## Configuration
##

my $csvFile = "matches.csv";

my $eloDefault = 1200;

my %eloOverride =
(
#  'SFV: Season 2' => { 'OneEyedJack' => 1600 }
);

my %seasons =
(
  'SF4: Season 1' => [ "2014-01-20", "2014-01-27", "2014-02-03", "2014-02-10", "2014-02-24", "2014-03-10", "2014-03-17", "2014-04-14", "2014-05-05", "2014-05-12", "2014-05-19", "2014-05-26" ],
  'SF4: Season 2' => [ "2014-06-23", "2014-07-08", "2014-07-14", "2014-07-28", "2014-08-04", "2014-08-11", "2014-08-18", "2014-08-25", "2014-09-15", "2014-09-22", "2014-09-29", "2014-10-06" ],
  'SF4: Season 3' => [ "2014-10-27", "2014-11-03", "2014-11-17", "2014-11-24", "2015-01-12", "2015-01-19", "2015-01-26", "2015-02-23", "2015-03-09", "2015-03-23", "2015-03-30", "2015-04-13" ],
  'SF4: Season 4' => [ "2015-04-27", "2015-05-04", "2015-05-11", "2015-05-18", "2015-05-25", "2015-06-08", "2015-06-15", "2015-07-13", "2015-07-20", "2015-07-27", "2015-08-03", "2015-08-10" ],
  'SF4: Season 5' => [ "2015-08-24", "2015-08-31", "2015-09-14", "2015-09-28", "2015-10-05", "2015-10-19", "2015-11-09", "2015-11-16", "2016-01-11", "2016-01-18", "2016-02-01" ],
  'SFV: Season 1' => [ "2016-04-18", "2016-05-02", "2016-05-16", "2016-05-23", "2016-05-30", "2016-06-06", "2016-06-27", "2016-07-11", "2016-07-25", "2016-08-08", "2016-08-15", "2016-08-22" ],
  'SFV: Season 2' => [ "2016-10-03", "2016-10-17", "2016-10-24", "2016-11-07", "2016-11-14", "2016-11-21", "2016-11-28", "2017-01-23", "2017-02-06", "2017-02-27", "2017-03-06", "2017-03-13" ],
  'SFV: Season 3' => [ "2017-06-05" ],
);

my %aliases =
(
  'Aedre'           => [ "Atilla", "Attila" ],
  'DeathToScrub'    => [ "DeathbyBass", "Death to Scrub", "DeathToScrubs" ],
  'DecrepitVision'  => [ "DecepitVision" ],
  'Greasy TayTay'   => [ "GreasyTayTay" ],
  'GokuDudeMan'     => [ "Gokududeman" ],
  'Kevontay'        => [ "kevontay" ],
  'IBleedCanadian'  => [ "iBleedCanadian" ],
  'LandShark'       => [ "Landshark" ],
  'MagicMike'       => [ "Mike" ],
  'NovaNewf',       => [ "Novanewf" ], 
  'Oligollis',      => [ "oligollis" ],
  'ooPo',           => [ "oopo" ],
  'PizzaGod',       => [ "Pizza=Man", "Pizzaman", "PizzaMan" ],
  'Realyst',        => [ "realyst" ],
  'RuneFyst',       => [ "Runefyst", "Runic Fist" ],
  'Savage Shrubber' => [ "SavageShrubber" ],
  'Sir Dudlington'  => [ "Lemmywinks" ],
  'SleepyPants',    => [ "SillyPant", "SillyPants", "Sillypants", "SillyPeasant", "Sleepypants" ],
  'TooMuchDog'      => [ "HardcoreWally" ],
  'B. Mison',       => [ "TheBlastProcessedMan" ],
  'Vaquez',         => [ "vakez", "vaquez" ],
  'WanderingGamblr' => [ "Wandering Gamblr", "Wandering Gambler" ],
  'ShadowMolasses'  => [ "Shadow Molasses" ],
  'Gzuskriced'      => [ "Gzus Kriced" ],
  'Ralimin'         => [ "Raliman" ],
);

##
## Parse the data files.
##

if ( ! open OUTFILE, "> $csvFile" )
{
  print "ERROR: Could not open CSV file for output. ($csvFile)\n";
}
else
{
  foreach my $season ( sort keys %seasons )
  {
    my %eloPrevious = ( );
    my %eloCurrent  = ( );

    foreach my $event ( sort @{$seasons{$season}} )
    {
      @eloPrevious{ sort keys %eloPrevious } = @eloCurrent{ sort keys %eloCurrent };

      if ( ! open INFILE, "< $event.json" )
      {
        print "ERROR: Could not open JSON file for input. ($event.json)\n";
      }
      else
      {
        my $fileData = <INFILE>;
        close INFILE;

        my $jsonData = decode_json ( $fileData );

        foreach my $match ( @{$jsonData->{matches}} )
        {
          if ( $match->{winner}{gamertag} )
          {
            my $winner = $match->{winner}{gamertag};
            my $loser  = $winner eq $match->{player2}{gamertag} ? $match->{player1}{gamertag} : $match->{player2}{gamertag};

            foreach my $alias ( keys %aliases )
            {
              if ( $winner ~~ $aliases{$alias} ) { $winner = $alias };
              if ( $loser  ~~ $aliases{$alias} ) { $loser  = $alias };
            }

            if ( ! exists $eloPrevious{$winner} ) { $eloPrevious{$winner} = exists $eloOverride{$season}{$winner} ? $eloOverride{$season}{$winner} : $eloDefault; }
            if ( ! exists $eloPrevious{$loser}  ) { $eloPrevious{$loser}  = exists $eloOverride{$season}{$loser}  ? $eloOverride{$season}{$loser}  : $eloDefault; }

            if ( ! exists $eloCurrent{$loser}  ) { $eloCurrent{$loser}  = $eloPrevious{$loser};  }
            if ( ! exists $eloCurrent{$winner} ) { $eloCurrent{$winner} = $eloPrevious{$winner}; }

            my @results = elo ( $eloPrevious{$winner}, 1, $eloPrevious{$loser} );
            my $eloChange = $results[ 0 ] - $eloPrevious{$winner};

            $eloCurrent{$winner} += $eloChange;
            $eloCurrent{$loser}  -= $eloChange;

            print OUTFILE "$season,$event,$winner,$eloPrevious{$winner},$loser,$eloPrevious{$loser},$eloChange\n";
          }
        }
      }
    }
  }

  close OUTFILE;
}
