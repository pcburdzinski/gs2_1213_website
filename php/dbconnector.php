<?php 

include 'config.php';

$HOST = $db["host"];
$PORT = $db["port"];
$USER = $db["user"];
$DBNAME = $db["name"];
$PASSWORD = $db["password"];

$conn = pg_connect("host=$HOST port=$PORT dbname=$DBNAME user=$USER password=$PASSWORD");

/*Get the Coordinates of one or all feature_of_interest */
function getCoords(){
	global $conn;
	$numargs = func_num_args();
	/* all foi */
	if ($numargs == 0){
		$result= pg_query($conn,"SELECT ST_X(geom), ST_Y(geom) FROM feature_of_interest");
		return pg_fetch_all($result);
	}
	/* one foi */
	else
	{
		$foi_id = func_get_arg(0);
		$result = pg_query($conn,"SELECT ST_X(geom), ST_Y(geom) 
				FROM feature_of_interest 
				WHERE feature_of_interest_id = '$foi_id' ");
		return pg_fetch_all($result);
	}
}

/* Get the name and the id of all feature_of_interest */
function getFoi(){
	global $conn;
	$result = pg_query($conn, "SELECT feature_of_interest_name, feature_of_interest_id
								FROM feature_of_interest
								ORDER BY feature_of_interest_name ASC");
	return pg_fetch_all($result);
}

/* Fliegt wahrscheinlich raus! ... oder nicht? */
function getFoiID($lat, $long){
	global $conn;
	if (isset ($lat) AND isset($long)){
			$result = pg_query($conn,"SELECT feature_of_interest_id 
					FROM feature_of_interest 
					WHERE ST_X(geom) = '$lat' AND ST_Y(geom) = '$long'");
			return pg_fetch_all($result);
	}
}
 

/* Get all observation values of one feature of interest */
function getAllObservationValues($foi_id, $start_date, $end_date){
	global $conn;
	if (isset ($foi_id) AND isset($start_date) AND isset($end_date)){
			$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, phenomenon_description, numeric_value, unit
					FROM observation NATURAL JOIN phenomenon
					WHERE (feature_of_interest_id = '$foi_id') AND (time_stamp >= '$start_date'::date))
						INTERSECT
					(SELECT time_stamp :: timestamp without time zone, phenomenon_description, numeric_value, unit
					FROM observation NATURAL JOIN phenomenon
					WHERE (feature_of_interest_id = '$foi_id') AND  (time_stamp <= '$end_date'::date))");	
			return pg_fetch_all($result);
	}	
}

/* Get all observation values of one feature of interest without outliers */
function getAllObservationValuesNO($foi_id, $start_date, $end_date){
	global $conn;
	if (isset ($foi_id) AND isset($start_date) AND isset($end_date)){
		$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, phenomenon_description, numeric_value, unit
				FROM observation NATURAL JOIN phenomenon NATURAL JOIN quality
				WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp >= '$start_date'::date))
				INTERSECT
				(SELECT time_stamp :: timestamp without time zone, phenomenon_description, numeric_value, unit
				FROM observation NATURAL JOIN phenomenon NATURAL JOIN quality
				WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND  (time_stamp <= '$end_date'::date))");
		return pg_fetch_all($result);
	}
}

