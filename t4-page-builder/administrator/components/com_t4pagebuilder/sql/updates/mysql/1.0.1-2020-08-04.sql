ALTER TABLE `#__jae_item` ADD `thumb` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `alias`;
ALTER TABLE `#__jae_item` ADD `bundle_css` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `css`;
ALTER TABLE `#__jae_item` ADD `page_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `type`;