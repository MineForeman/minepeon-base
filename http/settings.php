<?php

require_once('settings.inc.php');
require_once('miner.inc.php');

// Check for settings to write and do it after all checks
$writeSettings=false;

// User settings
if (isset($_POST['userTimezone'])) {

  $settings['userTimezone'] = $_POST['userTimezone'];
  $writeSettings=true;

}
if (isset($_POST['userPassword'])) {

	if ($_POST['userPassword'] <> '') {
	
		exec("/usr/bin/htpasswd -b /opt/minepeon/etc/uipassword minepeon " . $_POST['userPassword']);

	}
}
// Miner startup file

if (isset($_POST['minerSettings'])) {

	if ($_POST['minerSettings'] <> '') {
	
		file_put_contents('/opt/minepeon/etc/init.d/miner-start.sh', preg_replace('/\x0d/', '', $_POST['minerSettings']));
		//exec('/usr/bin/cat "'. $_POST['minerSettings'] . '" > /opt/minepeon/etc/init.d/miner-start.sh');
	}
}

$minerStartup = file_get_contents('/opt/minepeon/etc/init.d/miner-start.sh');


// Mining settings

if (isset($_POST['miningExpDev'])) {

  $settings['miningExpDev'] = $_POST['miningExpDev'];
  $writeSettings=true;

}
if (isset($_POST['miningExpHash'])) {

  $settings['miningExpHash'] = $_POST['miningExpHash'];
  $writeSettings=true;

}

// Donation settings
if (isset($_POST['donateEnable']) and isset($_POST['donateAmount'])) {

  $settings['donateEnable'] = $_POST['donateEnable']=="true";
  $settings['donateAmount'] = $_POST['donateAmount'];

  // If one of both 0, make them both
  if ($_POST['donateEnable']=="false" || $_POST['donateAmount']<1) {
    $settings['donateEnable'] = false;
    $settings['donateAmount'] = 0;
  }
  $writeSettings=true;
  
}

// Alert settings
if (isset($_POST['alertEnable'])) {

  $settings['alertEnable'] = $_POST['alertEnable']=="true";
  $writeSettings=true;
  
}
if (isset($_POST['alertDevice'])) {

  $settings['alertDevice'] = $_POST['alertDevice'];
  $writeSettings=true;

}
if (isset($_POST['alertEmail'])) {

	$settings['alertEmail'] = $_POST['alertEmail'];
	$writeSettings=true;

}
if (isset($_POST['alertSmtp'])) {

  $settings['alertSmtp'] = $_POST['alertSmtp'];
  $writeSettings=true;

}

// Write settings
if ($writeSettings) {
  ksort($settings);
  writeSettings($settings);
}

function formatOffset($offset) {
	$hours = $offset / 3600;
	$remainder = $offset % 3600;
	$sign = $hours > 0 ? '+' : '-';
	$hour = (int) abs($hours);
	$minutes = (int) abs($remainder / 60);

	if ($hour == 0 AND $minutes == 0) {
		$sign = ' ';
	}
	return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');

}

$utc = new DateTimeZone('UTC');
$dt = new DateTime('now', $utc);

$tzselect = '<select id="userTimezone" name="userTimezone" class="form-control">';

foreach(DateTimeZone::listIdentifiers() as $tz) {
	$current_tz = new DateTimeZone($tz);
	$offset =  $current_tz->getOffset($dt);
	$transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
	$abbr = $transition[0]['abbr'];

	$tzselect = $tzselect . '<option ' .($settings['userTimezone']==$tz?"selected":""). ' value="' .$tz. '">' .$tz. ' [' .$abbr. ' '. formatOffset($offset). ']</option>';
}
$tzselect = $tzselect . '</select>';


