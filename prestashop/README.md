Instalacja sklepu (dla FTP):

• Pobierz PrestaShop 1.4.8.3.

• Rozpakuj pliki na dysku.

• Zawartość rozpakowanego folderu /prestashop (nie sam folder) wgraj do katalogu głównego serwera (przy użyciu FTP). Można też wgrać pliki do subfolderu utworzonego w katalogu głównym np. http://www.mojastrona.pl/prestashop.

• Sprawdź czy foldery /config, /upload, /download, /tools/smarty/compile maja uprawnienia „write” (inaczej „CHMOD 777”). Jeśli trzeba, nadaj im atrybut „write” razem z ich podfolderami: /img, /mails, /modules, /themes/prestashop/lang, /translations.

• Rozpocznij instalację – w przeglądarce dodaj „/install” do adresu sklepu np. http://www.mojastrona.pl/prestashop/install.

• Po zakończonej instalacji przejdź do folderu z PrestaShop. Usuń z niego folder „/install” natomiast nazwę folderu „/admin” zmień np. na „/admin123”. Instalacja dobiegła końca.

Instalacja sklepu (dla DirectAdmin):

• Zaloguj się na swoją domenę w panelu DirectAdmin. Aby to zrobić, wpisz w przeglądarkę internetową adres swojej domeny i dodaj do niego ':2222' np. przykladowa_domena.pl:2222.

• Z menu "Web Applications" wybierz "zobacz więcej", sklep PrestaShop znajdziesz w "Aplikacje dla e-Commerce i Biznes".

• Wybierz sklep i kliknij "instaluj tą aplikację". Ustal domenę i wybierz wersję sklepu. Najlepiej wybrać PrestaShop 1.4.8.3 – pod nią została stworzona wtyczka integrująca. Pamiętaj, aby wybrać instalację z przykładową wersją sklepu – przyda się to podczas instalacji wtyczek. Po ustawieniu wszystkich parametrów kliknij "Zainstaluj". Instalator automatycznie stosuje zabezpieczenia wymagane przez PrestaShop, usuwa plik 'install' i zmienia nazwę katalogu "admin" na "iadmin"..

Sklep zostanie zainstalowany na naszej domenie. Kolejnym krokiem jest jego integracja z ifirma.pl.

Instalacja wtyczki:

• Pobierz wtyczkę. Wybierz opcję "Download" i format pliku (.zip lub tar.gz). Przy wyborze "tar.gz" na dysku zostanie zapisany folder o nazwie "ifirma-ifirma-api-sklepy-89a9f26". Przy wyborze ".zip" zapisany zostanie plik, który po rozpakowaniu stworzy taki sam folder. Znajdziemy w nim pliki niezbędne do integracji sklepu z ifirma.pl.

• Wtyczki trzeba skopiować do katalogu "admin123" (były katalog "admin", którego nazwa została zmieniona podczas instalacji przez FTP) lub 'iadmin' (w przypadku instalacji przez DirectAdmin).

• Wybierz pliki. W folderze, który wcześniej ściągnęliśmy z Github klikamy 'prestashop'. Następnie do katalogu "admin123"/"iadmin" wgraj pliki:

api_configuration.php
api_request.php
config.ini
download.php

• Wgraj też FOLDER (nie pliki z foldera, a folder) "ifirma".

• Plik "AdminTab.php" wgraj do folderu "classes" ("prestashop/classes").

• Plik "AdminPreferences.php" wgraj do folderu "tabs" ("prestashop/admin123/tabs" lub "prestashop/iadmin/tabs").

• Zmień uprawnienia wszystkim wgranym plikom (również tym w folderze "ifirma") na 755. Plikowi o nazwie "config.ini" dajemy uprawnienia równe 777.

• Plik "001_ifirma_invoice_map.sql" zawiera zapytanie, które musimy wysłać do bazy danych naszego sklepu. Aby to zrobić należy:

Zalogować się do phpMyAdmin.
Wybrać bazę danych PrestaShop, która domyślnie zapisana jest jako "_ps1".
Wybrać zakładkę "SQL".
Wkleić treść pliku "001_ifirma_invoice_map.sql" i kliknąć opcję "Wykonaj".

Przed skorzystaniem z API należy je skonfigurować za pomocą wygenerowanych w ifirma kluczy autoryzacyjnych. Aby wygenerować klucz należy:

• Zalogować się w ifirma.pl.
• Wybrać menu "administracja" > "ustawienia" > "ustawienia" > "klucze autoryzacji".
• Wygenerować dwa klucze symetryczne, jeden dla abonenta, drugi dla faktury.
• W przypadku integracji kilku sklepów z jednym kontem ifirma należy użyć tych samych kluczy autoryzacyjnych.

Integracja API:
• Zaloguj się w panelu administracyjnym swojego sklepu.
• Kliknij zakładkę "Preferences". Na dole listy wybierz "Konfiguracja API".
• Wklej wcześniej wygenerowane klucze, podaj login, którego używasz w ifirma i zapisz parametry.
• Od teraz, przy złożonych w sklepie zamówieniach (zakładka "Orders") będą dostępne funkcje "Wystaw fakturę" i "Wystaw fakturę wysyłkową". Wystawiona w ten sposób faktura pojawi się w menu "Przychody" w aplikacji ifirma.

Integracja PrestaShop z ifirma.pl została ukończona.