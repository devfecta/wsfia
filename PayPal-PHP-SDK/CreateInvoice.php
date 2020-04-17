<?php
ob_start();
session_start();

// # Create Invoice Sample
// This sample code demonstrate how you can create
// an invoice.
require './bootstrap.php';
use PayPal\Api\Address;
use PayPal\Api\BillingInfo;
use PayPal\Api\Cost;
use PayPal\Api\Currency;
use PayPal\Api\Invoice;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\InvoiceItem;
use PayPal\Api\MerchantInfo;
use PayPal\Api\PaymentTerm;
use PayPal\Api\Phone;
use PayPal\Api\ShippingInfo;
$invoice = new Invoice();
// ### Invoice Info
// Fill in all the information that is
// required for invoice APIs
$invoice
    ->setMerchantInfo(new MerchantInfo())
    ->setBillingInfo(array(new BillingInfo()))
    ->setNote("")  // <-- Notes section on the invoice
    ->setPaymentTerm(new PaymentTerm())
    ->setShippingInfo(new ShippingInfo());
// ### Merchant Info
// A resource representing merchant information that can be
// used to identify merchant
// Make changes to this file, bootstrap, and 
/**
 * setEmail("admin@wsfia.org") LIVE
 * setEmail("support@wsfia.org") SANDBOX
*/
$invoice->getMerchantInfo()
    ->setEmail("support@wsfia.org")   
    ->setFirstName("WSFIA")
    ->setLastName("Treasurer")
    ->setbusinessName("Wisconsin State Fire Inspectors Association")
    ->setPhone(new Phone())
    ->setAddress(new Address());
$invoice->getMerchantInfo()->getPhone()
    ->setCountryCode("US")
    ->setNationalNumber("262-719-3006");
// ### Address Information
// The address used for creating the invoice
$invoice->getMerchantInfo()->getAddress()
    ->setLine1("PO. Box 1075")
    ->setCity("Menomonee Falls")
    ->setState("WI")
    ->setPostalCode("53052")
    ->setCountryCode("US");
	
// echo "<br />Session Invoice:<pre>". print_r($_SESSION['Registrations']['Vendor'], true) . "</pre>";

if (isset($lineItems->billing) && isset($lineItems->lineItems)) {

    // ### Billing Information
    // Set the email address for each billing
    $billing = $invoice->getBillingInfo();
    $billing[0]
        ->setEmail($lineItems->billing->billingEmailAddress);
    $billing[0]->setBusinessName($lineItems->billing->billingBusiness->name)
        ->setAdditionalInfo("This is the billing Info")
        ->setAddress(new InvoiceAddress());
    $billing[0]->getAddress()
        ->setLine1($lineItems->billing->billingBusiness->streetAddress)
        ->setCity($lineItems->billing->billingBusiness->city)
        ->setState($lineItems->billing->billingBusiness->stateAbbreviation)
        ->setPostalCode($lineItems->billing->billingBusiness->zipcode)
        ->setCountryCode("US");

    // ### Items List
    // You could provide the list of all items for detailed breakdown of invoice
    $items = array();

    $i = 0;

    foreach($lineItems->lineItems as $lineItem) {

        $items[$i] = new InvoiceItem();
        $items[$i]
            ->setName($lineItem->itemName)
            ->setDescription($lineItem->itemDescription)
            ->setQuantity($lineItem->quantity)
            ->setUnitPrice(new Currency());
        $items[$i]->getUnitPrice()
            ->setCurrency("USD")
            ->setValue($lineItem->price);
        // #### Tax Item
        // You could provide Tax information to each item.
        $tax = new \PayPal\Api\Tax();
        $tax->setPercent(0)->setName("Local Tax on Sutures");
        $items[$i]->setTax($tax);

        $i++;

    }

}



