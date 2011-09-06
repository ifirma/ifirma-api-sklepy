<?php
abstract class BaseFlyweightBuilder{
	protected $fields = array();
	protected $details=array();

	public function __call($method,$args){
		$this->verifyValidFunctionCall($method,$args);
		$this->details[$method] = $this->toUtf8($args[0]);
	}
	protected function verifyValidFunctionCall($method,$args){
		$this->verifyValidMethodCall($method);
		$this->verifyThatHasOnlyOneArgument($args);
	}
	protected function hasField($field){
		return in_array($field,$this->fields);
	}
	protected function verifyValidMethodCall($method){
		if(! $this->hasField($method) ){
			throw new Exception('unsupported field' + $method);
		}
	}
	protected function verifyThatHasOnlyOneArgument($args){
		if( count($args) != 1 ){
			throw new Exception('invalid number of parameters');
		}
	}
	public function __toString(){
		print_r($this->details);
	}
	public function toJson(){
		return json_encode($this->details);
	}
	public function getDetails(){
		return $this->details;
	}
	public function toUtf8($string){
		//return iconv('ISO-8859-2','UTF-8//TRANSLIT//IGNORE', $string);
		return $string;
	}
}

class KontrahentFlyweightBuilder extends BaseFlyweightBuilder{
	protected $fields = array('Identyfikator','PrefiksUE','NIP','Ulica','Kraj','Email','Telefon');
	public function __construct($nazwa,$kod,$miejscowosc){
		$this->details['Nazwa'] = $this->toUtf8($nazwa);
		$this->details['KodPocztowy'] = $kod;
		$this->details['Miejscowosc'] = $this->toUtf8($miejscowosc);
	}
	public function getNIP(){
		return $this->details['NIP'];
	}
	public function getIdentyfikator(){
		return $this->details['Identyfikator'];
	}

}
class PozycjaFaktury extends BaseFlyweightBuilder{
	protected $fields = array('PKWiU');
	public function __construct($stawkaVAT,$ilosc,$cenaJednostkowa, $nazwaPelna,$jednostka,$typStawkiVat,$rabatx){
		$this->details['StawkaVat'] = $this->toUtf8($stawkaVAT);
		$this->details['Ilosc'] = $ilosc;
		$this->details['CenaJednostkowa'] = $cenaJednostkowa;
		$this->details['NazwaPelna'] = $this->toUtf8($nazwaPelna);
		$this->details['Jednostka'] = $this->toUtf8($jednostka);
		$this->details['TypStawkiVat'] = $typStawkiVat;
		$this->details['Rabat'] = $rabatx;
	}
	public function getCenaJednostkowa(){
		return $this->details['CenaJednostkowa'];
	}
	public function getStawkaVat(){
		return $this->details['StawkaVat'];
	}
}
class Faktura extends BaseFlyweightBuilder{
	protected $fields = array('NumerKontaBankowego','MiejsceWystawienia','TerminPlatnosci','NazwaSeriiNumeracji',
		'NazwaSzablonu','PodpisOdbiorcy','PodpisWystawcy','Uwagi');
	public function __construct($zaplacono,$identyfikatorKontrahenta,$liczOd,$dataWystawienia, $formatDatySprzedazy,$sposobZaplaty,
	$rodzajPodpisuOdiorcy,$widocznyNumerGios, $numer,$dataSprzedazy, $kontrahent ){
		$this->details['Zaplacono'] = $zaplacono;
		$this->details['IdentyfikatorKontrahenta'] = $identyfikatorKontrahenta;
		$this->details['LiczOd'] = $liczOd;
		$this->details['DataWystawienia'] = $dataWystawienia;
		$this->details['FormatDatySprzedazy'] = $formatDatySprzedazy;
		$this->details['SposobZaplaty'] = $this->toUtf8($sposobZaplaty);
		$this->details['RodzajPodpisuOdbiorcy'] = $rodzajPodpisuOdiorcy;
		$this->details['WidocznyNumerGios'] = $widocznyNumerGios;
		$this->details['Numer'] = $numer;
		$this->details['DataSprzedazy'] = $dataSprzedazy;
		$this->details['Kontrahent'] = $kontrahent->getDetails();
		$this->details['Pozycje'] = array();
		$nip = $kontrahent->getNIP();
		if( !empty($nip) ){
			$this->details['NIPKontrahenta']= $kontrahent->getNIP();
		} 
		
	}
	public function dodajPozycjeFaktury($nowaPozycja){
		$this->details['Pozycje'][] = $nowaPozycja->getDetails();
	}
	public function getDataWystawienia(){
		return $this->details['DataWystawienia'];
	}
	public function getZaplacono(){
		return $this->details['Zaplacono'];
	}
	public function setZaplacono($nowa_kwota){
		$this->details['Zaplacono'] = $nowa_kwota;
	}
	public function getPozycje(){
		return $this->details['Pozycje'];
	}
	
}

