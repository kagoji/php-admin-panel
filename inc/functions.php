<?php
session_start();
ob_start();
ob_flush();
error_reporting(0);
/*****************************
##  Function File
*******************************/
include('classlogger.php');

date_default_timezone_set('Asia/Dhaka');



/* $DBNAME = 'vdolog';
 $KEY 	 = 'dhfakHueyrer93KJr4042diJri0Nfk';
 $DBHOST = '127.0.0.1';
 $DBUSER = 'vdolog';
 $DBPASS = 'Vd0l09##@';*/

 $DBNAME = 'duronto_db';
 $KEY 	 = 'dhfakHueyrer93KJr4042diJri0Nfk';
 $DBHOST = 'localhost';
 $DBUSER = 'root';
 $DBPASS = '';
 

$MainContentDirecoty = 'repository/'; /*Main Content CMS Directory Define*/
$BulkDirecoty = 'repository/bulk-upload/'; /*Bulk Upload Content Common Directory Define*/

define('TBL_USER','users'); 
define('TBL_CONTENTS','tbl_contents');
define('TBL_CAT','tbl_category');
define('TBL_SUB_CAT','tbl_sub_catergory');
define('TBL_ALBUM','tbl_album_list');
define('TBL_ALERT_THAILAND', 'tbl_content_alert_thailand');
define('TBL_REPORT_THAILAND','tbl_reports_thailand');


/************* LOCAL ************************/



function get_database_connection()
{
	global $DBHOST,$DBUSER,$DBPASS,$DBNAME;


	$link= mysql_connect($DBHOST,$DBUSER,$DBPASS,$DBNAME);

	if(!$link)
	{
		die('Could not connect:'.mysql_connect_error());
	}
	return $link;



}

/**********************************************************
## Function for user_login
## Parm: username and PassWord
## Return: name
*************************************************************/

function user_login(){

	global $DBNAME,$KEY;

	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '0';
	
	$db_connect = get_database_connection();

	mysql_select_db($DBNAME)or die('Cannot select DB');

	$password=md5($password);

	$sql=mysql_query("SELECT * FROM ".TBL_USER." WHERE username='$username' AND password='$password'");

	if($sql){

		$row=mysql_fetch_assoc($sql);

		if(!empty($row)){

			$user_id = $row['user_id'];
			$user_type = $row['user_type'];
			$now = date('Y-m-d H:i:s');

			$sql = mysql_query("UPDATE ".TBL_USER." SET status=1, login_at='$now' where user_id='$user_id'");

				if($sql){
					$date=date('Y_m_d');

						if (!file_exists("logs/authlog/"))
   							mkdir("logs/authlog/", 0777, true);

						$log = new Logger("logs/authlog/auth");
						$log->logWrite("$username|Logged In");

						$encrypt_username = encrypt($username, $KEY);
						$encrypt_user_id = encrypt($user_id, $KEY);


						$_SESSION['drnt_username']=$encrypt_username;
						$_SESSION['drnt_user_type']=$user_type;
						$_SESSION['drnt_user_id']=$encrypt_user_id;
						$_SESSION['alert_message']='Successfully Logged In.';
						return 1;

				}else{

					$_SESSION['alert_message']='Email and Password combinations are invalid.';
					return 4;
				}
			
		}else{

			$_SESSION['alert_message']='You are not Registered.';
			return 3;
		} 

	}else{
		$_SESSION['alert_message']='You are not Registered.';
		return 2;
	} 

	

}


