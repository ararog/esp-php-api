<?php
class QueryTransformation {

	private $name;
	private $action;
	private $query;
	private $custom;
	private $message;
	private $messageId;
	 
	function __construct() {
		$this->name = "";
		$this->action = "";
		$this->query = "";
		$this->custom = "";
		$this->message = "";
		$this->messageId = 0;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function setAction($action) {
		$this->action = $action;
	}

	public function getAction() {
		return $this->action;
	}

	public function setQuery($query) {
		$this->query = $query;
	}

	public function getQuery() {
		return $this->query;
	}

	public function setCustom($custom) {
		$this->custom = $custom;
	}

	public function getCustom() {
		return $this->custom;
	}
	
	public function setMessage($message) {
		$this->message = $message;
	}

	public function getMessage() {
		return $this->message;
	}
	
	public function setMessageId($messageId) {
		$this->messageId = $messageId;
	}

	public function getMessageId() {
		return $this->messageId;
	}
}
?>
