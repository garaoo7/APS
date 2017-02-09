<?php

class Shiksha_question_model extends MY_Model{

	private $dbHandle;
	private function _init($handle = 'read'){
		if($handle=='read'){
			$this->dbHandle = $this->getReadHandle();
		}
		else if($handle=='write'){
			$this->dbHandle = $this->getWriteHandle();
		}
	}

	public function getShikshaQuestionsCount(){
		$this->_init('read');
		$query = "select count(*) as count from messageTable where parentId = 0 and fromOthers='user'";
		$result = $this->dbHandle->query($query)->result_array();
		return $result[0]['count'];
	}
	public function getShikshaMultipleQuestionsData($offset=0,$batchSize=1){
		$this->_init('read');
		$query = "select msgId as questionId,creationDate,userId,msgTxt as title,viewCount,status
				  from messageTable where parentId =0 and fromOthers = 'user' limit ".$batchSize." offset ".$offset."
				 ";
		$result = $this->dbHandle->query($query)->result_array();
		//echo '<pre>'.print_r($result,true).'</pre>';
		// die;
		return $result;
	}


	public function getShikshaAnswersCount(){
		$this->_init('read');
		$query = "select count(*) as count from messageTable where mainAnswerId = 0 and fromOthers='user'";
		$result = $this->dbHandle->query($query)->result_array();
		return $result[0]['count'];
	}
	public function getShikshaMultipleAnswersData($offset=0,$batchSize=1){
		$this->_init('read');
		$query = "select msgId as answerId,parentId as questionId,creationDate,userId,msgTxt as answerText,digUp as upVotes,status
				  from messageTable where mainAnswerId =0 and fromOthers = 'user' limit ".$batchSize." offset ".$offset."
				 ";
		$result = $this->dbHandle->query($query)->result_array();
		//echo '<pre>'.print_r($result,true).'</pre>';
		// die;
		return $result;
	}

	public function getShikshaTagsCount(){
		$this->_init('read');
		$query = "select count(*) as count from tags";
		$result = $this->dbHandle->query($query)->result_array();
		return $result[0]['count'];
	}

	public function getShikshaMultipleTagsData($offset=0,$batchSize=1){
		$this->_init('read');
		$query = "select id as tagId,tags as tagName,tag_quality_score,main_id,status
				  from tags limit ".$batchSize." offset ".$offset."
				 ";
		$result = $this->dbHandle->query($query)->result_array();
		//echo '<pre>'.print_r($result,true).'</pre>';
		// die;
		return $result;
	}
}?>

