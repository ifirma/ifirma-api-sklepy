<?php

/**
 * Description of InvoiceContractor
 *
 * @author platowski
 */
class PowerMedia_Ifirma_Model_InvoiceContractor {
	private $Nazwa;
	private $Nazwa2;
	private $Identyfikator;
	private $PrefiksUE;
	private $NIP;
	private $Ulica;
	private $KodPocztowy;
	private $Kraj;
	private $Miejscowosc;
	private $Email;
	private $Telefon;
	private $OsobaFizyczna;

	public function getNazwa() {
		return $this->Nazwa;
	}

	public function setNazwa($nazwa) {
		return $this->Nazwa = $nazwa;
	}

	public function getNazwa2() {
		return $this->Nazwa2;
	}

	public function setNazwa2($nazwa2) {
		return $this->Nazwa2 = $nazwa2;
	}

	public function getIdentyfikator() {
		return $this->Identyfikator;
	}

	public function setIdentyfikator($identyfikator) {
		return $this->Identyfikator = $identyfikator;
	}

	public function getPrefiksUE() {
		return $this->PrefiksUE;
	}

	public function setPrefiksUE($prefiks_ue) {
		return $this->PrefiksUE = $prefiks_ue;
	}

	public function getNIP() {
		return $this->NIP;
	}

	public function setNIP($nip) {
		return $this->NIP = $nip;
	}

	public function getUlica() {
		return $this->Ulica;
	}

	public function setUlica($ulica) {
		return $this->Ulica = $ulica;
	}

	public function getKodPocztowy() {
		return $this->KodPocztowy;
	}

	public function setKodPocztowy($kod_pocztowy) {
		return $this->KodPocztowy = $kod_pocztowy;
	}

	public function getKraj() {
		return $this->Kraj;
	}

	public function setKraj($kraj) {
		return $this->Kraj = $kraj;
	}

	public function getMiejscowosc() {
		return $this->Miejscowosc;
	}

	public function setMiejscowosc($miejscowosc) {
		return $this->Miejscowosc = $miejscowosc;
	}

	public function getEmail() {
		return $this->Email;
	}

	public function setEmail($email) {
		return $this->Email = $email;
	}

	public function getTelefon() {
		return $this->Telefon;
	}

	public function setTelefon($telefon) {
		return $this->Telefon = $telefon;
	}

	public function getOsobaFizyczna() {
		return $this->OsobaFizyczna;
	}

	public function setOsobaFizyczna($osoba_fizyczna) {
		return $this->OsobaFizyczna = $osoba_fizyczna;
	}
	/**
	 *
	 * @return array 
	 */
	public function getProperties() {
		return get_object_vars($this);
	}
}