/* Get the observation values of one feature of interest */
function getObservationValues($foi_id, $start_date, $end_date){
	global $conn;
	if (isset ($foi_id) AND isset($start_date) AND isset($end_date)){
		$numargs = func_num_args();
		switch($numargs){
			case 4:
				$offering1 = func_get_arg(3);
				$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND (time_stamp >= '$start_date'::date) 
						AND (offering_id = '$offering1'))
						INTERSECT
						(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND  (time_stamp <= '$end_date'::date) 
						AND (offering_id = '$offering1')) ORDER BY time_stamp ASC");
				return pg_fetch_all($result);
				break;
			
			case 5:
				$offering1 = func_get_arg(3);
				$offering2 = func_get_arg(4);
				$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id')  AND (time_stamp >= '$start_date'::date) 
						AND ((offering_id = '$offering1') OR (offering_id = '$offering2')))
						INTERSECT
						(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND  (time_stamp <= '$end_date'::date) 
						AND ((offering_id = '$offering1') OR (offering_id = '$offering2'))) ORDER BY time_stamp, offering_id ASC");
				return pg_fetch_all($result);
				break;
				
			case 6:
				$offering1 = func_get_arg(3);
				$offering2 = func_get_arg(4);
				$offering3 = func_get_arg(5);
				$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND (time_stamp >= '$start_date'::date) 
						AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3')))
						INTERSECT
						(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND  (time_stamp <= '$end_date'::date) 
						AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3'))) ORDER BY time_stamp, offering_id ASC");
				return pg_fetch_all($result);
				break;

			case 7:
				$offering1 = func_get_arg(3);
				$offering2 = func_get_arg(4);
				$offering3 = func_get_arg(5);
				$offering4 = func_get_arg(6);
				$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND (time_stamp >= '$start_date'::date)
						AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3') OR (offering_id = '$offering4')))
						INTERSECT
						(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND  (time_stamp <= '$end_date'::date)
						AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3') OR (offering_id = '$offering4'))) ORDER BY time_stamp, offering_id ASC");
				return pg_fetch_all($result);
				break;				
				
			case 8:
				$offering1 = func_get_arg(3);
				$offering2 = func_get_arg(4);
				$offering3 = func_get_arg(5);
				$offering4 = func_get_arg(6);
				$offering5 = func_get_arg(7);
				$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND (time_stamp >= '$start_date'::date) 
						AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3') OR (offering_id = '$offering4') OR (offering_id = '$offering5')))
						INTERSECT
						(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation
						WHERE (feature_of_interest_id = '$foi_id') AND  (time_stamp <= '$end_date'::date) 
						AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3') OR (offering_id = '$offering4') OR (offering_id = '$offering5'))) ORDER BY time_stamp, offering_id ASC");
				return pg_fetch_all($result);
				break;
		}
	}
}

/* Get the observation of one feature of interest with outliers = yes => show only outlier-values */
function getObservationValuesNo($foi_id, $start_date, $end_date){
	global $conn;
	if (isset ($foi_id) AND isset($start_date) AND isset($end_date)){
		$numargs = func_num_args();
		switch($numargs){
			case 4:
				$offering1 = func_get_arg(3);
				$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation NATURAL JOIN quality
						WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp >= '$start_date'::date)
						AND (offering_id = '$offering1'))
						INTERSECT
						(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation NATURAL JOIN quality
						WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp <= '$end_date'::date)
						AND (offering_id = '$offering1')) ORDER BY time_stamp ASC");
				return pg_fetch_all($result);
				break;
					
			case 5:
				$offering1 = func_get_arg(3);
				$offering2 = func_get_arg(4);
				$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation NATURAL JOIN quality
						WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp >= '$start_date'::date)
						AND ((offering_id = '$offering1') OR (offering_id = '$offering2')))
						INTERSECT
						(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation NATURAL JOIN quality
						WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp <= '$end_date'::date)
						AND ((offering_id = '$offering1') OR (offering_id = '$offering2'))) ORDER BY time_stamp, offering_id ASC");
				return pg_fetch_all($result);
				break;

			case 6:
				$offering1 = func_get_arg(3);
				$offering2 = func_get_arg(4);
				$offering3 = func_get_arg(5);
				$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation NATURAL JOIN quality
						WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp >= '$start_date'::date)
						AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3')))
						INTERSECT
						(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
						FROM observation NATURAL JOIN quality
						WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp <= '$end_date'::date)
						AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3'))) ORDER BY time_stamp, offering_id ASC");
				return pg_fetch_all($result);
				break;
				
				case 7:
					$offering1 = func_get_arg(3);
					$offering2 = func_get_arg(4);
					$offering3 = func_get_arg(5);
					$offering4 = func_get_arg(6);
					$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
							FROM observation NATURAL JOIN quality
							WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp >= '$start_date'::date)
							AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3') OR (offering_id = '$offering4')))
							INTERSECT
							(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
							FROM observation NATURAL JOIN quality
							WHERE (feature_of_interest_id = '$foi_id') AND  (quality_value='no') AND (time_stamp <= '$end_date'::date)
							AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3') OR (offering_id = '$offering4'))) ORDER BY time_stamp, offering_id ASC");
					return pg_fetch_all($result);
					break;
				
				case 8:
					$offering1 = func_get_arg(3);
					$offering2 = func_get_arg(4);
					$offering3 = func_get_arg(5);
					$offering4 = func_get_arg(6);
					$offering5 = func_get_arg(7);
					$result = pg_query($conn, "(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
							FROM observation NATURAL JOIN quality
							WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp >= '$start_date'::date)
							AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3') OR (offering_id = '$offering4') OR (offering_id = '$offering5')))
							INTERSECT
							(SELECT time_stamp :: timestamp without time zone, offering_id, numeric_value
							FROM observation NATURAL JOIN quality
							WHERE (feature_of_interest_id = '$foi_id') AND (quality_value='no') AND (time_stamp <= '$end_date'::date)
							AND ((offering_id = '$offering1')  OR (offering_id = '$offering2') OR (offering_id = '$offering3') OR (offering_id = '$offering4') OR (offering_id = '$offering5'))) ORDER BY time_stamp, offering_id ASC");
					return pg_fetch_all($result);
					break;
		}
	}
}

