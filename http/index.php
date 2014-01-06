<?php

require('miner.inc.php');
include('settings.inc.php');

//MinePeon temperature
$mpTemp = round(exec('cat /sys/class/thermal/thermal_zone0/temp') / 1000, 2);

//MinePeon Version
$version = exec('cat /opt/minepeon/etc/version');

//MinePeon CPU load
$mpCPULoad = sys_getloadavg();


$stats = cgminer("devs", "");
$status = $stats['STATUS'];
$devs = $stats['DEVS'];
$summary = cgminer("summary", "");
$pools = cgminer("pools", "");

include('head.php');
include('menu.php');
?>
<div class="container">
<div id="statusChart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>



  <div class="row">
    <div class="col-lg-4">
      <dl class="dl-horizontal">
        <dt>MinePeon Temp</dt>
        <dd><?php echo $mpTemp; ?> <small>&deg;C</small> | <?php echo $mpTemp*9/5+32; ?> <small>&deg;F</small></dd>
        <dt>MinePeon CPU Load</dt>
        <dd><?php echo $mpCPULoad[0]; ?> <small>[1 min]</small></dd>
        <dd><?php echo $mpCPULoad[1]; ?> <small>[5 min]</small></dd>
        <dd><?php echo $mpCPULoad[2]; ?> <small>[15 min]</small></dd>
      </dl>
    </div>
    <div class="col-lg-4">
      <dl class="dl-horizontal">
        <dt>Best Share</dt>
        <dd><?php echo $summary['SUMMARY'][0]['BestShare']; ?></dd>
        <dt>MinePeon Uptime</dt>
        <dd><?php echo secondsToWords(round($uptime[0])); ?></dd>
        <dt>Miner Uptime</dt>
        <dd><?php echo secondsToWords($summary['SUMMARY'][0]['Elapsed']); ?></dd>
      </dl>
    </div>
    <div class="col-lg-4">
      <dl class="dl-horizontal">
        <dt>MinePeon Version</dt>
        <dd><?php echo $version; ?></dd>
        <dt>Miner Version</dt>
        <dd><?php echo $summary['STATUS'][0]['Description']; ?></dd>
        <dt>Donation Minutes</dt>
        <dd><?php echo $settings['donateAmount']; ?>
      </dl>
    </div>
  </div>
  <center>
    <a class="btn btn-default" href='/restart.php'>Restart Miner</a>  
    <a class="btn btn-default" href='/reboot.php'>Reboot</a> 
    <a class="btn btn-default" href='/halt.php'>ShutDown</a>
  </center>
  <h3>Pools</h3>
  <table id="pools" class="table table-striped table-hover">
    <thead> 
      <tr>
	    <th></th>
        <th>URL</th>
        <th>User</th>
        <th>Status</th>
        <th title="Priority">Pr</th>
        <th title="GetWorks">GW</th>
        <th title="Accept">Acc</th>
        <th title="Reject">Rej</th>
        <th title="Discard">Disc</th>
        <th title="Last Share Time">Last</th>       
        <th title="Difficulty 1 Shares">Diff1</th>        
        <th title="Difficulty Accepted">DAcc</th>
        <th title="Difficulty Rejected">DRej</th>
        <th title="Last Share Difficulty">DLast</th>
        <th title="Best Share">Best</th>	
      </tr>
    </thead>
    <tbody>
      <?php echo poolsTable($pools['POOLS']); ?>
    </tbody>
  </table>

  <h3>Devices</h3>
  <?php echo statsTable($devs); ?>
  <?php
  if ($debug == true) {
	
	echo "<pre>";
	print_r($pools['POOLS']);
	print_r($devs);
	echo "<pre>";
	
  }
  ?>

</div>
<?php
include('foot.php');

