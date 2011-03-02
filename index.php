<?php

include_once 'inc/php/config.php';
include_once 'inc/php/functions.php';

//setup some variables/arrays
$action = array();
$action['result'] = null;

$text = array();

//check if the form has been submitted
if(isset($_POST['signup'])){

//	if ((($_FILES["file"]["type"] == "application/msword")
//	|| ($_FILES["file"]["type"] == "text/plain")
//	|| ($_FILES["file"]["type"] == "application/vnd.ms-excel"))
//	&& ($_FILES["file"]["size"] < 20000))
	if ($_FILES['file']['size'] < 500000)
	  {
	  if ($_FILES["file"]["error"] > 0)
	    {
#	    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
	    }
	  else
	    {
// debug info
//	    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//	    echo "Type: " . $_FILES["file"]["type"] . "<br />";
//	    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//	    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
	
	    if (file_exists("upload/" . $_FILES["file"]["name"]))
	      {
#	      echo $_FILES["file"]["name"] . " already exists. ";
	      }
	    else
	      {
	      $random = md5($_FILES["file"]["tmp_name"]);
	      $file_path = "upload/" . $random . "/";
	      mkdir($file_path, 0755);
	      $file_loc = $file_path . $_FILES["file"]["name"];
	      move_uploaded_file($_FILES["file"]["tmp_name"],
	      $file_loc);
//debug	      echo "Stored in: " . $file_path . $_FILES["file"]["name"];
	      }
	    }
	  }
	else
	  {
	  echo "Invalid file";
	  echo "Size: " . ($_FILES["file"]["size"]) . " <br />";
	  }

	//cleanup the variables
	//prevent mysql injection
	$file_location = '';
        $company = mysql_real_escape_string($_POST['company']);
	$username = mysql_real_escape_string($_POST['username']);
	$phone = mysql_real_escape_string($_POST['phone']);
	$email = mysql_real_escape_string($_POST['email']);
	$user_ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
	$user_referrer = $_SERVER['HTTP_REFERER'];
        $message = mysql_real_escape_string($_POST['message']);
	
	//quick/simple validation
        if(empty($company)){ $action['result'] = 'error'; array_push($text,'Please enter a Company Name'); }
	if(empty($username)){ $action['result'] = 'error'; array_push($text,'You forgot your username'); }
	if(empty($phone)){ $action['result'] = 'error'; array_push($text,'You forgot your phone number'); }
	if(empty($email)){ $action['result'] = 'error'; array_push($text,'You forgot your email'); }
	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
		$action['result'] = 'error'; array_push($text,'Improperly Formatted Email. Please retry.'); }
	
	if($action['result'] != 'error'){
				
		$phone = md5($phone);	
			
                $add = mysql_query("INSERT INTO `users` (username, password, email, message, user_ip, user_agent, user_referrer, company, file_location) VALUES ('$username', '$phone', '$email', '$message', '$user_ip', '$user_agent', '$user_referrer', '$company', '$file_loc')");

		
		if($add){
			
			//get the new user id
			$userid = mysql_insert_id();
			//$userid = $my_id;
			
			//create a random key
			$key = $username . $email . date('mYu');
			$key = md5($key);
			
			//add confirm row
			$confirm = mysql_query("INSERT INTO `confirm` VALUES(NULL,'$userid','$key','$email',CURRENT_TIMESTAMP)");	
			
			if($confirm){
			
				//include the swift class
				include_once 'inc/php/swift/swift_required.php';
			
				//put info into an array to send to the function
				$info = array(
					'username' => $username,
					'email' => $email,
                                        'company' => $company,
					'key' => $key);
			
				//send the email
				if(send_email($info)){
								
					//email sent
					$action['result'] = 'success';
					array_push($text,'Thanks for your submission. Please check your email for confirmation!');
				
				}else{
					
					$action['result'] = 'error';
					array_push($text,'Could not send confirm email');
				
				}
			
			}else{
				
				$action['result'] = 'error';
				array_push($text,'Confirm row was not added to the database. Reason: ' . mysql_error());
				
			}
			
		}else{
		
			$action['result'] = 'error';
			array_push($text,'User could not be added to the database. Reason: ' . mysql_error());
		
		}
	
	}
	
	$action['text'] = $text;
 
}

?>

<?php
include 'inc/elements/header.php'; ?>

<?= show_errors($action); ?>

<h2>Step 1</h2>
<h3>Let us know who you are. This information will be used in the authentication process.</h3>


<form method="post" action="" enctype="multipart/form-data">

    <fieldset>
    
    	<ul>
                <li>
                        <label for="company">Company Name:</label>
                        <input type="text" name="company" />
                </li>

    		<li>
    			<label for="username">Your Name:</label>
    			<input type="text" name="username" />
    		</li>
    		<li>
    			<label for="phone">Phone Number:</label>
    			<input type="text" name="phone" />
    		</li>
    		<li>
    			<label for="email">Your Email:</label>
    			<input type="text" name="email" />	
    		</li>
		<li>
                        <label for="message">Comments:</label><br>
			<textarea name="message" id="message" rows="10" cols="70"></textarea>
    		</li>
		<li>
		<div id='upload' class='centerdiv'>
			<br><br>
			<h2>Step 2</h2>
			<h3>Download the Security Profile Sheet</h3>
			<input type="hidden" id="expire_time" value="" />
			<span class="steps"><a href="Security_profile_a.xls"><img src="images/xls_ico.png" border="0" width="100" height="100" alt="Click to download the security profile form" /></a></span><br /><br />
			<br><br>
			<h2>Step 3</h2>
			<h3>Upload your edited security profile</h3>
			<label for="file"></label>
			<input type="file" name="file" id="file" />
			<br /><br />

			<input type="hidden" id="filekey" value="" />
			<div id="uploadbutton"><input type="submit" value="Submit for Review" class="large blue button" name="signup" /></div>
			<span id="error"></span>
		</div>

	        </li>
    	</ul>
    	
    </fieldset>
    
</form>			
		
<?php
include 'inc/elements/footer.php'; ?>
