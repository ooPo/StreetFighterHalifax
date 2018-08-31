#!/usr/bin/perl

##
## update-challonge.pl
## by Naomi Peori (naomi@peori.ca)
## by Elias Reid ()
##

use strict;
use warnings;

use JSON;
use Data::Dumper;

use Chess::Elo qw(:all);

##
## Configuration
##

my $csvFile = "challonge.csv";

my $eloDefault = 1200;

my %eloOverride =
(
#  'SFV: Season 2' => { 'OneEyedJack' => 1600 }
);

my %seasons =
(
  'Tekken 7: Season 1' => [ "2018-08-28" ],
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

        # JSON from challonge stores player IDs (generated per event I'm guessing),
        # rather than player names so, need to create a hash to map IDs to names

        my %IDtoName = ();

        foreach my $player ( @{$jsonData->{tournament}{participants}} )
        {
          $IDtoName{$player->{participant}{id}} = $player->{participant}{name};
        }

        foreach my $match ( @{$jsonData->{tournament}{matches}} )
        {
          if ( $match->{match}{winner_id} )
          {

            my $winner = $IDtoName{$match->{match}{winner_id}};
            my $loser  = $IDtoName{$match->{match}{loser_id}};

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
