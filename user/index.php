<?php  ini_set("display_errors",1);
 include "class/config.php";

$service = new services();

$action = $_GET['action'];
$authen_key = (!isset($_REQUEST['auth_key'])?"":$_REQUEST['auth_key']);


//echo md5("bugnoom_redonion");



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
        
	case "get_favorite":
		$rootkey = md5("get_favorite_redonion");
		$service->set_key($rootkey);
		$service->authen($authen_key);
			$data = array();
			$data['count'] = 10;
			$data['data'] = array(
					array("tech_id"=>"1","tech_name"=>"Tech1","tech_detail"=>"i'm a technic in air condition","tech_type"=>"Air","tech_location"=>"bangkapi","tech_pic"=>"images/tech/pic.jpg","tech_rate_point"=>"4.8","tech_star"=>"4"),
					array("tech_id"=>"2","tech_name"=>"Tech1","tech_detail"=>"i'm a technic in air condition","tech_type"=>"Air","tech_location"=>"bangkapi","tech_pic"=>"images/tech/pic.jpg","tech_rate_point"=>"4.8","tech_star"=>"4"),
					array("tech_id"=>"3","tech_name"=>"Tech1","tech_detail"=>"i'm a technic in air condition","tech_type"=>"Air","tech_location"=>"bangkapi","tech_pic"=>"images/tech/pic.jpg","tech_rate_point"=>"4.8","tech_star"=>"4"),
					array("tech_id"=>"4","tech_name"=>"Tech1","tech_detail"=>"i'm a technic in air condition","tech_type"=>"Air","tech_location"=>"bangkapi","tech_pic"=>"images/tech/pic.jpg","tech_rate_point"=>"4.8","tech_star"=>"4"),
					array("tech_id"=>"5","tech_name"=>"Tech1","tech_detail"=>"i'm a technic in air condition","tech_type"=>"Air","tech_location"=>"bangkapi","tech_pic"=>"images/tech/pic.jpg","tech_rate_point"=>"4.8","tech_star"=>"4"),
					array("tech_id"=>"6","tech_name"=>"Tech1","tech_detail"=>"i'm a technic in air condition","tech_type"=>"Air","tech_location"=>"bangkapi","tech_pic"=>"images/tech/pic.jpg","tech_rate_point"=>"4.8","tech_star"=>"4"),
					array("tech_id"=>"7","tech_name"=>"Tech1","tech_detail"=>"i'm a technic in air condition","tech_type"=>"Air","tech_location"=>"bangkapi","tech_pic"=>"images/tech/pic.jpg","tech_rate_point"=>"4.8","tech_star"=>"4")
					);
			$data['status'] = array("type"=>"success","error_exeption"=>"");
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
	
	case "updateToken":
		$pdata =file_get_contents("php://input");
		$alldata = json_decode($pdata);
		if($alldata->logintype =="techlogin"){
		   $sql = "Update fixx_technic SET device_token = :d_token Where tech_id=:id ";
		}else{
		   $sql = "Update fixx_user SET device_token = :d_token Where id=:id";
		}
		$arr = array(":d_token"=>$alldata->token_id,
			     ":id"=>$alldata->id
			    );
		$data = $service->raw_insert_data($sql,$arr);
		echo json_encode($data);
	break;

	case "facebook_login":
		$pdata = file_get_contents("php://input");
		$alldata = json_decode($pdata);
		$rootkey = md5("faceboo_logn");
		$service->set_key($rootkey);
		$service->authen($authen_key);
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
	
	case "checkEmail":
		$rootkey = md5("checkEmail_redonion");
		$service->set_key($rootkey);
		$service->authen($authen_key);
		
		$sql ="Select * from fixx_user Where username = :username";
		$array = array(":username"=>$_GET['u']);
		
		$seldata = $service->raw_select_sql($sql,$array);
		
		echo json_encode($seldata);
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
							":user_pic"=>$alldata->pic
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
            echo json_encode($data);
    break;
    
    case "getorder":
        $where = ($_REQUEST['status_id'] != '-1')?" Where o.status_id=:status_id" : "";
        $sql = "Select * from fixx_order as o inner join fixx_order_status as os on o.status_id=os.sta_id inner join fixx_technic as t on t.tech_id = o.tech_id ".$where." order by o.dateJob DESC";
        $array=array(":status_id"=>$_REQUEST['status_id']);
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
        
        $sql = "Select o.id as order_id,o.*,os.*,u.* from fixx_order as o inner join fixx_order_status as os on o.status_id=os.sta_id inner join fixx_user as u on u.id = o.user_id Where o.status_id=:status_id and o.tech_id=:tech_id order by o.dateJob DESC";
        $array=array(":status_id"=>$_REQUEST['status_id'],
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
	
}

 ?> 
