<?php
function is_api_update_request(){
	return  $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['api_key_update']) === true;
}
function has_required_fields_filled(){
	return post_isset_and_not_empty('API_KEY_ABONENT') && post_isset_and_not_empty('API_KEY_FAKTURA') && post_isset_and_not_empty('API_LOGIN');
}
function post_isset_and_not_empty($key){
	return isset( $_POST[$key] ) && !empty( $_POST[$key]);
}
function write_php_ini($fileName, $array){
	$res = array();
	foreach($array as $key => $val){
		if(is_array($val)){
			$res[] = "[$key]";
			foreach($val as $skey => $sval){
				$res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
			}
		}else{
			$res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
		}
	}
	safefilerewrite($fileName, implode("\r\n", $res));
}
function safefilerewrite($fileName, $dataToSave){
	if ($fp = fopen($fileName, 'w')){
		$startTime = microtime();
		do{
			$canWrite = flock($fp, LOCK_EX);
			// If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
			if(!$canWrite){
				usleep(round(rand(0, 100)*1000));
			}
		}while ((!$canWrite)and((microtime()-$startTime) < 1000));
		if ($canWrite){
			fwrite($fp, $dataToSave);
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
}
function mySQLSafe($value, $quote="'", $stripslashes = true) { 
		
		// strip quotes if already in
		$value = str_replace(array("\'","'"),"&#39;",$value);
		
		// Stripslashes 
		if (get_magic_quotes_gpc() && $stripslashes) { 
			$value = stripslashes($value); 
		} 
		// Quote value
		if(version_compare(phpversion(),"4.3.0")=="-1") {
			$value = mysql_escape_string($value);
		} else {
			$value = mysql_real_escape_string($value);
		}
		$value = $quote . trim($value) . $quote; 
	 
		return $value; 
	}
function get_api_connection_parameters($api_config_file_path){
	$config = parse_ini_file($api_config_file_path);
	return $config;
}
function get_api_config_value_from_context($key,$API_CONFIG_FILE_PATH){
	$api_connection_parameters = get_api_connection_parameters($API_CONFIG_FILE_PATH);
	return isset( $_POST['api_key_update'] ) ? $_POST[$key] :  $api_connection_parameters[$key];
}

function get_invoice_number_if_available($cart_order_id){
	global $db;
	$query = $db->query("select * from ifirma_invoice_map where document_type = 'invoice' and cart_order_id = ".mySQLSafe($cart_order_id));	
	return $query->row;
}
function can_process_request_for_invoice_generation($cart_order_id){
	if( $_GET['type'] == 'invoice' ){
		$results = get_invoice_number_if_available($cart_order_id);
		if( $results === false || $results == null){
			return true;
		}
	}
	return false;
}

function get_pro_forma_number_if_available($cart_order_id){
	global $db;
	$query = $db->query("select * from ifirma_invoice_map where document_type = 'pro_forma' and cart_order_id = ".mySQLSafe($cart_order_id));
	return $query->row;
}
function can_process_request_for_pro_forma_generation($cart_order_id){
	if( $_GET['type'] == 'pro_forma' ){
		$results = get_pro_forma_number_if_available($cart_order_id);
		if( $results === false ){
			return true;
		}
	}
	return false;
}


function get_order_summary($cart_order_id){
	global $db;
	$query = $db->query("SELECT * FROM `".DB_PREFIX."order` WHERE order_id=".mySQLSafe($cart_order_id));
	return $query->row;	
}
function get_order_data($cart_order_id){
	global $db;
	$query = $db->query("SELECT * FROM `".DB_PREFIX."order` WHERE order_id=".mySQLSafe($cart_order_id));
	return $query->row;
	
}
function get_order_ship($cart_order_id){
	global $db;
	$query = $db->query("SELECT * `FROM ".DB_PREFIX."order` WHERE order_id=".mySQLSafe($cart_order_id));
	return $query->row;
}
function verify_order_exists($order_summary){
	if( ! isset($order_summary) ){
		throw new Exception("no such order");
	}
}

function get_order_details($cart_order_id){
	global $db;
	$query = $db->query("SELECT * FROM `".DB_PREFIX."order_product` WHERE order_id=".mySQLSafe($cart_order_id));
	return $query->rows;
}
function get_customer($id){
	$sql = "SELECT * FROM `".DB_PREFIX."customer` WHERE customer_id=".mySQLSafe($id);
	
	$res = DB::getInstance()->ExecuteS($sql);
	return $res[0];
}
function get_customer_invoice_address($id){
	$sql = "SELECT * FROM ".DB_PREFIX."address WHERE address_id=".mySQLSafe($id);
	
	$res = DB::getInstance()->ExecuteS($sql);
	return $res[0];
}
function get_order_customer($order_summary){
	//$cust_summary = get_customer($order_summary['customer_id']);
	//$cust_addr = get_customer_invoice_address($order_summary['id_address_invoice']);
	$name = !empty( $order_summary['payment_company'] ) ?  $order_summary['payment_company']  : $order_summary['payment_lastname']." ".$order_summary['payment_firstname']; 
	$customer = new KontrahentFlyweightBuilder($name,$order_summary['payment_postcode'],$order_summary['payment_city']);
//	if( ! empty( $order_summary['vat_number']) ){
//		$customer->NIP($order_summary['vat_number']);
//	}
	if( ! empty( $order_summary['payment_address1']) ){
		$address = $order_summary['payment_address1'];
		if( ! empty( $order_summary['payment_address2']) ){
		$address .= ' ' . $order_summary['payment_address2'];
		}
		
		$customer->Ulica($address);
	}
	if( ! empty( $order_summary['telephone']) ){
		$customer->Telefon($order_summary['telephone']);
	}
	
	if(!($order_summary['payment_firstname'] != $order_summary['payment_firstname'] || $order_summary['payment_lastname'] != $order_summary['payment_lastname'])){
		if( ! empty( $order_summary['customer_id']) ){
			$customer->Identyfikator('IFI'.$order_summary['customer_id']);
		}
	}
	return $customer;
}
function get_invoice($order_summary, $customer,$wysylka){
	$payment_type = get_payment_type($order_summary);
	$is_gios_visible = false;
	$next_invoice_number = null;
	$total = round($order_summary['total'],2);
	$invoice = null;
	if ( $wysylka == true){
		$invoice = new FakturaWysylkowa($total,$customer->getIdentyfikator(),'BRT',date('Y-m-d'),'DZN','BPO',$is_gios_visible, $next_invoice_number,date('Y-m-d'),$customer);
	}else{
		$invoice = new Faktura($total,$customer->getIdentyfikator(),'BRT',date('Y-m-d'),'DZN',$payment_type,'BPO',$is_gios_visible, $next_invoice_number,date('Y-m-d'),$customer);
	}
	if( $payment_type == 'PRZ'){
		$invoice->NumerKontaBankowego("11111111111111111111111111");
	}
	
	return $invoice;
}

function get_payment_type($order_summary){
	

		$type = 'GTK';
	
	return $type;
//	return $order_summary['gateway'] == 'Dotpay' ? 'PRZ' : 'GTK';
}
function has_ship($order_summary){
	if( ! empty( $order_summary['shipping_method'] )  && $order_summary['shipping_method']!= ''){
		return true;
	}
	return false;
}

function get_pro_forma($order_summary, $customer){
	$is_gios_visible = false;
	$next_invoice_number = null;
	$invoice = new Proforma($customer->getIdentyfikator(),'BRT','SPRZ',date('Y-m-d'),'PRZ','BPO',$is_gios_visible,$next_invoice_number, $customer );
	$invoice->NumerKontaBankowego("11111111111111111111111111");
	return $invoice;
}

function add_invoice_positions($invoice, $order_details){
	foreach ( $order_details as $pos ){	
		$price_x = $pos['price'];
		$price_r = round($price_x,2);
		$price = $price_r + ($price_r * ($pos['tax']/100));
		//if (isset($pos['product_quantity_discount']) && $pos['product_quantity_discount'] != 0){ $price = $pos['product_quantity_discount'];}
		$unit = get_unit($pos['name'],$pos['quantity']);
		$invoice_position = new PozycjaFaktury(percentToFloat($pos['tax']),$pos['quantity'], $price,$pos['name'],$unit,'PRC');
		$invoice->dodajPozycjeFaktury($invoice_position);
	}
}
function get_unit($productCode,$quantity){
	$unit = 'szt.'; 
	if ( strcasecmp(substr($productCode,0,4),'BUTD') == 0  || 
		strcasecmp(substr($productCode,0,4),'BUTZ') == 0  ||
		strcasecmp( $productCode,'REK') == 0  ){
		$unit = $quantity > 1 ? 'pary' : 'para';
	}
	return $unit;
}
//@TODO
function add_invoice_ship_position($invoice,$order_summary){
	global $db;
	if( ! empty( $order_summary['shipping_method'] )  && $order_summary['shipping_method']!= ''){
		
		$query = $db->query("SELECT * FROM `".DB_PREFIX."order_total` WHERE order_id = ".mySQLSafe($order_summary['order_id'])." AND title LIKE '".$order_summary['shipping_method']."'");
		
		//$query = $db->query("SELECT * FROM `".DB_PREFIX."order_total` WHERE order_id = ".mySQLSafe($order_summary['order_id'])." AND title LIKE 'Flat Rate'");

		$val= $query->row['value'];
		
		$total_ship = round((floatval($val) * 123 / 100), 2 );
		//$total_ship = round($val,2);
		$invoice_position = new PozycjaFaktury(0.23,1,$total_ship,$order_summary['shipping_method'],'usl.','PRC');
		$invoice->dodajPozycjeFaktury($invoice_position);
	}
}
function add_invoice_note($invoice,$cart_order_id){
	$invoice->Uwagi("Zamowienie numer: ".$cart_order_id);
}
function percentToFloat($percent){
	return empty($percent) ? 0 : $percent / 100;
}
function get_invoice_url($invoice){
	$connection_parameters = get_api_connection_parameters("./config.ini");
	$klucz = $connection_parameters['API_KEY_FAKTURA'];
	$url = "https://www.ifirma.pl/iapi/fakturakraj.json";
	$nazwaUsera = $connection_parameters['API_LOGIN'];
	$nazwaKlucza = "faktura";
	$curlHandle = curl_init($url);
	$requestContent = $invoice->toJson();

	$hashWiadomosci = hmac($klucz,$url.$nazwaUsera.$nazwaKlucza.$requestContent);

	$headers=array(
		'Accept: application/json',
		'Content-type: application/json; charset=UTF-8',
		'Authentication: IAPIS user='.$nazwaUsera.', hmac-sha1='.$hashWiadomosci);
	curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
	curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT,100);
	curl_setopt($curlHandle, CURLOPT_URL, $url);
	curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curlHandle, CURLOPT_HTTPGET, false);
	curl_setopt($curlHandle, CURLOPT_POST, true);
	curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $requestContent);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,0);
	return curl_exec($curlHandle);
}

