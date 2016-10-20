<?php
	$this->assign('title','SHOWCASE | Nodes');
	$this->assign('nav','nodes');

	$this->display('_Header.tpl.php');
?>

<script type="text/javascript">
	$LAB.script("scripts/app/nodes.js").wait(function(){
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
	<i class="icon-th-list"></i> Nodes 
	<span id=loader class="loader progress progress-striped active"><span class="bar"></span></span>
	<span class='input-append pull-right searchContainer'>
		
		<input id='filter' type="text" placeholder="Search..." />
		<button class='btn add-on'><i class="icon-search"></i></button>
	
			</span>
</h1>



<script type="text/template" id="FilterDevEuiTemplate">		
	<select id=FilterDevEui>
		<option value="" disabled selected hidden>Filter Dev Eui...</option>
		<option value="">-- Verwijder Filter --</option>
		<% items.each(function(item) { %>
			<% if (item.get('devEui')){ %>
				<option>
				<%= _.escape(item.get('devEui') || '')%>
				</option>
			<%}%>
		<% }); %>
		</select>
	</script>
	
		
	<script type="text/template" id="FilterAliasTemplate">		
	
		<select id=FilterAlias>
		<option value="" disabled selected hidden>Filter Alias...</option>
		<option value="">-- Verwijder Filter --</option>
		<% items.each(function(item) { %>
			<% if (item.get('alias')){ %>
				<option>
				<%= _.escape(item.get('alias') || '')%>
				</option>
			<%}%>
		<% }); %>
		</select>
	</script>
		<br>


	<table>
	<thead>
	
	<h4>Filters</h4>	

	</thead>
		<tbody>
	<tr>
	<td><div id="FilterDevEuiTemplateContainer" class="collectionContainer"></div></td>
	<td><input type="text" id="FilterDevEuiDisplay" placeholder="-Geen Filter-"></td>
	
	</tr>
	
	<tr>
	<td><div id="FilterAliasTemplateContainer" class="collectionContainer"></div></td>
	<td><input type="text" id="FilterAliasDisplay" placeholder="-Geen Filter-"></td>
	
	</tr>
	</tbody>
	<table>
		<br>

 
	<!-- underscore template for the collection -->
	<script type="text/template" id="nodeCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<th id="header_DevEui">Dev Eui<% if (page.orderBy == 'DevEui') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Alias">Alias<% if (page.orderBy == 'Alias') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Omschrijving">Omschrijving<% if (page.orderBy == 'Omschrijving') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('devEui')) %>">
				<td><%= _.escape(item.get('devEui') || '') %></td>
				<td><%= _.escape(item.get('alias') || '') %></td>
				<td><%= _.escape(item.get('omschrijving') || '') %></td>
			</tr>
		<% }); %>
		
		</tbody>
		</table>
	
		<%=  view.getPaginationHtml(page) %>
	</script>

	<!-- underscore template for the model -->
	<script type="text/template" id="nodeModelTemplate">
		<form class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div id="devEuiInputContainer" class="control-group">
					<label class="control-label" for="devEui">Dev Eui</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="devEui" placeholder="Dev Eui" value="<%= _.escape(item.get('devEui') || '') %>">
						<span class="help-inline"></span>
					</div>
				</div>
				<div id="aliasInputContainer" class="control-group">
					<label class="control-label" for="alias">Alias</label>
					<div class="controls inline-inputs">
						<input type="text" class="input-xlarge" id="alias" placeholder="Alias" value="<%= _.escape(item.get('alias') || '') %>">
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
		<form id="deleteNodeButtonContainer" class="form-horizontal" onsubmit="return false;">
			<fieldset>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button id="deleteNodeButton" class="btn btn-mini btn-danger"><i class="icon-trash icon-white"></i> Delete Node</button>
						<span id="confirmDeleteNodeContainer" class="hide">
							<button id="cancelDeleteNodeButton" class="btn btn-mini">Cancel</button>
							<button id="confirmDeleteNodeButton" class="btn btn-mini btn-danger">Confirm</button>
						</span>
					</div>
				</div>
			</fieldset>
		</form>
	</script>

	<!-- modal edit dialog -->
	<div class="modal hide fade" id="nodeDetailDialog">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>
				<i class="icon-edit"></i> Edit Node
				<span id="modelLoader" class="loader progress progress-striped active"><span class="bar"></span></span>
			</h3>
		</div>
		<div class="modal-body">
			<div id="modelAlert"></div>
			<div id="nodeModelContainer"></div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" >Cancel</button>
			<button id="saveNodeButton" class="btn btn-primary">Save Changes</button>
		</div>
	</div>

	<div id="collectionAlert"></div>
	

	
	<div id="nodeCollectionContainer" class="collectionContainer">
	</div>
	


	<p id="newButtonContainer" class="buttonContainer">
		<button id="newNodeButton" class="btn btn-primary">Add Node</button>
	</p>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>
