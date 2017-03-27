<?php
class Search_lib{
		
	private $qustionModel;
	private $curlLib;
	private $ci;
	private $responseParser;
	function __construct(){
		$this->ci = &get_instance();
		$this->ci->load->library('indexer/Curl');
		$this->curlLib = new Curl();
		$this->ci->load->library('search/Response_parser');
		$this->responseParser = new Response_parser();
	}

	public function getResultTuples($inputQuery, $appliedFilters){
		if($inputQuery==''){
			return;
		}
		$tags = $this->_getTags($inputQuery);
		// _p($tags);
		// die;
		$result = $this->_getQuestions($inputQuery,$tags, $appliedFilters);
		// _p($result);
		// die;
		$data = $this->responseParser->prepareResponseData($result);

		return $data;
	}

	private function _getQuestions($inputQuery,$tags, $appliedFilters){
		$urlComponents = array();
        $urlComponents[] = 'q='.urlencode($inputQuery);
        $urlComponents[] = 'wt=phps';
        $urlComponents[] = 'defType=edismax';
        $urlComponents[] = 'fq=faceType:question';
        $urlComponents[] = 'fl=*,score';
        $urlComponents[] = 'df=questionTitle';
        $urlComponents[] = 'bq='.$this->_getTagsQF($tags);
        $urlComponents[] = 'hl=true&hl.fl=questionTitle&hl.simple.pre='.urlencode('<b>').'&hl.simple.post='.urlencode('</b>');
        $urlComponents[] = 'start=0&rows=100';
        // _p($urlComponents);die;
        $urlComponentsForAppliedFilter =  $this->_prepareAppliedFilter($appliedFilters);
        $urlComponents = array_merge($urlComponents , $urlComponentsForAppliedFilter);
        
        $facetComponents = $this->_getFacetComponent($tags);
        $urlComponents = array_merge($urlComponents, $facetComponents);
        //_p($urlComponents);die;
        
        $urlComponents= implode('&', $urlComponents);
        $result = $this->curlLib->curl(SOLR_SELECT_URL,$urlComponents)->getResult();
        // _p($result);die;	
		$result = unserialize($result);
		// _p($result);die;
		return $result;
	}

	private function _prepareAppliedFilter($appliedFilters){
		//_p($appliedFilters);die;
		$urlComponents = array();
		if(empty($appliedFilters))
			return $urlComponents;
		$appliedFiltersArray =array();
		foreach ($appliedFilters as $key => $value) {
			$appliedFiltersArray[$value['name']][] = $value['value'];
		}
		foreach ($appliedFiltersArray as $facet => $filters) {
			switch ($facet) {
				case 'Views':
					$orCondition ='';
					$viewFacetUrlComponents = 'fq={!tag=tagForViewCountFilter}viewCount:(';
					foreach ($filters as $key => $filter) {
						$viewFacetUrlComponents .= $orCondition.'['.str_replace('-', ' TO ',$filter).']';	
						$orCondition = ' OR ';
					}
					if($viewFacetUrlComponents!="") {
						$urlComponents[] = rtrim($viewFacetUrlComponents, ' OR ').')';
					}
				break;
				case 'Answers':
					$orCondition ='';
					$answerFacetUrlComponents = 'fq={!tag=tagForAnswerCountFilter}ansCount:(';
					foreach ($filters as $key => $filter) {
						$answerFacetUrlComponents .= $orCondition.'['.str_replace('-', ' TO ',$filter).']';
						$orCondition = ' OR ';
					}
					if($answerFacetUrlComponents!="") {
						$urlComponents[] = rtrim($answerFacetUrlComponents, ' OR ').')';
					}
				break;
				case 'Tags':

					foreach ($filters as $key => $filter) {
						$tagFacetUrlComponents[] =	'fq={!tag=tagForTagFilter}tag_name_'.$filter.':'.'*';
					}
					$urlComponents[] = implode(' OR ', $tagFacetUrlComponents);
				break;
				
			}
		}
		//_p($urlComponents);die;
		// tag filter
		// if(!empty($appliedFilters['tag']) && count($appliedFilters['tag']) > 0){
		// 	foreach ($appliedFilters['tag'] as $key => $value) {
		// 		$urlComponents[] = 'fq='.$key.':'.$value;
		// 	}
		// }

		// // view count filter
		// if(!empty($appliedFilters['views']) && count($appliedFilters['views']) > 0){
		// 	foreach ($appliedFilters['views'] as $views) {
		// 		$view = explode('-', $views);
		// 		$urlComponents[] = 'fq=viewCount:['.$view[0].' To '.$view[1].']';
		// 	}
		// }

		// answer count flter
		return $urlComponents;
	}

	private function _getFacetComponent($tags){
		$facets = array('tag', 'views', 'answer','date');
		$urlComponents[] = 'facet=true';
		foreach ($facets as $facet) {
			switch ($facet) {
				case 'tag':
					$urlComponents[] = $this->_getTagFacets($tags);
					break;
				
				case 'views':
					$urlComponents[] = $this->_getViewsFacets();
					break;

				case 'answer':
					$urlComponents[] = $this->_getAnswerFacets();
					break;

				case 'date':
					$urlComponents[] = $this->_getDateFacets();
					break;
			}
		}
		//$urlComponents = implode('&', $urlComponents);
		return $urlComponents;
		//_p($urlComponents);die;
	}

