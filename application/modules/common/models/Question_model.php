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
		$this->_init('read');
		// $query = "select count(*) as count from messageTable where parentId = 0 and fromOthers='user'";
		// $result = $this->dbHandle->query($query)->result_array();
		return $result[0]['count'];
	}

	public function getMultipleQuestionsData($offset=0,$batchSize=1){
		return;
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
}?>