/**********************************************************

## Function for encrypt
## Parm: user_id
## Return: result
*************************************************************/
function encrypt($string, $key) {
		$result = '';
		$str_len= strlen($string);
		for($i=0; $i<$str_len; $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		return base64_encode($result);
		
	}

/**********************************************************

## Function for decrypt
## Parm: user_id
## Return: result
*************************************************************/
function decrypt($string, $key) {
	$result = ' ';
	$string = base64_decode($string);
	$str_len = strlen($string);
	for($i=0; $i<$str_len; $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key,($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
		return $result;
}


/***********
### Loginchek
***************/
function loggedin(){

	if(isset($_SESSION['drnt_username'])){
		$session_value=$_SESSION['drnt_username'];
	
		if(isset($session_value) && !empty($session_value))
			return 1;
		else return 0;
		
	}else return 0;

}

/***********
### Logout
***************/

function logout(){

	global $DBNAME,$KEY;

	if(isset($_SESSION['drnt_user_id'])){

		$user_id = decrypt($_SESSION['drnt_user_id'], $KEY);
		$username = decrypt($_SESSION['drnt_username'], $KEY);

	    $now = date('Y-m-d H:i:s');
		$db_connect = get_database_connection();

		mysql_select_db($DBNAME)or die('Cannot select DB');

		$sql = mysql_query("UPDATE ".TBL_USER." SET status=0,login_at='$now' where user_id='$user_id'");

		if($sql){

			 unset($_SESSION['drnt_username']);
			 unset($_SESSION['drnt_user_id']);
			 unset($_SESSION['drnt_user_type']);
			 $_SESSION['alert_message']='Successfully Logged Out.';
			 $log = new Logger("logs/authlog/auth");
	   		 $log->logWrite("$username|Logged OUT");
	    	return 1;
		}else{

			$_SESSION['alert_message']='Something Wrong. Please Try again !!!.';
			return 0;
		}

	   
	   
	}else return 2;

	
}

/*********************
### get_username
**********************/

function get_username(){

	global $KEY;

	if(isset($_SESSION["username"]))
		$session_value=$_SESSION["username"];
	
	if(isset($session_value)){
		$username = decrypt(trim($session_value), $KEY);

		return $username;

	}else return false;

}



/**********************************************************
## Function for get_call_status
## Parm: $shortcode
## Return: report
*************************************************************/

function get_user_detail($user_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_USER." WHERE user_id='$user_id'");

	$row=mysql_fetch_array($sql);


	return $row;


}


/**********************************************************
## Function for category_insert
## Parm: 
## Return: report
*************************************************************/

function category_insert(){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$category_name = trim($_REQUEST['category_name']);
	$category_name_slug = explode(' ', strtolower($category_name));
	$category_name_slug = implode('_', $category_name_slug);
	$username = get_username();


	if(!empty($_FILES['banner_image']['tmp_name'])){
		$banner_path = banner_image_upload($category_name_slug);
	}else $banner_path='';

	
	$sql = mysql_query("INSERT INTO ".TBL_CAT." (category_name,category_name_slug,banner_image,created_by) VALUES('$category_name','$category_name_slug','$banner_path','$username')");

	if($sql){

		$_SESSION['alert_message'] = 'Category created Successfully.';
		return 1;
	}else{
		$_SESSION['alert_message'] = 'Please Try again later !!.';
		return 0;
	}


}


/**********************************************************
## Function for sub_category_insert
## Parm: 
## Return: report
*************************************************************/

function sub_category_insert(){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$sub_category_name = trim($_REQUEST['sub_category_name']);
	$sub_category_name_slug = explode(' ', strtolower($sub_category_name));
	$sub_category_name_slug = implode('_', $sub_category_name_slug);
	$username = get_username();

	$category_id = $_REQUEST['category_id'];

	
	$sql = mysql_query("INSERT INTO ".TBL_SUB_CAT." (sub_category_name,sub_category_name_slug,category_id,created_by) VALUES('$sub_category_name','$sub_category_name_slug','$category_id','$username')");


	if($sql){

		$_SESSION['alert_message'] = 'Sub Category created Successfully.';
		return 1;
	}else{
		$_SESSION['alert_message'] = 'Please Try again later !!.';
		return 0;
	}

}


/**********************************************************
## Function for sub_category_edit
## Parm: $sub_category_id
## Return: sqlll
*************************************************************/
    
function sub_category_edit($sub_category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

    $sqlll=mysql_fetch_assoc(mysql_query("SELECT * FROM ".TBL_SUB_CAT." WHERE sub_category_id='$sub_category_id'"));

    return $sqlll;

}

/**********************************************************
## Function for get_subcategory_with_cat
## Parm: $category_id
## Return: sql
*************************************************************/

function get_subcategory_with_cat($sub_category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$sql=mysql_fetch_assoc(mysql_query("SELECT ".TBL_SUB_CAT.".sub_category_name_slug, ".TBL_SUB_CAT.".sub_category_id,".TBL_CAT.".category_name_slug,".TBL_SUB_CAT.".category_id  FROM ".TBL_SUB_CAT." LEFT JOIN ".TBL_CAT." ON ".TBL_CAT.".category_id = ".TBL_SUB_CAT.".category_id WHERE ".TBL_SUB_CAT.".sub_category_id ='$sub_category_id'"));

	return $sql;

}

/**********************************************************
## Function for sub_category_update
## Parm: $sub_category_id
## Return: 1/0
*************************************************************/
    
function sub_category_update($sub_category_id){

	global $KEY,$DBNAME,$MainContentDirecoty;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	

	try{
			

			#Old Category
			$old_sub_category_info = get_subcategory_with_cat($sub_category_id);

			if (empty($old_sub_category_info)) {
		      throw new Exception("Sub Category information is Required."); 
		    }

			$update_sub_category_name = trim($_REQUEST['sub_category_name']);
			$update_category_id = $_REQUEST['category_id'];
			$update_sub_category_name_slug = explode(' ', strtolower($update_sub_category_name));
			$update_sub_category_name_slug = implode('_', $update_sub_category_name_slug);
			$update_username = trim(get_username());

			$update_category_info = get_category_info($update_category_id);
			$update_category_name = $update_category_info['category_name']; 


			$old_sub_category_name_slug = $old_sub_category_info['sub_category_name_slug'];

			#SubCategory Dir
			chmod($MainContentDirecoty.$old_sub_category_info['category_name_slug'], 0777); 
			 rename_dir($MainContentDirecoty.$old_sub_category_info['category_name_slug'],$MainContentDirecoty.$update_category_info['category_name_slug']);

			 chmod($MainContentDirecoty.$update_category_info['category_name_slug'].'/'.$old_sub_category_name_slug, 0777);
			 rename_dir($MainContentDirecoty.$update_category_info['category_name_slug'].'/'.$old_sub_category_name_slug,$MainContentDirecoty.$update_category_info['category_name_slug'].'/'.$update_sub_category_name_slug);

			 $old_content_dir = '/'.$old_sub_category_info['category_name_slug'].'/'.$old_sub_category_name_slug.'/';
			 $update_content_dir = '/'.$update_category_info['category_name_slug'].'/'.$update_sub_category_name_slug.'/';

			mysql_query("START TRANSACTION");
			$sql=mysql_query("UPDATE ".TBL_SUB_CAT." SET sub_category_name='$update_sub_category_name',sub_category_name_slug='$update_sub_category_name_slug',category_id='$update_category_id', created_by='$update_username' where sub_category_id='$sub_category_id'");

			$sql2 = mysql_query("UPDATE ".TBL_CONTENTS." SET content_category_name='$update_category_name', content_category_id='$update_category_id',content_preview=REPLACE(content_preview, '$old_content_dir', '$update_content_dir'),content_filepath=REPLACE(content_filepath,'$old_content_dir', '$update_content_dir') where content_sub_category_id='$sub_category_id'");

			
		    if($sql && $sql2){
		    	mysql_query("COMMIT");
				$_SESSION['alert_message'] = 'Sub Category Updated Successfully.';
				return 1;
			}else{
				mysql_query("ROLLBACK");
				$_SESSION['alert_message'] = 'Please Try again later ttt!!.';
				return 0;
			}

	}catch(Exception $e){
		mysql_query("ROLLBACK");
		$_SESSION['alert_message'] = $e->getMessage();
	  	 return 0;
			
	}

	

}

/**********************************************************
## Function for get_call_status
## Parm: $shortcode
## Return: report
*************************************************************/

function sub_category_delete($sub_category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	mysql_query("START TRANSACTION");
	$sql = mysql_query("DELETE FROM ".TBL_SUB_CAT." WHERE sub_category_id='$sub_category_id'");
	$sql2 = mysql_query("DELETE FROM ".TBL_CONTENTS." WHERE content_sub_category_id='$sub_category_id'");

	if($sql && $sql2){

		mysql_query("COMMIT");
		$_SESSION['alert_message'] = 'Sub Category Deleted Successfully.';
		return 1;
	}else{
		mysql_query("ROLLBACK");
		$_SESSION['alert_message'] = 'Please Try again later !!.';
		return 0;
	}

}


/**********************************************************
## Function for category_delete
## Parm: $shortcode
## Return: report
*************************************************************/

function category_delete($category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	mysql_query("START TRANSACTION");

	try{

	 	$sql = mysql_query("DELETE FROM ".TBL_CAT." WHERE category_id='$category_id'");
		$sql2 = mysql_query("DELETE FROM ".TBL_CONTENTS." WHERE content_category_id='$category_id'");
		$sql3= mysql_query("DELETE FROM ".TBL_SUB_CAT." WHERE category_id='$category_id'");

		if($sql && $sql2 && $sql3){
			mysql_query("COMMIT");
			$_SESSION['alert_message'] = 'Category Deleted Successfully.';
			return 1;

		}else{
			mysql_query("ROLLBACK");
			$_SESSION['alert_message'] = 'Please Try again later !!.';
			return 0;
		}
	}catch (Exception $e){
		mysql_query("ROLLBACK");
		$_SESSION['alert_message'] = 'Please Try again later !!.';
		return 0;
	}

}

/**********************************************************
## Function for category_edit
## Parm: $category_id
## Return: sql
*************************************************************/
    
function category_edit($category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

    $sqlll=mysql_fetch_assoc(mysql_query("SELECT * FROM ".TBL_CAT." WHERE category_id='$category_id'"));

    return $sqlll;

}

/**********************************************************
## Function for get_category_with_Sub
## Parm: $category_id
## Return: sql
*************************************************************/

function get_category_with_Sub($category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$sql=mysql_fetch_assoc(mysql_query("SELECT ".TBL_CAT.".*, count(".TBL_SUB_CAT.".sub_category_id) as sub_cat_count FROM ".TBL_CAT." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".category_id = ".TBL_CAT.".category_id WHERE ".TBL_CAT.".category_id ='$category_id' GROUP BY ".TBL_SUB_CAT.".category_id"));

	return $sql;

}


/**********************************************************
## Function for category_update
## Parm: $category_id
## Return: 1/0
*************************************************************/

    
function category_update($category_id){

	global $KEY,$DBNAME,$MainContentDirecoty;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	try {


			#Old Category
			$old_category_info = get_category_with_Sub($category_id);

			if (empty($old_category_info)) {
		      throw new Exception("Category information is Required."); 
		    }

		    if(!empty($_FILES['banner_image']['tmp_name'])){

				$banner_path = banner_image_upload($old_category_info['category_name_slug']);

			}else $banner_path=$old_category_info['banner_image'];


			#Update Category
			$update_category_name = trim($_REQUEST['category_name']);
			$update_category_name_slug = explode(' ', strtolower($update_category_name));
			$update_category_name_slug = implode('_', $update_category_name_slug);
			$username = trim(get_username());


			$old_category_name_slug = $old_category_info['category_name_slug'];
			$sub_cat_count = $old_category_info['sub_cat_count'];


			if($sub_cat_count==0){

				$old_content_dir = '/'.$old_category_name_slug.'/'.$old_category_name_slug.'/';
				$update_content_dir = '/'.$update_category_name_slug.'/'.$update_category_name_slug.'/';

				chmod($MainContentDirecoty.$old_category_name_slug, 0777);
				rename_dir($MainContentDirecoty.$old_category_name_slug,$MainContentDirecoty.$update_category_name_slug);

				chmod($MainContentDirecoty.$update_category_name_slug.'/'.$old_category_name_slug, 0777);
				rename_dir($MainContentDirecoty.$update_category_name_slug.'/'.$old_category_name_slug,$MainContentDirecoty.$update_category_name_slug.'/'.$update_category_name_slug);
			}else{

				$old_content_dir = '/'.$old_category_name_slug.'/';
				$update_content_dir = '/'.$update_category_name_slug.'/';

				chmod($MainContentDirecoty.$old_category_name_slug, 0777);
				rename_dir($MainContentDirecoty.$old_category_name_slug,$MainContentDirecoty.$update_category_name_slug);
			}

			mysql_query("START TRANSACTION");

			$sql=mysql_query("UPDATE ".TBL_CAT." SET category_name='$update_category_name',category_name_slug='$update_category_name_slug',banner_image='$banner_path', created_by='$username' where category_id='$category_id'");
		

			$sql2= mysql_query("UPDATE ".TBL_CONTENTS." SET content_category_name='$update_category_name', content_preview=REPLACE(content_preview, '$old_content_dir', '$update_content_dir'),content_filepath=REPLACE(content_filepath, '$old_content_dir', '$update_content_dir') where content_category_id='$category_id'");

			$sql3=mysql_query("UPDATE ".TBL_ALBUM." SET category='$update_category_name' where album_category_id='$category_id'");

		

		    if($sql || $sql2 || $sql3){
		    	mysql_query("COMMIT");
				$_SESSION['alert_message'] = 'Category Updated Successfully.';
				return 1;
			}else{

				 mysql_query("ROLLBACK");
				$_SESSION['alert_message'] = 'Please Try again later !!.';
				return 0;
			}
	    
	    // rest of code here...
	  }catch (Exception $e) {

	  	 $_SESSION['alert_message'] = $e->getMessage();

	  	 return 0;
	  
	  }

	

}

/**********************************************************
## Function for album_insert
## Parm: 
## Return: report
*************************************************************/

function album_insert(){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$album_name = trim($_REQUEST['album_name']);
	$album_details = trim($_REQUEST['album_details']);
	$featured = isset($_REQUEST['featured']) ? $_REQUEST['featured']:'' ;
	$tags = trim($_REQUEST['tags']);
	$created = date('Y-m-d H:i:s');

	$category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id']:'' ;
	$category_info = !empty($category_id) ? category_edit($category_id): Null;

	if(!empty($_FILES['poster_image']['tmp_name'])){
		$album_slug = explode(' ',trim(strtolower($album_name)));
		$album_slug = implode('_', $album_slug);
		$poster_path = poster_image_upload($album_slug);

		if(!empty($category_info)){
			$album_category_id = $category_id;
			$category = $category_info['category_name'];
		}else{
			$album_category_id = Null;
			$category = Null;
		}

		

		if(!empty($poster_path)){

			$sql = mysql_query("INSERT INTO ".TBL_ALBUM." (album_name,album_details,album_category_id,category,poster,featured,tags,created) VALUES('$album_name','$album_details','$album_category_id','$category','$poster_path','$featured','$tags','$created')");

			if($sql){

				$_SESSION['alert_message'] = 'Album created Successfully.';
				return 1;
			}else{
				$_SESSION['alert_message'] = 'Please Try again later !!.';
				return 0;
			}

		}else{
			$_SESSION['alert_message'] = 'Poster Upload error. !!.';
			return 0;
		}

	}else{
			$_SESSION['alert_message'] = 'Poster File Required. !!.';
			return 0;

	}

}

/**********************************************************
## Function for album_edit
## Parm: $shortcode
## Return: report
*************************************************************/
    
function album_edit($album_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

    $sqlll=mysql_fetch_assoc(mysql_query("SELECT * FROM ".TBL_ALBUM." WHERE album_id='$album_id'"));

    return $sqlll;


}
/**********************************************************
## Function for album_update
## Parm: $shortcode
## Return: report
*************************************************************/

function album_update($album_id){


	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$old_album_info = album_edit($album_id);

	$update_album_name = trim($_REQUEST['album_name']);
	$update_tags = trim($_REQUEST['tags']);
	$update_album_details = trim($_REQUEST['album_details']);
	$edit_poster_filepath = $_REQUEST['edit_poster_filepath'];
	$featured = isset($_REQUEST['featured']) ? $_REQUEST['featured']:'' ;


	$category_id = isset($_REQUEST['category_id']) ? $_REQUEST['category_id']:'' ;
	$category_info = !empty($category_id)? category_edit($category_id): Null;

	if(!empty($category_info)){
		$album_category_id = $category_id;
		$category = $category_info['category_name'];
	}else{
		$album_category_id = $old_album_info['album_category_id'];
		$category = $old_album_info['category'];
	}

	if(!empty($_FILES['poster_image']['tmp_name'])){

		$album_slug = explode(' ',trim(strtolower($update_album_name)));
		$album_slug = implode('_', $album_slug);
		$poster_path = poster_image_upload($album_slug);

		if(empty($poster_path)){
			$_SESSION['alert_message'] = 'Poster Upload error. !!.';
			return 0;
		}

	}else{
			
		$poster_path = $edit_poster_filepath;
	}

	
	$sql=mysql_query("UPDATE ".TBL_ALBUM." SET album_name='$update_album_name',album_details='$update_album_details',album_category_id='$album_category_id',category='$category', tags='$update_tags', poster='$poster_path',featured='$featured' where album_id='$album_id'");

	$sql2=mysql_query("UPDATE ".TBL_CONTENTS." SET content_album='$update_album_name', poster='$poster_path' where album_id='$album_id'");


    if($sql && $sql2){

		$_SESSION['alert_message'] = 'Album Updated Successfully.';
		return 1;
	}else{
		$_SESSION['alert_message'] = 'Please Try again later !!.';
		return 0;
	}

}


/**********************************************************
## Function for album_delete
## Parm: $shortcode
## Return: report
*************************************************************/

function album_delete($album_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("DELETE FROM ".TBL_ALBUM." WHERE album_id='$album_id'");

	if($sql){
		$_SESSION['alert_message'] = 'Album Deleted Successfully.';
		return 1;
	}else{
		$_SESSION['alert_message'] = 'Please Try again later !!.';
		return 0;
	}

}


/**********************************************************
## Function for content_publish
## Parm: $shortcode
## Return: report
*************************************************************/
    
function album_featured_manage($action,$album_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");
	
	if($action=="featured")
    	$sql=mysql_query("UPDATE ".TBL_ALBUM." SET featured='1' WHERE album_id ='$album_id'");
    else if($action=="undofeatured")
    	$sql=mysql_query("UPDATE ".TBL_ALBUM." SET featured='0' WHERE album_id ='$album_id'");
    else
    	return 0;

    return 1;
}



/**********************************************************
## Function for content_publish
## Parm: $shortcode
## Return: report
*************************************************************/
    
function content_publish($cid){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");
	$now = date('Y-m-d H:i:s');
    $sql=mysql_query("UPDATE ".TBL_CONTENTS." SET status='1',publish_date='$now' WHERE cid ='$cid'");

    return $sql;
}


/**********************************************************
## Function for content_undopublish
## Parm: $shortcode
## Return: report
*************************************************************/
    
function content_undopublish($cid){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");
	$now = date('Y-m-d H:i:s');
    $sql=mysql_query("UPDATE ".TBL_CONTENTS." SET status='0',publish_date='0000-00-00 00:00:00' WHERE cid ='$cid'");

    return $sql;


}


/**********************************************************
## Function for content_edit
## Parm: $shortcode
## Return: report
*************************************************************/
    
function content_edit($cid){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

    $sqlll=mysql_fetch_assoc(mysql_query("SELECT * FROM ".TBL_CONTENTS." WHERE cid='$cid'"));

        return $sqlll;


}


/**********************************************************
## Function for Content Update
## Parm: 
## Return: report
*************************************************************/

function content_update($cid){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$update_content_title = $_REQUEST['content_title'];
	$update_content_title_slug = explode(' ',trim(strtolower($update_content_title)));
	$update_content_title_slug = implode('_', $update_content_title_slug);
	$update_artist_name = $_REQUEST['artist_name'];
	$update_content_royality = $_REQUEST['content_royality'];
	$update_content_category_id = $_REQUEST['content_category_id'];
	$update_content_sub_category_id = isset($_REQUEST['content_sub_category_id']) ? $_REQUEST['content_sub_category_id']:0;
	$update_content_length = $_REQUEST['content_length'];
	$update_content_details =$_REQUEST['content_details'];
	$album_id = isset($_REQUEST['album_id']) ? $_REQUEST['album_id']:'';

	$update_content_preview = $_REQUEST['edit_content_preview'];
	$update_content_filepath = $_REQUEST['edit_content_filepath'];

	$update_category_info = get_category_info($update_content_category_id);
	$update_sub_category_info = get_sub_category_info($update_content_sub_category_id);

	$sub_category_name_slug = !empty($update_sub_category_info) ? $update_sub_category_info['sub_category_name_slug']: $update_category_info['category_name_slug'];
	
	$update_content_category_name=$update_category_info['category_name'];



	$image_file = $_FILES["content_preview"]['tmp_name'];
	$file=$_FILES['content_filepath']['name'];

		if($image_file != Null){
			$content_preview =preview_image_upload($update_category_info['category_name_slug'],$sub_category_name_slug,$update_content_title_slug);

		}else{ 
			$content_preview = $update_content_preview;
		}

		if($file!=Null){
			$content_filepath = content_file_upload($update_category_info['category_name_slug'],$sub_category_name_slug,$update_content_title_slug);
		}else{
			$content_filepath =$update_content_filepath;
		}


		$album_info = get_album_info($album_id);
		if(!empty($album_info)){
			$content_album = $album_info['album_name'];
			$poster = $album_info['poster'];
		}else{
			$album_id='';
			$content_album = 'Single';
			$poster = '';
		}


    	$sql=mysql_query("UPDATE ".TBL_CONTENTS." SET content_title='$update_content_title',content_title_slug='$update_content_title_slug', content_category_name='$update_content_category_name',content_category_id='$update_content_category_id', content_sub_category_id='$update_content_sub_category_id', content_preview='$content_preview', content_filepath='$content_filepath',  content_details='$update_content_details', label='$update_artist_name', content_royality='$update_content_royality', content_length='$update_content_length',album_id='$album_id',content_album='$content_album',poster='$poster' WHERE cid ='$cid'");

			$sql_query =$sql;


			if($sql_query){

				unset($_SESSION['form_data']);

				$log = new Logger("logs/contentlog/content");
	   		 	$log->logWrite("$username|content_details|$sql|updated");

	   		 	$_SESSION['alert_message']='Contents Updated Successfully !!!.';
                return 1;
			}


}


/**********************************************************
## Function for content_delete
## Parm: $shortcode
## Return: report
*************************************************************/

function content_delete($cid){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("DELETE FROM ".TBL_CONTENTS." WHERE cid='$cid'");

	if($sql){

		$_SESSION['alert_message'] = 'Contents Deleted Successfully.';
		return 1;
	}else{
		$_SESSION['alert_message'] = 'Please Try again later !!.';
		return 0;
	}

}



/**********************************************************
## Function for get_all_category
## Parm: 
## Return: report
*************************************************************/

function get_all_category(){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_CAT."  ");

	$all_category = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_category [] = $row;

		return $all_category;
	}

	return $all_category;
}


/**********************************************************
## Function for get_all_album
## Parm: 
## Return: report
*************************************************************/

function get_all_album(){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_ALBUM."  ");

	$all_album = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_album [] = $row;

		return $all_album;
	}

	return $all_album;
}



