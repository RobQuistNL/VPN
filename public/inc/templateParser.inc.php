<?php
/* 
 * TemplateParser reads in a templatefile, which has some variables in it.
 * Those variables will be replaced with content from the class.
 * 
*/
class SimpleTemplateParser {
	
	private $content;
	private $title;
	private $templateFile = '';
	
	private $isParsed = false;
	
	private $parsevars = array();
						
	public function __construct() {
		$this->initParseVars();
	}
	
	private function initParseVars() {
		/* Add some default values to be parsed. Can be edited for lateron */
		
		$this->parsevars=array(	'CONTENT'=>$this->content,
								'TITLE'=>$this->title,
								);
	}
	
	public function setTemplate($file) {
		$this->templateFile=$file;
	}
	
	public function setContent($string) {
		$this->content=$string;
	}
	public function setTitle($string) {
		$this->title=$string;
	}
	
	public function appendContent($string) {
		$this->content.=$string;
	}
	
	public function prependContent($string) {
		$this->content=$string.$this->content;
	}
	
	public function parse() {
		if ($this->templateFile=='') {
			throw new Exception('No template file selected.');
		}
		if (!file_exists($this->templateFile)) {
			if (!file_exists(PUBLIC_PATH.'/view/'.$this->templateFile)) {
				throw new Exception('Template file '.$this->templateFile.' not found in '.getcwd().' or '.PUBLIC_PATH.'/view/');
			} else {
				$this->output=file_get_contents(PUBLIC_PATH.'/view/'.$this->templateFile);
			}
		} else {
			$this->output=file_get_contents($this->templateFile);
		}
		
		$this->initParseVars();
		
		foreach ($this->parsevars as $key => $value) {
			$this->output=str_replace('{{'.$key.'}}',$value,$this->output);
		}

	}
	
	public function getOutput() {
	
		if ($this->isParsed) {
			return $this->output;
		} else {
			$this->parse();
			return $this->output;
		}
		
	}

}

?>