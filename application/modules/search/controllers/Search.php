<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends MX_Controller{

	private $indexer;
	private $questionModel;
	public function __construct(){
		$this->load->model('common/Question_model');
		$this->load->library('search/search_lib');
		$this->questionModel = new question_model();
		$this->indexer 	     = new search_lib();
	}
	public function getSuggestion(){
		$searchTerm = $this->input->get('searchTerm',true);
		echo $searchTerm;
		exit();
	}
}
?>