/**********************************************************
## Function for get_all_sub_category
## Parm: 
## Return: report
*************************************************************/

function get_all_sub_category(){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_SUB_CAT."  ");

	$all_sub_category = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_sub_category [] = $row;

		return $all_sub_category;
	}

	return $all_sub_category;
}


/**********************************************************
## Function for get_all_category
## Parm: 
## Return: report
*************************************************************/

function get_sub_category($category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_SUB_CAT." WHERE category_id='$category_id' ");

	$all_sub_category = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_sub_category [] = $row;

		return $all_sub_category;
	}

	return $all_sub_category;
}


/**********************************************************
## Function for get_category_info
## Parm: $shortcode
## Return: report
*************************************************************/

function get_category_info($category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_CAT." WHERE category_id='$category_id' ");

	$row=mysql_fetch_array($sql);

	return $row;

}

/**********************************************************
## Function for get_album_info
## Parm: $shortcode
## Return: report
*************************************************************/

function get_album_info($album_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_ALBUM." WHERE album_id='$album_id' ");

	$row=mysql_fetch_array($sql);

	return $row;

}


/**********************************************************
## Function for get_sub_category_info
## Parm: $shortcode
## Return: report
*************************************************************/

function get_sub_category_info($sub_category_id){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_SUB_CAT." WHERE sub_category_id='$sub_category_id' ");

	$row=mysql_fetch_array($sql);

	return $row;


}

/**********************************************************
## Function for get_album_by_name
## Parm: $shortcode
## Return: report
*************************************************************/

function get_album_by_name($album_name){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_ALBUM." WHERE album_name LIKE '$album_name' ");

	$row=mysql_fetch_array($sql);

	return $row;


}


/**********************************************************
## Function for get_category_by_name
## Parm: $shortcode
## Return: report
*************************************************************/

function get_category_by_name($category_name){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_CAT." WHERE category_name LIKE '$category_name' ");

	$row=mysql_fetch_array($sql);

	return $row;


}


/**********************************************************
## Function for get_sub_category_info
## Parm: $shortcode
## Return: report
*************************************************************/

function get_sub_category_by_name($sub_category_name){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT * FROM ".TBL_SUB_CAT." WHERE sub_category_name LIKE '$sub_category_name' ");

	$row=mysql_fetch_array($sql);

	return $row;


}



/**********************************************************
## Function for get_all_category
## Parm: 
## Return: report
*************************************************************/

function content_uploading(){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$_SESSION['form_data'] = $_REQUEST;


	$content_title = isset($_REQUEST['content_title']) ? $_REQUEST['content_title']:'';
	$content_title_slug = explode(' ',trim(strtolower($content_title)));
	$content_title_slug = implode('_', $content_title_slug);
	$label = isset($_REQUEST['label']) ? $_REQUEST['label']:'';
	$content_royality = isset($_REQUEST['content_royality']) ? $_REQUEST['content_royality']:'';
	$content_category_id = isset($_REQUEST['content_category_id']) ? $_REQUEST['content_category_id']:'';
	$content_sub_category_id = isset($_REQUEST['content_sub_category_id']) ? $_REQUEST['content_sub_category_id']:'';
	$content_length = isset($_REQUEST['content_length']) ? $_REQUEST['content_length']:'';
	$album_id = isset($_REQUEST['album_id']) ? $_REQUEST['album_id']:'';
	$content_details = isset($_REQUEST['content_details']) ? $_REQUEST['content_details']:'';
	$network = isset($_REQUEST['network']) ? $_REQUEST['network']:'';

	$category_info = get_category_info($content_category_id);
	$album_info = get_album_info($album_id);


	if(!empty($album_info)){
		$content_album = $album_info['album_name'];
		$poster = $album_info['poster'];
	}else{
		$album_id='';
		$content_album = 'Single';
		$poster = '';
	}

	$sub_category_info = get_sub_category_info($content_sub_category_id);


	$content_category_name = $category_info['category_name'];

	if(!empty($content_category_id) && !empty($category_info)){

		$sub_category_name_slug = !empty($sub_category_info) ? $sub_category_info['sub_category_name_slug']:$category_info['category_name_slug'];

		$content_preview = preview_image_upload($category_info['category_name_slug'],$sub_category_name_slug,$content_title_slug);

		$content_filepath = content_file_upload($category_info['category_name_slug'],$sub_category_name_slug,$content_title_slug);



		if(!empty($content_preview) && !empty($content_filepath) ){

			$sql = "INSERT INTO ".TBL_CONTENTS." (content_title, content_title_slug, content_category_name, content_category_id, content_sub_category_id, content_preview, content_filepath, content_details,label,content_royality,content_album,album_id,poster,content_length,new,status,created_by) VALUES ('$content_title', '$content_title_slug', '$content_category_name', '$content_category_id', '$content_sub_category_id', '$content_preview', '$content_filepath', '$content_details','$label','$content_royality','$content_album','$album_id','$poster','$content_length','1','0','$username')";

		
			$sql_query = mysql_query($sql);

			if($sql_query){

				unset($_SESSION['form_data']);

				$log = new Logger("logs/contentlog/content");
	   		 	$log->logWrite("$username|content_details|$sql|uploaded");

	   		 	$_SESSION['alert_message']='Contents Uploaded Successfully !!!.';
                return 1;
			}

		}else{

			$_SESSION['alert_message']='Contents Preview or File Uploaded error !!!.'.$content_preview.'/'.$content_filepath;

			return 0;
		} 



	}else{
		$_SESSION['alert_message']='Category or Sub Category Missing !!!.';
		return 0;
	}


}

/***********************************************************************
## Function for banner_image_upload
## Parm: $_File
## Return: $destinatin
***********************************************************************/

