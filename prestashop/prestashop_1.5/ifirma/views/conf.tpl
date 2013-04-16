{$processMessage}

<div class="warn">
	Moduł <b>nie</b> posida wsparcia dla rabatów procentowych i kwotowych.
</div>

<form action="{$formAction}" method="post" class="width4" style="margin: 0 auto;">
	<fieldset>
		<legend><img src="{$adminImg}prefs.gif" alt="Ustawienia" />Ustawienia</legend>

		<div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiVatName}">Jestem płatnikiem Vat:</label>
			<div class="margin-form">	
				<input type="checkbox" name="{$apiVatName}" id="ifirma_api_vatowiec" {$apiVatChecked} />
				<p class="preference_description">Jestem płatnikiem Vat</p>	
			</div>
		</div><div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiBillName}">Klucz do API - rachunek:</label>
			<div class="margin-form">	
				<input type="text" name="{$apiBillName}" id="ifirma_api_key_rachunek" value="{$apiBillValue}" size="50" />
				<p class="preference_description">Klucz API rachunek</p>	
			</div>
		</div>
		<div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiInvoiceName}">Klucz do API - faktura:</label>
			<div class="margin-form">	
				<input type="text" name="{$apiInvoiceName}" id="ifirma_api_key_faktura" value="{$apiInvoiceValue}" size="50" />
				<p class="preference_description">Klucz API faktura</p>	
			</div>
		</div>

		<div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiSubscriberName}">Klucz do API - abonent:</label>
			<div class="margin-form">
				<input type="text" name="{$apiSubscriberName}" id="ifirma_api_key_abonent" value="{$apiSubscriberValue}" size="50" />
				<p class="preference_description">Klucz API abonent</p>	
			</div>
		</div>

		<div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiLoginName}">Login do API:</label>
			<div class="margin-form">
				<input type="text" name="{$apiLoginName}" id="ifirma_api_login" value="{$apiLoginValue}" size="50" />
				<p class="preference_description">Twój login API</p>	
			</div>
		</div>

		<div class="clear center"><input class="button" style="margin-top: 10px" name="{$submitName}" id="{$submitName}" value="Zapisz" type="submit" /></div>
	</fieldset>
</form><br/>

<fieldset class="width4" style="margin: 0 auto;">
	<legend><img src="{$adminImg}comment.gif" alt="Informacje">Informacje</legend>
	<p>Blog ifirma.php - <a target="_blank" href="http://blog.ifirma.pl/">odwiedź</a></p>
</fieldset>