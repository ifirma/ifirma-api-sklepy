<?php
//na serwerze produkcyjnym usunac [/opencart]
require_once (realpath(getcwd().'/../../../../config.php'));
require_once(DIR_SYSTEM . 'startup.php');

require_once("ifirma/BuilderClasses.php");
require_once("ifirma/ifirma_functions.php");

$API_CONFIG_FILE_PATH='config.ini';
$has_form_error = false;
$registry = new Registry();
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('session', new Session());
if($registry->get('session')->data['token']!=$_GET['token']){
	echo "WystÄ…piÅ‚ problem. Zaloguj siÄ™ ponownie.";
}else{
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
<p class="pageTitle">Do korzystania z serwisu ifirma nale¿y wprowadziæ poni¿sza konfiguracje</p>
<?php if( $has_form_error ): ?>
<div class="error">Niepoprawnie wype³niony formularz. Wszystkie pola sa obowi¹zkowe</div>
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
}

 ?>
