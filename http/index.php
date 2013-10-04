<?php

require('miner.inc.php');
include('settings.inc.php');


create_graph("mhsav-hour.png", "-1h", "Last Hour");
create_graph("mhsav-day.png", "-1d", "Last Day");
create_graph("mhsav-week.png", "-1w", "Last Week");
create_graph("mhsav-month.png", "-1m", "Last Month");
create_graph("mhsav-year.png", "-1y", "Last Year");

function create_graph($output, $start, $title) {
  $RRDPATH = '/opt/minepeon/var/rrd/';
  $options = array(
    "--slope-mode",
    "--start", $start,
    "--title=$title",
    "--vertical-label=Hash per second",
    "--lower=0",
    "DEF:hashrate=" . $RRDPATH . "hashrate.rrd:hashrate:AVERAGE",
    "CDEF:realspeed=hashrate,1000,*",
    "LINE2:realspeed#FF0000"
    );

  $ret = rrd_graph("/opt/minepeon/http/rrd/" . $output, $options);
  if (! $ret) {
    echo "<b>Graph error: </b>".rrd_error()."\n";
  }
}

//MinePeon temperature
$mpTemp = substr(substr(exec('/opt/vc/bin/vcgencmd measure_temp'), 5), 0, -2);

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
  <h2>Status</h2>
  <p class="text-center">
    <img src="rrd/mhsav-hour.png" alt="mhsav.png" />
    <img src="rrd/mhsav-day.png" alt="mhsav.png" /><br/>
    <a href="#" id="chartToggle">Display extended charts</a>
  </p>
  <p class="text-center collapse chartMore">
    <img src="rrd/mhsav-week.png" alt="mhsav.png" />
    <img src="rrd/mhsav-month.png" alt="mhsav.png" />
  </p>
  <p class="text-center collapse chartMore">
    <img src="rrd/mhsav-year.png" alt="mhsav.png" />
  </p>
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
       <?php if ($settings['donateAmount'] == 0) { echo '<dd><marquee direction="left" scrollamount="3" behavior="scroll" style="width: 60px; height: 15px; color: #ff0000; font-size: 11px; text-decoration: blink;">Kitten Killer!</marquee></dd>'; }else{ echo "<dd>" . $settings['donateAmount']."</dd>"; } ?>
      </dl>
    </div>
  </div>

  <h3>Pools</h3>
  <table id="pools" class="tablesorter table table-striped table-hover">
    <thead> 
      <tr>
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
    $devShareTotal = $dev['Accepted'] + $dev['Rejected'] + $dev['HardwareErrors'];
    $rejectedErrorPercent = round(($dev['Rejected'] / $devShareTotal) * 100, 2);
    $hwErrorPercent = round(($dev['HardwareErrors'] / $devShareTotal) * 100, 2);
    if ($dev['MHS5s'] > 0) {
      $tableRow = $tableRow .
      ($hwErrorPercent >= 10 || $rejectedErrorPercent > 5 ? "<tr class=\"error\">" : "<tr>")
      ."<td class='text-left'>" . $dev['Name'] . "</td>
      <td>" . $dev['ID'] . "</td>
      <td>" . $dev['Temperature'] . "</td>
      <td><a href='http://mineforeman.com/bitcoin-mining-calculator/?hash=" . $dev['MHSav'] . "' target='_blank'>" . $dev['MHSav'] . "</a></td>
      <td>" . $dev['Accepted'] . "</td>
      <td>" . $dev['Rejected'] . " [" . $rejectedErrorPercent . "%]</td>
      <td>" . $dev['HardwareErrors'] . " [" . $hwErrorPercent . "%]</td>
      <td>" . $dev['Utility'] . "</td>
      <td>" . date('H:i:s', $dev['LastShareTime']) . "</td>
      </tr>";

      $devices++;
      $MHSav = $MHSav + $dev['MHSav'];
      $Accepted = $Accepted + $dev['Accepted'];
      $Rejected = $Rejected + $dev['Rejected'];
      $HardwareErrors = $HardwareErrors + $dev['HardwareErrors'];
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
  <th>" . $Rejected . " [" . round(($Rejected / $totalShares) * 100, 2) . "%]</th>
  <th>" . $HardwareErrors . " [" . round(($HardwareErrors / $totalShares) * 100, 2) . "%]</th>
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
    <td>" . $pool['DifficultyAccepted'] . "&nbsp;["  . (!$pool['Diff1Shares'] == 0 ? round(($pool['DifficultyAccepted'] / $pool['Diff1Shares']) * 100, 2) : 0) .  "%]</td>
    <td>" . $pool['DifficultyRejected'] . "&nbsp;["  . (!$pool['Diff1Shares'] == 0 ? round(($pool['DifficultyRejected'] / $pool['Diff1Shares']) * 100, 2) : 0) .  "%]</td>
    <td>" . $pool['LastShareDifficulty'] . "</td>
    <td>" . $pool['BestShare'] . "</td>     
    </tr>";

  }

  return $table;

}

