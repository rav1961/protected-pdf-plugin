<?php

class Protected_Pdf_File
{
    private $file_table = 'protected_pdf_files';

    public function is_protected($file_to_check)
    {
        global $wpdb;

        $table = $wpdb->prefix . $this->file_table;

        $sql = "SELECT count(id) as total FROM {$table} WHERE file_url=%s";

        $results = $wpdb->get_results(
            $wpdb->prepare($sql, $file_to_check)
        );

        return !(count($results) === 1 && intval($results[0]->total) === 0);
    }

    public function display_file($file_path)
    {
        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    }
}
