<?php
///phpinfo();
//die;

/**
 * VPN Portal (http://www.enrise.com/)
 *
 * @link      http://github.com/enrise/VPN for the canonical source repository
 * @copyright Copyright (c) 2012 Enrise BV.
 * @license   FreeBSD <LICENSE.MD>
**/

/* Include ZF2 */
error_reporting(E_ALL);
ini_set('display_errors',1);

$zf2Path = '/srv/http/VPN/vendor/zf2/library';

if ($zf2Path) {
    if (isset($loader)) {
        $loader->add('Zend', $zf2Path);
    } else {
        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));
    }
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}

/* APPLICATION */
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\Ldap as AuthAdapter;
use Zend\Config\Reader\Ini as ConfigReader;
use Zend\Config\Config;

//$username = $this->_request->getParam('username');
//$password = $this->_request->getParam('password');
$username='rquist';
$password='asdf';

$auth = new AuthenticationService();

$configReader = new ConfigReader();
$configData = $configReader->fromFile('../ldap-config.ini');
$config = new Config($configData, true);

$options = $config->production->ldap->toArray();


$adapter = new AuthAdapter($options,
                           $username,
                           $password);

$result = $auth->authenticate($adapter);

$messages = $result->getMessages();

foreach ($messages as $i => $message) {
	if ($i-- > 1) { // $messages[2] and up are log messages
		$message = str_replace("\n", "<br/>", $message);
		echo $message;
	}
}

echo 'ZF2 werkt?';

/* Other OLD stuff */
//$result = $ldap->search('(&(memberOf:1.2.840.113556.1.4.1941:=CN=VPN,OU=Roles,DC=enrise,DC=com))','dc=enrise,dc=com');
//$result = $ldap->search('(&(OU=Employees,DC=enrise,DC=com)(objectClass=user))','dc=enrise,dc=com');
//$result = $ldap->search('(&(OU:DN:=Employees)(objectClass=user))','DC=enrise,DC=com');
//$result = $ldap->search('(&(objectClass=user)(OU:DN:=Employees))','dc=enrise,dc=com');
//$result = $ldap->search('(&(objectClass=person)(|(ou:dn:=Employees)(ou:dn:=RandApp)))','dc=enrise,dc=com');

?>