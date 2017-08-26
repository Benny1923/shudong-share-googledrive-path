<?php
require_once dirname(__FILE__).'/Authorization.php';
require_once dirname(__FILE__).'/../../config.php';
require_once dirname(__FILE__).'/../connect.php';
require_once dirname(__FILE__).'/../function.php';
require_once dirname(__FILE__).'/../userShell.php';
class Google_drive {
    private $myclient, $myservice, $myaction;
    private $fileType,$fileEnd,$fileSize,$fileDir,$autoName,$nameRule,$ak,$sk,$id,$bucketname;
    public function upload($object, $name, $bucketname) {
        global $con, $userInfo;
        $userGroup = $userInfo['group'];
        $query1 = mysqli_query($con, "SELECT * FROM `sd_usergroup` where `id` = '$userGroup'");
        while ($row1 = mysqli_fetch_assoc($query1)) {
            $policyId = $row1['policyid'];
        }
        $this->new_client($policyId);
        $this->myservice = $this->myclient->create_service();
        $fileMetadata = new Google_Service_Drive_DriveFile(array('name' => $name, 'parents' => array($bucketname)));
        $file = $this->myservice->files->create($fileMetadata, array(
            'data'=> $object,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));
        return $name;
    }
    public function delete($item) {
        global $con;
        $query1 = mysqli_query($con, "SELECT * FROM sd_file where ming = '$item'");
        while($row1 = mysqli_fetch_assoc($query1)) {
            $policyId = $row1['policyid'];
        }
        $this->new_client($policyId);
        $this->myservice = $this->myclient->create_service();
        $inforequest = $this->myservice->files->listFiles(array(
            'q'=> 'name="'.$item.'" and parents="'.$this->bucketname.'"',
            'fields'=> 'files(id,mimeType,name),kind',
            'spaces'=> 'drive'
        ));
        $info = $inforequest->getFiles()[0];
        $fileid= $info->getId();
        $this->myservice->files->delete($fileid);
    }
    public function read($item) {
        global $con;
        $query1 = mysqli_query($con, "SELECT * FROM sd_file where ming = '$item'");
        while($row1 = mysqli_fetch_assoc($query1)) {
            $policyId = $row1['policyid'];
        }
        $this->new_client($policyId);
        $this->myservice = $this->myclient->create_service();
        $inforequest = $this->myservice->files->listFiles(array(
            'q'=> 'name="'.$item.'" and parents="'.$this->bucketname.'"',
            'fields'=> 'files(id,mimeType,name),kind',
            'spaces'=> 'drive'
        ));
        $info = $inforequest->getFiles()[0];
        $fileid= $info->getId();
        $respone = $this->myservice->files->get($fileid, array('alt'=>'media'));
        $content = $respone->getBody()->getContents();
        return array($content, $info->getMimeType());
    }
    private function new_client($policyId) {
        $this->myclient = new Google_Auth();
        $this->myclient->create_client();
        global $con;
        $query2 = mysqli_query($con,"SELECT * FROM `sd_policy` where `id` = '$policyId'");
        while ($row2 = mysqli_fetch_assoc($query2)) {
            $this->fileType = $row2['p_mimetype'];
            $this->fileEnd = explode(",", $row2['p_filetype']);
            $this->fileSize = $row2['p_size'];
            $this->fileDir = $row2['p_dir'];
            $this->autoName = $row2['p_autoname'];
            $this->nameRule = $row2['p_namerule'];
            $this->ak = $row2['p_ak'];
            $this->sk = json_decode($row2['p_sk'], true);
            $this->id = $row2['id'];
            $this->bucketname = $row2['p_bucketname'];
        }
        //first run check
        if (!isset($this->sk['access_token'])) {
            $this->sk = $this->myclient->get_authorization($this->ak);
            $p_sk = json_encode($this->sk);
            $p_id = $this->id;
            $sql = "UPDATE `sd_policy` SET `p_sk`='$p_sk' WHERE `id`='$p_id'";
            mysqli_query($con,$sql);
        }
        //set Token
        $this->myclient->set_authorization($this->sk);
        //check expired
        if ($this->myclient->check_authorization()) {
            $p_sk = json_encode($this->sk);
            $p_id = $this->id;
            $sql = "UPDATE `sd_policy` SET `p_sk`='$p_sk' WHERE `id`='$p_id'";
            mysqli_query($con,$sql);
        }
    }
}
?>