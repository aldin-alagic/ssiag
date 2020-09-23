<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL); 
require_once 'db_function.php';
$db = new DB_Functions();

require_once 'response_template.php';

header('Content-Type: application/json');

$data["email"] = trim(filter_var($_POST["email"], FILTER_SANITIZE_STRING));
$data["email"] = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
$data["password"] = trim(filter_var($_POST["password"], FILTER_SANITIZE_STRING));

$result = AllValuesEntered($data);
if ($result != "OK") {
    $response->status = false;
    $response->message = "ERR-04: $result";
    $response = json_encode($response);
    echo $response;
    exit();
}

$result = $db->UserExists($data['email']);
if (!$result) {
    $response->status = false;
    $response->message = "ERR-05";
    $response = json_encode($response);
    echo $response;
    exit();
}

$result = $db->CheckPassword($data);
if ($result["login_attempts"] != 0) {
    $response->status = false;
    $response->message = "ERR-6: {$result['login_attempts']}";
    $response = json_encode($response);
    echo $response;
    exit();
} else {
    $response->status = true;
    $response->message = "OK-02";
    $response->data = json_decode($result);
    $response = json_encode($response);
    echo $response;
    exit();
}

function AllValuesEntered($data)
{
    if (!isset($data["email"]) || ctype_space($data["email"]) || empty($data["email"]))
        return "E-mail";
    else if (!isset($data["password"]) || ctype_space($data["password"]) || empty($data["password"]))
        return "Password";

    return "OK";
}
