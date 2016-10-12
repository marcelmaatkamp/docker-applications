<?php
	$this->assign('title','SHOWCASE | Laatste_Observaties');
	$this->assign('nav','laatste_observaties');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/app/laatste_observaties.js").wait(function(){
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
	<i class="icon-th-list"></i> Laatste Observaties Per Node/Sensor
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

	
	
	

	<!-- underscore template for the collection -->
	<script type="text/template" id="laatste_ObservatieCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<!--<th id="header_Observatieid">Observatieid<% if (page.orderBy == 'Observatieid') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>-->
				<th id="header_Node">Node<% if (page.orderBy == 'Node') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Sensor">Sensor<% if (page.orderBy == 'Sensor') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Observatiewaarde">Observatiewaarde<% if (page.orderBy == 'Observatiewaarde') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Observatiedatum">Observatiedatum<% if (page.orderBy == 'Observatiedatum') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('observatieid')) %>">
				<!--<td><%= _.escape(item.get('observatieid') || '') %></td>-->
				<td><%= _.escape(item.get('node') || '') %></td>
				<td><%= _.escape(item.get('sensor') || '') %></td>
				<td><%= _.escape(item.get('observatiewaarde') || '') %></td>
				<td><%if (item.get('observatiedatum')) { %><%= _date(app.parseDate(item.get('observatiedatum'))).format('MMM D, YYYY h:mm A') %><% } else { %>NULL<% } %></td>
			</tr>
		<% }); %>
		</tbody>
		</table>

		<%=  view.getPaginationHtml(page) %>
	</script>

	
	
	
	<!-- underscore template for the model -->
	<script type="text/template" id="laatste_ObservatieModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="observatieidInputContainer" class="control-group">
					<label class="control-label" for="observatieid">Observatieid</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="observatieid" placeholder="Observatieid" value="<%= _.escape(item.get('observatieid') || '') %>">
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
				<div id="observatiewaardeInputContainer" class="control-group">
					<label class="control-label" for="observatiewaarde">Observatiewaarde</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="observatiewaarde" placeholder="Observatiewaarde" value="<%= _.escape(item.get('observatiewaarde') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="observatiedatumInputContainer" class="control-group">
					<label class="control-label" for="observatiedatum">Observatiedatum</label>
					<div class="controls inline-inputs">
						<div class="input-append date date-picker" data-date-format="yyyy-mm-dd">
							<input id="observatiedatum" type="text" value="<%= _date(app.parseDate(item.get('observatiedatum'))).format('YYYY-MM-DD') %>" />
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
						<div class="input-append bootstrap-timepicker-component">
							<input id="observatiedatum-time" type="text" class="timepicker-default input-small" value="<%= _date(app.parseDate(item.get('observatiedatum'))).format('h:mm A') %>" />
							<span class="add-on"><i class="icon-time"></i></span>
						</div>
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>
		
		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<!-- Disable delete
		<form id="deleteLaatste_ObservatieButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteLaatste_ObservatieButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Laatste_Observatie</button>
						<span id="confirmDeleteLaatste_ObservatieContainer" class="hide">
							<button id="cancelDeleteLaatste_ObservatieButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteLaatste_ObservatieButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
		
		 -->
		 
	</script>

	<!-- modal edit dialog -->
	
	<div class="modal hide fade" id="laatste_ObservatieDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Laatste Observatie
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="laatste_ObservatieModelContainer"></div>
		</div>
		<!-- Disable Cancel/Save
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveLaatste_ObservatieButton" class="btn btn-primary">Save Changes</button>
		</div>
		-->
	</div>
	
	<div id="collectionAlert"></div>
	
	<div id="laatste_ObservatieCollectionContainer" class="collectionContainer">
	</div>
	
	<!-- Disable Add
	<p id="newButtonContainer" class="buttonContainer">
		<button id="newLaatste_ObservatieButton" class="btn btn-primary">Add Laatste_Observatie</button>
	</p>
	-->
	
</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
