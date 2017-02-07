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

	public function getQuestionCount(){
		$this->_init('read');
		$query = 'select count(*) from questions where status=live';
		$result = $this->dbHandle->get($query)->result_array();
		return $result[0]['count'];
	}

	public function getMultipleQuestionsData($offset=0,$batchSize=1){
		return;
	}
  
}?>

