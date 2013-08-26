CREATE TABLE IF NOT EXISTS `ifirma_invoice_map` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`order_id` INT(11) NOT NULL,
	`invoice_id` INT(11) NOT NULL,
	`invoice_number` VARCHAR(32),
	`invoice_type` ENUM('invoice', 'invoice_send', 'invoice_proforma', 'invoice_bill') NOT NULL
) ENGINE=INNODB DEFAULT CHARSET=utf8;