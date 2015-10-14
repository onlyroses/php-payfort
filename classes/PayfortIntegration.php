<?php

/**
 * @author Payfort
 * @copyright Copyright PayFort 2012-2015
 * @version 1.0 2015-10-11 12:36:23 PM
 */

/**
 * This class has integration methods that help you to complete for integration
 */

class PayfortIntegration {
    
    public $amount;
    public $currency;
    public $merchant_identifier; // payfort merchant identifier
    public $access_code; // merchant access code
    public $order_description; // order description
    public $merchant_reference; // merchant order refrance
    public $customer_email;
    public $customer_ip; // ip adress
    public $language;
    public $command; // operation commnad (AUTHORIZATION)
    public $return_url; // back to merchant url
        
    
    /**
     * calculate fort signature
     * 
     * @param array $requestParams order request parameters
     * @param string $shaRequestPharse as Request encryption Pharse
     * @param string $securityType as security Type (sha256, sha128, sha512)
     * @return strin signature
     */
    public function calculateFortSignature($shaRequestPharse, $securityType) {
        
        $requestParams = $this->getRequestParams();

        ksort($requestParams);
        $concatedStr = '';
        foreach($requestParams as $key => $value) 
        {
            if($value != ''){
                $concatedStr .= strtolower($key).'='.$value;
            }
        }

        $concatedStr = $shaRequestPharse.$concatedStr.$shaRequestPharse;
        
        if($securityType == 'sha256') {
            $signature = hash('sha256', $concatedStr);
        } elseif ($securityType == 'sha128'){
            $signature = sha1($concatedStr);
        } elseif ($securityType == 'sha512'){
            $signature = hash('sha512', $concatedStr);
        }


        return $signature;
    }
    
    /**
     * genarate array of request Params
     * 
     * @return array of $requestParams
     */
    public function getRequestParams()
    {
       $requestParams =   array(
            'amount'                => $this->amount * 100,
            'currency'              => $this->currency,
            'merchant_identifier'   => $this->merchant_identifier,
            'access_code'           => $this->access_code,
            'order_description'     => $this->order_description,
            'merchant_reference'    => $this->merchant_reference,
            'customer_email'        => $this->customer_email,
            'customer_ip'           => $this->customer_ip,
            'language'              => $this->language,
            'command'               => $this->command,
            'return_url'            => $this->return_url,
        );
      
       return $requestParams;
    }
    
    /**
     * redirect to fort payment page 
     * 
     * @param boolean $testMode (true, false) , if test mode is true the redirect will be to the sandBox else will be to the production
     * @param aray $requestParams order request parameters
     * @param string $action as fortm action
     */
    function redirect($testMode, $requestParams = array(), $action = 'GET') 
    {
        if ($testMode) {
            //sandBox redirection
            $redirectUrl   = 'https://sbcheckout.payfort.com/FortAPI/paymentPage';
        } else {
            //production redirect
            $redirectUrl = 'https://checkout.payfort.com/FortAPI/paymentPage';
        }

        if($action == 'POST') {
            echo "<html xmlns='http://www.w3.org/1999/xhtml'>\n<head></head>\n<body>\n";
            echo "<form action='$redirectUrl' method='post' name='frm'>\n";
            foreach ($requestParams as $a => $b) {
                echo "\t<input type='hidden' name='".htmlentities($a)."' value='".htmlentities($b)."'>\n";
            }
            echo "\t<script type='text/javascript'>\n";
            echo "\t\tdocument.frm.submit();\n";
            echo "\t</script>\n";
            echo "</form>\n</body>\n</html>";
            die();
        }
        else {
            // mothod get
            $urlQuery = http_build_query($requestParams);
            $redirectUrl .= '?'.$urlQuery;
            header("Location: $redirectUrl");
            die();
        }
    }
    
    /**
     * calculate Signature after back to merchant and comapre it with request Signature
     * 
     * @param string $shaResponcePharse
     * @param string $securityType
     * @return boolean (true/ false)
     */
    public function calculateReturnToMerchantSignature($shaResponcePharse, $securityType)
    {
        $requestParams      = $this->getReturnRequestParams();

        $returnSignature    = $_REQUEST['signature'];

        ksort($requestParams);
        $concatedStr = '';
        foreach($requestParams as $key => $value) 
        {
            if($value != ''){
                $concatedStr .= strtolower($key).'='.$value;
            }
        }

        $concatedStr = $shaResponcePharse.$concatedStr.$shaResponcePharse;
        
        if($securityType == 'sha256') {
            $signature = hash('sha256', $concatedStr);
        } elseif ($securityType == 'sha128'){
            $signature = sha1($concatedStr);
        } elseif ($securityType == 'sha512'){
            $signature = hash('sha512', $concatedStr);
        }
        
        if ($returnSignature == $signature) {
            return true;
        } else {
            return false;
        }
       
        return $signature;
    }
    
    
    /**
     * get return request params
     * @return array $request 
     */
    public function getReturnRequestParams() {
        
        $request = $_REQUEST;
        
        //remove signature param deom parameters before calculate signature
        if (isset($request['signature'])) {
            unset($request['signature']);
            
        }
       
        return $request;
    
    }
    
}
