<?php

$page = basename($_SERVER['PHP_SELF']); 

switch ($page){

	case 'index.php':
	 $title= 'Dashboard';
	 $page_name='Dashboard';
	 $description = 'overview &amp; stats';
	 break;

	case 'login.php':
	 $title= 'Login';
	 $page_name='Login';
	 $description = '';
	 break;


	default:
	 $title= 'LIVE';
	 $description = '';
	 break;
}


?>