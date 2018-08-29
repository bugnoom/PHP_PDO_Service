<?php  ini_set("display_errors",1);
header('Access-Control-Allow-Origin:*');
 include "../class/config.php";

$service = new services();

$action = $_REQUEST['action'];
$authen_key = (!isset($_REQUEST['auth_key'])?"":$_REQUEST['auth_key']);


//echo md5("bugnoom_redonion");



switch($action){
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
		$sql = "select * from fixx_technic Where tech_user=:user and tech_pass=:pass";
		$array = array(":user"=> $alldata->username,
							  ":pass"=>$alldata->password
							 );
		
		$data = $service->raw_select_sql($sql,$array);	
			echo json_encode($data);
	break;
	
		
	case "getProvince":
		$sql = "Select * From provinces ";
        $load_data = $service->raw_select_sql($sql);
        echo json_encode($load_data);
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
	
	case "getTechnicAround":
	$lat = number_format($_REQUEST['lat'],6);
	$lng = number_format($_REQUEST['lng'],6);
	
		$sql = "select id,DISTRICT_CODE,AMPHUR_CODE,latlong.PROVINCE_CODE,(6371 * acos(cos(radians(:lat))*cos(radians(lat))*cos(radians(latlong.`long`)-radians(:lng))+sin(radians(:lat))*sin(radians(lat)))) as distance from latlong 
 HAVING distance < 5";
		
		$arr = array(":lat"=>$lat,":lng"=>$lng);
		$data = $service->raw_select_sql($sql,$arr);
		
		$techData = array();
		foreach($data as $row){
			
		$selTech = "select * from fixx_technic Where tech_district = :aumphur";
			$arrTech = array(":aumphur"=>$row['DISTRICT_CODE']);
			$rowTech = $service->raw_select_sql($selTech,$arrTech);
			$techData[] = $rowTech;
		
		}
		
		echo json_encode($techData);
		
	break;
	
	
}

 ?> 