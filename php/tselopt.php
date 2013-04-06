<?php 

/* Creates the option list - diagram version */

require_once ('dbconnector.php');

echo '<select id = "foi"
			name = "foi" onchange ="changeForm(this.value)">';
$rows = getFoi();
for ($i = 0; $i < count($rows); $i++){
	$row = $rows[$i];
		echo '<option value = "'.$row['feature_of_interest_id'].'">'.$row['feature_of_interest_name'].'</option>';
}
echo'</select>'
?>