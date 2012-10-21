<?php
/**
 * Config file. Edit as needed.
 */
define('APP_PATH' , '/srv/http/VPN'); //NO trailing slash
define('PUBLIC_PATH' , APP_PATH.'/public'); //NO trailing slash

define('LANG_FILE' , APP_PATH.'/lang/english.php'); //NO trailing slash

define('TEMP_DL_FOLDER' , APP_PATH.'/vpn-config-files/');

define('BRUTEFORCE_MINUTES' , 15); //Timespan, see below.
define('BRUTEFORCE_ATTEMPTS' , 10); //Maximum attemts in the given times, before we block the IP temporarily.