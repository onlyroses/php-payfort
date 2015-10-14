<?php

/**
 * @author Payfort
 * @copyright Copyright PayFort 2012-2015 
 * @version 1.0 2015-10-11 2:39:41 PM
 */


error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'classes/PayfortIntegration.php';

$testMode = TRUE;

$amount                 = 1000; // amount of the transaction , please check amount parameter in the integration guide 
$currency               = 'USD'; // currncy of the order
$merchant_identifier    = 'merchant_identifier'; // you will find this value under security settings of your account
$access_code            = 'access_code'; // you will find this value under security settings of your account
$order_description      = ''; // description of the order
$customer_email         = 'test@email.com'; // email of the customer
$customer_ip            = 'IP Address'; // IP address of the client 
$language               = 'en'; // en or ar
$command                = ''; // one of the values listed in the integration guide 
$return_url             = 'returnPageSample.php'; // Full URL of the return page , customer will be redirected to this URL after completing the transaction
$merchant_reference     = uniqid('ref_'); // order reference from the merchant for this transaction

$payfortIntegration = new PayfortIntegration();

// 1- set request parameters
$payfortIntegration->amount                 = $amount;
$payfortIntegration->currency               = $currency ;
$payfortIntegration->merchant_identifier    = $merchant_identifier;
$payfortIntegration->access_code            = $access_code;
$payfortIntegration->order_description      = $order_description;
$payfortIntegration->merchant_reference     = $merchant_reference;
$payfortIntegration->customer_ip            = $customer_ip;
$payfortIntegration->customer_email         = $customer_email;
$payfortIntegration->language               = $language;
$payfortIntegration->command                = $command;
$payfortIntegration->return_url             = $return_url;

// 2- generate request Paramters
$requestParams  = $payfortIntegration->getRequestParams();

// 3- generate request signature
$signature      = $payfortIntegration->calculateFortSignature('SHA_request_phrase','sha256');

// 4- add signature to the request
$requestParams['signature'] = $signature;

// 5-redirect to pafort payment page
$payfortIntegration->redirect($testMode, $requestParams, 'POST');