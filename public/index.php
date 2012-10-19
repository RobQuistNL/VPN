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
error_reporting(E_ALL);
ini_set('display_errors',1);

/* Include Config */
require "inc/config.inc.php";

/* Include ZF2 */
require "inc/embed_zf2.inc.php";

/* Database handler */
require "inc/sqlite.inc.php";

/* Include the most simplistic templateparser & languageparser & bootstrap generator */
require "inc/templateParser.inc.php";
require "inc/bootStrapper.inc.php";
require "inc/languageParser.inc.php";


/* Catch page */
$page='home';
if (isset($_GET["p"])) {
	$page=$_GET["p"];
}
$BS=new BootStrapper();
$lang=new LanguageParser();

$TP=new SimpleTemplateParser();
$TP->setTemplate('base_template.phtml');

$DB=new DB;
/* 
***************************** TO INSTALL, RUN THIS. 
$DB->install();
*/


switch ($page) {
	
	case 'home':
		$TP->setTitle($lang->t('login'));
		$TP->setContent($BS->heroUnit($lang->t('hometitle'),$lang->t('hometext')));
		$TP->appendContent($BS->row(
									$BS->block(12,$BS->loginForm($lang->t('username'),$lang->t('password'),$lang->t('signin'),'login.html'))
									)
							);
	break;
	
	case 'login':
		$TP->setTitle($lang->t('login'));
		if ($DB->getLoginsSince(BRUTEFORCE_MINUTES)>BRUTEFORCE_ATTEMPTS) {
			echo 'Bruteforce detected';
			die;
		}
		$options = array(
			'host'                   => 'dc01.enrise.com',
			'useStartTls'            => false,
			//'username'               => 'vpn01.public.cyso.enrise.net',
			//'password'               => 'We4rarusas5mub2a',
			'username'               => $_POST['username'],
			'password'               => $_POST['password'],
			'accountDomainName'      => 'enrise.com',
			'baseDn'                 => 'DC=enrise,DC=com',
		);
		$ldap = new Zend\Ldap\Ldap($options);
		try {
			$result = $ldap->search('(&(objectClass=user)(memberOf:1.2.840.113556.1.4.1941:=CN=VPN,OU=Roles,DC=enrise,DC=com))','dc=enrise,dc=com');
		} catch (Exception $e) {
			if (substr($e->getMessage(),0,4)=='0x31') {
				$DB->putLogin($_POST["username"]);
				$TP->setContent($BS->errormessage($lang->t('invalid_credentials')));
				$TP->appendContent($BS->row(
									$BS->block(12,$BS->loginForm($lang->t('username'),$lang->t('password'),$lang->t('signin'),'login.html'))
									)
							);
			} else {
				$TP->setContent($BS->errormessage($lang->t('ldap_server_not_reachable')));
				$TP->appendContent($BS->row(
									$BS->block(12,$BS->loginForm($lang->t('username'),$lang->t('password'),$lang->t('signin'),'login.html'))
									)
							);
			}
			break;
		}
		
		$allowed=0;
		$user=$_POST["username"];
		foreach ($result as $item) {
			if ($item['samaccountname'][0]==$user) {
				$allowed=1;
			}
		}
		$TP->appendContent($BS->successmessage($lang->t('loggedin')));
		
		if ($allowed==1) {
			$_SESSION["username"]=$_POST['username'];
			$_SESSION["ip"]=$_SERVER["REMOTE_ADDR"]; //Session stealing security / logging 
			
			$TP->appendContent($BS->row(
									$BS->block(3,'<H2>Windows</H2><a href="download.php?kind=win">Download .zip</a>') .
									$BS->block(3,'<H2>Windows + Installer</H2><a href="download.php?kind=winmsi">Download .zip</a>') .
									$BS->block(3,'<H2>Linux</H2><a href="download.php?kind=linux">Download .zip</a>') .
									$BS->block(3,'<H2>Mac</H2><a href="download.php?kind=mac">Download .zip</a>')
									)
							);
		} else {
			$TP->appendContent($BS->errormessage($lang->t('vpn_not_allowed')));
		}
		
	break;
	
	default: //404
		$TP->setContent(
			$BS->row(
					$BS->block(12,'<H2>'.$lang->t('404title').'</H2><p>'.$lang->t('404text').'</p>')
				)
		);
	break;

}

echo $TP->getOutput();
?>

