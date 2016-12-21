{$processMessage}

<div class="warn">
	Moduł <b>nie</b> posiada wsparcia dla rabatów procentowych i kwotowych.
</div>

<form action="{$formAction}" method="post" class="width4" style="margin: 0 auto;">
	<fieldset>
		<!--<legend><img src="{$adminImg}prefs.gif" alt="Ustawienia" />Ustawienia</legend> -->

	<legend><img src="{$ifirmaImg}prefs.gif"  alt="Ustawienia" />Ustawienia</legend> 
		<div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiVatName}">Jestem płatnikiem Vat:</label>
			<div class="margin-form">	
				<input type="checkbox" name="{$apiVatName}" id="ifirma_api_vatowiec" {$apiVatChecked} />
				<p class="preference_description">Jestem płatnikiem Vat</p>	
			</div>
		</div>
		
		{if $apiVatChecked}
			{assign var="ppdisplay" value="none"}
		{else}
			{assign var="ppdisplay" value=""}
		{/if}
		
		<div style="clear: both; padding-top:15px; display:{$ppdisplay}" id="ifirma_podstawa_prawna_div">
			<label class="conf_title" for="{$apiPodstawaPrawnaName}">Podstawa prawna:</label>
			<div class="margin-form">	
				<input type="text" name="{$apiPodstawaPrawnaName}" id="ifirma_api_key_faktura" value="{$apiPodstawaPrawnaValue}" size="50" />
				<p class="preference_description">Podstawa prawna zwolnienia z VAT</p>	
			</div>
		</div>
		
		<div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiRyczaltName}">Jestem ryczałtowcem:</label>
			<div class="margin-form">	
				<input type="checkbox" name="{$apiRyczaltName}" id="ifirma_api_ryczaltowiec" {$apiRyczaltChecked} />
				<p class="preference_description">Jestem ryczałtowcem</p>	
			</div>
		</div>
		
		{if $apiRyczaltChecked}
			{assign var="rddisplay" value=""}
		{else}
			{assign var="rddisplay" value="none"}
		{/if}

		<div style="clear: both; padding-top:15px; display:{$rddisplay}" id="ifirma_ryczalt_rate_div">
			<label class="conf_title" for="{$apiRyczaltRateName}">Stawka ryczałtu:</label>
			<div class="margin-form">	
				<select name="{$apiRyczaltRateName}" id="ifirma_api_ryczalt_rate">
                   {html_options values=$apiRyczaltRates output=$apiRyczaltRatesLabels selected=$apiRyczaltRateValue}
				</select>
			</div>
		</div>
		<div style="clear: both; padding-top:15px; display:{$rddisplay}"  id="ifirma_ryczalt_wpis_do_ewidencji_div">
			<label class="conf_title" for="{$apiRyczaltWpisDoEwidencji}">Wpis do ewidencji:</label>
			<div class="margin-form">	
				<input type="checkbox" name="{$apiRyczaltWpisDoEwidencji}" id="ifirma_api_ryczaltowiec" {$apiRyczaltWpisDoEwidencjiChecked} />
				<p class="preference_description">Wpis do ewidencji</p>	
			</div>
		</div>
		<div style="clear: both; padding-top:15px;">
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
			<label class="conf_title" for="{$apiLoginName}">Login ifirma.pl:</label>
			<div class="margin-form">
				<input type="text" name="{$apiLoginName}" id="ifirma_api_login" value="{$apiLoginValue}" size="50" />
				<p class="preference_description">Twój login ifirma.pl</p>	
			</div>
		</div>
		
		<div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiCityName}">Miasto wystawienia:</label>
			<div class="margin-form">
				<input type="text" name="{$apiCityName}" id="ifirma_api_login" value="{$apiCityValue}" size="50" />
				<p class="preference_description">Miasto wystawienia faktury</p>	
			</div>
		</div>
		
		<div style="clear: both; padding-top:15px;">
			<label class="conf_title" for="{$apiSeriesName}">Nazwa serii numeracji:</label>
			<div class="margin-form">
				<input type="text" name="{$apiSeriesName}" id="ifirma_api_login" value="{$apiSeriesValue}" size="50" />
				<p class="preference_description">Nazwa serii numeracji</p>	
			</div>
		</div>

		<div class="clear center"><input class="button" style="margin-top: 10px" name="{$submitName}" id="{$submitName}" value="Zapisz" type="submit" /></div>
	</fieldset>
</form><br/>

<fieldset class="width4" style="margin: 0 auto;">
	<!-- <legend><img src="{$adminImg}comment.gif" alt="Informacje">Informacje</legend> -->
	<legend><img src="{$ifirmaImg}comment.gif" alt="Informacje">Informacje</legend>
	<p>Blog ifirma - <a target="_blank" href="http://blog.ifirma.pl/">odwiedź</a></p>
</fieldset>
