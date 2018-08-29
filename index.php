<?php  ini_set("display_errors",1);
 include "class/config.php";

$service = new services();

$action = $_GET['action'];
$authen_key = (!isset($_REQUEST['auth_key'])?"":$_REQUEST['auth_key']);


//echo md5("bugnoom_redonion");
function generateRandomString($length = 6) {
    $characters = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


switch($action){
    case "get_technic_by_amphur":
        $sql = "SELECT * FROM `fixx_technic` AS ft INNER JOIN fixx_tech_type AS fty on ft.tech_type_id = fty.tech_type_id INNER JOIN fixx_tech_set_active as st on st.tech_id = ft.tech_id Where ft.tech_aumphur = :id and ft.is_active='1'and st.tech_active_id > 1 and ft.tech_type_id = :type_id order by tech_class_id,tech_aumphur asc  ";
        $arr = array(":id"=>$_REQUEST['amphur_code'],
                    ":type_id"=>$_REQUEST['type_id']);
        $data = $service->raw_select_sql($sql,$arr);
        echo json_encode($data);
        break;
        
    case "get_technic_by_type":
        $sql = "SELECT * FROM `fixx_technic` AS ft INNER JOIN fixx_tech_type AS fty on ft.tech_type_id = fty.tech_type_id INNER JOIN fixx_tech_set_active as st on st.tech_id = ft.tech_id Where ft.tech_type_id = :id and ft.is_active='1' and st.tech_active_id >1 order by tech_class_id,tech_aumphur ASC";
        $arr = array(":id"=>$_REQUEST['type_id']);
        $data = $service->raw_select_sql($sql,$arr);
        echo json_encode($data);
        break;
        
    case "get_technic_detail":
        $sql = "SELECT * FROM `fixx_technic` AS ft INNER JOIN fixx_tech_type AS fty on ft.tech_type_id = fty.tech_type_id Where ft.tech_id = :tech_id";
        $arrbind = array(":tech_id"=>$_REQUEST['tech_id']);//$_REQUEST['tech_id']);
        $data = $service->raw_select_sql($sql,$arrbind);
        echo json_encode($data);
    break;

	case "get_user_detail":
		$sql = "Select * From fixx_user Where id = :id";
		$arr = array(":id"=>$_GET['id']);
		$data = $service->raw_select_sql($sql,$arr);
		echo json_encode($data);
	break;
	case "update_user_detail":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
		$sql = "UPDATE fixx_user SET password = :password, firstname = :firstname, mobile=:mobile, address=:address,province_code=:province Where id=:id";
		$arr = array(":password"=>$alldata->password,
			     ":firstname"=>$alldata->firstname,
			     ":mobile"=>$alldata->mobile,
			     ":address"=>$alldata->address,
			     ":province"=>$alldata->province,
			     ":id"=>$alldata->id
			   );
		$data = $service->raw_insert_data($sql,$arr);
		echo json_encode($data);
	break;
 
	case "add_favorite":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
		$sql = "insert into fixx_favorite(user_id,tech_id)VALUES(:user_id,:tech_id)";
		$arr = array(":user_id"=>$alldata->user_id,
			     ":tech_id"=>$alldata->tech_id);
		$data = $service->raw_insert_data($sql,$arr);
		echo json_encode($data);
 	break;
	case "remove_favorite":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
		$sql = "Delete From fixx_favorite Where fav_id=:fav_id";
		$arr = array(":fav_id"=>$alldata->fav_id);
		$data = $service->raw_insert_data($sql,$arr);
		echo json_encode($data);
	break;
	case "get_favorite":
		$sql = "SELECT * FROM `fixx_favorite` as fv inner join fixx_user fu on fu.id = fv.user_id inner join fixx_technic ft on ft.tech_id = fv.tech_id inner join fixx_tech_type as fty on fty.tech_type_id=ft.tech_type_id Where fv.user_id=:user_id";
		$arr = array(":user_id"=>$_REQUEST['user_id']);
		$data = $service->raw_select_sql($sql,$arr);
		echo json_encode($data);
	break;
	case "check_favorite":
		$sql = "Select * FROM fixx_favorite Where user_id=:user_id and tech_id=:tech_id";
		$arr = array(":user_id"=>$_REQUEST['user_id'],
			     ":tech_id"=>$_REQUEST['tech_id']);
		$data = $service->raw_select_sql($sql,$arr);
		echo json_encode($data);
	break;

	case "login":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
		$rootkey = md5("get_login_redonion");
		$service->set_key($rootkey);
		$service->authen($alldata->auth_key);
		$sql = "select * from fixx_user Where username=:user and password=:pass";
		$array = array(":user"=> $alldata->username,
							  ":pass"=>$alldata->password
							 );
		
		$data = $service->raw_select_sql($sql,$array);	
			echo json_encode($data);
	break;

	case "updatePic":
		$pdata = file_get_contents("php://input");
		$alldata=json_decode($pdata);
		if($alldata->logintype == "techlogin"){
		   $sql = "Update fixx_technic SET tech_pic = :pic Where tech_id=:id";
		}else{
		   $sql = "Update fixx_user SET user_pic = :pic Where id=:id";
		}
		$arr = array(":pic"=>$alldata->picture,
			     ":id"=>$alldata->id
			    );
		$data = $service->raw_insert_data($sql,$arr);
		echo json_encode($data);
	break;	

	case "updateToken":
		$pdata =file_get_contents("php://input");
		$alldata = json_decode($pdata);
		if($alldata->logintype =="techlogin"){
		   $sql = "Update fixx_technic SET device_token = :d_token, device_name=:device Where tech_id=:id ";
		}else{
		   $sql = "Update fixx_user SET device_token = :d_token, device_name=:device  Where id=:id";
		}
		$arr = array(":d_token"=>$alldata->token_id,
			     ":id"=>$alldata->id,
			     ":device"=>$alldata->device
			    );
		$data = $service->raw_insert_data($sql,$arr);
		echo json_encode($data);
	break;

	case "facebook_login":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
	//	$rootkey = md5("register_redonion");
	//	$service->set_key($rootkey);
	//	$service->authen($alldata->auth_key);
		$sql = "INSERT INTO fixx_user(username,firstname,user_type,user_pic,province_code,facebook_id)VALUES(:username,:firstname,:user_type,:pic,:province,:facebookID)";
		$arr = array(":username"=>$alldata->email,
			     ":firstname"=>$alldata->firstname,
			     ":user_type"=>"2",
			     ":pic"=>$alldata->pic,
			     ":province"=>"10",
                 ":facebookID"=>$alldata->facebookid
			);
		$data = $service->raw_insert_data($sql,$arr);
		echo json_encode($data);
		
	break;
	
	case "getProvince":
		  $sql = "Select * From provinces ";
        $load_data = $service->raw_select_sql($sql);
        echo json_encode($load_data);
	break;
	
	case "getAumphur":
		  $sql = "Select * From amphures where PROVINCE_ID=:id ";
		  $arr = array(":id"=>1);
        $load_data = $service->raw_select_sql($sql,$arr);
        echo json_encode($load_data);
	break;
	
    case "checkTechEmail":
		$rootkey = md5("checkEmail_redonion");
		$service->set_key($rootkey);
		$service->authen($authen_key);
		
		$sql ="Select * from fixx_technic Where tech_user = :username";
		$array = array(":username"=>$_GET['u']);
		
		$seldata = $service->raw_select_sql($sql,$array);
		
		echo json_encode($seldata);
	break;
        
	case "checkEmail":
		$rootkey = md5("checkEmail_redonion");
		$service->set_key($rootkey);
		$service->authen($authen_key);
		
		$sql ="Select * from fixx_user Where username = :username";
		$array = array(":username"=>$_GET['u']);
		
		$seldata = $service->raw_select_sql($sql,$array);
		
		echo json_encode($seldata);
	break;
    case "sendEmail":
        $to = $_REQUEST['email'];
        $subject = "Fixxhome Forgotpass systems";

        $message = '
                    <font face="Arial">Your password has been reset.</font>
                    <br />&nbsp;<br />
                    <font face="Arial">Username: <strong>'.$_REQUEST['email'].'<br />
                    </strong>Password: <strong>'.$_REQUEST['pass'].'
                    </strong>
                    <br />
                    <br />
                    You can login with your new information
                    </font>
                    ';

            // Always set content-type when sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
                $headers .= 'From: <administrator@fixxhome.net>' . "\r\n";
            $data = array();
            if(mail($to,$subject,$message,$headers)){
                $data['success'] = "Send mail success";  
            }else{
                $data['error'] = "No data found!";  
            }
            echo json_encode($data);
    break;
	case "register":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
		$rootkey = md5("register_redonion");
		$service->set_key($rootkey);
		$service->authen($alldata->auth_key); 
		
		$sql = "INSERT INTO fixx_user(username,password,firstname,mobile,address,province_code,user_type,user_pic)VALUES(:username,:password,:firstname,:mobile,:address,:province,:user_type,:user_pic)";
		$array = array(":username"=>$alldata->email,
							":password" => $alldata->password,
							":firstname" => $alldata->firstname,
							":mobile"=>$alldata->mobile,
							":address"=>$alldata->address,
							":province"=>$alldata->province,
							":user_type"=>"1",
							":user_pic"=>""
							);
		$data = $service->raw_insert_data($sql,$array);
		
		echo json_encode($data);		
	break;

    	case "add_order":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
      		$dt =  date("Y-m-d H:i:s",strtotime($alldata->dateJob));
        	$sql = "INSERT INTO fixx_order(user_id,tech_id,mobile,detail,AMPHUR_CODE,dateJob,status_id,picture) VALUES(:user_id,:tech_id,:mobile,:detail,:amphur_code,:dateJob,:status_id,:picture)";
       		$array = array(":user_id"=>$alldata->user_id,
                       ":tech_id"=>$alldata->tech_id,
                       ":mobile"=>$alldata->mobile,
                       ":detail"=>$alldata->detail,
		       ":amphur_code"=>$alldata->amphur_code,
                       ":dateJob"=>$dt,//$alldata->dateJob,//strtotime($alldata->dateJob),
                       ":status_id"=>"2",
                       ":picture"=>$alldata->pic);
        	$data = $service->raw_insert_data($sql,$array);

			$getprefix = "Select tt.t_code From fixx_technic as th inner join fixx_tech_type as tt on th.tech_type_id = tt.tech_type_id Where th.tech_id = :tech_id";
			$arrprefix = array(":tech_id"=>$alldata->tech_id);
			$prefixdata = $service->raw_select_sql($getprefix,$arrprefix); 
			$prefixcode = $prefixdata[0]['t_code'];			

			$ordercode = $prefixcode.date('ymd').sprintf('%05d',$data['last_id']);
		
			$codesql = "UPDATE fixx_order SET order_code = :order_code Where id=:order_id";
			$arrcode = array(":order_code"=>$ordercode,
					 ":order_id"=>$data['last_id']);
			$codedata = $service->raw_insert_data($codesql,$arrcode);
		
		 echo json_encode($data);
    	break;
    
    	case "getorder":
		if($_REQUEST['status_id'] != '-1'){
			$where = " Where o.user_id=:user_id and o.status_id=:status_id";
			$array=array(":status_id"=>$_REQUEST['status_id'],":user_id"=>$_REQUEST['user_id']);
		}else{
			$where = " Where o.user_id=:user_id";
        		$array=array(":user_id"=>$_REQUEST['user_id']);
		}        	
        	$sql = "Select * from fixx_order as o inner join fixx_order_status as os on o.status_id=os.sta_id inner join fixx_technic as t on t.tech_id = o.tech_id ".$where." order by o.dateJob DESC";
      //  	$array=array(":status_id"=>$_REQUEST['status_id'],":user_id"=>$_REQUEST['user_id']);
        	$data = $service->raw_select_sql($sql,$array);
        	echo json_encode($data);
    	break;
    
    	case "getOrderById":
        	$sql = "Select * From fixx_order Where id=:id";
        	$arr = array(":id"=>$_REQUEST['order_id']);
        	$data = $service->raw_select_sql($sql,$arr);
        	echo json_encode($data);
        break;
        
    	case "delOrder":
        	$sql = "Delete from fixx_order Where id=:id";
        	$arr = array(":id"=>$_REQUEST['order_id']);
        	$data = $service->raw_insert_data($sql,$arr);
        	echo json_encode($data);
   	    break;
        
        case "updateStatusOrder":
            $sql = "UPDATE fixx_order SET status_id=:status_id Where id=:order_id";
            $arr = array(":status_id"=>$_REQUEST['status_id'],
                         ":order_id"=>$_REQUEST['order_id']);
            $data = $service->raw_insert_data($sql,$arr);
            echo json_encode($data);
        break;
        
        case "confirmOrder":
            $dateConfirm = date("Y-m-d H:i:s");
            if($_REQUEST['usertype'] == 'cust'){
                $sql = "UPDATE fixx_order SET cus_confirm='1', cus_confirm_date=:d Where id=:order_id";
            }else{
                $sql = "UPDATE fixx_order SET tech_confirm='1', tech_confirm_date=:d Where id=:order_id";
            }
            $arr = array(":order_id"=>$_REQUEST['order_id'],":d"=>$dateConfirm);
            $data = $service->raw_insert_data($sql,$arr);
            echo json_encode($data);
        
        //check if customer confirm and technic confirm system will generate a code for finishjob
            $sql_checkconfirm = "Select cus_confirm,tech_confirm from fixx_order Where id=:order_id";
            $arr_checkconfirm = array(":order_id"=>$_REQUEST['order_id']);
            $data_checkconfirm = $service->raw_select_sql($sql_checkconfirm,$arr_checkconfirm);
            if($data_checkconfirm[0]['cus_confirm'] == '1' && $data_checkconfirm[0]['tech_confirm'] == '1'){
                //generate code for finish job
                $gencode = generateRandomString();
                $sql_updatefinishCode = "UPDATE fixx_order SET order_finish_code=:gencode,status_id='1' Where id=:order_id";
                $arr_updatefinishCode = array(":gencode"=>$gencode,
                                              ":order_id"=>$_REQUEST['order_id']);
                $data_updatefinishCode = $service->raw_insert_data($sql_updatefinishCode,$arr_updatefinishCode);
            }else{
                //do nothing
            }
        break;
        
        case "finishOrder":
             $dateConfirm = date("Y-m-d H:i:s");
            if($_REQUEST['usertype'] == 'cust'){
                $sql = "UPDATE fixx_order SET cus_finish='1', cus_finish_date=:d Where id=:order_id";
            }else{
                $sql = "UPDATE fixx_order SET tech_finish='1', tech_finish_date=:d Where id=:order_id";
            }
            $arr = array(":order_id"=>$_REQUEST['order_id'],":d"=>$dateConfirm);
            $data = $service->raw_insert_data($sql,$arr);
            echo json_encode($data);
        //check if customer and technic finish a job system will set order status to 3 (Completed)
            $sql_checkconfirm = "Select cus_finish,tech_finish from fixx_order Where id=:order_id";
            $arr_checkconfirm = array(":order_id"=>$_REQUEST['order_id']);
            $data_checkconfirm = $service->raw_select_sql($sql_checkconfirm,$arr_checkconfirm);
            if($data_checkconfirm[0]['cus_finish'] == '1' && $data_checkconfirm[0]['tech_finish'] == '1'){
                //Update status job to Compete
                $sql_updatefinishCode = "UPDATE fixx_order SET status_id='3' Where id=:order_id";
                $arr_updatefinishCode = array(":order_id"=>$_REQUEST['order_id']);
                $data_updatefinishCode = $service->raw_insert_data($sql_updatefinishCode,$arr_updatefinishCode);
            }else{
                //do nothing
            }
        break;
        
        case "checkfinishcode":
            $sql = "Select * From fixx_order Where order_finish_code = :finishcode and id=:id";
            $arr = array(":finishcode"=>$_REQUEST['finishcode'],":id"=>$_REQUEST['id']);
            $data = $service->raw_select_sql($sql,$arr);
            if($data['error']){
                echo json_encode($data);
            }else{
                $sql_update = "UPDATE fixx_order SET order_finish_code_confirm = :finishcode Where id=:id";
                $arr_update = array(":finishcode"=>$_REQUEST['finishcode'],":id"=>$_REQUEST['id']);
                $data_update = $service->raw_insert_data($sql_update,$arr_update);
            }
        
        break;
        
	case "getPrivacy":
		$rootkey = md5("privacy_redonion");
		$service->set_key($rootkey);
		$service->authen($authen_key);
		$sql = "Select privacy_detail  From fixx_config";
		$data = $service->raw_select_sql($sql);
		
		echo json_encode($data);
	break;
	
	case "getTechType":
		$sql = "Select * From fixx_tech_type";
		$data = $service->raw_select_sql($sql);
		echo json_encode($data);
	break;
	
	case "getTechnicAround":
	$lat = number_format($_REQUEST['lat'],6);
	$lng = number_format($_REQUEST['lng'],6);
    $page = (isset($_REQUEST['page'])? $_REQUEST['page'] : "1");
  //  echo "page = ".$page;
  //  die;
	
		$sql = "select id,DISTRICT_CODE,AMPHUR_CODE,latlong.PROVINCE_CODE,(6371 * acos(cos(radians(:lat))*cos(radians(lat))*cos(radians(latlong.`long`)-radians(:lng))+sin(radians(:lat))*sin(radians(lat)))) as distance from latlong 
 HAVING distance < 5";
		
		$arr = array(":lat"=>$lat,":lng"=>$lng);
		$data = $service->raw_select_sql($sql,$arr);
echo json_encode($data);
$d = array();
foreach($data as $row){
	$d[] = $row['AMPHUR_CODE'];
}
//echo implode(",",$d);
$swl = "Select * from fixx_technic as tn inner join fixx_tech_set_active as st on st.tech_id=tn.tech_id inner join fixx_tech_amphur as ap on ap.tech_id=tn.tech_id  Where ap.amphur_code in(".implode(",",$d).") ";
//echo $swl;
$r =$service->raw_select_sql($swl);
echo json_encode($r);
die();		
		$techData = array();
		foreach($data as $row){
			
		$selTech = "select * from fixx_technic as tn inner join fixx_tech_set_active as st on st.tech_id = tn.tech_id  Where tech_district = :aumphur and st.tech_active_id <> 1 and tn.is_active=1";
			$arrTech = array(":aumphur"=>$row['DISTRICT_CODE']);
			$rowTech = $service->raw_select_sql($selTech,$arrTech);
			$techData[] = $rowTech;
		
		}
		
		echo json_encode($techData);
		
	break;
 
        
// for Technic Apps
    case "getOrderByType":
       if($_REQUEST['status_id'] != '3'){ 
        $sql = "Select o.id as order_id,o.*,os.*,u.* from fixx_order as o inner join fixx_order_status as os on o.status_id=os.sta_id inner join fixx_user as u on u.id = o.user_id Where (o.status_id<>'3' or o.status_id <>'0') and o.tech_id=:tech_id order by o.dateJob DESC";
       }else{ $sql = "Select o.id as order_id,o.*,os.*,u.* from fixx_order as o inner join fixx_order_status as os on o.status_id=os.sta_id inner join fixx_user as u on u.id = o.user_id Where o.status_id='3' and o.tech_id=:tech_id order by o.dateJob DESC";
       }
	 $array=array(//":status_id"=>$_REQUEST['status_id'],
                     ":tech_id"=>$_REQUEST['tech_id']);
        $data = $service->raw_select_sql($sql,$array);
        echo json_encode($data);
    break;
   
      case "getOrderCustomer":
        $sql = "select o.id as order_id,o.*,u.*,am.*,pv.* from fixx_order as o inner join fixx_user as u on o.user_id = u.id inner join amphures as am on am.amphur_code = o.amphur_code inner join provinces as pv on pv.PROVINCE_CODE=u.province_code  Where o.id = :order_id";
        $arr = array(":order_id"=>$_REQUEST['order_id']);
        $data = $service->raw_select_sql($sql,$arr);
        echo json_encode($data);
    break;

    case "updateStatusOrder":
	$spdata = file_get_contents("php://input");
	$pdata = json_decode($spdata);
    	$sql ="UPDATE fixx_order SET status_id=:status_id Where id=:order_id";
	$arr = array(":status_id"=>$pdata->status_id,
		     ":order_id"=>$pdata->order_id
			);
	$data = $service->raw_insert_data($sql,$arr);
	echo json_encode($data);
    break;

     
    case "techlogin":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
		$rootkey = md5("get_login_redonion");
		$service->set_key($rootkey);
		$service->authen($alldata->auth_key);
		$sql = "select * from fixx_technic Where tech_user=:user and tech_pass=:pass";
		$array = array(":user"=> $alldata->username,
							  ":pass"=>$alldata->password
							 );
		
		$data = $service->raw_select_sql($sql,$array);	
			echo json_encode($data);
	break;
    case "getTechType":
		$sql = "Select * From fixx_tech_type";
		$data = $service->raw_select_sql($sql);
		echo json_encode($data);
	break;
	
	case "getServiceActiveType":
		$sql = "Select * From fixx_tech_active_type";
		$data = $service->raw_select_sql($sql);
		echo json_encode($data);
	break;
	
	case "getServiceActive":
		$spdata = file_get_contents("php://input");
		$pdata = json_decode($spdata);
		$sql = "Select * From fixx_tech_set_active as s inner join fixx_tech_active_type as at on s.tech_active_id = at.tech_active_id  Where s.tech_id=:tech_id";
		$arr = array(":tech_id"=>$pdata->tech_id);
		$data=$service->raw_select_sql($sql,$arr);
		echo json_encode($data);
	break;
	
	case "updateTechActive":
	$spdata = file_get_contents("php://input");
	$pdata = json_decode($spdata);

		if(isset($pdata->startTime) || isset($pdata->endTime)){
			$startTime = $pdata->startTime;// $_REQUEST['startTime'];
			$endTime = $pdata->endTime; //$_REQUEST['endTime'];
		}else{
			$startTime = null;
			$endTime =null;
		}
		$sql = "update fixx_tech_set_active SET tech_active_id = :active_id, tech_active_start_time= :startTime, tech_active_stop_time=:endTime Where tech_id = :tech_id";
		$arr = array(":active_id"=>$pdata->active_id, //$_REQUEST['active_id'],
						  ":tech_id" => $pdata->tech_id, //$_REQUEST['tech_id'],
						  ":startTime"=>$startTime,
						  ":endTime"=>$endTime
						  );
		$data = $service->raw_insert_data($sql,$arr);
		echo json_encode($data);
	break;
        
    // function for Review
    case "addreview":
        $sql = 'INSERT INTO fixx_review(user_id,tech_id,review_point,review_text)VALUES(:user_id,:tech_id,:review_point,review_text)';
        $arr = array(":user_id"=>$_POST['user_id'],
                     ":tech_id"=>$_POST['tech_id'],
                     ":review_point"=>$_POST['review_point'],
                     ":review_text"=>$_POST['review_text']);
        $data = $service->raw_insert_data($sql,$arr);
        echo json_encode($data);
        
        //AVG point for technic
        $sql_avg ="Select avg(review_point) as avg_point from fixx_review Where tech_id=:tech_id";
        $arr_avg = array(":tech_id"=>$_POST['tech_id']);
        $data_avg = $service->raw_select_sql($sql_avg,$arr_avg);
        
        //update point to technic
        $sql_updatpoint = "UPDATE fixx_technic SET tech_point=:tech_point Where tech_id=:tech_id";
        $arr_updatepoint= array(":tech_point"=>$data_avg[0]['avg_point'],
                                ":tech_id"=>$_POST['tech_id']);
        $data_updatepoint=$service->raw_insert_data($sql_updatepoint,$arr_updatepoint);
        
        
    break;
	
    case "getreview":
        $sql = "Select * from fixx_review Where tech_id=:tech_id order by review_date DESC";
        $arr = array(":tech_id"=>$_REQUEST['tech_id']);
        $data = $service->raw_select_sql($sql,$arr);
        echo json_encode($data);
    break;
}

 ?> 
