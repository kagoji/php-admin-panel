<?php include('template/header.php'); ?>
<?php  

/*Login submit*/
if(!empty($_REQUEST['username']) && !empty($_REQUEST['password'])){

   $login_admin = user_login();

   if($login_admin==1)
      header('Location:index.php');
 }
/*Logout submit*/
if(isset($_REQUEST['action'])&& ($_REQUEST['action']=='logout')){

	 $logout = logout();
	  if($logout==1){
	   
		   header('Location:login.php'); 
		}

}

/*logged in check*/

if(loggedin()==1){

	header('Location:index.php');
	die();
}


?>

<a href="index.php" class="logo pull-left">
	<img src="assets/images/logo.png" height="54" alt="Duronto Admin" />
</a>

<div class="panel panel-sign">
	<div class="panel-title-sign mt-xl text-right">
		<h2 class="title text-uppercase text-weight-bold m-none"><i class="fa fa-user mr-xs"></i> Sign In</h2>
	</div>
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
		<form action="" method="post">
			<div class="form-group mb-lg">
				<label>Username</label>
				<div class="input-group input-group-icon">
					<input name="username" type="text" class="form-control input-lg" />
					<span class="input-group-addon">
						<span class="icon icon-lg">
							<i class="fa fa-user"></i>
						</span>
					</span>
				</div>
			</div>

			<div class="form-group mb-lg">
				<div class="clearfix">
					<label class="pull-left">Password</label>
				</div>
				<div class="input-group input-group-icon">
					<input name="password" type="password" class="form-control input-lg" />
					<span class="input-group-addon">
						<span class="icon icon-lg">
							<i class="fa fa-lock"></i>
						</span>
					</span>
				</div>
			</div>

			<div class="row">
				
				<div class="col-sm-4 pull-right">
					<button type="submit" class="btn btn-primary hidden-xs">Sign In</button>
				</div>
			</div>

		</form>
	</div>
</div>

<p class="text-center text-muted mt-md mb-md">Copyright &copy; <?php echo date('Y');?> Powered and Developed by Live Technologies Ltd.</p>

<?php include('template/footer.php'); ?>