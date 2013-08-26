<?php

/**
 * Description of Invoice
 *
 * @author platowski
 */
class PowerMedia_Ifirma_Model_InvoicePosition {

	private $StawkaVat;
	private $Ilosc;
	private $CenaJednostkowa;
	private $NazwaPelna;
	private $Jednostka;
	private $PKWiU;
	private $TypStawkiVat;
	private $Rabat;

	public function getStawkaVat() {
		return $this->StawkaVat;
	}

	public function setStawkaVat($stawka_vat) {
		return $this->StawkaVat = $stawka_vat;
	}

	public function getIlosc() {
		return $this->Ilosc;
	}

	public function setIlosc($ilosc) {
		return $this->Ilosc = $ilosc;
	}

	public function getCenaJednostkowa() {
		return $this->CenaJednostkowa;
	}

	public function setCenaJednostkowa($cena_jednostkowa) {
		return $this->CenaJednostkowa = $cena_jednostkowa;
	}

	public function getNazwaPelna() {
		return $this->NazwaPelna;
	}

	public function setNazwaPelna($nazwa_pelna) {
		return $this->NazwaPelna = $nazwa_pelna;
	}

	public function getJednostka() {
		return $this->Jednostka;
	}

	public function setJednostka($jednostka) {
		return $this->Jednostka = $jednostka;
	}

	public function getPKWiU() {
		return $this->PKWiU;
	}

	public function setPKWiU($pkwiu) {
		return $this->PKWiU = $pkwiu;
	}

	public function getTypStawkiVat() {
		return $this->TypStawkiVat;
	}

	public function setTypStawkiVat($typ_stawki_vat) {
		return $this->TypStawkiVat = $typ_stawki_vat;
	}

	public function getRabat() {
		return $this->Rabat;
	}

	public function setRabat($rabat) {
		return $this->Rabat = $rabat;
	}
	/**
	 *
	 * @return array 
	 */
	public function getProperties()
    {
		return get_object_vars($this);
    }
}