<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Search extends MX_Controller{

	private $searchLib;
	private $questionModel;
	public function __construct(){
		$this->load->model('common/Question_model');
		$this->load->library('search/search_lib');
		$this->questionModel = new question_model();
		$this->searchLib 	 = new search_lib();
	}
	public function index(){
		$inputQuery 	= $this->input->post('searchText',true);
		$resultTuples 	= $this->searchLib->getResultTuples($inputQuery,array());

		$resultTuples['inputQuery'] = $inputQuery;
		/*$resultTuples = array();
		$resultTuples['questions'][0] = array(
							'title'=>'where to do mba in delhi??',
							'description' => 'i want to do mba where??',
							'tags' => array('mba','delhi'),
							'creationDate' => '2016-9-22',
							'viewCount'	=> '100',
							'ansCount' => '20'
							);
		$resultTuples['facets'] = array('tags' => array(0=>array('name'=>'mba','count'=>'10')),);*/
									
		$this->load->view('search/searchResultPage',$resultTuples);

	}

	public function getFilteredResult(){
		$inputQuery = $this->input->post('inputQuery',true);
		$isAjax 	= $this->input->post('isAjax',true);
		$filters 	= $this->input->post('filters',true);
		$resultTuples = $this->searchLib->getResultTuples($inputQuery,$filters);
		$resultTuples['inputQuery'] = $inputQuery;
		//_p($filters);die;
		$appliedFilters = array();
		if(!empty($filters)){
			foreach ($filters as $key => $value) {
				$appliedFilters[$value['name']][$value['value']] = 1;
			}
		}
			
		$resultTuples['appliedFilters'] = $appliedFilters;
		echo $this->load->view('search/content',$resultTuples,true);
	}



	public function getSuggestion(){
		$searchTerm 	= $this->input->get('searchTerm',true);
		$suggestions  	= $this->searchLib->getSuggestion($searchTerm);
		echo json_encode($recommendateQuestions);		
		exit();
	}


}
?>