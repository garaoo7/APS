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
	public function getSuggestion(){
		$searchTerm = $this->input->get('searchTerm',true);
		$recommendateQuestions = array(
		    'abroadcourse249158'=>'problemsStudents learn to <strong>integrate</strong> theoretical and technical <strong>information</strong> systems knowledgeThe program is designed',
		    'abroadcourse241794'=>'communications, <strong>information</strong> security and <strong>integration</strong> of business <strong>information</strong> systems',
		    'abroadcourse224291'=>'ensures <strong>information</strong> <strong>integrity</strong> through secure networks, internet operations and computer applications',
		    'abroadcourse223784'=>'The Management <strong>Information</strong> Systems major focuses on IT-supported techniques for exploring',
		    'abroadcourse225232'=>'Web DesignGraduates will also be able to apply management skills to the thoughtful <strong>integration</strong> of <strong>information</strong>',
		    'abroadcourse218964'=>'system analysis and design, <strong>information</strong> management, and system <strong>integration</strong>',
		    'abroadcourse214410'=>'<strong>integrating</strong> services and systems to provide the most efficient service possible to the end userAn <strong>Information</strong>',
		    'abroadcourse206098'=>'Bachelor of <strong>Information</strong> Technology at CQUniversity will provide student with the opportunity to',
		    'abroadcourse234275'=>'Technology <strong>Integration</strong> & Project, <strong>Information</strong> Security Management, Data & <strong>Information</strong> Systems',
		    'abroadcourse263120'=>'how <strong>information</strong> technology should <strong>integrate</strong> with strategic approaches to business management'
		);

		echo json_encode($recommendateQuestions);		
		exit();
	}

	function getFilteredResult(){
		_p('getFilteredResult');die;
	}
}
?>