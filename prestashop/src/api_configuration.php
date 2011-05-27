<?php
//error_reporting(E_ALL);
define('_PS_ADMIN_DIR_', getcwd());
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility

include(PS_ADMIN_DIR.'/../config/config.inc.php');
include(PS_ADMIN_DIR.'/functions.php');
include(PS_ADMIN_DIR.'/header.inc.php');
require_once("ifirma/BuilderClasses.php");
require_once("ifirma/ifirma_functions.php");
if (!class_exists('Cookie'))
	exit();

$cookie = new Cookie('psAdmin', substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__), -10));
if (!$cookie->isLoggedBack())
	die;
$API_CONFIG_FILE_PATH='config.ini';
$has_form_error = false;
if( is_api_update_request() ){
	if( has_required_fields_filled() ){
		$parameters = array('API_KEY_FAKTURA' => $_POST['API_KEY_FAKTURA'], 'API_LOGIN' => $_POST['API_LOGIN'], 'API_KEY_ABONENT' => $_POST['API_KEY_ABONENT']);
		write_php_ini($API_CONFIG_FILE_PATH,$parameters);
	}else{
		$has_form_error = true;
	}	
}
?>
<style type="text/css">
.formRow label {
font-family: tahoma, helvetica, sans-serif;
font-style: normal;
font-size: 14px;
color: #0B70CE;
width: 200px;
display:block;
}

/** You can use this style for your LABEL elements **/
.formRow input.text {
font-family: tahoma, helvetica, sans-serif;
font-style: bold;
font-size: 13px;
color: #82983e;
}
.error{
	background-color:#F9F6C1;
	border:1px solid #FFCC66;
	margin:0 0 10px;
	padding:5px 10px;
	color: #CC0000;
}
</style>
<p class="pageTitle">Do korzystania z serwisu ifirma nalezy wprowadzic ponizsza konfiguracje</p>
<?php if( $has_form_error ): ?>
<div class="error">Niepoprawnie wypelniony formularz. Wszystkie pola sa obowiazkowe</div>
<?php endif; ?>
<p>
	<span class="copyText">
	</span>
	<form action="" method="post">
		<div class="formRow">
			<label for="apiKey">Klucz do API - faktura:</label><input id="apiKey" type="text" name="API_KEY_FAKTURA" value="<?php echo get_api_config_value_from_context('API_KEY_FAKTURA',$API_CONFIG_FILE_PATH); ?>" class="text"/>
		</div>
		<div class="formRow">
			<label for="apiKey">Klucz do API - abonent:</label><input id="apiKey" type="text" name="API_KEY_ABONENT" value="<?php echo get_api_config_value_from_context('API_KEY_ABONENT',$API_CONFIG_FILE_PATH); ?>" class="text"/>
		</div>
		<div class="formRow">

			<label for="apiLogin">Login do API:</label><input id="apiLogin" type="text" name="API_LOGIN" value="<?php echo get_api_config_value_from_context('API_LOGIN',$API_CONFIG_FILE_PATH);?>" class="text"/>
		</div>
		
		<div class="formRow">
			<input type="submit" value="Zapisz parametry" name="api_key_update" />
			<input type="submit" value="Anuluj zmiany" name="api_key_cancel"/>
		</div>
	</form>
</p>
<?php 
include('footer.inc.php');

 ?>