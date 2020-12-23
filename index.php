<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>KeySystem</title>
</head>

<body>
    <form id="Captcha" method="POST" action="">
        <div class="g-recaptcha" data-callback="recaptchaCallback"
            data-sitekey="6LcAphEaAAAAANnDdlI4U-iPpZcs75OYSwv42R6o" id="google">
        </div>
    </form>
    <p id="Notif">Please check the recaptcha to get your key!</p>
</body>
<script>
    function recaptchaCallback (){ 
 document.getElementById('Captcha').submit()
}
</script>
</html>


<?php
$configs = include('config.php');
if (isset($_POST['g-recaptcha-response'])) { 
    $secret = $configs['recaptcha'];
    $response = $_POST['g-recaptcha-response'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $response . '&remoteip=' . $remoteip;
    $response = file_get_contents($url);
    $jsResponse = json_decode($response, true);
    if ($jsResponse["success"] == 1) { 
        $key = keytodb(generatekey(25));
        echo '<script> document.getElementById("Notif").innerHTML = "Here is your generated key!" </script>';
        echo '<script> document.getElementById("google").style.display = "none";</script>';
        echo '<script> key = document.createElement("INPUT"); document.body.appendChild(key); key.value = "'.$key.'";</script>';
    } else {
        echo '<script> document.getElementById("Notif").innerHTML = "Make sure the recaptcha is checked!" </script>';
    }
}
function generatekey($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function keytodb($generatedkey)
{
    $configs = include('config.php');
    $mysqli = new mysqli($configs['host'], $configs['username'], $configs['password'], $configs['database']);
    $stmt = $mysqli->prepare("INSERT INTO keytable VALUES (?)");
    $stmt->bind_param("s", $generatedkey);
    $stmt->execute();
    return $generatedkey;
}
?>