<?php
if (!defined('ABSPATH')) {
    die('Invalid request.');
}

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

$tableMembers = $wpdb->prefix . PROTECTED_PDF_MEMBERS_TABLE;
$tableFiles = $wpdb->prev . PROTECTED_PDF_FILE_TABLE;

$sql = "DROP TABLE IF EXISTS {$tableFiles}, {$tableMembers}";
$wpdb->query($sql);
