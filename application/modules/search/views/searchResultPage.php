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
		<div class="loader" id="loadingImage" style="display:none;position: absolute; z-index: 9999; top: 585.5px; left: 579.5px;"><img src="<?php echo base_url();?>images/loader.gif"> Loading</div>

		<div id="content">
		<?php $this->load->view('search/content');?>
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