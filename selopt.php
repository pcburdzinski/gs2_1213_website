<?php 

	include ('dbconnector.php');
	
	echo '<select id = "foi"
			name = "foi">';
	$rows = getFoi();
	for ($i = 0; $i < count($rows); $i++){
		$row = $rows[$i];
		echo '<option value = "'.$row['feature_of_interest_id'].'">'.$row['feature_of_interest_name'].'</option>';
	}
	echo'</select>'
?>