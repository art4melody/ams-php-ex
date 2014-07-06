<?php

/**
 * ©2014 Artabit
 * 
 * Permission is hereby granted to any person obtaining a copy of this software
 * and associated documentation for use and/or modification in association with
 * the artabit.com service.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * AMS PHP payment example using the artabit.com service.
 * 
 */
 
require_once 'ams_options.php';

function amsLog($contents) {
	// Change with your logging preferences or other handling method
	error_log($contents);
}

function amsCurl($url, $apiToken, $apiSecret, $post = false) {
	global $amsOptions;	
	
	$url = ($url[0] == '/') ? $url : "/" . $url;
	$url = 'https://' . AMS_HOST . $url;
	$curl = curl_init($url);
	$length = 0;
	if ($post) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		$length = strlen($post);
	}
	
	$auth = base64_encode($apiToken . ":" . $apiSecret);
	$header = array(
		'Content-Type: application/json',
		"Content-Length: $length",
		"Authorization: Basic $auth"
		);

	curl_setopt($curl, CURLOPT_PORT, AMS_PORT);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // should be 1, verify certificate
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // should be 2, check existence of CN and verify that it matches hostname
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		
	$responseString = curl_exec($curl);
	
	if($responseString == false) {
		$response = array('error' => curl_error($curl));
	} else {
		$response = json_decode($responseString, true);
		if (!$response)
			$response = array('error' => 'Invalid json: ' . $responseString);
	}
	curl_close($curl);

	return $response;
}

// See api documentation for information on these options.
/**
* Example:
* amsCreateInvoice(1442, 1000000, array(
* 	'return_url' => 'https://my.commerce.web/order/complete?p=1234&msg=success',
*	'cancel_url' => 'https://my.commerce.web/order/cancelled?p=1234',
*	'callback_url' => 'https://my.commerce.web/ams_callback.php'
* ));
**/
function amsCreateInvoice($orderId, $price, $options = array()) {	
	global $amsOptions;	
	
	$options = array_merge($amsOptions, $options);	// $options override any options found in ams_options.php
	
	$options['amount_local'] = $price;
	$options['transaction_id'] = strval($orderId);
	
	$postOptions = array('amount_local', 'transaction_id', 'cancel_url', 'return_url', 'callback_url', 'currency_local', 'currency_virtual', 'valid_until');
	foreach($postOptions as $o)
		if (array_key_exists($o, $options))
			$post[$o] = $options[$o];
	$post = json_encode($post);
	
	$response = amsCurl('/invoices', $options['apiToken'], $options['apiSecret'], $post);
	
	return $response;
}

// Call from your notification handler to convert $_POST data to an object containing invoice data
function amsVerifyCallback($amsPublicKey = false) {
	global $amsOptions;
	if (!$amsPublicKey)
		$amsPublicKey = $amsOptions['amsPublicKey'];
	
	$post = file_get_contents("php://input");
	if (!$post)
		return 'ERROR - No post data.';
		
	$json = json_decode($post, true);
	
	if (is_string($json))
		return 'ERROR - Format is not JSON: ' . $json; // error

	if (!array_key_exists('payload', $json)) 
		return 'ERROR - No payload. Invalid format.';
	
	$payload = $json['payload'];
	preg_match('/"payload":(.*), "sign":/', $post, $matches);
	$payloadString = $matches[1];
	$sign = base64_decode($json['sign']);
	$public_key = openssl_get_publickey($amsPublicKey);
	$verify = openssl_verify($payloadString, $sign, $public_key);
	if ($verify != 1) return "ERROR - Response not verified.";
	
	return $payload;
}
