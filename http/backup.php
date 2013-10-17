<?php

include('settings.inc.php');

$fileDate = date("YmdHis");

// got part of this from phpmyadmin.
header('Content-Type: application/x-gzip');
$content_disp = ( ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT) == 'IE') ? 'inline' : 'attachment';
header('Content-Disposition: ' . $content_disp . '; filename="' . $fileDate . 'MinePeonBackup.tar.gz"');
header('Pragma: no-cache');
header('Expires: 0');

// create the gzipped tarfile.
passthru( "tar cz /opt/minepeon/etc/minepeon.conf /opt/minepeon/etc/miner.conf /opt/minepeon/etc/uipassword /opt/minepeon/var/rrd/*.rrd /opt/minepeon/etc/init.d/miner-start.sh");

