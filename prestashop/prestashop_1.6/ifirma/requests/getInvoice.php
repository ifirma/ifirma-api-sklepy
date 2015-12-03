<?php

define('_PS_ADMIN_DIR_', getcwd());
require_once (_PS_ADMIN_DIR_.'/../../../config/config.inc.php');
require_once dirname(__FILE__) . '/../manager/ApiManager.php';

if(!\ifirma\ApiManager::getInstance()->checkIfirmaHash()){
	 Tools::redirectAdmin('../../../');
}

$id = Tools::getValue('id');

header('Content-Type: application/pdf');
header('Content-disposition: attachment; filename="'.\ifirma\ApiManager::getInstance()->getDocumentPdfName($id).'"');
echo \ifirma\ApiManager::getInstance()->getDocumentAsPdf($id);
?>
