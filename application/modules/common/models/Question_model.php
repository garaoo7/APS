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
		return 20;
	}

	public function getMultipleQuestionsData($offset=0,$batchSize=1){
		return;
	}
  
}?>

