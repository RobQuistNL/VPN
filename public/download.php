<?php
/**
 * VPN Portal (http://www.enrise.com/)
 *
 * @link      http://github.com/enrise/VPN for the canonical source repository
 * @copyright Copyright (c) 2012 Enrise BV.
 * @license   FreeBSD <LICENSE.MD>
**/
/* Start session */
session_start();

/* DEBUG DATA */
//error_reporting(E_ALL);
//ini_set('display_errors',1);

/* Include Config */
require "inc/config.inc.php";

/* Include ZF2 */
require "inc/embed_zf2.inc.php";

/* Catch not logged-in and stolen sessions */
if (!isset($_SESSION['username']) || !isset($_SESSION['ip'])) {
    header('Location: index.php');
    die;
}
if ($_SESSION['ip']!=$_SERVER["REMOTE_ADDR"]) {
    header('Location: index.php');
    die;
}

function deleteDir($dirPath) {
    /* Recursive deletion */
    if (substr($dirPath, strlen($dirPath) - 1, 1) != DIRECTORY_SEPARATOR ) {
        $dirPath .= DIRECTORY_SEPARATOR ;
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}
    
function Zip($source, $destination) {
    /* A function to zip a whole directory, recursively. */
    if (extension_loaded('zip') === true) {
        if (file_exists($source) === true) {
            $zip = new ZipArchive();
            if ($zip->open($destination, ZIPARCHIVE::CREATE) === true) {
                $source = realpath($source);
                if (is_dir($source) === true) {
                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($files as $file) {
                        $file = realpath($file);
                        if (is_dir($file) === true) {
                            $zip->addEmptyDir(str_replace($source . DIRECTORY_SEPARATOR , '', $file . DIRECTORY_SEPARATOR ));
                        }
                        else if (is_file($file) === true) {
                            $zip->addFromString(str_replace($source . DIRECTORY_SEPARATOR , '', $file), file_get_contents($file));
                        }
                    }
                }
                else if (is_file($source) === true) {
                    $zip->addFromString(basename($source), file_get_contents($source));
                }
            }

            return $zip->close();
        }
    }
    return false;
}
    
$ovpnContent=<<<TXT

client
dev tun
proto tcp
remote vpn.enrise.com 443
tls-remote "/C=nl/L=Amersfoort/O=4worx/CN=vpnhost.enrise.com/emailAddress=systeembeheer@4worx.com"
resolv-retry infinite
nobind
persist-key
persist-tun
ca vpn.enrise.com.ca.crt
cert vpn.enrise.com.user.crt
key vpn.enrise.com.user.key
auth-user-pass
cipher AES-256-CBC
auth SHA1
comp-lzo
route-delay 4
verb 3
reneg-sec 0

TXT;
try {
    //Remove the old files (if there are any)
    deleteDir(TEMP_DL_FOLDER . DIRECTORY_SEPARATOR  . $_SESSION['username']);
    //Remove old .zip
    unlink(TEMP_DL_FOLDER . DIRECTORY_SEPARATOR . $_SESSION['username'] . '.zip');
    
    //Create folder structure
    mkdir(TEMP_DL_FOLDER . DIRECTORY_SEPARATOR  . $_SESSION['username'], 0777); //Make the dir
    mkdir(TEMP_DL_FOLDER . DIRECTORY_SEPARATOR  . $_SESSION['username'] . DIRECTORY_SEPARATOR . 'config', 0777); 
    mkdir(TEMP_DL_FOLDER . DIRECTORY_SEPARATOR  . $_SESSION['username'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $_SESSION['username'] . '@vpn.enrise.com', 0777); 
    
    $configFolder = TEMP_DL_FOLDER . DIRECTORY_SEPARATOR  . $_SESSION['username'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $_SESSION['username'] . '@vpn.enrise.com';
    
    //Write config file
    $fh = fopen($configFolder . DIRECTORY_SEPARATOR  . $_SESSION['username'] . '@vpn.enrise.com.ovpn', 'w');
    fwrite($fh, $ovpnContent);
    fclose($fh);
    
    //Add specific files for different OS's
    switch ($_GET['kind']) {
        case 'winexe':
            copy(TEMP_DL_FOLDER . 'Viscosity.exe', TEMP_DL_FOLDER . DIRECTORY_SEPARATOR  . $_SESSION['username'] . DIRECTORY_SEPARATOR . 'Viscosity Installer.exe');
            break;
        
        case 'mac':
            copy(TEMP_DL_FOLDER . 'Viscosity.dmg', TEMP_DL_FOLDER . DIRECTORY_SEPARATOR  . $_SESSION['username'] . DIRECTORY_SEPARATOR . 'Viscosity Installer.dmg');
            break;
            
        case 'linux':
            copy(TEMP_DL_FOLDER . 'linux.pkg', TEMP_DL_FOLDER . DIRECTORY_SEPARATOR  . $_SESSION['username'] . DIRECTORY_SEPARATOR . 'Viscosity Installer.pkg');
            break;
            
    }
    
} catch (Exception $e) {
    echo 'Files could not be generated! Please contact an administrator.';
}

Zip(TEMP_DL_FOLDER . DIRECTORY_SEPARATOR . $_SESSION['username'], TEMP_DL_FOLDER . DIRECTORY_SEPARATOR . $_SESSION['username'] . '.zip');

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$_SESSION['username'].'-vpn.zip');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize(TEMP_DL_FOLDER.DIRECTORY_SEPARATOR .$_SESSION['username'].'.zip'));
ob_clean();
flush();
readfile(TEMP_DL_FOLDER.DIRECTORY_SEPARATOR .$_SESSION['username'].'.zip');
exit;
