<?php

/**
 *
 * FusionCharts Exporter - 'SVG Conversion Resource' handles
 * FusionCharts (since XTT) Server Side Export feature that
 * helps FusionCharts JavaScript charts to get exported.
 *
 *
 *    @author FusionCharts
 *    @description FusionCharts Exporter (Server-Side - PHP)
 *    @version 0.0.0.1 [ 32 December 2012 ]
 *
 */
/**
 *  ChangeLog / Version History:
 *  ----------------------------
 *
 *
 *  1.0.0.0 [ 31 December 2012 ]
 *
 *
 * 	FEATURES:
 *       - Integrated with new Export feature of FusionCharts XT & FusionCharts Exporter v 2.0
 *       - can save to server side directory
 *       - can provide download or open in browser window/frame other than _self
 *
 * 	ISSUES:
 *
 *
 *
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
 *   @requires	index.php  A file that includes this resource
 *              Java 1.3+ & Apache Batik rasterizer class: Export JavaScript charts
 *              (http://xmlgraphics.apache.org/batik/)
 *
 *
 *   Details
 *   -------
 *
 *   The resource files would have these things as common:
 *
 *   	a) a constant - MIMETYPES that would have a string
 *         containing semicolon separated key value pairs.
 * 		   Each key can be a format name specified in the
 * 		   HANDLERASSOCIATIONS constant. The associated value
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
// ==============================================================================
//   Users are recommended NOT to perform any editing beyond this point.       ==
// ==============================================================================

ini_set('magic_quotes_gpc', 'off');

/* ------------------------- EXPORT RESOURCES -------------------------------- */

// This constant lists the mime types related to each export format this resource handles
// The value is semicolon separated key value pair for each format
// Each key is the format and value is the mime type
define("MIMETYPES", "jpg=image/jpeg;jpeg=image/jpeg;gif=image/gif;png=image/png;pdf=application/pdf;svg=image/svg+xml");
// This constant lists all the file extensions for the export formats
// The value is semicolon separated key value pair for each format
// Each key is the format and value is the file extension
define("EXTENSIONS", "jpg=jpg;jpeg=jpg;gif=gif;png=png;pdf=pdf;svg=svg");

define('TEMP_PATH', 'temp/');
define('BATIK_PATH', 'batik/batik-rasterizer.jar');
define('INKSCAPE_PATH', '/usr/bin/inkscape');
define('CONVERT_PATH', '/usr/bin/convert'); // imagemagic
// =============================================================================
// ==                             Public Functions                            ==
// =============================================================================
class stitchImageCallback {
   private $imageData;

   function __construct($imageData) {
       $this->imageData = $imageData;
   }

   public function callback($matches) {
       $imageRet = '';
       $imageName = explode('/', $matches['2']);
       $imageName = array_pop($imageName);

       foreach ($this->imageData as $key => $value) {
           if ($value->name .'.'.$value->type == $imageName) {
               $imageRet = $value->encodedData;
           }
       }
       if ($imageRet == '') {
           return '';
       }
       return $matches[1] . $imageRet;
   }
}

/**
* The function is use to stitch the image to the SVG when downloaded as SVG
* @param  [string] $svg       [SVG with image link]
* @param  [array] $imageData [Image datauri array]
* @return [string]            [SVG with imageDatauri]
*/
function stitchImageToSvg ($svg, $imageData) {
   if($imageData != null) {
        $imageData = json_decode($imageData);
        $callback = new stitchImageCallback($imageData);
        return preg_replace_callback("/(<image[^>]*xlink:href *= *[\"']?)([^\"']*)/i", array($callback, 'callback'), $svg);
   } else {
        return $svg;
   }

}
/**
 *  Gets Export data from FCExporter - main module and build the export binary/objct.
 *  @param	$stream 	(string) export image data in FusionCharts compressed format
 *      	$meta		{array)	Image meta data in keys "width", "heigth" and "bgColor"
 *              $exportParams   {array} Export related parameters
 *  @return 			image object/binary
 */
