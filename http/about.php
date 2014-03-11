<?php

include('settings.inc.php');
include('head.php');
include('menu.php');

?>
        <div class="container">
    <fieldset>
      <legend>About</legend>
                
				<img align="right" title="MinePeonQR" alt="Donate to 12Ui8w9q6eq6TxZmow8H9VHbZdfEnsLDsB" src="https://mineforeman.com/wp-content/uploads/2013/01/MinePeonQR.png" width="200" height="200" />
				<blockquote class="a">Peon's in feudal times were indentured vassals, often assigned dangerous, tedious and unpleasant work, like mining.<cite>MineForeman</cite></blockquote>
        </div>

    <div class="container">
     <fieldset>
      <legend>Contact</legend>
              
				<div style="padding: 10px 0 0 0;">You can contact Neil directly at;-</div>
<div style="padding: 10px 0 0 0;"><img style="border: 0; float: left;" alt="" src="https://mineforeman.com/email/MineFormanEmail.png" />
<div style="margin-left: 10px; float: left;">
<div style="padding: 0 0 3px 0;"><a href="https://www.facebook.com/MineForeman"><img style="border: 0;" alt="Facebook" src="https://mineforeman.com/email/facebook.png" /></a></div>
<div style="padding: 0 0 3px 0;"><a href="https://twitter.com/mineforeman"><img style="border: 0;" alt="Twitter" src="https://mineforeman.com/email/twitter.png" /></a></div>
<div style="padding: 0 0 3px 0;"><a href="http://www.linkedin.com/company/mineforeman-com"><img style="border: 0;" alt="LinkedIn" src="https://mineforeman.com/email/linkedin.png" /></a></div>
</div>
<div style="margin-left: 10px; float: left;">
<div style="font-weight: bold; font-family: helvetica; font-size: 16px;">Neil Fincham</div>
<div style="font-family: helvetica; font-size: 12px; color: #999;">MineForeman</div>
<div style="font-family: helvetica; font-size: 12px; color: #999;">Phone: +64 21 545 583</div>
<div style="font-family: helvetica; font-size: 12px; color: #999;">C/O Integral LTD, 99 Sala St, Rotorua, New Zealand. 3010</div>
<div style="font-family: helvetica; font-size: 12px; color: #999;"><a style="font-family: helvetica; font-size: 12px; color: #2f97ff; text-decoration: none;" href="http://mineforeman.com/">Website</a> | <a style="font-family: helvetica; font-size: 12px; color: #2f97ff; text-decoration: none;" href="mailto:neil@mineforeman.com">neil@mineforeman.com</a></div>
</div>
</div>
        </div>

        <div class="container">
<br>
    <fieldset>
      <legend><?php echo $lang["license"]; ?></legend>
            
				<div>
			<?php
//The language system is Work in progress
if ($settings['lang'] == "no"){
include("lang/no/license.no.php");
}else{
include("lang/en/license.en.php");
}
?>
  </fieldset>
				</div>
        </div>
<?php

include('foot.php');
