<?php

/**
 *
 * FusionCharts Exporter is a PHP script that handles
 * FusionCharts (since v3.1) Server Side Export feature.
 * This in conjuncture with other resource PHP scripts would
 * process FusionCharts Export Data POSTED to it from FusionCharts
 * and convert the data to image or PDF and subsequently save to the
 * server or response back as http response to client side as download.
 *
 * Starting FusionCharts XT (v3.3) it is capable of exporting JavaScript charts.
 *
 * This script is named as "FusionCharts Export Handler - main module"
 *
 *    @author FusionCharts
 *    @description FusionCharts Exporter (Server-Side - PHP)
 *    @version 3.3 [ 31 December 2012 ]
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
 *  ChangeLog / Version History:
 *  ----------------------------
 *
 *   3.3 [ 31 December 2012 ]
 *       - Support Export of JavaScript chart (SVG) to allowed export formats
 *         via batik-rasterizer Java library
 *
 *
 *   3.2 [ 4 September 2010 ]
 *       - background color was turning black in a few linux distro - fixed .
 *          The original code is kept commneted. (FCExporter_IMG.php - line: 277)
 *
 *   2.0 [ 12 February 2009 ]
 *       - Integrated with new Export feature of FusionCharts 3.1
 *       - can save to server side directory
 *       - can provide download or open in popup window
 *       - can report back to chart
 *       - can save as PDF/JPG/PNG/GIF
 *
 *   1.0 [ 16 August 2007 ]
 *       - can process chart data to jpg image and response back to client side as download.
 *
 */
/**
 * Copyright (c) 2016 InfoSoft Global Private Limited. All Rights Reserved.
 *
 */
/**
 *  GENERAL NOTES
 *  -------------
 *
 *  Chart would POST export data (which consists of encoded image data stream,
 *  width, height, background color and various other export parameters like
 *  exportFormat, exportFileName, exportAction, exportTargetWindow) to this script.
 *
 *  The script would process this data using appropriate resource files and build
 *  export binary (PDF/image)
 *
 *  It either saves the binary as file to a server side directory or push it as
 *  Download to client side.
 *
 *
 *
 */
/**
 *   @requires	FCExporter_REL2IMG.php: Export Flash charts to PNG/JPG
 *              FCExporter_REL2IMG.php: Export Flash charts to PDF
 *              FCExporter_REL2IMG.php: Export JavaScript charts to all formats
 *
 *              Java 1.3+ & Apache Batik rasterizer class: Export JavaScript charts
 *              (http://xmlgraphics.apache.org/batik/)
 *
 *
 *   Details
 *   -------
 *   Only one export resource would be included at one time.
 *
 *   The resource files would have these things as common:
 *
 *   	a) a constant - MIMETYPES that would have a string
 *         containing semicolon separated key value pairs.
 * 		   Each key can be a format name specified in the
 * 		   HANDLER_ASSOCIATIONS constant. The associated value
 * 		   would be the mimetype for the specified format.
 *
 * 		   e.g. define("MIMETYPES","jpg=image/jpeg;jpeg=image/jpeg;png=image/png;gif=image/gif");
 *
 *
 * 		b) a constant - EXTENSIONS that again contain a string of
 * 		   semicolon separated key value pair. Each key would again be the
 * 		   format name and the extension would be the file extension.
 *
 * 		   e.g. define("EXTENSIONS","jpg=jpg;jpeg=jpg;png=png;gif=gif");
 *
 *
 *      c) a function  - exportProcessor ( $stream , $meta )
 * 		   It would take the FusionCharts exncoded image string as $stream &
 * 		   an associative array $meta containging width, height and bgColor keys.
 *
 *         The function would return an object of mixed type which would contain
 * 		   the processed binary/relevant export object.
 *
 *
 * 		d) a function - exportOutput ( $exportObj, $exportSettings, $quality=1 )
 *         It would take the processed export object and other export setting as parameter.
 *         Moreover, it would take an optional parameter - $quality (in scale of 0 to 1).
 *         By Default, the $quality is passed as 1 (best quality)
 *
 * 		   The function would return the file path on success or return false on failure.
 *
 *      [ The other code in the resource file can be anything that support this architecture ]
 *
 */
// =============================================================================
// ==                  Constants and  Variables                               ==
// =============================================================================
// USERS are to EDIT the values of the following constants.

/* ----------------------- EXPORT PATH & URI -------------------------------- */

