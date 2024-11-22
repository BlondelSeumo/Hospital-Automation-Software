<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Stripe_payment {

    private $_CI;
    public $api_config;


    
    function __construct()
    {
        $this->_CI = &get_instance();
        $this->api_config = $this->_CI->paymentsetting_model->getActiveMethod();

        $stripe_secret_api_key = $this->api_config->api_secret_key;
        \Stripe\Stripe::setApiKey($stripe_secret_api_key);
    }

 
     public function payment($data) {
       
         $gateway = Omnipay::create('Stripe');
         $secret_key = $this->api_config->api_secret_key;
         $gateway->setApiKey($secret_key);

         $params = array(
             'cancelUrl' => base_url('parent/payment/getsuccesspayment'),
             'returnUrl' => base_url('parent/payment/getsuccesspayment'),
             'amount' => number_format($data['total'], 2, '.', ''), 
             'currency' => $data['currency'],
             'token' => $data['stripeToken']
         );
         $response = $gateway->purchase($params)->send();
         return $response;
     }

    public function PaymentIntent($jsonObj)
    {

        $amount = $jsonObj->amount;
        $currency = $jsonObj->currency;
        $description = 'payment details';
        // Define the product item price and convert it to cents
        $product_price = round($amount * 100);

        try {
            // Create PaymentIntent with amount, currency and description
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $product_price,
                'currency' => $currency,
                'description' => $description,
                'payment_method_types' => ['card'],
            ]);

            $output = [
                'paymentIntentId' => $paymentIntent->id,
                'clientSecret' => $paymentIntent->client_secret
            ];

            echo json_encode($output);
        } catch (Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }





    public function AddCustomer($jsonObj)
    {

        $payment_intent_id = !empty($jsonObj->payment_intent_id) ? $jsonObj->payment_intent_id : '';
        $fullname = !empty($jsonObj->fullname) ? $jsonObj->fullname : '';
        $email = !empty($jsonObj->email) ? $jsonObj->email : '';

        // Add new customer fullname and email to stripe 
        try {
            $customer = \Stripe\Customer::create(array(
                'name' => $fullname,
                'email' => $email
            ));
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if (empty($error) && !empty($customer)) {
            try {
                // Attach Customer Data with PaymentIntent using customer ID
                \Stripe\PaymentIntent::update($payment_intent_id, [
                    'customer' => $customer->id
                ]);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            $output = [
                'customer_id' => $customer->id
            ];
            echo json_encode($output);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $error]);
        }
    }

    public function InsertTransaction($jsonObj)
    {
        $payment = !empty($jsonObj->payment_intent) ? $jsonObj->payment_intent : '';
        $customer_id = !empty($jsonObj->customer_id) ? $jsonObj->customer_id : '';

        // Retrieve customer information from stripe
        try {
            $customerData = \Stripe\Customer::retrieve($customer_id);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if (empty($error)) {
            $return_array = ['status' => 1, 'payment' => $payment];
            return $return_array;
        } else {
            $return_array = ['status' => 0, 'error' => $error];
            return $return_array;
        }
    }

}

?>