function banner_image_upload($category_name_slug){

	global $MainContentDirecoty;
	$username = get_username();

	$file = $_FILES["banner_image"]['tmp_name'];
	list($width, $height) = getimagesize($file);
    $file_ext   = array('jpg','png','gif','bmp','JPG','jpeg');
    $post_ext   = end(explode('.',$_FILES['banner_image']['name']));
    $photo_name = explode(' ', trim(strtolower($_FILES['banner_image']['name'])));
    $photo_name = implode('_', $photo_name);
    $photo_type = $_FILES['banner_image']['type'];
    $photo_size = $_FILES['banner_image']['size'];
    $photo_tmp  = $_FILES['banner_image']['tmp_name'];
    $photo_error= $_FILES['banner_image']['error'];
    
    if( in_array($post_ext,$file_ext) && ($photo_error == 0 )){
           
    		$fullpath ='../banner/';

    		/*directory create*/
			if (!file_exists($fullpath))
			 mkdir($fullpath, 0777, true);
            
            $destination = $fullpath.'banner_'.time().$photo_name;
            $uploadedpath = 'banner/banner_'.time().$photo_name;
            if(move_uploaded_file($photo_tmp,$destination)){

            	$log = new Logger("logs/contentlog/content");
	   		 	$log->logWrite("$username|banner|$destination|uploaded");

	   		 	$_SESSION['alert_message']='Image Uploaded Successfully !!!.';
                return $uploadedpath;

            }else{

            	$_SESSION['alert_message']='Directory Missing !!!.';
				return 0;
            }
    
    }else{

    	$_SESSION['alert_message']='Something Wrong in image. Please Try again !!!.';
			return 0;
    }

}
/***********************************************************************
## Function for poster_image_upload
## Parm: $_File
## Return: $destinatin
***********************************************************************/

function poster_image_upload($album_slug){

	global $MainContentDirecoty;
	$username = get_username();

	$file = $_FILES["poster_image"]['tmp_name'];
	list($width, $height) = getimagesize($file);
    $file_ext   = array('jpg','png','gif','bmp','JPG','jpeg');
    $post_ext   = end(explode('.',$_FILES['poster_image']['name']));
    $photo_name = explode(' ', trim(strtolower($_FILES['poster_image']['name'])));
    $photo_name = implode('_', $photo_name);
    $photo_type = $_FILES['poster_image']['type'];
    $photo_size = $_FILES['poster_image']['size'];
    $photo_tmp  = $_FILES['poster_image']['tmp_name'];
    $photo_error= $_FILES['poster_image']['error'];
    
    if( in_array($post_ext,$file_ext) && ($photo_error == 0 )){
           
    		$fullpath = $MainContentDirecoty.$album_slug.'/';

    		/*directory create*/
			if (!file_exists($fullpath))
			 mkdir($fullpath, 0777, true);
            
            $destination = $fullpath.'poster_'.$photo_name;
            if(move_uploaded_file($photo_tmp,$destination)){

            	$log = new Logger("logs/contentlog/content");
	   		 	$log->logWrite("$username|poster|$destination|uploaded");

	   		 	$_SESSION['alert_message']='Image Uploaded Successfully !!!.';
                return $destination;

            }else{

            	$_SESSION['alert_message']='Directory Missing !!!.';
				return 0;
            }
    
    }else{

    	$_SESSION['alert_message']='Something Wrong in image. Please Try again !!!.';
			return 0;
    }

}


/***********************************************************************
## Function for preview_image_upload
## Parm: $_File
## Return: $destinatin
***********************************************************************/

function preview_image_upload($category_slug,$sub_category_slug,$content_title_slug){

	global $MainContentDirecoty;
	$username = get_username();


	$file = $_FILES["content_preview"]['tmp_name'];
	list($width, $height) = getimagesize($file);
    $file_ext   = array('jpg','png','gif','bmp','JPG','jpeg');
    $post_ext   = end(explode('.',$_FILES['content_preview']['name']));
    $photo_name = explode(' ', trim(strtolower($_FILES['content_preview']['name'])));
    $photo_name = implode('_', $photo_name);
    $photo_type = $_FILES['content_preview']['type'];
    $photo_size = $_FILES['content_preview']['size'];
    $photo_tmp  = $_FILES['content_preview']['tmp_name'];
    $photo_error= $_FILES['content_preview']['error'];
    
    if( in_array($post_ext,$file_ext) && ($photo_error == 0 )){
           
    		$fullpath = $MainContentDirecoty.$category_slug."/".$sub_category_slug.'/'.$content_title_slug."/";

    		/*directory create*/
			if (!file_exists($fullpath))
			 mkdir($fullpath, 0777, true);
            
            $destination = $fullpath.time().'_'.$photo_name;
            if(move_uploaded_file($photo_tmp,$destination)){

            	$log = new Logger("logs/contentlog/content");
	   		 	$log->logWrite("$username|preview|$destination|uploaded");

	   		 	$_SESSION['alert_message']='Image Uploaded Successfully !!!.';
                return $destination;

            }else{

            	$_SESSION['alert_message']='Directory Missing !!!.';
            	
				return 0;
            }
    
    }else{

    	$_SESSION['alert_message']='Something Wrong in image. Please Try again !!!.';
			return 0;
    }

}


/***********************************************************************
## Function for content_file_upload
## Parm: $_File
## Return: $destinatin
***********************************************************************/

function content_file_upload($category_slug,$sub_category_slug,$content_title_slug){

	global $MainContentDirecoty;


	$username = get_username();
	
    $file_ext  = array('mp4','wav','mp3','3gp','amr','mvi','wmv','.apk','jpg','png','gif','bmp','JPG','jpeg');
    $post_ext  = end(explode('.',$_FILES['content_filepath']['name']));
    $file_name = explode(' ', trim(strtolower($_FILES['content_filepath']['name'])));
    $file_name = implode('_', $file_name);
    $file_type = $_FILES['content_filepath']['type'];
    $file_size = $_FILES['content_filepath']['size'];
    $file_tmp  = $_FILES['content_filepath']['tmp_name'];
    $file_error= $_FILES['content_filepath']['error'];

    if( in_array($post_ext, $file_ext) && ($file_error == 0 )){

           
    		$fullpath = $MainContentDirecoty.$category_slug."/".$sub_category_slug.'/'.$content_title_slug."/";

    		/*directory create*/
			if (!file_exists($fullpath))
			 mkdir($fullpath, 0777, true);
            
            $destination = $fullpath.time().'_'.$file_name;
            if(move_uploaded_file($file_tmp,$destination)){

            	$log = new Logger("logs/contentlog/content");
	   		 	$log->logWrite("$username|content|$destination|uploaded");

	   		 	$_SESSION['alert_message']='Content Uploaded Successfully !!!.';
                return $destination;

            }else{
            
            	$_SESSION['alert_message']='Directory Missing !!!.';
				return 0;

			
            }

    }else{
    	
    	$_SESSION['alert_message']='Something Wrong in video. Please Try again !!!.';
			return 0;
    	
    }

}

/**********************************************************
## Function for rename dir 
## Parm: $oldfile,$newfile
## Return: $days
*************************************************************/
function rename_dir($oldfile,$newfile) {

    if (!rename($oldfile,$newfile)) {
        if (copy ($oldfile,$newfile)) {
            unlink($oldfile);
            return TRUE;
        }
        return FALSE;
    }
    return TRUE;
}

/**********************************************************
## Function for get_all_date
## Parm: $from_date,$to_date
## Return: $days
*************************************************************/

function get_all_date($from_date,$to_date){
	$days = array();
	$stop = strtotime($to_date);
	for ($current = strtotime($from_date); $current <= $stop; $current = strtotime('+1 days', $current)) {
		$days[] = date('Y-m-d', $current);
	}
	
	return $days;
}

/**********************************
## Function for getUrlGenerator
## Parm: $_GET
## Return: true/false
***************/

function getUrlGenerator($url){

	$request_url = '';
	$flag=0;
 	foreach ($url as $key => $value) {

 		if(!empty($value) && ($key != 'page')){
 			
	 			$request_url .= '&'.$key.'='.$value;
 		}
 	}
 	return $request_url;
}

/**********************************
## Function for paginationGenerator
## Parm: $_GET
## Return: true/false
***************/
function paginationGenerator($num_rec_per_page,$total_records){

	if($total_records <= $num_rec_per_page)
		return false;

	$pagename = basename($_SERVER['PHP_SELF']);

	if(!empty($_GET))
		$request_url = getUrlGenerator($_GET);
	else $request_url='';

	if(isset($_GET['page'])&&(!empty($_GET['page'])))
		$current = $_GET['page'];
	else
		$current = 1;

	$paginate = '<ul class="pagination">';
	$total_pages = ceil($total_records / $num_rec_per_page);


    if($current>1){
        $arrow_left ="";
        $link = $pagename."?page=".($current-1);
    }else{
        $arrow_left="disabled";
        $link = "#";
    }
    $paginate .= '<li class="'.$arrow_left.'"><a href="'.$link.'" aria-label="Previous"><i class="fa fa-angle-left"></i></a></li>';


    for ($i=1;$i<=$total_pages;$i++){

    	
        if($current==$i)
            $paginate .= '<li class="active"><a href="'.$pagename.'?page='.$i.$request_url.'">' .$i.'</a><li>';
         else  $paginate .= '<li ><a href="'.$pagename.'?page='.$i.$request_url.'">' .$i.'</a><li>';
	 }



    if($current!=$total_pages){
        $arrow_right ="";
        $lastlink = $pagename."?page=".($current+1);
    }else{
        $arrow_right="disabled";
        $lastlink = "#";
    } 

    $paginate .= '<li class="'.$arrow_right.'"><a href="'.$lastlink.'" aria-label="Next"><i class="fa fa-angle-right"></i></a></li>';

	$paginate .= '<ul class="pagination">'; 

	return $paginate;
	
}


/**********************************
## Function for CurrentPagelink
## Parm: $_GET
## Return: true/false
***************/
function CurrentPagelink(){
	$url = $_GET;

	if(!empty($url)){
		$request_url = '?';
	 	foreach ($url as $key => $value) {

	 		if(!empty($value)){
		 			$request_url .= '&'.$key.'='.$value;
	 		}
	 	}
	}
	
 	$pagename = basename($_SERVER['PHP_SELF']);

 	if($request_url !='?')
 		$current_url = $pagename.$request_url;
 	else
 		$current_url = $pagename;

 	return $current_url;
}


/**********************************
## Function for CurrentRequest
## Parm: $_GET
## Return: true/false
***************/
function CurrentRequest(){
	$url = $_GET;

	if(!empty($url)){
		$request_url = '';
	 	foreach ($url as $key => $value) {

	 		if(!empty($value)){
		 			$request_url .= '&'.$key.'='.$value;
	 		}
	 	}
	}

 	return $request_url;
}


/**********************************************************
## Function for get_scheduled_news
## Parm: $status,$start_from,$num_rec_per_page
## Return: all_schedule
*************************************************************/

