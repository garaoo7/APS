<?php

class Question_model extends MY_Model{

	private $dbHandle;
	private function _init($handle = 'read'){
		if($handle=='read'){
			$this->dbHandle = $this->getReadHandle();
		}
		else if($handle=='write'){
			$this->dbHandle = $this->getWriteHandle();
		}
	}

	public function getQuestionsCount(){
		$this->_init('write');
		$sql = "select count(*) as count from questions where status in ('live','closed')";
		$result = $this->dbHandle->query($sql)->result_array();
		return $result[0]['count'];
	}
	public function getMinimumQuestionId(){
		$this->_init('write');
		$sql = "select min(questionId) as min from questions where status in ('live','closed')";
		$result = $this->dbHandle->query($sql)->result_array();
		return $result[0]['min'];
	}
	public function getMaximumQuestionId(){
		$this->_init('write');
		$sql = "select max(questionId) as max from questions where status in ('live','closed')";
		$result = $this->dbHandle->query($sql)->result_array();
		return $result[0]['max'];
	}
	public function getMultipleQuestionsData($baseQuestionId=0,$batchSize=1){
		$this->_init('write');
		// $sql = "select q.questionId,q.title,q.description,q.creationDate,q.updated,q.viewCount,q.ansCount,t.tagId,t.tagName,t.tag_quality_score,t.main_id from questions q left join questionTag qt on q.questionId = qt.questionId inner join tag t on t.tagId = qt.tagId where q.status in ('live','closed') and qt.status='live' and t.status='live'";
		// echo $baseQuestionId.' ';
		// echo $baseQuestionId+$batchSize;

		$maxQuestionId = $baseQuestionId + $batchSize;
		$sql = "SELECT 
				    q.questionId,
				    q.title as questionTitle,
				    q.description as questionDescription,
				    q.creationDate as questionCreationDate,
				    q.viewCount,
				    q.ansCount,
				    group_concat(t.tagId SEPARATOR '|::|') as tagId,
				    group_concat(t.tagName SEPARATOR '|::|') as tagName				    
				FROM
				    questions q
				        LEFT JOIN
				    questionTag  qt  ON q.questionId = qt.questionId AND qt.status = 'live'
				        LEFT JOIN
				    tag t ON t.tagId = qt.tagId AND t.status = 'live'
				WHERE
						q.status IN ('live' , 'closed')
				        AND q.questionId >= ".(int)$baseQuestionId."
				        AND q.questionId < ". (int)$maxQuestionId."
				       	group by questionId";
		//echo '<pre>'.$sql.'</pre>';
		
		$result =$this->dbHandle->query($sql)->result_array();
		
		// if($baseQuestionId == 1220000){
		// 	 		// echo '<pre>'.print_r($sql,true).'</pre>';			
		// }
		
		// echo '<pre>'.print_r($result,true).'</pre>';
		// _p($sql);
		// _p($result);
		// die;
		return $result;
// die;
	}
  

  	public function addMultipleQuestions($data){
  		$this->_init('write');
  		$this->dbHandle->trans_start();
  		$this->dbHandle->insert_batch('questions', $data);
  		$this->dbHandle->trans_complete();
  		// echo $this->dbHandle->last_query();
  		if($this->dbHandle->trans_status()==false){
  			echo "insertion failed";
  			return false;
  		}
  		return true;
  	}

  	public function addMultipleAnswers($data){
  		$this->_init('write');
  		$this->dbHandle->trans_start();
  		$this->dbHandle->insert_batch('answer', $data);
  		$this->dbHandle->trans_complete();
  		// echo $this->dbHandle->last_query();
  		if($this->dbHandle->trans_status()==false){
  			echo "insertion failed";
  			return false;
  		}
  		return true;
  	}
  	public function addMultipleTags($data){
  		$this->_init('write');
  		$this->dbHandle->trans_start();
  		$this->dbHandle->insert_batch('tag', $data);
  		$this->dbHandle->trans_complete();
  		// echo $this->dbHandle->last_query();
  		if($this->dbHandle->trans_status()==false){
  			echo "insertion failed";
  			return false;
  		}
  		return true;
  	}

  	public function addMultipleQuestionTags($data){
  		$this->_init('write');
  		$this->dbHandle->trans_start();
  		$this->dbHandle->insert_batch('questionTag', $data);
  		$this->dbHandle->trans_complete();
  		// echo $this->dbHandle->last_query();
  		if($this->dbHandle->trans_status()==false){
  			echo "insertion failed";
  			return false;
  		}
  		return true;
  	}

  	public function getTagCount(){
		$this->_init('write');
		$sql = "select count(*) as tagCount from tag where status = 'live'";
		$result = $this->dbHandle->query($sql)->result_array();
		return $result;
	}

	public function getTagDetails($start, $count){
		$this->_init('write');
		$sql = "select tagId, tagName, tag_quality_score as tagQualityScore from tag where status = 'live' limit $start , $count";
		$result = $this->dbHandle->query($sql)->result_array();
		return $result;	
	}
}?>

