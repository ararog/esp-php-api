<?php
class Navigator {

	private $name;
	private $displayName;
	private $elements;
	
	function __construct() {
    	$this->name = "";
    	$this->displayName = "";
    	$this->elements = array();
	}	
	
	public function setName($name) {
		$this->name = $name;	
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setDisplayName($displayName) {
		$this->displayName = $displayName;	
	}
	
	public function getDisplayName() {
		return $this->displayName;
	}
	
	public function setNavigationElement($element) {
		$this->elements = $elements;
	}
	
	public function getNavigationElement() {
		return $this->elements;
	}
	
	public function addItem($navigatorItem) {
		array_push($this->elements, $navigatorItem);
	}
}
?>
