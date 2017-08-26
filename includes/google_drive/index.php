<?php
require_once 'drive_class.php';

$gfile = $_GET['file'];

if (empty($gfile)) {
    header('HTTP/1.1  400 Bad Request');
    exit();
}

$mydrive = new Google_drive();
$content = $mydrive->read($gfile);
header("Content-Type: ".$content[1]);
echo $content[0];

?>