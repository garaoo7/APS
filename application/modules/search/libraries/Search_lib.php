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
	public function getResultTuples($inputQuery){
		if($inputQuery==''){
			return;
		}
		$tags = $this->_getTags($inputQuery);
		$result = $this->_getQuestions($inputQuery,$tags);
		//_p($result);die;
		$data = $this->_prepareResponseData($result);

		return $data;
	}

	private function _prepareQuestionTupleData($response){
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
		$data['facets']['Tag'] = $data['facet']['fieldFacet']['tagFacet'];
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
		foreach ($queryFacetData as $key => $value) {
			if(strpos($key, 'viewCount:') !== false){
				$viewCountFacet[str_replace('viewCount:', '', $key)] = $value;
			}
		}
		if(count($viewCountFacet) >0){
			$queryFacet['viewCountFacet'] = $this->_prepareViewCountFacet($viewCountFacet);
		}
		//_p($rangeFacet);die;
		return $queryFacet;
	}

	private function _prepareViewCountFacet($viewCountFacetInput){
		$viewCountFacet = array();
		$rangeArray= array(
			'[* TO 1000}' => array(
				'min' => 0,
				'max' => 1000,
				'text' => '* To 1000'
			),
			'[1000 TO 5000}' => array(
				'min' => 1001,
				'max' => 5000,
				'text' => '1001 To 5000'
			),
			'[5000 TO 10000}' => array(
				'min' => 5001,
				'max' => 10000,
				'text' => '50001 To 10000'
			),
			'[10000 TO *}' => array(
				'min' => 10001,
				'max' => '*',
				'text' => '10001 To *'
			),
		);
		foreach ($viewCountFacetInput as $range => $count) {
			$viewCountFacet[] = array(
				'name'	=> $rangeArray[$range]['text'],
				'count' => $count,
				'minMax' => $rangeArray[$range]['min'].'_'.$rangeArray[$range]['max']
				);
		}
		//_p($viewCountFacet);die;
		return $viewCountFacet;
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

	private function _getQuestions($inputQuery,$tags){
		$urlComponents = array();
        $urlComponents[] = 'q='.$inputQuery;
        $urlComponents[] = 'wt=phps';
        $urlComponents[] = 'defType=edismax';
        $urlComponents[] = 'fq=faceType:question';
        $urlComponents[] = 'fl=*,score';
        $urlComponents[] = 'df=questionTitle';
        $urlComponents[] = 'bq='.$this->_getTagsQF($tags);
        $urlComponents[] = 'facet=true';
        $urlComponents[] = $this->_getTagFacets($tags);
        $urlComponents[] = 'facet.range=ansCount&facet.range.start=0&facet.range.end=65000&facet.range.gap=100';	
        $urlComponents[] = 'facet.query=viewCount:[* TO 1000}&facet.query=viewCount:[1000 TO 5000}&facet.query=viewCount:[5000 TO 10000}&facet.query=viewCount:[10000 TO *}&facet.query=ansCount:[* TO 1000}';
        $urlComponents= implode('&', $urlComponents);
        
        $result = $this->curlLib->curl(SOLR_SELECT_URL,$urlComponents)->getResult();
		$result = unserialize($result);
		return $result;
	}

	private function _getTagFacets($tags){
		$str = '';
		foreach ($tags as $tag) {
			$str.='facet.field=tag_name_'.$tag['tagId'].'&';
		}
		return $str;	
	}

	private function _getTagsQF($tags){
		$str = '';
		foreach ($tags as $tag) {
			$str.=' tag_name_'.$tag['tagId'].':'.$tag['tagName'].'^'.(1000*$tag['tagQualityScore']);
		}
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

		$urlComp = implode('&', $urlComponents);
		$result = $this->curlLib->curl(SOLR_SELECT_URL,$urlComp)->getResult();
		$result = unserialize($result);
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
}
?>