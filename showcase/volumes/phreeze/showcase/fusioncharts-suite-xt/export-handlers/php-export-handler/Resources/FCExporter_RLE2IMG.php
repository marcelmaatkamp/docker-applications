<?php
/**
 *
 * FusionCharts Exporter - 'Image Resource' handles
 * FusionCharts (since v3.1) Server Side Export feature that
 * helps FusionCharts exported as Image files in various formats.
 *
 *
 *    @author FusionCharts
 *    @description FusionCharts Exporter (Server-Side - PHP)
 *    @version 2.0 [ 12 February 2009 ]
 *
 */
/**
 *  ChangeLog / Version History:
 *  ----------------------------
 *
 *
 *  1.1  [ 18 July 2009 ]
 *  background color was turning black in a few linux distro - fixed . The original code is kept commneted. Line 277
 *
 *
 *
 *   1.0 [ 12 February 2009 ]
 *
 *	FEATURES:
 *       - Integrated with new Export feature of FusionCharts 3.1 & FusionCharts Exporter v 2.0
 *       - can save to server side directory
 *       - can provide download or open in browser window/frame other than _self
 *       - can save in various image formats viz. jpeg, png and gif.
 *
 *
 */
/**
 * Copyright (c) 2016 Infosoft Global Private Limited
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 *  GENERAL NOTES
 *  -------------
 *
 *  Chart would POST export data (which consists of encoded image data stream,
 *  width, height, background color and various other export parameters like
 *  exportFormat, exportFileName, exportAction, exportTargetWindow) to this script.
 *
 *  The script would process this data using appropriate resource files build
 *  export binary (PDF/image)
 *
 *  It either saves the binary as file to a server side directory or push it as
 *  Download or open in a new browser window/frame.
 *
 *
 */
/**
 *   @requires	FCExporter.php  A file that includes this resource
 *
 *
 *   Details
 *   -------
 *
 *   The resource files would have these things as common:
 *
 *   	a) a constant - MIMETYPES that would have a string
 *         containing semicolon separated key value pairs.
 *		   Each key can be a format name specified in the
 *		   HANDLERASSOCIATIONS constant. The associated value
 *		   would be the mimetype for the specified format.
 *
 *		   e.g. define("MIMETYPES","jpg=image/jpeg;jpeg=image/jpeg;png=image/png;gif=image/gif");
 *
 *
 *		b) a constant - EXTENSIONS that again contain a string of
 *		   semicolon separated key value pair. Each key would again be the
 *		   format name and the extension would be the file extension.
 *
 *		   e.g. define("EXTENSIONS","jpg=jpg;jpeg=jpg;png=png;gif=gif");
 *
 *
 *      c) a function  - exportProcessor ( $stream , $meta )
 *		   It would take the FusionCharts exncoded image string as $stream &
 *		   an associative array $meta containging width, height and bgColor keys.
 *
 *         The function would return an object of mixed type which would contain
 *		   the processed binary/relevant export object.
 *
 *
 *		d) a function - exportOutput ( $exportObj, $exportSettings, $quality=1 )
 *         It would take the processed export object and other export setting as parameter.
 *         Moreover, it would take an optional parameter - $quality (in scale of 0 to 1).
 *         By Default, the $quality is passed as 1 (best quality)
 *
 *		   The function would return the file path on success or return false on failure.
 *
 *      [ The other code in the resource file can be anything that support this architecture ]
 *
 */

// =============================================================================
// ==                  Constants and  Variables                               ==
// =============================================================================
// **** Users are recommended NOT to perform any editing beyond this point. ****

/* ------------------------- EXPORT RESOURCES -------------------------------- */

// This constant lists the mime types related to each export format this resource handles
// The value is semicolon separated key value pair for each format
// Each key is the format and value is the mime type
define( "MIMETYPES", "jpg=image/jpeg;jpeg=image/jpeg;gif=image/gif;png=image/png" );

// This constant lists all the file extensions for the export formats
// The value is semicolon separated key value pair for each format
// Each key is the format and value is the file extension
define( "EXTENSIONS", "jpg=jpg;jpeg=jpg;gif=gif;png=png" );


// =============================================================================
// ==                             Public Functions                            ==
// =============================================================================


/**
 *  Gets Export data from FCExporter - main module and build the export binary/objct.
 *  @param	$stream 	(string) export image data in FusionCharts compressed format
 *      	$meta		{array)	Image meta data in keys "width", "heigth" and "bgColor"
 *              $exportParams   {array} Export related parameters
 *  @return 			image object/binary
 */
function exportProcessor( $stream, $meta, $exportParams )
{

	// create a new export object
	// here it is an image generator class that handles jpg, png, gif export
	// pass all reqiured parameters
	$FCExporter = new FCIMGGenerator ( $stream, $meta['width'], $meta['height'], $meta['bgColor'] );

	// return export ready image object
	return $FCExporter->getImageObject();
}


/**
 *  exports (save/download) FusinoCharts exported image.
 *  @param	$exportObj 		(mixed) binary/objct exported by exportProcessor
 *	@param	$exportSettings	(array) various server-side export settings stored in keys like
 *									"type", "ready" "filepath" etc. Required for 'save' expotAction.
 *									For 'download' action "filepath" is blank (this is checked to find
 *									whether the action is "download" or not.
 *	@param	$quality		(integer) quality factor 0-1 (1 being the best quality). As of now we always pass 1.
 *
 *  @return 				false is fails. {filepath} if succeeds. Only returned when action is 'save'.
 */
