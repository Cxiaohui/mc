# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.17)
# Database: mc_datas
# Generation Time: 2018-01-16 06:36:56 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table mc_admin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mc_admin`;

CREATE TABLE `mc_admin` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `log` varchar(20) NOT NULL DEFAULT '' COMMENT '账号',
  `pwd` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `stat` char(5) NOT NULL DEFAULT '' COMMENT '随机码',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `role` varchar(10) NOT NULL DEFAULT '' COMMENT '角色名',
  `name` varchar(16) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别:0未知，1男，2女',
  `is_work` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否在职',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '登录控制',
  `depart_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '部门Id',
  `department` varchar(20) NOT NULL DEFAULT '' COMMENT '部门名称',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `loginip` varchar(64) NOT NULL DEFAULT '' COMMENT '最近登录ip',
  `lgtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最近登录时间',
  `post` varchar(20) NOT NULL DEFAULT '' COMMENT '职位',
  `contact` varchar(100) NOT NULL DEFAULT '' COMMENT '联系方式',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注信息',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `depart_id` (`depart_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户表';

LOCK TABLES `mc_admin` WRITE;
/*!40000 ALTER TABLE `mc_admin` DISABLE KEYS */;

INSERT INTO `mc_admin` (`id`, `log`, `pwd`, `stat`, `role_id`, `role`, `name`, `sex`, `is_work`, `status`, `depart_id`, `department`, `create_time`, `loginip`, `lgtime`, `post`, `contact`, `remark`, `isdel`)
VALUES
	(2,'test_man','ea9fa594b991eeb7235cd5333ce23f19','ZjbQp',3,'普通成员','测试男',1,1,1,3,'开发部',1497357567,'127.0.0.1',1497627318,'职员','','',0),
	(3,'cxhui','e1062faf460b1138f1b4740423e8465f','zfDNX',2,'ROOT','小飞',1,1,1,3,'开发部',1497357684,'127.0.0.1',1514016577,'主管','sssss','',0);

/*!40000 ALTER TABLE `mc_admin` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mc_admin_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mc_admin_access`;

CREATE TABLE `mc_admin_access` (
  `role_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `node_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限配置表';

LOCK TABLES `mc_admin_access` WRITE;
/*!40000 ALTER TABLE `mc_admin_access` DISABLE KEYS */;

INSERT INTO `mc_admin_access` (`role_id`, `node_id`)
VALUES
	(2,120),
	(2,119),
	(2,118),
	(2,117),
	(2,116),
	(2,115),
	(2,114),
	(2,113),
	(2,112),
	(2,111),
	(2,110),
	(2,109),
	(2,108),
	(2,107),
	(2,106),
	(2,122),
	(2,105),
	(2,104),
	(2,103),
	(2,102),
	(2,101),
	(2,100),
	(2,99),
	(2,98),
	(2,97),
	(2,96),
	(2,95),
	(3,24),
	(3,23),
	(3,19),
	(3,14),
	(3,10),
	(3,9),
	(3,8),
	(3,3),
	(3,2),
	(3,1),
	(2,94),
	(2,93),
	(2,92),
	(3,28),
	(3,29),
	(2,91),
	(2,90),
	(2,89),
	(2,88),
	(2,87),
	(2,86),
	(2,85),
	(2,84),
	(2,83),
	(2,82),
	(2,81),
	(2,80),
	(2,79),
	(2,78),
	(2,77),
	(2,76),
	(2,75),
	(2,74),
	(2,73),
	(2,72),
	(2,71),
	(2,70),
	(2,69),
	(2,68),
	(2,67),
	(2,66),
	(2,64),
	(2,63),
	(2,62),
	(2,61),
	(2,60),
	(2,59),
	(2,58),
	(2,57),
	(2,56),
	(2,55),
	(2,54),
	(2,53),
	(2,52),
	(2,51),
	(2,50),
	(2,49),
	(2,48),
	(2,47),
	(2,46),
	(2,45),
	(2,44),
	(2,43),
	(2,42),
	(2,41),
	(2,40),
	(2,39),
	(2,38),
	(2,37),
	(2,36),
	(2,35),
	(2,34),
	(2,33),
	(2,32),
	(2,31),
	(2,29),
	(2,28),
	(2,27),
	(2,26),
	(2,25),
	(2,24),
	(2,23),
	(2,22),
	(2,21),
	(2,20),
	(2,19),
	(2,18),
	(2,17),
	(2,16),
	(2,15),
	(2,14),
	(2,13),
	(2,12),
	(2,11),
	(2,10),
	(2,9),
	(2,8),
	(2,30),
	(2,7),
	(2,6),
	(2,5),
	(2,4),
	(2,3),
	(2,2),
	(2,1),
	(2,121);

/*!40000 ALTER TABLE `mc_admin_access` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mc_admin_department
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mc_admin_department`;

CREATE TABLE `mc_admin_department` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '部门名称',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间 ',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='部门表';

LOCK TABLES `mc_admin_department` WRITE;
/*!40000 ALTER TABLE `mc_admin_department` DISABLE KEYS */;

INSERT INTO `mc_admin_department` (`id`, `name`, `sort`, `addtime`, `isdel`)
VALUES
	(1,'总经办',0,1497425417,0),
	(2,'管理部',0,1497425452,0),
	(3,'开发部',0,1497515598,0);

