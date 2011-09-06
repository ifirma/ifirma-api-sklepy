<?php
//error_reporting(E_ALL);
define('_PS_ADMIN_DIR_', getcwd());
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility

include(PS_ADMIN_DIR.'/../config/config.inc.php');
include(PS_ADMIN_DIR.'/functions.php');
require_once("ifirma/BuilderClasses.php");
require_once("ifirma/ifirma_functions.php");

$cart_order_id = $_POST['cart_order_id'];

if (!class_exists('Cookie'))
	exit();

$cookie = new Cookie('psAdmin', substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__), -10));
if (!$cookie->isLoggedBack())
	die;
$order_summary = get_order_summary($cart_order_id);
verify_order_exists($order_summary);


$order_details =  get_order_details($cart_order_id);
$wysylka = false;
if(isset($_POST['Wysylkowa']) && $_POST['Wysylkowa'] == '1'){
	$wysylka = true;
}
$order_summary = $order_summary;
$order_data = get_order_data($cart_order_id);
$customer = get_order_customer($order_data);
//$shipping = get_order_ship($cart_order_id);



if( can_process_request_for_invoice_generation($cart_order_id) ){
	$invoice =  get_invoice($order_summary, $customer, $wysylka);
	
	add_invoice_note($invoice,$cart_order_id);
	add_invoice_positions($invoice,$order_details,$discount_percentage_rate,$discount_amount);
	add_invoice_ship_position($invoice,$order_summary);

	weryfikuj_fakture_pod_katem_zgodnosci_zaokraglen($invoice);
	handle_invoice_generation($cart_order_id,$invoice);
}else if( can_process_request_for_pro_forma_generation($cart_order_id) ){
	$invoice =  get_pro_forma($order_summary, $customer);
	add_invoice_note($invoice,$cart_order_id);
	add_invoice_positions($invoice,$order_details,$discount_percentage_rate,$discount_amount);
	add_invoice_ship_position($invoice,$order_summary);
	handle_pro_forma_generation($cart_order_id,$invoice);
}else{
	throw new Exception('not authorized to perform operation');

}
?>