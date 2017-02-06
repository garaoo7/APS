<?php
class Indexer_lib{
		
	private $questionModel;
	private $ci;
	private function _init_lib(){
		$this->ci = & get_instance();
		$this->ci->load->model('common/Question_model');
		$this->questionModel = new question_model();
	}

	public function getQuestionDocuments($questionIds){

	}

	public function getQuestionCount(){
		$this->_init_lib();
		return $this->questionModel->getQuestionCount();
	}
}
?>