<?php

class SimpleTemplateParser {
	
	private $content;
	private $title;
	private $templateFile='';
	
	private $isParsed=false;
	
	private $parsevars=array();
						
	function __construct() {
		$this->initParseVars();
	}
	
	function initParseVars() {
		/* Add some default values to be parsed. Can be edited for lateron */
		
		$this->parsevars=array(	'CONTENT'=>$this->content,
								'TITLE'=>$this->title,
								);
	}
	
	function setTemplate($file) {
		$this->templateFile=$file;
	}
	
	function setContent($string) {
		$this->content=$string;
	}
	function setTitle($string) {
		$this->title=$string;
	}
	
	function appendContent($string) {
		$this->content.=$string;
	}
	
	function prependContent($string) {
		$this->content=$string.$this->content;
	}
	
	
	
	function parse() {
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
	
	function getOutput() {
	
		if ($this->isParsed) {
			return $this->output;
		} else {
			$this->parse();
			return $this->output;
		}
		
	}

}

?>