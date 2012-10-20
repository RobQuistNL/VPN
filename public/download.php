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
if (!isset($_SESSION['username'])) {
	die;
}
if (!isset($_SESSION['ip'])) {
	die;
}
/* DEBUG DATA */
error_reporting(E_ALL);
ini_set('display_errors',1);

/* Include Config */
require "inc/config.inc.php";

/* Include ZF2 */
require "inc/embed_zf2.inc.php";

if ($_SESSION['ip']!=$_SERVER["REMOTE_ADDR"]) {
	header('Location: index.php');
}
	
	function Zip($source, $destination){
    if (extension_loaded('zip') === true)    {
        if (file_exists($source) === true)        {
                $zip = new ZipArchive();
                if ($zip->open($destination, ZIPARCHIVE::CREATE) === true)                {
                        $source = realpath($source);
                        if (is_dir($source) === true)                        {
                                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
                                foreach ($files as $file)                              {
                                        $file = realpath($file);
                                        if (is_dir($file) === true)                                        {
                                                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                                        }
                                        else if (is_file($file) === true)                                        {
                                                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                                        }
                                }
                        }
                        else if (is_file($source) === true)                        {
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
	mkdir(TEMP_DL_FOLDER.'/'.$_SESSION['username'],0777); //Make the dir
	mkdir(TEMP_DL_FOLDER.'/'.$_SESSION['username'].'/config',0777); 
	mkdir(TEMP_DL_FOLDER.'/'.$_SESSION['username'].'/config/'.$_SESSION['username'].'@vpn.enrise.com/',0777); 
	$configFolder=TEMP_DL_FOLDER.'/'.$_SESSION['username'].'/config/'.$_SESSION['username'].'@vpn.enrise.com';
	$fh = fopen($configFolder.'/'.$_SESSION['username'].'@vpn.enrise.com.ovpn', 'w');
	fwrite($fh, $ovpnContent);
	fclose($fh);
} catch (Exception $e) {
	echo 'Files could not be generated! Please contact an administrator.';
}

Zip(TEMP_DL_FOLDER.'/'.$_SESSION['username'],TEMP_DL_FOLDER.'/'.$_SESSION['username'].'.zip');

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$_SESSION['username'].'-vpn.zip');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize(TEMP_DL_FOLDER.'/'.$_SESSION['username'].'.zip'));
ob_clean();
flush();
readfile(TEMP_DL_FOLDER.'/'.$_SESSION['username'].'.zip');
exit;

?>

