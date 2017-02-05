<?php
class Search_lib{
		
	private $qustionModel;

	private function _init_lib(){
		$this->questionModel = $this->load->model('common/Question_model');
	}

}
?>