// ### Billing Information
if (isset($_SESSION['Registrations']['Vendor'])) {
    /*
    // Set the email address for each billing
    $billing = $invoice->getBillingInfo();
    $billing[0]
        ->setEmail($_SESSION['Registrations']['Vendor'][0]['eMailAddress']);
    $billing[0]->setBusinessName($_SESSION['Registrations']['Vendor'][0]['companyName'])
        ->setAdditionalInfo("This is the billing Info")
        ->setAddress(new InvoiceAddress());
    $billing[0]->getAddress()
        ->setLine1($_SESSION['Registrations']['Vendor'][0]['streetAddress'])
        ->setCity($_SESSION['Registrations']['Vendor'][0]['cityName'])
        ->setState($_SESSION['Registrations']['Vendor'][0]['stateAbbreviation'])
        ->setPostalCode($_SESSION['Registrations']['Vendor'][0]['zipCode'])
        ->setCountryCode("US");

    // ### Items List
    // You could provide the list of all items for detailed breakdown of invoice
    $items = array();

    $lineItemIndex = 0;

    for ($index = 0; $index < count($_SESSION['Registrations']); $index++) {
        $items[$lineItemIndex] = new InvoiceItem();
        $items[$lineItemIndex]
            ->setName($_SESSION['Registrations']['Vendor'][$index]['vendorBoothOption']['optionName'])
            ->setDescription($_SESSION['Registrations']['Vendor'][$index]['lineItemDescription'])
            ->setQuantity(1)
            ->setUnitPrice(new Currency());
        $items[$lineItemIndex]->getUnitPrice()
            ->setCurrency("USD")
            ->setValue($_SESSION['Registrations']['Vendor'][$index]['vendorBoothOption']['optionCost']);
        // #### Tax Item
        // You could provide Tax information to each item.
        $tax = new \PayPal\Api\Tax();
        $tax->setPercent(0)->setName("Local Tax on Sutures");
        $items[$lineItemIndex]->setTax($tax);

        if (isset($_SESSION['Registrations']['Vendor'][$index]['SponsorOptions'])) {

            for ($indexOptions = 0; $indexOptions < count($_SESSION['Registrations']['Vendor'][$index]['SponsorOptions']); $indexOptions++) {
                $lineItemIndex++;

                $items[$lineItemIndex] = new InvoiceItem();
                
                $items[$lineItemIndex]
                    ->setName($_SESSION['Registrations']['Vendor'][$index]['SponsorOptions'][$indexOptions]['OptionName'])
                    ->setDescription("")
                    ->setQuantity($_SESSION['Registrations']['Vendor'][$index]['SponsorOptions'][$indexOptions]['OptionQuantity'])
                    ->setUnitPrice(new Currency());
                $items[$lineItemIndex]->getUnitPrice()
                    ->setCurrency("USD")
                    ->setValue(intval($_SESSION['Registrations']['Vendor'][$index]['SponsorOptions'][$indexOptions]['OptionCost']));
                // #### Tax Item
                // You could provide Tax information to each item.
                $tax = new \PayPal\Api\Tax();
                $tax->setPercent(0)->setName("Local Tax on Sutures");
                $items[$lineItemIndex]->setTax($tax);
                // $lineItemIndex++;
            }
        }
        $lineItemIndex++;
    }
    */
} else {

    //return $_POST['lineItems'];

    // ### Billing Information
    // Set the billing for the invoice
    /*
    $billing = $invoice->getBillingInfo();
    $billing[0] 
        ->setEmail($_POST['lineItems'][0]['emailAddress']);
    $billing[0]->setBusinessName($_POST['lineItems'][0]['business']['name'])
        ->setAdditionalInfo("Additional Order Information")
        ->setAddress(new InvoiceAddress());
    $billing[0]->getAddress()
        ->setLine1($_POST['lineItems'][0]['business']['streetAddress'])
        ->setCity($_POST['lineItems'][0]['business']['city'])
        ->setState($_POST['lineItems'][0]['business']['stateAbbreviation'])
        ->setPostalCode($_POST['lineItems'][0]['business']['zipcode'])
        ->setCountryCode("US");
        
   // $lineItemIndex = 0;
    // ### Line Items
    // You could provide the list of all items for detailed breakdown of invoice
    $items = array();
    for ($i = 0; $i < count($_POST['lineItems']); $i++) {

        $items[$i] = new InvoiceItem();
        $items[$i]
            ->setName($_POST['lineItems'][$i]['itemName'])
            ->setDescription($_POST['lineItems'][$i]['itemDescription'])
            ->setQuantity(1)
            ->setUnitPrice(new Currency());
        $items[$i]->getUnitPrice()
            ->setCurrency("USD")
            ->setValue($_POST['lineItems'][$i]['price']);
        // #### Tax Item
        // You could provide Tax information to each item.
        $tax = new \PayPal\Api\Tax();
        $tax->setPercent(0)->setName("Local Tax on Sutures");
        $items[$i]->setTax($tax);

    }
    */
    /*
    // Set the email address for each billing
    $billing = $invoice->getBillingInfo();
    $billing[0]
        ->setEmail($_SESSION['Registrations'][0]['eMailAddress']);
    $billing[0]->setBusinessName($_SESSION['Registrations'][0]['departmentName'])
        ->setAdditionalInfo("This is the billing Info")
        ->setAddress(new InvoiceAddress());
    $billing[0]->getAddress()
        ->setLine1($_SESSION['Registrations'][0]['streetAddress'])
        ->setCity($_SESSION['Registrations'][0]['cityName'])
        ->setState($_SESSION['Registrations'][0]['stateAbbreviation'])
        ->setPostalCode($_SESSION['Registrations'][0]['zipCode'])
        ->setCountryCode("US");

    // ### Items List
    // You could provide the list of all items for detailed breakdown of invoice
    $items = array();

    $lineItemIndex = 0;

    for ($index = 0; $index < count($_SESSION['Registrations']); $index++) {
        $items[$lineItemIndex] = new InvoiceItem();
        $items[$lineItemIndex]
            ->setName($_SESSION['Registrations'][$index]['lineItemName'])
            ->setDescription($_SESSION['Registrations'][$index]['lineItemDescription'])
            ->setQuantity(1)
            ->setUnitPrice(new Currency());
        $items[$lineItemIndex]->getUnitPrice()
            ->setCurrency("USD")
            ->setValue($_SESSION['Registrations'][$index]['ConferenceFee']);
        // #### Tax Item
        // You could provide Tax information to each item.
        $tax = new \PayPal\Api\Tax();
        $tax->setPercent(0)->setName("Local Tax on Sutures");
        $items[$lineItemIndex]->setTax($tax);

        if (isset($_SESSION['Registrations'][$index]['guests'])) {

            for ($indexGuest = 0; $indexGuest < count($_SESSION['Registrations'][$index]['guests']); $indexGuest++) {
                $lineItemIndex++;

                $items[$lineItemIndex] = new InvoiceItem();

                $guestCost = 0;

                if (isset($_SESSION['Registrations'][$index]['guests'][$indexGuest]['guestBanquet'])) {
                    $guestCost = $guestCost + $_SESSION['Registrations'][$index]['guests'][$indexGuest]['guestBanquet'];
                }

                if (isset($_SESSION['Registrations'][$index]['guests'][$indexGuest]['guestVendorNight'])) {
                    $guestCost = $guestCost + $_SESSION['Registrations'][$index]['guests'][$indexGuest]['guestVendorNight'];
                }

                $items[$lineItemIndex]
                    ->setName($_SESSION['Registrations'][$index]['guests'][$indexGuest]['lineItemName'])
                    ->setDescription("")
                    ->setQuantity(1)
                    ->setUnitPrice(new Currency());
                $items[$lineItemIndex]->getUnitPrice()
                    ->setCurrency("USD")
                    ->setValue($guestCost);
                // #### Tax Item
                // You could provide Tax information to each item.
                $tax = new \PayPal\Api\Tax();
                $tax->setPercent(0)->setName("Local Tax on Sutures");
                $items[$lineItemIndex]->setTax($tax);
                // $lineItemIndex++;
            }
        }
        $lineItemIndex++;
    }
    */
}

