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

$root = explode('/', $_SERVER['SCRIPT_FILENAME']);
array_pop($root);
$root = implode('/', $root);
$log = $root . '/error.log';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("log_errors", 1);
//ini_set("error_log", $log);

$port = ($_SERVER['SERVER_PORT'] == "80" || $_SERVER['SERVER_PORT'] == 443) ? "" : ":" . $_SERVER['SERVER_PORT'];
$path = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $port . $_SERVER['REQUEST_URI'];
$script = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $port . $_SERVER['SCRIPT_NAME'];

require_once "ams_lib.php";

$action = '';
if (array_key_exists('a', $_GET)) {
	$action = $_GET['a'];
}
if ($action == '') $action = 'create';

switch($action) {
	case 'create':
		// Create invoice.
		$response = amsCreateInvoice("AMS PHP Example", 10000, array(
		 	'return_url' => $script . "?a=success",
			'cancel_url' => $script . "?a=cancel",
			'callback_url' => $path . "ams_callback.php"
		));
		if (array_key_exists("error", $response)) {
			// Error.
			amsLog($response['error']);
		} else {
			// Success. Redirect to payment page.
			return header('Location: ' . $response['invoicePage']);
		}
		break;
	case 'success':
		echo "Payment SUCCESS. See log for detail.";
		break;
	case 'cancel':
		echo "Payment CANCELLED. See log for detail.";
		break;
	default:
		echo "Nothing";
		break;
}

?>