<?php

//na serwerze produkcyjnym usunac [/opencart]
require_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');
require_once(DIR_SYSTEM . 'startup.php');

require_once("ifirma/BuilderClasses.php");
require_once("ifirma/ifirma_functions.php");
$cart_order_id = $_GET['cart_order_id'];
$registry = new Registry();
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('session', new Session());
if($registry->get('session')->data['token']!=$_GET['token']){
	echo "Wystąpił problem. Zaloguj się ponownie.";
}else{
$order_summary = get_order_summary($cart_order_id);
verify_order_exists($order_summary);


$order_details =  get_order_details($cart_order_id);
$wysylka = false;
if(isset($_GET['Wysylkowa']) && $_GET['Wysylkowa'] == '1'){
	$wysylka = true;
}
$order_summary = $order_summary;
$order_data = get_order_data($cart_order_id);
$customer = get_order_customer($order_data);

if( can_process_request_for_invoice_generation($cart_order_id) ){
	$invoice =  get_invoice($order_summary, $customer, $wysylka);
	
	add_invoice_note($invoice,$cart_order_id);
	add_invoice_positions($invoice,$order_details);
	add_invoice_ship_position($invoice,$order_summary);
	weryfikuj_fakture_pod_katem_zgodnosci_zaokraglen($invoice);
	handle_invoice_generation($cart_order_id,$invoice);
}else if( can_process_request_for_pro_forma_generation($cart_order_id) ){

}else{
	throw new Exception('not authorized to perform operation');

}
}
?>