<?php
class Indexer_lib{
		
	private $questionModel;
	private $ci;
	function __construct(){		
		$this->ci = & get_instance();
	}
	private function _init_lib(){
		$this->ci = & get_instance();
		$this->ci->load->model('common/Question_model');
		$this->questionModel = new question_model();
	}
	public function getMinimumQuestionId(){
		$this->_init_lib();
		return $this->questionModel->getMinimumQuestionId();
	}
	public function getMaximumQuestionId(){
		$this->_init_lib();
		return $this->questionModel->getMaximumQuestionId();
	}
	public function getQuestionDocuments($questionIds){

	}
	public function getMultipleQuestionsDocuments($baseQuestionId=0,$batchSize=1){
		$this->_init_lib();
		$questionData = $this->questionModel->getMultipleQuestionsData($baseQuestionId,$batchSize);
		$getQuestionDocuments = $this->createDocuments($questionData);
	}
	public function getQuestionCount(){
		$this->_init_lib();
		return $this->questionModel->getQuestionsCount();
	}

	public function generateUrlOnSearch() {
        $urlComponents = array();
        
        $urlComponents[] = 'q="Cyprus"';
        $urlComponents[] = 'wt=phps';
        $urlComponents[] = 'defType=edismax';
        $urlComponents[] = 'fq=facetype:abroadlisting';
        $urlComponents[] = 'df=saUnivName';
        $SOLR_AUTOSUGGESTOR_URL = "http://localhost:8983/solr/select?";
        $solrUrl = $SOLR_AUTOSUGGESTOR_URL.implode('&',$urlComponents);
        $urlComp = explode('?', $solrUrl);
        $this->ci->load->library('indexer/Curl');
        $this->curlLib = new Curl();
        $this->curlLib->setIsRequestToSolr(1);
        $customCurlObject = $this->curlLib->curl($urlComp[0], $urlComp[1]);
        $customCurlObject1 = $customCurlObject->getResult();
        var_dump($customCurlObject1);die;
    }

    public function createDocument($questionsData){

    }
}
?>