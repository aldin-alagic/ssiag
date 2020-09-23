<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
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


$instanceName = 'ssiag';
$secret = 'kRhweEXIgju7EYgh8Lm879vshtoCwe';
$payrexx = new \Payrexx\Payrexx($instanceName, $secret);
$invoice = new \Payrexx\Models\Request\Invoice();

// info for payment link (reference id)
$invoice->setReferenceId('Order number of my online shop application');
// info for payment page (title, description)
$invoice->setTitle('Online shop payment');
$invoice->setDescription('Thanks for using Payrexx to pay your order');

// administrative information, which provider to use (psp)
// psp #1 = Payrexx' test mode, see http://developers.payrexx.com/docs/miscellaneous
$invoice->setPsp(1);

// internal data only displayed to administrator
$invoice->setName('Online-Shop payment #001');

// payment information
$invoice->setPurpose('Shop Order #001');
$amount = 5.90;
// don't forget to multiply by 100
$invoice->setAmount($amount * 100);

// VAT rate percentage (nullable)
$vatRate = 7.70;
$invoice->setVatRate($vatRate);

// Product SKU
$sku = 'P01122000';
$invoice->setSku($sku);

$invoice->setCurrency('CHF');
$invoice->setPreAuthorization(false);
$invoice->setReservation(false);

$invoice->addField($type = 'email', $mandatory = true, $defaultValue = 'my-customer@example.com');
$invoice->addField($type = 'company', $mandatory = true, $defaultValue = 'Ueli Kramer Firma');
$invoice->addField($type = 'forename', $mandatory = true, $defaultValue = 'Ueli');
$invoice->addField($type = 'surname', $mandatory = true, $defaultValue = 'Kramer');
$invoice->addField($type = 'country', $mandatory = true, $defaultValue = 'AT');
$invoice->addField($type = 'title', $mandatory = true, $defaultValue = 'miss');
$invoice->addField($type = 'terms', $mandatory = true);
$invoice->addField($type = 'privacy_policy', $mandatory = true);
$invoice->addField($type = 'custom_field_1', $mandatory = true, $defaultValue = 'Value 001', $name = 'Das ist ein Feld');

try {
    $response = $payrexx->create($invoice);
    var_dump($response);
} catch (\Payrexx\PayrexxException $e) {
    print $e->getMessage();
}
