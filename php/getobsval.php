<?php

/* Create a JSON object with all observation values */

include_once 'dbconnector.php';

function getValues(){
	if (isset($_POST['foi']) AND isset($_POST['startdate']) AND isset($_POST['enddate'])){
	// If argument is set, use it (temperature or humidity
	$numarg = func_num_args();
	if ($numarg == 1){
		$obs = array(func_get_arg(0));
	} else {
		// else use the observation
		if (isset($_POST['observation'])){
			$obs = $_POST['observation'];
		}
	}
	
$outliers = $_POST['outliers'];
$numargs = count($obs);
$rows = array();
$cols = array();
// Get all time_stamp of all observations
$timestamp = getTimeStamp($_POST['foi'], $_POST['startdate'], $_POST['enddate']);

//1, 2 or 3 additional parameters?
switch ($numargs) {
	// 1 additional parameter - obs1
	case 1:
		$obs1 = $obs ['0'];
		// Get the observation values
		if ($outliers == 'yes'){
		$rows = getObservationValues($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1);
		}
		else  {
			$rows = getObservationValuesNo($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1);
		}
		// Set the columns
		$cols = array(
				array('label' => 'date','type' => 'string'),
				array('label' => $obs1,'type' => 'number'));
		
		// required for $rows - see below
		$j = 0;	

		//if available insert the values
		for($i = 0; $i < count($timestamp); $i++){
			$ttime = $timestamp[$i];
			$temp = array();
			$temp[] = array('v' => $ttime['time_stamp']);
			
			if ($j < count($rows)){
				$row = $rows[$j];
								
					if ($row['time_stamp'] == $ttime['time_stamp']){
						$temp[] = array('v' => $row['numeric_value']);
						$j++;	
						}
					// row['time_stamp'] != ttime['time_stamp']
					else {
						// if j = 0, value is null
						if ($j == 0){
							$temp[] = array('v' => null);
						} else {
							// get the last value
						$temp2 = end($trows);
						$temp[] = array('v' => $temp2['c']['1']['v']);
						}
					}
										
				}
				// j >= count($rows)
				else {
					// if j = 0, value is null
					if ($j == 0){
						$temp[] = array('v' => null);
					} else {
						// get the last value
					$temp2 = end($trows);
					$temp[] = array('v' => $temp2['c']['1']['v']);
					}
				}
			// save the result to $trows and delete $temp
			$trows[] = array('c' => $temp);
			unset($temp);
		}
		//to JSON and return $table			
		$table = arrayToJSON($cols, $trows);
		echo $table;
		break;
		
	// 2 additional parameter - obs1, obs2	
	case 2:
		$obs1 = $obs['0'];
		$obs2 = $obs['1'];
		//Get the observation values
		if ($outliers == 'yes'){
		$rows = getObservationValues($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2);
		} else {
			$rows = getObservationValuesNo($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2);
		}
		
		//Set the columns
		$cols = array(
				array('label' => 'date','type' => 'string'),
				array('label' => $obs1, 'type' => 'number'),
				array('label' => $obs2, 'type' => 'number'));
		
		//required for $rows - see below
		$j = 0;
		
		//if available insert the values
		for ($i = 0; $i < count($timestamp); $i++){
			$ttime = $timestamp[$i];
			$temp = array();
			$temp[] = array('v' => $ttime['time_stamp']);		
				
			if ($j < count($rows)){
				$row = $rows[$j];
				
					if ($ttime['time_stamp'] == $row['time_stamp']){
//case 2 - obs1
						if ($row['offering_id'] == $obs1 ){
							$temp[] = array('v' => $row['numeric_value']);
							$j++;
							
							if ($j <count($rows)){
							$row = $rows[$j];
							}
						}
						//if not -> get the last value
						else {
							// if j = 0, new value is null
							if ($j == 0){
								$temp[] = array('v' => null);
								// get the last obs1_value
							} else {
							// if exists, get the last obs1_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['1']['v']);
								}
							}									
						}
//case 2 - obs2
						if ($row['offering_id'] == $obs2 ){
							$temp[] = array('v' => $row['numeric_value']);
							$j++;
						}
						//if not -> get the last value
						else {
							// if j = 0, new value is null
							if ($j == 0){
								$temp[] = array('v' => null);
							} else {
							// if exists, get the last obs2_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['2']['v']);
									}
								}									
							}
						}
					//if j > count($rows)
					else {
						// if j = 0, new values are null
						if ($j == 0){
						$temp[] = array('v' => null);
						$temp[] = array('v' => null);
						} else {
							// get the last obs1_ and obs2_values
							$temp2 = end($trows);
							$temp[] = array('v' => $temp2['c']['1']['v']);
							$temp[] = array('v' => $temp2['c']['2']['v']);
						}
					}
			}
			else {
				// if j = 0, new values are null
				if ($j == 0){
					$temp[] = array('v' => null);
					$temp[] = array('v' => null);
				} else {
					// get the last obs1_ and obs2_values
					$temp2 = end($trows);
					$temp[] = array('v' => $temp2['c']['1']['v']);
					$temp[] = array('v' => $temp2['c']['2']['v']);
				}
			}

			//save the result to $trows and delete $temp
			$trows[] = array('c' => $temp);
			unset($temp);
			}
		//to JSON and return table
		$table = arrayToJSON($cols, $trows);
		echo $table;
		break;
		
// 3 additional parameters - obs1, obs2, obs3	
	case 3:
		$obs1 = $obs['0'];
		$obs2 = $obs['1'];
		$obs3 = $obs['2'];
		//Get the observation values
		if ($outliers == 'yes'){
		$rows = getObservationValues($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2, $obs3);
		} else {
			$rows = getObservationValuesNo($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2, $obs3);
		}
		//Set the columns
		$cols = array(
				array('label' => 'date','type' => 'string'),
				array('label' => $obs1, 'type' => 'number'),
				array('label' => $obs2, 'type' => 'number'),
				array('label' => $obs3, 'type' => 'number'));

		//required for $rows - see below
		$j = 0;
		
		//if available insert the values
		for ($i = 0; $i < count($timestamp); $i++){
			$ttime = $timestamp[$i];
			$temp = array();
			$temp[] = array('v' => $ttime['time_stamp']);
			
			if ($j < count($rows)){			
				$row = $rows[$j];
				
				if ($ttime['time_stamp'] == $row['time_stamp']){
//case 3 - obs1			
					if ($row['offering_id'] == $obs1 ){
						$temp[] = array('v' => $row['numeric_value']);
						$j++;
						
						if ($j < count($rows)){
						$row = $rows[$j];
						}
					}
					//if not -> get the last value
					else {
						// if j = 0, value is null
						if ($j == 0){
							$temp[] = array('v' => null);							
						}
						else {
							// if exists, get the last obs1_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['1']['v']);
								}
						}
					}
//case 3 - obs2					
					if ($row['offering_id'] == $obs2 ){
						$temp[] = array('v' => $row['numeric_value']);
						$j++;
						
						if ($j < count($rows)){
						$row = $rows[$j];
						}
					}
					//if not -> get the last value
					else {
						// if j = 0, value is null
						if ($j == 0){
							$temp[] = array('v' => null);							
						}
						else {
							// if exists, get the last obs2_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['2']['v']);
								}	
							}
						}					
// case 3 - obs3
					if ($row['offering_id'] == $obs3 ){
						$temp[] = array('v' => $row['numeric_value']);
						$j++;
					}
					//if not -> null
									//if not -> get the last value
					else {
						// if j = 0, value is null
						if ($j == 0){
							$temp[] = array('v' => null);							
						}
						else {
							// if exists, get the last obs1_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['3']['v']);
								}
							}
						}
					}	
				//if j > count($rows)
				else {
					if ($j == 0){
					$temp[] = array('v' => null);
					$temp[] = array('v' => null);
					$temp[] = array('v' => null);
					}
					else {
						$temp2 = end($trows);
						$temp[] = array('v' => $temp2['c']['1']['v']);
						$temp[] = array('v' => $temp2['c']['2']['v']);
						$temp[] = array('v' => $temp2['c']['3']['v']);
					}
				}
			}
			
			else {
				if ($j == 0){
					$temp[] = array('v' => null);
					$temp[] = array('v' => null);
					$temp[] = array('v' => null);
					}
				else {
					$temp2 = end($trows);
					$temp[] = array('v' => $temp2['c']['1']['v']);
					$temp[] = array('v' => $temp2['c']['2']['v']);
					$temp[] = array('v' => $temp2['c']['3']['v']);
				}
			}				
			//save the result to $trows and delete $temp					
			$trows[] = array('c' => $temp);
			unset($temp);
		}
			
		//to JSON and return table		
		$table = arrayToJSON($cols, $trows);
		echo $table;		
		break;

