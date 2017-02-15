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
			echo 'Min Question id : '.$minQuestionId.'<br>';
			echo 'Max Question id : '.$maxQuestionId.'<br>';
			$minQuestionId = 3500000;
			//$questionCount = $this->indexerLib->getQuestionCount();
			$baseQuestionId = $minQuestionId;
			$count = 0;
			while($baseQuestionId<=$maxQuestionId){
				echo "Fetching Documents with baseQuestionId : ".$baseQuestionId. " and batchSize=".$batchSize;
				$questionDocuments = $this->indexerLib->getMultipleQuestionsDocuments($baseQuestionId,$batchSize);		
				echo '<br> Documents generated';
				echo '<br>sending for indexing';
				$response = $this->indexerLib->indexDocuments($questionDocuments);
				die;
				// echo '<br>';
				// $count = $count + count($questionDocuments);
				// echo 'baseQuestion : '.$baseQuestionId.'  ';
				// $temp = $baseQuestionId+$batchSize;
				// echo 'maxQuestionId : '.$temp.' ';
				// echo 'count : '.($count).'<br>';
				echo $response;
				die;
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

	public function temp(){
		$url= 'http://localhost:8983/solr/APS/update?commit=true';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_POST, 1);
		$result = curl_exec($ch);
		var_dump($result);
		curl_close($ch);
		echo 'sad';
		print_r($result);
die;
		$this->load->library('indexer/Curl');


        $this->curlLib = new Curl();
        if(1 || $handler=='update'){
        	$response = $this->curlLib->curl(SOLR_UPDATE_URL.'?commit=true');
        	echo "<br>inside<br>";
        	print_r($response);
        	die;
        }
	}
}
?>