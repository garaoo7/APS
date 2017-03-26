<?php

class Response_parser 
{
	public function prepareResponseData($response){
		// _p($response);die;
		$questionData = $response['response'];
		$data = array();
		if(empty($questionData)){
			return $data;
		}
		$data['questionCount'] = $questionData['numFound'];
		$result = $this->_prepareQuestionTupleData($questionData['docs'],$response['highlighting']);
		$data['questions'] = $result;
		$data['facet'] = $this->_prepareFacets($response['facet_counts']);
		//_p($data['facets']);die;
		//_p($data);die;
		$data['facets']['Views'] = $data['facet']['queryFacet']['viewCountFacet'];
		$data['facets']['Answers'] = $data['facet']['queryFacet']['ansCountFacet'];
		$data['facets']['Tags'] = $data['facet']['fieldFacet'] ? $data['facet']['fieldFacet']['tagFacet']: array();
		unset($data['facet']);
		return $data;
	}

	private function _prepareQuestionTupleData($response,$highlighting){
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
										'questionTitle' => $highlighting[$questionDetails['unique_id']]['questionTitle'][0],
										// 'questionTitle' => $questionDetails['questionTitle'],
										// 'description' 	=> $questionDetails['questionDescription'],
										'creationDate'	=> $creationDate,
										'viewCount'		=> $questionDetails['viewCount'],
										'ansCount'		=> $questionDetails['ansCount'],
										'tags' 			=> $questionTags
										);
		}
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
		$fieldFacet = array();
		$fields = array('tagIdNameMap');
		foreach ($fields as $fieldName) {
			switch ($fieldName) {
				case 'tagIdNameMap':
					$tagFacet = $this->_prepareTagsFacet($fieldFacetData['tagIdNameMap']);
					if(count($tagFacet) > 0){
						$fieldFacet['tagFacet'] = $tagFacet;
					}
					break;
			}
		}
		// $tagFacet = array();
		// foreach ($fieldFacetData as $key => $tagDetails) {
		// 	if(strpos($key, 'tag_name_') !== false){
		// 		foreach ($tagDetails as $tag => $tagQuestionCount) {
		// 			$tagFacet[] = array(
		// 				'field' => $key,
		// 				'name'	=> $tag,
		// 				'count'	=> $tagQuestionCount
		// 			);
		// 		}
		// 	}
		// }
		// //_p($tagFacet);die;
		// //die;
		
		// //_p($tagFacet);die;
		// //_p($fieldFacet);die;
		return $fieldFacet;
	}

	private function _prepareTagsFacet($data){
		$tagFacet = array();
		foreach ($data as $key => $value) {
			if($value > 0){
				$tagDetails = explode('|::|',$key);
				$tagFacet[] = array('id'=>$tagDetails[0],'name'=>$tagDetails[1],'count'=>$value);
			}
		}
		return $tagFacet;
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
			if($count > 0){
				$viewCountFacet[] = array(
					'name'	=> $viewCountRange[$range],
					'count' => $count,
					'id'    => $range
					);
			}
				
		}
		//_p($viewCountFacet);die;
		return $viewCountFacet;
	}

	private function _prepareAnsCountFacet($ansCountFacetInput){
		//_p($viewCountFacetInput);//die;
		$ansCountFacet = array();
		global $ansCountRange;
		//_p($ansCountRange);
		//_p($ansCountFacetInput);die;
		foreach ($ansCountFacetInput as $range => $count) {
			if($count > 0){
				$ansCountFacet[] = array(
					'name'	=> $ansCountRange[$range],
					'count' => $count,
					'id'    => $range
					);
			}
		}
		//_p($viewCountFacet);die;
		return $ansCountFacet;
	}
}

?>