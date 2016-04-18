<?php
$includePath = '.' . PATH_SEPARATOR . 'app';
getIncludePath('app', $includePath);
set_include_path($includePath);

function getIncludePath($dir, &$includePath) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..' || $file == 'config') {
            continue;
        }
        if (is_dir($dir . '/' . $file)) {
            $includePath .= PATH_SEPARATOR . $dir . '/' . $file;
            getIncludePath($dir . '/' . $file, $includePath);
        }
    }
}

function __autoload($className) {
    require $className . '.php';
}