// 4 additional parameters - obs1, obs2, obs3, obs4	
		case 4:
			$obs1 = $obs['0'];
			$obs2 = $obs['1'];
			$obs3 = $obs['2'];
			$obs4 = $obs['3'];
			
			//Get the observation values
			if ($outliers == 'yes'){
				$rows = getObservationValues($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2, $obs3, $obs4);
			} else {
				$rows = getObservationValuesNo($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2, $obs3, $obs4);
			}
			//Set the columns
			$cols = array(
					array('label' => 'date','type' => 'string'),
					array('label' => $obs1, 'type' => 'number'),
					array('label' => $obs2, 'type' => 'number'),
					array('label' => $obs3, 'type' => 'number'),
					array('label' => $obs4, 'type' => 'number'));
			//required for $rows - see below
			$j = 0;
			
			//if available insert the values
			for ($i = 0; $i < count($timestamp); $i++){
				$ttime = $timestamp[$i];
				$temp = array();
				$temp[] = array('v' => $ttime['time_stamp']);
					
				if ($j < count($rows)){
					$row = $rows[$j];
			
					if ($ttime['time_stamp'] == $row['time_stamp']){
// case 4 - obs1							
						if ($row['offering_id'] == $obs1 ){
							$temp[] = array('v' => $row['numeric_value']);
							$j++;
			
							if ($j < count($rows)){
								$row = $rows[$j];
							}
						}
						//if not -> get the last value
						else {
							// if j = 0, value is null
							if ($j == 0){
								$temp[] = array('v' => null);
							}
							else {
								// if exists, get the last obs1_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['1']['v']);
								}
							}
						}
//case 4 - obs2							
						if ($row['offering_id'] == $obs2 ){
							$temp[] = array('v' => $row['numeric_value']);
							$j++;
			
							if ($j < count($rows)){
								$row = $rows[$j];
							}
						}
						//if not -> get the last value
						else {
							// if j = 0, value is null
							if ($j == 0){
								$temp[] = array('v' => null);
							}
							else {
								// if exists, get the last obs2_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['2']['v']);
								}
							}
						}
//case 4 - obs3
						if ($row['offering_id'] == $obs3 ){
							$temp[] = array('v' => $row['numeric_value']);
							$j++;
							if ($j < count($rows)){
								$row = $rows[$j];
							}
						}
						//if not -> get the last value
						else {
							// if j = 0, value is null
							if ($j == 0){
								$temp[] = array('v' => null);
							}
							else {
								// if exists, get the last obs3_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['3']['v']);
								}
							}
						}