function handle_pro_forma_generation($cart_order_id,$invoice){
	$connection_parameters = get_api_connection_parameters("./config.ini");
	$nazwaUsera = $connection_parameters['API_LOGIN']; //login do ifirma
	$klucz_hex =  $connection_parameters['API_KEY_FAKTURA']; //klucz wygenerowany w ifirmie

	$typ_faktury = 'oryg'; //typ faktury w pdfie: np.dup, kopia, oryg, orygkopia
	$nazwaPliku = "./upload/faktura.json";
	$nazwaKlucza = "faktura";
	$typ_pliku = "json"; //rodzaj pliku, ktory wysylamy
	$typ_pobierz = "pdf"; //oczekiwana odpowiedz
	$url = "https://www.ifirma.pl/iapi/fakturaproformakraj.json";
	$url_get = "https://www.ifirma.pl/iapi/fakturakraj/";
	$sciezka = './';
	
	handle_document_generation($cart_order_id,$invoice, $url,'pro_forma' );
}

function handle_invoice_generation($cart_order_id,$invoice){	
	$url = "https://www.ifirma.pl/iapi/fakturakraj.json";
	if( "FakturaWysylkowa" == get_class($invoice)){
		$url = "https://www.ifirma.pl/iapi/fakturawysylka.json";
	}
	handle_document_generation($cart_order_id,$invoice, $url,'invoice' );
}
function faktura_stworzona($cart_order_id){
	global $db;
	$query = $db->query("select count(*) as ile from ifirma_invoice_map where document_type = 'invoice' and cart_order_id = '$cart_order_id'");
	
	return $query->row['ile'];
}
function add_info_about_generated_document($document_type,$cart_order_id,$invoice_number,$invoice_type){
	global $db;
	$db->query("insert into ifirma_invoice_map(document_type,cart_order_id,invoice_number,invoice_type)  values('$document_type','$cart_order_id','$invoice_number','$invoice_type')" );
	
}
function add_info_about_invoice_fault($cart_order_id){
	global $db;
	$query = $db->query("update ifirma_invoice_map set correction_needed = 1 where cart_order_id = '$cart_order_id'");
}
function add_info_about_invoice_fault_resolved($cart_order_id){
	global $db;
	$query = $db->query("update ifirma_invoice_map set correction_done = 1 where cart_order_id = '$cart_order_id'");
}
function needs_attention($cart_order_id){
	global $db;
	$query = $db->query("select count(*) as ile from ifirma_invoice_map where correction_needed = 1 and correction_done = 0 and cart_order_id = '$cart_order_id'");
	return $query->row['ile'];
}
function is_response_status_ok($response_status){
	return $response_status === 0;
}
function display_errors($errors){
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="pl-PL">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<body>';
	foreach ($errors as $message){
		echo 'Faktura na kwote <b>'.$message['kwota'].'</b> dla kontrahenta '.$message['nazwaKontrahenta'].' : '.$message['info'].'<br />';
	}
	echo '</body></html>';
}
function redirect_to_order_page($cart_order_id){
	$adminwd = getcwd();
	$admindir = strrpos($adminwd,'/');
	$admindir = substr($adminwd, $admindir);
	$url = 'Location: '.$_SERVER['HTTP_REFERER'];
	
	header( $url );
}
function weryfikuj_fakture_pod_katem_zgodnosci_zaokraglen($invoice){
//	echo "<pre>";
//	var_dump($invoice);
//	echo "</pre>";
	$faktura_sum = 0;
	foreach ($invoice->getPozycje() as $pos){
		$faktura_sum += $pos['CenaJednostkowa'] * $pos['Ilosc'];

	}
//	echo "Faktura sum: " . $faktura_sum . "Faktura org: " . $invoice->getZaplacono();
	//$faktura_sum += 1.0;
	if(round($faktura_sum,2) != $invoice->getZaplacono())
	{
//		echo " zmieniam";
		$invoice->setZaplacono(round($faktura_sum,2));
//		echo "po zmianie: " . $invoice->getZaplacono();
	}
}
function handle_document_generation($cart_order_id,$invoice, $url,$document_type ){
	$connection_parameters = get_api_connection_parameters("./config.ini");
	$nazwaUsera = $connection_parameters['API_LOGIN']; //login do ifirma
	$klucz_hex =  $connection_parameters['API_KEY_FAKTURA']; //klucz wygenerowany w ifirmie

	//$typ_faktury = 'orygkopia'; //typ faktury w pdfie: np.dup, kopia, oryg, orygkopia
	$nazwaKlucza = "faktura";
	$typ_pliku = "json"; //rodzaj pliku, ktory wysylamy
	$typ_pobierz = "pdf"; //oczekiwana odpowiedz	
	$url_get = "https://www.ifirma.pl/iapi/fakturakraj/";

	$klucz = hexToStr($klucz_hex);
	$exception = false;
	$bledy = array();
	$content = $invoice->toJson();
	$curlWysylanieHandle = curl_init($url);
	$faktura_do_poprawki = false;
	ustaw_odpowiedni_miesiac_ksiegowy($invoice,$connection_parameters); 
	$rsp = wyslij_jedna($typ_pliku,$content,$klucz,$url,$nazwaUsera,$nazwaKlucza,$curlWysylanieHandle);
	
	$tab = json_decode($rsp,true);
	if(is_response_status_ok( $tab['response']['Kod'] ))
	{
		$url_pliku = '';
		$invoice_number = $tab['response']['Identyfikator'];
		if (isset($typ_faktury)){
			$url_pliku = $url_get.$invoice_number.'.'.$typ_pobierz.'.'.$typ_faktury;
		}else{
			$url_pliku = $url_get.$invoice_number.'.'.$typ_pobierz;
		}
		$curlHandle = curl_init($url_pliku);
		curl_close($curlHandle);
		unset($curlHandle);
		add_info_about_generated_document($document_type,$cart_order_id,$invoice_number,get_class($invoice));
		//-------
		$generated_invoice_array = handle_invoice_download($cart_order_id, "json");
	
		$invdetails = $invoice->getZaplacono();
		$invpozycje = $invoice->getPozycje();

		if($generated_invoice_array['response']['Zaplacono'] != $invdetails) $faktura_do_poprawki = true;
		$l = 0;
		foreach($generated_invoice_array['response']['Pozycje'] as $pozycja){
			$vatDwa = (float)$invpozycje[$l]['StawkaVat'];//->getStawkaVat();
 			
 			$cenaDwa = (float)$invpozycje[$l]['CenaJednostkowa'];//->getCenaJednostkowa();
			$vatRaz = (float)$pozycja['StawkaVat'];
			$cenaRaz = (float)$pozycja['CenaJednostkowa'];
			++$l;
			if($vatRaz!=$vatDwa || $cenaRaz!=$cenaDwa) $faktura_do_poprawki = true;
			
		}
		
		if ($faktura_do_poprawki == true) {
			add_info_about_invoice_fault($cart_order_id);
			echo "Wykryto drobne nieprawidlowosci.<br/> Mo≈ºliwa przyczyna: -r√≥≈ºnica w sposobie zaokrƒÖglania kwoty podatku.<br />";
			echo "Proszƒô dokonaƒá weryfikacji poprawno≈õci wystawionej faktury z poziomu serwisu www.ifirma.pl";
	}
	//-----------
	}else{
		$bledy = przechwyc_bledy($content,$tab,$bledy);
	}
	curl_close($curlWysylanieHandle);
	unset($curlWysylanieHandle);

	if( count( $bledy ) > 0 ){
		display_errors($bledy);		
	}else{
		redirect_to_order_page($cart_order_id);
	}
}

