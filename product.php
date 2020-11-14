<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
require_once 'response_template.php';
header('Content-Type: application/json');


if (isset($_POST['product_id']) && !empty($_POST['product_id']) && isset($_POST['size_length']) && !empty($_POST['size_length'])) {

    $data["product_id"] = trim(filter_var($_POST["product_id"], FILTER_SANITIZE_STRING));
    $data["size_length"] = trim(filter_var($_POST["size_length"], FILTER_SANITIZE_STRING));

    $result = AllValuesEntered($data);
    if ($result != "OK") {
        $response->status = false;
        $response->message = "ERR-18: $result";
        $response = json_encode($response);
        echo $response;
        exit();
    }

    $productStock = 0;
    if ($data["product_id"] == 200 || $data["product_id"] == 210) {
        $inlayStock = GetInlayStock($data["size_length"]);
        $legsStock = GetLegsStock($data["size_length"]);
        $chillerStock = GetProductStock("529742950");
        $productStock = min($inlayStock, $legsStock, $chillerStock);
    } elseif ($data["product_id"] == 230) {
        $productStock = GetLegsStock($data["size_length"]);
    } elseif ($data["product_id"] == 240) {
        $productStock = GetInlayStock($data["size_length"]);
    } elseif ($data["product_id"] == 220) {
        $productStock = GetProductStock("529742950");
    } elseif ($data["product_id"] == 250) {
        $productStock = GetProductStock("615489568");
    }

    $response->status = true;
    $response->message = "OK-07";
    $response->data = $productStock;
    $response = json_encode($response);
    echo $response;
}

function AllValuesEntered($data)
{
    if (!isset($data["product_id"]) || ctype_space($data["product_id"]) || empty($data["product_id"]))
        return "Product";
    else if (!isset($data["size_length"]) || ctype_space($data["size_length"]) || empty($data["size_length"]))
        return "Size";

    return "OK";
}

function GetInlayStock($inlay)
{
    $inlays = array("L1" => "501700831", "L2" => "656250817", "L3" => "284444764", "N1" => "121421827", "N2" => "225753583", "N3" => "192540830");
    $id = $inlays[$inlay];
    $stock = GetProductStock($id);
    return $stock;
}

function GetLegsStock($legs)
{
    $legsList = array("L1" => "343136653", "L2" => "261409745", "L3" => "507390358", "N1" => "548959841", "N2" => "429056211", "N3" => "492397733");
    $id = $legsList[$legs];
    $stock = GetProductStock($id);
    return $stock;
}

function GetProductStock($id)
{
    $token = GetToken();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.smallinvoice.com/v2/catalog/products/$id");
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        ['Content-Type: application/json', "Authorization: Bearer $token"]
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $item = json_decode($response, true)["item"];
    $stock = $item["inventory_available"];
    return $stock;
}

function GetToken()
{
    $ch = curl_init();
    $payload = json_encode([
        'grant_type' => 'client_credentials',
        'client_id' => 'PDP4UUW6MRRP0EJ4BGOZ',
        'client_secret' => 's9Mxefs2Ht96C6Uo5fqCwqVICtW/e45M8CymAfAqfNKZ+9J3rplTm9eKxKcjYn1ubKO5ie5vCdWJrYH6',
        'scope' => 'invoice contact catalog',
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_URL, 'https://api.smallinvoice.com/v2/auth/access-tokens');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $token = json_decode($response, true)["access_token"];
    return $token;
}