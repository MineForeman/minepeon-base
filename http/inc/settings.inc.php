<?php

if (!isset($_SERVER['HTTPS']) and php_sapi_name() <> 'cli') {
	header('Location: https://' . $_SERVER["SERVER_NAME"] . $_SERVER['REQUEST_URI']);
}

$settings = json_decode(@file_get_contents('/opt/minepeon/etc/minepeon.conf'), true);

if(isset($settings['userTimezone'])){
  $timezone = $settings['userTimezone'];
  ini_set( 'date.timezone', $timezone );
  putenv('TZ=' . $timezone);
  date_default_timezone_set($timezone);
}

?>