function ustaw_odpowiedni_miesiac_ksiegowy($invoice,$connection_parameters){
	$nazwaUsera = $connection_parameters['API_LOGIN'];
	$nazwaKluczaAbonenckiego = "abonent";
	$typ_pliku = "json"; //rodzaj pliku, ktory wysylamy
	
	$url_msc_ksiegowy = "https://www.ifirma.pl/iapi/abonent/miesiacksiegowy.json";
	$url_msc_ksiegowy_get = "https://www.ifirma.pl/iapi/abonent/miesiacksiegowy/";

	$curlHandle = curl_init($url_msc_ksiegowy_get);
	$kluczMscKs = hexToStr($connection_parameters['API_KEY_ABONENT']);
	$biezacy_okres_ksiegowy = pobierz_miesiac($kluczMscKs,$url_msc_ksiegowy,$nazwaUsera,$nazwaKluczaAbonenckiego,$curlHandle);
	$rok = date('Y',strtotime($invoice->getDataWystawienia()));
	$miesiac = date('m',strtotime($invoice->getDataWystawienia()));
	$roznica_miesiecy = ( intval($rok*1) - $biezacy_okres_ksiegowy['rok']*1) * 12 
		+ ($miesiac*1 - $biezacy_okres_ksiegowy['miesiac']*1);
	$msc_ksiegowy_content = "";
	if( $roznica_miesiecy > 0 ){
		$msc_ksiegowy_content = '{"MiesiacKsiegowy":"NAST","PrzeniesDaneZPoprzedniegoRoku":true}';
	}else if( $roznica_miesiecy < 0){
		$msc_ksiegowy_content= '{"MiesiacKsiegowy":"POPRZ","PrzeniesDaneZPoprzedniegoRoku":true}';
	}	
	while( $roznica_miesiecy > 0 ){
		wyslij_zadanie_put("json",$msc_ksiegowy_content,$kluczMscKs,$url_msc_ksiegowy,$nazwaUsera,$nazwaKluczaAbonenckiego,$curlHandle);
		--$roznica_miesiecy;
	}
	while( $roznica_miesiecy < 0 ){
		wyslij_zadanie_put("json",$msc_ksiegowy_content,$kluczMscKs,$url_msc_ksiegowy,$nazwaUsera,$nazwaKluczaAbonenckiego,$curlHandle);
		++$roznica_miesiecy;
	}
	curl_close($curlHandle);
    unset($curlHandle);
}

