<?php

require_once 'dbconnector.php';

if (isset($_POST['observation'])){
$obs = $_POST['observation'];
$numargs = count($obs);
switch ($numargs) {
	case 1:
		$obs1 = $obs ['0'];
		getObservationValues($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1);
		break;
	case 2:
		$obs1 = $obs['0'];
		$obs2 = $obs['1'];
		getObservationValues($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2);
		break;
	case 3:
		$obs1 = $obs['0'];
		$obs2 = $obs['1'];
		$obs3 = $obs['2'];
		getObservationValues($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2, $obs3);
	}
}

?>