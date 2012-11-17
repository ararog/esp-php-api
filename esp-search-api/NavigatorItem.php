<?php
class NavigatorItem {
	
   	private $label;	
   	private $value;	
	private $count;	
   
	function __construct() {
	    	$this->label = "";
	    	$this->value = "";
	    	$this->count = 0;
	}	
	
	public function setLabel($label) {
		$this->label = $label;	
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function setValue($value) {
		$this->value = $value;	
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function setCount($count) {
		$this->count = $count;	
	}
	
	public function getCount() {
		return $this->count;
	}
}
?>
