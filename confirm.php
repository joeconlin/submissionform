<?php

include_once 'inc/php/analytics.php';
include_once 'inc/php/config.php';
include_once 'inc/php/functions.php';
include_once 'inc/php/swift/swift_required.php';
?>

<?php
include 'inc/elements/header.php'; ?>

<?php

//setup some variables
$action = array();
$action['result'] = null;

//check if the $_GET variables are present
	
//quick/simple validation
if(empty($_GET['email']) || empty($_GET['key'])){
	$action['result'] = 'error';
	$action['text'] = 'We are missing variables. Please double check your email.';			
}
		
if($action['result'] != 'error'){

	//cleanup the variables
	$email = mysql_real_escape_string($_GET['email']);
	$key = mysql_real_escape_string($_GET['key']);
	
	//check if the key is in the database
	$check_key = mysql_query("SELECT * FROM `confirm` WHERE `email` = '$email' AND `key` = '$key' LIMIT 1") or die(mysql_error());
	
	if(mysql_num_rows($check_key) != 0){
				
		//get the confirm info
		$confirm_info = mysql_fetch_assoc($check_key);
		
		//confirm the email and update the users database
		$update_users = mysql_query("UPDATE `users` SET `active` = 1 WHERE `id` = '$confirm_info[userid]' LIMIT 1") or die(mysql_error());

		// grab the user data
		$user_result = mysql_query("SELECT * FROM `users` WHERE `id` = '$confirm_info[userid]' LIMIT 1") or die(mysql_error());
		while ($row = mysql_fetch_assoc($user_result)) {
                        $user_name = $row["username"];
			$user_email = $row["email"];
			$user_ip = $row["user_ip"];
			$user_agent = $row["user_agent"];
			$user_referrer = $row["user_referrer"];
			$user_company = $row["company"];
			$user_file_location = $row["file_location"];
			$user_message = $row["message"];
			
			$info = array(
				'username' => $user_name,
				'email' => $user_email,
				'user_ip' => $user_ip,
				'user_agent' => $user_agent,
				'user_referrer' => $user_referrer,
				'company' => $user_company,
				'file_location' => $user_file_location,
				'message' => $user_message);
			if(send_confirm_email($info)){
				$action['result'] = 'success';
				array_push($text,'Email Sent (i hope)');
			}else{

                                $action['result'] = 'error';
                                array_push($text,'Could not send confirm email');
                                }
		}

		//delete the confirm row
		$delete = mysql_query("DELETE FROM `confirm` WHERE `id` = '$confirm_info[id]' LIMIT 1") or die(mysql_error());
		
		if($update_users){
			$action['result'] = 'success';
			$action['text'] = 'Your submission has been confirmed and is pending review. Thank-You!';
		}else{
			$action['result'] = 'error';
			$action['text'] = 'The request could not be updated Reason: '.mysql_error();;
		}
	}else{
		$action['result'] = 'error';
		$action['text'] = 'The key and email is not in our database.';
	}
}
?>

<?= 
show_errors($action); ?>

<?php
include 'inc/elements/footer.php'; ?>
