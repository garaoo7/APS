<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller{

	public function __construct(){
		$this->questionModel = $this->load->model('common/Question_model');
	}
	public function index(){

		$this->load->view('common/HomePage');
	}
}
?>