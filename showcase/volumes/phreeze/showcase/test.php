<?php
   /* **Step 1:** Include the `fusioncharts.php` file that contains functions to embed the charts. */
   include("includes/fusioncharts.php");
   ?>
<html>

   <head>
  	<title>FusionCharts XT - Simple Column 2D Chart</title>

  	<!-- **Step 2:**  Include the `fusioncharts.js` file. This file is needed to render the chart. Ensure that the path to this JS file is correct. Otherwise, it may lead to JavaScript errors. -->

  	<script src="fusioncharts/fusioncharts.js"></script>
   </head>
   <body>
  	<?php

     	/* **Step 3:** Create a `columnChart` chart object using the FusionCharts PHP class constructor. Syntax for the constructor: `FusionCharts("type of chart", "unique chart id", "width of chart", "height of chart", "div id to render the chart", "data format", "data source")`   */

    	$columnChart = new FusionCharts("Column2D", "myFirstChart" , 600, 300, "chart-1", "json",
            '{
                "chart": {
                    "caption": "Monthly revenue for last year",
                    "subCaption": "Harry\’s SuperMart",
                    "xAxisName": "Month",
                    "yAxisName": "Revenues (In USD)",
                    "numberPrefix": "$",
                    "paletteColors": "#0075c2",
                    "bgColor": "#ffffff",
                    "borderAlpha": "20",
                    "canvasBorderAlpha": "0",
                    "usePlotGradientColor": "0",
                    "plotBorderAlpha": "10",
                    "placeValuesInside": "1",
                    "rotatevalues": "1",
                    "valueFontColor": "#ffffff",
                    "showXAxisLine": "1",
                    "xAxisLineColor": "#999999",
                    "divlineColor": "#999999",
                    "divLineIsDashed": "1",
                    "showAlternateHGridColor": "0",
                    "subcaptionFontSize": "14",
                    "subcaptionFontBold": "0"
                },
                "data": [{
                    "label": "Jan",
                    "value": "420000"
                }, {
                    "label": "Feb",
                    "value": "810000"
                }, {
                    "label": "Mar",
                    "value": "720000"
                }, {
                    "label": "Apr",
                    "value": "550000"
                }, {
                    "label": "May",
                    "value": "910000"
                }, {
                    "label": "Jun",
                    "value": "510000"
                }, {
                    "label": "Jul",
                    "value": "680000"
                }, {
                    "label": "Aug",
                    "value": "620000"
                }, {
                    "label": "Sep",
                    "value": "610000"
                }, {
                    "label": "Oct",
                    "value": "490000"
                }, {
                    "label": "Nov",
                    "value": "900000"
                }, {
                    "label": "Dec",
                    "value": "730000"
                }]
            }'
    );

/* Because we are using JSON to specify chart data, `json` is passed as the value for the data format parameter of the constructor. The actual chart data, in string format, is passed as the value for the data source parameter of the constructor. Alternatively, you can store this string in a variable and pass the variable to the constructor. */

     	/* **Step 4:** Render the chart */
     	$columnChart->render();
  	?>
  	<div id="chart-1"><!-- Fusion Charts will render here--></div>
   </body>
</html>