/*!40000 ALTER TABLE `mc_admin_department` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mc_admin_node
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mc_admin_node`;

CREATE TABLE `mc_admin_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '节点ID',
  `name` varchar(20) NOT NULL COMMENT '节点名字',
  `title` varchar(50) DEFAULT '' COMMENT '节点中文名称',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态，0隐藏，1显示',
  `remark` varchar(255) DEFAULT '' COMMENT '备注说明 ',
  `sort` smallint(6) unsigned DEFAULT '0' COMMENT '排序',
  `pid` smallint(6) unsigned NOT NULL COMMENT '父节点',
  `gid` smallint(6) NOT NULL DEFAULT '0' COMMENT '分组id',
  `level` tinyint(1) unsigned NOT NULL COMMENT '级别',
  `ismenu` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否用为菜单',
  `icon` varchar(32) NOT NULL DEFAULT '' COMMENT '图标',
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `gid` (`gid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限节点表';

LOCK TABLES `mc_admin_node` WRITE;
/*!40000 ALTER TABLE `mc_admin_node` DISABLE KEYS */;

INSERT INTO `mc_admin_node` (`id`, `name`, `title`, `status`, `remark`, `sort`, `pid`, `gid`, `level`, `ismenu`, `icon`)
VALUES
	(1,'mc_admin','COOL后台',1,'sdfsdf',0,0,0,1,1,' icon-home'),
	(2,'Sysuser','后台用户',1,'',0,1,3,2,1,'icon-user'),
	(3,'index','用户列表',1,'',0,2,3,3,1,''),
	(4,'add','添加用户',1,'',0,3,3,3,1,''),
	(5,'edit','编辑用户',1,'',0,3,3,3,0,''),
	(6,'del','删除用户',1,'',0,3,3,3,0,''),
	(7,'changepwd','更改密码',1,'',0,3,3,3,0,''),
	(8,'Syspower','权限管理',1,'',1,1,3,2,1,'icon-asterisk'),
	(9,'menusort','菜单排序',1,'',0,8,3,3,1,''),
	(10,'group','权限分组',1,'',1,8,3,3,1,''),
	(11,'groupinfo','添加/编辑分组',1,'',0,10,3,3,0,''),
	(12,'groupdel','删除分组',1,'',0,10,3,3,0,''),
	(13,'groupopers','分组的其他操作',1,'',0,10,3,3,0,''),
	(14,'nodelist','权限列表',1,'',2,8,3,3,1,''),
	(15,'nodeadd','添加权限节点',1,'',0,14,3,3,1,''),
	(16,'nodeedit','编辑权限节点',1,'',0,14,3,3,0,''),
	(17,'nodedel','删除权限节点',1,'',0,14,3,3,0,''),
	(18,'nodeopers','节点其他操作',1,'',0,14,3,3,0,''),
	(19,'rolelist','角色列表',1,'',3,8,3,3,1,''),
	(20,'roleadd','添加角色',1,'',0,19,3,3,1,''),
	(21,'roleedit','编辑角色',1,'',0,19,3,3,0,''),
	(22,'roledel','删除角色',1,'',0,19,3,3,0,''),
	(23,'Sysdepart','部门管理',1,'',2,1,3,2,1,'icon-group'),
	(24,'index','部门列表',1,'',0,23,3,3,1,''),
	(25,'add','添加部门',1,'',0,24,3,3,0,''),
	(26,'edit','编辑部门',1,'',0,24,3,3,0,''),
	(27,'del','删除部门',1,'',0,24,3,3,0,''),
	(28,'Index','后台首页',1,'',0,1,1,2,1,'icon-home'),
	(29,'index','后台首页',1,'',0,28,1,3,1,'');

/*!40000 ALTER TABLE `mc_admin_node` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mc_admin_node_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mc_admin_node_group`;

CREATE TABLE `mc_admin_node_group` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '组名',
  `icon` varchar(32) NOT NULL DEFAULT '' COMMENT '图标',
  `link` varchar(32) NOT NULL DEFAULT '' COMMENT '分组默认链接',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='菜单分组表';

LOCK TABLES `mc_admin_node_group` WRITE;
/*!40000 ALTER TABLE `mc_admin_node_group` DISABLE KEYS */;

INSERT INTO `mc_admin_node_group` (`id`, `name`, `icon`, `link`, `sort`)
VALUES
	(1,'首页','icon-home','index/index',0),
	(2,'用户中心','icon-user','member/index',1),
	(3,'系统设置','icon-cog','Sysuser/index',8);

/*!40000 ALTER TABLE `mc_admin_node_group` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mc_admin_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mc_admin_role`;

CREATE TABLE `mc_admin_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(20) NOT NULL COMMENT '角色名',
  `pid` smallint(6) NOT NULL DEFAULT '0' COMMENT '父ID（未使用）',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态，0禁用，1启用',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注说明',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色表';

LOCK TABLES `mc_admin_role` WRITE;
/*!40000 ALTER TABLE `mc_admin_role` DISABLE KEYS */;

INSERT INTO `mc_admin_role` (`id`, `name`, `pid`, `status`, `remark`)
VALUES
	(3,'普通成员',0,1,'查看权限'),
	(2,'ROOT',0,1,'超级管理员');

/*!40000 ALTER TABLE `mc_admin_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mc_admin_role_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mc_admin_role_user`;

CREATE TABLE `mc_admin_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL COMMENT '角色ID',
  `user_id` char(32) DEFAULT NULL COMMENT '用户ID',
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色用户表';




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
