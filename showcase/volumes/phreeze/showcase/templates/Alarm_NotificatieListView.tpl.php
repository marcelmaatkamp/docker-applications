<?php
	$this->assign('title','SHOWCASE | Alarm_Notificaties');
	$this->assign('nav','alarm_notificaties');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/app/alarm_notificaties.js").wait(function(){
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
	<i class="icon-th-list"></i> Alarm_Notificaties
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

	<!-- underscore template for the collection -->
	<script type="text/template" id="alarm_NotificatieCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<th id="header_Id">Id<% if (page.orderBy == 'Id') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_AlarmRegel">Alarm Regel<% if (page.orderBy == 'AlarmRegel') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Kanaal">Kanaal<% if (page.orderBy == 'Kanaal') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_P1">P1<% if (page.orderBy == 'P1') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_P2">P2<% if (page.orderBy == 'P2') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th id="header_P3">P3<% if (page.orderBy == 'P3') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_P4">P4<% if (page.orderBy == 'P4') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Meldingtekst">Meldingtekst<% if (page.orderBy == 'Meldingtekst') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
-->
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('id')) %>">
				<td><%= _.escape(item.get('id') || '') %></td>
				<td><%= _.escape(item.get('alarmRegel') || '') %></td>
				<td><%= _.escape(item.get('kanaal') || '') %></td>
				<td><%= _.escape(item.get('p1') || '') %></td>
				<td><%= _.escape(item.get('p2') || '') %></td>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<td><%= _.escape(item.get('p3') || '') %></td>
				<td><%= _.escape(item.get('p4') || '') %></td>
				<td><%= _.escape(item.get('meldingtekst') || '') %></td>
-->
			</tr>
		<% }); %>
		</tbody>
		</table>

		<%=  view.getPaginationHtml(page) %>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="alarm_NotificatieModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="idInputContainer" class="control-group">
					<label class="control-label" for="id">Id</label>
					<div class="controls inline-inputs">
						<span class="input-xlarge uneditable-input" id="id"><%= _.escape(item.get('id') || '') %></span>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="alarmRegelInputContainer" class="control-group">
					<label class="control-label" for="alarmRegel">Alarm Regel</label>
					<div class="controls inline-inputs">
						<select id="alarmRegel" name="alarmRegel"></select>
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="kanaalInputContainer" class="control-group">
					<label class="control-label" for="kanaal">Kanaal</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="kanaal" placeholder="Kanaal" value="<%= _.escape(item.get('kanaal') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="p1InputContainer" class="control-group">
					<label class="control-label" for="p1">P1</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="p1" placeholder="P1" value="<%= _.escape(item.get('p1') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="p2InputContainer" class="control-group">
					<label class="control-label" for="p2">P2</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="p2" placeholder="P2" value="<%= _.escape(item.get('p2') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="p3InputContainer" class="control-group">
					<label class="control-label" for="p3">P3</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="p3" placeholder="P3" value="<%= _.escape(item.get('p3') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="p4InputContainer" class="control-group">
					<label class="control-label" for="p4">P4</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="p4" placeholder="P4" value="<%= _.escape(item.get('p4') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="meldingtekstInputContainer" class="control-group">
					<label class="control-label" for="meldingtekst">Meldingtekst</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="meldingtekst" placeholder="Meldingtekst" value="<%= _.escape(item.get('meldingtekst') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteAlarm_NotificatieButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteAlarm_NotificatieButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Alarm_Notificatie</button>
						<span id="confirmDeleteAlarm_NotificatieContainer" class="hide">
							<button id="cancelDeleteAlarm_NotificatieButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteAlarm_NotificatieButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<!-- modal edit dialog -->
	<div class="modal hide fade" id="alarm_NotificatieDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Edit Alarm_Notificatie
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="alarm_NotificatieModelContainer"></div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveAlarm_NotificatieButton" class="btn btn-primary">Save Changes</button>
		</div>
	</div>

	<div id="collectionAlert"></div>
	
	<div id="alarm_NotificatieCollectionContainer" class="collectionContainer">
	</div>

	<p id="newButtonContainer" class="buttonContainer">
		<button id="newAlarm_NotificatieButton" class="btn btn-primary">Add Alarm_Notificatie</button>
	</p>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
