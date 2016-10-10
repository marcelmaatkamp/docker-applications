<?php
/**
 *
 * FusionCharts Exporter - 'PDF Resource' handles
 * FusionCharts (since v3.1) Server Side Export feature that
 * helps FusionCharts exported as PDF file.
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
 *  1.1  [ 13 March 2009 ]
 *  Fixed PDF Image data Compressor
 *
 *
 *
 *   1.0 [ 12 February 2009 ]
 *
 *	FEATURES:
 *       - Integrated with new Export feature of FusionCharts 3.1 & FusionCharts Exporter v 2.0
 *       - can save to server side directory
 *       - can provide download or open in browser window/frame other than _self
 *       - can save single or multiple page PDF
 *
 *	ISSUES:
 * 		 - best viewed in 72 DPI
 *		 - Each page is of the same pixel size that of the chart
 *		 - no page margin or gutter as of now
 *		 - no custom text or other PDF elements can be incorporated
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
 *  ISSUES
 *  ------
 *   Q> What if someone wishes to open in the same page where the chart existed as postback
 *      replacing the old page?
 *
 *   A> Not directly supported using any chart attribute or parameter but can do by
 *      removing/commenting the line containing 'header( content-disposition ...'
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
// ==============================================================================
//   Users are recommended NOT to perform any editing beyond this point.       ==
// ==============================================================================

/* ------------------------- EXPORT RESOURCES -------------------------------- */

// This constant lists the mime types related to each export format this resource handles
// The value is semicolon separated key value pair for each format
// Each key is the format and value is the mime type
define( "MIMETYPES" , "pdf=application/pdf" );

// This constant lists all the file extensions for the export formats
// The value is semicolon separated key value pair for each format
// Each key is the format and value is the file extension
define( "EXTENSIONS", "pdf=pdf" );


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
function exportProcessor( $stream , $meta, $exportParams )
{

	// create a new export object
	// here it is an PDF generator class
	// pass all reqiured parameters
	$FCExporter = new FCPDFGenerator ( $stream, $meta ['width' ], $meta ['height' ], $meta ['bgColor' ] );

	// return export ready PDF binary data
	return $FCExporter->getPDFObjects();
}

/**
 *  exports (save/download) FusinoCharts exported PDF.
 *  @param	$exportObj 		(mixed) binary/objct exported by exportProcessor
 *	@param	$exportSettings	(array) various server-side export settings stored in keys like
 *									"type", "ready" "filepath" etc. Required for 'save' expotAction.
 *									For 'download' action "filepath" is blank (this is checked to find
 *									whether the action is "download" or not.
 *	@param	$quality		(integer) quality factor 0-1 (1 being the best quality). As of now we always pass 1.
 *
 *  @return 				false is fails. {filepath} if succeeds. Only returned when action is 'save'.
 */
function exportOutput ( $exportObj, $exportSettings, $quality = 1 )
{

	// calls imagepdf function that saves/downloads PDF binary
	// store saving status in $doneExport which receives false if fails and true on success
	$doneExport = imagepdf ( $exportObj, @$exportSettings ['filepath'] );

	// check $doneEport and if true sets status to {filepath}'s value
	// set false if fails
	$status =( $doneExport ? basename ( @$exportSettings ['filepath'] ) : false );

	// return status
	return $status;

}

/**
 *  emulates imagepng/imagegif/imagejpeg. It saves PDF data to server or to sets to download as per
 *  the exportAction. exportAction is 'download' when {filepath} is null.
 *  @param	$exportObj 	(string) PDF binary exported by exportProcessor
 *	@param	$filepath	(string) Path where the exported PDF is to be stored
 *									when the action is "download" it is null.
 *
 *  @return 			(boolean) false is fails. true if succeeds. Only returned when action is 'save'.
 */
