<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MX_Controller{

	private $indexerLib;
	public function __construct(){
		 $this->load->library('indexer/indexer_lib');
		 $this->indexerLib = new indexer_lib();
	}

	public function indexDocuments($type = 'single',$batchSize = 30,$questionId=''){
		if($type =='all'){
			$minQuestionId =(int) $this->indexerLib->getMinimumQuestionId();
			$maxQuestionId  = (int)$this->indexerLib->getMaximumQuestionId();
			$batchSize = (int)$batchSize;
			echo $minQuestionId.'<br>';
			echo $maxQuestionId;
			//$minQuestionId = 1500000;
			//$questionCount = $this->indexerLib->getQuestionCount();
			$baseQuestionId = $minQuestionId;
			$count = 0;
			while($baseQuestionId<=$maxQuestionId){
				echo $baseQuestionId.'<br>';
				$questionDocuments = $this->indexerLib->getMultipleQuestionsDocuments($baseQuestionId,$batchSize);	
				echo '<br>';
				$count = $count + count($questionDocuments);
				echo 'baseQuestion : '.$baseQuestionId.'  ';
				$temp = $baseQuestionId+$batchSize;
				echo 'maxQuestionId : '.$temp.' ';
				echo 'count : '.($count).'<br>';
				//$response = $this->indexerLib->indexDocuments($questionDocuments);
				ob_flush();
				flush();
				$baseQuestionId = $baseQuestionId+$batchSize;
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