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

require_once 'ams_lib.php';

$response = amsVerifyCallback();
if (is_string($response)) {
  // LOG ERROR & MESSAGE HERE
  amsLog('ERROR - AMS callback error: ' . $response);
} else {
  $order_id = intval($response['transaction_id']);
  
  switch($response['status']) {
    case 0: // Created
      amsLog('INFO - Invoice created.');
      break;
    case 1: // Received
      amsLog('INFO - Invoice received.');
      break;
    case 2: // Confirmed
      amsLog('INFO - Invoice confirmed.');
      break;
    case -1: // Canceled
      amsLog('INFO - Invoice cancelled.');
      break;
    case -2: // Unknown
      amsLog('INFO - Unknown invoice status.');
      break;
    case -3: // Incomplete
      amsLog('INFO - Invoice incomplete.');
      break;
    case -4: // Late
      amsLog('INFO - Invoice has timeout.');
      break;
  }
}

?>