<?php
/**
 *
 * Takes $_POST sanitizes and returns $sanitized_post
 *
 *
 *
 *
 */
$errorSanitized = 0;
function sanitizeParams($postParams)
{
    $sanitized_post = array ();
    $parametersArray = array();
    $subParamArray = array();
    $returnsubParamArray = array();
    $returnparameters ;
    if(isset($_POST['filename']) && !empty($_POST['filename']))
    {
        $sanitized_post['filename'] = returnSanitized(sanitize_file_name($_POST['filename']), FILTER_SANITIZE_STRING);
        $sanitized_post['type'] = returnSanitized($_POST['type'], FILTER_SANITIZE_STRING);
        $sanitized_post['width'] = returnSanitized($_POST['width'], FILTER_SANITIZE_NUMBER_INT);
        $sanitized_post['svg'] = $_POST['svg'];

    }
    else
    {

        $sanitized_post['charttype'] = returnSanitized($_POST['charttype'], FILTER_SANITIZE_STRING);
        $sanitized_post['stream_type'] = returnSanitized($_POST['stream_type'], FILTER_SANITIZE_STRING);
        $sanitized_post['meta_bgColor'] = returnSanitized($_POST['meta_bgColor'], FILTER_SANITIZE_STRING);
        $sanitized_post['meta_DOMId'] = returnSanitized($_POST['meta_DOMId'], FILTER_SANITIZE_STRING);
        $sanitized_post['meta_width'] = returnSanitized($_POST['meta_width'], FILTER_SANITIZE_NUMBER_INT);
        $sanitized_post['meta_height'] = returnSanitized($_POST['meta_height'], FILTER_SANITIZE_NUMBER_INT);
        $sanitized_post['stream'] = $_POST['stream'];


        $parametersArray = explode("|",$_POST['parameters']);
        $indexCount = 0;
        foreach($parametersArray as $subParams)
        {
            $subParamArray[$indexCount] = explode("=",$subParams);
            $indexCount = $indexCount + 1;
        }

        $subParamArray[0][1] = sanitize_file_name($subParamArray[0][1]);
        $subParamArray[1][1] = sanitize_alphaNum($subParamArray[1][1]);
        $subParamArray[2][1] = sanitize_Action($subParamArray[2][1]);


        $returnsubParamArray [0] = implode("=",$subParamArray[0]);
        $returnsubParamArray [1] = implode("=",$subParamArray[1]);
        $returnsubParamArray [2] = implode("=",$subParamArray[2]);
        $returnsubParamArray [3] = implode("=",$subParamArray[3]);

        $returnparameters = implode("|",$returnsubParamArray);
        $sanitized_post['parameters'] = $returnparameters;

    }
    return $sanitized_post;


}
function sanitize_Action($param)
{
    return "download";

}
function sanitize_alphaNum($param)
{
    if(preg_match("/[^a-z0-9\-]+/i", $param))
    {
        return preg_replace("/[^a-z0-9\-]+/i", "-", $param);
    }
    else
        return $param;

}
function sanitize_file_name( $filename ) {
    $filename_raw = $filename;
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
    // $special_chars = apply_filters('sanitize_file_name_chars', $special_chars, $filename_raw);
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = trim($filename, '.-_');

    // Split the filename into a base and extension[s]
    $parts = explode('.', $filename);

    // Return if only one extension
    if ( count($parts) <= 2 )
        return  $filename  ;

    // Process multiple extensions
    $filename = array_shift($parts);
    $extension = array_pop($parts);
    $mimes = get_allowed_mime_types();

    // Loop over any intermediate extensions. Munge them with a trailing underscore if they are a 2 - 5 character
    // long alpha string not in the extension whitelist.
    foreach ( (array) $parts as $part) {
        $filename .= '.' . $part;

        if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
            $allowed = false;
            foreach ( $mimes as $ext_preg => $mime_match ) {
                $ext_preg = '!^(' . $ext_preg . ')$!i';
                if ( preg_match( $ext_preg, $part ) ) {
                    $allowed = true;
                    break;
                }
            }
            if ( !$allowed )
                $filename .= '_';
        }
    }
    $filename .= '.' . $extension;

    return  $filename ;
}
function returnSanitized ($param,$filter = "")
{
    If($filter != "")
    {
        if(filter_var($param, $filter))
            return filter_var($param, $filter);
        else
            return "";
        //$errorSanitized = $errorSanitized + 1;
    }
}
?>