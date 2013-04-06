<?php include 'getobsval.php'; ?>

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

	<?php date_default_timezone_set('Europe/Berlin');?>
	$( "#datepicker" ).datepicker( { dateFormat: 'yy-mm-dd' });
	$( "#datepicker2" ).datepicker( {dateFormat: 'yy-mm-dd' });

	var startdate = "<?php 
		if (isset($_POST['startdate']))
			{echo  $_POST['startdate'];} 
		else {echo date("Y-m-d",time());} ?>";

	var enddate = "<?php   
		if (isset($_POST['enddate']))
			{echo $_POST['enddate'];} 
		else {echo date("Y-m-d",time() + 86400);}?>";
		
	if (typeof startdate !== "undefined"){
		$ ( "#datepicker").datepicker('setDate', startdate);
	} else {
		$( "#datepicker").datepicker('setDate', '+0');
	}
	
	if (typeof enddate !== "undefined"){
		$ ( "#datepicker2").datepicker('setDate', enddate);
	} else {
		$( "#datepicker2").datepicker('setDate', '+1');
	}		
}
)
;
</script>   
    
<script>
    // Load the Visualization API and the piechart package.
    google.load('visualization', '1', {'packages':['corechart']});
     
    //get the data
	var jsonData = <?php getValues()  ?>;
	var jsonData2 = <?php getValues("TEMPERATURE")  ?>;
	var jsonData3 = <?php getValues("AIR_HUMIDITY") ?>;
	var foi = "<?php if (isset($_POST['foi'])){ echo $_POST['foi']; } ?>";

//function to draw the chart
    function drawChart() {

	if (!(foi == "Geist" || foi == "Weseler")){		
    	
    	// Create our data table out of JSON data loaded from server.
          var data = new google.visualization.DataTable(jsonData);
          var data2 = new google.visualization.DataTable(jsonData2);
          var data3 = new google.visualization.DataTable(jsonData3);

          // Instantiate and draw our chart, passing in some options.
          var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
          var chart2 = new google.visualization.LineChart(document.getElementById('chart2_div'));
          var chart3 = new google.visualization.LineChart(document.getElementById('chart3_div'));

          
          chart.draw(data, {curveType: "function", width: 900, height: 400, vAxis:{title: "Werte in ppm", viewWindow:{min: 0}}, hAxis:{title: "Datum", slantedText:false}, chartArea:{width: '50%'}});
          chart2.draw(data2, {curveType: "function", width: 900, height: 400, vAxis:{title: "Temperatur in °C"}, hAxis:{slantedText:false}, chartArea:{width: '50%'}});
          chart3.draw(data3, {curveType: "function", width: 900, height: 400, vAxis:{title:"rel. Luftfeuchtigkeit in %", viewWindow:{min: 0}},hAxis:{slantedText:false}, chartArea:{width: '50%'}});
	}
	else {
    	// Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(jsonData);

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        
        chart.draw(data, {width: 700, height: 400, vAxis:{title: "Werte in µg/m³", viewWindow:{min: 0}}, hAxis:{title: "Datum", slantedText:false}, chartArea:{width: '50%'}});
	}
}
	


	// json-object are empty, do nothing	
	if (jsonData == "" || jsonData2 == "" || jsonData3 == ""){
	}
	// else draw the charts
	else {
    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);
   	}
</script>