function all_category_list($start_from,$num_rec_per_page){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$last = $start_from+$num_rec_per_page;

	
	$sql = mysql_query("SELECT * FROM ".TBL_CAT." Order by category_id desc limit $start_from,$num_rec_per_page");
	$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CAT." ");
	$total_records = mysql_fetch_assoc($sql2);
	$all_category = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_category [] = $row;
	}

	$result = array(
					'total_records' => $total_records['total'],
					'all_category'  => $all_category
				);

	return $result;
	
}


/**********************************************************
## Function for all_album_list
## Parm: $status,$start_from,$num_rec_per_page
## Return: all_schedule
*************************************************************/

function all_album_list($start_from,$num_rec_per_page){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$last = $start_from+$num_rec_per_page;

	
	$sql = mysql_query("SELECT * FROM ".TBL_ALBUM." Order by album_id desc limit $start_from,$num_rec_per_page");
	$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_ALBUM." ");
	$total_records = mysql_fetch_assoc($sql2);
	$all_album = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_album [] = $row;
	}

	$result = array(
					'total_records' => $total_records['total'],
					'all_album'  => $all_album
				);

	return $result;
	
}


/**********************************************************
## Function for all_Sub_category_list
## Parm: $start_from,$num_rec_per_page
## Return: all_schedule
*************************************************************/

function all_Sub_category_list($start_from,$num_rec_per_page){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$last = $start_from+$num_rec_per_page;

	
	$sql = mysql_query("SELECT ".TBL_SUB_CAT.".*,".TBL_CAT.".category_name FROM ".TBL_SUB_CAT." INNER JOIN ".TBL_CAT." ON ".TBL_SUB_CAT.".category_id = ".TBL_CAT.".category_id Order by sub_category_id desc limit $start_from,$num_rec_per_page");

	$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_SUB_CAT." ");
	$total_records = mysql_fetch_assoc($sql2);
	$all_sub_category = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_sub_category [] = $row;
	}

	$result = array(
					'total_records' => $total_records['total'],
					'all_sub_category'  => $all_sub_category
				);

	return $result;
	
}


/**********************************************************
## Function for all_content_list
## Parm: $status,$start_from,$num_rec_per_page
## Return: all_schedule
*************************************************************/

function all_content_list($start_from,$num_rec_per_page){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$last = $start_from+$num_rec_per_page;

	
	

	if(isset($_REQUEST['category']) || isset($_REQUEST['sub_category'])){
		$category_id= $_REQUEST['category'];
		$sub_category_id= $_REQUEST['sub_category'];

		if(!empty($_REQUEST['category']) && ($_REQUEST['category']>0) && empty($_REQUEST['sub_category'])){

		

			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE ".TBL_CONTENTS.".content_category_id='$category_id' Order by cid desc limit $start_from,$num_rec_per_page");

			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." where content_category_id='$category_id' ");

		}else if(!empty($_REQUEST['category']) && ($_REQUEST['category']>0) && !empty($_REQUEST['sub_category']) && ($_REQUEST['sub_category']>0) ){



			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE ".TBL_CONTENTS.".content_category_id='$category_id' and ".TBL_CONTENTS.".content_sub_category_id='$sub_category_id' Order by cid desc limit $start_from,$num_rec_per_page");

			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." where content_category_id='$category_id' and content_sub_category_id='$sub_category_id'");

		}else{
			
			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id Order by cid desc limit $start_from,$num_rec_per_page");
			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." ");
		}

	}else{

	
		$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id Order by cid desc limit $start_from,$num_rec_per_page");
		$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." ");
	}
	
	$total_records = mysql_fetch_assoc($sql2);
	$all_content = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_content [] = $row;
	}

	$result = array(
					'total_records' => $total_records['total'],
					'all_content'  => $all_content
				);

	return $result;
	
}


/**********************************************************
## Function for all_published_content_list
## Parm: $status,$start_from,$num_rec_per_page
## Return: all_schedule
*************************************************************/

function all_published_content_list($start_from,$num_rec_per_page){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$last = $start_from+$num_rec_per_page;

	if(isset($_REQUEST['category']) || isset($_REQUEST['sub_category'])){
		$category_id= $_REQUEST['category'];
		$sub_category_id= $_REQUEST['sub_category'];

		if(!empty($_REQUEST['category']) && ($_REQUEST['category']>0) && empty($_REQUEST['sub_category'])){

			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE ".TBL_CONTENTS.".status=1 and ".TBL_CONTENTS.".content_category_id='$category_id' Order by cid desc limit $start_from,$num_rec_per_page");
			
			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." where status=1 and content_category_id='$category_id' ");

		}else if(!empty($_REQUEST['category']) && ($_REQUEST['category']>0) && !empty($_REQUEST['sub_category']) && ($_REQUEST['sub_category']>0) ){

			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE ".TBL_CONTENTS.".status=1 and ".TBL_CONTENTS.".content_category_id='$category_id' and ".TBL_CONTENTS.".content_sub_category_id='$sub_category_id' Order by cid desc limit $start_from,$num_rec_per_page");

			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." where status=1 and content_category_id='$category_id' and content_sub_category_id='$sub_category_id'");

		}else{
			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE status=1 Order by cid desc limit $start_from,$num_rec_per_page");
			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." status=1 ");
		}

	}else{
		$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE status=1 Order by cid desc limit $start_from,$num_rec_per_page");
		$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." status=1 ");
	}

	$total_records = mysql_fetch_assoc($sql2);
	$all_content = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_content [] = $row;
	}

	$result = array(
					'total_records' => $total_records['total'],
					'all_content'  => $all_content
				);

	return $result;
	
}


/**********************************************************
## Function for all_pending_content_list
## Parm: $status,$start_from,$num_rec_per_page
## Return: all_schedule
*************************************************************/

function all_pending_content_list($start_from,$num_rec_per_page){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$last = $start_from+$num_rec_per_page;



	if(isset($_REQUEST['category']) || isset($_REQUEST['sub_category'])){
		$category_id= $_REQUEST['category'];
		$sub_category_id= $_REQUEST['sub_category'];

		if(!empty($_REQUEST['category']) && ($_REQUEST['category']>0) && empty($_REQUEST['sub_category'])){

			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE ".TBL_CONTENTS.".status=0 and ".TBL_CONTENTS.".content_category_id='$category_id' Order by cid desc limit $start_from,$num_rec_per_page");
			
			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." where status=0 and content_category_id='$category_id' ");

		}else if(!empty($_REQUEST['category']) && ($_REQUEST['category']>0) && !empty($_REQUEST['sub_category']) && ($_REQUEST['sub_category']>0) ){

			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE ".TBL_CONTENTS.".status=0 and ".TBL_CONTENTS.".content_category_id='$category_id' and ".TBL_CONTENTS.".content_sub_category_id='$sub_category_id' Order by cid desc limit $start_from,$num_rec_per_page");

			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." where status=0 and content_category_id='$category_id' and content_sub_category_id='$sub_category_id'");

		}else{
			$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE status=0 Order by cid desc limit $start_from,$num_rec_per_page");
			$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." status=0 ");
		}

	}else{
		$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE status=0 Order by cid desc limit $start_from,$num_rec_per_page");
		$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." status=0 ");
	}

	
	$total_records = mysql_fetch_assoc($sql2);
	$all_content = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_content [] = $row;
	}

	$result = array(
					'total_records' => $total_records['total'],
					'all_content'  => $all_content
				);

	return $result;
	
}



/**********************************************************
## Function for all_pending_content_list
## Parm: $status,$start_from,$num_rec_per_page
## Return: all_schedule
*************************************************************/

function search_content_list(){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();



	mysql_select_db($DBNAME) or die("Cannot select DB");



	if(isset($_REQUEST['search_data']) && !empty($_REQUEST['search_data'])){

		$serach = trim($_REQUEST['search_data']);

	
		$sql = mysql_query("SELECT * FROM ".TBL_CONTENTS." WHERE content_title like '%$serach%' or content_category_name like '%$serach%' or content_details like '%$serach%' or label like '%$serach%' or content_royality like '%$serach%' or content_album like '%$serach%' or network like '%$serach%' group by cid Order by cid desc");


		$all_content = array();

		if($sql){
			while ($row = mysql_fetch_array($sql)) 			
				$all_content [] = $row;
		}
	

		return $all_content;

	}else{

		$_SESSION['alert_message']='Please enter a search query !!!.';
		return 0;
	}

	
	
}



/*******************************
### readerCSV
*******************************/
function readerCSV($csvFile){
 $file_handle = fopen($csvFile, 'r');
 while (!feof($file_handle) ) {
  $line_of_text[] = fgetcsv($file_handle, 1024);
 }

 fclose($file_handle);

 $_SESSION['csv_file_data']=$line_of_text;
 $_SESSION['csv_file_header']=$line_of_text[0];
 return $line_of_text;
}




/*******************************
### csvdataprocess
*******************************/
function csvdataprocess($all_data){
 $embeded = array();

 for($i=1;$i<count($all_data);$i++){
  for ($j=0; $j <count($all_data[0]) ; $j++) { 
    $k =$all_data[0][$j];
    if(!empty($all_data[$i][$j]))
    $embeded[$i][$k] = $all_data[$i][$j];  
  }

 }
	$_SESSION['csv_process_data']=$embeded;

 return $embeded;
}



/***********************************************************************
## Function for preview_image_upload
## Parm: $_File
## Return: $destinatin
***********************************************************************/

function bulkCsvUpload(){

	global $BulkDirecoty;

	$username = get_username();

	

    $file_ext  = array('csv');
    $post_ext  = end(explode('.',$_FILES['csv_file']['name']));
    $file_name = explode(' ', trim(strtolower($_FILES['csv_file']['name'])));
    $file_name = implode('_', $file_name);
    $file_type = $_FILES['csv_file']['type'];
    $file_size = $_FILES['csv_file']['size'];
    $file_tmp  = $_FILES['csv_file']['tmp_name'];
    $file_error= $_FILES['csv_file']['error'];

    if( in_array($post_ext, $file_ext) && ($file_error == 0 )){
    	$destination = $BulkDirecoty.time().'_'.$file_name;
	    if(move_uploaded_file($file_tmp,$destination)){

	    	$log = new Logger("logs/bulkupload/bulk");
			 	$log->logWrite("$username|bulk_file|$destination|uploaded");

			 	$_SESSION['alert_message']='Bulk File Uploaded Successfully !!!.';

			 	$_SESSION['bulk_csv_file'] = $destination;

	        return $destination;

	    }else{

	    	$_SESSION['alert_message']='Directory Missing !!!.';
			return 0;
	    }
    }else {
    	$_SESSION['alert_message']=' .csv File Required!!!.';
			return 0;
    }
    	
}