function handle_document_download($cart_order_id,$url_get,$invoice_number,$typ_pobierz = "pdf"){
	global $db,$config;
	$connection_parameters = get_api_connection_parameters("./config.ini");
	$nazwaUsera = $connection_parameters['API_LOGIN']; //login do ifirma
	$klucz_hex =  $connection_parameters['API_KEY_FAKTURA']; //klucz wygenerowany w ifirmie

	$nazwaKlucza = "faktura";
	
	
	$klucz = hexToStr($klucz_hex);
	$url_pliku = '';
	
	if (isset($typ_faktury)){
		$url_pliku = $url_get.$invoice_number.'.'.$typ_pobierz.'.'.$typ_faktury;
	}else{
		$url_pliku = $url_get.$invoice_number.'.'.$typ_pobierz;
	}
	$curlHandle = curl_init($url_pliku);
	$tresc = pobierz_plik($klucz,$url_pliku,$nazwaUsera,$nazwaKlucza,$typ_pobierz,$invoice_number,$curlHandle);
	$nazwa = $nazwaKlucza."_".$nazwaUsera."_".$invoice_number.'.'.$typ_pobierz;
	curl_close($curlHandle);
	unset($curlHandle);
	$tresc_decoded = json_decode($tresc, true);
	if ($typ_pobierz == "json"){
		return $tresc_decoded;
	}else{
	sciagnij_plik($typ_pobierz,$nazwa,$tresc);
	}
	
}
function handle_invoice_download($cart_order_id,$typ_pobierz = "pdf"){
	$url = "https://www.ifirma.pl/iapi/fakturakraj/";
	$row = get_invoice_number_if_available($cart_order_id);
	if( $row['invoice_type'] == "FakturaWysylkowa" ){
		$url = "https://www.ifirma.pl/iapi/fakturawysylka/";
	}
	$invoice_number = $row['invoice_number'];
	if ($typ_pobierz == "json"){
		return handle_document_download($cart_order_id,$url,$invoice_number,$typ_pobierz);
	}else{
	handle_document_download($cart_order_id,$url,$invoice_number,$typ_pobierz);
	}
}

