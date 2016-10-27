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
	<i class="icon-th-list"></i> Alarmen
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

	<!-- underscore template for the collection -->
	<script type="text/template" id="alarmCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<!-- <th id="header_Id">Id<% if (page.orderBy == 'Id') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>-->
				<th id="header_Node">Node<% if (page.orderBy == 'Node') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Sensor">Sensor<% if (page.orderBy == 'Sensor') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Alarmtrigger">Alarmtrigger<% if (page.orderBy == 'Alarmtrigger') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Observatiewaarde">Observatiewaarde<% if (page.orderBy == 'Observatiewaarde') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>

				<th id="header_Observatietijdstip">Observatietijdstip<% if (page.orderBy == 'Observatietijdstip') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>

			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('id')) %>">
				<!--<td><%= _.escape(item.get('id') || '') %></td>-->
				<td><%= _.escape(item.get('node') || '') %></td>
				<td><%= _.escape(item.get('sensor') || '') %></td>
				<td><%= _.escape(item.get('alarmtrigger') || '') %></td>
				<td><%= _.escape(item.get('observatiewaarde') || '') %></td>

				<td><%if (item.get('observatietijdstip')) { %><%= _date(app.parseDate(item.get('observatietijdstip'))).format('MMM D, YYYY h:mm A') %><% } else { %>NULL<% } %></td>

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
				<div id="idInputContainer" class="control-group">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="id" placeholder="Id" value="<%= _.escape(item.get('id') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="nodeInputContainer" class="control-group">
					<label class="control-label" for="node">Node</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="node" placeholder="Node" value="<%= _.escape(item.get('node') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="sensorInputContainer" class="control-group">
					<label class="control-label" for="sensor">Sensor</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="sensor" placeholder="Sensor" value="<%= _.escape(item.get('sensor') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="alarmtriggerInputContainer" class="control-group">
					<label class="control-label" for="alarmtrigger">Alarmtrigger</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="alarmtrigger" placeholder="Alarmtrigger" value="<%= _.escape(item.get('alarmtrigger') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="observatiewaardeInputContainer" class="control-group">
					<label class="control-label" for="observatiewaarde">Observatiewaarde</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="observatiewaarde" placeholder="Observatiewaarde" value="<%= _.escape(item.get('observatiewaarde') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="observatietijdstipInputContainer" class="control-group">
					<label class="control-label" for="observatietijdstip">Observatietijdstip</label>
					<div class="controls inline-inputs">
						<div class="input-append date date-picker" data-date-format="yyyy-mm-dd">
							<input id="observatietijdstip" type="text" value="<%= _date(app.parseDate(item.get('observatietijdstip'))).format('YYYY-MM-DD') %>" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						<div class="input-append bootstrap-timepicker-component">
							<input id="observatietijdstip-time" type="text" class="timepicker-default input-small" value="<%= _date(app.parseDate(item.get('observatietijdstip'))).format('h:mm A') %>" />
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
							<button id="confirmDeleteAlarmButton" class="btn btn-mini btn-danger">Confirm</button>
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
