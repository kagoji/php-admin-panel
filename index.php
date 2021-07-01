<?php include('template/header.php'); ?>
<?php

	/*logged in check*/
	if(loggedin() !=1){

		header('Location:login.php');
		die();
	}
?>

<section role="main" class="content-body">
	<header class="page-header">
		<h2><?php echo isset($title) ? $title:'Blank Page' ; ?></h2>
	</header>
	<!-- start: page -->
	<div class="row">
		<div class="col-md-6">
			<section class="panel panel-featured panel-featured-warning">
				<header class="panel-heading">
					<div class="panel-actions">
						<a href="#" class="panel-action panel-action-toggle" data-panel-toggle=""></a>
						<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss=""></a>
					</div>

					<h2 class="panel-title">Title</h2>
				</header>
				<div class="panel-body">
					<?php 

						if(isset($_SESSION['alert_message'])){
							$show_message=$_SESSION['alert_message'];

							echo "<div class='alert alert-danger'>";
							echo "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
						    echo $show_message;
							echo "</div>";
							unset($_SESSION['alert_message']);
						}

					?>
					<code>.panel-featured.panel-featured-warning</code>
				</div>
			</section>
		</div>

		<div class="col-md-6">
			<section class="panel panel-featured panel-featured-warning">
				<header class="panel-heading">
					<div class="panel-actions">
						<a href="#" class="panel-action panel-action-toggle" data-panel-toggle=""></a>
						<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss=""></a>
					</div>

					<h2 class="panel-title">Title</h2>
				</header>
				<div class="panel-body">
					<code>.panel-featured.panel-featured-warning</code>
				</div>
			</section>
		</div>
		
	</div>
								
	<!-- end: page -->
</section>

<?php include('template/footer.php'); ?>