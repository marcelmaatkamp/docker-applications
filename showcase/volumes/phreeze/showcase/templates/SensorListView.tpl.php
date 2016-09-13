<?php
	$this->assign('title','SHOWCASE | Sensoren');
	$this->assign('nav','sensoren');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/app/sensoren.js").wait(function(){
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
	<i class="icon-th-list"></i> Sensoren
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

	<!-- underscore template for the collection -->
	<script type="text/template" id="sensorCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<th id="header_SensorId">Sensor Id<% if (page.orderBy == 'SensorId') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Omschrijving">Omschrijving<% if (page.orderBy == 'Omschrijving') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('sensorId')) %>">
				<td><%= _.escape(item.get('sensorId') || '') %></td>
				<td><%= _.escape(item.get('omschrijving') || '') %></td>
			</tr>
		<% }); %>
		</tbody>
		</table>

		<%=  view.getPaginationHtml(page) %>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="sensorModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="sensorIdInputContainer" class="control-group">
					<label class="control-label" for="sensorId">Sensor Id</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="sensorId" placeholder="Sensor Id" value="<%= _.escape(item.get('sensorId') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="omschrijvingInputContainer" class="control-group">
					<label class="control-label" for="omschrijving">Omschrijving</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="omschrijving" placeholder="Omschrijving" value="<%= _.escape(item.get('omschrijving') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteSensorButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteSensorButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Sensor</button>
						<span id="confirmDeleteSensorContainer" class="hide">
							<button id="cancelDeleteSensorButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteSensorButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<!-- modal edit dialog -->
	<div class="modal hide fade" id="sensorDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Edit Sensor
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="sensorModelContainer"></div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveSensorButton" class="btn btn-primary">Save Changes</button>
		</div>
	</div>

	<div id="collectionAlert"></div>
	
	<div id="sensorCollectionContainer" class="collectionContainer">
	</div>

	<p id="newButtonContainer" class="buttonContainer">
		<button id="newSensorButton" class="btn btn-primary">Add Sensor</button>
	</p>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
