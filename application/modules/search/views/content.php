<div class="main-div">
			<form name="aps" action="<?php echo base_url('search/search')?>" method="POST"><br>	
				<div>
					<input type="text" class="search" id="searchBox" name ='searchText' value="<?php echo $inputQuery;?>" placeholder="Search a question">
					<input id='searchButton' type="submit" class="button" value="Search">
				</div>
				<div id ="recommdated-questions" class="recommdated-questions"></div>
			</form>
		</div>


		<div style="margin-top: 75px;">
		<div style="float: left">
		<!-- <b style="font-size:20px">Facets:</b> -->
		<?php
		//_p($facets);die;
		if(!empty($facets)){
			$this->load->view('facet');
		}
		?>

		</div>
		<div style="margin-left: 25%" id = "questionResult">
				<?php 
			if(!empty($questions)){
				foreach ($questions as $question) {
					// _p($question);
					// die;	
					echo '<p style="font-size:20px">'.$question['questionTitle'].'</p>';
					echo '<div style="padding-left:15px">';
					// echo '<p >'.$question['description'].'</p>';
					echo '<p ><b>Answers</b> : '.$question['ansCount'].'&nbsp&nbsp&nbsp&nbsp&nbsp <b>Views</b> : '.$question['viewCount'].'</p>';		
					if($question['tags']){
						echo '<b>Tags:</b> ';
						$question['tags'] = implode(" || ", $question['tags']);
						echo $question['tags'];
					}
					$date = explode(' ', $question['creationDate']);
					echo '<p><b>Creation Date: </b>'.$date[0].'</p>';
					echo '</div>';
				}
			}
			else{
				echo "No result Found";
			}
				?>
			
		</div>
		</div>