<?php

/*
 * ==========================================================
 * UPLOAD.PHP
 * ==========================================================
 *
 * Manage all uploads of front-end and admin. © 2021 board.support. All rights reserved.
 *
 */

include_once('../config.php');
if (defined('SB_CROSS_DOMAIN') && SB_CROSS_DOMAIN) {
    header('Access-Control-Allow-Origin: *');
}

$allowed_extensions = array('json','psd','ai','jpg','jpeg','png','gif','pdf','doc','docx','key','ppt','odt','xls','xlsx','zip','rar','mp3','m4a','ogg','wav','mp4','mov','wmv','avi','mpg','ogv','3gp','3g2','mkv','txt','ico','csv','java','js','xml','unx','ttf','font','css','scss');

if (isset($_FILES['file'])) {
    if (0 < $_FILES['file']['error']) {
        die(json_encode(array('error', 'Support Board: Error into upload.php file.')));
    } else {
        $file_name = sb_sanatize($_FILES['file']['name']);
        $infos = pathinfo($file_name);
        $directory_date = date('d-m-y');
        $path = '../uploads/' . $directory_date;
        $url = SB_URL . '/uploads/' . $directory_date;
        if (isset($infos['extension']) && in_array(strtolower($infos['extension']), $allowed_extensions)) {
            if (defined('SB_UPLOAD_PATH') && SB_UPLOAD_PATH != '' && defined('SB_UPLOAD_URL') && SB_UPLOAD_URL != '') {
                $path = SB_UPLOAD_PATH . '/' . $directory_date;
                $url = SB_UPLOAD_URL . '/' . $directory_date;
            }
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $file_name = rand(1000, 99999) . '_' . $file_name;
            move_uploaded_file($_FILES['file']['tmp_name'], $path . '/' . $file_name);
            die(json_encode(array('success', $url . '/' . $file_name)));
        } else {
            die(json_encode(array('success', 'extension_error')));
        }
    }
} else {
    die(json_encode(array('error', 'Support Board Error: Key file in $_FILES not found.')));
}

function sb_sanatize($string) {
    $string = str_replace(['<script', '</script'], ['&lt;script', '&lt;/script'], $string);
    $string = str_replace(['javascript:', 'onclick=', 'onerror='], '', $string);
    return htmlspecialchars($string, ENT_NOQUOTES | ENT_SUBSTITUTE, 'utf-8');
}

?>