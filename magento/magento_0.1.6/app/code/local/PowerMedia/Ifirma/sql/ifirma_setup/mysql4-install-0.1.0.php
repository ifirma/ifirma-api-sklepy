<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	DROP TABLE IF EXISTS `{$installer->getTable('ifirma/ifirma')}`;

	CREATE TABLE IF NOT EXISTS `{$installer->getTable('ifirma/ifirma')}` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`order_id` INT(11) NOT NULL,
	`invoice_id` INT(11) NOT NULL,
	`invoice_number` VARCHAR(32),
	`invoice_type` ENUM('invoice', 'invoice_send', 'invoice_proforma', 'invoice_bill') NOT NULL
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
?>
