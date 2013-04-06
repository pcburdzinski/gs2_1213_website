<!DOCTYPE HTML>
<head>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
    <script src="https://www.google.com/jsapi"></script>
    <script src="../js/jquery-1.9.1.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
	<script src="../js/ger_dpicker.js"></script>
	<link rel="shortcut icon" href="../images/egg_v1.png">
	<link rel="stylesheet" type="text/css" href="../css/styles.css" />
 
 
	<script>
	$(function() {
		$( "#datepicker" ).datepicker( { dateFormat: 'yy-mm-dd' });
		$( "#datepicker").datepicker('setDate', '+0');
		$( "#datepicker2" ).datepicker( {dateFormat: 'yy-mm-dd' });
		$( "#datepicker2").datepicker('setDate', '+1');
	});
	</script> 
</head>
<body>
	<div id="wrapper">
		<div id="headerwrap">
			<div id="header">
				<img class="logo" src="../images/egg_logo.png" width=70 height=55 align="left">
				<p>SkyEagle<p>
			</div>
        </div>
		<div id="navigationwrap">
            <ul id="menu-bar">
				<li><a href="Home.php">Home</a></li>
				<li><a href="Karte.php">Karte</a></li>
				<li><a href="Diagramme.php">Diagramme</a></li>
				<li class="current"><a href="Tabelle.php">Tabelle</a></li>
				<li><a href="Hilfe.php">Hilfe</a></li>
				<li><a href="Impressum.php">Impressum</a></li>
			</ul>
		</div>
		<div id="contentwrap">
			<div id="content">
				<form action = "#" method ="post" name="form">
					<fieldset>
						<legend>Bitte w&auml;hlen Sie eine Messstation</legend>
						<p>
							<label>Messstation</label>
							<?php
								include('selopt.php');
							?>
						</p>
					</fieldset>
					<fieldset>
						<legend>Bitte w&auml;hlen Sie ein Zeitintervall</legend>
						<p>
							<label for = "datepicker">von:</label>
							<input type = "text" 
								id="datepicker"
								name = "startdate"
							/>	
							<label for = "datepicker2">bis:</label>
							<input type = "text"
								id="datepicker2"
								name = "enddate"
							/>	
						</p>
					</fieldset>
					<fieldset>
						<legend>Bitte w&auml;hlen Sie aus, ob die Tabelle Ausrei&szlig;er beinhalten soll oder nicht</legend>
						<p>
							<input type = "radio"
								name = "Ausreisser"
								value = "Unbereinigt"
								checked = 'checked'
							/>
							unbereinigte Werte
							<input type = "radio"
								name = "Ausreisser"
								value = "Bereinigt"
							/>
							bereinigte Werte
						</p>
					</fieldset>
					<fieldset>
						<p>
						<input class = "searchButton" type = "submit" value = "Tabelle anzeigen"/>
						</p>
					</fieldset>
				</form>
				<table border="0" cellspacing="10" cellpadding="0">
					<?php
						//ini_set( "display_errors", 0);
						include_once 'dbconnector.php';
						
						/* Part für die unbereinigte bzw. bereinigte Tabelle. Es wird gecheckt, ob ein Feature of Interest, ein Startdatum, ein Enddatum 
							und ein Radiobutton ausgewählt wurde */
							
						if (isset($_POST['foi']) AND isset($_POST['startdate']) AND isset($_POST['enddate']) AND isset($_POST["Ausreisser"])){
							$selected_radio = $_POST['Ausreisser'];
							
							/* Der Part für die unbereinigte Tabelle. Zunächst werden einige Variablen definiert und auf Werte
								aus der Datenbank gesetzt.
								$start: das ausgewählte Startdatum
								$end: das ausgewählte Enddatum
								$foi: die ausgewählte Messstation */	
							
							if ($selected_radio == 'Unbereinigt'){
								$start = $_POST['startdate'];
								$end = $_POST['enddate'];
								$foi = $_POST['foi'];
								$num = getTableNumRows($foi,$start,$end);			// Variable für die Tabellengröße
								$result1 = getTableTimeStamp($foi,$start,$end);
								
								/* Ausschließen der bestimmten Kombinationen, da manche Messwerte nur begrenzt und nicht bei jeder Messstation
									verfügbar sind. So ist z.B. SO2 nur bei der Lanuv-Station 'Geist' verfügbar */
									
								if ($foi != 'Weseler' AND $foi != 'Geist'){$result2 = getTableOffering($foi,$start,$end, 'TEMPERATURE');}
								if ($foi != 'Weseler' AND $foi != 'Geist'){$result3 = getTableOffering($foi,$start,$end, 'AIR_HUMIDITY');}
								if ($foi != 'Weseler' AND $foi != 'Geist'){$result4 = getTableOffering($foi,$start,$end, 'CO_CONCENTRATION');}
								$result5 = getTableOffering($foi,$start,$end, 'NO2_CONCENTRATION');
								if ($foi != 'Weseler'){$result6 = getTableOffering($foi,$start,$end, 'O3_CONCENTRATION');}
								if ($foi == 'Weseler' OR $foi == 'Geist'){$result7 = getTableOffering($foi,$start,$end, 'NO_CONCENTRATION');}
								if ($foi == 'Geist'){$result8 = getTableOffering($foi,$start,$end, 'SO2_CONCENTRATION');}
								if ($foi == 'Weseler' OR $foi == 'Geist'){$result9 = getTableOffering($foi,$start,$end, 'PM10_CONCENTRATION');}
								
								/* Die Tabelle für die Air Quality Eggs */ 
								
								if ($foi != 'Weseler' AND $foi != 'Geist'){	
					?>
					<tr>
						<th>Zeit</th>
						<th>Temperatur in &deg;C</th>
						<th>Luftfeuchte in %</th>
						<th>CO in ppm</th>
						<th>NO2 in ppm</th>
						<th>O3 in ppm</th>
					</tr>
					<?php
					
					/* Zeile für Zeile wird ausgelesen und anschließend in die erstellte Tabelle geschrieben.
						Dabei ist zu beachten, dass es vorkommt, dass Messwerte bei Messstationen fehlen können. 
						Aus diesem Grund wird kontrolliert, ob der aktuelle Zeitstempel gleich dem Zeitstempel des Messwertes entspricht.
						Dafür werden auch die "offS..." Variablen verwendet, welche einen Counter für den Messparameter darstellen.
						Damit keine Messwerte in der Tabelle übersprungen werden bzw. die ganze Abfrage "verrutscht", wird der aktuelle $i Wert
						mit dem Counter subtrahiert. Dadurch bleibt die time_stamp-Abfrage stets in der gleichen Zeile.
						Ist kein Wert vorhanden, so wird ein "-" in die Tabelle eingetragen. */										
											
									$i = 0;
									$offStemp = 0;
									$offShum = 0;
									$offSco = 0;
									$offSno2 = 0;
									$offSo3 = 0;
									while($i < $num){
										$time_stamp=pg_result($result1,$i,"time_stamp");
										if (pg_result($result2,$i-$offStemp,"time_stamp") == $time_stamp) {$temperature = pg_result($result2,$i-$offStemp,"numeric_value");}
											else {$temperature = "-"; $offStemp++;}
										if (pg_result($result3,$i-$offShum,"time_stamp") == $time_stamp) {$humidity = pg_result($result3,$i-$offShum,"numeric_value");}
											else {$humidity = "-"; $offShum++;}
										if (pg_result($result4,$i-$offSco,"time_stamp") == $time_stamp) {$co = pg_result($result4,$i-$offSco,"numeric_value");}
											else {$co = "-"; $offSco++;}
										if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");}
											else {$no2 = "-"; $offSno2++;}
										if (pg_result($result6,$i-$offSo3,"time_stamp") == $time_stamp) {$o3 = pg_result($result6,$i-$offSo3,"numeric_value");}
											else {$o3 = "-"; $offSo3++;}
					?>
					
					<tr>
						<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
						<td><?php echo $temperature; ?></td>
						<td><?php echo $humidity; ?></td>
						<td><?php echo $co; ?></td>
						<td><?php echo $no2; ?></td>
						<td><?php echo $o3; ?></td>
					</tr>
					
					<?php
							$i++;
									}
								}
						/* Der Teil analog für die Lanuv-Station an der Weseler Straße. 
							Diese misst NO, NO2 und PM10 und benötigt daher eine eigene Tabelle */
							
									if ($foi == 'Weseler'){
					?>
					
					<tr>
						<th>Zeit</th>
						<th>NO in &micro;g/m&sup3;</th>
						<th>NO2 in &micro;g/m&sup3;</th>
						<th>PM10 in &micro;g/m&sup3;</th>
					</tr>
					
					<?php
									$i = 0;
									$offSno = 0;
									$offSno2 = 0;
									$offSpm10 = 0;
									while($i < $num){
										$time_stamp = pg_result($result1,$i,"time_stamp");
										if (pg_result($result7,$i-$offSno,"time_stamp") == $time_stamp) {$no = pg_result($result7,$i-$offSno,"numeric_value");}
											else {$no = "-"; $offSno++;}
										if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");}
											else {$no2 = "-"; $offSno2++;}
										if (pg_result($result9,$i-$offSpm10,"time_stamp") == $time_stamp) {$pm10 = pg_result($result9,$i-$offSpm10,"numeric_value");}
											else {$pm10 = "-"; $offSpm10++;}
					?>
					
					<tr>
						<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
						<td><?php echo $no; ?></td>
						<td><?php echo $no2; ?></td>
						<td><?php echo $pm10; ?></td>
					</tr>
					
					<?php
									$i++;
									}
								}
								
						/* Der Teil analog für die Lanuv-Station im Geistviertel. 
							Diese misst NO, NO2, SO2, O3 und PM10 und benötigt daher eine eigene Tabelle */
							
									if ($foi == 'Geist'){
					?>
					
					<tr>
						<th>Zeit</th>
						<th>NO in &micro;g/m&sup3;</th>
						<th>NO2 in &micro;g/m&sup3;</th>
						<th>PM10 in &micro;g/m&sup3;</th>
						<th>SO2 in &micro;g/m&sup3;</th>
						<th>O3 in &micro;g/m&sup3;</th>
					</tr>
					
					<?php
									$i = 0;
									$offSno = 0;
									$offSno2 = 0;
									$offSpm10 = 0;
									$offSso2 = 0;
									$offSo3 = 0;
									while($i < $num){
										$time_stamp = pg_result($result1,$i,"time_stamp");
										if (pg_result($result7,$i-$offSno,"time_stamp") == $time_stamp) {$no = pg_result($result7,$i-$offSno,"numeric_value");}
											else {$no = "-"; $offSno++;}
										if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");}
											else {$no2 = "-"; $offSno2++;}
										if (pg_result($result9,$i-$offSpm10,"time_stamp") == $time_stamp) {$pm10 = pg_result($result9,$i-$offSpm10,"numeric_value");}
											else {$pm10 = "-"; $offSpm10++;}
										if (pg_result($result8,$i-$offSso2,"time_stamp") == $time_stamp) {$so2 = pg_result($result8,$i-$offSso2,"numeric_value");}
											else {$so2 = "-"; $offSso2++;}
										if (pg_result($result6,$i-$offSo3,"time_stamp") == $time_stamp) {$o3 = pg_result($result6,$i-$offSo3,"numeric_value");}
											else {$o3 = "-"; $offSo3++;}
					?>
					
					<tr>
						<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
						<td><?php echo $no; ?></td>
						<td><?php echo $no2; ?></td>
						<td><?php echo $pm10; ?></td>
						<td><?php echo $so2; ?></td>
						<td><?php echo $o3; ?></td>
					</tr>
					
					<?php
									$i++;
									}
									}
							}
							
						/* Der Teil für die bereinigten Tabellen, falls der Radiobutton "bereinigt" ausgewählt wurde. 
							Prinzipiell ist die Vorgehensweise analog zum obigen Teil, nur dass andere Funktionen verwendet werden,
							um Ausreißer in der Datenbank zu identifizieren. */
						
							if ($selected_radio == 'Bereinigt'){
								$start = $_POST['startdate'];
								$end = $_POST['enddate'];
								$foi = $_POST['foi'];
								$num = getTableBerNumRows($foi,$start,$end);
								$result1 = getTableBerTimeStamp($foi,$start,$end);
								if ($foi != 'Weseler' AND $foi != 'Geist'){$result2 = getTableBerOffering($foi,$start,$end, 'TEMPERATURE');}
								if ($foi != 'Weseler' AND $foi != 'Geist'){$result3 = getTableBerOffering($foi,$start,$end, 'AIR_HUMIDITY');}
								if ($foi != 'Weseler' AND $foi != 'Geist'){$result4 = getTableBerOffering($foi,$start,$end, 'CO_CONCENTRATION');}
								$result5 = getTableBerOffering($foi,$start,$end, 'NO2_CONCENTRATION');
								if ($foi != 'Weseler'){$result6 = getTableBerOffering($foi,$start,$end, 'O3_CONCENTRATION');}
								if ($foi == 'Weseler' OR $foi == 'Geist'){$result7 = getTableLanuvBerOffering($foi,$start,$end, 'NO_CONCENTRATION');}
								if ($foi == 'Geist'){$result8 = getTableLanuvBerOffering($foi,$start,$end, 'SO2_CONCENTRATION');}
								if ($foi == 'Weseler' OR $foi == 'Geist'){$result9 = getTableLanuvBerOffering($foi,$start,$end, 'PM10_CONCENTRATION');}
								
								if ($foi != 'Weseler' AND $foi != 'Geist'){
					?>
					<tr>
						<th>Zeit</th>
						<th>Temperatur in &deg;C</th>
						<th>Luftfeuchte in %</th>
						<th>CO in ppm</th>
						<th>NO2 in ppm</th>
						<th>O3 in ppm</th>
					</tr>
					<?php
					
						/* Zusätzliche Überprüfung, ob es sich um einen Ausreißer handelt oder ob der Wert bereits getestet wurde. Anschließend
							werden in der Tabelle Ausreißer rot markiert und noch nicht überprüfte Werte grau. */	
							
									$i = 0;
									$offStemp = 0;
									$offShum = 0;
									$offSco = 0;
									$offSno2 = 0;
									$offSo3 = 0;
									while($i < $num){
										$time_stamp = pg_result($result1,$i,"time_stamp");
										if (pg_result($result2,$i-$offStemp,"time_stamp") == $time_stamp) {$temperature = pg_result($result2,$i-$offStemp,"numeric_value");
											$tempout = pg_result($result2,$i-$offStemp,"quality_value");}
												else {$temperature = "-"; $tempout = 'no'; $offStemp++;}
										if (pg_result($result3,$i-$offShum,"time_stamp") == $time_stamp) {$humidity = pg_result($result3,$i-$offShum,"numeric_value");
											$humout = pg_result($result3,$i-$offShum,"quality_value");}
												else {$humidity = "-"; $humout = 'no'; $offShum++;}
										if (pg_result($result4,$i-$offSco,"time_stamp") == $time_stamp) {$co = pg_result($result4,$i-$offSco,"numeric_value");
											$coout = pg_result($result4,$i-$offSco,"quality_value");}
												else {$co = "-"; $coout = 'no'; $offSco++;}
										if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");
											$no2out = pg_result($result5,$i-$offSno2,"quality_value");}
												else {$no2 = "-"; $no2out = 'no'; $offSno2++;}
										if (pg_result($result6,$i-$offSo3,"time_stamp") == $time_stamp) {$o3 = pg_result($result6,$i-$offSo3,"numeric_value");
											$o3out = pg_result($result6,$i-$offSo3,"quality_value");}
												else {$o3 = "-"; $o3out = 'no'; $offSo3++;}
					?>
					<tr>
						<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
						<td><?php if ($tempout == 'no') echo $temperature;
									elseif ($tempout == 'yes') echo '<span style="color:#FF0000">'.$temperature.'</span>';
										else echo '<span style="color:#664C4C">'.$temperature.'</span>'?></td>
						<td><?php if ($humout == 'no') echo $humidity; 
									elseif ($humout == 'yes') echo '<span style="color:#FF0000">'.$humidity.'</span>';
										else echo '<span style="color:#664C4C">'.$humidity.'</span>'?></td>
						<td><?php if ($coout == 'no') echo $co;
									elseif ($coout == 'yes') echo '<span style="color:#FF0000">'.$co.'</span>';
										else echo '<span style="color:#664C4C">'.$co.'</span>'?></td>
						<td><?php if ($no2out == 'no') echo $no2;
									elseif ($no2out == 'yes') echo '<span style="color:#FF0000">'.$no2.'</span>';
										else echo '<span style="color:#664C4C">'.$no2.'</span>'?></td>
						<td><?php if ($o3out == 'no') echo $o3;
									elseif ($tempout == 'yes') echo '<span style="color:#FF0000">'.$o3.'</span>';
										else echo '<span style="color:#664C4C">'.$o3.'</span>'?></td>
					</tr>
					
					<?php
									$i++;
									}
								}
								
							/* Der Teil für die Station an der Weseler Straße */
							
									if ($foi == 'Weseler'){
					?>
					
					<tr>
						<th>Zeit</th>
						<th>NO in &micro;g/m&sup3;</th>
						<th>NO2 in &micro;g/m&sup3;</th>
						<th>PM10 in &micro;g/m&sup3;</th>
					</tr>
					
					<?php
										$i = 0;
										$offSno = 0;
										$offSno2 = 0;
										$offSpm10 = 0;
										while($i < $num){
										$time_stamp = pg_result($result1,$i,"time_stamp");
										if (pg_result($result7,$i-$offSno,"time_stamp") == $time_stamp) {$no = pg_result($result7,$i-$offSno,"numeric_value");
											$noout = pg_result($result7,$i-$offSno,"quality_value");}
											else {$no = "-"; $noout = 'no'; $offSno++;}
										if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");
											$no2out = pg_result($result5,$i-$offSno2,"quality_value");}
											else {$no2 = "-"; $no2out = 'no'; $offSno2++;}	
										if (pg_result($result9,$i-$offSpm10,"time_stamp") == $time_stamp) {$pm10 = pg_result($result9,$i-$offSpm10,"numeric_value");
											$pm10out = pg_result($result9,$i-$offSpm10,"quality_value");}
											else {$pm10 = "-"; $pm10out = 'no'; $offSpm10++;}
					?>
					
					<tr>
						<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
						<td><?php if ($noout == 'no') echo $no;
									elseif ($noout == 'yes') echo '<span style="color:#FF0000">'.$no.'</span>';
										else echo '<span style="color:#664C4C">'.$no.'</span>'?></td>
						<td><?php if ($no2out == 'no') echo $no2;
									elseif ($no2out == 'yes') echo '<span style="color:#FF0000">'.$no2.'</span>';
										else echo '<span style="color:#664C4C">'.$no2.'</span>'?></td>
						<td><?php if ($pm10out == 'no') echo $pm10;
									elseif ($pm10out == 'yes') echo '<span style="color:#FF0000">'.$pm10.'</span>';
										else echo '<span style="color:#664C4C">'.$pm10.'</span>'?></td>
					</tr>
					
					<?php
									$i++;
										}
									}
									
							/* Der Teil für die Station im Geistviertel */
							
									if ($foi == 'Geist'){
					?>
					
					<tr>
						<th>Zeit</th>
						<th>NO in &micro;g/m&sup3;</th>
						<th>NO2 in &micro;g/m&sup3;</th>
						<th>PM10 in &micro;g/m&sup3;</th>
						<th>SO2 in &micro;g/m&sup3;</th>
						<th>O3 in &micro;g/m&sup3;</th>
					</tr>
					
					<?php
										$offSno = 0;
										$offSno2 = 0;
										$offSpm10 = 0;
										$offSso2 = 0;
										$offSo3 = 0;
										$i = 0;
										while($i < $num){
										$time_stamp = pg_result($result1,$i,"time_stamp");
										if (pg_result($result7,$i-$offSno,"time_stamp") == $time_stamp) {$no = pg_result($result7,$i-$offSno,"numeric_value");
											$noout = pg_result($result7,$i-$offSno,"quality_value");}
											else {$no = "-"; $noout = 'no'; $offSno++;}
										if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");
											$no2out = pg_result($result5,$i-$offSno2,"quality_value");}
											else {$no2 = "-"; $no2out = 'no'; $offSno2++;}	
										if (pg_result($result9,$i-$offSpm10,"time_stamp") == $time_stamp) {$pm10 = pg_result($result9,$i-$offSpm10,"numeric_value");
											$pm10out = pg_result($result9,$i-$offSpm10,"quality_value");}
											else {$pm10 = "-"; $pm10out = 'no'; $offSpm10++;}
										if (pg_result($result8,$i-$offSso2,"time_stamp") == $time_stamp) {$so2 = pg_result($result8,$i-$offSso2,"numeric_value");
											$so2out = pg_result($result8,$i-$offSso2,"quality_value");}
											else {$so2 = "-"; $so2out = 'no'; $offSso2++;}
										if (pg_result($result6,$i-$offSo3,"time_stamp") == $time_stamp) {$o3 = pg_result($result6,$i-$offSo3,"numeric_value");
											$o3out = pg_result($result6,$i-$offSo3,"quality_value");}
											else {$o3 = "-"; $o3out = 'no'; $offSo3++;}

					?>
					
					<tr>
						<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
						<td><?php if ($noout == 'no') echo $no;
									elseif ($noout == 'yes') echo '<span style="color:#FF0000">'.$no.'</span>';
										else echo '<span style="color:#664C4C">'.$no.'</span>'?></td>
						<td><?php if ($no2out == 'no') echo $no2;
									elseif ($no2out == 'yes') echo '<span style="color:#FF0000">'.$no2.'</span>';
										else echo '<span style="color:#664C4C">'.$no2.'</span>'?></td>
						<td><?php if ($pm10out == 'no') echo $pm10;
									elseif ($pm10out == 'yes') echo '<span style="color:#FF0000">'.$pm10.'</span>';
										else echo '<span style="color:#664C4C">'.$pm10.'</span>'?></td>
						<td><?php if ($so2out == 'no') echo $so2;
									elseif ($so2out == 'yes') echo '<span style="color:#FF0000">'.$so2.'</span>';
										else echo '<span style="color:#664C4C">'.$so2.'</span>'?></td>
						<td><?php if ($o3out == 'no') echo $o3;
									elseif ($o3out == 'yes') echo '<span style="color:#FF0000">'.$o3.'</span>';
										else echo '<span style="color:#664C4C">'.$o3.'</span>'?></td>
					</tr>
					
					<?php
										$i++;
										}
									}
							}
						}
						else {
						
							/* Der Teil für die Weiterleitung von der Kartenseite. 
								Wird auf den "Tabellen" Link in einem Popup geklickt, so werden über die URL $_GET Variablen verschickt.
								Dadurch wird dann automatisch eine Tabelle mit Daten von heute und den vergangenen zwei Tagen erstellt.
								Die Vorgehensweise ist analog zu den bereits vorangegangenen Teilen. */
							
							$start = $_GET["starting"];
							$end = $_GET["ending"];
							$foi = $_GET["foiid"];
							$num = getTableNumRows($foi,$start,$end);			
							$result1 = getTableTimeStamp($foi,$start,$end);
							if ($foi != 'Weseler' AND $foi != 'Geist'){$result2 = getTableOffering($foi,$start,$end, 'TEMPERATURE');}
							if ($foi != 'Weseler' AND $foi != 'Geist'){$result3 = getTableOffering($foi,$start,$end, 'AIR_HUMIDITY');}
							if ($foi != 'Weseler' AND $foi != 'Geist'){$result4 = getTableOffering($foi,$start,$end, 'CO_CONCENTRATION');}
							$result5 = getTableOffering($foi,$start,$end, 'NO2_CONCENTRATION');
							if ($foi != 'Weseler'){$result6 = getTableOffering($foi,$start,$end, 'O3_CONCENTRATION');}
							if ($foi == 'Weseler' OR $foi == 'Geist'){$result7 = getTableOffering($foi,$start,$end, 'NO_CONCENTRATION');}
							if ($foi == 'Geist'){$result8 = getTableOffering($foi,$start,$end, 'SO2_CONCENTRATION');}
							if ($foi == 'Weseler' OR $foi == 'Geist'){$result9 = getTableOffering($foi,$start,$end, 'PM10_CONCENTRATION');}
								
							if ($foi != 'Weseler' AND $foi != 'Geist'){
					?>
								<tr>
									<th>Zeit</th>
									<th>Temperatur in &deg;C</th>
									<th>Luftfeuchte in %</th>
									<th>CO in ppm</th>
									<th>NO2 in ppm</th>
									<th>O3 in ppm</th>
								</tr>
					<?php
					
					/* Zeile für Zeile wird ausgelesen und anschließend in die erstellte Tabelle geschrieben */
					
								$i = 0;
								$offStemp = 0;
								$offShum = 0;
								$offSco = 0;
								$offSno2 = 0;
								$offSo3 = 0;
								while($i < $num){
									$time_stamp=pg_result($result1,$i,"time_stamp");
									if (pg_result($result2,$i-$offStemp,"time_stamp") == $time_stamp) {$temperature = pg_result($result2,$i-$offStemp,"numeric_value");}
										else {$temperature = "-"; $offStemp++;}
									if (pg_result($result3,$i-$offShum,"time_stamp") == $time_stamp) {$humidity = pg_result($result3,$i-$offShum,"numeric_value");}
										else {$humidity = "-"; $offShum++;}
									if (pg_result($result4,$i-$offSco,"time_stamp") == $time_stamp) {$co = pg_result($result4,$i-$offSco,"numeric_value");}
										else {$co = "-"; $offSco++;}
									if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");}
										else {$no2 = "-"; $offSno2++;}
									if (pg_result($result6,$i-$offSo3,"time_stamp") == $time_stamp) {$o3 = pg_result($result6,$i-$offSo3,"numeric_value");}
										else {$o3 = "-"; $offSo3++;}
					?>
					
								<tr>
									<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
									<td><?php echo $temperature; ?></td>
									<td><?php echo $humidity; ?></td>
									<td><?php echo $co; ?></td>
									<td><?php echo $no2; ?></td>
									<td><?php echo $o3; ?></td>
								</tr>
					<?php
									$i++;
								}
							}
							
							if ($foi == 'Weseler'){
					?>
								<tr>
									<th>Zeit</th>
									<th>NO in &micro;g/m&sup3;</th>
									<th>NO2 in &micro;g/m&sup3;</th>
									<th>PM10 in &micro;g/m&sup3;</th>
								</tr>
					<?php
					
					/* Zeile für Zeile wird ausgelesen und anschließend in die erstellte Tabelle geschrieben */
					
								$i = 0;
								$offSno = 0;
								$offSno2 = 0;
								$offSpm10 = 0;
								while($i < $num){
									$time_stamp = pg_result($result1,$i,"time_stamp");
									if (pg_result($result7,$i-$offSno,"time_stamp") == $time_stamp) {$no = pg_result($result7,$i-$offSno,"numeric_value");}
										else {$no = "-"; $offSno++;}
									if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");}
										else {$no2 = "-"; $offSno2++;}
									if (pg_result($result9,$i-$offSpm10,"time_stamp") == $time_stamp) {$pm10 = pg_result($result9,$i-$offSpm10,"numeric_value");}
										else {$pm10 = "-"; $offSpm10++;}
					?>
					
								<tr>
									<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
									<td><?php echo $no; ?></td>
									<td><?php echo $no2; ?></td>
									<td><?php echo $pm10; ?></td>
								</tr>
					<?php
									$i++;
								}
							}
							
							if ($foi == 'Geist'){
					?>
								<tr>
									<th>Zeit</th>
									<th>NO in &micro;g/m&sup3;</th>
									<th>NO2 in &micro;g/m&sup3;</th>
									<th>PM10 in &micro;g/m&sup3;</th>
									<th>SO2 in &micro;g/m&sup3;</th>
									<th>O3 in &micro;g/m&sup3;</th>
								</tr>
					<?php
					
					/* Zeile für Zeile wird ausgelesen und anschließend in die erstellte Tabelle geschrieben */
					
								$i = 0;
								$offSno = 0;
								$offSno2 = 0;
								$offSpm10 = 0;
								$offSso2 = 0;
								$offSo3 = 0;
								while($i < $num){
									$time_stamp = pg_result($result1,$i,"time_stamp");
									if (pg_result($result7,$i-$offSno,"time_stamp") == $time_stamp) {$no = pg_result($result7,$i-$offSno,"numeric_value");}
										else {$no = "-"; $offSno++;}
									if (pg_result($result5,$i-$offSno2,"time_stamp") == $time_stamp) {$no2 = pg_result($result5,$i-$offSno2,"numeric_value");}
										else {$no2 = "-"; $offSno2++;}
									if (pg_result($result9,$i-$offSpm10,"time_stamp") == $time_stamp) {$pm10 = pg_result($result9,$i-$offSpm10,"numeric_value");}
										else {$pm10 = "-"; $offSpm10++;}
									if (pg_result($result8,$i-$offSso2,"time_stamp") == $time_stamp) {$so2 = pg_result($result8,$i-$offSso2,"numeric_value");}
										else {$so2 = "-"; $offSso2++;}
									if (pg_result($result6,$i-$offSo3,"time_stamp") == $time_stamp) {$o3 = pg_result($result6,$i-$offSo3,"numeric_value");}
										else {$o3 = "-"; $offSo3++;}
					?>
					
								<tr>
									<td><?php echo date_format(date_create($time_stamp), 'd.m.Y H:i:s'); ?></td>
									<td><?php echo $no; ?></td>
									<td><?php echo $no2; ?></td>
									<td><?php echo $pm10; ?></td>
									<td><?php echo $so2; ?></td>
									<td><?php echo $o3; ?></td>
								</tr>
					<?php
									$i++;
								}
							}
						}
					?>
				
				</table>
			</div>
		</div>
	</div>
</body>