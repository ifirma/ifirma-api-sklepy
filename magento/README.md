Ostrzeżenie – nie instaluj nowej wersji wtyczki, jeżeli Twój sklep jest już zintegrowany z ifirma.pl (w pzypadku gdy poprzednia wtyczka nie została odinstalowana).

Wtyczka kompatybilna z Magento w wersji 1.8+ oraz 1.9+

Instrukcja:
-----------
- Pobierz wtyczkę.
- Rozpakuj pobrany plik.
- Zaloguj się do panelu administracyjnego Twojego sklepu.
- Przejdź do zakładki System > Magento Connect > Zarządzanie Magento Connect.
- W sekcji Direct package file upload wybierz plik iFirma-0.1.6.tgz i zatwierdź Upload.
- W oknie poniżej powinien pojawić się komunikat o powodzeniu instalacji.
- Po powrocie do panelu administracyjnego sklepu w zakładce System > Konfiguracja powinna pojawić się zakładka IFIRMA, w której można dokonać konfiguracji integracji.

Dane potrzebne do konfiguracji integracji odnajdziesz po zalogowaniu na swoje konto w ifirma.pl w zakładce Narzędzia > API.

Jeśli instalacja wtyczki zakończyła się powodzeniem, a mimo to w zakąłdce System > Konfiguracja nie ma widocznej zakładki IFIRMA, to upewnij się, że wyczyściłeś pamięć cache oraz sprawdź przy pomocy klienta FTP uprawnienia zainstalowanych plików.

Uwagi:
------
 - polskie stawki VAT(23%, 8%, 5%, 0%) należy samodzielnie zdefiniować w konfiguracji sklepu
 - nie ma możliwości ustawienia stawki VAT 'zw' (sklep nie daje możliwości zdefiniowana stawki z wartością inną niż liczba)
 - sprzedaż w walucie obsługiwana jest na zasadzie 1:1, czyli w sklepie 1USD = 1PLN na fakturze
