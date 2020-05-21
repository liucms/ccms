DROP TABLE IF EXISTS `haoma`;
CREATE TABLE `haoma` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '排序',
  `name` varchar(11) NOT NULL DEFAULT '' COMMENT '会员',
  `value` varchar(1) NOT NULL DEFAULT '0' COMMENT '标记',
  `time` varchar(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `origin` varchar(50) NOT NULL DEFAULT 'admin' COMMENT '来源',
  `operator` varchar(50) NOT NULL DEFAULT 'admin' COMMENT '赠送',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
