<?php

$db = new PDO('sqlite:/opt/minepeon/var/sql/summary.db');

$sql = "SELECT * from summary";


$results = $db->query($sql);

// echo "date\tclose";

while ($row = $results->fetch(PDO::FETCH_ASSOC)) {

	echo $row['datetime'] . "\t";
	if ($row['MHSav'] != '') {
		echo $row['MHSav'] . "\t";
	} else {
		echo '0' . "\t";
	}
	if ($row['MHS5s'] != '') {
		echo $row['MHS5s'] . "\t";
	} else {
		echo '0' . "\t";
	}
	if ($row['PiTemperature'] != '') {
		echo $row['PiTemperature'] . "
";
	} else {
		echo '0' . "
";
	}

}


