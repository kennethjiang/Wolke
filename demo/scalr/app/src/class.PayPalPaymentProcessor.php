<?

	class PayPalPaymentProcessor
	{
		/**
		 * Module config
		 *
		 * @var DataForm
		 */
		public $Config;
				
		/**
		 * Constructor 
		 * @param DataForm $ConfigDataForm DataForm object that you returned in GetConfigurationForm(), filled with values.
		 * @return void
		 */
		public function __construct($Config)
		{
			$this->Config = $Config;
		}
		
		/**
		 * Must return module name.
		 * @return string Module name
		 */
		public function GetModuleName()
		{
			return "PayPal";
		}
		
		/**
		 * This method is called in postback routine, to check either this (and not some other) module must process this postback.
		 * If you return true, OnPaymentComplete() will be called.
		 * @param array $request Anything that we received from payment gateway (basically $_REQUEST).
		 * @return bool true, if it does seem that payment postback is posted for this module.
		 */
		public function CheckSignature($request)
		{
			return (
    				$request['verify_sign'] && 
    				$request['receiver_email'] && 
    				$request['payer_email'] &&
    				$request['payer_id']
				   );
		}
		
		/**
		 * Must return Order ID. You passed it to payment gateway in RedirectToGateway(), remember? Now it should return it back, so you know what this payment is for. And you return it.
		 * @param array $request Anything that we received from payment gateway (basically $_REQUEST).
		 * @return int order ID
		 */
		public function GetOrderID($request)
		{
			return $request["custom"];
		}
		
		/**
		 * Must return a DataForm object that will be used to draw a configuration form for this module.
		 * @return DataForm object
		 */
		public static function GetConfigurationForm()
		{
			$ConfigurationForm = new DataForm();
			$ConfigurationForm->AppendField( new DataFormField("business", FORM_FIELD_TYPE::TEXT, "Business E-mail"));
			$ConfigurationForm->AppendField( new DataFormField("receiver", FORM_FIELD_TYPE::TEXT, "Receiver E-mail"));
			$ConfigurationForm->AppendField( new DataFormField("isdemo", FORM_FIELD_TYPE::CHECKBOX , "Test mode", 1));
			$ConfigurationForm->AppendField( new DataFormField("currency", FORM_FIELD_TYPE::TEXT , "Currency"));
						
			return $ConfigurationForm;
		}
		
		/**
		 * Must return a DataForm object  that will be used to draw a payment form for this module.
		 * @return DataForm object. 
		 */
		public static function GetPaymentForm()
		{
			return false;
		}
		
		/**
		 * This method is called to validate either user filled all fields of your form properly.
		 * If you return true, ProccessPayment will be called. If you return array, user will be presented with values of this array as errors. 
		 *
		 * @param array $post_values
		 * @return true or array of error messages.
		 */
		public function ValidatePaymentFormData($post_values)
		{
			return true;
		}
		
		/**
		 * This method is called when we received a postback from payment proccessor and CheckSignature() returned true. 
		 *
		 * @param array $request Anything that we received from payment gateway (basically $_REQUEST).
		 * @return bool True if payment succeed or false if failed. If payment is failed and you return false, $this->GetFailureReason() will also be called.
		 */
		public function OnPaymentComplete($request)
		{
			$res = $this->PostBack($request);
			
			$result =  strcmp($res, "VERIFIED") == 0 && 
			$request['mc_currency'] == $this->Config->GetFieldByName("currency")->Value && 
			($request['receiver_email'] == 'paypal@scalr.com' || strtolower($request['business']) == strtolower($this->Config->GetFieldByName("business")->Value) || 
			(strtolower($this->Config->GetFieldByName("receiver")->Value) == strtolower($request['receiver_email'])));
			
			$result = ($result && ($request["payment_status"] == "Completed" || 
										($request["payment_status"] == "Pending" && 
											$this->Config->GetFieldByName("isdemo")->Value == 1
										)
								  )
					  );
			
			if ($result)
			    return true;
			else
			{
			    $this->FailureReason = _("Payment notify validation falied: {$res}");
			    return false;
			}
		}
		
		/**
		 * Redirect user to gateway payment form, using HTTP 'Location:' header or UI::RedirectPOST($host, $values);
		 * 
		 * @param float $amount Purchase amount
		 * @param int $invoiceid Can be used as an unique identifier.
		 * @param float $amount
		 * @param string $payment_for Human-readable description of the payment
		 * @param int $term
		 * @param string $term_unit
		 * @return void
		 */
		public final function RedirectToGateway($invoiceid, $amount, $payment_for, $term, $term_unit = "M")
		{
			$url = $this->GetDeferredPaymentURL($invoiceid, $amount, $payment_for, $term, $term_unit);
					
			header("Location: {$url}");
			exit();
		}
		
		/**
		 * This method is called if you return false in OnPaymentComplete();
		 * If your payment gateway supports this, you can provide a user with an URL, which can be used to pay this invoice later. This URL will be emailed to user.
		 * This most likely will be the URL that you are redirecting to inside RedirectToGateway().
		 * @param float $amount Purchase amount
		 * @param int $invoiceid Can be used as an unique identifier.
		 * @param float $amount
		 * @param string $payment_for Human-readable description of the payment
		 * @param int $term
		 * @param string $term_unit
		 * @return string URL of the payment page, with all parameters.
		 */
		public function GetDeferredPaymentURL($invoiceid, $amount, $payment_for, $term, $term_unit = "M")
		{
			$host = ($this->Config->GetFieldByName("isdemo")->Value == 1) ? "www.sandbox.paypal.com" : "www.paypal.com";
				
			$data = array(
				"business" 		=> $this->Config->GetFieldByName("business")->Value,
				"page_style"	=> "PayPal",
				"item_name"		=> $payment_for,
				"notify_url"	=> CONFIG::$IPNURL,
				"no_shipping"	=> 1,
				"return"		=> CONFIG::$PDTURL,
				"no_note"		=> 1,
				"currency_code"	=> $this->Config->GetFieldByName("currency")->Value,
				"a3"			=> $amount,
				"p3"			=> $term,
				"t3"			=> $term_unit,
				"src"			=> 1,
				"sra"			=> 1,
				"custom"		=> $invoiceid
			);
						
			return "https://{$host}/subscriptions/?".http_build_query($data);	
		}
		
		/**
		* Send postback to PayPal server
		* @return string $res
		*/
		private final function PostBack($request)
		{
			// read the post from PayPal system and add 'cmd'
			$req = 'cmd=_notify-validate';
			
			foreach ($request as $key => $value) 
			{
				$value = urlencode(stripslashes($value));
				$req .= "&{$key}={$value}";
			}
			
			// post back to PayPal system to validate
			$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
			
			// open socket
			$host = ($this->Config->GetFieldByName("isdemo")->Value == 1) ? "www.sandbox.paypal.com" : "www.paypal.com";
			
			for($i = 1; $i < 4; $i++)
			{
				$fp = @fsockopen($host, 80, $errno, $errstr, 30);
							
				if ($fp)
				{
					//Push response
					@fputs($fp, $header . $req);
					// Read response from server
					while (!feof($fp)) 
						$res = @fgets($fp, 128);
					
					// Close pointer
					@fclose ($fp);
					
					if ($res)
						return $res;
				}

				sleep(3);
			}
			
			return false;
		}
		
		/**
		 * This method is called if payment failes.
		 * Must return a string with explanation of a payment reason.
		 * @return string
		 */
		public function GetFailureReason()
		{
			return $this->FailureReason;
		}
		
		public static function GetMinimumAmount()
		{
			return false;
		}
	}

?>