/**
 * IMPORTANT: You need to change the location of folder where
 *            the exported chart images/PDFs will be saved on your
 * 			  server. Please specify the path to a folder with
 * 			  write permissions in the constant SAVE_PATH below.
 *
 * 	Please provide the path as per PHP path conventions. You can use relative or
 * 	absolute path.
 *  Examples: './' , '/users/me/images/' , './myimages'
 *
 * 	For Windows servers you can ALSO use \\ as path separator too. e.g. c:\\php\\mysite\\
 */
define("SAVE_PATH", "ExportedImages/");

/**
 * 	IMPORTANT: This constant HTTP_URI stores the HTTP reference to
 * 	           the folder where exported charts will be saved.
 * 			   Please enter the HTTP representation of that folder
 * 			   in this constant e.g., http://www.yourdomain.com/images/
 */
define("HTTP_URI", "ExportedImages/");

// ==============================================================================
//   Users are recommended NOT to perform any editing beyond this point.       ==
// ==============================================================================

/* ------------------------- EXPORT RESOURCES -------------------------------- */

// This constant defines the name of the export handler script file
// The name is appended with a suffix (from constant HANDLER_ASSOCIATIONS)
define("EXPORT_HANDLER", "FCExporter_");

// This constant lists all the currently supported export formats
// and related export handler file suffix.
// e.g. for JPEG the suffix is
define("HANDLER_ASSOCIATIONS", "RLE:PDF=PDF;JPEG=IMG;JPG=IMG;PNG=IMG;GIF=IMG|SVG:SVG=ALL;PDF=ALL;JPEG=ALL;JPG=ALL;PNG=ALL;GIF=ALL");

// default mime types
define( "MIME_TO_FORMAT", "image/jpg=jpg;image/jpeg=jpg;image/gif=gif;image/png=png;application/pdf=pdf;image/svg+xml=svg" );

// Path where the export handler files are located
// Please note that the resource path should be relative to
// FCExporter.php file's directory
// By default the path is "./Resources/"
define("RESOURCE_PATH", "Resources/");


/* ---------------------------- Export  Settings ------------------------------- */

/**
 *  OVERWRITEFILE sets whether the export handler would overwrite an existing file
 *  the newly created exported file. If it is set to false the export handler would
 *  not overwrite. In this case if INTELLIGENTFILENAMING is set to true the handler
 *  would add a suffix to the new file name. The suffix is a randomly generated UUID.
 *  Additionally, you add a timestamp or random number as additional suffix.
 *
 */
define("OVERWRITEFILE", false);
define("INTELLIGENTFILENAMING", true);
define("FILESUFFIXFORMAT", "TIMESTAMP"); // value can be either 'TIMESTAMP' or 'RANDOM'
// List the default exportParameter values taken if not provided by chart
$defaultParameterValues = array(
    "exportfilename" => "FusionCharts",
    "exportaction" => "download",
    "exporttargetwindow" => "_self",
    "exportformat" => "PNG"
);


// Stores server notices if any as string [ to be send back to chart after save ]
$notices = "";

// =============================================================================
// ==                                processing                               ==
// =============================================================================

/**
 * Retrieve export data from POST Request sent by chart
 * Parse the Request stream into export data readable by this script
 *
 * Store export data into an array containing keys 'stream' (contains encoded
 * image data) ; 'meta' ( contains an array with 'width', 'height' and 'bgColor' keys) ;
 * and 'parameters' ( array of all export parameters from chart as keys, like - exportFormat,
 * exportFileName, exportAction etc.)
 */
$exportRequestStream = $_POST;
$exportData = parseExportRequestStream($exportRequestStream);

/**
 * Get the name of the export resource (php file) as per export format
 * Dynamically include the resource. The resource would process the data
 * and perform all export related tasks
 */
$exporterResource = getExporter($exportData ['parameters'] ["exportformat"], $exportData ["streamtype"]);


// if resource is not found terminate with error report
if (!@include( $exporterResource )) {
    raise_error(404, true);
}

/*
 * Pass export stream and meta values to the export processor &
 * get back the export binary
 */
$exportObject = exportProcessor($exportData ['stream'], $exportData ['meta'], $exportData ['parameters']);


/*
 * Send the export binary to output module which would either save to a server directory
 * or send the export file to download. Download terminates the process while
 * after save the output module sends back export status
 */
$exportedStatus = outputExportObject($exportObject, $exportData ['parameters']);


