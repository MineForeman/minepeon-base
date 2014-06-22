<?php

function totalMH() {
  $stats = miner("devs", "");
$devs = $stats['DEVS'];
  $MHSav = 0;

if(count($devs)==0){
    return "0";
  }

  foreach ($devs as $dev) {
     $MHSav = $MHSav + $dev['MHSav'];
	}
  
  return $MHSav;
}

