<?php
include_once 'Navigator.php';
include_once 'Document.php';
include_once 'QueryResult.php';

class Query {

	private $parameters;
	private $connection;
	
	private $query;					
	private $hits; 						
	private $offset; 						
	private $lemmatize; 					
	private $navigation; 					 
	
	private $navigators; 					

	private $language; 					
	private $sortby;						
	private $spellcheck; 					
	private $synomyns; 					
	
	private $inCache; 						
	
	private $searchViewName;				
	
	private $modifiers;						 
	
	function __construct() {
		$this->hits 	  = 10;
		$this->offset 	  = 0;
		$this->language   = "pt";
		$this->navigation = TRUE;
		$this->lemmatize  = FALSE;
		$this->synomyns   = FALSE;
		$this->inCache    = FALSE;
		
		$this->parameters = array();
		$this->modifiers  = array();
	}

	public function getConnection(){
		return $this->connection;
	}
	
	public function setConnection($aux) {
		$this->connection = $aux;
	}
	
	public function getOffset() {
		return $this->offset;
	}
	
	public function setOffset($aux) {
		$this->offset = $aux;
	}
	
	public function getQuery() {
		return $this->query;
	}
	
	public function setQuery($aux) {
		$this->query = $aux;
	}
	
	public function getHits() {
		return $this->hits;
	}
	
	public function setHits($aux) {
		$this->hits = $aux;
	}
	
	public function getLemmatize() {
		return $this->lemmatize;
	}
	
	public function setLemmatize($aux) {
		$this->lemmatize = $aux;
	}
	
	public function getNavigation() {
		return $this->navigation;
	}
	
	public function setNavigation($aux) {
		$this->navigation = $aux;
	}

	public function getNavigators() {
		return $this->navigators;
	}
	
	public function setNavigators($aux) {
		$this->navigators = $aux;
	}
	
	public function getLanguage() {
		return $this->language;
	}
	
	public function setLanguage($aux) {
		$this->language = $aux;
	}
	
	public function getSortby() {
		return $this->sortby;
	}
	
	public function setSortby($aux) {
		$this->sortby = $aux;
	}

	public function getSpellcheck() {
		return $this->spellcheck;
	}
	
	public function setSpellcheck($aux) {
		$this->spellcheck = $aux;
	}
	
	public function getSynomyns() {
		return $this->synomyns;
	}
	
	public function setSynomyns($aux) {
		$this->synomyns = $aux;
	}

	public function getInCache() {
		return $this->inCache;
	}
	
	public function setInCache($aux) {
		$this->inCache = $aux;
	}

	public function getSearchViewName() {
		return $this->searchViewName;
	}
	
	public function setSearchViewName($aux) {
		$this->searchViewName = $aux;
	}
		
	public function addModifier( $aux ) {
		array_push($this->modifiers, $aux);
	}	
	
	private function prepareQuery() {
		if ( strlen($this->query) > 0 ) {
			$this->parameters["query"] = urlencode($this->query);
		}
		
		if ( intval(trim($this->hits)) > 0 ) {
			$this->parameters["hits"] = $this->hits;
		}
		
		if ( intval(trim($this->offset)) > 0 ) {
			$this->parameters["offset"] = $this->offset;
		}
		
		if ( strlen($this->searchViewName) > 0 ) {
			$this->parameters["view"] = $this->searchViewName;
		}
		
		if ( strlen($this->language) > 0 ) {
			$this->parameters["language"] = $this->language;
		}
		
		if ( strlen($this->sortby) > 0 ) {
			$this->parameters["sortby"] = $this->sortby;
		}
		
		if ( $this->inCache ) {
			$this->parameters["qtf_teaser:view"] = "hithighlight";
		}
		
		if ( strlen($this->navigators) > 0 ) {
			$this->parameters["rpf_navigation:navigators"] = $this->navigators;
		}

		if ( $this->navigation ) {
			$this->parameters["rpf_navigation:enabled"] = "true";
		}
		else {	
			$this->parameters["rpf_navigation:enabled"] = "false";
		}
		
		if ( $this->lemmatize ) {
			$this->parameters["qtf_lemmatize"] = "true";
		}
		else {	
			$this->parameters["qtf_lemmatize"] = "false";
		}
		
		if ( $this->synomyns ) {
			$this->parameters["qtf_querysynonyms"] = "true";
		}
		else {	
			$this->parameters["qtf_querysynonyms"] = "false";
		}
		
		if ( strlen($this->spellcheck) > 0 ) {
			
			if ( trim($this->spellcheck) == "yes" || trim($this->spellcheck) == "1" ) {
				$this->parameters["spell"] = "1";
			}
			
			if ( trim($this->spellcheck) == "no" || trim($this->spellcheck) == "0" ) {
				$this->parameters["spell"] = "0";
			}
			
			if ( trim($this->spellcheck) == "Suggest" || trim($this->spellcheck) == "suggest" ) {
				$this->parameters["spell"] = "suggest";
			}
		}
				
		$filters = "";
		
		if ( count($this->modifiers) > 0 ) {
			foreach($this->modifiers as $modifier) {
				
				$value = $modifier->getValue();
				if (strpos( $value , " " ) > 0) {
					$value = chr(34) . $modifier->getValue() . chr(34);
				}
				
				if (is_numeric($value)) { 
					$filters = $filters . "+" . $modifier->getField() . ":$value";
				} 
				else if (! strpos( $value , " " )) {
					$filters = $filters . "+" . $modifier->getField() . ":$value";
				}
				else {
					$filters = $filters . "+" . $modifier->getField() . ":^\"$value\"$";
				}
			}
			$this->parameters["navigation"] = $filters;
		}
	}
	
	public function execute($termo, $canal, $subportal) {
		
		if ( strlen($termo) > 0 ) {
			$tipoRefino = $_REQUEST["tiporefino"];
			if ( $tipoRefino == "categorias" || $tipoRefino == "intra" || strlen($tipoRefino) == 0 ) { 
				$this->query = 'and(string("' . str_replace('"', '', $termo) . '", mode="simpleall", annotation_class="user"), classificacao:not(1))';
			}
			else if ($tipoRefino == "procedimento") {
				$this->query = 'and(string("' . str_replace('"', '', $termo) . '", mode="simpleall", annotation_class="user"), or(classificacao:1, generic1:"' . str_replace('"', '', $termo) . '"))';
			}
			else if ($tipoRefino == "localidades") {
				$this->query = 'string("' . str_replace('"', '', $termo) . '", mode="simpleall", annotation_class="user")';
			}
		}
	
		$this->prepareQuery();
		$this->connection->setParameters($this->parameters);
		$this->connection->buildURL();
		
		$objQueryResult = new QueryResult();
		
		$content = file_get_contents($this->connection->getUrl()); 
		if ($content !== false) {
			$objXML = simplexml_load_string($content);
			$objQueryResult->parseServerResponse($objXML);

			return $objQueryResult;
		} 
		
		return NULL;
	}
}
?>
