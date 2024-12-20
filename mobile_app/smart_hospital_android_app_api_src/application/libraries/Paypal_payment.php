<?php


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');
class Paypal_payment {

    private $_CI;
    public $api_config;
    public $currency;

    function __construct() {
        $this->_CI = & get_instance();
        $this->api_config = $this->_CI->paymentsetting_model->getActiveMethod();
      
    }

    public function payment($data) {

       $name = $data['name'];
        $phone = $data['phone'];
        $patient_id = $data['patient_id'];
        if($data["id"]){
            $id = $data['id'];
        }

        $amount_balance = $data['total'];
        $currency = $data['currency_name'];

        $gateway = Omnipay::create('PayPal_Express');
        $gateway->setUsername($this->api_config->api_username);
        $gateway->setPassword($this->api_config->api_password);
        $gateway->setSignature($this->api_config->api_signature);
        $gateway->setTestMode(FALSE);

        $params = array(
            'cancelUrl' => base_url('gateway/paypal/getsuccesspayment'),
            'returnUrl' => base_url('gateway/paypal/getsuccesspayment'),
            'name' => $name,
            'phone' => $phone,
            'patient_id' => $patient_id,
            'id' => $id,
            'description' => $data['productinfo'],
            'amount' => number_format($amount_balance, 2, '.', ''),
            'currency' => $currency,
        );
        $response = $gateway->purchase($params)->send();
        return $response;
    }

    public function success($data) {

       $name = $data['name'];
        $amount_balance = $data['total'];
        $currency = $data['currency_name'];
        $gateway = Omnipay::create('PayPal_Express');
        $gateway->setUsername($this->api_config->api_username);
        $gateway->setPassword($this->api_config->api_password);
        $gateway->setSignature($this->api_config->api_signature);
        $gateway->setTestMode(TRUE);
        $params = array(
            'cancelUrl' => base_url('gateway/paypal/getsuccesspayment'),
            'returnUrl' => base_url('gateway/paypal/getsuccesspayment'),
            'guardian_phone' => $data['phone'],
            'name' => $name,
            'description' => $data['productinfo'],
            'amount' => number_format($amount_balance, 2, '.', ''),
            'currency' => $currency,
        );
        $response = $gateway->completePurchase($params)->send();

        return $response;
    }

}

?>