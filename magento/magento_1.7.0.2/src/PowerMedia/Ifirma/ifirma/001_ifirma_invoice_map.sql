CREATE TABLE `ifirma` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`document_type` VARCHAR( 31 ) NULL ,
`order_id` VARCHAR( 31 ) NOT NULL ,
`invoice_number` VARCHAR( 31 ) NOT NULL ,
`invoice_type` VARCHAR( 31 ) NOT NULL,
`correction_needed` INT( 1 ) NOT NULL DEFAULT '0',
`correction_done` INT( 1 ) NOT NULL DEFAULT '0'
) ENGINE = InnoDB;
