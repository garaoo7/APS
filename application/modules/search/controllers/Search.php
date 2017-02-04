<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends MX_Controller{

	public function __construct(){
	}
	public function index(){
		$this->load->view('search/HomePage');
	}
	public function getSuggestion(){
		$searchTerm = $this->input->get('searchTerm',true);
		echo $searchTerm;
		exit();
	}
}
?>