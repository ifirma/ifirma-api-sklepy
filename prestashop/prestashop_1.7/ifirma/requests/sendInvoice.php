<?php

define('_PS_ADMIN_DIR_', getcwd());
require_once (_PS_ADMIN_DIR_.'/../../../config/config.inc.php');
require_once dirname(__FILE__) . '/../manager/ApiManager.php';
require_once dirname(__FILE__) . '/../manager/InternalComunicationManager.php';

if(!\ifirma\ApiManager::getInstance()->checkIfirmaHash()){
	Tools::redirectAdmin('../../../');
}

$id = Tools::getValue('id');
$type = Tools::getValue('type');

$sendResult = \ifirma\ApiManager::getInstance()->sendInvoice($id, $type);

ifirma\InternalComunicationManager::getInstance()->{ifirma\InternalComunicationManager::KEY_SEND_RESULT} = $sendResult;

header('Location: '.$_SERVER['HTTP_REFERER']);
?>
