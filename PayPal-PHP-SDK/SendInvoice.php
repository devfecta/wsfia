<?php
// # Create Invoice Sample
// This sample code demonstrate how you can send
// a legitimate invoice to the payer
/** @var Invoice $invoice */

$lineItems = json_decode($_POST['lineItems'], FALSE);

 //echo json_encode($lines->billing->billingEmailAddress, JSON_PRETTY_PRINT);
 //return 0;

$invoice = require 'CreateInvoice.php';
use PayPal\Api\Invoice;

try {
    // ### Send Invoice
    // Send a legitimate invoice to the payer
    // with a valid ApiContext (See bootstrap.php for more on `ApiContext`)
    ////return 'Invoice Send Test';
    $sendStatus = $invoice->send($apiContext);

    
} catch (Exception $ex) {
    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
    // ResultPrinter::printError("Send Invoice", "Invoice", null, null, $ex);

    ////return 'Invoice Send Error Test';
    exit(1);
}
// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
 // ResultPrinter::printResult("Send Invoice", "Invoice", $invoice->getId(), null, null);
// ### Retrieve Invoice
// Retrieve the invoice object by calling the
// static `get` method
// on the Invoice class by passing a valid
// Invoice ID
// (See bootstrap.php for more on `ApiContext`)
try {
    ////return 'Invoice Test';
    $invoice = Invoice::get($invoice->getId(), $apiContext);

    
} catch (Exception $ex) {
    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
    // ResultPrinter::printError("Get Invoice (Not Required - For Sample Only)", "Invoice", $invoice->getId(), $invoice->getId(), $ex);
    ////return 'Invoice Error Test';
    exit(1);
}
// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
//ResultPrinter::printResult("Get Invoice (Not Required - For Sample Only)", "Invoice", $invoice->getId(), $invoice->getId(), $invoice);

// header("Location: /PayPal-PHP-SDK/GetInvoice.php");
//header("Location: /conference-registration-thank-you");

// $invoice echos JSON
echo json_encode($invoice, JSON_PRETTY_PRINT);
return json_encode($invoice, JSON_PRETTY_PRINT);

?>