<div class="row" id="start_products">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-heading">
                <legend><img src="../img/admin/invoice.gif">Fakturowanie - ifirma.pl</legend>
            </div>
            <fieldset style="margin-top: 20px;">
                {$sendResultMessage}
                {$invoiceValidationMessage}
                
                    {if $invoice}
                        <a href="../modules/ifirma/requests/getInvoice.php?id={$invoice->id}&h={$hash}"><img src="{$ifirmaImg}pdf.gif"/>Pobierz fakturę krajową &raquo;</a>
                    {else}
                        <a href="../modules/ifirma/requests/sendInvoice.php?id={$orderId}&h={$hash}&type={$actionInvoice}"><img src="../img/admin/next.gif"/>Wystaw fakturę krajową &raquo;</a>
                    {/if}
                    <br/>
                    
                {if !$isVat}
                    {if $bill}
                        <a href="../modules/ifirma/requests/getInvoice.php?id={$bill->id}&h={$hash}"><img src="{$ifirmaImg}pdf.gif"/>Pobierz rachunek &raquo;</a>
                    {else}
                        <a href="../modules/ifirma/requests/sendInvoice.php?id={$orderId}&h={$hash}&type={$actionBill}"><img src="../img/admin/next.gif"/>Wystaw rachunek &raquo;</a>
                    {/if}
                {else}
                    {if $invoiceSend}
                        <a href="../modules/ifirma/requests/getInvoice.php?id={$invoiceSend->id}&h={$hash}"><img src="{$ifirmaImg}pdf.gif"/>Pobierz fakturę wysyłkową&raquo;</a>
                    {else}
                        <a href="../modules/ifirma/requests/sendInvoice.php?id={$orderId}&h={$hash}&type={$actionInvoiceSend}"><img src="../img/admin/next.gif"/>Wystaw fakturę wysyłkową &raquo;</a>
                    {/if}
                    <br/>
                    {if $invoiceProforma}
                        <a href="../modules/ifirma/requests/getInvoice.php?id={$invoiceProforma->id}&h={$hash}"><img src="{$ifirmaImg}pdf.gif"/>Pobierz fakturę proforma&raquo;</a>
                        {if !$invoice}
                             <br />
                            <a href="../modules/ifirma/requests/sendInvoice.php?id={$invoiceProforma->id}&h={$hash}&type={$actionInvoiceFromProforma}"><img src="../img/admin/next.gif"/>Wystaw fakturę krajową na podstawie faktury proforma &raquo;</a>
                        {/if}
                    {else}
                        <a href="../modules/ifirma/requests/sendInvoice.php?id={$orderId}&h={$hash}&type={$actionInvoiceProforma}"><img src="../img/admin/next.gif"/>Wystaw fakturę proforma &raquo;</a>
                    {/if}
                {/if}
            </fieldset>
        </div>
    </div>
</div>