/*
 * Build Appropriate Export Status and send back to chart by flushing the
 * procesed status to http response. This returns status back to chart.
 * [ This is not applicable when Download action took place ]
 */
flushStatus($exportedStatus, $exportData ['meta']);


// =============================================================================
// ==                             terminate  process                          ==
// =============================================================================
#################################################################################
##                                                                             ##
##                           FUNCTION DECLARATION                              ##
##                                                                             ##
#################################################################################
#### ------------------------ INPUT STREAM  -------------------------------- ####

/**
 *  Parses POST stream from chart and builds an array containing
 *  export data and parameters in a format readable by other functions.
 *
 *  @param	$exportRequestStream 	All POST data (array) from chart
 *  @return	An array of processed export data and parameters
 */
function parseExportRequestStream($exportRequestStream) {

    // Check for SVG
    $exportData ['streamtype'] = strtoupper(@$exportRequestStream ['stream_type']);
    // backward compatible SVG stream type detection
    if (!$exportData ['streamtype']) {
        if (@$exportRequestStream ['svg']) {
            $exportData ['streamtype'] = "SVG";
        }
        else {
            $exportData ['streamtype'] = "RLE";
        }
    }

    // get string of compressed/encoded image data
    // halt with error message  if stream is not found
    $exportData ['stream'] = (string)@$exportRequestStream ['stream']
            or $exportData ['stream'] = (string)@$exportRequestStream ['svg'] // backward compatible
            or raise_error(100, true);

    // get all export related parameters and parse to validate and process these
    // add notice if 'parameters' is not retrieved. In that case default values would be taken
    if (!@$exportRequestStream['parameters'])
        raise_error(102);

    // parse parameters
    $exportData ['parameters'] = parseExportParams(@$exportRequestStream ['parameters'], @$exportRequestStream);
    $exportData ['parameters'] ["exportformat"] = strtoupper(@$exportData ['parameters'] ["exportformat"]);

    // get width and height of the chart
    // halt with error message  if width/height is/are not retrieved
    $exportData ['meta']['width'] = (int) @$exportRequestStream ['meta_width']
            or $exportData ['meta']['width'] = (int) @$exportRequestStream ['width'] // backward compatible
            or raise_error(101);
    $exportData ['meta']['height'] = (int) @$exportRequestStream ['meta_height']
            or raise_error(101);

    // get background color of chart
    // add notice if background color is not retrieved
    $exportData ['meta']['bgColor'] = @$exportRequestStream ['meta_bgColor'];

    // chart DOMId
    $exportData ['meta']['DOMId'] = @$exportRequestStream ['meta_DOMId'];

    // return collected and processed data
    return $exportData;
}

/**
 *  Parse export 'parameters' string into an associative array with key => value elements.
 *  Also sync default values from $defaultparameterValues array (global)
 *  @param 	$strParams A string with parameters (key=value pairs) separated  by | (pipe)
 *  @return An associative array of key => value pairs
 */
function parseExportParams($strParams, $exportRequestStream = array()) {
    // get global definition of default parameter values
    global $defaultParameterValues;

    // split string into associative array of [export parameter name => value ]
    $params = bang($strParams, array("|", "="));

    $exportFilename = @$params['exportfilename'];
    $exportFormat = @$params['exportformat'];

     // backward compatible setting to get filename
    if (!$exportFilename) {
        $exportFilename = (string)@$exportRequestStream["filename"];
        if ($exportFilename) {
            $params['exportfilename'] = $exportFilename;
        }
    }
    // backward compatible setting to get exportFormat through mimetype
    if (!$exportFormat) {

        $mimeType = strtolower((string)@$exportRequestStream["type"]);
	$mimeList = bang( @MIME_TO_FORMAT );

        $exportFormat = $mimeList[$mimeType];

        if ($exportFormat) {
           $params['exportformat'] = $exportFormat;
        }
        else {
           $params['exportformat'] = 'png';
        }
    }

    if (is_array($defaultParameterValues)) {
        // sync with default values
        $params = $params + $defaultParameterValues;
    }

    // return parameters' array
    return $params;
}



/**
 *  Builds and returns a path of the Export Resource PHP file needed to
 * 	export the chart to the format specified as parameter.
 *  @param	$strFormat (string) export format specified form chart
 *  @return A path (string) containing the Export Resource PHP file
 * 			the for specified format
 */