/**********************************************************
## Function for bulk_upload_preview
## Parm: 
## Return: report
*************************************************************/
function bulk_upload_preview($csvdataprocess)
{	
	global $BulkDirecoty;
	$preview_all_data = array();

	foreach ($csvdataprocess as $key => $data) {
		

		$upload_data['Title'] = (isset($data['Title']) && !empty($data['Title'])) ? $data['Title']:'<span class="alert alert-warning">Title Missing</span>';

		$upload_data['content_title'] = (isset($data['Title']) && !empty($data['Title'])) ? $data['Title']:'';



		if(isset($data['CategoryName']) && !empty($data['CategoryName'])){

			$category_info = get_category_by_name(trim($data['CategoryName']));

			$upload_data['CategoryName']= !empty($category_info) ? $data['CategoryName']:'<span class="alert alert-warning">Invalid CategoryName</span>';

			$upload_data['content_category_name'] = !empty($category_info) ? $category_info['category_name']:'';
			$upload_data['content_category_id'] = !empty($category_info) ? $category_info['category_id']:'';

		}else{

			$upload_data['CategoryName'] ='<span class="alert alert-warning">CategoryName Missing</span>';
			$upload_data['content_category_name'] = '';
			$upload_data['content_category_id'] = '';
		
		} 



		if(isset($data['SubCategoryName']) && !empty($data['SubCategoryName'])){

			$sub_category_info = get_sub_category_by_name($data['SubCategoryName']);

			$upload_data['SubCategoryName']= !empty($sub_category_info) ? $data['SubCategoryName']:'<span class="alert alert-warning">Invalid SubCategoryName</span>';
			$upload_data['content_sub_category_id'] = !empty($sub_category_info) ? $sub_category_info['sub_category_id']:'';

	

		}else{
			$upload_data['SubCategoryName'] ='<span class="alert alert-info">Single Category</span>';
			$upload_data['content_sub_category_id'] = '';
		} 


		if(isset($data['Album']) && !empty($data['Album'])){

			$album_info = get_album_by_name($data['Album']);

			$upload_data['Album']= !empty($album_info) ? $album_info['album_name']:$data['Album'].' <span class="alert alert-info">New Album</span>';

			$upload_data['album_id']= !empty($album_info) ? $album_info['album_id'] : -1;
			$upload_data['Poster'] = !empty($album_info) ? $album_info['poster'] :'<span class="alert alert-info">poster Missing</span>';
			$upload_data['content_album'] = !empty($album_info) ? $album_info['album_name'] :'';

			if($upload_data['album_id']==-1){
				$upload_data['Poster'] = (isset($data['Poster']) && !empty($data['Poster']) && file_exists($BulkDirecoty.$data['Poster'])) ? $data['Poster']:'<span class="alert alert-warning">Poster Missing</span>';
			}

		}else{
			$upload_data['Album'] ='<span class="alert alert-info">Single</span>';
			$upload_data['album_id']= 0;

		} 

		$upload_data['Label'] = (isset($data['Label ']) && !empty($data['Label']) ) ? $data['Label']:'<span class="alert alert-info">Label Missing</span>';
		

		$upload_data['Royality'] = (isset($data['Royality']) && !empty($data['Royality']) ) ? $data['Royality']:'<span class="alert alert-info">Royality  Missing</span>';
		

		$upload_data['Tags'] = (isset($data['Tags']) && !empty($data['Tags']) ) ? $data['Tags']:'<span class="alert alert-info">Tags Missing</span>';
		



		$upload_data['Network'] = (isset($data['Network']) && !empty($data['Network']) ) ? $data['Network']:'<span class="alert alert-info">Network Missing</span>';
		



		$upload_data['PreviewPath'] = (isset($data['PreviewPath']) && !empty($data['PreviewPath']) && file_exists($BulkDirecoty.$data['PreviewPath'])) ? $data['PreviewPath']:'<span class="alert alert-warning">PreviewPath Missing</span>';
		

		$upload_data['Filepath'] = (isset($data['Filepath']) && !empty($data['Filepath']) && file_exists($BulkDirecoty.$data['Filepath'])) ? $data['Filepath']:'<span class="alert alert-warning">Filepath Missing</span>';
		


		$upload_data['Details'] = (isset($data['Details']) && !empty($data['Details'])) ? $data['Details']:'<span class="alert alert-warning">Details Missing</span>';
		

		$upload_data['Length'] = (isset($data['Length']) && !empty($data['Length'])) ? $data['Length']:'<span class="alert alert-info">Length Missing</span>';

		

		$preview_all_data[]= $upload_data;
	}

	$_SESSION['preview_all_data'] = $preview_all_data;

	return $preview_all_data;
}


/**********************************************************
## Function for bulk_content_uploading
## Parm: 
## Return: report
*************************************************************/

function bulk_content_uploading($csvdataprocess){

	$row_count =0;
	foreach ($csvdataprocess as $key => $csvRowData) {
		$row = content_uploaderByCsvRowData($csvRowData);
		if($row==1)
			$row_count++;
	}
	unset($_SESSION['bulk_csv_file']);
	unset($_SESSION['csv_file_data']);
	unset($_SESSION['csv_file_header']);
	unset($_SESSION['csv_process_data']);
	$_SESSION['alert_message']='Empty csv File.';

	$_SESSION['alert_message'] = "Total uploaded Row :".($key)."<br>Inserted Row :".$row_count."<br>Error Row :".($key-$row_count);
	return 1;
}



/**********************************************************
## Function for content_uploaderByCsv
## Parm: $csvRowData
## Return: report
*************************************************************/

function content_uploaderByCsvRowData($csvRowData){

	global $KEY,$DBNAME,$BulkDirecoty,$MainContentDirecoty;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$_REQUEST = $csvRowData;


	if(isset($_REQUEST['Title']) && !empty($_REQUEST['Title']))
		$content_title =  $_REQUEST['Title'];
	else return 0;
	


	$content_title_slug = explode(' ',trim(strtolower($content_title)));
	$content_title_slug = implode('_', $content_title_slug);
	$content_royality = isset($_REQUEST['Royality']) ? $_REQUEST['Royality']:'';

	if(isset($_REQUEST['CategoryName']) && !empty($_REQUEST['CategoryName'])){

		$category_info = get_category_by_name(trim($_REQUEST['CategoryName']));

		if(!empty($category_info)){
			$content_category_name =  $category_info['category_name'];
			$content_category_id = $category_info['category_id'];
		}else return 0;
			
	}else return 0;

	if(isset($_REQUEST['SubCategoryName']) && !empty($_REQUEST['SubCategoryName'])){

		$sub_category_info = get_sub_category_by_name(trim($_REQUEST['SubCategoryName']));

		if(!empty($sub_category_info)){
			$sub_category_name_slug =   $sub_category_info['sub_category_name_slug'];
			$content_sub_category_id = $sub_category_info['sub_category_id'];
		}else{
			$sub_category_name_slug = $category_info['category_name_slug'];
			$content_sub_category_id='';
		}			

	}else{
		$sub_category_name_slug = $category_info['category_name_slug'];
		$content_sub_category_id = '';
	}

	if(isset($_REQUEST['Album']) && !empty($_REQUEST['Album'])){

			$album_info = get_album_by_name(trim($_REQUEST['Album']));

			if(!empty($album_info)){

				$content_album = !empty($album_info) ? $album_info['album_name']:'';
				$album_id = !empty($album_info) ? $album_info['album_id']:'';
				$poster = !empty($album_info) ? $album_info['poster']:'';

			}else{
				$create = album_create($_REQUEST['Album'],$_REQUEST['Poster'],$_REQUEST['Tags']);

				if($create==1){
					$album_info = get_album_by_name($_REQUEST['Album']);
					$content_album = !empty($album_info) ? $album_info['album_name']:'';
					$album_id = !empty($album_info) ? $album_info['album_id']:'';
					$poster = !empty($album_info) ? $album_info['poster']:'';
				}else{
					$content_album = "single";
					$album_id = '';
					$poster = '';
				}
			}

	}else{
		$content_album = "single";
		$album_id = '';
		$poster = '';
	}

	if(isset($_REQUEST['PreviewPath']) && !empty($_REQUEST['PreviewPath'])){

		$commonpath = $BulkDirecoty.$_REQUEST['PreviewPath'];

			if(file_exists($commonpath)==1){

				$file_name=basename($commonpath);

		    	$MainFilePath= $MainContentDirecoty.$category_info['category_name_slug'].'/'.$sub_category_name_slug.'/'.$content_title_slug.'/';
		  		
		  		$content_preview = filemoveByCopy($commonpath,$MainFilePath,$file_name);

				if(empty($content_preview))
					return 0;

			}else return 0;
	}


	if(isset($_REQUEST['Filepath']) && !empty($_REQUEST['Filepath'])){

		$commonpath = $BulkDirecoty.$_REQUEST['Filepath'];

			if(file_exists($commonpath)==1){

				$file_name=basename($commonpath);

		    	$MainFilePath= $MainContentDirecoty.$category_info['category_name_slug'].'/'.$sub_category_name_slug.'/'.$content_title_slug.'/';
		  		
		  		$content_filepath = filemoveByCopy($commonpath,$MainFilePath,$file_name);


				if(empty($content_filepath))
					return 0;
				
			}else return 0;
	}

	$content_details = isset($_REQUEST['Details']) ? $_REQUEST['Details']:'';
	$content_length	 = isset($_REQUEST['Length']) ? $_REQUEST['Length']:'';
	$network = isset($_REQUEST['Network']) ? $_REQUEST['Network']:'';
	$label = isset($_REQUEST['Label']) ? $_REQUEST['Label']:'';

	$sql = "INSERT INTO ".TBL_CONTENTS." (content_title, content_title_slug, content_category_name, content_category_id, content_sub_category_id, content_preview, content_filepath, content_details,label,content_royality,content_album,album_id,poster,content_length,new,status,created_by) VALUES ('$content_title', '$content_title_slug', '$content_category_name', '$content_category_id', '$content_sub_category_id', '$content_preview', '$content_filepath', '$content_details','$label','$content_royality','$content_album','$album_id','$poster','$content_length','1','0','$username')";

		
	$sql_query = mysql_query($sql);

	if($sql_query){

		$log = new Logger("logs/contentlog/content");
		$log->logWrite("$username|content_details|$sql|uploaded");


        return 1;

	}else return 0;

}


/**********************************************************
## Function for album_insert
## Parm: 
## Return: report
*************************************************************/

function album_create($album_name,$poster,$tags){

	global $KEY,$DBNAME,$BulkDirecoty,$MainContentDirecoty;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$album_details = "";
	$created = date('Y-m-d H:i:s');
	
	$album_slug = explode(' ',trim(strtolower($album_name)));
	$album_slug = implode('_', $album_slug);

	$commonpath = $BulkDirecoty.$poster;

	if(file_exists($commonpath)==1){

		$file_name=basename($commonpath);

    	$MainFilePath=$MainContentDirecoty.$album_slug.'/';
  		
  		$poster_path = filemoveByCopy($commonpath,$MainFilePath,$file_name);

		if(!empty($poster_path)){

			$sql = mysql_query("INSERT INTO ".TBL_ALBUM." (album_name,album_details,poster,tags,created) VALUES('$album_name','$album_details','$poster_path','$tags','$created')");
			return 1;
		}return 0;

	}else return 0;

}

