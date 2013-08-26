<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Invoice
 *
 * @author platowski
 */
class PowerMedia_Ifirma_Model_InvoiceSend {

	private $Zaplacono;
	private $LiczOd;
	private $NumerKontaBankowego;
	private $DataWystawienia;
	private $MiejsceWystawienia;
	private $DataSprzedazy;
	private $FormatDatySprzedazy;
	private $TerminPlatnosci;
	private $NazwaSeriiNumeracji;
	private $NazwaSzablonu;
	private $RodzajPodpisuOdbiorcy;
	private $PodpisOdbiorcy;
	private $PodpisWystawcy;
	private $Uwagi;
	private $WidocznyNumerGios;
	private $Numer;
	private $Pozycje = array();
	private $Kontrahent;
	private $IdentyfikatorKontrahenta;
	private $NIPKontrahenta;

	public function getZaplacono() {
		return $this->Zaplacono;
	}

	public function setZaplacono($zaplacono) {
		return $this->Zaplacono = $zaplacono;
	}

	public function getLiczOd() {
		return $this->LiczOd;
	}

	public function setLiczOd($licz_od) {
		return $this->LiczOd = $licz_od;
	}

	public function getNumerKontaBankowego() {
		return $this->NumerKontaBankowego;
	}

	public function setNumerKontaBankowego($numer_konta_bankowego) {
		return $this->NumerKontaBankowego = $numer_konta_bankowego;
	}

	public function getDataWystawienia() {
		return $this->DataWystawienia;
	}

	public function setDataWystawienia($data_wystawienia) {
		return $this->DataWystawienia = $data_wystawienia;
	}

	public function getMiejsceWystawienia() {
		return $this->MiejsceWystawienia;
	}

	public function setMiejsceWystawienia($miejsce_wystawienia) {
		return $this->MiejsceWystawienia = $miejsce_wystawienia;
	}

	public function getDataSprzedazy() {
		return $this->DataSprzedazy;
	}

	public function setDataSprzedazy($data_sprzedazy) {
		return $this->DataSprzedazy = $data_sprzedazy;
	}

	public function getFormatDatySprzedazy() {
		return $this->FormatDatySprzedazy;
	}

	public function setFormatDatySprzedazy($format_daty_sprzedazy) {
		return $this->FormatDatySprzedazy = $format_daty_sprzedazy;
	}

	public function getTerminPlatnosci() {
		return $this->TerminPlatnosci;
	}

	public function setTerminPlatnosci($termin_platnosci) {
		return $this->TerminPlatnosci = $termin_platnosci;
	}

	public function getNazwaSeriiNumeracji() {
		return $this->NazwaSeriiNumeracji;
	}

	public function setNazwaSeriiNumeracji($nazwa_serii_numeracji) {
		return $this->NazwaSeriiNumeracji = $nazwa_serii_numeracji;
	}

	public function getNazwaSzablonu() {
		return $this->NazwaSzablonu;
	}

	public function setNazwaSzablonu($nazwa_szablonu) {
		return $this->NazwaSzablonu = $nazwa_szablonu;
	}

	public function getRodzajPodpisuOdbiorcy() {
		return $this->RodzajPodpisuOdbiorcy;
	}

	public function setRodzajPodpisuOdbiorcy($rodzaj_podpisu_odbiorcy) {
		return $this->RodzajPodpisuOdbiorcy = $rodzaj_podpisu_odbiorcy;
	}

	public function getPodpisOdbiorcy() {
		return $this->PodpisOdbiorcy;
	}

	public function setPodpisOdbiorcy($podpis_odbiorcy) {
		return $this->PodpisOdbiorcy = $podpis_odbiorcy;
	}

	public function getPodpisWystawcy() {
		return $this->PodpisWystawcy;
	}

	public function setPodpisWystawcy($podpis_wystawcy) {
		return $this->PodpisWystawcy = $podpis_wystawcy;
	}

	public function getUwagi() {
		return $this->Uwagi;
	}

	public function setUwagi($uwaga) {
		return $this->Uwagi = $uwaga;
	}

	public function getWidocznyNumerGios() {
		return $this->WidocznyNumerGios;
	}