function getExporter($strFormat, $streamtype = "RLE") {

    // get array of [format => handler suffix ] from HANDLER_ASSOCIATIONS
    $associationCluster = bang(HANDLER_ASSOCIATIONS, array('|', ':'), true);
    $associations = bang(@$associationCluster[$streamtype], array(";", "="), true);

    // validate and decide on proper suffix form the $associations array
    // if not found take the format as suffix of the Export Resource
    $exporterSuffix = (@$associations [$strFormat]);
    if (!$exporterSuffix) {
        $exporterSuffix = strtoupper($strFormat);
    }


    // build Export Resource PHP file path
    // Add resource path (constant), Export handler (constant) and export suffix
    $path = RESOURCE_PATH . EXPORT_HANDLER . strtoupper($streamtype) . "2{$exporterSuffix}.php";

    return $path;
}

#### ------------------------ OUTPUT EXPORT FILE -------------------------------- ####

/**
 *  Checks whether the export action is download or save.
 *  If action is 'download', send export parameters to 'setupDownload' function.
 *  If action is not-'download', send export parameters to 'setupServer' function.
 *  In either case it gets exportSettings and passes the settings along with
 *  processed export binary (image/PDF) to the output handler function if the
 *  export settings return a 'ready' flag set to 'true' or 'download'. The export
 *  process would stop here if the action is 'download'. In the other case,
 *  it gets back success status from output handler function and returns it.
 *
 *  @param 	$exportObj 		An export binary/object of mixed type (image/PDF)
 *  @param 	$exportParams	An array of export parameters
 *  @return 				export success status ( filename if success, false if not)
 */
function outputExportObject($exportObj, $exportParams) {
    // checks whether the export action is 'download'
    $isDownload = strtolower($exportParams ["exportaction"]) == "download";


    // dynamically call 'setupDownload' or 'setupServer' as per export action
    // pass export paramters and get back export settings in an array
    $exportActionSettings = call_user_func('setup' . ($isDownload ? 'Download' : 'Server'), $exportParams['exportfilename'], $exportParams['exportformat'], $exportParams['exporttargetwindow']
    );

    // check whether export setting gives a 'ready' flag to true/'download'
    // and call output handler
    // return status back (filename if success, false if not success )
    return ( @$exportActionSettings ['ready'] ? exportOutput($exportObj, $exportActionSettings, 1) : false );
}

/**
 *  Flushes exported status message/or any status message to the chart or the output stream on error
 *  It parses the exported status through parser function parseExportedStatus,
 *  builds proper response string using buildResponse function and flushes the response
 *  string to the output stream and terminates the program.
 *  @param	$status		exported status ( false if failed/error, filename as string if success)
 *         	$meta		array containing meta descriptions of the chart like width, height
 * 			$msg		custom message to be added as statusMessage
 *
 */
function flushStatus($status, $meta, $msg = '') {
    die(buildResponse(parseExportedStatus($status, $meta, $msg)));
}

/**
 *  Parses the exported status and builds an array of export status information. As per
 *  status it builds a status array which contains statusCode (0/1), statusMesage, fileName,
 *  width, height, DOMId and notice in some cases.
 *  @param	$status		exported status ( false if failed/error, filename as stirng if success)
 *         	$meta		array containing meta descriptions of the chart like width, height and DOMId
 * 			$msg		custom message to be added as statusMessage
 * 	@return			 	array of status information
 */
function parseExportedStatus($status, $meta, $msg = '') {
    // get global 'notice' variable
    global $notices;
    global $exportData;

    // add notice
    if ($notices)
        $arrStatus [] = "notice=" . @$notices;

    // Add DOMId
    $arrStatus [] = "DOMId=" . @$meta["DOMId"];

    // add file URI , width and height when status success
    // provide 0 as width and height on failure
    $arrStatus [] = "height=" . ( @$status ? @$meta ['height'] : 0 );
    $arrStatus [] = "width=" . ( @$status ? @$meta ['width'] : 0 );
    $arrStatus [] = "fileName=" . ( @$status ? ( preg_replace('/([^\/]$)/i', '${1}/', HTTP_URI) . @$status ) : "" );

    // add status message . Priority 1 is a custom message if provided
    $arrStatus [] = "statusMessage=" . ( trim(@$msg) ? @$msg :
                    ( $status ? "success" : "failure" ));
    // add statusCode to 1 on success
    $arrStatus [] = "statusCode=" . ( @$status ? "1" : "0" );

    // return status information
    return $arrStatus;
}

