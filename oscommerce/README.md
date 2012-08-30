Instalacja sklepu (dla FTP):

• Pobierz osCommerce 2.3.2.

• Rozpakuj pliki na dysku.

• Zawartość folderu „catalog/” wgraj do katalogu głównego serwera (przy użyciu FTP). Można też wgrać pliki do subfolderu utworzonego w katalogu głównym np. http://www.mojastrona.pl/oscommerce.

Instalacja sklepu (dla DirectAdmin):

• Zaloguj się na swoją domenę w panelu DirectAdmin. Aby to zrobić, wpisz w przeglądarkę internetową adres swojej domeny i dodaj do niego ':2222' np. przykladowa_domena.pl:2222.

• Z menu 'Web Applications' wybierz 'zobacz więcej', sklep osCommerce znajdziesz w 'Aplikacje dla e-Commerce i Biznes'.

• Wybierz sklep i kliknij 'instaluj tą aplikację'. Ustal domenę i wybierz wersję sklepu. Najlepiej wybrać osCommerce 2.3.1 – pod nią została stworzona wtyczka integrująca. Po ustawieniu wszystkich parametrów kliknij 'Zainstaluj'.

Sklep zostanie zainstalowany na naszej domenie. Kolejnym krokiem jest jego integracja z ifirma.pl.

Instalacja wtyczki:

• Pobierz wtyczkę. Wybierz opcję 'Download' i format pliku (.zip lub tar.gz). Przy wyborze 'tar.gz' na dysku zostanie zapisany folder o nazwie 'ifirma-ifirma-api-sklepy-89a9f26'. Przy wyborze '.zip' zapisany zostanie plik, który po rozpakowaniu stworzy taki sam folder. Znajdziemy w nim pliki niezbędne do integracji sklepu z ifirma.pl.

• Wtyczkę trzeba skopiować do katalogu admin' za pomocą FTP.

• Z folderu, który wcześniej ściągnęliśmy z Github należy wgrać pliki:

api_configuration.php
api_request.php
config.ini
download.php
orders.php
Wgraj też FOLDER (nie pliki z foldera, a folder) 'ifirma'.

• Plik "configuration.php" podmieniamy w "catalog/admin/includes/boxes/configuration.php".

• Zmień uprawnienia wszystkim wgranym plikom (również tym w folderze 'ifirma'). Zaznacz je (klikając w kwadraciki po prawej stronie listy). Na dole strony wybierz opcję 'Ustaw uprawnienia' w okienko obok wpisując 755. Plikowi o nazwie 'config.ini' dajemy uprawnienia równe 777.

• Plik '001_ifirma_invoice_map.sql' zawiera zapytanie, które musimy wysłać do bazy danych naszego sklepu. Aby to zrobić należy:

• Zalogować się do phpMyAdmin.
• Wybrać bazę danych osCommerce, która domyślnie zapisana jest jako '_osco1'

• Wybrać zakładkę 'SQL'.

• Wkleić treść pliku '001_ifirma_invoice_map.sql' i kliknąć opcję 'Wykonaj'.

• Przed skorzystaniem z API należy je skonfigurować za pomocą wygenerowanych w ifirma kluczy autoryzacyjnych. Aby wygenerować klucz należy:

Zalogować się w ifirma.pl.
Wybrać menu 'administracja' > 'ustawienia' > 'ustawienia' > 'klucze autoryzacji'.
Wygenerować dwa klucze symetryczne, jeden dla abonenta, drugi dla faktury.
W przypadku integracji kilku sklepów z jednym kontem ifirma należy użyć tych samych kluczy autoryzacyjnych.

Integracja API:
• Zaloguj się w panelu administracyjnym swojego sklepu.
• Kliknij "Configuration" > "Ifirma konfiguracja".
• Wklej wcześniej wygenerowane klucze, podaj login, którego używasz w ifirma i zapisz parametry.
• Aby wystawić fakturę wybierz "Customers" > "Orders".

Integracja osCommerce z ifirma.pl została ukończona. Teraz przy każdym zamówieniu będzie można użyć opcji 'wystaw fakturę' lub 'wystaw fakturę wysyłkową'. Taka faktura pojawi się w menu 'Przychody' w aplikacji ifirma.