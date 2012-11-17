<?php
class QueryModifier {

	private $field;
	private $value;
	 
	function __construct() {
		$this->field = "";
		$this->value = "";
	}

	public function setField($field) {
		$this->field = $field;
	}

	public function getField() {
		return $this->field;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}
}
?>