<script>
// enable or disable the checkboxes
function changeForm(name){
	var checkboxCO = document.getElementById("chkCO"),
		checkboxO3 = document.getElementById("chkO3"),
		checkboxSO2 = document.getElementById("chkSO2"),
		checkboxPM10 = document.getElementById("chkPM10");
		checkboxNO = document.getElementById("chkNO");
// If Lanuv-station Geist is set, disable CO-Checkbox and enable O3, SO2, PM10 and NO
	if (name == "Geist"){
		checkboxCO.setAttribute('disabled', true);
		checkboxCO.checked = false;
		checkboxO3.removeAttribute('disabled');
		checkboxSO2.removeAttribute('disabled');
		checkboxPM10.removeAttribute('disabled');
		checkboxNO.removeAttribute('disabled');
	}
// If Lanuv-Station Weseler is set, disable CO, O3 and SO2-Checkbox and enable PM10 and NO
	else { if (name == "Weseler") {
		checkboxCO.setAttribute('disabled', true);
		checkboxCO.checked = false;
		checkboxO3.setAttribute('disabled', true);
		checkboxO3.checked = false;
		checkboxSO2.setAttribute('disabled', true);
		checkboxSO2.checked = false;
		checkboxPM10.removeAttribute('disabled');
		checkboxNO.removeAttribute('disabled');
	}
// else AQE is set
	else {
		checkboxCO.removeAttribute('disabled');
		checkboxO3.removeAttribute('disabled');
		checkboxSO2.setAttribute('disabled', true);
		checkboxSO2.checked = false;
		checkboxPM10.setAttribute('disabled', true);
		checkboxPM10.checked = false;
		checkboxNO.setAttribute('disabled', true);
		checkboxNO.checked = false;
	}
	}
}

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
				<li class="current"><a href="Diagramme.php">Diagramme</a></li>
				<li><a href="Tabelle.php">Tabelle</a></li>
				<li><a href="Hilfe.php">Hilfe</a></li>
				<li><a href="Impressum.php">Impressum</a></li>
			</ul>
		</div>
		<div id="contentwrap">
			<div id="content">
				<form action = "Diagramme.php" method ="post" id = "form" name="form">
					<fieldset>
						<legend>Bitte w&auml;hlen Sie eine Messstation</legend>
						<p>
							<label>Messstation</label>
							<?php include('tselopt.php');?>
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
						<legend>Bitte w&auml;hlen Sie aus, ob das Diagramm Ausrei&szlig;er beinhalten soll oder nicht</legend>
						<p>
						<input type="radio" name="outliers" value="yes" checked> unbereinigte Werte</input>
						<input type="radio" name="outliers" value="no"> bereinigte Werte</input>
						</p>
					</fieldset>
					<fieldset>
						<legend>Bitte w&auml;hlen Sie aus, welche Messwerte sie im Diagramm anzeigen wollen</legend>
						<p>
						
						<input type = "checkbox"
							id = "chkCO"
							value = "CO_CONCENTRATION"
							name = "observation[]"
							 <?php  if (isset($_POST['observation'])){
							 			if (in_array("CO_CONCENTRATION", $_POST['observation'])){
											echo 'checked ="checked"';
										}
									}
									if (isset($_POST['foi'])){
										if (($_POST['foi'] == "Geist") OR ($_POST['foi'] == "Weseler")){
											echo 'disabled';
										}
									}?>
							/>
						<label for = "chkCO">CO</label>

						<input type = "checkbox"
							id="chkNO"
							value = "NO_CONCENTRATION"
							name= "observation[]"
							 <?php  if (isset($_POST['observation'])){
							 			if (in_array("NO_CONCENTRATION", $_POST['observation'])){
											echo 'checked ="checked"';
											}
							 		}
									if (isset($_POST['foi'])){
										if (($_POST['foi'] != "Geist") OR ($_POST['foi'] != "Weseler")){
											echo 'disabled';
										}
									} else{ echo 'disabled';}
									?>							
							
							/>
						<label for = "chkNO">NO</label>
						
						<input type = "checkbox"
							id = "chkNO2"
							value = "NO2_CONCENTRATION"
							name = "observation[]"
							 <?php  if (isset($_POST['observation'])){
							 			if (in_array("NO2_CONCENTRATION", $_POST['observation'])){
											echo 'checked ="checked"';
										}
									}?>							
							/>
						<label for = "chkNO2">NO2</label>
						
						<input type = "checkbox"
							id = "chkO3"
							value = "O3_CONCENTRATION"
							name = "observation[]"
							 <?php  if (isset($_POST['observation'])){
							 			if (in_array("O3_CONCENTRATION", $_POST['observation'])){
											echo 'checked ="checked"';
										}
									}?>							
							/>
						<label for = "chkO3">O3</label>

						<input type = "checkbox"
							id = "chkPM10"
							value = "PM10_CONCENTRATION"
							name = "observation[]"
							 <?php  if (isset($_POST['observation'])){
							 			if (in_array("PM10_CONCENTRATION", $_POST['observation'])){
											echo 'checked ="checked"';
										}
									}								
									if (isset($_POST['foi'])){
										if (($_POST['foi'] != "Geist") OR ($_POST['foi'] != "Weseler")){
											echo 'disabled';
										}
									} else {echo 'disabled'; }?>							
								/>
						<label for = "chkPM10">PM10</label>	
						
						<input type = "checkbox"
							id = "chkSO2"
							value = "SO2_CONCENTRATION"
							name = "observation[]"
							 <?php  if (isset($_POST['observation'])){
							 			if (in_array("SO2_CONCENTRATION", $_POST['observation'])){
											echo 'checked ="checked"';
										}
									}
									if (isset($_POST['foi'])){
										if (($_POST['foi'] != "Geist") OR ($_POST['foi'] != "Weseler")){
											echo 'disabled';
										}
									} else {echo 'disabled';}?>							
							/>
						<label for = "chkSO2">SO2</label>
						</p>
					</fieldset>

					<fieldset>
						<input 	class = "searchButton"
								type = "submit"
								value = "Diagramm anzeigen"
						/>
					</fieldset>		
				</form>
				<?php 
				if (isset($_POST['foi'])){
					if ($_POST['foi'] != "Geist" OR $_POST['foi'] != "Weseler"){
						echo '
						<div id="chart_div"></div>
						<div id="chart2_div"></div>
						<div id="chart3_div"></div> ';
						}
					else { echo '<div id="chart_div"></div>'; }} ?>
		</div>
	</div>
  </body>

</html>