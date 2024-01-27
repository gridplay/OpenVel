<?php
$dbhost = "localhost";
$dbport = 3306;
$dbuser = "canada";
$dbpass = "L0v3sux";
$dbs = ["grid_sim1"]; // database per simulator
error_reporting(0);
class DB {
	public $mysqli;
	public function __construct($dbhost,$dbuser,$dbpass,$dbname,$dbport) {
		mysqli_report(MYSQLI_REPORT_OFF);
		$this->mysqli = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
		$this->mysqli->set_charset('utf8mb4');
	}
	public function close() {
		$this->mysqli->close();
	}
}
if (strtolower($_SERVER['REQUEST_METHOD']) == "post") {
	$sqms = [];
	foreach($dbs as $dbn) {
		$db = new DB($dbhost,$dbuser,$dbpass,$dbn,$dbport);
		$res = $db->mysqli->query("SELECT * FROM land");
		foreach ($res as $row) {
			$sqms[] = ['pid' => $row['UUID'],
			"sqm" => $row['Area'], 
			'uuid' => $row['OwnerUUID'], 
			'region' => $row['RegionUUID'], 
			'parcel' => $row['Name']];
		}
		$db->close();
	}
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(['sqm' => $sqms]);
}
if (strtolower($_SERVER['REQUEST_METHOD']) == "get") {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(['status' => 'moo']);
}