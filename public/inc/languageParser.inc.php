<?php
/* 
 * LanguageParser reads in a languagefile, which has an array
 * with translations from keys to text. E.G.:
 * $this->text_array['dutch'] = 'Nederlands';
 * 
*/
class LanguageParser {
	
	private $langfile = '';
	private $text_array = array();
	
	public function __construct() {
		$langfile=LANG_FILE;
		if (!file_exists($langfile)) {
			throw new Exception('Language file does not exist. Change language file in configfile!');
		}
		require LANG_FILE;
	}
	
	/*
	 * Reads the key, and if it exists returns the translation.
	 * translate(string / int $id);
	 */
	public function translate($id) {
		
		if (array_key_exists($id,$this->text_array)) {
			return $this->text_array[$id];
		} else {
			return $id;
		}
	
	}
	
	/*
	 * Shorthand version for translate function.
	 */
	public function t($id) {
		return $this->translate($id);
	}
	
}
?>