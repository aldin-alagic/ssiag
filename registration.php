<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
require_once 'db_function.php';
require_once 'mail.php';
$db = new DB_Functions();

require_once 'response_template.php';

header('Content-Type: application/json');

$data["email"] = trim(filter_var($_POST["email"], FILTER_SANITIZE_STRING));
$data["full-name"] = trim(filter_var($_POST["full-name"], FILTER_SANITIZE_STRING));
$data["password"] = trim(filter_var($_POST["password"], FILTER_SANITIZE_STRING));
$data["phone-number"] = trim(filter_var($_POST["phone-number"], FILTER_SANITIZE_STRING));
$data["city"] = trim(filter_var($_POST["city"], FILTER_SANITIZE_STRING));
$data["post-code"] = trim(filter_var($_POST["post-code"], FILTER_SANITIZE_STRING));
$data["street-address"] = trim(filter_var($_POST["street-address"], FILTER_SANITIZE_STRING));
$data["language"] = trim(filter_var($_POST["language"], FILTER_SANITIZE_STRING));
if (isset($_POST["company"])) {
    $data["company"] = trim(filter_var($_POST["company"], FILTER_SANITIZE_STRING));
    $data["company-name"] = trim(filter_var($_POST["company-name"], FILTER_SANITIZE_STRING));
}

$result = AllValuesEntered($data);
if ($result !== "OK") {
    $response->status = false;
    $response->message = "ERR-01: {$result}";
    $response = json_encode($response);
    echo $response;
    return;
}

$result = CheckValues($data);
if ($result !== "OK") {
    $response->status = false;
    $response->message = "ERR-02: {$result}";
    $response = json_encode($response);
    echo $response;
    return;
}

$userCheck = $db->UserExists($data['email']);
if ($userCheck) {
    $response->status = false;
    $response->message = "ERR-03";
    $response = json_encode($response);
    echo $response;
    return;
}

$langCheck = CheckLanguage($data["language"]);
if (!$langCheck) {
    $response->status = false;
    $response->message = "ERR-15";
    $response = json_encode($response);
    exit($response);
}

$hash = CreateHash($data['password']);
$user = $db->StoreUser($data, $hash);

$mail = new Mail();
$mail->SendWelcome($user, $data["language"]);


$response->status = true;
$response->message = "OK-01";
$response->data = $user;
$response = json_encode($response);
echo $response;



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

function CheckValues($data)
{
    if (!preg_match("/(\w+\.)*(\w+)@(\w+\.){1,2}(\w{2,5})/", $data["email"]))
        return "E-mail";
    if (strlen($data["password"]) < 8)
        return "Password";
    return "OK";
}

function AllValuesEntered($data)
{
    if (!isset($data["email"]) || ctype_space($data["email"]) || empty($data["email"]))
        return "E-mail";
    else if (!isset($data["full-name"]) || ctype_space($data["full-name"]) || empty($data["full-name"]))
        return "Name";
    else if (!isset($data["password"]) || ctype_space($data["password"]) || empty($data["password"]))
        return "Password";
    else if (!isset($data["phone-number"]) || ctype_space($data["phone-number"]) || empty($data["phone-number"]))
        return "Phone number";
    else if (!isset($data["city"]) || ctype_space($data["city"]) || empty($data["city"]))
        return "City";
    else if (!isset($data["post-code"]) || ctype_space($data["post-code"]) || empty($data["post-code"]))
        return "Postal code";
    else if (!isset($data["street-address"]) || ctype_space($data["street-address"]) || empty($data["street-address"]))
        return "Street";
    else if (isset($data["company"]) && (!isset($data["company-name"]) || ctype_space($data["company-name"]) || empty($data["company-name"])))
        return "Company name";
    else if (!isset($data["language"]) || ctype_space($data["language"]) || empty($data["language"]))
        return "Language";
    return "OK";
}

function CheckLanguage($lang)
{
    if ($lang != "eng" && $lang != "de") {
        return false;
    }
    return true;
}
