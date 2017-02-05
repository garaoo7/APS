<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MX_Controller{

	private $indexerLib;
	public function __construct(){
		 $this->load->library('indexer/indexer_lib');
		 $this->indexerLib = new indexer_lib();
	}

	public function indexDocuments($type = 'single',$batchSize = '1',$questionId=''){
		if($type =='all'){
			$offset = 0;
			$batchSize = (int)$batchSize;
			$questionCount = $this->indexerLib->getQuestionCount();
			while($offset<=$questionCount){
				$questionIds = $this->indexerLib->getAllQuestionIds($offset,$batchSize);	
				$offset = $offset+$batchSize;
			}
		}
		else if($type =='single' && !empty($questionId)){

		}
	}
}
?>