//case 4 - obs4
						if ($row['offering_id'] == $obs4 ){
							$temp[] = array('v' => $row['numeric_value']);
							$j++;
							
						}

						//if not -> get the last value
						else {
							// if j = 0, value is null
							if ($j == 0){
								$temp[] = array('v' => null);
							}
							else {
								// if exists, get the last obs4_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['4']['v']);
								}
							}
						}
						
					}
					//if j > count($rows)
					else {
						if ($j == 0){
							$temp[] = array('v' => null);
							$temp[] = array('v' => null);
							$temp[] = array('v' => null);
							$temp[] = array('v' => null);
						}
						else {
							$temp2 = end($trows);
							$temp[] = array('v' => $temp2['c']['1']['v']);
							$temp[] = array('v' => $temp2['c']['2']['v']);
							$temp[] = array('v' => $temp2['c']['3']['v']);
							$temp[] = array('v' => $temp2['c']['4']['v']);
						}
					}
				}
					
				else {
					if ($j == 0){
						$temp[] = array('v' => null);
						$temp[] = array('v' => null);
						$temp[] = array('v' => null);
						$temp[] = array('v' => null);
					}
					else {
						$temp2 = end($trows);
						$temp[] = array('v' => $temp2['c']['1']['v']);
						$temp[] = array('v' => $temp2['c']['2']['v']);
						$temp[] = array('v' => $temp2['c']['3']['v']);
						$temp[] = array('v' => $temp2['c']['4']['v']);
					}
				}
				//save the result to $trows and delete $temp
				$trows[] = array('c' => $temp);
				unset($temp);
			}
				
			//to JSON and return table
			$table = arrayToJSON($cols, $trows);
			echo $table;
			break;

