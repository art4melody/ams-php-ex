aMS PHP Integration Example
===========================

Artabit Merchant Service PHP integration tutorial.

Structure
---------
1. `ams_options.php` - used to store configurations
2. `ams_lib.php` - library of php functions related to AMS API
3. `example.php` - sample code to create invoice to AMS
4. `ams_callback.php` - sample code to receive callback from AMS

Tutorial
--------
1. Copy all of these files to your public PHP commerce web (public means accessible by external sites)
2. Create an application access (API Token and API Secret) at ams.artabit.com
3. Configure `ams_options.php` and put API token and API Secret value from your application
4. Configure `ams_lib.php` -> amsLog function to fit your application logging mechanism
5. `See example.php` to know how to create invoice from AMS
6. Configure `ams_callback.php` to fit your application requirements (update order status, notify buyer, etc when AMS sends invoice status update callback)