function handle_pro_forma_download($cart_order_id){
	$row = get_pro_forma_number_if_available($cart_order_id);
	$invoice_number = $row['invoice_number'];
	$url = "https://www.ifirma.pl/iapi/fakturaproformakraj/";
	handle_document_download($cart_order_id,$url,$invoice_number);
}



function hmac($key,$data) {
	$blocksize=64;
	$hashfunc='sha1';
	if (strlen($key)>$blocksize)
	$key=pack('H*', $hashfunc($key));
	$key=str_pad($key,$blocksize,chr(0x00));
	$ipad=str_repeat(chr(0x36),$blocksize);
	$opad=str_repeat(chr(0x5c),$blocksize);
	$hmac = pack('H*',$hashfunc(($key^$opad).pack('H*',$hashfunc(($key^$ipad).$data))));
	return bin2hex($hmac);
}

function hexToStr($hex)
{
	$string='';
	for ($i=0; $i < strlen($hex)-1; $i+=2)
	{
		$string .= chr(hexdec($hex[$i].$hex[$i+1]));
	}
	return $string;
}

function wyslij_jedna($typ_pliku,$content,$klucz,$url,$nazwaUsera,$nazwaKlucza,$curlHandle)
{

	$hashWiadomosci = hmac($klucz,$url.$nazwaUsera.$nazwaKlucza.$content);
	$headers=array(
        'Accept: application/'.$typ_pliku,
        'Content-type: application/'.$typ_pliku.'; charset=UTF-8',
        'Authentication: IAPIS user='.$nazwaUsera.', hmac-sha1='.$hashWiadomosci
	);

	curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
	curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT,100);
	curl_setopt($curlHandle, CURLOPT_URL, $url);
	curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curlHandle, CURLOPT_HTTPGET, false);
	curl_setopt($curlHandle, CURLOPT_POST, true);
	curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $content);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,0);

	$rsp = curl_exec($curlHandle);
	return $rsp;
}

