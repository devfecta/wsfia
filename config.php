<?php

define('__WEBROOT__', dirname(dirname(__FILE__)));

//define('DNS', 'mysql:host=localhost;dbname=wsfia');
//define('USERNAME', 'devfecta');
//define('PASSWORD', 'Fire@1976');
define('DNS', 'mysql:host=104.198.147.108;dbname=wsfia');
define('USERNAME', 'root');
define('PASSWORD', 'm3tallica');

//(@__DIR__ == '__DIR__') && define('__DIR__', realpath(dirname(__FILE__)));
define("DATABASE_TYPE", "MySQL");        
define("TTY", "");
define("OPTIONS", "");
define("PORT", "");
define("VERSION", "v2019");
/*
$TestMode = false;
if ($TestMode)
{
	define("DOMAIN", "localhost");
	define("HOSTNAME",        "localhost:3306");
	define("DATABASE",        "wsfia");
	define("LOGIN",           "root");
	define("PASSWORD",        "thecr0w");
	
	define("PAYPALENDPOINT",        "api.sandbox.paypal.com");
	define("PAYPALCLIENTID",        "AR8daRDAj04DdpGhtdWY1a_AgmWFHf_0ClNpPdbIh7ka2DY9S6g_1RoZm7kd");
	define("PAYPALSECRET",        "EGHodxDMiqiM9__o_wxIDwSjcL-QprwGLGD1c3WX-9MA3rYQfqKrwSJRoMvR");
}
else 
{
	define("DOMAIN", $_SERVER['SERVER_NAME']);
	//define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/adhound");
	define("HOSTNAME",        "localhost");
	define("DATABASE",        "wsfia2019");
	define("LOGIN",           "wsfia_DB");
	define("PASSWORD",        "Insp3ct0r");
	
	define("PAYPALENDPOINT",        "api.paypal.com");
	define("PAYPALCLIENTID",        "AYC91hDd8EgEkisunHgflQxmi7884apE81A41K-DRbBpMOcPSKfUxff_xD8K");
	define("PAYPALSECRET",        "EEmQRBBMM_zPHQww5TzRVsx_F8pKqacLxYaWSfFjpdb36STdtZFTsrWZgFMQ");
}
*/
define("COPYRIGHT", '&copy; Copyright ' . date('Y') . ' <a href="http://www.wsfia.org" class="text-white" title="Wisconsin State Fire Inspectors Association">Wisconsin State Fire Inspectors Association</a> All Rights Reserved');

?>