function exportOutput ( $exportObj, $exportSettings , $quality = 1 )
{

	// decides image encoding and saving php(GD) function as per export type
	switch( strtolower( $exportSettings['type' ]) )
	{
		// in case of PNG check if 'imagepng' function exists.
		// save the image as png
		// store saving status in $doneExport which receives false if fails and true on success
		case "png" :
			if( function_exists ( "imagepng" ) ) {
				$doneExport = imagepng ( $exportObj, @$exportSettings ['filepath' ], $quality*9 );
			}
			break;

		// in case of GIF check if 'imagegif' function exists.
		// save the image as gif
		// store saving status in $doneExport which receives false if fails and true on success
		case "gif" :
			if( function_exists ( "imagegif" ) ) {
				// This is done as a fix to some PHP versions running on IIS
				if( trim(@$exportSettings ['filepath']) )
					$doneExport = imagegif ( $exportObj, @$exportSettings ['filepath'] );
				else
					$doneExport = imagegif ( $exportObj );
			}
			break;

		// in case of JPG/JPEG check if 'imagejpeg' function exists.
		// save the image as jpg
		// store saving status in $doneExport which receives false if fails and true on success
		case "jpg" :
		case "jpeg":
			if( function_exists ( "imagejpeg" ) ) {
				$doneExport = imagejpeg ( $exportObj, @$exportSettings ['filepath' ], $quality*100 );
			}
			break;

		default :
			raise_error( "Invalid Export Format." , true);
			break;

	}

	// clear memory after saving
	imagedestroy( $exportObj );

	// check 'filepath'. If it is null - the action is 'download' and hence terminate execution
	if ( !@$exportSettings [ 'filepath' ] ) exit();

	// check $doneEport and if true sets status to {filepath}'s value
	// set false if fails
	$status =( @$doneExport ? basename ( @$exportSettings ['filepath'] ) : false );

	// return status
	return $status;
}


#################################################################################
##                                                                             ##
##                     	 		 EXPORT CLASS             		               ##
##                                                                             ##
#################################################################################

class FCIMGGenerator
{
	//Array - Stores multiple chart export data
	var $arrExportData;
	//stores number of pages = length of $arrExportData array
	var $numPages=0;

	//Constructor - By default the chart export data can be passed to this
	function FCIMGGenerator($imageData_FCFormat="", $width="", $height="", $bgcolor="ffffff"){
		if($imageData_FCFormat && $width && $height){
			$this->setBitmapData($imageData_FCFormat, $width, $height, $bgcolor);
		}
	}

	// Add chart export data
	function setBitmapData($imageData_FCFormat, $width, $height, $bgcolor="ffffff"){
		$this->arrExportData[$this->numPages]["width"]=$width;
		$this->arrExportData[$this->numPages]["height"]=$height;
		$this->arrExportData[$this->numPages]["bgcolor"]=$bgcolor;
		$this->arrExportData[$this->numPages]["imageData"]=$imageData_FCFormat;
		$this->numPages++;
	}


	function getImageObject($id=0){
		//create image
		$image = imagecreatetruecolor($this->arrExportData[$id]["width"], $this->arrExportData[$id]["height"]);

		// Detect the background color
		if (!$this->arrExportData[$id]["bgcolor"]){
			$this->arrExportData[$id]["bgcolor"] = "ffffff";
		}
		//set Background color
		// Some linux distro have issues with imagefill
		// Hence, using imagefilledrectangle() instead
		//imagefill($image, 0, 0, $this->composeColor($image,$this->arrExportData[$id]["bgcolor"]));
		imagefilledrectangle($image, 0, 0,($this->arrExportData[$id]["width"]+0)-1, ($this->arrExportData[$id]["height"]+0)-1,$this->composeColor($image,$this->arrExportData[$id]["bgcolor"]));

		// Split the data into rows using ; as separator
		$rows = explode(";", $this->arrExportData[$id]["imageData"]);

		// Iterate through all the rows
		for($i= 0; $i<count($rows); $i++){
			$x=0;
			// Parse all the pixels in this row
			$pixels = explode(",", $rows[$i]);
			// Iterate through the pixels
			for($j=0; $j<count($pixels); $j++){
				// Split the pixel into color and repeat value
				$thispix = explode("_", $pixels[$j]);
				// Reference to color
				$c = $thispix[0];
				// Reference to repeat factor
				$r = (int)$thispix[1];
				//If color is empty (i.e., background pixel) skip
				if ($c==""){
					$x+=$r;
					continue;
					//$c=$this->arrExportData[$id]["bgcolor"];
				}
				// get color
				$color=$this->composeColor($image,$c);
				//draw line
				imageline($image, $x, $i, ($x+$r)-1, $i, $color);
				//set next x pixel position
				$x+=$r;
			}
		}

		return $image;
	}


	// build color object for GD image object
	// Parsee 6 Byte Hex Color string to 3 Byte RGB color
	function composeColor($imgObj,$strHexColor){
		if (strlen($strHexColor)<6){
			//If the hexadecimal code is less than 6 characters, pad with 0
			$strHexColor = str_pad($strHexColor, 6, '0', STR_PAD_LEFT);
		}
		//Convert value from HEX to RRGGBB (3 bytes)
		$rr = hexdec(substr($strHexColor, 0, 2));
		$gg = hexdec(substr($strHexColor, 2, 2));
		$bb = hexdec(substr($strHexColor, 4, 2));
		// Allocate the color
		return imagecolorallocate($imgObj, $rr, $gg, $bb);
	}

}

//needed to validate inclusion of this resource file in main file - FCExporter.php
return 'true';

?>