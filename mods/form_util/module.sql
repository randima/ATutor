# sql file for form utility module

CREATE TABLE IF NOT EXISTS `at_forms` (
  `id` varchar(10) NOT NULL,
  `rank` int(2) NOT NULL,
  `type` varchar(15) NOT NULL,
  `label` varchar(15) NOT NULL,
  `name` varchar(10) NOT NULL,
  `required` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `at_forms_options` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `element_id` int(5) NOT NULL,
  `choice` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `language_text` VALUES ('en', '_module','form_util','Form Setup',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','form_util_tool','Form Generation Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msg','AT_ERROR_ID_EXISTS','Some IDs already exist try different id',NOW(),'');