/* Get the last observation values of one feature_of_interest */
function getLastObservationValues($foi_id){
	global $conn;
	if (isset ($foi_id)){
		$result = pg_query($conn, "SELECT time_stamp, offering_id, numeric_value, unit 
				FROM observation NATURAL JOIN phenomenon
				WHERE (feature_of_interest_id = '$foi_id') AND (time_stamp = (
															SELECT max(time_stamp :: timestamp without time zone) 
															FROM observation 
															WHERE feature_of_interest_id ='$foi_id')) ORDER BY offering_id");
		return pg_fetch_all($result);
	}
}

/* Get the timestamp of one feature_of_interest */
function getTimeStamp($foi_id, $start_date, $end_date){
	global $conn;
	if (isset ($foi_id) AND isset ($start_date) AND isset($end_date)){
		$result = pg_query($conn, "SELECT distinct time_stamp :: timestamp without time zone
									FROM observation
									WHERE (feature_of_interest_id ='$foi_id') AND (time_stamp >= '$start_date'::date) AND (time_stamp <= '$end_date'::date) ORDER BY time_stamp ASC");
		return pg_fetch_all($result);	
	}
}

function arrayToJSON($cols, $rows){
	$table = json_encode(array(
			'cols' => $cols,
			'rows' => $rows),
			JSON_NUMERIC_CHECK);
	return $table;
}

//----------------------------------------------------------------------------------------

/* Gibt die Anzahl der benötigten Spalten für die Tabelle zurück */

function getTableNumRows($foi_id, $start_date, $end_date){
	global $conn;
	if (isset ($foi_id) AND isset ($start_date) AND isset ($end_date)){
		$result = pg_query($conn, "SELECT distinct time_stamp
									FROM observation 
									WHERE feature_of_interest_id = '$foi_id' AND time_stamp >= '$start_date'::date AND time_stamp <= '$end_date'::date");
		$num = pg_num_rows($result);
	return $num;
	}
}

/* Gibt jegliche Zeitstempel aus im festgelegten Zeitintervall */
									
function getTableTimeStamp($foi_id, $start_date, $end_date){
	global $conn;
		$result1 = pg_query($conn, "SELECT distinct time_stamp 
									FROM observation 
									WHERE feature_of_interest_id = '$foi_id' AND time_stamp >= '$start_date'::date AND time_stamp <= '$end_date'::date ORDER BY time_stamp asc");
		return $result1;
}

/* Gibt den zu dem jeweiligen Messparameter den Wert und den Messpunkt aus */

function getTableOffering($foi_id, $start_date, $end_date, $offering_id){
	global $conn;
		$result2 = pg_query($conn, "SELECT time_stamp,numeric_value 
									FROM observation 
									WHERE feature_of_interest_id = '$foi_id' AND offering_id = '$offering_id' AND time_stamp >= '$start_date'::date AND time_stamp <= '$end_date'::date ORDER BY time_stamp asc");
		return $result2;
}

function getName(){
	global $conn;
	$result = pg_query($conn, "SELECT feature_of_interest_name
								FROM feature_of_interest 
								WHERE feature_of_interest_id != 'Weseler' AND feature_of_interest_id != 'Geist' order by feature_of_interest_id");
	return pg_fetch_all($result);
}

function getLanuvName(){
	global $conn;
	$result = pg_query($conn, "SELECT feature_of_interest_name 
								FROM feature_of_interest 
								WHERE feature_of_interest_id = 'Weseler' OR feature_of_interest_id = 'Geist' order by feature_of_interest_id");
	return pg_fetch_all($result);
}

/* Funktion, welche die IDs aller Air Quality Eggs ausgibt. */

function getFoiIdMap(){
	global $conn;
	$result = pg_query($conn, "SELECT feature_of_interest_id
								FROM feature_of_interest 
								WHERE feature_of_interest_id != 'Weseler' AND feature_of_interest_id != 'Geist' order by feature_of_interest_id");
	return pg_fetch_all($result);
}

/* Funktion, welche zu den Air Quality Eggs den letzten gemessenen Zeitpunkt ausgibt. */

function getFoiIdMap2(){
	global $conn;
	$result = pg_query($conn, "SELECT feature_of_interest_id,max(time_stamp) 
								FROM observation natural inner join feature_of_interest 
								WHERE feature_of_interest_id != 'Geist' AND feature_of_interest_id != 'Weseler' group by feature_of_interest_id");
	return pg_fetch_all($result);
}

/* Funktion, welche die IDs der Lanuv Messstationen ausgibt. */

function getLanuvFoiId(){
	global $conn;
	$result = pg_query($conn, "SELECT feature_of_interest_id
								FROM feature_of_interest 
								WHERE feature_of_interest_id = 'Weseler' OR feature_of_interest_id = 'Geist' order by feature_of_interest_id");
	return pg_fetch_all($result);
}

/* Funktion, welche zu den Lanuv Messstationen den letzten gemessenen Zeitpunkt ausgibt. */

function getLanuvFoiId2(){
	global $conn;
	$result = pg_query($conn, "SELECT feature_of_interest_id,max(time_stamp) 
								FROM observation natural inner join feature_of_interest 
								WHERE feature_of_interest_id = 'Geist' OR feature_of_interest_id = 'Weseler' group by feature_of_interest_id order by feature_of_interest_id");
	return pg_fetch_all($result);
}

/* Da ein komplette gefülltes Array benötigt wird, um den Air Quality Eggs auf der Karte die richtigen Werte zuzuweisen,
	wird ein jeweiliges Array befüllt. Dafür wird einmal ein noch teil-befülltes Array und ein Array verwandt, welches alle ID's 
	der Air Quality Eggs beinhaltet. Daraufhin werden die beiden Arrays immer verglichen, ob das AQE bereits in dem $zufüllende
	Array vorhanden ist. Ist dies nicht der Fall, so wird die ID des AQE eingetragen. Ebenfalls wird der Time_Stamp kontrolliert.
	Ist keiner vorhanden, so wird für den maximalen Time_Stamp ein "_" eingetragen.
	Anschließend wird das befüllte Array zurückgegeben. */

function umwandeln(){
	$ausgabe = getFoiIdMap();
	$zufüllende = getFoiIdMap2();
	$i = 0;
	while ($i <= count($ausgabe)-1){ 		// Die Anzahl der Eier
	if ($zufüllende[$i]['feature_of_interest_id'] == $ausgabe[$i]['feature_of_interest_id']);
		else {
				$zufüllende[$i+1]['feature_of_interest_id'] = $zufüllende[$i]['feature_of_interest_id'];
				$zufüllende[$i+1]['max'] = $zufüllende[$i]['max'];
				$zufüllende[$i]['feature_of_interest_id'] = "_";
				$zufüllende[$i]['max'] = "_";
				}
				$i++;
	}
	return $zufüllende;
}

/* Analog zum Teil obigen Teil für die Lanuv Stationen. */

function LANUVumwandeln(){
	$ausgabe = getLanuvFoiId();
	$zufüllende = getLanuvFoiId2();
	$j = 0;
	while ($j <= 1){
	if ($zufüllende[$j]['feature_of_interest_id'] == $ausgabe[$j]['feature_of_interest_id']);
		else {
				$zufüllende[$j+1]['feature_of_interest_id'] = $zufüllende[$j]['feature_of_interest_id'];
				$zufüllende[$j+1]['max'] = $zufüllende[$j]['max'];
				$zufüllende[$j]['feature_of_interest_id'] = "_";
				$zufüllende[$j]['max'] = "_";
				}
				$j++;
	}
	return $zufüllende;
}

function getEggCoords(){
	global $conn;
	$numargs = func_num_args();
	/* all foi */
	if ($numargs == 0){
		$result = pg_query($conn,"SELECT ST_X(geom), ST_Y(geom) FROM feature_of_interest WHERE feature_of_interest_id != 'Weseler' AND feature_of_interest_id != 'Geist' order by feature_of_interest_id");
		return pg_fetch_all($result);
	}
}

function getLanuvCoords(){
	global $conn;
	$numargs = func_num_args();
	/* all foi */
	if ($numargs == 0){
		$result = pg_query($conn,"SELECT ST_X(geom), ST_Y(geom) FROM feature_of_interest WHERE feature_of_interest_id = 'Weseler' OR feature_of_interest_id = 'Geist' order by feature_of_interest_id");
		return pg_fetch_all($result);
	}
}

/* Funktion um den aktuellsten Messwert zu erhalten. Dabei wird zunächst ein Array befüllt (siehe umwandeln()) und 2 Variablen definiert:
	$gegenFOI = die gegenwärtige ID
	$gegenTS = der gegenwärtige TimeStamp
	Daraufhin wird gecheckt, ob überhaupt etwas in dem aktuellen Arrayelement enthalten ist.
	Ist etwas enthalten wird die Abfrage durchgeführt. Existiert kein Messwert zum jeweiligen Zeitpunkt, so wird in das Popup auf der Karte
	ein "-" eingetragen. */

function getLatestOffering($offering_id){
	global $conn;
		$transform = umwandeln();
		$i = 0;
		while ($i < count($transform)) {
		$gegenFOI = $transform[$i]['feature_of_interest_id'];
		$gegenTS = $transform[$i]['max'];
			if ($gegenFOI == "_" or $gegenTS == "_");
			else {
				$result = pg_query($conn, "SELECT numeric_value
									FROM observation
									WHERE time_stamp = '$gegenTS' AND feature_of_interest_id = '$gegenFOI' AND offering_id = '$offering_id'");
				if (pg_result($result,0,"numeric_value") == false) {$ActualResult = '-';}
				else {$ActualResult = pg_result($result,0,"numeric_value");}
				$TempArray[$i]['feature_of_interest_id'] = $gegenFOI;
				$TempArray[$i]['numeric_value'] = $ActualResult;
			}
			$i++;
		}
		return $TempArray;
}

/* Analog zu oben */

function getLatestLanuvOffering($offering_id){
	global $conn;
		$transform = LANUVumwandeln();
		$i = 0;
		while ($i < count($transform)) {
		$gegenFOI = $transform[$i]['feature_of_interest_id'];
		$gegenTS = $transform[$i]['max'];
			if ($gegenFOI == "_" or $gegenTS == "_");
			else {
				$result = pg_query($conn, "SELECT numeric_value
									FROM observation
									WHERE time_stamp = '$gegenTS' AND feature_of_interest_id = '$gegenFOI' AND offering_id = '$offering_id'");
				if (pg_result($result,0,"numeric_value") == false) {$ActualResult = '-';}
				else {$ActualResult = pg_result($result,0,"numeric_value");}
				$NOArray[$i]['feature_of_interest_id'] = $gegenFOI;
				$NOArray[$i]['numeric_value'] = $ActualResult;
			}
			$i++;
		}
		return $NOArray;
}

/* Die folgenden Funktionen unterscheiden sich zu obigen nur dahingehend, dass sie die Ausreißertabelle aus der Datenbank miteinbeziehen. */

function getTableBerNumRows($foi_id, $start_date, $end_date){
	global $conn;
	if (isset ($foi_id) AND isset ($start_date) AND isset ($end_date)){
		$result = pg_query($conn, "SELECT distinct time_stamp 
									FROM observation NATURAL JOIN phenomenon NATURAL JOIN quality 
									WHERE (feature_of_interest_id = '$foi_id') AND (time_stamp >= '$start_date'::date) AND (time_stamp <= '$end_date'::date)");
		$num = pg_num_rows($result);
	return $num;
	}
}

function getTableBerTimeStamp($foi_id, $start_date, $end_date){
	global $conn;
		$result1 = pg_query($conn, "SELECT distinct time_stamp 
									FROM observation NATURAL JOIN phenomenon NATURAL JOIN quality 
									WHERE (feature_of_interest_id = '$foi_id') AND (time_stamp >= '$start_date'::date) AND (time_stamp <= '$end_date'::date) order by time_stamp asc");
		return $result1;
}

function getTableBerOffering($foi_id, $start_date, $end_date, $offering_id){
	global $conn;
		$result2 = pg_query($conn, "SELECT time_stamp,numeric_value, quality_value
									FROM observation NATURAL JOIN phenomenon NATURAL JOIN quality 
									WHERE (feature_of_interest_id = '$foi_id') AND (offering_id='$offering_id') AND (time_stamp >= '$start_date'::date) AND (time_stamp <= '$end_date'::date) order by time_stamp asc");
		return $result2;
}

function getTableLanuvBerOffering($foi_id, $start_date, $end_date, $offering_id){
	global $conn;
		$result = pg_query($conn, "SELECT numeric_value, quality_value, time_stamp
									FROM observation NATURAL JOIN phenomenon NATURAL JOIN quality
									WHERE feature_of_interest_id = '$foi_id' AND offering_id = '$offering_id' AND time_stamp >= '$start_date'::date AND time_stamp <= '$end_date'::date ORDER BY time_stamp asc");
		return $result;
}

?>