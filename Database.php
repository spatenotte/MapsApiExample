<?php

	include 'pass.php';
	define("DB_host", $host);
	define("DB_name", $dbname);
	define("DB_username", $username);
	define("DB_pass", $pass);
	define("DB_port", $port);

	if(isset($_POST['func'])) {
		if($_POST['func'] == 'getNodes') {
		    getNodes();
		}
		else if($_POST['func'] == 'getEdges'){
			getEdges();
		}
	}

	function connect() {
		return pg_connect("host=" . DB_host . " dbname=" . DB_name . " user=" . DB_username . " password=" . DB_pass . " port=" . DB_port) or die('Could not connect: ' . pg_last_error());
	}

	function close($result, $dbconn) {
		pg_free_result($result);
		pg_close();
	}

	function getNodes() {
		$conn = connect();
		$query = 'SELECT * FROM node';
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		$array = array();
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		    array_push($array, array('id' => $line["id"], 'cityname' => $line["city_name"], 'lat' => $line["latitude"], 'lng' => $line["longitude"]));
		}
		close($result, $conn);
		echo json_encode($array);
	}

	function getEdges() {
		$conn = connect();
		$query = 'SELECT * FROM edge';
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		$array = array();
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			if($line["first_node_id"] > $line["second_node_id"]) {
				array_push($array, array('origin' => $line["second_node_id"], 'destination' => $line["first_node_id"], 'distance' => $line["distance_between_nodes"]));
			}
		    else {
		    	array_push($array, array('origin' => $line["first_node_id"], 'destination' => $line["second_node_id"], 'distance' => $line["distance_between_nodes"]));
		    }
		}
		close($result, $conn);
		echo json_encode($array);
	}
?>
