<?php
class LanguageParser {
	
	private $langfile='';
	private $text_array=array();
	
	public function __construct() {
		$langfile=LANG_FILE;
		if (!file_exists($langfile)) {
			throw new Exception('Language file does not exist. Change language file in configfile!');
		}
		require LANG_FILE;
	}

	public function translate($id) {
		
		if (array_key_exists($id,$this->text_array)) {
			return $this->text_array[$id];
		} else {
			return $id;
		}
	
	}
	
	public function t($id) {
		return $this->translate($id);
	}
	
}
?>