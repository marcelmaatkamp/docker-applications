<?php
	$this->assign('title','SHOWCASE | Alarm_Regels');
	$this->assign('nav','alarm_regels');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/app/alarm_regels.js").wait(function(){
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
	<i class="icon-th-list"></i> Alarm Regels
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

	<!-- underscore template for the collection -->
	<script type="text/template" id="alarm_RegelCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<th id="header_Id">Id<% if (page.orderBy == 'Id') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th> 
				<th id="header_Node">Node<% if (page.orderBy == 'NodeAlias') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Sensor">Sensor<% if (page.orderBy == 'SensorOmschrijving') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_AlarmTrigger">Alarm Trigger<% if (page.orderBy == 'AlarmTrigger') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('id')) %>"> 
				<td><%= _.escape(item.get('id') || '') %></td> 
				<td><%= _.escape(item.get('nodeAlias') || '') %></td>
				<td><%= _.escape(item.get('sensorOmschrijving') || '') %></td>
				<td><%= _.escape(item.get('alarmTrigger') || '') %></td>
			</tr>
		<% }); %>
		</tbody>
		</table>

		<%=  view.getPaginationHtml(page) %>
	</script>
	
	

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
	
	
	
	
	
	
	
	

	<!-- underscore template for the model -->
	<script type="text/template" id="alarm_RegelModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<!-- <div id="idInputContainer" class="control-group">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<span class="input-xlarge uneditable-input" id="id"><%= _.escape(item.get('id') || '') %></span>
						<span class="help-inline"></span>
					</div>
				</div>-->
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
				<div id="alarmTriggerInputContainer" class="control-group">
					<label class="control-label" for="alarmTrigger">Alarm Trigger</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="alarmTrigger" placeholder="Alarm Trigger" value="<%= _.escape(item.get('alarmTrigger') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteAlarm_RegelButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteAlarm_RegelButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Alarm_Regel</button>
						<span id="confirmDeleteAlarm_RegelContainer" class="hide">
							<button id="cancelDeleteAlarm_RegelButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteAlarm_RegelButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<!-- modal edit dialog -->
	<div class="modal hide fade" id="alarm_RegelDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Edit Alarm_Regel
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="alarm_RegelModelContainer"></div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveAlarm_RegelButton" class="btn btn-primary">Save Changes</button>
		</div>
	</div>

	<div id="collectionAlert"></div>
	
	<div id="alarm_RegelCollectionContainer" class="collectionContainer">
	</div>

	<p id="newButtonContainer" class="buttonContainer">
		<button id="newAlarm_RegelButton" class="btn btn-primary">Add Alarm Regel</button>
	</p>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