function exportProcessor($stream, $meta, $exportParams, $imageData=null) {

    // get mime type list parsing MIMETYPES constant declared in Export Resource PHP file
    $ext = strtolower($exportParams["exportformat"]);
    $ext2 = '';
    $mimeList = bang(@MIMETYPES);
    $mimeType = $mimeList[$ext];

    // prepare variables
    if (get_magic_quotes_gpc()) {
        $stream = stripslashes($stream);
    }

    // create a new export data
    $tempFileName = md5(rand());
    if ('jpeg' == $ext || 'jpg' == $ext) {
        $ext = 'png';
        $ext2 = 'jpg';
    }

    $tempInputSVGFile = realpath(TEMP_PATH) . "/{$tempFileName}.svg";
    $tempOutputFile = realpath(TEMP_PATH) . "/{$tempFileName}.{$ext}";
    $tempOutputJpgFile = realpath(TEMP_PATH) . "/{$tempFileName}.jpg";
    $tempOutputPngFile = realpath(TEMP_PATH) . "/{$tempFileName}.png";

    if ($ext != 'svg') {

        // width format for batik
        $width = @$meta['width'];
        $height = @$meta['height'];

        $size = '';
        if (!empty($width) && !empty($height)) {
            $size = "-w {$width} -h {$height}";
        }
        // override the size in case of pdf output
        if ('pdf' == $ext) {
            $size = '';
        }

        //batik bg color format
        $bg = @$meta['bgColor'];
        if ($bg) {
            $bg = " --export-background=".$bg;
        }

        // generate the temporary file
        if (!file_put_contents($tempInputSVGFile, $stream)) {
            die("Couldn't create temporary file. Check that the directory permissions for
			the " . TEMP_PATH . " directory are set to 777.");
        }

        // do the conversion
        //$command = "java -jar ". BATIK_PATH ." -m $mimeType $width $bg $tempInputSVGFile";
        $command = INKSCAPE_PATH . "$bg --without-gui {$tempInputSVGFile} --export-{$ext} $tempOutputFile {$size}";

        //echo $command;exit;
        $output = shell_exec($command);
        if ('jpg' == $ext2) {
            $comandJpg = CONVERT_PATH . " -quality 100 $tempOutputFile $tempOutputJpgFile";
            $tempOutputFile = $tempOutputJpgFile;

            $output .= shell_exec($comandJpg);
        }

        // catch error
        if (!is_file($tempOutputFile) || filesize($tempOutputFile) < 10) {
            $return_binary = $output;
            raise_error($output, true);
        }
        // stream it
        else {
            $return_binary = file_get_contents($tempOutputFile);
        }

        // delete temp internal image files if exist
        $imageData = json_decode($imageData);
        if ($imageData) {
            foreach ($imageData as $key => $value) {
                $tempInternalImage = realpath(TEMP_PATH) . "/{$value->name}.{$value->type}";
                if (file_exists($tempInternalImage)) {
                    unlink($tempInternalImage);
                }
            }
        }

        // delete temp files
        if (file_exists($tempInputSVGFile)) {
            unlink($tempInputSVGFile);
        }
        if (file_exists($tempOutputFile)) {
            unlink($tempOutputFile);
        }
        if (file_exists($tempOutputPngFile)) {
            unlink($tempOutputPngFile);
        }

        // SVG can be streamed back directly
    } else if ($ext == 'svg') {
        $stream = stitchImageToSvg($stream, $imageData);
        $return_binary = $stream;
    } else {
        raise_error("Invalid Export Format.", true);
    }

    // return export ready binary data
    return @$return_binary;
}

/**
 *  exports (save/download) SVG to export formats.
 *  @param	$exportObj 		(mixed) binary/objct exported by exportProcessor
 * 	@param	$exportSettings	(array) various server-side export settings stored in keys like
 * 					"type", "ready" "filepath" etc. Required for 'save' expotAction.
 * 					For 'download' action "filepath" is blank (this is checked to find
 * 					whether the action is "download" or not.
 * 	@param	$quality		(integer) quality factor 0-1 (1 being the best quality). As of now we always pass 1.
 *
 *  @return 				false is fails. {filepath} if succeeds. Only returned when action is 'save'.
 */
function exportOutput($exportObj, $exportSettings, $quality = 1) {

    // calls svgparser function that saves/downloads binary
    // store saving status in $doneExport which receives false if fails and true on success
    $doneExport = filesaver($exportObj, @$exportSettings ['filepath']);

    // check $doneExport and if true sets status to {filepath}'s value
    // set false if fails
    $status = ( $doneExport ? basename(@$exportSettings ['filepath']) : false );

    // return status
    return $status;
}

/**
 *  emulates imagepng/imagegif/imagejpeg. It saves data to server
 *  @param	$exportObj 	(resource) binary exported by exportProcessor
 *  @param	$filepath	(string) Path where the exported is to be stored
 *                              when the action is "download" it is null.
 *
 *  @return     (boolean) false is fails. true if succeeds. Only returned when action is 'save'.
 */
function filesaver($exportObject, $filepath) {

    // when filepath is null the action is 'download'
    // hence write the  bimary to response stream and immediately terminate/close/end stream
    // to prevent any garbage that might get into response and corrupt the  binary
    if (!@$filepath)
        die($exportObject);

    // open file path in write mode
    $fp = @fopen($filepath, "w");

    if ($fp) {
        // write  binary
        $status = @fwrite($fp, $exportObject);
        //close file
        $fc = @fclose($fp);
    }

    // return status
    return (bool) @$status;
}

/**
 * Converts Hex color values to rgb
 *
 * @param string $h Hex values
 * @param string $sep rgb value separator
 * @return string
 */
function hex2rgb($h, $sep = "") {
    $h = str_replace("#", "", $h);

    if (strlen($h) == 3) {
        $r = hexdec(substr($h, 0, 1) . substr($h, 0, 1));
        $g = hexdec(substr($h, 1, 1) . substr($h, 1, 1));
        $b = hexdec(substr($h, 2, 1) . substr($h, 2, 1));
    } else {
        $r = hexdec(substr($h, 0, 2));
        $g = hexdec(substr($h, 2, 2));
        $b = hexdec(substr($h, 4, 2));
    }
    $rgb = array($r, $g, $b);
    if ($sep) {
        $rgb = implode($sep, $rgb);
    }
    return $rgb;
}

return 'true';
