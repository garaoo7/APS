<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/HomePage.css')?>">
		<title>APS Search</title>
	</head>
	<body>
		<header>
		
			<h1 style="margin-left: 10px;color:#696969;font-family: arial, sans-serif;font-size:12px;">APS</h1>			
		
		</header>
		
		<div class="main-div" style="padding-top: 14%;">
			<form name="aps" action="<?php echo base_url('search/search')?>" method="POST"><br>	
				<div>
					<input type="text" class="search" id="searchBox" name ='searchText' placeholder="Search a question">
					<input id='searchButton' type="submit" class="button" value="Search">
				</div>
				<div id ="recommdated-questions" class="recommdated-questions"></div>
			</form>
		</div>
		

		






		<!-- <footer>
			<a class="leftlinks" href="https://www.google.com/intl/en/ads/?fg=1">Advertising</a>
			<a class="leftlinks" href="https://www.google.com/services/?fg=1">Business</a>
			<a class="leftlinks" href="https://www.google.com/intl/en/about/">About</a>
			<a class="rightlinks" href="https://www.google.com/preferences?hl=en">Settings</a>
			<a class="rightlinks" href="https://www.google.com/intl/en/policies/?fg=1">Privacy & Terms</a>
		</footer> -->
	</body>
	<script src="<?php echo base_url('js/jquery-3.1.1.min.js')?>"></script>
	<script src="<?php echo base_url('js/autosuggestor.js')?>"></script>
	<script src="<?php echo base_url('js/common.js')?>"></script>
	<script type="text/javascript">
		var base_url = '<?php echo base_url(); ?>';
	</script>
</html>