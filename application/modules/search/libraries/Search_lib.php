<?php
class Search_lib{
		
	private $qustionModel;
	private $curlLib;
	private $ci;
	function __construct(){
		$this->ci = &get_instance();
		$this->ci->load->library('indexer/Curl');
		$this->curlLib = new Curl();
	}
	// private function _init_lib(){
	// 	$this->questionModel = $this->load->model('common/Question_model');

	// }
	public function getResultTuples($inputQuery, $appliedFilters){
		if($inputQuery==''){
			return;
		}
		$tags = $this->_getTags($inputQuery);//_p($tags);die;
		$result = $this->_getQuestions($inputQuery,$tags, $appliedFilters);
		//_p($result);die;
		$data = $this->_prepareResponseData($result);

		return $data;
	}

	private function _prepareQuestionTupleData($response){
		$data= array();
		foreach ($response as $key => $questionDetails) {
			$questionTags = array();
			foreach ($questionDetails as $key => $value) {
				if(strpos($key, 'tag_name_') !== false){
					$questionTags[] = $value;
				}
 			}
 			$creationDate = rtrim(str_replace('T', ' ', $questionDetails['questionCreationDate']), 'Z');
			$data[] = array(
										'questionId'	=> $questionDetails['questionId'],
										'questionTitle' => $questionDetails['questionTitle'],
										'description' 	=> $questionDetails['questionDescription'],
										'creationDate'	=> $creationDate,
										'viewCount'		=> $questionDetails['viewCount'],
										'ansCount'		=> $questionDetails['ansCount'],
										'tags' 			=> $questionTags
										);
		}
		return $data;
	}

	private function _prepareResponseData($response){
		//_p($response);die;
		$questionData = $response['response'];
		$data = array();
		$data['questionCount'] = $questionData['numFound'];
		$result = $this->_prepareQuestionTupleData($questionData['docs']);
		$data['questions'] = $result;
		$data['facet'] = $this->_prepareFacets($response['facet_counts']);
		//_p($data['facets']);die;
		//_p($data);die;
		$data['facets']['View Count'] = $data['facet']['queryFacet']['viewCountFacet'];
		$data['facets']['Answer Count'] = $data['facet']['queryFacet']['ansCountFacet'];
		$data['facets']['Tag'] = $data['facet']['fieldFacet'] ? $data['facet']['fieldFacet']['tagFacet']: array();
		unset($data['facet']);
		return $data;
	}

	private function _prepareFacets($facetData){
		//_p($facetData);die;
		$facets = array();
		$facets['queryFacet'] = $this->_prepareQueryFacet($facetData['facet_queries']);
		$facets['fieldFacet'] = $this->_prepareFieldFacet($facetData['facet_fields']);
		//_p($facets);die;
		return $facets;
	}

	private function _prepareFieldFacet($fieldFacetData){
		//_p($fieldFacetData);
		$fieldFacet = array();
		$tagFacet = array();
		foreach ($fieldFacetData as $key => $tagDetails) {
			if(strpos($key, 'tag_name_') !== false){
				foreach ($tagDetails as $tag => $tagQuestionCount) {
					$tagFacet[] = array(
						'field' => $key,
						'name'	=> $tag,
						'count'	=> $tagQuestionCount
					);
				}
			}
		}
		//_p($tagFacet);die;
		//die;
		if(count($tagFacet) > 0){
			$fieldFacet['tagFacet'] = $tagFacet;
		}
		//_p($tagFacet);die;
		//_p($fieldFacet);die;
		return $fieldFacet;
	}

	private function _prepareQueryFacet($queryFacetData){
		//_p($queryFacetData);die;
		$queryFacet = array();
		$viewCountFacet = array();
		$ansCountFacet = array();
		foreach ($queryFacetData as $key => $value) {
			if(strpos($key, 'views') !== false){
				$viewCountFacet[str_replace('views', '', $key)] = $value;
			}
			if(strpos($key, 'answer') !== false){
				$ansCountFacet[str_replace('answer', '', $key)] = $value;
			}
		}
		//_p($viewCountFacet);die;
		if(count($viewCountFacet) >0){
			$queryFacet['viewCountFacet'] = $this->_prepareViewCountFacet($viewCountFacet);
		}
		if(count($ansCountFacet) >0){
			$queryFacet['ansCountFacet'] = $this->_prepareAnsCountFacet($ansCountFacet);
		}
		//_p($queryFacet);die;
		return $queryFacet;
	}

	private function _prepareViewCountFacet($viewCountFacetInput){
		//_p($viewCountFacetInput);//die;
		$viewCountFacet = array();
		global $viewCountRange;
		//_p($viewCountRange);die;
		foreach ($viewCountFacetInput as $range => $count) {
			$viewCountFacet[] = array(
				'name'	=> $viewCountRange[$range],
				'count' => $count,
				);
		}
		//_p($viewCountFacet);die;
		return $viewCountFacet;
	}

