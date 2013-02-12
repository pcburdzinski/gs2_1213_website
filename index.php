<?php

include('dbconnector.php');

$conn = connect();


//get DB-Name:
$dbname = getDBname($conn);
echo <<<HTML
<div>$dbname</div>
HTML;

//query example:
$result = pg_query('SELECT id, name FROM sensor');
echo "<table>\n";
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";

?>