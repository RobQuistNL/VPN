<?php
/**
 * Config file. Edit as needed.
 */
define('APP_PATH', realpath(__DIR__.'/../../'));

define('PUBLIC_PATH', APP_PATH . '/public'); //NO trailing slash

define('LANG_FILE', APP_PATH . '/lang/english.php'); //NO trailing slash

define('TEMP_DL_FOLDER', APP_PATH . '/vpn-config-files/'); //Where the downloadable ZIP's will be stored.

define('BRUTEFORCE_MINUTES', 15); //Timespan, see below.
define('BRUTEFORCE_ATTEMPTS', 10); //Maximum attemts in the given times, before we block the IP temporarily.

//VPN File storage
define('VPN_USERS_PATH', '/etc/openvpn/users.conf.d');
define('VPN_KEYS_PATH', '/etc/openvpn/easy-rsa');
