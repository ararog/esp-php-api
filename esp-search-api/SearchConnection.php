<?php
class SearchConnection {

	private $host;
	private $port;
	private $url;
	private $path;
	private $parameters;
	
	function __construct() {
		$this->host = "";
		$this->port = "";
		$this->url = "";
		$this->path = "";
		$this->parameters = array();
	}

	public function setHost($host) {
		$this->host = $host;
	}

	public function getHost() {
		return $this->host;
	}

	public function setPort($port) {
		$this->port = $port;
	}

	public function getPort() {
		return $this->port;
	}

	public function setUrl($url) {
		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}

	public function getPath() {
		return $this->path;
	}
	
	public function setParameters($parameters) {
		$this->parameters = $parameters;
	}

	public function getParameters() {
		return $this->parameters;
	}
	
	public function discoverSearchProfile($paramSearchView) {

		$content = file_get_contents("http://" . $this->host . ":" . $this->port . "/get?view=" . $paramSearchView); 
		if ($content !== false) {

			$objXML = new SimpleXMLElement($content);
			if(! $objXML)
				return NULL;

			$objSearchProfile = new SearchProfile();
			$objSearchProfile->parseConfiguration($objXML);
		
			$this->path = "/cgi-bin/xml-" . $objSearchProfile->getResultView();

			return $objSearchProfile;
		} 

		return NULL;
	}
	
	public function buildURL() {
		$this->url = "http://" . $this->host . ":" . $this->port . $this->path;
		
		if(count($this->parameters) > 0){
			$this->url = $this->url . "?encoding=iso-8859-1";
			
			foreach ($this->parameters as $key => $value) {
				$this->url = $this->url . "&" . $key . "=" . $value;
			}
		}
	}	
}
?>