function statsTable($devs) {
  if(count($devs)==0){
    return "</tbody></table><div class='alert alert-danger'>No devices running</div>";
  }

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
        <th>MH/s</th>
        <th>Accept</th>
        <th>Reject</th>
        <th>Error</th>
        <th>Utility</th>
        <th>Last Share</th>
      </tr>
    </thead>
    <tbody>';

  foreach ($devs as $dev) {
    if ($dev['MHS5s'] > 0) {
	  if (isset($dev['Temperature'])) {
		$temperature = $dev['Temperature'];
	  } else {
	    $temperature = "N/A";
	  }
      $tableRow = $tableRow .
      ($hwErrorPercent >= 10 || $rejectedErrorPercent > 5 ? "<tr class=\"error\">" : "<tr>")
      ."<td class='text-left'>" . $dev['Name'] . "</td>
      <td>" . $dev['ID'] . "</td>
      <td>" . $temperature . "</td>
      <td><a href='http://mineforeman.com/bitcoin-mining-calculator/?hash=" . $dev['MHSav'] . "' target='_blank'>" . $dev['MHSav'] . "</a></td>
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


  $totalShares = $Accepted + $Rejected + $HardwareErrors;
  $tableRow = $tableRow . "
  </tbody>
  <tfoot>
  <tr>
  <th>Totals</th>
  <th>" . $devices . "</th>
  <th></th>
  <th><a href='http://mineforeman.com/bitcoin-mining-calculator/?hash=" . $MHSav . "' target='_blank'>" . $MHSav . "</a></th>
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

  return $tableRow;
}

function secondsToWords($seconds)
{
  $ret = "";

  /*** get the days ***/
  $days = intval(intval($seconds) / (3600*24));
  if($days> 0)
  {
    $ret .= "$days<small> day </small>";
  }

  /*** get the hours ***/
  $hours = (intval($seconds) / 3600) % 24;
  if($hours > 0)
  {
    $ret .= "$hours<small> hr </small>";
  }

  /*** get the minutes ***/
  $minutes = (intval($seconds) / 60) % 60;
  if($minutes > 0)
  {
    $ret .= "$minutes<small> min </small>";
  }

  /*** get the seconds ***/
  $seconds = intval($seconds) % 60;
  if ($seconds > 0) {
    $ret .= "$seconds<small> sec</small>";
  }

  return $ret;
}

function poolsTable($pools) {

// class="success" error warning info

  $poolID = 0;

  $table = "";
  foreach ($pools as $pool) {

    if ($pool['Status'] <> "Alive") {

      $rowclass = 'error';

    } else {

      $rowclass = 'success';

    }
	
	$poolURL = explode(":", str_replace("/", "", $pool['URL']));

    $table = $table . "
    <tr class='" . $rowclass . "'>
	<td>";
	if($poolID != 0) {
		$table = $table . "<a href='/?url=" . $pool['URL'] . "&user=" . $pool['User'] . "'><img src='/img/up.png'></td>";
	}
	$table = $table . "
    <td class='text-left'>" . $poolURL[1] . "</td>
    <td class='text-left ellipsis'>" . $pool['User'] . "</td>
    <td class='text-left'>" . $pool['Status'] . "</td>
    <td>" . $pool['Priority'] . "</td>
    <td>" . $pool['Getworks'] . "</td>
    <td>" . $pool['Accepted'] . "</td>
    <td>" . $pool['Rejected'] . "</td>
    <td>" . $pool['Discarded'] . "</td>
    <td>" . date('H:i:s', $pool['LastShareTime']) . "</td>        
    <td>" . $pool['Diff1Shares'] . "</td>       
    <td>" . round($pool['DifficultyAccepted']) . "&nbsp;["  . (!$pool['Diff1Shares'] == 0 ? round(($pool['DifficultyAccepted'] / $pool['Diff1Shares']) * 100) : 0) .  "%]</td>
    <td>" . round($pool['DifficultyRejected']) . "&nbsp;["  . (!$pool['Diff1Shares'] == 0 ? round(($pool['DifficultyRejected'] / $pool['Diff1Shares']) * 100) : 0) .  "%]</td>
    <td>" . round($pool['LastShareDifficulty'], 0) . "</td>
    <td>" . $pool['BestShare'] . "</td>
    </tr>";
	$poolID++;
  }

  return $table;

}

