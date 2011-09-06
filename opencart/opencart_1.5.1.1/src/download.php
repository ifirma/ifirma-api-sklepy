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
handle_invoice_download($cart_order_id);
}
?>