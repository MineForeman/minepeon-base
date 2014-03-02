<?php

include('settings.inc.php');



include('head.php');
include('menu.php');

?>
        <div class="container">
                <h1><?php echo $lang["license"]; ?></h1> 
				<div>
			<?php
//The language system is Work in progress
if ($settings['lang'] == "no"){
include("lang/no/license.no.php");
}else{
include("lang/en/license.en.php");
}
?>
				</div>
        </div>
<?php

include('foot.php');
