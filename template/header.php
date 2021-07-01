<?php include("inc/functions.php"); ?>
<?php include("title.php"); ?>
<!doctype html>
<html class="fixed">

<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title><?php echo isset($title) ? $title.' |' :'' ; ?> Duronto CMS</title>
		<meta name="keywords" content="HTML5 Admin Template" />
		<meta name="description" content="Porto Admin - Responsive HTML5 Template">
		<meta name="author" content="okler.net">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />

		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme-custom.css">

		<!-- Head Libs -->
		<script src="assets/vendor/modernizr/modernizr.js"></script>
		<script src="assets/vendor/style-switcher/style.switcher.localstorage.js"></script>

		
</head>
	<body>

		<?php if(isset($page)&&($page !='login.php')) { ?>
		<section class="body"><!--Section Body Start-->
			<?php include 'header-top.php';?>
			<div class="inner-wrapper"><!--Inner wrapper Start-->
				<?php include 'sidebar-left.php';?>
		<?php }else { ?>
			<section class="body-sign">
				<div class="center-sign">
		<?php } ?>