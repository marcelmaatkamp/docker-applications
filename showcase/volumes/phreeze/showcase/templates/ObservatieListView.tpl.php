<?php
include("php-wrapper/fusioncharts.php");
include_once("_global_config.php");
?>

<?php
	$this->assign('title','SHOWCASE | Observaties');
	$this->assign('nav','observaties');

	$this->display('_Header.tpl.php');
?>




<script type="text/javascript">
	$LAB.script("scripts/app/observaties.js").wait(function(){
		$(document).ready(function(){
			page.init();
		});
		
		// hack for IE9 which may respond inconsistently with document.ready
		setTimeout(function(){
			if (!page.isInitialized) page.init();
		},1000);
	});

</script>


		
<script type="text/javascript" src="fusioncharts-suite-xt/js/fusioncharts.js"></script>
<script type="text/javascript" src="fusioncharts-suite-xt/js/themes/fusioncharts.theme.ocean.js"></script>



<div class="container">



<h1>
	<i class="icon-th-list"></i> Observaties
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

<script type="text/template" id="FilterNodeTemplate">	
	
	<select id=FilterNode>
		<option value="" disabled selected hidden>Filter Node...</option>
		<option value="">-- Verwijder Filter --</option>
	
				
				
				
<?php

				

$db = new mysqli(
GlobalConfig::$CONNECTION_SETTING->Host, 
GlobalConfig::$CONNECTION_SETTING->Username,
GlobalConfig::$CONNECTION_SETTING->Password,
GlobalConfig::$CONNECTION_SETTING->DBName,
GlobalConfig::$CONNECTION_SETTING->Port);


if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}


$sql = "select distinct `node`.`dev_eui`, `node`.`alias` as NodeAlias
		from `observatie`
		inner join node on node.dev_eui = observatie.node
		inner join sensor on sensor.sensor_id = observatie.sensor
        having NodeAlias is not null and NodeAlias != ''";


if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
}

while($row = $result->fetch_assoc()){
    echo '<option>' . $row['NodeAlias'] . '</option>';
}

mysqli_close($db);

?>
				
	
		</select>
	</script>
	

<script type="text/template" id="FilterSensorTemplate">		
	
		<select id=FilterSensor>
		<option value="" disabled selected hidden>Filter Sensor...</option>
		<option value="">-- Verwijder Filter --</option>
		<?php

				

$db = new mysqli(
GlobalConfig::$CONNECTION_SETTING->Host, 
GlobalConfig::$CONNECTION_SETTING->Username,
GlobalConfig::$CONNECTION_SETTING->Password,
GlobalConfig::$CONNECTION_SETTING->DBName,
GlobalConfig::$CONNECTION_SETTING->Port);


if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}


$sql = "select distinct `sensor`.`omschrijving` as SensorOmschrijving
		from `observatie`
		inner join node on node.dev_eui = observatie.node
		inner join sensor on sensor.sensor_id = observatie.sensor
        having SensorOmschrijving is not null and SensorOmschrijving != ''";


if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
}

while($row = $result->fetch_assoc()){
    echo '<option>' . $row['SensorOmschrijving'] . '</option>';
}

mysqli_close($db);

?>
		</select>
	</script>


	<table>
	<thead>
	
	<h4>Filters</h4>	

	</thead>
		<tbody>
	<tr>
	<td><div id="FilterNodeTemplateContainer" class="collectionContainer"></div></td>
	<td><input type="text" id="FilterNodeDisplay" placeholder="-Geen Filter-"></td>
	
	</tr>
	
	<tr>
	<td><div id="FilterSensorTemplateContainer" class="collectionContainer"></div></td>
	<td><input type="text" id="FilterSensorDisplay" placeholder="-Geen Filter-"></td>
	
	</tr>
	</tbody>
	</table>
		<br>
	
	



	<!-- underscore template for the collection -->
	<script type="text/template" id="observatieCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<!--<th id="header_Id">Id<% if (page.orderBy == 'Id') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>-->
				<th id="header_Node">NodeAlias<% if (page.orderBy == 'nodeAlias') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Sensor">sensorOmschrijving<% if (page.orderBy == 'sensorOmschrijving') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_DatumTijdAangemaakt">Datum Tijd Aangemaakt<% if (page.orderBy == 'DatumTijdAangemaakt') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Waarde">Waarde<% if (page.orderBy == 'Waarde') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('id')) %>">
				<!--<td><%= _.escape(item.get('id') || '') %></td>-->
				<td><%= _.escape(item.get('nodeAlias') || '') %></td>
				<td><%= _.escape(item.get('sensorOmschrijving') || '') %></td>
				<td><%if (item.get('datumTijdAangemaakt')) { %><%= _date(app.parseDate(item.get('datumTijdAangemaakt'))).format('MMM D, YYYY h:mm A') %><% } else { %>NULL<% } %></td>
				<td><%= _.escape(item.get('waarde') || '') %></td>
			</tr>
		<% }); %>
		</tbody>
		</table>

		<%=  view.getPaginationHtml(page) %>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="observatieModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="idInputContainer" class="control-group">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<span class="input-xlarge uneditable-input" id="id"><%= _.escape(item.get('id') || '') %></span>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="nodeInputContainer" class="control-group">
					<label class="control-label" for="node">Node</label>
					<div class="controls inline-inputs">
						<select id="node" name="node"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="sensorInputContainer" class="control-group">
					<label class="control-label" for="sensor">Sensor</label>
					<div class="controls inline-inputs">
						<select id="sensor" name="sensor"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="datumTijdAangemaaktInputContainer" class="control-group">
					<label class="control-label" for="datumTijdAangemaakt">Datum Tijd Aangemaakt</label>
					<div class="controls inline-inputs">
						<div class="input-append date date-picker" data-date-format="yyyy-mm-dd">
							<input id="datumTijdAangemaakt" type="text" value="<%= _date(app.parseDate(item.get('datumTijdAangemaakt'))).format('YYYY-MM-DD') %>" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						<div class="input-append bootstrap-timepicker-component">
							<input id="datumTijdAangemaakt-time" type="text" class="timepicker-default input-small" value="<%= _date(app.parseDate(item.get('datumTijdAangemaakt'))).format('h:mm A') %>" />
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="waardeInputContainer" class="control-group">
					<label class="control-label" for="waarde">Waarde</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="waarde" placeholder="Waarde" value="<%= _.escape(item.get('waarde') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteObservatieButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteObservatieButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Observatie</button>
						<span id="confirmDeleteObservatieContainer" class="hide">
							<button id="cancelDeleteObservatieButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteObservatieButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<!-- modal edit dialog -->
	<div class="modal hide fade" id="observatieDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Edit Observatie
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="observatieModelContainer"></div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveObservatieButton" class="btn btn-primary">Save Changes</button>
		</div>
	</div>

	<div id="collectionAlert"></div>
	
	<div id="observatieCollectionContainer" class="collectionContainer">
	</div>

	<p id="newButtonContainer" class="buttonContainer">
		<button id="newObservatieButton" class="btn btn-primary">Add Observatie TEST ONLY</button>
	</p>

</p>	

<!--<div id="chart-1">chart here</div>-->


	
</div> <!-- /container -->


<?php
	$this->display('_Footer.tpl.php');
?>