/**********************************************************
## Function for csv_content_uploading
## Parm: 
## Return: report
*************************************************************/

 function filemoveByCopy($sourecpath,$MainFilePath,$file_name){
	if (!file_exists($MainFilePath))
 		mkdir($MainFilePath, 0777, true);

	$destination = $MainFilePath.time().'_'.$file_name;

	copy($sourecpath, $destination);

	return $destination;
		
}


/**********************************************************
## Function for csv_content_uploading
## Parm: 
## Return: report
*************************************************************/

function csv_content_uploading($csvdataprocess){


	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
for($i=1;$i<=count($csvdataprocess);$i++){

	$a=$csvdataprocess[$i];

	// $title=$csvdataprocess[$i]['Title'];
	
    $title=isset($csvdataprocess[$i]['Title']) ? $csvdataprocess[$i]['Title'] :''; 
	$content_title_slug = explode(' ',trim(strtolower($title)));
	$content_title_slug = implode('_', $content_title_slug);

    // $artist=$csvdataprocess[$i]['Label'];
    // $category=$csvdataprocess[$i]['Category Name'];
    // $sub_category=$csvdataprocess[$i]['Sub Category Name'];
    // $royality=$csvdataprocess[$i]['Royality'];
    // $album=$csvdataprocess[$i]['Album'];
    // $length= $csvdataprocess[$i]['Length'];
    // $previewpath=$csvdataprocess[$i]['Preview'];
    // $filepath=$csvdataprocess[$i]['Filepath'];
    // $details=$csvdataprocess[$i]['Details'];

    $artist=isset($csvdataprocess[$i]['Label']) ? $csvdataprocess[$i]['Label'] :''; 
    $category=isset($csvdataprocess[$i]['Category Name']) ? $csvdataprocess[$i]['Category Name'] :''; 
    $sub_category=isset($csvdataprocess[$i]['Sub Category Name']) ? $csvdataprocess[$i]['Sub Category Name'] :''; 
    $royality=isset($csvdataprocess[$i]['Royality']) ? $csvdataprocess[$i]['Royality'] :''; 
    $album=isset($csvdataprocess[$i]['Album']) ? $csvdataprocess[$i]['Album'] :''; 
    $length=isset($csvdataprocess[$i]['Length']) ? $csvdataprocess[$i]['Length'] :''; 
    $previewpath=isset($csvdataprocess[$i]['Preview']) ? $csvdataprocess[$i]['Preview'] :''; 
    $filepath=isset($csvdataprocess[$i]['Filepath']) ? $csvdataprocess[$i]['Filepath'] :''; 
    $details=isset($csvdataprocess[$i]['Details']) ? $csvdataprocess[$i]['Details'] :''; 



$sql = mysql_query("SELECT * FROM ".TBL_CAT." WHERE category_name LIKE '$category' ");
$category_info=mysql_fetch_array($sql);
$category_id= !empty($category_info['category_id']) ? $category_info['category_id']:'';
$category_slug= !empty($category_info['category_name_slug']) ? $category_info['category_name_slug']:'';


$sub_sql= mysql_query("SELECT * FROM ".TBL_SUB_CAT." WHERE sub_category_name LIKE '$sub_category'");
$sub_category_info=mysql_fetch_array($sub_sql);
$sub_category_id= !empty($sub_category_info['sub_category_id']) ? $sub_category_info['sub_category_id']:''; 
$sub_category_name_slug= !empty($sub_category_info['sub_category_name_slug']) ? $sub_category_info['sub_category_name_slug'] :'';


$album_sql = mysql_query("SELECT * FROM ".TBL_ALBUM." WHERE album_name LIKE '$album'");
$album_info=mysql_fetch_array($album_sql);
$album_id= !empty($album_info['albunm_id']) ? $album_info['albunm_id']:''; 
$album_name= !empty($album_info['album_name']) ? $album_info['album_name']:'';
$poster = !empty($album_info['poster']) ? $album_info['poster']:'';


$commonpath = $BulkDirecoty.$filepath;

if(file_exists($commonpath)==1){
   // if($filepath!=Null){
    	$file_name=basename($commonpath);

    	$MainFilePath=$MainContentDirecoty.$category_slug.'/'.$sub_category_name_slug.'/'.$content_title_slug.'/';
  			if (!file_exists($MainFilePath))
	 		mkdir($MainFilePath, 0777, true);
    	$destination = $MainFilePath.time().'_'.$file_name;

  		copy($commonpath, $destination);
    	$content_upload = $destination;
	// }

	// if($previewpath!=Null){
    	$commonpreviewpath = $BulkDirecoty.$previewpath;
if(file_exists($commonpreviewpath)==1){

    	$preview_name=basename($commonpreviewpath);

    	$MainPreviewPath= $MainContentDirecoty.$category_slug.'/'.$sub_category_name_slug.'/'.$content_title_slug.'/';

  			if (!file_exists($MainPreviewPath))
	 		mkdir($MainPreviewPath, 0777, true);
    	$DestinationPreview = $MainFilePath.time().'_'.$preview_name;

  		copy($commonpreviewpath, $DestinationPreview);
    	$content_preview_upload = $DestinationPreview;

	// }

	$sql = "INSERT INTO ".TBL_CONTENTS." (content_title, content_title_slug, label, content_category_name, content_category_id, content_sub_category_id, content_royality, content_album,album_id,poster,content_length, new, content_filepath, content_preview, content_details, created_by) VALUES ('$title', $content_title_slug, '$artist','$category', '$category_id', '$sub_category_id', '$royality', '$album_name','$album_id','$poster', '$length', '1', '$content_upload', '$content_preview_upload', '$details', '$username')";
		$sql_query = mysql_query($sql);
}
}

	}

	if(empty($sql_query)){
		$sql_query='';
	}


		if($sql_query){
		unset($_SESSION['form_data']);
		$log = new Logger("logs/contentlog/content");
		 	$log->logWrite("$username|content_details|$sql|uploaded");

		 	$_SESSION['alert_message']='CSV Uploaded Successfully !!!.';
             return 1;
		}





}


/***********************************************************************
## Function for video_upload
## Parm: $_File
## Return: $destinatin
***********************************************************************/

function video_upload(){

	global $MainContentDirecoty;

	$username = get_username();
	
    $file_ext  = array('mp4','wav','mp3','3gp','amr','mvi','wmv');
    $post_ext  = end(explode('.',$_FILES['video_file']['name']));
    $file_name = explode(' ', trim(strtolower($_FILES['video_file']['name'])));
    $file_name = implode('_', $file_name);
    $file_type = $_FILES['video_file']['type'];
    $file_size = $_FILES['video_file']['size'];
    $file_tmp  = $_FILES['video_file']['tmp_name'];
    $file_error= $_FILES['video_file']['error'];

    if( in_array($post_ext, $file_ext) && ($file_error == 0 )){

    
           
    		$fullpath = $MainContentDirecoty."videotest/";

    		/*directory create*/
			if (!file_exists($fullpath))
			 mkdir($fullpath, 0777, true);
            
            $destination = $fullpath.time().'_'.$file_name;
            if(move_uploaded_file($file_tmp,$destination)){

            	$log = new Logger("logs/contentlog/content");
	   		 	$log->logWrite("$username|content|$destination|uploaded");

	   		 	$_SESSION['alert_message']='Content Uploaded Successfully !!!.';
                return $destination;

            }else{

            	$_SESSION['alert_message']='Directory Missing !!!.';
				return 0;

			
            }

    }else{

    	$_SESSION['alert_message']='Something Wrong in video. Please Try again !!!.';
			return 0;
    	
    }

}


/**********************************************************
## Function for content_report_list
## Parm: 
## Return: all_report
*************************************************************/

function content_report_list(){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("SELECT count(tbl_contents.cid)as total_records,tbl_category.category_name, sum(tbl_contents.album_id > 0)as album_count, sum(tbl_contents.status=0) as unpublished,sum(tbl_contents.status=1) as published  FROM tbl_contents INNER JOIN tbl_category ON tbl_category.category_id = tbl_contents.content_category_id GROUP BY tbl_contents.content_category_id");


	$all_report = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_report [] = $row;
	}


	return $all_report;
	
}

/**********************************************************
## Function for content_report_list
## Parm: 
## Return: all_report
*************************************************************/

function content_report_counter($all_report){

	$total_category =0;
	$total_content=0;
	$total_album=0;
	$total_published=0;
	$total_unpublished=0;

	if(!empty($all_report) && count($all_report)>0){

		foreach ($all_report as $key => $report) {
			
			$total_content = $total_content+$report['total_records'];
			$total_album = $total_album+$report['album_count'];
			$total_unpublished = $total_unpublished+$report['unpublished'];
			$total_published = $total_published+$report['published'];
			$total_category++;
		}
	}

	$counter_data = array(
			'total_category'=>$total_category,
			'total_content'=>$total_content,
			'total_album'=>$total_album,
			'total_published'=>$total_published,
			'total_unpublished'=>$total_unpublished
		);

	return $counter_data;
	
}


/**********************************************************
## Function for alert_content_list
## Parm: $status,$start_from,$num_rec_per_page
## Return: result
*************************************************************/

function alert_content_list($start_from,$num_rec_per_page){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$last = $start_from+$num_rec_per_page;



	if(isset($_REQUEST['alert_category']) && !empty($_REQUEST['alert_category']) && ($_REQUEST['alert_category'] !='All') ){

		$alert_category= $_REQUEST['alert_category'];
		/*$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CONTENTS." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_SUB_CAT.".sub_category_id = ".TBL_CONTENTS.".content_sub_category_id WHERE ".TBL_CONTENTS.".content_category_name like '%$alert_category%' Order by cid desc limit $start_from,$num_rec_per_page");
		
		$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_CONTENTS." where status=0 and content_category_name like '%$alert_category%' ");*/

		$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CAT." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_CAT.".category_id = ".TBL_SUB_CAT.".sub_category_id LEFT JOIN ".TBL_CONTENTS." ON ".TBL_CAT.".category_id = ".TBL_CONTENTS.".content_category_id WHERE ".TBL_CAT.".category_name_slug like '$alert_category' Order by ".TBL_CONTENTS.".cid desc limit $start_from,$num_rec_per_page");


		$sql2 = mysql_query("SELECT count(".TBL_CONTENTS.".*) as total FROM ".TBL_CAT." LEFT JOIN ".TBL_CONTENTS." ON ".TBL_CAT.".category_id = ".TBL_CONTENTS.".content_category_id WHERE ".TBL_CAT.".category_name_slug like '$alert_category' ");

	}else{
		$sql = mysql_query("SELECT ".TBL_CONTENTS.".*,".TBL_SUB_CAT.".sub_category_name FROM ".TBL_CAT." LEFT JOIN ".TBL_SUB_CAT." ON ".TBL_CAT.".category_id = ".TBL_SUB_CAT.".sub_category_id LEFT JOIN ".TBL_CONTENTS." ON ".TBL_CAT.".category_id = ".TBL_CONTENTS.".content_category_id  Order by ".TBL_CONTENTS.".cid desc limit $start_from,$num_rec_per_page");


		$sql2 = mysql_query("SELECT count(".TBL_CONTENTS.".*) as total FROM ".TBL_CAT." LEFT JOIN ".TBL_CONTENTS." ON ".TBL_CAT.".category_id = ".TBL_CONTENTS.".content_category_id ");
	}

	
	$total_records = mysql_fetch_assoc($sql2);
	$all_content = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_content [] = $row;
	}

	$result = array(
					'total_records' => $total_records['total'],
					'all_content'  => $all_content
				);

	return $result;
	
}



