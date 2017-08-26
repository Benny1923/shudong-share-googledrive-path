<?php
require_once("pass.php");

$gd_decrypt = openssl_decrypt(file_get_contents(dirname(__FILE__)."/client_secret.json.aes"), "AES-256-CBC", $gd_pass, 0, substr(hash("sha512", $gd_pass), 0, 16));
$gd_client = json_decode($gd_decrypt, true)["installed"];
$gd_clientid = $gd_client["client_id"];
$gd_clientsecret = $gd_client["client_secret"];
$gd_redirect_uris = $gd_client["redirect_uris"];

?>