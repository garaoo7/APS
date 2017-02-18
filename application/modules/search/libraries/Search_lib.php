<?php
class Search_lib{
		
	private $qustionModel;

	private function _init_lib(){
		$this->questionModel = $this->load->model('common/Question_model');
	}
	public function getResultTuples($inputQuery){
		if($inputQuery==''){
			return;
		}
		$query = $this->_getSolrQuery($inputQuery);
	}
	private function _getSolrQuery($inputQuery){
		$urlComponents = array();
        
        $urlComponents[] = 'q='.$inputQuery;
        $urlComponents[] = 'wt=phps';
        $urlComponents[] = 'defType=edismax';
        $urlComponents[] = 'fq=facetype:abroadlisting';
        $urlComponents[] = 'df=saUnivName';
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