include('head.php');
include('menu.php');
?>
<div class="container">
  <h2>Settings</h2>
  <form name="user" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>User</legend>
      <div class="form-group">
        <label for="userTimezone" class="control-label col-lg-3">Timezone</label>
        <div class="col-lg-9">
          <?php echo $tzselect ?>
          <p class="help-block">MinePeon thinks it is now <?php echo date('D, d M Y H:i:s T') ?></p>
        </div>
      </div>
      <div class="form-group">
        <label for="userPassword" class="control-label col-lg-3">New Password</label>
        <div class="col-lg-9">
          <input type="password" placeholder="New password" id="userPassword" name="userPassword" class="form-control">
          <p class="help-block">An empty password will not be saved.</p>
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>

  <form name="mining" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Mining</legend>
      <div class="form-group">
        <label for="miningExpDev" class="control-label col-lg-3">Expected Devices</label>
        <div class="col-lg-9">
          <input type="number" value="<?php echo $settings['miningExpDev'] ?>" id="miningExpDev" name="miningExpDev" class="form-control">
          <p class="help-block">
            If the count of active devices falls below this value, an alert will be sent.
          </p>
        </div>
      </div>
      <div class="form-group">
        <label for="miningExpHash" class="control-label col-lg-3">Expected Hashrate</label>
        <div class="col-lg-9">
          <div class="input-group">
            <input type="number" value="<?php echo $settings['miningExpHash'] ?>" id="miningExpHash" name="miningExpHash" class="form-control">
            <span class="input-group-addon">MH/s</span>
          </div>
          <p class="help-block">
            If the hashrate falls below this value an alert will be sent.
          </p>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>

  <form name="alerts" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Alerts</legend>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <div class="checkbox">
            <input type='hidden' value='false' name='alertEnable'>
            <label>
              <input type="checkbox" <?php echo $settings['alertEnable']?"checked":""; ?> value="true" id="alertEnable" name="alertEnable"> Enable e-mail alerts
            </label>
          </div>
        </div>
      </div>
      <div class="form-group alert-enabled <?php echo $settings['alertEnable']?"":"collapse"; ?>">
        <label for="alertDevice" class="control-label col-lg-3">Device Name</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $settings['alertDevice'] ?>" id="alertDevice" name="alertDevice" class="form-control" placeholder="MinePeon">
        </div>
      </div>
      <div class="form-group alert-enabled <?php echo $settings['alertEnable']?"":"collapse"; ?>">
        <label for="alertEmail" class="control-label col-lg-3">E-mail</label>
        <div class="col-lg-9">
          <input type="email" value="<?php echo $settings['alertEmail'] ?>" id="alertEmail" name="alertEmail" class="form-control" placeholder="example@example.com">
        </div>
      </div>
      <div class="form-group alert-enabled <?php echo $settings['alertEnable']?"":"collapse"; ?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Server</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $settings['alertSmtp'] ?>" id="alertSmtp" name="alertSmtp" class="form-control" placeholder="smtp.myisp.com">
          <p class="help-block">Please choose your own SMTP server.</p>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>
  
  <form name="minerStartup" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Miner Startup Settings</legend>
      <div class="form-group">
        <label for="minerSettings" class="control-label col-lg-3">Settings</label>
        <div class="col-lg-9">
          <div>
			<textarea rows="4" cols="120" id="minerSettings" name="minerSettings"><?php echo $minerStartup ?></textarea>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
		  <button type="button" type="bfgminer" onclick="myFunction('bfgminer')" class="btn btn-default">Default bfgminer</button>
		  <button type="button" type="cgminer" onclick="myFunction('cgminer')" class="btn btn-default">Default cgminer</button>
		  <script language="javascript" type="text/javascript">
			function myFunction(miner) {
			  if (miner == "cgminer") {
				document.getElementById('minerSettings').value = "#!/bin/bash\n/usr/bin/screen -dmS miner /opt/minepeon/bin/cgminer -c /opt/minepeon/etc/miner.conf\n";
			  } 
			  if (miner == "bfgminer") {
				document.getElementById('minerSettings').value = "#!/bin/bash\n/usr/bin/screen -dmS miner /opt/minepeon/bin/bfgminer -S all -c /opt/minepeon/etc/miner.conf\n";
			  }
			}
		  </script>
		  <p class="help-block">
            Enter you own miner parameters or select a default bfgminer or cgminer configuration.  You will need to press Save and then reboot MinePeon when you finish.
          </p>
        </div>
      </div>
    </fieldset>
  </form>
  
  <form name="donation" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Donation</legend>
      <div class="form-group">
        <label for="donateAmount" class="control-label col-lg-3">Donation</label>
        <div class="col-lg-9">
          <div class="checkbox">
            <input type='hidden' value='false' name='donateEnable'>
            <label>
              <input type="checkbox" <?php echo $settings['donateEnable']?"checked":""; ?> value="true" id="donateEnable" name="donateEnable"> Enable donation
            </label>
          </div>
          <div class="donate-enabled <?php echo $settings['donateEnable']?"":"collapse"; ?>">
            <div class="input-group">
              <input type="number" value="<?php echo $settings['donateAmount'] ?>" placeholder="Donation minutes" id="donateAmount" name="donateAmount" class="form-control">
              <span class="input-group-addon">minutes per day</span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>
  
</div>
<?php
include('foot.php');
?>
