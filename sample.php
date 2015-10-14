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

$amount                 = 1000;
$currency               = 'USD';
$merchant_identifier    = 'merchant_identifier';
$access_code            = 'access_code';
$order_description      = 'order1';
$customer_email         = 'test@email.com';
$customer_ip            = '93.95.204.106';
$language               = 'en';
$command                = 'AUTHORIZATION';
$return_url             = 'returnPageSample.php';
$merchant_reference     = uniqid('ref_');

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