	public function setWidocznyNumerGios($set_widoczny_numer_gios) {
		return $this->WidocznyNumerGios = $set_widoczny_numer_gios;
	}

	public function getNumer() {
		return $this->Numer;
	}

	public function setNumer($numer) {
		return $this->Numer = $numer;
	}

	public function getPozycje() {
		return $this->Pozycje;
	}

	public function setPozycje(array $pozycje) {
		return $this->Pozycje = $pozycje;
	}

	public function getKontrahent() {
		return $this->Kontrahent;
	}

	public function setKontrahent($kontrahent) {
		return $this->Kontrahent = $kontrahent;
	}

	public function getIdentyfikatorKontrahenta() {
		return $this->IdentyfikatorKontrahenta;
	}

	public function setIdentyfikatorKontrahenta($identyfikator_kontrahenta) {
		return $this->IdentyfikatorKontrahenta = $identyfikator_kontrahenta;
	}

	public function getNIPKontrahenta() {
		return $this->NIPKontrahenta;
	}

	public function setNIPKontrahenta($nip_kontrahenta) {
		return $this->NIPKontrahenta = $nip_kontrahenta;
	}

	/**
	 *
	 * @return array 
	 */
	public function getProperties() {
		return get_object_vars($this);
	}

	/**
	 *
	 * @return string Json array
	 */
	public function toJson() {
		$json = new Zend_Json();
		return $json->encode($this->getProperties());
	}

	public function init(Mage_Sales_Model_Order $order) {
		$this->setLiczOd("BRT");
		$this->setDataWystawienia(date('Y-m-d'));
		$this->setDataSprzedazy(date('Y-m-d'));
		$this->setFormatDatySprzedazy("DZN");
		$this->setRodzajPodpisuOdbiorcy("BPO");
		$this->setWidocznyNumerGios(true);
		$this->setZaplacono('0');
		$this->setTerminPlatnosci('');
		$items = $order->getAllVisibleItems();
		$positions=array();
		foreach ($items as $item) {
			/* @var $item Mage_Sales_Model_Order_Item */
			$invoice_position = new PowerMedia_Ifirma_Model_InvoicePosition();
			$tax = sprintf('%.2f',($item->getTaxPercent()/100));
			$invoice_position->setStawkaVat((string)$tax);
			$invoice_position->setIlosc($item->getQtyOrdered());
			$invoice_position->setNazwaPelna($item->getName());
			$invoice_position->setJednostka("sztuk");
			$invoice_position->setTypStawkiVat("PRC");
			$invoice_position->setCenaJednostkowa($item->getPriceInclTax());
			$invoice_position->setPKWiU("");
			$positions[] = $invoice_position->getProperties();
		}
		$shipping = $order->getShippingInclTax();
		if($shipping >= 0){
			/* @var $item Mage_Sales_Model_Order_Item */
			$invoice_position = new PowerMedia_Ifirma_Model_InvoicePosition();
			$tax = sprintf('%.2f',($item->getTaxPercent()/100));
			$invoice_position->setStawkaVat((string)$tax);
			$invoice_position->setIlosc(1);
			$invoice_position->setNazwaPelna('Koszty dostawy');
			$invoice_position->setJednostka("sztuk");
			$invoice_position->setTypStawkiVat("PRC");
			$invoice_position->setCenaJednostkowa($shipping);
			$invoice_position->setPKWiU("");
			$positions[] = $invoice_position->getProperties();
		}
		$this->setPozycje($positions);
		$invoice_contractor = new PowerMedia_Ifirma_Model_InvoiceContractor();
		$invoice_contractor->setNazwa($order->getBillingAddress()->getName());
		$invoice_contractor->setUlica($order->getBillingAddress()->getStreetFull());
		$invoice_contractor->setKodPocztowy($order->getBillingAddress()->getPostcode());
		$invoice_contractor->setMiejscowosc($order->getBillingAddress()->getCity());
		$invoice_contractor->setKraj($order->getBillingAddress()->getCountry());
		$this->setKontrahent($invoice_contractor->getProperties());
	}

}
