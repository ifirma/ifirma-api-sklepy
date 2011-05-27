<?php
require_once ('includes/application_top.php');
require_once("includes/functions/database.php");
require_once("ifirma/BuilderClasses.php");
require_once("ifirma/ifirma_functions.php");
$cart_order_id = $_POST['cart_order_id'];
ini_set('zend.ze1_compatibility_mode', 0);
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
$shipping = get_order_ship($cart_order_id);

if( can_process_request_for_invoice_generation($cart_order_id) ){
	$invoice =  get_invoice($order_summary, $customer, $wysylka);
	add_invoice_note($invoice,$cart_order_id);
	add_invoice_positions($invoice,$order_details,$discount_percentage_rate,$discount_amount);
	add_invoice_ship_position($invoice,$shipping);
	handle_invoice_generation($cart_order_id,$invoice);
}else if( can_process_request_for_pro_forma_generation($cart_order_id) ){
	$invoice =  get_pro_forma($order_summary, $customer);
	add_invoice_note($invoice,$cart_order_id);
	add_invoice_positions($invoice,$order_details,$discount_percentage_rate,$discount_amount);
	add_invoice_ship_position($invoice,$shipping);
	handle_pro_forma_generation($cart_order_id,$invoice);
}else{
	throw new Exception('not authorized to perform operation');
}
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
