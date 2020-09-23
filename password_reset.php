<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

require_once 'db_function.php';
require_once 'mail.php';
require_once 'response_template.php';
header('Content-Type: application/json');

if (isset($_POST["email"]) && (!empty($_POST["email"]))) {
    $db = new DB_Functions();
    $mail = new Mail();

    $email = $_POST["email"];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $response->status = false;
        $response->message = "ERR-7";
        $response = json_encode($response);
        exit($response);
    }

    $userCheck = $db->UserExists($email);
    if (!$userCheck) {
        $response->status = false;
        $response->message = "ERR-8";
        $response = json_encode($response);
        exit($response);
    }

    $lang = $_POST["language"];
    $lang = filter_var($lang, FILTER_SANITIZE_STRING);
    $langCheck = CheckLanguage($lang);
    if (!$langCheck) {
        $response->status = false;
        $response->message = "ERR-14";
        $response = json_encode($response);
        exit($response);
    }

    $user = $db->GetUser($email);
    $expirationDate = GetExpirationDate();
    $token = GetToken($email);
    $tokenHash = hash('sha256', $token);
    $db->StoreToken($email, $tokenHash, $expirationDate);
    $mail->SendResetPassword($user, $token, $lang);
    $response->status = true;
    $response->message = "OK-3";
    $response = json_encode($response);
    exit($response);
}

if (isset($_POST["token"]) && (!empty($_POST["token"]))) {
    $db = new DB_Functions();

    $token = $_POST["token"];
    $token = filter_var($token, FILTER_SANITIZE_STRING);
    if (!$token) {
        $response->status = false;
        $response->message = "ERR-9";
        $response = json_encode($response);
        exit($response);
    }

    $tokenHash = hash('sha256', $token);
    $tokenExists = $db->TokenExists($tokenHash);
    if (!$tokenExists) {
        $response->status = false;
        $response->message = "ERR-10";
        $response = json_encode($response);
        exit($response);
    }

    $tokenValid = $db->TokenValid($tokenHash);
    if (!$tokenValid) {
        $response->status = false;
        $response->message = "ERR-11";
        $response = json_encode($response);
        exit($response);
    }

    $email = $tokenValid["email"];
    $password = $_POST["password"];
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    if (!$password) {
        $response->status = false;
        $response->message = "ERR-12";
        $response = json_encode($response);
        exit($response);
    }
    $hash = CreateHash($password);
    $changePassword = $db->StoreUserPassword($email, $hash);

    $tokenDeleted = $db->DeleteToken($tokenHash);
    if (!$tokenDeleted) {
        $response->status = false;
        $response->message = "ERR-13";
        $response = json_encode($response);
        exit($response);
    }

    $response->status = true;
    $response->message = "OK-4";
    $response = json_encode($response);
    exit($response);
}

function GetExpirationDate()
{
    $expFormat = mktime(
        date("H") + 3,
        date("i"),
        date("s"),
        date("m"),
        date("d"),
        date("Y")
    );
    $expDate = date("Y-m-d H:i:s", $expFormat);
    return $expDate;
}

function GetToken($email)
{
    $db = new DB_Functions();
    $rand = random_bytes(16);
    $token = md5($rand . $email);

    while ($db->TokenExists($token)) {
        $rand = random_bytes(16);
        $token = md5($rand . $email);
    }

    return $token;
}

function CreateHash($password)
{
    $cstrong = true;
    $salt = openssl_random_pseudo_bytes(32, $cstrong);
    $saltB64 = base64_encode($salt);
    $iterations = 10000;
    $hash = hash_pbkdf2("sha256", $password, $salt, $iterations);
    $hashB64 = base64_encode(pack('H*', $hash));
    return [$saltB64, $hashB64];
}

function CheckLanguage($lang)
{
    if ($lang != "eng" && $lang != "de") {
        return false;
    }
    return true;
}
