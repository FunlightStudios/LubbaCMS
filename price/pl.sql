SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `pl_furnitures`
-- ----------------------------
DROP TABLE IF EXISTS `furnitures`;
CREATE TABLE `furnitures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of pl_furnitures
-- ----------------------------
INSERT INTO `furnitures` VALUES ('1', '4533_1402657384.gif', '15', 'Habbo WM 2014', 'TOOOOOOORRRR!');
INSERT INTO `furnitures` VALUES ('2', '3250_1347634453.gif', '23', 'Wolkenthron', 'Wirf einen Blick auf dein Reich von der Sicherheit den Wolken');
INSERT INTO `furnitures` VALUES ('3', '4357_1397663926.gif', '7', 'Kaninchenbildnis', 'Übermenschliches Hasenwesen');
INSERT INTO `furnitures` VALUES ('4', '5463_1426698876.gif', '4', 'Schmelzender Minz-Schokohase', 'Gefertigt von der Santini Corporation');
INSERT INTO `furnitures` VALUES ('5', '4702_1408798995.gif', '6', 'Altmodische Kasse', 'Das macht 20 Taler');

-- ----------------------------
-- Table structure for `pl_settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `row` varchar(255) DEFAULT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of pl_settings
-- ----------------------------
INSERT INTO `settings` VALUES ('SitePath', 'http://localhost/price');
INSERT INTO `settings` VALUES ('WebPath', 'http://localhost/price/Web');
INSERT INTO `settings` VALUES ('SiteName', 'Habbo PL');
INSERT INTO `settings` VALUES ('Language', 'DE');
INSERT INTO `settings` VALUES ('Template', 'Default');
INSERT INTO `settings` VALUES ('Style', 'Default');
INSERT INTO `settings` VALUES ('FurnituresPath', 'http://localhost/price/Furnitures');

-- ----------------------------
-- Table structure for `pl_users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of pl_users
-- ----------------------------