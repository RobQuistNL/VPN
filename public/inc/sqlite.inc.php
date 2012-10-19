<?php
class DB {

	private $handle=null;
	
	function __construct() {
		$this->handle = sqlite_open(APP_PATH.'/db/sqlite.db', 0666, $error);
		if (!$this->handle)  {
			throw new Exception($error);
		}
	}
	
	function install() {
		$stm = "CREATE TABLE logins (Id integer PRIMARY KEY," . 
       "ipaddress varchar(39) NOT NULL, logged_time datetime, username varchar(100))";
		@$q = sqlite_exec($this->handle, $stm, $error);
		if (!$q) {
		   throw new Exception ("Cannot install database. $error");
		}
	}
	
	function putLogin($username) {
		$query = "INSERT INTO logins (ipaddress,logged_time,username) VALUES ('".$_SERVER["REMOTE_ADDR"]."',DATETIME('now'),'".sqlite_escape_string($username)."')";
		@$q = sqlite_query($this->handle, $query);
		
		if (!$q) {
		   throw new Exception ("Cannot query database. $error");
		}
	}
	
	function getLoginsSince($minutes) {
		$query = "SELECT COUNT(*) as times
FROM logins 
WHERE logged_time >= DATETIME('now', '-".($minutes*60)." seconds')";
		@$q = sqlite_query($this->handle, $query);
		if (!$q) {
		   throw new Exception ("Cannot query database. $error");
		}
		
		$r=sqlite_fetch_array($q, SQLITE_NUM); 
		return $r[0];
	}
	
	
	
}
?>