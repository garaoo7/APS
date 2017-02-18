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
		$query = $this->_getSolrQuery($inputQuery,$tags);

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
		_p($result);

		die;
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
		// _p($tags);
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