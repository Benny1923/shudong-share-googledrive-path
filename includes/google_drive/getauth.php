<?php
require_once "Authorization.php";
$myclient = new Google_Auth;
$myclient->create_client();
$auth_url = $myclient->get_authorizationURL();

header("Location: $auth_url", 1, 302);

?>