<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>SkyEagle</title>
	<link rel="shortcut icon" href="../images/egg_v1.png">
	<!--<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>-->
	<link rel="stylesheet" type="text/css" href="../css/styles.css" />

	<link rel="stylesheet" href="../css/leaflet.css" />
	<!--[if lte IE 8]><link rel="stylesheet" href="css/leaflet.ie.css" /><![endif]-->

	<script src="../js/leaflet.js"></script>
	<script src="../js/jquery-1.8.2.min.js"></script>
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
				<li class="current"><a href="Karte.php">Karte</a></li>
				<li><a href="Diagramme.php">Diagramme</a></li>
				 <li><a href="Tabelle.php">Tabelle</a></li>
				 <li><a href="Hilfe.php">Hilfe</a></li>
				 <li><a href="Impressum.php">Impressum</a></li>
				</ul>
		</div>
		</div>
		<br></br>
        <div id="map"></div>
		<form method='POST' onsubmit='addr_search();return false;'> <!-- Auch Drücken von Enter löst die Suche aus -->
		<div id="search"> <!-- Schriftzug "Stadt eingeben..." taucht auf und verschwindet" -->
			<input class="searchField" type="text" name="addr" value="Stadt eingeben..." 
				onfocus="if (this.value == 'Stadt eingeben...')
					{this.value = '';}" onblur="if (this.value == '')
					{this.value = 'Stadt eingeben...';}" 
				id="addr" size="10" />
			<button class="searchButton" type="button" onclick="addr_search();">Suche</button> <!-- Anpassen des Search Buttons -->
			<div id="results"/>
		</div>
		</form>
		
		<?php
			//ini_set( "display_errors", 0);
			include_once 'dbconnector.php';
			/* Jegliche Variablen der Getter zum Abrufen der aktuellsten Daten */
			$eggname = getName();				
			$eggfoi = getFoiIdMap();
			$eggcoords = getEggCoords();
			$eggtemp = getLatestOffering('TEMPERATURE');
			$egghum = getLatestOffering('AIR_HUMIDITY');
			$eggco = getLatestOffering('CO_CONCENTRATION');
			$eggno2 = getLatestOffering('NO2_CONCENTRATION');
			$eggo3 = getLatestOffering('O3_CONCENTRATION');
			$ausgabe = getFoiIdMap();
			print_r(count($ausgabe));
			$lanuvname = getLanuvName();
			$lanuvfoi = getLanuvFoiId();
			$lanuvcoords = getLanuvCoords();
			$lanuvno = getLatestLanuvOffering('NO_CONCENTRATION');
			$lanuvno2 = getLatestLanuvOffering('NO2_CONCENTRATION');
			$lanuvpm10 = getLatestLanuvOffering('PM10_CONCENTRATION');
			$lanuvso2 = getLatestLanuvOffering('SO2_CONCENTRATION');
			$lanuvo3 = getLatestLanuvOffering('O3_CONCENTRATION');
			
			/* Definition von heute und vorgestern für die Weiterleitung der Tabelle aus dem Popup.
				Bei dem heutigen Tag muss ein Tag aufaddiert werden, da der Datepicker sonst <$heute annimmt, statt <=$heute */
			$vorgestern = date("Y-m-d", strtotime("-2 day"));
			$heute = date("Y-m-d", strtotime("+1 day"));

		?>
		
		<script language="javascript" type="text/javascript">
		
		var map;

		function load_map() {
			
			map = new L.map('map', {zoomControl: true}).setView([51.967, 7.63],13);
			L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				maxZoom: 18,
				attribution: 'Map data &copy; 2012 <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
			}).addTo(map);
			var eggIcon = L.icon({
				iconUrl: '../images/egg_v1.png',
				iconSize: [22,28]
			});
			var lanuvIcon = L.icon({
				iconUrl: '../images/lanuv_antenna.png',
				iconSize: [28,30]
			});
						
			var i = 0;						
			var EierLayer = new Array();	// Verwendet für die Layerauswahl der Eier
			/* -------Json Kodierung der am Anfang definierten Variablen. eval() damit auch was dem Objekt zugeordnet wird------- */
			var EierNamen = eval(' (' + <?php print json_encode(json_encode($eggname)); ?> + ')');
			var EierFoi = eval(' (' + <?php print json_encode(json_encode($eggfoi)); ?> + ')');
			var EierCoords = eval( ' (' + <?php print json_encode(json_encode($eggcoords)); ?> + ')');
			var EierTemp = eval( ' (' + <?php print json_encode(json_encode($eggtemp)); ?> + ')');
			var EierLuft = eval( ' (' + <?php print json_encode(json_encode($egghum)); ?> + ')');
			var EierCO = eval( ' (' + <?php print json_encode(json_encode($eggco)); ?> + ')');
			var EierNO2 = eval( ' (' + <?php print json_encode(json_encode($eggno2)); ?> + ')');
			var EierO3 = eval( ' (' + <?php print json_encode(json_encode($eggo3)); ?> + ')');
			
			while (i <= EierFoi.length - 1) {						// für alle Eier durchlaufen, welche sich in der Datenbank befinden
				var EggName = EierNamen[i].feature_of_interest_name;	// Einamen auslesen
				
				var EggFoi = EierFoi[i].feature_of_interest_id;
				
				var EggCoordX = EierCoords[i].st_y;		// Eikoordinaten. x und y Koordinate sind in der Datenbank vertauscht
				var EggCoordY = EierCoords[i].st_x;
				
				/* Ausgabe der ganzen Messwerte. Wenn kein Wert vorhanden ist wird ' - ' ins Feld geschrieben */
				
				if (EierTemp[i] === undefined) {var EggTemp = '-'}	 
				else var EggTemp = EierTemp[i].numeric_value;			
		
				if (EierLuft[i] === undefined) {var EggHum = '-'}
				else var EggHum = EierLuft[i].numeric_value;
				
				if (EierCO[i] === undefined) {var EggCO = '-'}
				else var EggCO = EierCO[i].numeric_value;
				
				if (EierNO2[i] === undefined) {var EggNO2 = '-'}
				else var EggNO2 = EierNO2[i].numeric_value;
				
				if (EierO3[i] === undefined) {var EggO3 = '-'}
				else var EggO3 = EierO3[i].numeric_value;
			
				/* Erstellen für Popups für jedes Ei. Marker wird an Koordinaten gebunden und enthält Verlinkungen zu Wikipedia-Artikeln.
					Bei der Tabellenverlinkung wird eine Tabelle erstellt mit den Messwerten von heute, gestern und vorgestern */
					
				var markerEggs = L.marker([EggCoordX, EggCoordY],{icon: eggIcon}).bindPopup(
					EggName+"</br><a href=\"http://de.wikipedia.org/wiki/Temperatur\" target=\"_blank\">Temperatur</a>: "
					+EggTemp+" °C</br><a href=\"http://de.wikipedia.org/wiki/Luftfeuchtigkeit\"target=\"_blank\">Luftfeuchtigkeit</a>: "
					+EggHum+" %</br><a href=\"http://de.wikipedia.org/wiki/Kohlenstoffmonoxid\"target=\"_blank\">Kohlenstoffmonoxid</a>: "
					+EggCO+" ppm</br><a href=\"http://de.wikipedia.org/wiki/Stickstoffdioxid\"target=\"_blank\">Stickstoffdioxid</a>: "
					+EggNO2+" ppm</br><a href=\"http://de.wikipedia.org/wiki/Ozon\"target=\"_blank\">Ozon</a>: "
					+EggO3+" ppm</br></br><a href=\"Tabelle.php?starting=<?php echo $vorgestern?>&ending=<?php echo $heute?>&foiid="+EggFoi+"\"_blank\">Tabelle</a> ")
					;
				EierLayer[i] = markerEggs;		// Layer Array für jegliche Eier
				i++;
			}
			
			/* Analog zu dem obigen Teil mit den Air Quality Eggs */
			
			var j = 0;
			var LanuvLayer = new Array();
			var LanuvName = eval(' (' + <?php print json_encode(json_encode($lanuvname)); ?> + ')');
			var LanuvFoi = eval(' (' + <?php print json_encode(json_encode($lanuvfoi)); ?> + ')');
			var LanuvCoords = eval( ' (' + <?php print json_encode(json_encode($lanuvcoords)); ?> + ')');
			var LanuvNO = eval( ' (' + <?php print json_encode(json_encode($lanuvno)); ?> + ')');
			var LanuvNO2 = eval( ' (' + <?php print json_encode(json_encode($lanuvno2)); ?> + ')');
			var LanuvPM10 = eval( ' (' + <?php print json_encode(json_encode($lanuvpm10)); ?> + ')');
			var LanuvSO2 = eval( ' (' + <?php print json_encode(json_encode($lanuvso2)); ?> + ')');
			var LanuvO3 = eval( ' (' + <?php print json_encode(json_encode($lanuvo3)); ?> + ')');
			
			while (j <= LanuvFoi.length - 1) {
				var LANUVName = LanuvName[j].feature_of_interest_name;
				
				var LANUVFoi = LanuvFoi[j].feature_of_interest_id;
				
				var LANUVCoordX = LanuvCoords[j].st_x;		
				var LANUVCoordY = LanuvCoords[j].st_y;
				
				if (LanuvNO[j] === undefined) {var LANUVNO = '-'}
				else var LANUVNO = LanuvNO[j].numeric_value;
				
				if (LanuvNO2[j] === undefined) {var LANUVNO2 = '-'}
				else var LANUVNO2 = LanuvNO2[j].numeric_value;
				
				if (LanuvPM10[j] === undefined) {var LANUVPM10 = '-'}
				else var LANUVPM10 = LanuvPM10[j].numeric_value;
				
				if (LanuvSO2[j] === undefined) {var LANUVSO2 = '-'}
				else var LANUVSO2 = LanuvSO2[j].numeric_value;
				
				if (LanuvO3[j] === undefined) {var LANUVO3 = '-'}
				else var LANUVO3 = LanuvO3[j].numeric_value;

				/* Unterscheidung zwischen Geist und Weseler muss erfolgen, da nicht beide Messstationen die selben Messparameter besitzen */
				
				if (LANUVFoi == 'Geist') {
					var markerLanuv = L.marker([LANUVCoordX, LANUVCoordY],{icon: lanuvIcon}).bindPopup(
						LANUVName+"</br><a href=\"http://de.wikipedia.org/wiki/Stickstoffmonoxid\" target=\"_blank\">Stickstoffmonoxid</a>: "
						+LANUVNO+" µg/m³</br><a href=\"http://de.wikipedia.org/wiki/Stickstoffdioxid\" target=\"_blank\">Stickstoffdioxid</a>: "
						+LANUVNO2+" µg/m³</br><a href=\"http://de.wikipedia.org/wiki/PM10\" target=\"_blank\">Feinstaub</a>: "
						+LANUVPM10+" µg/m³</br><a href=\"http://de.wikipedia.org/wiki/Schwefeldioxid\" target=\"_blank\">Schwefeldioxid</a>: "
						+LANUVSO2+" µg/m³</br><a href=\"http://de.wikipedia.org/wiki/Ozon\" target=\"_blank\">Ozon</a>: "
						+LANUVO3+" µg/m³</br></br><a href=\"Tabelle.php?starting=<?php echo $vorgestern?>&ending=<?php echo $heute?>&foiid="+LANUVFoi+"\"_blank\">Tabelle</a> ")
					;
				}
				else {
					var markerLanuv = L.marker([LANUVCoordX, LANUVCoordY],{icon: lanuvIcon}).bindPopup(
						LANUVName+"</br><a href=\"http://de.wikipedia.org/wiki/Stickstoffmonoxid\" target=\"_blank\">Stickstoffmonoxid</a>: "
						+LANUVNO+" µg/m³</br><a href=\"http://de.wikipedia.org/wiki/Stickstoffdioxid\" target=\"_blank\">Stickstoffdioxid</a>: "
						+LANUVPM10+" µg/m³</br></br><a href=\"Tabelle.php?starting=<?php echo $vorgestern?>&ending=<?php echo $heute?>&foiid="+LANUVFoi+"\"_blank\">Tabelle</a> ")
					;
				}
				LanuvLayer[j] = markerLanuv;
				j++;
			}
			/* Definierung der beiden Layergruppen und hinzufügen zur Karte */
			var AQEs = L.layerGroup(EierLayer);
			var Lanuvs = L.layerGroup(LanuvLayer);
			
			var OverlayMaps = {
					"Air Quality Eggs": AQEs,
					"Lanuv Stationen": Lanuvs
				};
				
			L.control.layers(null,OverlayMaps).addTo(map);
			
		}
		
		/* Funktionen der Adresssuche. Credits dafür gehen an das Tutorial von derickr: 
			https://github.com/derickr/osm-tools/tree/master/leaflet-nominatim-example */
			
		function chooseAddr(lat, lng, type) {
			var location = new L.LatLng(lat, lng);
			map.panTo(location);
			if (type == 'city' || type == 'administrative') {
				map.setZoom(11);
			} else {
				map.setZoom(15);
			}
		}

		function addr_search() {
			var inp = document.getElementById("addr");
			$.getJSON('http://nominatim.openstreetmap.org/search?format=json&limit=5&q=' + inp.value, function(data) {
				var items = [];

				$.each(data, function(key, val) {
					items.push("<li><a href='#' onclick='chooseAddr(" + val.lat + ", " + val.lon + ", \"" + val.type + "\");return false;'>" + val.display_name + '</a></li>');
				});

				$('#results').empty();
					if (items.length != 0) {
						$('<p>', { html: "Search results:" }).appendTo('#results');
						$('<ul/>', {
							'class': 'my-new-list',
							html: items.join('')
						}).appendTo('#results');
					} else {
						$('<p>', { html: "No results found" }).appendTo('#results');
					}
			});
		}

		window.onload = load_map;
		
		</script>		
</body>
</html>