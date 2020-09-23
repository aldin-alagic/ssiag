<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
require_once 'db_function.php';
require_once 'mail.php';
$db = new DB_Functions();

require_once 'response_template.php';

spl_autoload_register(function ($class) {
    $root = dirname(__DIR__);
    $classFile = $root . '/composer/vendor/payrexx/payrexx/lib/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

header('Content-Type: application/json');

$data["full-name"] = trim(filter_var($_POST["full-name"], FILTER_SANITIZE_STRING));
$data["email"] = trim(filter_var($_POST["email"], FILTER_SANITIZE_STRING));
$data["phone-number"] = trim(filter_var($_POST["phone-number"], FILTER_SANITIZE_STRING));
$data["address"] = trim(filter_var($_POST["address"], FILTER_SANITIZE_STRING));
$data["city"] = trim(filter_var($_POST["city"], FILTER_SANITIZE_STRING));
$data["country"] = trim(filter_var($_POST["country"], FILTER_SANITIZE_STRING));
$data["postcode"] = trim(filter_var($_POST["postcode"], FILTER_SANITIZE_STRING));
$data["payment-option"] = trim(filter_var($_POST["payment-option"], FILTER_SANITIZE_STRING));
$data["cart"] = $_POST["cart"];

if (isset($_POST["company"])) {
    $data["company"] = trim(filter_var($_POST["company"], FILTER_SANITIZE_STRING));
    $data["company-name"] = trim(filter_var($_POST["company-name"], FILTER_SANITIZE_STRING));
}

if (isset($_POST["different-delivery-address"])) {
    $data["different-delivery-address"] = trim(filter_var($_POST["different-delivery-address"], FILTER_SANITIZE_STRING));
    $data["delivery-full-name"] = trim(filter_var($_POST["delivery-full-name"], FILTER_SANITIZE_STRING));
    $data["delivery-phone-number"] = trim(filter_var($_POST["delivery-phone-number"], FILTER_SANITIZE_STRING));
    $data["delivery-address"] = trim(filter_var($_POST["delivery-address"], FILTER_SANITIZE_STRING));
    $data["delivery-city"] = trim(filter_var($_POST["delivery-city"], FILTER_SANITIZE_STRING));
    $data["delivery-country"] = trim(filter_var($_POST["delivery-country"], FILTER_SANITIZE_STRING));
    $data["delivery-postcode"] = trim(filter_var($_POST["delivery-postcode"], FILTER_SANITIZE_STRING));
}

$result = AllValuesEntered($data);
if ($result !== "OK") {
    $response->status = false;
    $response->message = "ERR-16: {$result}";
    $response = json_encode($response);
    die($response);
}

if ($data["payment-option"] == "now") {
    $products = json_decode($data["cart"], true);
    $listOfProducts = "";
    for ($i = 0; $i < count($products); $i++) {
        $productId = $products[$i]["id"];
        $product = $db->GetProduct($productId);
        if (!$product) {
            $response->status = false;
            $response->message = "ERR-17: {$result}";
            $response = json_encode($response);
            die($response);
        }

        $num = $i + 1;
        if ($productId == 200 ||  $productId == 210 || $productId == 230 || $productId == 240) {
            $listOfProducts .= "$num.  {$product['name']}  |  Price: {$product['price']}  |  Quantity: {$products[$i]['quantity']}  |  Size: {$products[$i]['size']}  |  Length:  {$products[$i]['length']}\n";
        } else {
            $listOfProducts .= "$num.  {$product['name']}  |  Price: {$product['price']}  |  Quantity: {$products[$i]['quantity']} \n";
        }
    }
    $invoiceTotal = $db->GetInvoiceTotal($products) * 100;

    $instanceName = 'ssiag';
    $secret = 'kRhweEXIgju7EYgh8Lm879vshtoCwe';
    $payrexx = new \Payrexx\Payrexx($instanceName, $secret);
    $invoice = new \Payrexx\Models\Request\Invoice();

    // info for payment link (reference id)
    $invoice->setReferenceId('Order number of my online shop application');

    // info for payment page (title, description)
    $invoice->setTitle('Online shop payment');
    $invoice->setDescription("Thanks for using Payrexx to pay your order. Your order includes: \n" . $listOfProducts);

    // administrative information, which provider to use (psp)
    // psp #1 = Payrexx' test mode, see http://developers.payrexx.com/en/REST-API/Miscellaneous
    $invoice->setPsp(1);

    // internal data only displayed to administrator
    $invoice->setName('Online-Shop payment');

    // payment information
    $invoice->setPurpose('Shop Order');

    // don't forget to multiply by 100
    $invoice->setAmount($invoiceTotal);

    // VAT rate percentage (nullable)
    $vatRate = 7.70;

    $invoice->setVatRate($vatRate);

    // ISO code of currency, list of alternatives can be found here
    // http://developers.payrexx.com/en/REST-API/Miscellaneous
    $invoice->setCurrency('CHF');

    // whether charge payment manually at a later date (type authorization)
    $invoice->setPreAuthorization(false);

    // whether charge payment manually at a later date (type reservation)
    $invoice->setReservation(false);

    if ($data["company"]) {
        $invoice->addField($type = 'company', $mandatory = true, $defaultValue =  $data["company-name"]);
    }
    $invoice->addField($type = 'custom_field_1', $mandatory = true, $defaultValue = $data["full-name"], $name = 'Name');
    $invoice->addField($type = 'email', $mandatory = true, $defaultValue =  $data["email"]);
    $invoice->addField($type = 'country', $mandatory = true, $defaultValue = "CH");
    $invoice->addField($type = 'place', $mandatory = true, $defaultValue = $data["city"]);
    $invoice->addField($type = 'street', $mandatory = true, $defaultValue = $data["address"]);
    $invoice->addField($type = 'postcode', $mandatory = true, $defaultValue = $data["postcode"]);
    $invoice->addField($type = 'phone', $mandatory = true, $defaultValue = $data["phone-number"]);
    $invoice->addField($type = 'terms', $mandatory = true);

    $responsePayrexx = $payrexx->create($invoice);
    $link = $responsePayrexx->getLink();

    $response->status = true;
    $response->message = "OK-5";
    $response->data = $link;
    $response = json_encode($response);
    die($response);
} else if ($data["payment-option"] == "invoice") {
    $response->status = true;
    $response->message = "OK-6";
    $response = json_encode($response);
    die($response);
}



function AllValuesEntered($data)
{
    if (!isset($data["email"]) || ctype_space($data["email"]) || empty($data["email"]))
        return "E-mail";
    else if (!isset($data["full-name"]) || ctype_space($data["full-name"]) || empty($data["full-name"]))
        return "Name";
    else if (!isset($data["phone-number"]) || ctype_space($data["phone-number"]) || empty($data["phone-number"]))
        return "Phone number";
    else if (!isset($data["city"]) || ctype_space($data["city"]) || empty($data["city"]))
        return "City";
    else if (!isset($data["postcode"]) || ctype_space($data["postcode"]) || empty($data["postcode"]))
        return "Postal code";
    else if (!isset($data["address"]) || ctype_space($data["address"]) || empty($data["address"]))
        return "Street address";
    else if (!isset($data["cart"]) || ctype_space($data["cart"]) || empty($data["cart"]))
        return "Cart";
    else if (isset($data["company"]) && (!isset($data["company-name"]) || ctype_space($data["company-name"]) || empty($data["company-name"])))
        return "Company name";
    else if (!isset($data["payment-option"]) || ctype_space($data["payment-option"]) || empty($data["payment-option"]) || ($data["payment-option"] != "now" && $data["payment-option"] != "invoice"))
        return "Payment option";
    else if (isset($data["different-delivery-address"])) {
        if (!isset($data["delivery-full-name"]) || ctype_space($data["delivery-full-name"]) || empty($data["delivery-full-name"]))
            return "Delivery name";
        else if (!isset($data["delivery-phone-number"]) || ctype_space($data["delivery-phone-number"]) || empty($data["delivery-phone-number"]))
            return "Delivery phone number";
        else if (!isset($data["delivery-city"]) || ctype_space($data["delivery-city"]) || empty($data["delivery-city"]))
            return "Delivery city";
        else if (!isset($data["delivery-postcode"]) || ctype_space($data["delivery-postcode"]) || empty($data["delivery-postcode"]))
            return "Delivery postal code";
        else if (!isset($data["delivery-address"]) || ctype_space($data["delivery-address"]) || empty($data["delivery-address"]))
            return "Delivery street address";
    }
    return "OK";
}
