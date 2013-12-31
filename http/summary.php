<?php

$db = new PDO('sqlite:/opt/minepeon/var/sql/summary.db');

$sql = "SELECT * from summary";


$results = $db->query($sql);

// echo "date\tclose";

while ($row = $results->fetch(PDO::FETCH_ASSOC)) {

	echo $row['datetime'] . "\t";
	echo $row['MHSav'] . "\t";
	echo $row['MHS5s'] . "\t";
	echo $row['PiTemperature'] . "
";

}


/*
var_dump($results->fetchArray());

print_r($results);


while ($row = $results->fetchArray()) {

	echo $row['datetime'] . "\t";
	echo $row['MHSav'] . "\t";
	echo $row['PiTemperature'] . "\n";

}
*/