	private function _getDateFacets(){
		$urlComponents = 'facet.date=questionCreationDate&facet.date.start=NOW/YEAR-8YEARS&facet.date.end=NOW/YEAR-4YEARS&facet.date.gap=%2B1YEAR';
		return $urlComponents;
	}

	private function _getTagFacets($tags){
		// $tagFacetComponent = array();
		// foreach ($tags as $tag) {
		// 	$tagFacetComponent[] = 'facet.field=tag_name_'.$tag['tagId'];
		// }
		// $tagFacetComponent = implode('&', $tagFacetComponent);
		//_p($tagFacetComponent);die;
		$tagFacetComponent = 'facet.field={!ex=tagForTagFilter}tagIdNameMap';
		return $tagFacetComponent;
	}

	private function _getViewsFacets(){
		global $viewCountRange;
		$viewFacetComponents = array();
		foreach ($viewCountRange as $key => $value) {
			$keys = explode('-', $key);
			$viewFacetComponents[] = 'facet.query={!ex=tagForViewCountFilter key =views'.$key.'}viewCount:['.$keys[0].' TO '.$keys[1].']';
		}
		$viewFacetComponents = implode('&', $viewFacetComponents);
		return $viewFacetComponents;
		//_p($viewFacetComponents);die;
	}

	private function _getAnswerFacets(){
		global $ansCountRange;
		$ansFacetComponents = array();
		foreach ($ansCountRange as $key => $value) {
			$keys = explode('-', $key);
			$ansFacetComponents[] = 'facet.query={!ex=tagForAnswerCountFilter key =answer'.$key.'}ansCount:['.$keys[0].' TO '.$keys[1].']';
		}
		$ansFacetComponents = implode('&', $ansFacetComponents);
		//_p($ansFacetComponents);die;
		return $ansFacetComponents;
		
	}

	private function _getTagsQF($tags){
		$str = '';
		if(!empty($tags)){
			foreach ($tags as $tag) {
				$str.=' tag_name_'.$tag['tagId'].':"'. urlencode($tag['tagName']) .'"^'.(1000*$tag['tagQualityScore']);

				//$str.=' tag_name_'.$tag['tagId'].':"'. str_replace('&', '\&', $tag['tagName']) .'"^'.(64000*$tag['tagQualityScore']);
			}	
		}
		//_p($str);die;
		return $str;
	}

	private function _getTags($inputQuery){
		$urlComponents = array();
		$urlComponents[] = 'q='.$inputQuery;
		$urlComponents[] = 'wt=phps';
		$urlComponents[] = 'defType=edismax';
		$urlComponents[] = 'fq=faceType:tag';
		$urlComponents[] = 'df=tagName';
		$urlComponents[] = 'fl=tagId,tagName,tagQualityScore';
		$urlComponents[] = 'bq=tagQualityScore:[0 TO 0.25]^2000 tagQualityScore:[0.25 TO 0.5]^4000 tagQualityScore:[0.5 TO 0.75]^64000 tagQualityScore:[0.75 TO *]^128000';
		$urlComponents[] = 'start=0&rows=20';

		$urlComp = implode('&', $urlComponents);
		$this->curlLib->setIsRequestToSolr(1);
		$result = $this->curlLib->curl(SOLR_SELECT_URL,$urlComp)->getResult();
		
		// die;
		$result = unserialize($result);
		//_p($result);die;
		$tags = $result['response']['docs'];
		return $tags;
	}

	// public function generateUrlOnSearch() {
 //        $urlComponents = array();
        
 //        $urlComponents[] = 'q="Cyprus"';
 //        $urlComponents[] = 'wt=phps';
 //        $urlComponents[] = 'defType=edismax';
 //        $urlComponents[] = 'fq=facetype:abroadlisting';
 //        $urlComponents[] = 'df=saUnivName';
 //        $SOLR_AUTOSUGGESTOR_URL = "http://localhost:8983/solr/select?";
 //        $solrUrl = $SOLR_AUTOSUGGESTOR_URL.implode('&',$urlComponents);
 //        $urlComp = explode('?', $solrUrl);
 //        $this->ci->load->library('indexer/Curl');
 //        $this->curlLib = new Curl();
 //        $this->curlLib->setIsRequestToSolr(1);
 //        $customCurlObject = $this->curlLib->curl($urlComp[0], $urlComp[1]);
 //        $customCurlObject1 = $customCurlObject->getResult();
 //        var_dump($customCurlObject1);die;
 //    }

	public function getSuggestion($searchTerm){
		$urlComponents = array();
		$urlComponents[] = 'q="'.$searchTerm.'"';
		$urlComponents[] = 'wt=phps';

		$urlComp = implode('&', $urlComponents);
		$this->curlLib->setIsRequestToSolr(1);
		$result = $this->curlLib->curl(SOLR_SUGGEST_URL,$urlComp)->getResult();
		
		$result = unserialize($result);
		$result = $result['response']['docs'];
		$returnArray = array();
		foreach ($result as $question) {
			$returnArray[] = $question['questionTitle'];
		}
		return $returnArray;
	}
}
?>