<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller{

	public function __construct(){
		 $this->load->model('common/Question_model');
		 $this->questionModel = new question_model();

		 $this->load->model('common/Shiksha_question_model');
		 $this->shikshaQuestionModel = new Shiksha_question_model();

	}
	public function index(){

		$this->load->view('common/HomePage');
	}





	public function createQuestionsData(){
		$questionsCount = $this->shikshaQuestionModel->getShikshaQuestionsCount();
		
		$offset = 0;
		$batchSize = 10000;

		// $questionsCount = 9;
		// $batchSize = 5;
		echo "Total No of Questions : ". $questionsCount.'<br>';
		ob_flush();
		flush();
		while($offset<$questionsCount){
			echo '<br>Fetching Questions with <br> Offset : '.$offset.'<br> batchSize: '.$batchSize;
			ob_flush();
			flush();
			$questionData = $this->shikshaQuestionModel->getShikshaMultipleQuestionsData($offset,$batchSize);
			echo "<br>data fetched";
			ob_flush();
			flush();
			$status = $this->questionModel->addMultipleQuestions($questionData);
			if($status){
				$offset = $offset + $batchSize;	
				echo "<br>inserted successfully<br>";
				ob_flush();
				flush();
			}
			else{
				echo "<br>Try again with same offset and batchSize";
				break;
				ob_flush();
				flush();
			}
		}
	}

	public function createAnswerData(){
		$answersCount = $this->shikshaQuestionModel->getShikshaAnswersCount();
		
		$offset = 750000;
		$batchSize = 10000;

		// $answersCount = 9;
		// $batchSize = 5;
		echo "Total No of Answers : ". $answersCount.'<br>';
		ob_flush();
		flush();
		while($offset<$answersCount){
			echo '<br>Fetching Answers with <br> Offset : '.$offset.'<br> batchSize: '.$batchSize;
			ob_flush();
			flush();
			$answerData = $this->shikshaQuestionModel->getShikshaMultipleAnswersData($offset,$batchSize);
			echo "<br>data fetched";
			ob_flush();
			flush();
			$status = $this->questionModel->addMultipleAnswers($answerData);
			if($status){
				$offset = $offset + $batchSize;	
				echo "<br>inserted successfully<br>";
				ob_flush();
				flush();
			}
			else{
				echo "<br>Try again with same offset and batchSize";
				break;
				ob_flush();
				flush();
			}
		}
	}


	public function createTagsData(){
		$tagsCount = $this->shikshaQuestionModel->getShikshaTagsCount();
		
		$offset = 0;
		$batchSize = 10000;

		// $tagsCount = 9;
		// $batchSize = 5;
		echo "Total No of tags : ". $tagsCount.'<br>';
		ob_flush();
		flush();
		while($offset<$tagsCount){
			echo '<br>Fetching tags with <br> Offset : '.$offset.'<br> batchSize: '.$batchSize;
			ob_flush();
			flush();
			$tagsData = $this->shikshaQuestionModel->getShikshaMultipleTagsData($offset,$batchSize);
			echo "<br>data fetched";
			ob_flush();
			flush();
			$status = $this->questionModel->addMultipleTags($tagsData);
			if($status){
				$offset = $offset + $batchSize;	
				echo "<br>inserted successfully<br>";
				ob_flush();
				flush();
			}
			else{
				echo "<br>Try again with same offset and batchSize";
				break;
				ob_flush();
				flush();
			}
		}
	}


	public function createQuestionTagsData(){
		$questionTagsCount = $this->shikshaQuestionModel->getQuestionTagsCount();
		
		$offset = 0;
		$batchSize = 10000;

		// $questionTagsCount = 9;
		// $batchSize = 5;
		echo "Total No of tags question relations : ". $questionTagsCount.'<br>';
		ob_flush();
		flush();
		while($offset<$questionTagsCount){
			echo '<br>Fetching tags question relations with <br> Offset : '.$offset.'<br> batchSize: '.$batchSize;
			ob_flush();
			flush();
			$questionTagsData = $this->shikshaQuestionModel->getShikshaMultipleQuestionTagsData($offset,$batchSize);
			echo "<br>data fetched";
			ob_flush();
			flush();
			$status = $this->questionModel->addMultipleQuestionTags($questionTagsData);
			if($status){
				$offset = $offset + $batchSize;	
				echo "<br>inserted successfully<br>";
				ob_flush();
				flush();
			}
			else{
				echo "<br>Try again with same offset and batchSize";
				break;
				ob_flush();
				flush();
			}
		}
	}
}
?>