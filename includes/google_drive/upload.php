<?php
require_once("../../config.php");
require_once('../function.php');
require_once('../connect.php');
require_once('../userShell.php');
require_once('drive_class.php');
$fileAction=$_POST['action'];
$fileKey=$_POST['key'];
$fileSize = "0";
$fileType = ",";
$fileDir = "data";
$autoName = true ;
$nameRule = "{time} dddd";
$userGroup = $userInfo['group'];
$results1 = mysqli_query($con,"SELECT * FROM sd_usergroup where id = $userGroup");
while($row1 = mysqli_fetch_assoc($results1)){ 
	$policyId = $row1['policyid'];
}
$results = mysqli_query($con,"SELECT * FROM sd_policy where id = $policyId");
while($row = mysqli_fetch_assoc($results)){ 
	$fileType = $row['p_mimetype'];
	$fileEnd = explode(",", $row['p_filetype']);
	$fileSize = $row['p_size'];
	$fileDir = $row['p_dir'];
	$autoName = $row['p_autoname'];
    $nameRule = $row['p_namerule'];
    $bucketname = $row['p_bucketname'];
}
$fileType = explode(",",$fileType);
if(!empty($_FILES["file"]["name"])){
	$fileExplode = explode(".",$_FILES["file"]["name"]);
	if($fileType[0] == "*"){
		$fileType = array($_FILES["file"]["type"],"");
	}
	if(!(in_array($_FILES["file"]["type"],$fileType) || in_array(end($fileExplode),$fileEnd))){
		header('HTTP/1.1  400 Bad Request');
		$result["error"] = urlencode("不支持此文件类型");
		echo urldecode(json_encode($result));
		exit();
	}else if($_FILES["file"]["size"] > $fileSize){
		header('HTTP/1.1  400 Bad Request');
		$result["error"] = urlencode("文件尺寸超出限制");
		echo urldecode(json_encode($result));
		exit();
	}else if($_FILES["file"]["error"] > 0){
		header('HTTP/1.1  400 Bad Request');
		$result["error"] = $_FILES["file"]["error"];
		echo json_encode($result);
		exit();	
	}
	if ($autoName == "true"){
		$arrayName = array('{date}' => date('YmjGis'),'{rand4}' => rand(1000,9999),'{rand8}' => rand(10000000,99999999) ,'{time}' => time());
		if(end($fileExplode)=="php"){
			exit();
		}
		$fileNmae = strtr($nameRule,$arrayName).".".end($fileExplode);
	}else{ 
		$fileNmae = $_FILES["file"]["name"];
	}
	if(file_exists(dirname(dirname(__FILE__))."/".$fileDir."/".$fileNmae)){ 
		header('HTTP/1.1  614 Bad Request');
		$result["error"] = urlencode("文件重名");
		echo urldecode(json_encode($result));
		exit();
	}else{
        $mydrive = new Google_drive();
		$driveup = $mydrive->upload(file_get_contents($_FILES["file"]["tmp_name"]),$fileNmae ,$bucketname);
		$result["hash"]="";
		$result["key"]=urlencode($driveup);
		header('HTTP/1.1  200 OK');
		echo urldecode(json_encode($result));
	}
}else{}
?>
