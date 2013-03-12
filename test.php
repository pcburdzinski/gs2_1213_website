<!DOCTYPE HTML>
<head>
<title>test</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

<script>
$(function() {
	$( "#datepicker" ).datepicker( { dateFormat: 'yy-mm-dd' });
	$( "#datepicker2" ).datepicker( {dateFormat: 'yy-mm-dd' });

});
</script>

</head>

<body>
	<form action = "#" method ="post" name="form">
		<fieldset>
		<legend>Selecting Test</legend>
		<p>
			<label>Messstation</label>
					<?php 
						include('selopt.php');
					
					?>
		</p>
		</fieldset>
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
				
		</p>
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
</body>