<?php
define('_PS_ADMIN_DIR_', getcwd());
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility

include(PS_ADMIN_DIR.'/../config/config.inc.php');
include(PS_ADMIN_DIR.'/functions.php');
//include(PS_ADMIN_DIR.'/header.inc.php');
//require_once("includes/functions/database.php");
require_once("ifirma/BuilderClasses.php");
require_once("ifirma/ifirma_functions.php");
$cart_order_id = $_GET['cart_order_id'];
if (!class_exists('Cookie'))
	exit();

$cookie = new Cookie('psAdmin', substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__), -10));
if (!$cookie->isLoggedBack())
	die;
handle_invoice_download($cart_order_id);

//require('footer.inc.php');
?>