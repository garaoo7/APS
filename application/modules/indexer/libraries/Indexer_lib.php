<?php
class Indexer_lib{
		
	private $questionModel;
	private $ci;
	 function __construct(){
		$this->ci = & get_instance();
		$this->ci->load->model('common/Question_model');
		$this->questionModel = new question_model();
	}

	public function getQuestionDocuments($questionIds){

	}

	public function getQuestionCount(){
		return $this->questionModel->getQuestionsCount();
	}

	public function getMultipleQuestionsDocuments($offset=0,$batchSize=1){
		$questionsData = $this->questionModel->getMultipleQuestionsData($offset,$batchSize);
		$questionsDocuments = $this->createDocuments($questionsData);
		return $questionsDocuments;
	}
	public function indexDocuments($questionsDocuments){
		$params=['name'=>'John', 'surname'=>'Doe', 'age'=>36)
		$defaults = array(
		CURLOPT_URL => 'http://myremoteservice/', 
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $params,
		);
		$ch = curl_init();
		curl_setopt_array($ch, ($options + $defaults));
	}

	public function createDocuments($questionsData){
		$questionDocuments[]['id'] = '1';
		$questionDocuments[]['title'] = 'test';
		return $questionDocuments;
	}
}
?>