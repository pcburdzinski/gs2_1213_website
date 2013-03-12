<!DOCTYPE HTML>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SkyEagle</title>
<link rel="shortcut icon" href="../images/Yoshi_Egg.png">
<link rel="stylesheet" type="text/css" href="../css/styles.css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script src="ger_dpicker.js"></script>

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
				<img class="logo" src="../images/Yoshi_Egg.png" width=55 height=55 align="left"> <!--Logo und Anpassung der Größe-->
				<p>SkyEagle<p> <!-- Name des Projekts-->
			</div>
        </div>
        <div id="navigationwrap">
            <ul id="menu-bar"> <!-- Reiter und Menüpunkte. Namen und Verlinkungen -->
				<li class="current"><a href="Home.htm">Home</a></li>
				<li><a href="Karte.htm">Karte</a></li>
				<li><a href="#">Diagramme</a>
				<ul>
				   <li><a href="../php/test.php">Diagramm ohne Ausrei&szliger</a></li>	<!--" &szlig " für das ß -->
				   <li><a href="#">Diagramm mit Ausrei&szliger</a></li>
				  </ul>
				 </li>
				 <li><a href="#">Tabelle</a>
				  <ul>
				   <li><a href="#">Tabelle ohne Ausrei&szliger</a></li>
				   <li><a href="#">Tabelle mit Ausrei&szliger</a></li>
				  </ul>
				 </li>
				 <li><a href="Hilfe.htm">Hilfe</a></li>
				 <li><a href="Impressum.htm">Impressum</a></li>
				</ul>
		</div>
		<div id="contentwrap">
			<div id="content">
				<form action = "#" method ="post" name="form">
					<fieldset>
						<legend>Selecting Test</legend><br>
						<label>Messstation</label>
							<?php 
							include('selopt.php');
							?>
					</fieldset><br>
					<fieldset>
						<legend>Check Test</legend>			
							<input type = "text" 
								id="datepicker"
								name = "startdate"
							/>	
							<input type = "text"
								id="datepicker2"
								name = "enddate"
							/>						
					</fieldset>
					<fieldset>	
						<label>checkbox</label>	
							<input type = "checkbox"
								id = "chkNO2"
								value = "NO2"
								name = "observation[]" />
						<label for "chkNO2">NO2</label>
							<input type = "checkbox"
								id = "chkSO2"
								value = "SO2"
								name = "observation[]" />
						<label for = "chkSO2">SO2</label>
							<input type = "checkbox"
								id = "chkO3"
								value = "O3"
								name = "observation[]" />
						<label for = "chkO3">O3</label>
					</fieldset>
					<fieldset>
						<label>Button</label>
							<input type = "submit"
							/>
					</fieldset>		
				</form>
				<div>	
					<?php
					include ('getobs.php');
					?>
				</div>
			</div>
		</div>
	</div>
</body>