function wyslij_zadanie_put($typ_pliku,$requestContent,$klucz,$url,$nazwaUsera,$nazwaKlucza,$curlHandle)
{    
	 
    $hashWiadomosci = hmac($klucz,$url.$nazwaUsera.$nazwaKlucza.$requestContent);
    $headers=array(
        'Accept: application/'.$typ_pliku,
        'Content-type: application/'.$typ_pliku.'; charset=UTF-8',
        'Authentication: IAPIS user='.$nazwaUsera.', hmac-sha1='.$hashWiadomosci
    );
    
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT,100);
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curlHandle, CURLOPT_HTTPGET, false);
	curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PUT');     
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $requestContent);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,0);
        
    $rsp = curl_exec($curlHandle);
    return $rsp;
}

function pobierz_plik($klucz,$url,$nazwaUsera,$nazwaKlucza,$typ,$numer,$curlHandle)
{
	global $sciezka_www_tmp;
	$hashWiadomosci = hmac($klucz,$url.$nazwaUsera.$nazwaKlucza);
	$headers=array(
        'Accept: application/'.$typ,
        'Content-type: application/'.$typ.'; charset=UTF-8',
        'Authentication: IAPIS user='.$nazwaUsera.', hmac-sha1='.$hashWiadomosci
	);
	curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
	curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT,100);
	curl_setopt($curlHandle, CURLOPT_URL, $url);
	curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,0);
	$rsp = curl_exec($curlHandle);
	return $rsp;
}

