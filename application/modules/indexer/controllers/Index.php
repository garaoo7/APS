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
					$response = $this->indexerLib->indexDocuments($tagDocuments);
					echo $response.'<br>';
					ob_flush();
					flush();
					$start += $batchSize;
				}
			}
		}else if($type == 'single' && !empty($tagId)){

		}
	}

	public function indexQuestionDocuments($type = 'single',$batchSize = 30,$questionId=''){
		if($type =='all'){
			$minQuestionId 	=	(int) 	$this->indexerLib->getMinimumQuestionId();
			$maxQuestionId  = 	(int)	$this->indexerLib->getMaximumQuestionId();
			$batchSize 		= 	(int)	$batchSize;
			echo 'Min Question id : '.$minQuestionId.'<br>'; 
			echo 'Max Question id : '.$maxQuestionId.'<br>';
<<<<<<< HEAD
			$minQuestionId = 3440070;
=======
>>>>>>> d582dc4f4cba2b45fe336100f6f660f7c0baa1d8
			//$questionCount = $this->indexerLib->getQuestionCount();
			$baseQuestionId = $minQuestionId;
			// $count = 0;
			while($baseQuestionId<=$maxQuestionId){
				echo "<br>Fetching Documents with baseQuestionId : ".$baseQuestionId. " and batchSize=".$batchSize;
				$questionDocuments = $this->indexerLib->getMultipleQuestionsDocuments($baseQuestionId,$batchSize);		
				// die("sad");
				echo '<br> Documents generated';
				echo '<br>sending for indexing';
				$response = $this->indexerLib->indexDocuments($questionDocuments);
<<<<<<< HEAD
				if(!$response){
					echo "indexing failed";
					break;
				}
=======
				echo $response;
>>>>>>> d582dc4f4cba2b45fe336100f6f660f7c0baa1d8
				ob_flush();
				flush();
				$baseQuestionId = $baseQuestionId+$batchSize;
			}
		}
		else if($type =='single' && !empty($questionId)){

		}
<<<<<<< HEAD
		echo "<br>please make a commit<br>";
	}

	public function indexTags(){

	}
=======
	}	
>>>>>>> d582dc4f4cba2b45fe336100f6f660f7c0baa1d8

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