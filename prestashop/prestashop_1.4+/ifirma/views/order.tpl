<fieldset style="margin-top: 20px;">
	<legend><img src="../img/admin/invoice.gif">Fakturowanie - ifirma.pl</legend>
	{$sendResultMessage}
	{$invoiceValidationMessage}
	{if $isVat}
		{if $invoice}
			<a href="../modules/ifirma/requests/getInvoice.php?id={$invoice->id}&h={$hash}"><img src="../img/admin/pdf.gif"/>Pobierz fakturę krajową &raquo;</a>
		{else}
			<a href="../modules/ifirma/requests/sendInvoice.php?id={$orderId}&h={$hash}&type={$actionInvoice}"><img src="../img/admin/next.gif"/>Wystaw fakturę krajową &raquo;</a>
		{/if}
		<br/>
		{if $invoiceSend}
			<a href="../modules/ifirma/requests/getInvoice.php?id={$invoiceSend->id}&h={$hash}"><img src="../img/admin/pdf.gif"/>Pobierz fakturę wysyłkową&raquo;</a>
		{else}
			<a href="../modules/ifirma/requests/sendInvoice.php?id={$orderId}&h={$hash}&type={$actionInvoiceSend}"><img src="../img/admin/next.gif"/>Wystaw fakturę wysyłkową &raquo;</a>
		{/if}
		<br/>
		{if $invoiceProforma}
			<a href="../modules/ifirma/requests/getInvoice.php?id={$invoiceProforma->id}&h={$hash}"><img src="../img/admin/pdf.gif"/>Pobierz fakturę proforma&raquo;</a>
			{if !$invoice}
				 <br />
				<a href="../modules/ifirma/requests/sendInvoice.php?id={$invoiceProforma->id}&h={$hash}&type={$actionInvoiceFromProforma}"><img src="../img/admin/next.gif"/>Wystaw fakturę krajową na podstawie faktury proforma &raquo;</a>
			{/if}
		{else}
			<a href="../modules/ifirma/requests/sendInvoice.php?id={$orderId}&h={$hash}&type={$actionInvoiceProforma}"><img src="../img/admin/next.gif"/>Wystaw fakturę proforma &raquo;</a>
		{/if}
	{else}
		{if $bill}
			<a href="../modules/ifirma/requests/getInvoice.php?id={$bill->id}&h={$hash}"><img src="../img/admin/pdf.gif"/>Pobierz rachunek &raquo;</a>
		{else}
			<a href="../modules/ifirma/requests/sendInvoice.php?id={$orderId}&h={$hash}&type={$actionBill}"><img src="../img/admin/next.gif"/>Wystaw rachunek &raquo;</a>
		{/if}
	{/if}
</fieldset>