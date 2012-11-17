<?php
class Document {

	private $fields;

	function __construct() {
		$this->fields = array();
	}
	 
	public function getFields() {
		return $this->fields;
	}
	 
	public function addField($key, $value) {
		$_key   = (string)$key;
		$_value = (string)$value;

		$this->fields[$_key] = $_value;
	}
}
?>
