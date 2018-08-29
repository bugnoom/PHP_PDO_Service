<?php
ini_set("display_errors","1");
    	require_once('class/PushNotifications.php');
	require_once('class/config.php');

	$service = new services();
	$action = $_REQUEST['action'];

	switch($action){
		case "TestSend":
			$sql = "Select * From fixx_technic Where tech_id=:id";
			$arr = array(":id"=>$_REQUEST['id']);
			$data = $service->raw_select_sql($sql,$arr);
			foreach($data as $row){
			  $device = $row['device_name'];
			  $token = $row['device_token'];
			}
			$msg_payload = array(	'mtitle'=>"Fixxhome Test send",
						'mdesc'=>"TEST SEND MESSAGE Via PUSH");
			if($device == "iOS"){
			    PushNotifications::iOS($msg_payload,$token);
			}else{
			  PushNotifications::android($msg_payload,$token);
			}
		break;
		case "customer_order":
			$sql = "SELECT fixx_order.id as order_id,fixx_order.*,fixx_technic.* FROM `fixx_order` inner join fixx_technic on fixx_order.tech_id = fixx_technic.tech_id WHERE fixx_order.id=:id";
			$arr = array(":id"=>$_REQUEST['id']);
			$data = $service->raw_select_sql($sql,$arr);
			foreach($data as $row){
				$device = $row['device_name'];
				$token = $row['device_token'];
			}
			$msg_payload = array('mtitle'=>$_REQUEST['texttitle'],
					     'mdesc' =>$_REQUEST['textdesc']);		
	if($device == 'iOS'){
				PushNotifications::iOS($msg_payload,$token);
			}else{
				PushNotifications::android($msg_payload,$token);
			}
		break;

		}

/*
	// Message payload
	$msg_payload = array (
		'mtitle' => 'Test push notification title',
		'mdesc' => 'Test push notification body',
	);


	
	// For Android
	$regId = 'APA91bHdOmMHiRo5jJRM1jvxmGqhComcpVFDqBcPfLVvaieHeFI9WVrwoDeVVD1nPZ82rV2DxcyVv-oMMl5CJPhVXnLrzKiacR99eQ_irrYogy7typHQDb5sg4NB8zn6rFpiBuikNuwDQzr-2abV6Gl_VWDZlJOf4w';
	// For iOS
	$deviceToken = 'FE66489F304DC75B8D6E8200DFF8A456E8DAEACEC428B427E9518741C92C6660';
	// For WP8
	$uri = 'http://s.notify.live.net/u/1/sin/HmQAAAD1XJMXfQ8SR0b580NcxIoD6G7hIYP9oHvjjpMC2etA7U_xy_xtSAh8tWx7Dul2AZlHqoYzsSQ8jQRQ-pQLAtKW/d2luZG93c3Bob25lZGVmYXVsdA/EKTs2gmt5BG_GB8lKdN_Rg/WuhpYBv02fAmB7tjUfF7DG9aUL4';
	
	// Replace the above variable values
	
	
    	PushNotifications::android($msg_payload, $regId);
    	
  //  	PushNotifications::WP8($msg_payload, $uri);
    	
    	PushNotifications::iOS($msg_payload, $deviceToken);
*/
?>
