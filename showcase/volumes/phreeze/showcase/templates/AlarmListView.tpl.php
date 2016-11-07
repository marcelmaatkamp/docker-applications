<?php
	$this->assign('title','SHOWCASE | Alarmen');
	$this->assign('nav','alarmen');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/app/alarmen.js").wait(function(){
		$(document).ready(function(){
			page.init();
		});
		
		// hack for IE9 which may respond inconsistently with document.ready
		setTimeout(function(){
			if (!page.isInitialized) page.init();
		},1000);
	});
</script>

<div class="container">

<h1>
	<i class="icon-th-list"></i> Alarm Rapport
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

<br>
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
		from `alarm_regel`
		inner join node on node.dev_eui = alarm_regel.node
		inner join sensor on sensor.sensor_id = alarm_regel.sensor
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
		from `alarm_regel`
		inner join node on node.dev_eui = alarm_regel.node
		inner join sensor on sensor.sensor_id = alarm_regel.sensor
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
	<script type="text/template" id="alarmCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<!--<th id="header_Id">Id<% if (page.orderBy == 'Id') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>-->
				<th id="header_Node">Node<% if (page.orderBy == 'Node') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Sensor">Sensor<% if (page.orderBy == 'Sensor') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Alarmtrigger">Alarmtrigger<% if (page.orderBy == 'Alarmtrigger') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Observatiewaarde">Observatiewaarde<% if (page.orderBy == 'Observatiewaarde') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>

				<th id="header_Observatietijdstip">Observatietijdstip (UTC)<% if (page.orderBy == 'Observatietijdstip') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>

			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('id')) %>">
				<!--<td><%= _.escape(item.get('id') || '') %></td>-->
				<td><%= _.escape(item.get('node') || '') %></td>
				<td><%= _.escape(item.get('sensor') || '') %></td>
				<td><%= _.escape(item.get('alarmtrigger') || '') %></td>
				<td><%= _.escape(item.get('observatiewaarde')  || '') %> <%= _.escape(item.get('sensoreenheid')  || '') %></td>
				
				

				<td><%if (item.get('observatietijdstip')) { %><%= _date(app.parseDate(item.get('observatietijdstip'))).format('DD-MM-YYYY - HH:mm:ss') %><% } else { %>NULL<% } %></td>

			</tr>
		<% }); %>
		</tbody>
		</table>

		<%=  view.getPaginationHtml(page) %>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="alarmModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<!-- <div id="idInputContainer" class="control-group">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge uneditable-input" id="id" placeholder="Id" value="<%= _.escape(item.get('id') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>-->
				<div id="nodeInputContainer" class="control-group">
					<label class="control-label" for="node">Node</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge uneditable-input" id="node" placeholder="Node" value="<%= _.escape(item.get('node') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="sensorInputContainer" class="control-group">
					<label class="control-label" for="sensor">Sensor</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge uneditable-input" id="sensor" placeholder="Sensor" value="<%= _.escape(item.get('sensor') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="alarmtriggerInputContainer" class="control-group">
					<label class="control-label" for="alarmtrigger">Alarmtrigger</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge uneditable-input" id="alarmtrigger" placeholder="Alarmtrigger" value="<%= _.escape(item.get('alarmtrigger') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="observatiewaardeInputContainer" class="control-group">
					<label class="control-label" for="observatiewaarde">Observatiewaarde</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge uneditable-input" id="observatiewaarde" placeholder="Observatiewaarde" value="<%= _.escape(item.get('observatiewaarde') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="observatietijdstipInputContainer" class="control-group">
					<label class="control-label" for="observatietijdstip">Observatietijdstip</label>
					<div class="controls inline-inputs">
						<div class="input-append date date-picker" data-date-format="dd-mm-yyyy">
							<input id="observatietijdstip" type="text" class="input-xlarge uneditable-input" value="<%= _date(app.parseDate(item.get('observatietijdstip'))).format('DD-MM-YYYY') %>" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						<div class="input-append bootstrap-timepicker-component">
							<input id="observatietijdstip-time" type="text" class="timepicker-default input-small uneditable-input" value="<%= _date(app.parseDate(item.get('observatietijdstip'))).format('hh:mm:ss') %>" />
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete
		<!-- Disable Cancel/Save
		<form id="deleteAlarmButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteAlarmButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Alarm</button>
						<span id="confirmDeleteAlarmContainer" class="hide">
							<button id="cancelDeleteAlarmButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteAlarmButton" class="btn btn-mini btn-danger">Bevestig Delete</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
		
		-->
		
	</script>

	<!-- modal edit dialog -->
	<div class="modal hide fade" id="alarmDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Alarm
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="alarmModelContainer"></div>
		</div>
			<!-- Disable Cancel/Save
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveAlarmButton" class="btn btn-primary">Save Changes</button>
		</div>
		-->
	</div>

	<div id="collectionAlert"></div>
	
	<div id="alarmCollectionContainer" class="collectionContainer">
	</div>
	
	<!-- Disable Add
	<p id="newButtonContainer" class="buttonContainer">
		<button id="newAlarmButton" class="btn btn-primary">Add Alarm</button>
	</p>
	-->

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
