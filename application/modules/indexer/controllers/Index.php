<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MX_Controller{

	private $indexerLib;
	public function __construct(){
		 $this->load->library('indexer/indexer_lib');
		 $this->indexerLib = new indexer_lib();
	}

	public function indexDocuments($type = 'single',$batchSize = 30,$questionId=''){
		if($type =='all'){
			$offset = 0;
			$batchSize = (int)$batchSize;
			$questionCount = $this->indexerLib->getQuestionCount();
			echo $questionCount;
			while($offset<=$questionCount){
				$questionDocuments = $this->indexerLib->getMultipleQuestionsDocuments($offset,$batchSize);	
				var_dump($questionDocuments);
				$response = $this->indexerLib->indexDocuments($questionDocuments);
				$offset = $offset+$batchSize;
			}
		}
		else if($type =='single' && !empty($questionId)){

		}
	}

	public function getDataFromSolr(){
		$this->load->library('indexer/indexer_lib');
		 $this->indexerLib = new indexer_lib();		
		$response = $this->indexerLib->generateUrlOnSearch();
	}
}
?>