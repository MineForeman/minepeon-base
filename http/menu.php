<?php

include_once('functions.inc.php');
include_once('settings.inc.php');
if(!isset($settings['update'])) {
    $settings['update'] = false;
}
this_session_start();
login_check("quick");

if ($settings['lang'] == "no"){
include("lang/no/lang.no.php");
}else{
include("lang/en/lang.en.php");
}
?>
  
<div class="navbar navbar-default">
<div class="container">
    <a class="navbar-brand" href="http://mineforeman.com/minepeon/"><small>Beta </small>MinePeon</a>
    <ul class="nav navbar-nav">
      <li><a href="/"><?php echo $lang["status"]; ?></a></li>
      <li><a href="/pools.php"><?php echo $lang["pools"]; ?></a></li>
      <li><a href="/settings.php"><?php echo $lang["settings"]; ?></a></li>
      <li><a href="/plugins.php"><?php echo $lang["plugins"]; ?></a></li> 
      <li><a href="/about.php"><?php echo $lang["about"]; ?></a></li>
      <li><a href="http://minepeon.com/forums/">Forum</a></li>
<?php 
   if ($handle = opendir('plugins/api_menu/')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
               $menuadd=simplexml_load_file("plugins/api_menu/" . $entry);
echo "<li><a href='" . $menuadd->pl_folder . "'>" . $menuadd->Menu_text . "</a></li>";

            }
        }
        closedir($handle);
   }
?>
</ul>
  </div>
</div>
<?php
if ($settings['update'] == "true"){
?>
<div align="center" class="container">
<div class="alert alert-info alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <strong>Minepeon:</strong> Update available! <a href="/update.php" class="alert-link">Do you want to update?</a>
</div>
</div>
<?php
}
?>