/**********************************************************
## Function for alert_session_insert
## Parm: content_id
## Return: result
*************************************************************/

function alert_session_insert(){

	if(isset($_SESSION['alert_content'])){

		$old_alert = $_SESSION['alert_content'];

		if(!in_array($_REQUEST['alert_content_id'],$old_alert)){

			array_push($old_alert, $_REQUEST['alert_content_id']);
			$_SESSION['alert_content']=$old_alert;

		}else $_SESSION['alert_message'] ='Already Added';

	}else{
		$_SESSION['alert_content']=array($_REQUEST['alert_content_id']);
	}

	return true;
}

/**********************************************************
## Function for alert_schedule_insert
## Parm: 
## Return: result
*************************************************************/

function alert_schedule_insert(){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$counter=0;

	for($i=0;$i<$_REQUEST['alert_content_count'];$i++){

		if(isset($_REQUEST['send_date'][$i]) && isset($_REQUEST['expired_date'][$i]) && !empty($_REQUEST['send_date'][$i]) && !empty($_REQUEST['expired_date'][$i])){

			$interval = (strtotime($_REQUEST['expired_date'][$i])-strtotime($_REQUEST['send_date'][$i]))/86400;

			if($interval>0){


				$content_id = $_REQUEST['content_id'][$i];
		  		$send_date=$_REQUEST['send_date'][$i];
		  		$expired_date=$_REQUEST['expired_date'][$i];

				$checksql = mysql_query("SELECT count(*) as total FROM ".TBL_ALERT_THAILAND." WHERE content_id='$content_id' ");

				$records_exits = mysql_fetch_assoc($checksql);
				$records_exits_count= $records_exits['total'];

				$datesql = mysql_query("SELECT count(*) as total FROM ".TBL_ALERT_THAILAND." WHERE  send_date like '$send_date' ");
				$records_date = mysql_fetch_assoc($datesql);
				$records_date_count= $records_date['total'];

				if($records_exits_count==0 && $records_date_count<2){

					//$lastsql = mysql_query("SELECT MAX(srl) as last_srl FROM ".TBL_ALERT_THAILAND."  ");

					$lastsql = mysql_query("SELECT AUTO_INCREMENT as last_srl  FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$DBNAME' AND TABLE_NAME = '".TBL_ALERT_THAILAND."' ");

					$last_id = mysql_fetch_assoc($lastsql);
					$max_last_id= $last_id['last_srl'];

					$body = 'http://vdolog.mobi/TH_MT/view.php?push_id='.$max_last_id;

					$alertsql = mysql_query("INSERT INTO ".TBL_ALERT_THAILAND." (content_id,body,country,send_date,expire_date,validity,status) VALUES('$content_id','$body','Thailand','$send_date','$expired_date','$interval','1')");

					if($alertsql)
						$counter++;

				}

			}
		}
		
	}



	unset($_SESSION['alert_content']);
	$_SESSION['alert_message'] = "Total ".$counter."schedule has been inserted";
	mysql_close();

	return 1;

}

/**********************************************************
## Function for alert_schedule_list
## Parm: $status,$start_from,$num_rec_per_page
## Return: result
*************************************************************/

function alert_schedule_list($start_from,$num_rec_per_page){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$last = $start_from+$num_rec_per_page;



	if(isset($_REQUEST['alert_list']) && !empty($_REQUEST['alert_list']) && ($_REQUEST['alert_list'] =='Thailand') ){

		$alert_list= $_REQUEST['alert_list'];
		$sql = mysql_query("SELECT * FROM ".TBL_ALERT_THAILAND."  WHERE country like '$alert_list' Order by srl desc limit $start_from,$num_rec_per_page");
		$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_ALERT_THAILAND." where country like '$alert_list' ");

	}else{
		$sql = mysql_query("SELECT * FROM ".TBL_ALERT_THAILAND."  Order by srl desc limit $start_from,$num_rec_per_page");
		$sql2 = mysql_query("SELECT count(*) as total FROM ".TBL_ALERT_THAILAND."  ");

	}

	
	$total_records = mysql_fetch_assoc($sql2);
	$all_content = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_content [] = $row;
	}

	$result = array(
					'total_records' => $total_records['total'],
					'all_content'  => $all_content
				);

	return $result;
	
}

/**********************************************************
## Function for alert_schedule_delete
## Parm: $shortcode
## Return: report
*************************************************************/

function alert_schedule_delete($srl){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	
	$sql = mysql_query("DELETE FROM ".TBL_ALERT_THAILAND." WHERE srl='$srl'");

	if($sql){

		$_SESSION['alert_message'] = 'Alert schedule Deleted Successfully.';
		return 1;
	}else{
		$_SESSION['alert_message'] = 'Please Try again later !!.';
		return 0;
	}

}


/**********************************************************
## Function for alert_schedule_edit
## Parm: $srl
## Return: sqlll
*************************************************************/
    
function alert_schedule_edit($srl){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

    $schedule=mysql_fetch_assoc(mysql_query("SELECT * FROM ".TBL_ALERT_THAILAND." WHERE srl='$srl'"));

    return $schedule;

}

/**********************************************************
## Function for alert_schedule_insert
## Parm: 
## Return: result
*************************************************************/

function alert_schedule_update(){

	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	if(isset($_REQUEST['send_date']) && isset($_REQUEST['expired_date']) && !empty($_REQUEST['send_date']) && !empty($_REQUEST['expired_date'])){

		$interval = (strtotime($_REQUEST['expired_date'])-strtotime($_REQUEST['send_date']))/86400;

		if($interval>0){

			$alert_srl = $_REQUEST['alert_schedule_srl'];
			$content_id = $_REQUEST['content_id'];
	  		$send_date=$_REQUEST['send_date'];
	  		$expired_date=$_REQUEST['expired_date'];

			

			$datesql = mysql_query("SELECT count(*) as total FROM ".TBL_ALERT_THAILAND." WHERE  send_date like '$send_date' ");
			$records_date = mysql_fetch_assoc($datesql);
			$records_date_count= $records_date['total'];

			if($records_date_count<2){

				//$lastsql = mysql_query("SELECT MAX(srl) as last_srl FROM ".TBL_ALERT_THAILAND."  ");

				$alertsql = mysql_query("UPDATE ".TBL_ALERT_THAILAND." SET send_date='$send_date',expire_date='$expired_date',validity='$interval' where srl='$alert_srl'");
				mysql_close();

				$_SESSION['alert_message'] = "Content schedule has been updated";

			}else $_SESSION['alert_message'] = "Record Exits";

		}else $_SESSION['alert_message'] = "Invalid Send or Expire Date";

	}else $_SESSION['alert_message'] = "Send and Expire Date Required";
		
	return 1;

}

/**********************************************************
## Function for get_report_thailand
## Parm: $from_date,$to_date,$service
## Return: result
*************************************************************/

function get_report_thailand($from_date,$to_date,$service){


	global $KEY,$DBNAME;

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	if($service!='all')
		$sql = mysql_query("SELECT * FROM ".TBL_REPORT_THAILAND." WHERE `service_name`='$service' AND report_date BETWEEN '$from_date' AND '$to_date' GROUP BY report_date");
	else
		$sql = mysql_query("SELECT * FROM ".TBL_REPORT_THAILAND." WHERE report_date BETWEEN '$from_date' AND '$to_date' GROUP BY report_date");

	$all_data = array();

	if($sql){
		while ($row = mysql_fetch_array($sql)) 			
			$all_data [] = $row;

		return $all_data;
	}else return false;
}


/**********************************************************
## Function for game_uploading
## Parm: 
## Return: 0/1
*************************************************************/

function game_uploading(){

	global $KEY,$DBNAME;

	$username = get_username();

	$db_connect = get_database_connection();

	mysql_select_db($DBNAME) or die("Cannot select DB");

	$_SESSION['form_data'] = $_REQUEST;


	$game_title = isset($_REQUEST['game_title']) ? $_REQUEST['game_title']:'';
	$game_title_slug = explode(' ',trim(strtolower($game_title)));
	$game_title_slug = implode('_', $game_title_slug);
	
	$game_royality = isset($_REQUEST['game_royality']) ? $_REQUEST['game_royality']:'';
	$game_category_id = isset($_REQUEST['game_category_id']) ? $_REQUEST['game_category_id']:'';
	$game_sub_category_id = isset($_REQUEST['game_sub_category_id']) ? $_REQUEST['game_sub_category_id']:'';


	$game_details = isset($_REQUEST['game_details']) ? $_REQUEST['game_details']:'';
	$category_info = get_category_info($game_category_id);


	$album_id='';
	$game_album = 'Single';
	$poster = '';
	

	$sub_category_info = get_sub_category_info($game_sub_category_id);


	$game_category_name = $category_info['category_name'];

	if(!empty($game_category_id) && !empty($category_info)){

		$sub_category_name_slug = !empty($sub_category_info) ? $sub_category_info['sub_category_name_slug']:$category_info['category_name_slug'];

		$game_preview = preview_image_upload($category_info['category_name_slug'],$sub_category_name_slug,$game_title_slug);

		$game_filepath = content_file_upload($category_info['category_name_slug'],$sub_category_name_slug,$game_title_slug);



		if(!empty($game_preview) && !empty($game_filepath) ){

			$sql = "INSERT INTO ".TBL_CONTENTS." (content_title, content_title_slug, content_category_name, content_category_id, content_sub_category_id, content_preview, content_filepath, content_details,label,content_royality,content_album,album_id,poster,content_length,new,status,created_by) VALUES ('$game_title', '$game_title_slug', '$game_category_name', '$game_category_id', '$game_sub_category_id', '$game_preview', '$game_filepath', '$game_details','','$game_royality','$game_album','$album_id','$poster','','1','0','$username')";

		
			$sql_query = mysql_query($sql);

			if($sql_query){

				unset($_SESSION['form_data']);

				$log = new Logger("logs/contentlog/content");
	   		 	$log->logWrite("$username|game_details|$sql|uploaded");

	   		 	$_SESSION['alert_message']='Game Uploaded Successfully !!!.';
                return 1;
			}

		}else{

			$_SESSION['alert_message']='Game Preview or File Uploaded error !!!.'.$game_preview.'/'.$game_filepath;

			return 0;
		} 



	}else{
		$_SESSION['alert_message']='Category or Sub Category Missing !!!.';
		return 0;
	}


}






?>
