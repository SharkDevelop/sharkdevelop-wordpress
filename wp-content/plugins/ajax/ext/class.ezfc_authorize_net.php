<?php

defined( 'ABSPATH' ) OR exit;

define("EZFC_EXT_AUTHORIZE_NET_VERSION", "1.0.0");

require EZFC_PATH . "lib/authorize.net/autoload.php";
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class EZFC_Extension_Authorize_Net {
	private $auth, $card, $controller, $customer, $order, $payment, $ref_id, $request, $response, $transaction;
	public $environment;

	/**
		set up with cc details
	**/
	public function __construct($token) {
		$login_id = trim(get_option("ezfc_authorize_login_id"));
		$transaction_id = trim(get_option("ezfc_authorize_transaction_key"));

		if (empty($login_id) || empty($transaction_id)) {
			ezfc_send_ajax(Ezfc_Functions::send_message("error", __("Empty Authorize.net API login ID or transaction key.", "ezfc")));
			die();
		}

		$this->auth = new AnetAPI\MerchantAuthenticationType();
		$this->auth->setName($login_id);
		$this->auth->setTransactionKey($transaction_id);

		// Set the transaction's refId
		$this->ref_id = "ref" . time();

		// Create the payment data for a credit card
		$this->card = new AnetAPI\CreditCardType();
		/*$this->card->setCardNumber("4111111111111111");
		$this->card->setExpirationDate("1226");
		$this->card->setCardCode("123");*/

		// token
		$this->card->setIsPaymentToken(true);
		$this->card->setCryptogram("EjRWeJASNFZ4kBI0VniQEjRWeJA=");

		$this->payment = new AnetAPI\PaymentType();
		$this->payment->setCreditCard($this->card);

		// Preparing the order data for the transaction
		$this->order = new AnetAPI\OrderType();
		$this->order->setDescription("New Item");
	}

	/**
		create transaction
	**/
	public function transaction($amount) {
		$this->transaction = new AnetAPI\TransactionRequestType();
		$this->transaction->setTransactionType("authCaptureTransaction"); 
		$this->transaction->setAmount($amount);
		$this->transaction->setOrder($this->order);
		$this->transaction->setPayment($this->payment);
	}

	/**
		customer details
	**/
	public function customer($customer_data) {
		$this->customer = new AnetAPI\CustomerAddressType();
		$this->customer->setFirstName($customer_data["fname"]);
		$this->customer->setLastName($customer_data["lname"]);
		$this->customer->setAddress($customer_data["address"]);
		$this->customer->setCity($customer_data["city"]);
		$this->customer->setState($customer_data["state"]);
		$this->customer->setCountry($customer_data["country"]);
		$this->customer->setZip($customer_data["zip"]);
		$this->customer->setPhoneNumber($customer_data["phone"]);
		$this->customer->setEmail($customer_data["email"]);

		$this->transaction->setBillTo($this->customer);
	}

	/**
		process
	**/
	public function process() {
		$this->request = new AnetAPI\CreateTransactionRequest();
		$this->request->setMerchantAuthentication($this->auth);
		$this->request->setRefId($this->ref_id);
		$this->request->setTransactionRequest($this->transaction);

		$this->controller = new AnetController\CreateTransactionController($this->request);
		$this->response = $this->controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

		if ($this->response != null) {
			if ($this->response->getMessages()->getResultCode() == \SampleCode\Constants::RESPONSE_OK) {
				$tresponse = $this->response->getTransactionResponse();
	
				if ($tresponse != null && $tresponse->getMessages() != null) {
					echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
					echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
					echo "Charge Credit Card TRANS ID : " . $tresponse->getTransId() . "\n";
					echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n";
					echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
				} else {
					echo "Transaction Failed \n";
					if ($tresponse->getErrors() != null) {
						echo " Error code : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
						echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
					}
				}
			} else {
				echo "Transaction Failed \n";
				$tresponse = $this->response->getTransactionResponse();
				if ($tresponse != null && $tresponse->getErrors() != null) {
					echo " Error code : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
					echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
				} else {
					echo " Error code : " . $this->response->getMessages()->getMessage()[0]->getCode() . "\n";
					echo " Error message : " . $this->response->getMessages()->getMessage()[0]->getText() . "\n";
				}
			}
		} else {
			echo "No response returned \n";
		}
	}
}