function imagepdf ( $exportObject , $filepath )
{

	// when filepath is null the action is 'download'
	// hence write the PDF bimary yo response stream and immediately terminate/close/end stream
	// to prevent any garbage that might get into response and corrupt the PDF binary
	if ( !@$filepath ) die ( $exportObject );

	// open file path in write mode
	$fp = @fopen ( $filepath , "w" );

	if( $fp )
	{
		// write PDF binary
		$status = @fwrite( $fp , $exportObject );
		//close file
		$fc = @fclose( $fp );
	}

	// return status
	return ( bool ) @$status;

}


#################################################################################
##                                                                             ##
##                     	 		 EXPORT CLASS             		               ##
##                                                                             ##
#################################################################################

class FCPDFGenerator
{
	//Array - Stores multiple chart export data
	var $arrExportData;
	//stores number of pages = length of $arrExportData array
	var $numPages=0;

	//Constructor - By default the chart-export-data can be passed to this
	function FCPDFGenerator($imageData_FCFormat="", $width="", $height="", $bgcolor="ffffff"){
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
	//create image PDF object containing the chart image
	function addImageToPDF($id=0,$isCompressed=true){
		//PDF Object number
		$imgObjNo = 6 + $id*3;

		//Get chart Image binary
		$baImg=$this->getBitmapData24($id);
		//Compress image binary
		$imgBinary = $isCompressed?gzcompress($baImg, 9):$baImg;
		//get the lenght of the image binary
		$len=strlen($imgBinary);
		//Build PDF object containing the image binary and other formats required
		$imgObj=$imgObjNo." 0 obj\n<<\n/Subtype /Image /ColorSpace /DeviceRGB /BitsPerComponent 8 /HDPI 72 /VDPI 72 ".($isCompressed?"/Filter /FlateDecode ":"")."/Width ".$this->arrExportData[$id]["width"]." /Height ".$this->arrExportData[$id]["height"]." /Length ".$len." >>\nstream\n".$imgBinary."endstream\nendobj\n";


		return $imgObj;

	}

	//Main PDF builder function
	function getPDFObjects($isCompressed=true) {

		$PDFBytes="";
		//Store all PDF objects in this temporary string to be written to ByteArray
		$strTmpObj="";

		//start xref array
		$xRefList[0]="xref\n0 ";
		$xRefList[1]="0000000000 65535 f \n"; //Address Refenrece to obj 0

		//Build PDF objects sequentially
		//version and header
		$strTmpObj="%PDF-1.3\n%{FC}\n";
		$PDFBytes.=$strTmpObj;

		//OBJECT 1 : info (optional)
		$strTmpObj="1 0 obj<<\n/Author (FusionCharts)\n/Title (FusionCharts)\n/Creator (FusionCharts)\n>>\nendobj\n";
		$xRefList[]=$this->calculateXPos(strlen($PDFBytes)); //refenrece to obj 1
		$PDFBytes.=$strTmpObj;

		//OBJECT 2 : Starts with Pages Catalogue
		$strTmpObj="2 0 obj\n<< /Type /Catalog /Pages 3 0 R >>\nendobj\n";
		$xRefList[]=$this->calculateXPos(strlen($PDFBytes)); //refenrece to obj 2
		$PDFBytes.=$strTmpObj;

		//OBJECT 3 : Page Tree (reference to pages of the catalogue)
		$strTmpObj="3 0 obj\n<<  /Type /Pages /Kids [";
		for($i=0;$i<$this->numPages;$i++){
			$strTmpObj.=((($i+1)*3)+1)." 0 R\n";
		}
		$strTmpObj.="] /Count ".$this->numPages." >>\nendobj\n";

		$xRefList[]=$this->calculateXPos(strlen($PDFBytes)); //refenrece to obj 3
		$PDFBytes.=$strTmpObj;


		//Each image page
		for($itr=0;$itr<$this->numPages;$itr++){
			$iWidth=$this->arrExportData[$itr]["width"];
			$iHeight=$this->arrExportData[$itr]["height"];
			//OBJECT 4..7..10..n : Page config
			$strTmpObj=((($itr+2)*3)-2)." 0 obj\n<<\n/Type /Page /Parent 3 0 R \n/MediaBox [ 0 0 ".$iWidth." ".$iHeight." ]\n/Resources <<\n/ProcSet [ /PDF ]\n/XObject <</R".($itr+1)." ".(($itr+2)*3)." 0 R>>\n>>\n/Contents [ ".((($itr+2)*3)-1)." 0 R ]\n>>\nendobj\n";
			$xRefList[]=$this->calculateXPos(strlen($PDFBytes)); //refenrece to obj 4,7,10,13,16...
			$PDFBytes.=$strTmpObj;


			//OBJECT 5...8...11...n : Page resource object (xobject resource that transforms the image)
			$xRefList[]=$this->calculateXPos(strlen($PDFBytes)); //refenrece to obj 5,8,11,14,17...
			$PDFBytes.=$this->getXObjResource($itr);

			//OBJECT 6...9...12...n : Binary xobject of the page (image)
			$imgBA=$this->addImageToPDF($itr,$isCompressed);
			$xRefList[]=$this->calculateXPos(strlen($PDFBytes));//refenrece to obj 6,9,12,15,18...
			$PDFBytes.=$imgBA;
		}



		//xrefs	compilation
		$xRefList[0].=(count($xRefList)-1)."\n";

		//get trailer
		$trailer=$this->getTrailer(strlen($PDFBytes) ,count($xRefList)-1);

		//write xref and trailer to PDF
		$PDFBytes.=implode("",$xRefList);
		$PDFBytes.=$trailer;

		//write EOF
		$PDFBytes.="%%EOF\n";

		return $PDFBytes;

	}


	//Build Image resource object that transforms the image from First Quadrant system to Second Quadrant system
	function getXObjResource($itr=0) {
		return ((($itr+2)*3)-1)." 0 obj\n<< /Length ".(24+strlen($this->arrExportData[$itr]["width"].$this->arrExportData[$itr]["height"]))." >>\nstream\nq\n".$this->arrExportData[$itr]["width"]." 0 0 ".$this->arrExportData[$itr]["height"]." 0 0 cm\n/R".($itr+1)." Do\nQ\nendstream\nendobj\n";
	}

	// Calculate the XREF position of eah PDF Object
	function calculateXPos($posn){
		return (str_pad($posn, 10, '0', STR_PAD_LEFT))." 00000 n \n";
	}

	// Calculate and build Trailer of PDF
	function getTrailer($xrefpos,$numxref=7) {
		return "trailer\n<<\n/Size ".$numxref."\n/Root 2 0 R\n/Info 1 0 R\n>>\nstartxref\n".$xrefpos."\n";
	}


	// Parse Chart Image data in to PDF Ready Image Data
	function getBitmapData24($id=0){
		$imageData24="";

		// Split the data into rows using ; as separator
		$rows = explode(";", $this->arrExportData[$id]["imageData"]);

		// Detect the background color
		if (!($this->arrExportData[$id]["bgcolor"])){
			$this->arrExportData[$id]["bgcolor"] = "ffffff";
		}

		// Iterate through all the rows
		for($i= 0; $i<count($rows); $i++){
			// Parse all the pixels in this row
			$pixels = explode(",", $rows[$i]);
			// Iterate through the pixels
			for($j=0; $j<count($pixels); $j++){
				// Split the pixel into color and repeat value
				$thispix = explode("_", $pixels[$j]);
				// Reference to color
				$c = $thispix[0];
				// Reference to repeat factor
				$r = ( int ) $thispix[1];
					//If color is not empty (i.e., not background pixel)
					if ($c==""){
						$c=$this->arrExportData[$id]["bgcolor"];
					}
					if (strlen($c)<6){
						//If the hexadecimal code is less than 6 characters, pad with 0
						$c = str_pad($c, 6, '0', STR_PAD_LEFT);
					}
					$rgb = pack("CCC",hexdec(substr($c, 0, 2)),hexdec(substr($c, 2, 2)),hexdec(substr($c, 4, 2)));

					$strPixelRGB_hex=str_repeat(($rgb),$r);
					// Set the pixel
					$imageData24.= $strPixelRGB_hex;
			}
		}

		return $imageData24;
	}


}


return 'true';
?>