// 5 additional parameters - obs1, obs2, obs3, obs4, obs5
			case 5:
				$obs1 = $obs['0'];
				$obs2 = $obs['1'];
				$obs3 = $obs['2'];
				$obs4 = $obs['3'];
				$obs5 = $obs['4'];
					
				//Get the observation values
				if ($outliers == 'yes'){
					$rows = getObservationValues($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2, $obs3, $obs4, $obs5);
				} else {
					$rows = getObservationValuesNo($_POST['foi'], $_POST['startdate'], $_POST['enddate'], $obs1, $obs2, $obs3, $obs4, $obs5);
				}
				//Set the columns
				$cols = array(
						array('label' => 'date','type' => 'string'),
						array('label' => $obs1, 'type' => 'number'),
						array('label' => $obs2, 'type' => 'number'),
						array('label' => $obs3, 'type' => 'number'),
						array('label' => $obs4, 'type' => 'number'),
						array('label' => $obs5, 'type' => 'number'));
				//required for $rows - see below
				$j = 0;
					
				//if available insert the values
				for ($i = 0; $i < count($timestamp); $i++){
					$ttime = $timestamp[$i];
					$temp = array();
					$temp[] = array('v' => $ttime['time_stamp']);
						
					if ($j < count($rows)){
						$row = $rows[$j];
							
						if ($ttime['time_stamp'] == $row['time_stamp']){
// case 5 - obs1
							if ($row['offering_id'] == $obs1 ){
								$temp[] = array('v' => $row['numeric_value']);
								$j++;
									
								if ($j < count($rows)){
									$row = $rows[$j];
								}
							}
							//if not -> get the last value
							else {
								// if j = 0, value is null
								if ($j == 0){
									$temp[] = array('v' => null);
								}
								else {
							// if exists, get the last obs1_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['1']['v']);
								}
							}
						}
//case 5 - obs2
							if ($row['offering_id'] == $obs2 ){
								$temp[] = array('v' => $row['numeric_value']);
								$j++;
									
								if ($j < count($rows)){
									$row = $rows[$j];
								}
							}
							//if not -> get the last value
							else {
								// if j = 0, value is null
								if ($j == 0){
									$temp[] = array('v' => null);
								}
								else {
							// if exists, get the last obs2_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['2']['v']);
									}
								}
							}
//case 5 - obs3
							if ($row['offering_id'] == $obs3 ){
								$temp[] = array('v' => $row['numeric_value']);
								$j++;
								if ($j < count($rows)){
									$row = $rows[$j];
								}
							}
							//if not -> get the last value
							else {
								// if j = 0, value is null
								if ($j == 0){
									$temp[] = array('v' => null);
								}
								else {
							// if exists, get the last obs1_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['3']['v']);
									}
								}
							}
//case 5 - obs4
							if ($row['offering_id'] == $obs4 ){
								$temp[] = array('v' => $row['numeric_value']);
								$j++;
								if ($j < count($rows)){
									$row = $rows[$j];
								}									
							}
			
							//if not -> get the last value
							else {
								// if j = 0, value is null
								if ($j == 0){
									$temp[] = array('v' => null);
								}
								else {
							// if exists, get the last obs4_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['4']['v']);
									}
								}
							}

//case 5 - obs5
							if ($row['offering_id'] == $obs5 ){
								$temp[] = array('v' => $row['numeric_value']);
								$j++;	
							}
								
							//if not -> get the last value
							else {
								// if j = 0, value is null
								if ($j == 0){
									$temp[] = array('v' => null);
								}
								else {
							// if exists, get the last obs1_value
								if(isset($trows) == false){
									$temp[] = array('v' => null);
								} else{
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['5']['v']);
									}
								}
							}
								
						}
						//if j > count($rows)
						else {
							if ($j == 0){
								$temp[] = array('v' => null);
								$temp[] = array('v' => null);
								$temp[] = array('v' => null);
								$temp[] = array('v' => null);
								$temp[] = array('v' => null);
							}
							else {
								$temp2 = end($trows);
								$temp[] = array('v' => $temp2['c']['1']['v']);
								$temp[] = array('v' => $temp2['c']['2']['v']);
								$temp[] = array('v' => $temp2['c']['3']['v']);
								$temp[] = array('v' => $temp2['c']['4']['v']);
								$temp[] = array('v' => $temp2['c']['5']['v']);
							}
						}
					}
						
					else {
						if ($j == 0){
							$temp[] = array('v' => null);
							$temp[] = array('v' => null);
							$temp[] = array('v' => null);
							$temp[] = array('v' => null);
							$temp[] = array('v' => null);
						}
						else {
							$temp2 = end($trows);
							$temp[] = array('v' => $temp2['c']['1']['v']);
							$temp[] = array('v' => $temp2['c']['2']['v']);
							$temp[] = array('v' => $temp2['c']['3']['v']);
							$temp[] = array('v' => $temp2['c']['4']['v']);
							$temp[] = array('v' => $temp2['c']['5']['v']);
						}
					}
					//save the result to $trows and delete $temp
					$trows[] = array('c' => $temp);
					unset($temp);
				}
			
				//to JSON and return table
				$table = arrayToJSON($cols, $trows);
				echo $table;
				break;

}

unset($cols);
unset($rows);
unset($table);
unset($row);
unset($trows);
unset($timestamp);
unset($ttime);
unset($obs);
unset($outliers);
	}
	else {
	echo '""';
	}
}
?>