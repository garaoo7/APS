<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MX_Controller{

	private $indexerLib;
	public function __construct(){
		 $this->load->library('indexer/indexer_lib');
		 $this->indexerLib = new indexer_lib();
	}

	public function indexTags($type='single', $batchSize = 30, $tagId=''){
		if($type == 'all'){
			$getTagCount = $this->indexerLib->getTagCount();			
			if($getTagCount == 0){
				echo "No entry for live tag in Tag table.<br>";
				echo "No indexing required for tags.<br>";
			}else{
				$noOfBatches = ceil($getTagCount/$batchSize);
				//$noOfBatches = 1;
				$start = 0;
				$count = $batchSize;
				//$count=1;
				for($batchNo=1; $batchNo <= $noOfBatches; $batchNo++){
					echo 'Fatching row from '.$start.' To '.($start+$count-1).'<br>';
					$tagDetails = $this->indexerLib->getTagDetails($start, $count);
					//echo '<pre>'.print_r($tagDetails,true).'</pre>';
					$tagDocuments = $this->indexerLib->createDocumetForIndexingTags($tagDetails);
					//print_r($tagDocuments);die;
					unset($tagDetails);
					$response = $this->indexerLib->indexDocuments($tagDocuments);
					unset($tagDocuments);
					echo $response.'<br>';
					ob_flush();
					flush();
					$start += $batchSize;
					echo '<br>'.memory_get_peak_usage().'<br>';
				}
			}
		}else if($type == 'single' && !empty($tagId)){

		}
	}

	public function indexQuestionDocuments($type = 'single',$batchSize = 30,$questionId=''){
		if($type =='all'){
			$minQuestionId =(int) $this->indexerLib->getMinimumQuestionId();
			$maxQuestionId  = (int)$this->indexerLib->getMaximumQuestionId();
			$batchSize = (int)$batchSize;
			echo 'Min Question id : '.$minQuestionId.'<br>';
			echo 'Max Question id : '.$maxQuestionId.'<br>';
			//$questionCount = $this->indexerLib->getQuestionCount();
			$baseQuestionId = $minQuestionId;
			$count = 0;
			while($baseQuestionId<=$maxQuestionId){
				echo "Fetching Documents with baseQuestionId : ".$baseQuestionId. " and batchSize=".$batchSize;
				$questionDocuments = $this->indexerLib->getMultipleQuestionsDocuments($baseQuestionId,$batchSize);		
				echo '<br> Documents generated';
				echo '<br>sending for indexing';
				$response = $this->indexerLib->indexDocuments($questionDocuments);
				echo $response;
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