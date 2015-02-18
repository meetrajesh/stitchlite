<?php

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('html_errors', true);

chdir(dirname(__FILE__));

if (file_exists('./init.local.php')) {
    require './init.local.php';
}

session_start();
date_default_timezone_set('America/Los_Angeles');

require './functions.php';

add_define('CSRF_SECRET', '90*9(djk239%29hsad*&672631');
add_define('USER_SALT_LEN', 3);
add_define('API_SECRET', '48askk290sh2876');
add_define('UPLOAD_MAX_SIZE', 10*1024*1024); // 10M
add_define('STATIC_PREFIX', '/static');
add_define('WEB_ROOT', dirname(__FILE__) . '/public');
add_define('TEMPLATE_ROOT', dirname(__FILE__) . '/views');

// clean up $_GET and $_POST, ensure all values are strings (no arrays)
foreach (array('_GET', '_POST', '_REQUEST', '_COOKIE') as $sglobal) {
    foreach ($$sglobal as $k => $v) {
        ${$sglobal}[$k] = trim((string) $v);
    }
}