/**
 *  Builds response from an array of status information. Each value of the array
 *  should be a string which is one [key=value ] pair. This array are either joined by
 *  a & to build a querystring (to pass to chart) or joined by a HTML <BR> to show neat
 *  and clean status informaton in Browser window if download fails at the processing stage.
 *
 *  @param	 $arrMsg	Array of string containing status data as [key=value ]
 *  @return				A string to be written to output stream
 */
function buildResponse($arrMsg) {
    // access global variable to get export action
    global $exportData;

    // check whether export action is download. If so the response output would be at browser
    // i.e. the output format would be HTML
    $isHTML = ( ( $exportData ['parameters']['exportaction'] ) != null ? (strtolower(
                            $exportData ['parameters']['exportaction']) == "download" ) : true );


    // If the output format is not HTML then start building a quertstring hence start with a &
    $msg = ( $isHTML ? "" : "&" );
    // join all status data from array using & or <BE> as per export action
    // Joining with & would convert the string into a querystring as each element already contains
    // key=value.
    $msg .= implode(( $isHTML ? "<BR>" : "&"), $arrMsg);

    // return response
    return $msg;
}

/**
 *  check server permissions and settings and return ready flag to exportSettings
 *  @param 	$exportFile 	Name of the new file to be created
 *  @param 	$exportType		Export type
 *  @param 	$target			target window where the download would happen [ Not required here ]
 *  @return 	An array containing exportSettings and ready flag
 */
function setupServer($exportFile, $exportType, $target = "_self") {
    // get extension related to specified type
    $ext = '.' . getExtension(strtolower($exportType));

    // set export type
    $retServerStatus ['type'] = $exportType;

    // assume that server is ready
    $retServerStatus['ready'] = true;

    // process SAVE_PATH : the path where export file would be saved
    // add a / at the end of path of / is absent at the end
    $path = preg_replace('/([^\/]$)/i', '${1}/', SAVE_PATH);

    // check whether directory exists
    // raise error and halt execution if directory does not exists
    $fe = file_exists(realpath($path)) or raise_error(" Server Directory does not exist.", true);

    // check if directory is writable or not
    $dirWritable = is_writable(realpath($path));

    // build filepath
    $retServerStatus ['filepath'] = realpath($path) . '/' . $exportFile . $ext;

    // check whether file exists
    if (!file_exists($retServerStatus ['filepath'])) {
        // need to create a new file if does not exists
        // need to check whether the directory is writable to create a new file
        if ($dirWritable) {
            // if directory is writable return with ready flag
            return $retServerStatus;
        } else {
            // if not writable halt and raise error
            raise_error(403, true);
        }
    }

    // add notice that file exists
    raise_error(" File already exists.");

    //if overwrite is on return with ready flag
    if (OVERWRITEFILE) {
        // add notice while trying to overwrite
        raise_error(" Export handler's Overwrite setting is on. Trying to overwrite.");

        // see whether the existing file is writable
        // if not halt raising error message
        $iw = is_writable($retServerStatus ['filepath']) or
                raise_error(" Overwrite forbidden. File cannot be overwritten.", true);

        // if writable return with ready flag
        return $retServerStatus;
    }

    // raise error and halt execution when overwrite is off and intelligent naming is off
    if (!INTELLIGENTFILENAMING)
        raise_error(" Export handler's Overwrite setting is off. Cannot overwrite.", true);

    raise_error(" Using intelligent naming of file by adding an unique suffix to the exising name.");
    // Intelligent naming
    // generate new filename with additional suffix
    $retServerStatus ['filepath'] = realpath($path) . '/' . $exportFile . "_" . generateIntelligentFileId() . $ext;

    // return intelligent file name with ready flag
    // need to check whether the directory is writable to create a new file
    if ($dirWritable) {
        // if directory is writable return with ready flag
        // add new filename notice
        raise_error(" The filename has changed to " . basename($retServerStatus ['filepath']) . '.');
        return $retServerStatus;
    } else {
        // if not writable halt and raise error
        raise_error(403, true);
    }

    // in any unknown case the export should not execute
    $retServerStatus ['ready'] = false;
    raise_error(" Not exported due to unknown reasons.");
    return $retServerStatus;
}

/**
 *  setup download headers and return ready flag to exportSettings
 *  @param 	$exportFile 	Name of the new file to be created
 *  @param 	$exportType		Export type
 *  @param 	$target			target window where the download would happen (_self/_blank/_parent/_top/window name)
 *  @return 	An array containing exportSettings and ready flag
 */
