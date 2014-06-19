<?php

require_once('miner.inc.php');
include_once('functions.inc.php');
include_once('settings.inc.php');

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
    //echo "<b>Graph error: </b>".rrd_error()."\n";
  }
}



if (isset($_POST['url'])) {
        
$pools = miner('pools','')['POOLS'];
  $pool = 0;
  foreach ($pools as $key => $value) {
    if(isset($value['User']) && $value['URL']==$_POST['url']){
	  miner('switchpool',$pool);
    }
	$pool = $pool + 1;
  }

}

include('head.php');
?>
<script src="js/jquery.min.js"></script>
<?php
  if ($debug == true) {
?>
   <script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            $("#status1").load("ajax/status.php");
            $("#miners1").load("ajax/miners.php");
            $("#pools1").load("ajax/pools.php");
           }, 1000);
    });
   </script>
<?php
}
include('menu.php');
?>

<div class="container">
  <h2>Status</h2>
  <?php
  if (file_exists('/opt/minepeon/http/rrd/mhsav-hour.png')) {
  ?>
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
  <?php
  } else {
  ?>
  <center><h1>Graphs not ready yet</h1></center>
  <center><h2>Please wait upto 5 minutes</h2></center>
  <?php
  }
  ?>

 <div id="status1"><?php include_once("ajax/status.php"); ?></div>

  <center>
    <a class="btn btn-default" href='/restart.php'><?php echo $lang["restartminer"]; ?></a>  
    <a class="btn btn-default" href='/reboot.php'><?php echo $lang["reboot"]; ?></a> 
    <a class="btn btn-default" href='/halt.php'><?php echo $lang["shutdown"]; ?></a>
  </center>
  <h3><?php echo $lang["pools"]; ?></h3>

      <div id="pools1"><?php include_once("ajax/pools.php"); ?></div>


  <h3><?php echo $lang["devices"]; ?></h3>
 <div id="miners1"><?php include_once("ajax/miners.php"); ?></div>
  <?php
  if ($debug == true) {
	
	echo "<pre>";
	print_r($pools['POOLS']);
	print_r($devs);
	echo "<pre>";
	
  }
  ?>

</div>
<script language="javascript" type="text/javascript">
 
document.title = '<?php echo $G_MHSav; ?>|<?php echo $version; ?>';
 
<?php 
 
// Change screen colour test for alerts
 
if ($settings['donateAmount'] < 1) {
	echo 'document.body.style.background = "#FFFFCF"';
}

?>

</script>
<?php
include('foot.php');

