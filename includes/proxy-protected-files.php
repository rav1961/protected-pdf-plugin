<?php
require_once dirname(__FILE__) . '/class-protected-pdf-file.php';

if (!defined('PLUGIN_PROXY_FILE_PATH')) {
    define('PLUGIN_PROXY_FILE_PATH', 'protected-pdf-plugin/includes/proxy-protected-files.php');
}

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);

if (is_array($parse_uri) && count($parse_uri) > 1) {
    ini_set('include_path', $parse_uri[0]);
}


$protectedFile = new Protected_Pdf_File();

// check format file
if (strpos($_SERVER['SCRIPT_NAME'], PLUGIN_PROXY_FILE_PATH) !== false) {
    require $parse_uri[0] . 'wp-load.php';

    $file_fullname_path = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . $_SERVER['REDIRECT_URL'];
    $file_fullname_uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // check protected files in database table
    if ($protectedFile->is_protected($file_fullname_uri) && (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe')) {
        wp_die(__('Access denied', 'protected-pdf'));
    } else {
        $protectedFile->display_file($file_fullname_path);
    }
}
