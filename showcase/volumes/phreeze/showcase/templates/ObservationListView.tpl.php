<?php
	$this->assign('title','SHOWCASE | Observations');
	$this->assign('nav','observations');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/app/observations.js").wait(function(){
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
	<i class="icon-th-list"></i> Observations
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

	<!-- underscore template for the collection -->
	<script type="text/template" id="observationCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<th id="header_Id">Id<% if (page.orderBy == 'Id') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Version">Version<% if (page.orderBy == 'Version') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_DateCreated">Date Created<% if (page.orderBy == 'DateCreated') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_NodeId">Node Id<% if (page.orderBy == 'NodeId') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_SensorId">Sensor Id<% if (page.orderBy == 'SensorId') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th id="header_Value">Value<% if (page.orderBy == 'Value') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
-->
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('id')) %>">
				<td><%= _.escape(item.get('id') || '') %></td>
				<td><%= _.escape(item.get('version') || '') %></td>
				<td><%if (item.get('dateCreated')) { %><%= _date(app.parseDate(item.get('dateCreated'))).format('MMM D, YYYY h:mm A') %><% } else { %>NULL<% } %></td>
				<td><%= _.escape(item.get('nodeId') || '') %></td>
				<td><%= _.escape(item.get('sensorId') || '') %></td>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<td><%= _.escape(item.get('value') || '') %></td>
-->
			</tr>
		<% }); %>
		</tbody>
		</table>

		<%=  view.getPaginationHtml(page) %>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="observationModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="idInputContainer" class="control-group">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<span class="input-xlarge uneditable-input" id="id"><%= _.escape(item.get('id') || '') %></span>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="versionInputContainer" class="control-group">
					<label class="control-label" for="version">Version</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="version" placeholder="Version" value="<%= _.escape(item.get('version') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="dateCreatedInputContainer" class="control-group">
					<label class="control-label" for="dateCreated">Date Created</label>
					<div class="controls inline-inputs">
						<div class="input-append date date-picker" data-date-format="yyyy-mm-dd">
							<input id="dateCreated" type="text" value="<%= _date(app.parseDate(item.get('dateCreated'))).format('YYYY-MM-DD') %>" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						<div class="input-append bootstrap-timepicker-component">
							<input id="dateCreated-time" type="text" class="timepicker-default input-small" value="<%= _date(app.parseDate(item.get('dateCreated'))).format('h:mm A') %>" />
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="nodeIdInputContainer" class="control-group">
					<label class="control-label" for="nodeId">Node Id</label>
					<div class="controls inline-inputs">
						<select id="nodeId" name="nodeId"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="sensorIdInputContainer" class="control-group">
					<label class="control-label" for="sensorId">Sensor Id</label>
					<div class="controls inline-inputs">
						<select id="sensorId" name="sensorId"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="valueInputContainer" class="control-group">
					<label class="control-label" for="value">Value</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="value" placeholder="Value" value="<%= _.escape(item.get('value') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteObservationButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteObservationButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Observation</button>
						<span id="confirmDeleteObservationContainer" class="hide">
							<button id="cancelDeleteObservationButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteObservationButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<!-- modal edit dialog -->
	<div class="modal hide fade" id="observationDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Edit Observation
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="observationModelContainer"></div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveObservationButton" class="btn btn-primary">Save Changes</button>
		</div>
	</div>

	<div id="collectionAlert"></div>
	
	<div id="observationCollectionContainer" class="collectionContainer">
	</div>

	<p id="newButtonContainer" class="buttonContainer">
		<button id="newObservationButton" class="btn btn-primary">Add Observation</button>
	</p>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
