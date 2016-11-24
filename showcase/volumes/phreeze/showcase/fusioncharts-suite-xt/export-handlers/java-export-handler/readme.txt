FusionCharts J2EE Export Handler
==================================

What is FusionCharts J2EE export handler?

FusionCharts Suite XT uses JavaScript to generate charts in the browser, using SVG and VML (for older IE). If you need
to export the charts as images or PDF, you need a server-side helper library to convert the SVG to image/PDF. These
export handlers allow you to take the SVG from FusionCharts charts and convert to image/PDF.

How does the export handler work?

- A chart is generated in the browser. When the export to image or PDF button is clicked, the chart generates the SVG
string to represent the current state and sends to the export handler. The export handler URL is configured via chart
attributes.
- The export handler accepts the SVG string along with chart configuration like chart type, width, height etc., and uses
InkScape & ImageMagick library to convert to image or PDF.
- The export handler either writes the image or PDF to disk, based on the configuration provided by chart, or streams it
back to the browser.

Version
=======

3.0

Requirements
============

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

*  You should have a Windows/Linux based server with Administrative facility to install softwares. This is particularly
important, if you are using a shared hosting service.
*  Both Inkscape and ImageMagick need to be installed in order to make the whole system work. Please visit to
the respective sites and follow the instructions on installation.
*  Edit web.xml and add the following servlet mapping in your application's web.xml :
<servlet>
<display-name>FCExporter</display-name>
<servlet-name>FCExporter</servlet-name>
<servlet-class>com.fusioncharts.exporter.servlet.FCExporter</servlet-class>
<load-on-startup>1</load-on-startup>
</servlet>
<servlet-mapping>
<servlet-name>FCExporter</servlet-name>
<url-pattern>/JSP/ExportExample/FCExporter</url-pattern>
</servlet-mapping>
*  Modify the URL-pattern as per your application needs.
*  Specify the xml attribute exportHandler='FCExporter' assuming that the jsp rendering the chart is present in /JSP/ExportExample folder
*  Configuration of the folder where the generated image is to be saved in server is to be set in fusioncharts_export.properties file inside the Classes directory.
*  Configuration of Inkscape and ImageMagick path(Only for Windows Environment) : Open fusioncharts_export.properties file present in the Classes directory and make changes in the following values there:

********************************fusioncharts_export.properties*************************************
#Please specify the path to a folder with write permissions relative to web application root
#The exported image/PDF files would be saved here(for Linux based server SAVEPATH should be changed to relative or absolute path accordingly)
SAVEPATH=/JSP/ExportExample/ExportedImages/

#This constant HTTP_URI stores the HTTP reference to 
#the folder where exported charts will be saved. 
#Please enter the HTTP representation of that folder 
#in this constant e.g., http://www.yourdomain.com/images/
HTTP_URI=http://localhost:8081/ExportHandler/JSP/ExportExample/ExportedImages/

#OVERWRITEFILE sets whether the export handler would overwrite an existing file 
#the newly created exported file. If it is set to false the export handler would
#not overwrite. In this case if INTELLIGENTFILENAMING is set to true the handler
#would add a suffix to the new file name. The suffix is a randomly generated UUID.
#Additionally, you can add a timestamp or random number as additional prefix.
FILESUFFIXFORMAT=TIMESTAMP
OVERWRITEFILE=false
INTELLIGENTFILENAMING=true

#Set the path of Inkscape here(Only for Windows)
INKSCAPE_PATH=C:\\Program Files (x86)\\Inkscape

#Set the path of ImageMagick here(Only for Windows)
IMAGEMAGICK_PATH=C:\\Program Files\\ImageMagick-6.9.0-Q16
*********************************************************************************************
License
-------

FUSIONCHARTS:

Copyright (c) FusionCharts Technologies LLP
License Information at http://www.fusioncharts.com/license

Known Issues / limitations:
---------------------------

*  When we export to an SVG file. The file renders correctly in browsers, but may not render properly in other image
softwares.
*  If the chart has any external images as in logo, background or in anchors they will not get exported in the exported
image.
*  The whole system is configured for Windows/Linux based server.