IFIRMA API - OpenCart
=====================

- Aby zainstalować OpenCart (opis wykonany do wersji `OpenCart_v.1.4.9.4`), należy mieć dostęp do zdalnego serwera internetowego lub do lokalnego serwera na komputerze (MAMP – dla Mac OS-a, LAMP – dla Linuksa, WAMP – dla Windows), z zainstalowanym systemem bazodanowym, jak np. MySQL.

- Za pośrednictwem phpMyAdmin (panelu administracyjnego systemu bazodanowego) tworzymy bazę danych, którą wpisujemy od instalatora OpenCarta. Panel administracyjny phphMyAdmin pozycja MYSQL localhost > Utwórz nową bazę danych> "podajemy własną nazwę (np. baza opencart)".

- Następnie pobieramy darmowe oprogramowanie sklepu internetowego OpenCart ze strony internetowej www.opencart.com zakładka download (`v.1.4.9.4`).

- Kopiujemy rozpakowaną bazę sklepu na serwer. Wchodzimy na naszą domenę np. http://www.nasza_domena.pl i rozpoczynamy instalację postepując zgodnie ze wskazówkami wyświetlanymi na ekranie, wpisując w Database configuration:

	nazwę bazy danych serwera (np. localhost),
	nazwę bazy danych stworzoną z poziomu administratora phpMyAdmin (np. baza opencart),
	login jaki podajemy łącząc się z bazą danych phpMyAdmin (np. root),
	hasło jakie podajemy łącząc się z bazą danych phpMyAdmin (np. root).
Dalej postępujemy zgodnie z komunikatami pojawiającymi podczas instalacji, po której ze względów bezpieczeństwa usuwamy z serwera katalog install oraz zmieniamy nazwę katalogu admin na własną (np. admin123)

- Następnie stąd pobieramy pliki niezbędne do integracji sklepu internetowego z ifirma.pl. Do naszego folderu (np. admin123) należy skopiować:

	folder ifirma/*
	api_configuration.php
	api_request.php
	config.ini
	download.php
	do podmiany/modyfikacji jest plik OpenCart admin/view/template/sale/order_list.tpl

- wszystkie pliki powinny mieć odpowiednie uprawnienia (755, a w przypadku config.ini 777). (przykładowa komenda chmod 755 api_request.php)

- `001_ifirma_invoice_map.sql` zawiera zapytanie, które należy wysłać do stworzonej bazy danych OpenCart. Treść zapytania można wkleić np. do edytora zapytań MySQL z poziomu narzędzi administracyjnych bazy danych, takich jak phpMyAdmin. Utworzone zostaną niezbędne tabele potrzebne do integracji. Dodatkowo wykonanie tego zapytania utworzy dodatkową tabelę w bazie danych.

- Przed skorzystaniem z API należy dokonać konfiguracji za pomocą wygenerowanych uprzednio w ifirma.pl kluczy symetrycznych dla abonenta i faktury oraz za pomocą swojego loginu do serwisu ifirma.pl. Konfiguracji dokonać należy z poziomu panelu administratora sklepu (zakładka Sales->Orders-> Konfiguracja API).

- Następnie można już wystawić fakturę, która pojawi się w przychodach na naszym koncie w serwisie ifirma.pl
