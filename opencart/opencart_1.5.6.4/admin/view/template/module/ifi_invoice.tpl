<style type="text/css">
    label.conf_title {
        width: 235px;
        float: left;
        padding: 0.2em 0.5em 0 0;
        text-align: right;
        font-weight: bold;
        line-height: 22px;
    }
    .margin-form {
        padding: 0 0 1em 260px;
        color: #7F7F7F;
        font-size: 0.85em;
    }
    p.preference_description {
        font-style: italic;
        clear: both;
        text-align: left;
        width: 500px;
        color: #7F7F7F;
        font-size: 11px;
    }
    .ifi-settings{
        margin: 0 auto;
        width: 640px;
    }
    .center{
        text-align:center;
    }
    fieldset{
        color: #585A69;
        background-color: #EBEDF4;
    }
    .btn{
        text-decoration: none;
        color: white;
        display: inline-block;
        padding: 5px 15px 5px 15px;
        background: #003A88;
        -webkit-border-radius: 10px 10px 10px 10px;
        -moz-border-radius: 10px 10px 10px 10px;
        -khtml-border-radius: 10px 10px 10px 10px;
        border-radius: 10px 10px 10px 10px;
    }
</style>
<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>

    <div class="attention">
            <?php echo $warning_info ?>
    </div>

    <form id="ifirma-form" action="<?php echo $form_action; ?>" method="post">
        <fieldset>
            <legend><img src="view/image/setting.png" alt="Ustawienia" style="float:left;"/><span style="line-height:22px;"><?php echo $settings ?></span></legend>
            <div class="ifi-settings">

                    <div style="clear: both; padding-top:15px;">
                            <label class="conf_title" for="<?php echo $api_vat_name; ?>"><?php echo $is_vat_payer ?></label>
                            <div class="margin-form">	
                                    <input type="checkbox" name="<?php echo $api_vat_name; ?>" id="ifirma_api_vatowiec" <?php echo $api_vat_checked; ?> />
                                    <p class="preference_description"><?php echo $is_vat_payer_description ?></p>	
                            </div>
                    </div><div style="clear: both; padding-top:15px;">
                            <label class="conf_title" for="<?php echo $api_bill_name; ?>"><?php echo $api_key_bill ?></label>
                            <div class="margin-form">	
                                    <input type="text" name="<?php echo $api_bill_name; ?>" id="ifirma_api_key_rachunek" value="<?php echo $api_bill_value ?>" size="50" />
                                    <p class="preference_description"><?php echo $api_key_bill_description ?></p>	
                            </div>
                    </div>
                    <div style="clear: both; padding-top:15px;">
                            <label class="conf_title" for="<?php echo $api_invoice_name; ?>"><?php echo $api_key_invoice?></label>
                            <div class="margin-form">	
                                    <input type="text" name="<?php echo $api_invoice_name; ?>" id="ifirma_api_key_faktura" value="<?php echo $api_invoice_value; ?>" size="50" />
                                    <p class="preference_description"><?php echo $api_key_invoice_description ?></p>	
                            </div>
                    </div>

                    <div style="clear: both; padding-top:15px;">
                            <label class="conf_title" for="<?php echo $api_subscriber_name; ?>"><?php echo $api_key_subscriber ?></label>
                            <div class="margin-form">
                                    <input type="text" name="<?php echo $api_subscriber_name; ?>" id="ifirma_api_key_abonent" value="<?php echo $api_subscriber_value; ?>" size="50" />
                                    <p class="preference_description"><?php echo $api_key_subscriber_description ?></p>	
                            </div>
                    </div>

                    <div style="clear: both; padding-top:15px;">
                            <label class="conf_title" for="<?php echo $api_login_name; ?>"><?php echo $api_login ?></label>
                            <div class="margin-form">
                                    <input type="text" name="<?php echo $api_login_name; ?>" id="ifirma_api_login" value="<?php echo $api_login_value; ?>" size="50" />
                                    <p class="preference_description"><?php echo $api_login_description ?></p>	
                            </div>
                    </div>

                    <div class="center">
                        <a onclick="$('#ifirma-form').submit();" name="<?php echo $submit_name; ?>" class="button"><span><?php echo $save ?></span></a>
                        <a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
                    </div>
            </div>
        </fieldset>
    </form><br/>
    <fieldset  style="margin: 0 auto;">
        <legend><img src="view/image/information.png" alt="Informacje" style="float:left;"><span style="line-height:22px;"><?php echo $info; ?></span></legend>
        <div class="center">
            <p>Blog ifirma.pl - <a target="_blank" href="http://blog.ifirma.pl/"><?php echo $visit; ?></a></p>
        </div>
    </fieldset>
</div>
<?php echo $footer; ?>