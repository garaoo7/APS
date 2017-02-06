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
		return $this->questionModel->getQuestionCount();
	}

	public function getMultipleQuestionsDocuments($offset=0,$batchSize=1){
		$questionData = $this->questionModel->getMultipleQuestionsData($offset,$batchSize);
	}
	public function indexDocuments($questionDocuments){

	}
}
?>