function setupDownload($exportFile, $exportType, $target = "_self") {

    $exportType = strtolower($exportType);

    // get mime type list parsing MIMETYPES constant declared in Export Resource PHP file
    $mimeList = bang(@MIMETYPES);

    // get the associated extension for the export type
    $ext = getExtension($exportType);

    // set content-type header
    header('Content-type:' . $mimeList [$exportType]);

    // set content-disposition header
    // when target is _self the type is 'attachment'
    // when target is other than self type is 'inline'
    // NOTE : you can comment this line in order to replace present window (_self) content with the image/PDF
    header('Content-Disposition: ' . ( strtolower($target == "_self") ? "attachment" : "inline" ) . '; filename="' . $exportFile . '.' . $ext . '"');

    // return exportSetting array. Ready should be set to download
    return array('ready' => 'download', "type" => $exportType);
}

/**
 *  gets file extension checking the export type.
 *  @param	$exportType 	(string) export format
 *  @return file extension as string
 */
function getExtension($exportType) {

    // get an associative array of [type=> extension]
    // from EXTENSIONS constant defined in Export Resource PHP file
    $extensionList = bang(@EXTENSIONS);
    $exportType = strtolower($exportType);

    // if extension type is present in $extensionList return it, otherwise return the type
    return ( @$extensionList [$exportType] ? $extensionList [$exportType] : $exportType );
}

/**
 *  generates a file suffix for a existing file name to apply intelligent
 *  file naming
 *  @return 	a string containing UUID and random number /timestamp
 */
function generateIntelligentFileId() {
    //generate UUID
    $UUID = md5(uniqid(rand(), true));

    // chck for additional suffix : timestamp or random
    // accrodingly add random number ot timestamp
    $UUID .= '_' . ( strtolower(FILESUFFIXFORMAT) != "timestamp" ? rand() :
                    date('dmYHis') . '_' . round(microtime(true) - floor(microtime(true)), 2) * 100
            );

    return $UUID;
}

/**
 *  Helper function that splits a string containing delimiter separated key value pairs
 *  into associative array
 *  @param 	$str	(string) delimiter separated key value pairs
 *  @param  $delimiterList	an Array whose first element is the delimiter and the
 * 							second element can be anything which separates key from value
 *
 *  @return An associative array with key => value
 */
function bang($str, $delimiterList = array(";", "="), $retainPropertyCase = false) {

    if (!$delimiterList) {
        $delimiterList = array(";", "=");
    }

    $retArray = array();
    // split string as per first delimiter
    $tmpArray = explode($delimiterList[0], $str);
    // iterate through each element of split string
    for ($i = 0; $i < count($tmpArray); $i++) {
        // split each element as per second delimiter
        $tmp2Array = explode($delimiterList[1], $tmpArray[$i], 2);
        if ($tmp2Array[0] && $tmp2Array[1]) {
            // if the secondary split creats at-least 2 array elements
            // make the fisrt element as the key and the second as the value
            // of the resulting array
            $retArray[$retainPropertyCase ? $tmp2Array[0] : strtolower($tmp2Array[0])] = $tmp2Array[1];
        }
    }
    return $retArray;
}

/**
 *  Error reporter function that has a list of error messages. It can terminate the execution
 *  and send successStatus=0 along with a error message. It can also append notice to a global variable
 *  and continue execution of the program.
 *  @param		$code 	error code as Integer (referring to the index of the errMessages
 * 						array containing list of error messages)
 * 						OR, it can be a string containing the error message/notice
 * 	@param 		$halt 	(boolean) Whether to halt execution
 */
function raise_error($code, $halt = false) {

    // access global notice storage
    global $notices;

    //list of error messages
    $errMessages [100] = " Insufficient data.";
    $errMessages [101] = " Width/height not provided.";
    $errMessages [102] = " Insufficient export parameters.";
    $errMessages [400] = " Bad request.";
    $errMessages [401] = " Unauthorized access.";
    $errMessages [403] = " Directory write access forbidden.";
    $errMessages [404] = " Export Resource not found.";


    // take $code as error message  if $code is string
    // if $code is present as index of errorMessages array, take the value of the element
    // take Error! only when all fails
    $err_message = is_string($code) ? $code :
            ( @$errMessages [$code] ? $errMessages [$code] : "statusMessage=ERROR!" );

    // If halt is true stop execution and send response back to chart/output stream
    if ($halt) {
        flushStatus(false, '', $err_message);
    } else {
        // otherwise add the message into global notice repository
        $notices .= $err_message;
    }
}

?>