	private function _prepareAnsCountFacet($ansCountFacetInput){
		//_p($viewCountFacetInput);//die;
		$ansCountRange = array();
		global $ansCountRange;
		//_p($ansCountRange);
		//_p($ansCountFacetInput);die;
		foreach ($ansCountFacetInput as $range => $count) {
			$ansCountFacet[] = array(
				'name'	=> $ansCountRange[$range],
				'count' => $count,
				);
		}
		//_p($viewCountFacet);die;
		return $ansCountFacet;
	}


	private function _getSolrQuery($inputQuery,$tags){
		$urlComponents = array();
        $urlComponents[] = 'q='.$inputQuery;
        $urlComponents[] = 'wt=phps';
        $urlComponents[] = 'defType=edismax';
        $urlComponents[] = 'fq=faceType:question';
        $urlComponents[] = 'df=questionTitle';
        $urlComponents[] = 'bq='.$this->_getTagsQF($tags);
        $urlComponents= implode('&', $urlComponents);
        $result = $this->curlLib->curl(SOLR_SELECT_URL,$urlComponents)->getResult();
		$result = unserialize($result);
		return $result;
	}

	private function _getQuestions($inputQuery,$tags, $appliedFilters){
		$urlComponents = array();
        $urlComponents[] = 'q='.$inputQuery;
        $urlComponents[] = 'wt=phps';
        $urlComponents[] = 'defType=edismax';
        $urlComponents[] = 'fq=faceType:question';
        $urlComponents[] = 'fl=*,score';
        $urlComponents[] = 'df=questionTitle';
        $urlComponents[] = 'bq='.$this->_getTagsQF($tags);
        
        $urlComponentsForAppliedFilter =  $this->_prepareAppliedFilter($appliedFilters);
        $urlComponents = array_merge($urlComponents , $urlComponentsForAppliedFilter);
        
        $facetComponents = $this->_getFacetComponent($tags);
        $urlComponents = array_merge($urlComponents, $facetComponents);
        //_p($urlComponents);die;
        $urlComponents= implode('&', $urlComponents);
        
        $result = $this->curlLib->curl(SOLR_SELECT_URL,$urlComponents)->getResult();
        //_p($result);die;	
		$result = unserialize($result);
		//_p($result);die;
		return $result;
	}

	private function _prepareAppliedFilter($appliedFilters){
		$urlComponents = array();
		// tag filter
		if(!empty($appliedFilters['tag']) && count($appliedFilters['tag']) > 0){
			foreach ($appliedFilters['tag'] as $key => $value) {
				$urlComponents[] = 'fq='.$key.':'.$value;
			}
		}

		// view count filter
		if(!empty($appliedFilters['views']) && count($appliedFilters['views']) > 0){
			foreach ($appliedFilters['views'] as $views) {
				$view = explode('-', $views);
				$urlComponents[] = 'fq=viewCount:['.$view[0].' To '.$view[1].']';
			}
		}

		// answer count flter
		return $urlComponents;
	}

	private function _getFacetComponent($tags){
		$facets = array('tag', 'views', 'answer');
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
			}
		}
		//$urlComponents = implode('&', $urlComponents);
		return $urlComponents;
		//_p($urlComponents);die;
	}

	private function _getTagFacets($tags){
		$tagFacetComponent = array();
		foreach ($tags as $tag) {
			$tagFacetComponent[] = 'facet.field=tag_name_'.$tag['tagId'];
		}
		$tagFacetComponent = implode('&', $tagFacetComponent);
		//_p($tagFacetComponent);die;
		return $tagFacetComponent;
	}

	private function _getViewsFacets(){
		global $viewCountRange;
		$viewFacetComponents = array();
		foreach ($viewCountRange as $key => $value) {
			$keys = explode('-', $key);
			$viewFacetComponents[] = 'facet.query={!key =views'.$key.'}viewCount:['.$keys[0].' TO '.$keys[1].']';
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
			$ansFacetComponents[] = 'facet.query={!key =answer'.$key.'}ansCount:['.$keys[0].' TO '.$keys[1].']';
		}
		$ansFacetComponents = implode('&', $ansFacetComponents);
		//_p($ansFacetComponents);die;
		return $ansFacetComponents;
		
	}

	private function _getTagsQF($tags){
		$str = '';
		foreach ($tags as $tag) {
			$str.=' tag_name_'.$tag['tagId'].':"'.$tag['tagName'].'"^'.(1000*$tag['tagQualityScore']);
		}
		return $str;
	}

	private function _getTags($inputQuery){
		$urlComponents = array();
		$urlComponents[] = 'q='.$inputQuery;
		$urlComponents[] = 'start=0';
		$urlComponents[] = 'rows=100';
		$urlComponents[] = 'wt=phps';
		$urlComponents[] = 'defType=edismax';
		$urlComponents[] = 'fq=faceType:tag';
		$urlComponents[] = 'df=tagName';
		$urlComponents[] = 'fl=tagId,tagName,tagQualityScore';
		//_p($urlComponents);die;
		$urlComp = implode('&', $urlComponents);
		$result = $this->curlLib->curl(SOLR_SELECT_URL,$urlComp)->getResult();
		$result = unserialize($result);
		$tags = $result['response']['docs'];
		//_p($tags);die;
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
}
?>