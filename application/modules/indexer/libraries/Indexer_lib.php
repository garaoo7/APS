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
    	$completeXML = '';
        //print_r($questionsData);die;
        //echo '<pre>'.print_r($questionsData,true).'</pre>';die;
    	foreach ($questionsData as $question) {
            //print_r($question);die;
    		$tagDetails = array();	
    		$tagId = explode($delimiter,$question['tagId']);
    		$tagName = explode($delimiter,$question['tagName']);
            if(!empty($tagId[0])){
                $size = count($tagId);
                for($i=0;$i<$size;$i++){
                    $question['tag_name_'.$tagId[$i]] = $tagName[$i];
                    $question['tagIdNameMap'][] = $tagId[$i].'|::|'.$tagName[$i];
                }
            }

            $question['questionCreationDate'] = str_replace(' ', 'T', $question['questionCreationDate']).'Z';
            $question['unique_id'] = 'question'.$question['questionId'];
            $question['faceType'] = 'question';
    		unset($question['tagId']);
    		unset($question['tagName']);
    		$completeXML .= $this->generateXML($question);
    	}
        $completeXML = '<add>'.$completeXML.'</add>';
	return $completeXML;
    }

    public function generateXML($documents){
        // _p($documents);
        // die;
    	$xml = '<doc>';
    	foreach ($documents as $key => $value) {
            if(!is_array($value)){
                $value = trim($value);
                $key = trim($key);
                if($value!=''){
                    $xml  = $xml . '<field name ="'.$key.'"><![CDATA['.htmlentities(strip_tags($this->asciConvert($value))).']]></field>';      
                }
                
            }
            else if(is_array($value)){
                $key = trim($key);
                foreach ($value as $individualVal) {
                    $individualVal = trim($individualVal);
                    if($individualVal != "") {
                        $xml  = $xml . '<field name ="'.$key.'"><![CDATA['.htmlentities(strip_tags($this->asciConvert($individualVal))).']]></field>';
                    }
                }
            }
    	}
    	$xml .= '</doc>';
    	return $xml;
    }

    public function createDocumetForIndexingTags($tagDetails){
        $xmlDocument = '';
        foreach ($tagDetails as $tagDetail) {
            $xmlDocument .= $this->generateXML($tagDetail);
        }
        $xmlDocument = '<add>'.$xmlDocument.'</add>';
        return $xmlDocument;
    }

    public function asciConvert($string){
    	return preg_replace_callback('/[^\x20-\x7F]/', function($m){return '';}, $string);
    }

    public function indexDocuments($documents){
    	$this->ci->load->library('indexer/Curl');
        $this->curlLib = new Curl();
        $updateUrl = SOLR_UPDATE_URL;
        echo $updateUrl.'<br>';

        if(!is_array($documents)){
            $documents = array($documents);
        }
        foreach ($documents as $key => $document) {
        	$response = $this->curlLib->curl($updateUrl, $document,1);   
        	echo '<pre>'.print_r($response).'</pre>';
        }
     	//$response = $this->commit('APS','update');
     	//echo '<pre>'.print_r($response).'</pre>';
     	// die;
    }
    public function commit($collection,$handler){
    	$this->ci->load->library('indexer/Curl');
        $this->curlLib = new Curl();
        if($handler=='update'){
        	$response = $this->curlLib->curl(SOLR_UPDATE_URL.'?commit=true','');
        	echo "<br>inside<br>";
        	print_r($response);
        	die;
        }
        return $response;
    }

    public function getTagCount(){
        $this->_init_lib();
        $result = $this->questionModel->getTagCount();
        return $result[0]['tagCount'];
    }

    public function getTagDetails($start, $count){
        $this->_init_lib();
        $tagDetails = $this->questionModel->getTagDetails($start, $count);
        $tagDetails = $this->_processTagDetails($tagDetails);
        return $tagDetails;
    }

    private function _processTagDetails($tagDetails){
        // print_r()($tagDetails);
        foreach ($tagDetails as $key => $tagDetail) {
            $tagDetails[$key]['unique_id'] = 'tag'.$tagDetail['tagId'];
            $tagDetails[$key]['faceType'] = 'tag';
        }
        return $tagDetails;
    }
}
?>