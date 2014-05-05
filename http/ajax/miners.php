<?php
require_once('miner.inc.php');
include_once('functions.inc.php');
include_once("../lang/en/lang.en.php");
include_once('settings.inc.php');
$G_MHSav = 0;
$stats = miner("devs", "");
$status = $stats['STATUS'];
$devs = $stats['DEVS'];
$summary = miner("summary", "");
$pools = miner("pools", "");

  if(count($devs)==0){
    echo "<div class='alert alert-danger'>No devices running</div>";
  }else{

  $devices = 0;
  $MHSav = 0;
  $Accepted = 0;
  $Rejected = 0;
  $HardwareErrors = 0;
  $Utility = 0;

  $tableRow = '<table id="stats" class="tablesorter table table-striped table-hover stats">
    <thead>
      <tr>
        <th>Name</th>
        <th>ID</th>
        <th>Temp</th>
        <th>Hash Rate</th>
        <th>Accept</th>
        <th>Reject</th>
        <th>Error</th>
        <th>Utility</th>
        <th>Last Share</th>
      </tr>
    </thead>
    <tbody>';

 	$hwErrorPercent = 0;
	$DeviceRejected = 0;

  foreach ($devs as $dev) {
  
	// Sort out valid deceives
	
	$validDevice = true;
 
	if(!isset($dev['MHS5s'])) {
    		$dev['MHS5s'] = 0;
	}

       if(!isset($dev['MHS20s'])) {
               $dev['MHS20s'] = 0;
        }

	
       if (!$dev['MHS5s'] > 1 || !$dev['MHS20s'] > 1) {
		// not mining, not a valid device
		$validDevice = false;
	}
        
	//if ((time() - $dev['LastShareTime']) > 1000) {
	//	// Only show devices that have returned a share in the past 5 minutes
	//	$validDevice = false;
	//}
	
	if (isset($dev['Temperature'])) {
		$temperature = $dev['Temperature'];
	} else {
		$temperature = "N/A";
	}
        
        if ($dev['MHSav'] > 999){
          $hrate = $dev['MHSav'] / 1000;
	  $hrate = number_format((float)$hrate, 2, '.', '');
          $hrate = $hrate . " GH/s";
        }else{
	  $hrate = $dev['MHSav'] . " MH/s";
        }
	
	if ($validDevice) {

		if ($dev['DeviceHardware%'] >= 10 || $dev['DeviceRejected%'] > 5) {
			$tableRow = $tableRow . "<tr class=\"error\">";
		} else {
			$tableRow = $tableRow . "<tr class=\"success\">";
		}
		
	$tableRow = $tableRow . "<td>" . $dev['Name'] . "</td>
      <td>" . $dev['ID'] . "</td>
      <td>" . $temperature . "</td>
      <td><a href='http://mineforeman.com/bitcoin-mining-calculator/?hash=" . $dev['MHSav'] . "' target='_blank'>" . $hrate . "</a></td>
      <td>" . $dev['Accepted'] . "</td>
      <td>" . $dev['Rejected'] . " [" . round($dev['DeviceRejected%'], 2) . "%]</td>
      <td>" . $dev['HardwareErrors'] . " [" . round($dev['DeviceHardware%'], 2) . "%]</td>
      <td>" . $dev['Utility'] . "</td>
      <td>" . date('H:i:s', $dev['LastShareTime']) . "</td>
      </tr>";

		$devices++;
		$MHSav = $MHSav + $dev['MHSav'];
		$Accepted = $Accepted + $dev['Accepted'];
		$Rejected = $Rejected + $dev['Rejected'];
		$HardwareErrors = $HardwareErrors + $dev['HardwareErrors'];
		$DeviceRejected = $DeviceRejected + $dev['DeviceRejected%'];
		$hwErrorPercent = $hwErrorPercent + $dev['DeviceHardware%'];
		$Utility = $Utility + $dev['Utility'];



	}
  }

        if ($MHSav > 999){
          $hrateT = $MHSav / 1000;
	  $hrateT = number_format((float)$hrateT, 2, '.', '');
          $hrateT = $hrateT . " GH/s";
        }else{
	  $hrateT = $MHSav . " MH/s";
        }
  $GLOBALS['G_MHSav'] = $hrateT . "|" . $devices . " DEV";
  $totalShares = $Accepted + $Rejected + $HardwareErrors;
  $tableRow = $tableRow . "
  </tbody>
  <tfoot>
  <tr>
  <th>Totals</th>
  <th>" . $devices . "</th>
  <th></th>
  <th><a href='http://mineforeman.com/bitcoin-mining-calculator/?hash=" . $MHSav . "' target='_blank'>" . $hrateT . "</a></th>
  <th>" . $Accepted . "</th>
  <th>" . $Rejected . " [" . round(($DeviceRejected / $devices), 2) . "%]</th>
  <th>" . $HardwareErrors . " [" . round(($hwErrorPercent / $devices), 2) . "%]</th>
  <th>" . $Utility . "</th>
  <th></th>
  </tr>
  </tfoot>
  </tbody>
  </table>
  ";
echo $tableRow;
}
?>
