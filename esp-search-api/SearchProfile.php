<?php
class SearchProfile {

	private $resultView;
	private $rankProfile;
	 
	function __construct() {
		$this->resultView = "";
		$this->rankProfile = "";
	}

	public function setResultView($resultView) {
		$this->resultView = $resultView;
	}

	public function getResultView() {
		return $this->resultView;
	}

	public function setRankProfile($rankProfile) {
		$this->rankProfile = $rankProfile;
	}

	public function getRankProfile() {
		return $this->rankProfile;
	}
	
	public function parseConfiguration($xmlDocument){

		$attributes = $xmlDocument->{"result-spec"}->attributes();

		$this->resultView  = $attributes["default-result-view"];
		$this->rankProfile = $attributes["default-rank-profile"];
	}	
}
?>