function sciagnij_plik($typ,$nazwa,$rsp)
{
	header('Content-Type: application/'.$typ);
	header('Content-disposition: attachment; filename='.$nazwa);
	echo $rsp;
}
function przechwyc_bledy($json,$tab,$bledy)
{
	$content = json_decode($json, true);
	$ta_faktura = array(
		'nazwaKontrahenta' => $content["Kontrahent"]["Nazwa"],
		'kwota' => $content["Zaplacono"],
		'info' => $tab['response']['Informacja']
	);

	$bledy[] = $ta_faktura;
	return $bledy;
}

function pobierz_miesiac_i_rok_ksiegowy($klucz,$url,$nazwaUsera,$nazwaKlucza,$curlHandle)
{
    $hashWiadomosci = hmac($klucz,$url.$nazwaUsera.$nazwaKlucza);
    $typ = "json";
    $headers=array(
        'Accept: application/'.$typ,
        'Content-type: application/'.$typ.'; charset=UTF-8',
        'Authentication: IAPIS user='.$nazwaUsera.', hmac-sha1='.$hashWiadomosci
    );
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT,100);
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,0);    
    $rsp = curl_exec($curlHandle);
    
    $tab = json_decode($rsp,true);
    
    if ($tab['response']['Kod'] == 0)
	    return array( 'miesiac' => $tab['response']['MiesiacKsiegowy'], 'rok' => $tab['response']['RokKsiegowy']);
	    
	throw new Exception("Wyst√Ñ‚Ä¶pi√Ö‚Äö problem z po√Ö‚Äö√Ñ‚Ä¶czeniem z ifrm√Ñ‚Ä¶. Przyczyna: ".$tab['response']['Informacja']);
}


function pobierz_miesiac($klucz,$url,$nazwaUsera,$nazwaKlucza,$curlHandle)
{
    $hashWiadomosci = hmac($klucz,$url.$nazwaUsera.$nazwaKlucza);
    $typ = "json";
    $headers=array(
        'Accept: application/'.$typ,
        'Content-type: application/'.$typ.'; charset=UTF-8',
        'Authentication: IAPIS user='.$nazwaUsera.', hmac-sha1='.$hashWiadomosci
    );
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT,100);
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,0);    
    $rsp = curl_exec($curlHandle);
    $tab = json_decode($rsp,true);
   
    if (!isset($tab['response']['Kod]'])||$tab['response']['Kod'] == 0)
	    return array( 'miesiac' => $tab['response']['MiesiacKsiegowy'], 'rok' => $tab['response']['RokKsiegowy']);
	    
	throw new Exception("Wyst√Ñ‚Ä¶pi√Ö‚Äö problem z po√Ö‚Äö√Ñ‚Ä¶czeniem z ifrm√Ñ‚Ä¶. Przyczyna: ".$tab['response']['Informacja']);
}
?>
