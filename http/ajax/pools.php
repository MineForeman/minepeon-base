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

?>
  <table id="pools" class="table table-striped table-hover">
    <thead> 
      <tr>
        <th></th>
        <th><?php echo $lang["url"]; ?></th>
        <th><?php echo $lang["user"]; ?></th>
        <th><?php echo $lang["status"]; ?></th>
        <th title="Priority">Pr</th>
        <th title="GetWorks">GW</th>
        <th title="Accept">Acc</th>
        <th title="Reject">Rej</th>
        <th title="Discard">Disc</th>
        <th title="Last Share Time"><?php echo $lang["last"]; ?></th>       
        <th title="Difficulty 1 Shares">Diff1</th>        
        <th title="Difficulty Accepted">DAcc</th>
        <th title="Difficulty Rejected">DRej</th>
        <th title="Last Share Difficulty">DLast</th>
        <th title="Best Share"><?php echo $lang["best"]; ?></th>	
      </tr>
    </thead>

<?php

echo poolsTable($pools["POOLS"]);

echo "</tbody></table>";

function poolsTable($pools) {

// class="success" error warning info

  $poolID = 0;

  $table = "";
  
  array_sort_by_column($pools, 'Priority');
  
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
		$table = $table . "<form name='spool' action='/' method='post'><input type='hidden' name='url' value='" . $pool['URL'] . "' /><input type='image' src='/img/up.png' name='image'></form>";
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
?>
