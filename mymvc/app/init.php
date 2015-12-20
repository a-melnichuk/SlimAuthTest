<?php
include 'bootstrap.php';

    /*
     * Autoloads Models
     * 
     * @param $class_path - model class
     */

function __autoload($class_name) {
    $filename = $class_name . '.php';
    $file = __SITE_PATH . '/model/' . $filename;

    if (file_exists($file) == false)
    {
        return false;
    }
    include ($file);
}

$registry = new app\Registry();
$registry->db = db::getInstance();