$invoice->setItems($items);
// #### Final Discount
// You can add final discount to the invoice as shown below. You could either use "percent" or "value" when providing the discount
/*
$cost = new Cost();
$cost->setPercent("0");
$invoice->setDiscount($cost);
*/
$invoice->getPaymentTerm()
    ->setTermType("NET_45");
// ### Shipping Information
/*
$invoice->getShippingInfo()
    ->setFirstName("")
    ->setLastName("")
    ->setBusinessName("Not applicable")
    ->setPhone(new Phone())
    ->setAddress(new InvoiceAddress());
$invoice->getShippingInfo()->getPhone()
    ->setCountryCode("")
    ->setNationalNumber("");
$invoice->getShippingInfo()->getAddress()
    ->setLine1("")
    ->setCity("")
    ->setState("")
    ->setPostalCode("")
    ->setCountryCode("");
*/
// ### Logo
// You can set the logo in the invoice by providing the external URL pointing to a logo
$invoice->setLogoUrl('https://pics.paypal.com/00/s/MTUwWDE5MFhQTkc/p/YWRhN2MyY2EtODk5MC00M2ZhLThiNWItNzBlZGE4YTFjMmE5/image_109.PNG');
// For Sample Purposes Only.
$request = clone $invoice;
try {
    // ### Create Invoice
    // Create an invoice by calling the invoice->create() method
    // with a valid ApiContext (See bootstrap.php for more on `ApiContext`)
    
    $invoice->create($apiContext);

} catch (Exception $ex) {
    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
    ResultPrinter::printError("Create Invoice", "Invoice", null, $request, $ex);
    exit(1);
}
// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
// ResultPrinter::printResult("Create Invoice", "Invoice", $invoice->getId(), $request, $invoice);

return $invoice;
?>