class Proforma extends BaseFlyweightBuilder{
	protected $fields = array('NumerKontaBankowego','MiejsceWystawienia','TerminPlatnosci',
		'NazwaSzablonu','PodpisOdbiorcy','PodpisWystawcy','Uwagi');
	
	public function __construct($identyfikatorKontrahenta,$liczOd,$typFakturyKrajowej,$dataWystawienia,$sposobZaplaty,
	$rodzajPodpisuOdiorcy,$widocznyNumerGios, $numer, $kontrahent ){
		$this->details['IdentyfikatorKontrahenta'] = $identyfikatorKontrahenta;
		$this->details['LiczOd'] = $liczOd;
		$this->details['TypFakturyKrajowej'] = $typFakturyKrajowej;
		$this->details['DataWystawienia'] = $dataWystawienia;
		$this->details['SposobZaplaty'] = $sposobZaplaty;
		$this->details['RodzajPodpisuOdbiorcy'] = $rodzajPodpisuOdiorcy;
		$this->details['WidocznyNumerGios'] = $widocznyNumerGios;
		$this->details['Numer'] = $numer;
		$this->details['Kontrahent'] = $kontrahent->getDetails();
		$this->details['Pozycje'] = array();
		$nip = $kontrahent->getNIP();
		if( !empty($nip) ){
			$this->details['NIPKontrahenta']=$kontrahent->getNIP();
		} 
	}
	public function dodajPozycjeFaktury($nowaPozycja){
		$this->details['Pozycje'][] = $nowaPozycja->getDetails();
	}
	public function getDataWystawienia(){
		return $this->details['DataWystawienia'];
	}
	public function getZaplacono(){
		return $this->details['Zaplacono'];
	}
	public function getPozycje(){
		return $this->details['Pozycje'];
	}
}

class FakturaWysylkowa extends BaseFlyweightBuilder{
	protected $fields = array('NumerKontaBankowego','DataOtrzymaniaZaplaty', 'MiejsceWystawienia','TerminPlatnosci','NazwaSeriiNumeracji',
		'NazwaSzablonu','PodpisOdbiorcy','PodpisWystawcy','Uwagi');
	public function __construct($zaplacono,$identyfikatorKontrahenta,$liczOd,$dataWystawienia, $formatDatySprzedazy,
		$rodzajPodpisuOdiorcy,$widocznyNumerGios, $numer,$dataSprzedazy, $kontrahent ){
		$this->details['Zaplacono'] = $zaplacono;
		$this->details['IdentyfikatorKontrahenta'] = $identyfikatorKontrahenta;
		$this->details['LiczOd'] = $liczOd;
		$this->details['DataWystawienia'] = $dataWystawienia;
		$this->details['FormatDatySprzedazy'] = $formatDatySprzedazy;
		$this->details['RodzajPodpisuOdbiorcy'] = $rodzajPodpisuOdiorcy;
		$this->details['WidocznyNumerGios'] = $widocznyNumerGios;
		$this->details['Numer'] = $numer;
		$this->details['DataSprzedazy'] = $dataSprzedazy;
		$this->details['Kontrahent'] = $kontrahent->getDetails();
		$this->details['Pozycje'] = array();
		$nip = $kontrahent->getNIP();
		if( !empty($nip) ){
			$this->details['NIPKontrahenta']=$kontrahent->getNIP();
		} 
		
	}
	public function dodajPozycjeFaktury($nowaPozycja){
		$this->details['Pozycje'][] = $nowaPozycja->getDetails();
	}
	public function getDataWystawienia(){
		return $this->details['DataWystawienia'];
	}
	public function getZaplacono(){
		return $this->details['Zaplacono'];
	}
	public function getPozycje(){
		return $this->details['Pozycje'];
	}
	
}

?>