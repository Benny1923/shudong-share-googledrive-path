<?php
if ($_POST['action'] == "install") {
    $secret = $_FILES["secret"]["tmp_name"];
    $pass = $_POST["pass"];
    if ($_FILES["secret"]["name"]) {
        //move_uploaded_file($secret, "client_secret_bak.json");
        //$conetnt = json_decode(file_get_contents("client_secret_bak.json"), true);
        $content = json_decode(file_get_contents($secret), true);
    }
    if (!$pass) {
        echo "<script>alert(\"Please set password!\");</script>";
    } else if (!$_FILES["secret"]["name"]) {
        echo "<script>alert(\"Please select secret file!\");</script>";
    } else if (!$content["installed"]["client_id"] || !$content["installed"]["client_secret"]) {
        echo "<script>alert(\"Invalid api secret format!\");</script>";
    } else {
        $encrypt = openssl_encrypt(file_get_contents($secret), "AES-256-CBC", $pass, false, substr(hash("sha512", $pass),0,16));
        file_put_contents("client_secret.json.aes", $encrypt);
        $val = '<?php $gd_pass="' . $pass . '"; ?>';
        file_put_contents("pass.php", $val);
        echo "<script>alert(\"Install Success!\\nThis page will been delete!\");window.location.assign(\"../../admin/\");</script>";
        @unlink(__FILE__);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>google api key installer</title>
        <script>
            function gen() {
                var pass="";
                var chars = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789";
                var i;
                for (i=0;i<10;i++) {
                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                document.getElementById("password").value = pass;
            }
        </script>
    </head>
    <body>
        <form method="POST" enctype="multipart/form-data">
            <input value="install" name="action" style="display:none;">
            Please upload your api secret:<br>
            <input type="file" name="secret"><br>
            and set a password:<br>
            <input type="text" name="pass" id="password" maxlength="16">
            <button type="submit">Submit</button>
        </form>
        <div style="height: 12px"></div>
        <font style="color: #00ff00">
            Set password for api secret<br>
            You don't need rember it<br>
            You also can click <a onclick="gen()" href="#">here</a> to generate random password.
        </font>
    </body>
</html>