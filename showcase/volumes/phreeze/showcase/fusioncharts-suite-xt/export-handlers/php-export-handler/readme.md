FusionCharts PHP Export Handler
==================================

What is FusionCharts PHP export handler?

FusionCharts Suite XT uses JavaScript to generate charts in the browser, using SVG and VML (for older IE). If you need
to export the charts as images or PDF server-side, you need a server-side helper library to convert the SVG to image/PDF.
These export handlers allow you to take the SVG/base64imageData from FusionCharts charts and convert to image/PDF.

How does the export handler work?

- A chart is generated in the browser. When the export to image or PDF button is clicked, the chart generates the SVG
string or generate base64 image (only for modern browser and FusionCharts v 3.11.0 or above) to represent the current state and sends to the export handler. The export handler URL is configured via chart
attributes.
- The export handler accepts the SVG string along with chart configuration like chart type, width, height etc., and uses
InkScape & ImageMagick if required library to convert to image or PDF.
- The export handler either writes the image or PDF to disk, based on the configuration provided by chart, or streams it
back to the browser.

Version
=======

4.0

Requirements
============

Inkscape and ImageMagick is only required for FusionCharts lower version then 3.11.0.
For FusionCharts v 3.11.0 and higher if using older browser (IE < 10) which doesnot support base64 encoding and decoding.

Inkscape:

Inkscape is an open source vector graphics editor. What sets Inkscape apart is its use of Scalable Vector Graphics
(SVG), an open XML-based W3C standard, as the native format. Inkscape has a powerful command line interface and can
be used in scripts for a variety of tasks, such as exporting and format conversions. For details, refer to the
following page.

http://inkscape.org/doc/inkscape-man.html


ImageMagick:

ImageMagick is a free and open-source software suite for displaying, converting, and editing raster image and vector
image files. The software mainly consists of a number of command-line interface utilities for manipulating images.
For further details, please refer to the the following page.

http://www.imagemagick.org/

Installation
============

1. Place the files in the server folder from where the index.php can be accessed.
2. Give write permission to the folder 'temp' and 'ExportedImages' (create if not there);
3. If using older FusionCharts (< 3.11.0) or using older browser (IE < 10) install Inkscape and ImageMagick, check the path is same as the file inside Resources have.


*********************************************************************************************
License
-------

FUSIONCHARTS:

Copyright (c) FusionCharts Technologies LLP
License Information at http://www.fusioncharts.com/license