<?php
// $includePath = get_include_path();
$includePath = '.' . PATH_SEPARATOR . 'app';
getIncludePath('app', $includePath);
set_include_path($includePath);

function getIncludePath($dir, &$includePath) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (is_dir($dir . '/' . $file)) {
            $includePath .= PATH_SEPARATOR . $dir . '/' . $file;
            getIncludePath($dir . '/' . $file, $includePath);
        }
    }
}

function __autoload($className) {
    if (substr($className, -10) != 'Controller') {
        require $className . '.php';
        return;
    }
    $classes = array(
                    'Base',
                    'Base_Controller',
                    'Frontend_Controller',
                    'Backend_Controller',
                );
    if (in_array($className, $classes)) {
        require $className . '.php';
        return;
    }
    $temp = explode('_', strtolower($className));
    $count = count($temp);
    $file = 'app/' . $temp[$count-1] . '/' . $temp[$count-2] . '/' . $temp[$count-3] . '/' . $className . '.php';
    if (is_file($file)) {
        require $className . '.php';
    }
}