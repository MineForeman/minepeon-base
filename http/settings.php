<?php

require_once('settings.inc.php');
require_once('miner.inc.php');
 
// Check for settings to write and do it after all checks
$writeSettings=false;

// Restore 

if (isset($_FILES["file"]["tmp_name"])) {
	exec("tar -xzf " . $_FILES["file"]["tmp_name"] . " -C / ");
	header('Location: /reboot.php');
	exit;
}



// User settings
if (isset($_POST['userTimezone'])) {

  $settings['userTimezone'] = $_POST['userTimezone'];
  ksort($settings);
  writeSettings($settings);
  header('Location: /settings.php');
  exit;

}

if (isset($_POST['lang'])) {

  $settings['lang'] = $_POST['lang'];
  $writeSettings=true;

}

if (isset($_POST['userPassword1'])) {

	if ($_POST['userPassword1'] <> '') {
	
		exec("/usr/bin/htpasswd -b /opt/minepeon/etc/uipassword minepeon " . $_POST['userPassword1']);
		header('Location: /settings.php');
		exit;

	}
}
// Miner startup file

if (isset($_POST['minerSettings'])) {

	if ($_POST['minerSettings'] <> '') {
	
		file_put_contents('/opt/minepeon/etc/init.d/miner-start.sh', preg_replace('/\x0d/', '', $_POST['minerSettings']));
		exec('/usr/bin/chmod +x /opt/minepeon/etc/init.d/miner-start.sh');
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
  $settings['donateTime'] = $_POST['donateTime'];
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

if (isset($_POST['alertSMTPAuth'])) {

  $settings['alertSMTPAuth'] = $_POST['alertSMTPAuth']=="true";
  $writeSettings=true;

}

if (isset($_POST['alertSmtpAuthUser'])) {

  $settings['alertSmtpAuthUser'] = $_POST['alertSmtpAuthUser'];
  $writeSettings=true;

}

if (isset($_POST['alertSmtpAuthPass'])) {

  $settings['alertSmtpAuthPass'] = $_POST['alertSmtpAuthPass'];
  $writeSettings=true;

}

if (isset($_POST['alertSmtpAuthPort'])) {

  $settings['alertSmtpAuthPort'] = $_POST['alertSmtpAuthPort'];
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
  
<!-- ######################## -->

  <form name="timezone" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>TimeZone</legend>
      <div class="form-group">
        <label for="userTimezone" class="control-label col-lg-3">Timezone</label>
        <div class="col-lg-9">
          <?php echo $tzselect ?>
          <p class="help-block">MinePeon thinks it is now <?php echo date('D, d M Y H:i:s T') ?></p>
		  <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>

  <form name="language" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Language</legend>

      <div class="form-group">
        <label for="userlanguage" class="control-label col-lg-3">Language</label>
        <div class="col-lg-9">
          <select name="lang" class="form-control">
          <option value="en">English</option>
          <option value="no" <?php if ($settings['lang'] == "no"){echo "selected";} ?>>Norsk (Norwegian)</option>
          </select>
          <br>
          <button type="submit" id class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>
  
    <form name="password" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Password</legend>
      <div class="form-group">
        <label for="userPassword" class="control-label col-lg-3">New Password</label>
        <div class="col-lg-9">
          <input type="password" placeholder="New password" id="userPassword1" name="userPassword1" class="form-control" onkeyup="checkPass(); return false;">
		  <br />
		  <input type="password" placeholder="Repeat Password" id="userPassword2" name="userPassword2" class="form-control" onkeyup="checkPass(); return false;">
		  <br />
          <button type="submit" id="submitPassword" class="btn btn-default">Save</button>
        </div>
		
      </div>
	  
    </fieldset>
  </form>
  
<!-- ######################## -->

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
  
<!-- ######################## Alerts -->

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
          <div class="checkbox" >
            <input type='hidden' value='false' name='alertSMTPAuth'>
            <label class="form-group alert-enabled ">
              <input type="checkbox"  class="form-group alert-enabled " <?php echo $settings['alertSMTPAuth']?"checked":""; ?> value="true" id="alertSMTPAuth" name="alertSMTPAuth"> Use SMTP Auth
            </label>
          </div>
        </div>
      </div>
	  
	  <div class="form-group smtpauth-enabled alert-enabled <?php echo $settings['alertSMTPAuth']?"":"collapse"; ?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Auth Username</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $settings['alertSmtpAuthUser'] ?>" id="alertSmtpAuthUser" name="alertSmtpAuthUser" class="form-control">
        </div>
      </div>
	  
	  <div class="form-group smtpauth-enabled alert-enabled <?php echo $settings['alertSMTPAuth']?"":"collapse"; ?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Auth Password</label>
        <div class="col-lg-9">
          <input type="password" value="<?php echo $settings['alertSmtpAuthPass'] ?>" id="alertSmtpAuthPass" name="alertSmtpAuthPass" class="form-control">
        </div>
      </div>

	  <div class="form-group smtpauth-enabled alert-enabled <?php echo $settings['alertSMTPAuth']?"":"collapse"; ?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Auth Port</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $settings['alertSmtpAuthPort'] ?>" id="alertSmtpAuthPort" name="alertSmtpAuthPort" class="form-control">
        </div>
      </div>
	  
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>
  
<!-- ######################## -->

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
		  <button type="button" type="bfgminer" onclick="minerSwitch('bfgminer')" class="btn btn-default">Default bfgminer</button>
		  <button type="button" type="cgminer" onclick="minerSwitch('cgminer')" class="btn btn-default">Default cgminer</button>
		  <button type="button" type="cgminer" onclick="minerSwitch('cgminer-HEXu')" class="btn btn-default">cgminer-HEXu</button>
		  <script language="javascript" type="text/javascript">
			function minerSwitch(miner) {
			  if (miner == "cgminer") {
				document.getElementById('minerSettings').value = "#!/bin/bash\nsleep 10\n/usr/bin/screen -dmS miner /opt/minepeon/bin/cgminer -c /opt/minepeon/etc/miner.conf\n";
			  } 
			  if (miner == "bfgminer") {
				document.getElementById('minerSettings').value = "#!/bin/bash\nsleep 10\n/usr/bin/screen -dmS miner /opt/minepeon/bin/bfgminer -S all -c /opt/minepeon/etc/miner.conf\n";
			  }
			  if (miner == "cgminer-HEXu") {
				document.getElementById('minerSettings').value = "#!/bin/bash\nsleep 10\n/usr/bin/screen -dmS miner /opt/minepeon/bin/cgminer-HEXu -c /opt/minepeon/etc/miner.conf\n";
			  } 
			}
		  </script>
		  <p class="help-block">
            Enter you own miner parameters or select a default bfgminer or cgminer configuration.  
			You will need to press Save and then reboot MinePeon when you finish.<br />
			If you intend to enable the cgminer-HEXu option <a href="http://minepeon.com/index.php/Cgminer-HEXu">please read this page for instructions.</a>
          </p>
        </div>
      </div>
    </fieldset>
  </form>
  
<!-- ######################## -->

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
<div class="input-group">
  <span class="input-group-addon">Donate At</span>
<select name="donateTime" class="form-control">
          <option <?php if ($settings['donateTime'] == "1"){echo "selected";} ?> value="1">1</option>
          <option <?php if ($settings['donateTime'] == "2"){echo "selected";} ?>  value="2">2</option>
          <option <?php if ($settings['donateTime'] == "3"){echo "selected";} ?>  value="3">3</option>
          <option <?php if ($settings['donateTime'] == "4"){echo "selected";} ?>  value="4">4</option>
          <option <?php if ($settings['donateTime'] == "5"){echo "selected";} ?>  value="5">5</option>
          <option <?php if ($settings['donateTime'] == "6"){echo "selected";} ?>  value="6">6</option>
          <option <?php if ($settings['donateTime'] == "7"){echo "selected";} ?>  value="7">7</option>
          <option <?php if ($settings['donateTime'] == "8"){echo "selected";} ?>  value="8">8</option>
          <option <?php if ($settings['donateTime'] == "9"){echo "selected";} ?>  value="9">9</option>
          <option <?php if ($settings['donateTime'] == "10"){echo "selected";} ?>  value="10">10</option>
          <option <?php if ($settings['donateTime'] == "11"){echo "selected";} ?>  value="11">11</option>
          <option <?php if ($settings['donateTime'] == "12"){echo "selected";} ?>  value="12">12</option>
          <option <?php if ($settings['donateTime'] == "13"){echo "selected";} ?>  value="13">13</option>
          <option <?php if ($settings['donateTime'] == "14"){echo "selected";} ?>  value="14">14</option>
          <option <?php if ($settings['donateTime'] == "15"){echo "selected";} ?>  value="15">15</option>
          <option <?php if ($settings['donateTime'] == "16"){echo "selected";} ?>  value="16">16</option>
          <option <?php if ($settings['donateTime'] == "17"){echo "selected";} ?>  value="17">17</option>
          <option <?php if ($settings['donateTime'] == "18"){echo "selected";} ?>  value="18">18</option>
          <option <?php if ($settings['donateTime'] == "19"){echo "selected";} ?>  value="19">19</option>
          <option <?php if ($settings['donateTime'] == "20"){echo "selected";} ?>  value="20">20</option>
          <option <?php if ($settings['donateTime'] == "21"){echo "selected";} ?>  value="21">21</option>
          <option <?php if ($settings['donateTime'] == "22"){echo "selected";} ?>  value="22">22</option>
          <option <?php if ($settings['donateTime'] == "23"){echo "selected";} ?>  value="23">23</option>
          <option <?php if ($settings['donateTime'] == "24"){echo "selected";} ?>  value="24">24</option>
          </select>
              <span class="input-group-addon">O'Clock</span>
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
  
<!-- ######################## -->

  <form name="backup" action="/settings.php" method="post" enctype="multipart/form-data" class="form-horizontal">
    <fieldset>
      <legend>Backup</legend>
     <div class="form-group">
        <div class="col-lg-9 col-offset-3">
		  <a class="btn btn-default" href="/backup.php">Backup</a>
		  <p class="help-block">The backup will contain all of your settings and statistics.  Plugins will have to be restored separately.</p>
        </div>
      </div>
      <div class="form-group">
		<div class="col-lg-9 col-offset-3">
		  <input type="file" name="file" id="file" class="btn btn-default" data-input="false">
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-lg-9 col-offset-3">
		  <button type="submit" name="submit" class="btn btn-default">Restore</button>
		  <p class="help-block">Restoring a configuration will cause your MinePeon to reboot.</p>
		</div>
      </div>
    </fieldset>
  </form>
<script type="text/javascript" id="js">
  function checkPass()
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('userPassword1');
    var pass2 = document.getElementById('userPassword2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('confirmMessage');
	var submit = document.getElementById('submitPassword');
    //Set the colors we will be using ...
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    //Compare the values in the password field 
    //and the confirmation field
    if(pass1.value == pass2.value){
        //The passwords match. 
        //Set the color to the good color and inform
        //the user that they have entered the correct password 
		document.getElementById("submitPassword").disabled = false;
        pass2.style.backgroundColor = goodColor;
        message.style.color = goodColor;
        message.innerHTML = "Passwords Match!"
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
		document.getElementById("submitPassword").disabled = true;
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Passwords Do Not Match!"
    }
} </script>
<?php
include('foot.php');
?>
