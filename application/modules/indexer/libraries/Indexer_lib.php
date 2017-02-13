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
		echo '<br> question data fetched';
		echo '<br> generating documents';
		$getQuestionDocuments = $this->createDocuments($questionData);
		return $getQuestionDocuments;
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

    public function createDocuments($questionsData){
    	$delimiter  = '|::|';
    	$completeXML = array();
        //print_r($questionsData);die;
    	foreach ($questionsData as $question) {
            //print_r($question);die;
    		$tagDetails = array();	
    		$tagId = explode($delimiter,$question['tagId']);
    		$tagName = explode($delimiter,$question['tagName']);
    		$tagQualityScore = explode($delimiter,$question['tag_quality_score']);
    		// print_r($tagId);
    		// print_r($tagName);
    		// print_r($tagQualityScore);
            if($tagId){
                $size = count($tagId);
                for($i=0;$i<$size;$i++){
                    $question['tag_name_'.$tagId[$i]] = $tagName[$i];
                    $question['tag_quality_'.$tagId[$i]] = $tagQualityScore[$i];
                }
            }
        		

            $question['questionCreationDate'] = str_replace(' ', 'T', $question['questionCreationDate']).'Z';
    		// //
    		// if($size>1){
    		// 	print_r($size);
    		// echo '<pre>'.print_r($tagDetails,true).'</pre>';	
    		// 	die;;
    		// }

    		// echo '<pre>'.print_r($question,true).'</pre>';	
    		
    		// die;
    		// $question = array();
    		// // $question['id']
    		unset($question['tagId']);
    		unset($question['tagName']);
    		unset($question['tag_quality_score']);
    		$completeXML[] = $this->generateXML($question);
    	}
	return $completeXML;
    }

    public function generateXML($questionData){
    	$xml = '<add><doc>';
    	foreach ($questionData as $key => $value) {
    		$xml  = $xml . '<field name ="'.$key.'"><![CDATA['.htmlentities(strip_tags($this->asciConvert($value))).']]></field>';
    	}
    	$xml .= '</doc></add>';
    	return $xml;
    }
    public function asciConvert($string){
    	return $string;

    	//return preg_replace_callback('/[^\x20-\x7F]/e', ' ', $string);
    }

    public function indexDocuments($questionDocuments){
    	echo count($questionDocuments);
    	print_r($questionDocuments);	
    	die;
    	$this->ci->load->library('indexer/Curl');
        $this->curlLib = new Curl();
        $updateUrl = SOLR_UPDATE_URL;
        echo $updateUrl;
        foreach ($questionDocuments as $key => $value) {
        	$response = $this->curlLib->curl($updateUrl, $value,1);   
        	echo '<pre>'.print_r($response).'</pre>';
        }
     	$response = $this->commit('APS','update');
     	echo '<pre>'.print_r($response).'</pre>';
     	// die;
    }
    public function commit($collection,$handler){
    	$this->ci->load->library('indexer/Curl');
        $this->curlLib = new Curl();
        if($handler=='update'){
        	$response = $this->curlLib->curl(SOLR_UPDATE_URL.'?commit=true',array());
        	echo "<br>inside<br>";
        	print_r($response);
        	die;
        }
        return $response;
    }
}
?>