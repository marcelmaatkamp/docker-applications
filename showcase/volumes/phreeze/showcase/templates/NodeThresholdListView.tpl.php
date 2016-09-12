<?php
	$this->assign('title','SHOWCASE | NodeThresholds');
	$this->assign('nav','nodethresholds');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/app/nodethresholds.js").wait(function(){
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
	<i class="icon-th-list"></i> NodeThresholds
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	</span>
</h1>

	<!-- underscore template for the collection -->
	<script type="text/template" id="nodeThresholdCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<th id="header_Id">Id<% if (page.orderBy == 'Id') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Version">Version<% if (page.orderBy == 'Version') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_KeepaliveTimeout">Keepalive Timeout<% if (page.orderBy == 'KeepaliveTimeout') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_NodeId">Node Id<% if (page.orderBy == 'NodeId') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_RmqChannel">Rmq Channel<% if (page.orderBy == 'RmqChannel') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<th id="header_SensorId">Sensor Id<% if (page.orderBy == 'SensorId') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_State">State<% if (page.orderBy == 'State') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
-->
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('id')) %>">
				<td><%= _.escape(item.get('id') || '') %></td>
				<td><%= _.escape(item.get('version') || '') %></td>
				<td><%= _.escape(item.get('keepaliveTimeout') || '') %></td>
				<td><%= _.escape(item.get('nodeId') || '') %></td>
				<td><%= _.escape(item.get('rmqChannel') || '') %></td>
<!-- UNCOMMENT TO SHOW ADDITIONAL COLUMNS
				<td><%= _.escape(item.get('sensorId') || '') %></td>
				<td><%= _.escape(item.get('state') || '') %></td>
-->
			</tr>
		<% }); %>
		</tbody>
		</table>

		<%=  view.getPaginationHtml(page) %>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="nodeThresholdModelTemplate">
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
				<div id="keepaliveTimeoutInputContainer" class="control-group">
					<label class="control-label" for="keepaliveTimeout">Keepalive Timeout</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="keepaliveTimeout" placeholder="Keepalive Timeout" value="<%= _.escape(item.get('keepaliveTimeout') || '') %>">
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
				<div id="rmqChannelInputContainer" class="control-group">
					<label class="control-label" for="rmqChannel">Rmq Channel</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="rmqChannel" placeholder="Rmq Channel" value="<%= _.escape(item.get('rmqChannel') || '') %>">
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
				<div id="stateInputContainer" class="control-group">
					<label class="control-label" for="state">State</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="state" placeholder="State" value="<%= _.escape(item.get('state') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
			</fieldset>
		</form>

		<!-- delete button is is a separate form to prevent enter key from triggering a delete -->
		<form id="deleteNodeThresholdButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteNodeThresholdButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete NodeThreshold</button>
						<span id="confirmDeleteNodeThresholdContainer" class="hide">
							<button id="cancelDeleteNodeThresholdButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteNodeThresholdButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<!-- modal edit dialog -->
	<div class="modal hide fade" id="nodeThresholdDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Edit NodeThreshold
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="nodeThresholdModelContainer"></div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveNodeThresholdButton" class="btn btn-primary">Save Changes</button>
		</div>
	</div>

	<div id="collectionAlert"></div>
	
	<div id="nodeThresholdCollectionContainer" class="collectionContainer">
	</div>

	<p id="newButtonContainer" class="buttonContainer">
		<button id="newNodeThresholdButton" class="btn btn-primary">Add NodeThreshold</button>
	</p>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
