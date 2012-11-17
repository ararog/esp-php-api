<?php
include_once 'QueryTransformation.php';
include_once 'NavigatorItem.php';

class QueryResult {

	private $firstHit;
	private $lastHit;
	private $hits;
	private $totalHits;
	private $time;
	private $documents;
	private $navigators;
	private $queryTransformations;
	
	function __construct() {
		$this->firstHit   = 0;
		$this->lastHit    = 0;
		$this->hits   	  = 0;
		$this->totalHits  = 0;
		$this->label 	  = "";
		$this->value 	  = "";
		$this->count 	  = 0;
		$this->documents  = array();
		$this->navigators = array();
		$this->queryTransformations = array();
	}

	public function setFirstHit($firstHit) {
		$this->firstHit = $firstHit;
	}

	public function getFirstHit() {
		return $this->firstHit;
	}

	public function setLastHit($lastHit) {
		$this->lastHit = $lastHit;
	}

	public function getLastHit() {
		return $this->lastHit;
	}

	public function setHits($hits) {
		$this->hits = $hits;
	}

	public function getHits() {
		return $this->hits;
	}

	public function setTotalHits($totalHits) {
		$this->totalHits = $totalHits;
	}

	public function getTotalHits() {
		return $this->totalHits;
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
	
	//'Setters and Getters
	public function getDocuments() {
		return $this->documents;
	}
	
	public function setDocuments($aux) {
		$this->documents = $aux;
	}
	
	public function getNavigators() {
		return $this->navigators;
	}
	
	public function setNavigators($aux) {
		$this->navigators = $aux;
	}

	public function getQueryTransformations() {
		return $this->queryTransformations;
	}
	
	public function setQueryTransformations($aux) {
		$this->queryTransformations = $aux;
	}	
	
	private function addDocument( $aux ) {
		array_push($this->documents, $aux);
	}

	private function addNavigator( $aux ) {
		array_push($this->navigators, $aux);
	}

	private function addQueryTransformation( $aux ) {
		array_push($this->queryTransformations, $aux);
	}

	public function parseServerResponse( $xmlDocument ) { 

		$queryTransformationsEntries = $xmlDocument->xpath("//QUERYTRANSFORM");
		if ( count($queryTransformationsEntries) > 0 ) {
			foreach ($queryTransformationsEntries as $queryTransformationEntry) {
				
				$queryTransformationAttributes = $queryTransformationEntry->attributes();
				
				$objQueryTransformation = new QueryTransformation();
				$objQueryTransformation->setName($queryTransformationAttributes["NAME"]);
				$objQueryTransformation->setAction($queryTransformationAttributes["ACTION"]);
				$objQueryTransformation->setQuery($queryTransformationAttributes["QUERY"]);
				$objQueryTransformation->setCustom($queryTransformationAttributes["CUSTOM"]);
				$objQueryTransformation->setMessage($queryTransformationAttributes["MESSAGE"]);
				$objQueryTransformation->setMessageId($queryTransformationAttributes["MESSAGEID"]);
				
				$this->addQueryTransformation($objQueryTransformation);
			}
		}
	
		$navigatorsEntries = $xmlDocument->xpath("//NAVIGATIONENTRY");
		if ( count($navigatorsEntries) > 0 ) {
			foreach($navigatorsEntries as $navigatorEntry) {
				$navigatorEntryAttributes = $navigatorEntry->attributes();
				
				$objNavigator = new Navigator();
				$objNavigator->setName($navigatorEntryAttributes["NAME"]);
				$objNavigator->setDisplayName($navigatorEntryAttributes["DISPLAYNAME"]);
				
				$navigatorItems = $navigatorEntry->NAVIGATIONELEMENTS->NAVIGATIONELEMENT;
				foreach($navigatorItems as $item) {
					
					$navigatorItemAttributes = $item->attributes();
			
					$objNavigatorItem = new NavigatorItem();
					$objNavigatorItem->setLabel($navigatorItemAttributes["NAME"]);
					$objNavigatorItem->setValue($navigatorItemAttributes["MODIFIER"]);
					$objNavigatorItem->setCount($navigatorItemAttributes["COUNT"]);
					$objNavigator->addItem( $objNavigatorItem );
				}
				$this->addNavigator($objNavigator);
			}
		}
		
		$docCacheUrl = NULL;
		
		$documentsEntries = $xmlDocument->xpath("//HIT");
		if ( count($documentsEntries) > 0 ) {
			foreach($documentsEntries as $documentEntry) {
				$objDocument = new Document();
				
				$fields = $documentEntry->FIELD;

				$docCacheUrl = "";

				foreach($fields as $field) {

					preg_match('#doccacheurl="(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)"#', (string) $field, $matches, PREG_OFFSET_CAPTURE);
					if(count($matches) > 0) {
						if (strlen($docCacheUrl) == 0) {
							$docCacheUrl = $matches[1][0];
						}
					}

					$fieldAttributes = $field->attributes();

					$fieldName = $fieldAttributes["NAME"];
					if ( $fieldName != "viewsourceurl" ) {
						if ( $fieldName != "body") {
							$objDocument->addField($fieldName, $field);
						}
						else {
							if ( count($field) == 0 ) {
								$objDocument->addField($fieldName, $field);
							}
							else {
								$value = (string) $field;

								$nodes = $field->children();
								foreach($nodes as $node){
									$value = $value . $node;
									if ($node == "sep") {
										echo $node;	
										$value = $value . "...";
									}
								}
								$objDocument->addField($fieldName, $value);
								$value = "";
							}	
						}							
					}
					else {
						if (strlen($docCacheUrl) > 0 && strlen($field) > 0) {
							$objDocument->addField($fieldName, $docCacheUrl);
						}
						else {	
							$objDocument->addField($fieldName, $field);
						}	
					}
				}
				
				$this->addDocument($objDocument);
			}
		}
		
		$resultSets = $xmlDocument->xpath("//RESULTSET");
		if ( count($resultSets) > 0 ) {
			foreach($resultSets as $resultSet){
				
				$resultsetAttributes = $resultSet->attributes();
				
				$this->firstHit	 = $resultsetAttributes["FIRSTHIT"];
				$this->lastHit 	 = $resultsetAttributes["LASTHIT"];
				$this->hits 	 = $resultsetAttributes["HITS"];
				$this->totalHits = $resultsetAttributes["TOTALHITS"];
				$this->time 	 = $resultsetAttributes["TIME"];
			}
		}	
	}
}
?>
