<?php
include("php-wrapper/fusioncharts.php");
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




<?php


	
// This is a simple example on how to draw a chart using FusionCharts and PHP.
// We have included includes/fusioncharts.php, which contains functions
// to help us easily embed the charts.
/* Include the `fusioncharts.php` file that contains functions  to embed the charts. */

  

/* The following 4 code lines contain the database connection information. Alternatively, you can move these code lines to a separate file and include the file here. You can also modify this code based on your database connection. */

 $hostdb = "mysql";  // MySQl host
 $hostport = "3306";  // MySQl port
 $userdb = "root";  // MySQL username
 $passdb = "my-secret-pw";  // MySQL password
 $namedb = "showcase";  // MySQL database name

 // Establish a connection to the database
 $dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb, $hostport);

 // Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect
 if ($dbhandle->connect_error) {
  exit("There was an error with your connection: ".$dbhandle->connect_error);
 }

  // Form the SQL query that returns the top 10 most populous countries
  $strQuery = "SELECT node as Node, round(avg(waarde)/10,1) as Temperatuur from observatie where sensor = 2 group by Node;";

  // Execute the query, or else return the error message.
  $result = $dbhandle->query($strQuery) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

  // If the query returns a valid response, prepare the JSON string
  if ($result) {
    // The `$arrData` array holds the chart attributes and data
    $arrData = array(
      "chart" => array(
          "caption" => "Gemiddelde temperatuur per Sensor ",
          "paletteColors" => "#0075c2",
          "bgColor" => "#ffffff",
          "borderAlpha"=> "20",
          "canvasBorderAlpha"=> "0",
          "usePlotGradientColor"=> "0",
          "plotBorderAlpha"=> "10",
          "showXAxisLine"=> "1",
          "xAxisLineColor" => "#999999",
          "showValues" => "1",
          "divlineColor" => "#999999",
          "divLineIsDashed" => "1",
          "showAlternateHGridColor" => "0"
        )
    );

    $arrData["data"] = array();

    // Push the data into the array
    while($row = mysqli_fetch_array($result)) {
      array_push($arrData["data"], array(
          "label" => $row["Node"],
          "value" => $row["Temperatuur"]
          )
      );
    }

    /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */

    $jsonEncodedData = json_encode($arrData);

	
    /*Create an object for the column chart using the FusionCharts PHP class constructor. Syntax for the constructor is ` FusionCharts("type of chart", "unique chart id", width of the chart, height of the chart, "div id to render the chart", "data format", "data source")`. Because we are using JSON data to render the chart, the data format will be `json`. The variable `$jsonEncodeData` holds all the JSON data for the chart, and will be passed as the value for the data source parameter of the constructor.*/

    $columnChart = new FusionCharts("column2D", "myFirstChart" , 600, 300, "chart-1", "json", $jsonEncodedData);

    // Render the chart
  
	$columnChart->render();

	
	

    // Close the database connection
    $dbhandle->close();
  }
  
  
?>






	<!-- underscore template for the collection -->
	<script type="text/template" id="observatieCollectionTemplate">
		<table class="collection table table-bordered table-hover">
		<thead>
			<tr>
				<th id="header_Id">Id<% if (page.orderBy == 'Id') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Node">Node<% if (page.orderBy == 'Node') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Sensor">Sensor<% if (page.orderBy == 'Sensor') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_DatumTijdAangemaakt">Datum Tijd Aangemaakt<% if (page.orderBy == 'DatumTijdAangemaakt') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
				<th id="header_Waarde">Waarde<% if (page.orderBy == 'Waarde') { %> <i class='icon-arrow-<%= page.orderDesc ? 'up' : 'down' %>' /><% } %></th>
			</tr>
		</thead>
		<tbody>
		<% items.each(function(item) { %>
			<tr id="<%= _.escape(item.get('id')) %>">
				<td><%= _.escape(item.get('id') || '') %></td>
				<td><%= _.escape(item.get('node') || '') %></td>
				<td><%= _.escape(item.get('sensor') || '') %></td>
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
		<button id="newObservatieButton" class="btn btn-primary">Add Observatie</button>
	</p>

</p>	

<div id="chart-1">chart here</div>


	
</div> <!-- /container -->


<?php
	$this->display('_Footer.tpl.php');
?>
