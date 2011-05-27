<?php
require_once ('includes/application_top.php');
require_once("includes/functions/database.php");
require_once("ifirma/BuilderClasses.php");
require_once("ifirma/ifirma_functions.php");
$cart_order_id = $_GET['cart_order_id'];

handle_invoice_download($cart_order_id);

require(DIR_WS_INCLUDES . 'application_bottom.php');
?>