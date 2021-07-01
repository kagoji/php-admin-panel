<?php
include('functions.php');


/*Ajax Sub Category*/
if(isset($_REQUEST['get_sub_cat'])){

	if(!empty($_REQUEST['content_category_id'])){

		$all_sub_category = get_sub_category($_REQUEST['content_category_id']);
		$list ='<option value="">Select Sub Category</option>';
		if(!empty($all_sub_category)){
			foreach ($all_sub_category as $key => $sub_category) {
				$list.= '<option value='.$sub_category['sub_category_id'].'>'.$sub_category["sub_category_name"].'</option>';
			}
		}

		echo $list;
	}
}



?>