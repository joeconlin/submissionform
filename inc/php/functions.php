<?php

function format_email($info, $format){

	//set the root
	$root = $_SERVER['DOCUMENT_ROOT'].'';

	//grab the template content
	$template = file_get_contents($root.'/signup_template.'.$format);
			
	//replace all the tags
	$template = ereg_replace('{USERNAME}', $info['username'], $template);
	$template = ereg_replace('{EMAIL}', $info['email'], $template);
        $template = ereg_replace('{COMPANY}', $info['company'], $template);
	$template = ereg_replace('{KEY}', $info['key'], $template);
//        $template = ereg_replace('{USER_IP}', $info['user_ip'], $template);
//        $template = ereg_replace('{USER_AGENT}', $info['user_agent'], $template);
//        $template = ereg_replace('{USER_REFERRER}', $info['user_referrer'], $template);
	$template = ereg_replace('{SITEPATH}','https://sitelocation.com/', $template);
		
	//return the html of the template
	return $template;

}

function format_confirm_email($info, $format){

        //set the root
        $root = $_SERVER['DOCUMENT_ROOT'].'';

        //grab the template content
        $template = file_get_contents($root.'/confirm_template.'.$format);

        //replace all the tags
        $template = ereg_replace('{USERNAME}', $info['username'], $template);
        $template = ereg_replace('{EMAIL}', $info['email'], $template);
        $template = ereg_replace('{COMPANY}', $info['company'], $template);
        $template = ereg_replace('{USER_IP}', $info['user_ip'], $template);
        $template = ereg_replace('{USER_AGENT}', $info['user_agent'], $template);
        $template = ereg_replace('{USER_REFERRER}', $info['user_referrer'], $template);
        $template = ereg_replace('{MESSAGE}', $info['message'], $template);
        $template = ereg_replace('{SITEPATH}','https://sitelocation.com/', $template);
        $template = ereg_replace('{FILE_LOCATION}', $info['file_location'], $template);

        //return the html of the template
        return $template;

}

//send the welcome letter
function send_email($info){
		
	//format each email
	$body = format_email($info,'html');
	$body_plain_txt = format_email($info,'txt');

	//setup the mailer
	$transport = Swift_MailTransport::newInstance();
	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance();
	$message ->setSubject('Information Submission Received');
	$message ->setFrom(array('blackhole@example.com' => 'from me'));
	$message ->setTo(array($info['email'] => $info['username']));
	
	$message ->setBody($body_plain_txt);
	$message ->addPart($body, 'text/html');
			
	$result = $mailer->send($message);
	
	return $result;
	
}

//send the welcome letter
function send_confirm_email($info){

        //format each email
        $my_body = format_confirm_email($info,'html');
        $my_body_plain_txt = format_confirm_email($info,'txt');

        //setup the mailer
        $my_transport = Swift_MailTransport::newInstance();
        $my_mailer = Swift_Mailer::newInstance($my_transport);
        $my_message = Swift_Message::newInstance();
        $my_message ->setSubject('Information Submission Confirmed');
        $my_message ->setFrom(array('blackhole@example.com' => 'Information'));
        $my_message ->setTo(array('address@example.com' => 'Authorized Reviewer'));

        $my_message ->setBody($my_body_plain_txt);
        $my_message ->addPart($my_body, 'text/html');

        $my_result = $my_mailer->send($my_message);

        return $my_result;

}


//cleanup the errors
function show_errors($action){

	$error = false;

	if(!empty($action['result'])){
	
		$error = "<ul class=\"alert $action[result]\">"."\n";

		if(is_array($action['text'])){
	
			//loop out each error
			foreach($action['text'] as $text){
			
				$error .= "<li><p>$text</p></li>"."\n";
			
			}	
		
		}else{
		
			//single error
			$error .= "<li><p>$action[text]</p></li>";
		
		}
		
		$error .= "</ul>"."\n";
		
	}

	return $error;

}
