/*
SQLyog Enterprise - MySQL GUI v7.11 
MySQL - 5.0.37-community-nt : Database - happyfactory
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`happyfactory` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `happyfactory`;

/*Table structure for table `game_advices` */

DROP TABLE IF EXISTS `game_advices`;

CREATE TABLE `game_advices` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `content` varchar(50) NOT NULL default '',
  `dateline` int(10) NOT NULL default '0',
  `reply` varchar(50) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `game_advices` */

insert  into `game_advices`(`id`,`uid`,`content`,`dateline`,`reply`,`status`) values (9,397329017,'任务系统好像还没有做好？希望你们尽快完善！',1252294573,'我们正在抓紧时间开发任务系统，感谢您对我们的支持！',1),(11,397329017,'不是吧？',1252304327,'',0);

/*Table structure for table `game_changelogs` */

DROP TABLE IF EXISTS `game_changelogs`;

CREATE TABLE `game_changelogs` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(15) NOT NULL default '',
  `content` varchar(50) NOT NULL default '',
  `dateline` int(10) NOT NULL default '0',
  `hits` int(10) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `game_changelogs` */

insert  into `game_changelogs`(`id`,`title`,`content`,`dateline`,`hits`) values (1,'[已修正]网站更新说明','修复了玩家升级时经验更新不合理的问题',0,5),(4,'玩家升级Bug修正','调整为更加合理的算法',1252135176,0),(5,'研发中心成功并入游戏系统','通过研发中心可以提升您的生产效率和有效的控制生产成本！',1252294909,0);

/*Table structure for table `game_factorylevel` */

DROP TABLE IF EXISTS `game_factorylevel`;

CREATE TABLE `game_factorylevel` (
  `factid` int(10) NOT NULL default '0',
  `level` int(10) NOT NULL default '0',
  `price` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

/*Data for the table `game_factorylevel` */

insert  into `game_factorylevel`(`factid`,`level`,`price`) values (1,1,1500),(1,2,2600),(1,3,3800),(1,4,12000),(2,1,1900),(2,2,3400),(2,3,5600),(2,4,14500),(3,4,26000),(3,3,4900),(3,2,3700),(3,1,2500),(3,5,120000);

/*Table structure for table `game_factorys` */

DROP TABLE IF EXISTS `game_factorys`;

CREATE TABLE `game_factorys` (
  `factid` int(11) NOT NULL auto_increment,
  `factoryname` varchar(15) NOT NULL default '',
  `level` int(10) NOT NULL default '1',
  `pic` varchar(30) NOT NULL default '',
  `pic2` varchar(30) NOT NULL default '',
  `makecateid` int(2) NOT NULL default '0',
  PRIMARY KEY  (`factid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `game_factorys` */

insert  into `game_factorys`(`factid`,`factoryname`,`level`,`pic`,`pic2`,`makecateid`) values (1,'饮食生产线',1,'sp.gif','sp_act.gif',1),(2,'服饰生产线',1,'fs.gif','fs_act.gif',2),(3,'金属生产线',1,'js.gif','js_act.gif',3);

/*Table structure for table `game_friends` */

DROP TABLE IF EXISTS `game_friends`;

CREATE TABLE `game_friends` (
  `uid` int(11) NOT NULL default '0',
  `fuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`fuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_friends` */

insert  into `game_friends`(`uid`,`fuid`) values (88520154,397329017),(397329017,88520154),(397329017,498780579);

/*Table structure for table `game_invites` */

DROP TABLE IF EXISTS `game_invites`;

CREATE TABLE `game_invites` (
  `uid` int(11) NOT NULL default '0',
  `inviter` int(11) NOT NULL default '0',
  `dateline` int(10) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`inviter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_invites` */

insert  into `game_invites`(`uid`,`inviter`,`dateline`,`status`) values (118513417,397329017,1252306674,0),(475609002,397329017,1252306674,0),(465504959,397329017,1252306674,0),(368824282,397329017,1252306674,0),(470579623,397329017,1252306674,0),(533954189,397329017,1252306622,0),(529134715,397329017,1252306622,0),(510045774,397329017,1252306674,1);

/*Table structure for table `game_makelog` */

DROP TABLE IF EXISTS `game_makelog`;

CREATE TABLE `game_makelog` (
  `id` int(11) NOT NULL auto_increment,
  `makecateid` int(10) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `pid` int(10) NOT NULL default '0',
  `productname` varchar(15) NOT NULL default '',
  `count` int(10) NOT NULL default '0',
  `starttime` int(10) NOT NULL default '0',
  `endtime` int(10) NOT NULL default '0',
  `dateline` int(10) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Data for the table `game_makelog` */

insert  into `game_makelog`(`id`,`makecateid`,`uid`,`pid`,`productname`,`count`,`starttime`,`endtime`,`dateline`) values (8,1,397329017,82,'葡萄酒',2,1252040801,1252040811,1252040820),(3,3,397329017,53,'银手镯',3,1252028433,1252028448,1252040191),(4,1,397329017,87,'鱼片',1,1252040220,1252040225,1252040229),(5,3,397329017,33,'水果刀',1,1252028349,1252034960,1252040668),(6,3,397329017,54,'银挂坠',2,1252040654,1252040664,1252040672),(7,1,397329017,78,'蛋糕',1,1252028254,1252028259,1252040676),(9,3,397329017,58,'金挂坠',1,1252045153,1252045158,1252045164),(10,3,397329017,56,'银锁',1,1252050860,1252050865,1252050870),(11,3,397329017,54,'银挂坠',5,1252050853,1252050878,1252050881),(12,3,397329017,55,'银水杯',15,1252052165,1252052170,1252054647),(13,3,397329017,60,'金牛',20,1252054842,1252054942,1252054968),(14,1,397329017,86,'肉罐头',1,1252055002,1252055007,1252055010),(15,3,397329017,35,'水壶',13,1252055643,1252055708,1252055719),(16,2,397329017,69,'真丝衬衫',1,1252295159,1252295164,1252295260),(17,3,397329017,57,'金戒指',5,1252313151,1252313176,1252313182),(18,3,397329017,57,'金戒指',10,1252314275,1252314325,1252314329);

/*Table structure for table `game_makeprocess` */

DROP TABLE IF EXISTS `game_makeprocess`;

CREATE TABLE `game_makeprocess` (
  `id` int(11) NOT NULL auto_increment,
  `makecateid` int(10) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `pid` int(10) NOT NULL default '0',
  `count` int(10) NOT NULL default '0',
  `starttime` int(10) NOT NULL default '0',
  `endtime` int(10) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Data for the table `game_makeprocess` */

/*Table structure for table `game_makes` */

DROP TABLE IF EXISTS `game_makes`;

CREATE TABLE `game_makes` (
  `makeid` int(11) NOT NULL auto_increment,
  `pid` int(10) NOT NULL default '0',
  `items` varchar(50) character set gbk NOT NULL default '',
  `maketime` int(10) NOT NULL default '60',
  `makecateid` int(2) NOT NULL default '0',
  PRIMARY KEY  (`makeid`),
  UNIQUE KEY `make_id` (`makeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `game_makes` */

insert  into `game_makes`(`makeid`,`pid`,`items`,`maketime`,`makecateid`) values (1,90,'51:3',5,1),(2,79,'49:1',5,1),(3,78,'48:2',5,1),(4,77,'48:2',5,1),(5,76,'48:2',5,1),(6,75,'48:1',5,1),(7,80,'49:1',5,1),(8,81,'49:2',5,1),(9,89,'51:3',5,1),(10,88,'51:2',5,1),(11,87,'51:1',5,1),(12,86,'50:3',5,1),(13,85,'50:2',5,1),(14,84,'50:2',5,1),(15,83,'50:1',5,1),(16,82,'49:4',5,1),(17,33,'40:1',5,3),(18,34,'40:2',5,3),(19,35,'40:1',5,3),(20,36,'40:3',5,3),(21,37,'41:1',5,3),(22,38,'41:2',5,3),(23,39,'41:2',5,3),(24,52,'41:3',5,3),(25,53,'42:1',5,3),(26,54,'42:1',5,3),(27,55,'42:2',5,3),(28,56,'42:2',5,3),(29,57,'43:1',5,3),(30,58,'43:1',5,3),(31,59,'43:2',5,3),(32,60,'43:3',5,3),(33,31,'44:2',5,2),(34,32,'44:1',5,2),(35,61,'44:3',5,2),(36,62,'44:3',5,2),(37,63,'45:2',5,2),(38,64,'45:1',5,2),(39,65,'45:3',5,2),(40,66,'45:3',5,2),(41,67,'46:1',5,2),(42,68,'46:3',5,2),(43,69,'46:2',5,2),(44,70,'46:4',5,0),(45,71,'47:1',5,2),(46,72,'47:2',5,2),(47,73,'47:3',5,2),(48,74,'47:4',5,2);

/*Table structure for table `game_marketlog` */

DROP TABLE IF EXISTS `game_marketlog`;

CREATE TABLE `game_marketlog` (
  `id` int(11) NOT NULL auto_increment,
  `suid` int(11) NOT NULL default '0',
  `buid` int(11) NOT NULL default '0',
  `pid` int(10) NOT NULL default '0',
  `price` int(10) NOT NULL default '0',
  `count` int(10) NOT NULL default '0',
  `dateline` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`,`suid`,`buid`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `game_marketlog` */

insert  into `game_marketlog`(`id`,`suid`,`buid`,`pid`,`price`,`count`,`dateline`) values (1,397329017,397329017,47,50,2,1251789977),(2,88520154,397329017,57,60,1,1251858018),(3,88520154,397329017,57,60,1,1251858262),(4,397329017,397329017,47,50,1,1251858274),(5,397329017,397329017,51,36,1,1251858292),(6,88520154,397329017,57,60,1,1251858298),(7,88520154,397329017,57,60,2,1252315781);

/*Table structure for table `game_markets` */

DROP TABLE IF EXISTS `game_markets`;

CREATE TABLE `game_markets` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `pid` int(10) NOT NULL default '0',
  `price` int(10) NOT NULL default '0',
  `count` int(10) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `dateline` int(10) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`pid`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `game_markets` */

insert  into `game_markets`(`id`,`uid`,`username`,`pid`,`price`,`count`,`status`,`dateline`) values (5,88520154,'格子被单',57,60,7,0,1251787255),(7,397329017,'wxg19861007',57,420,6,0,1252315863);

/*Table structure for table `game_productcategory` */

DROP TABLE IF EXISTS `game_productcategory`;

CREATE TABLE `game_productcategory` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `game_productcategory` */

insert  into `game_productcategory`(`id`,`category`) values (1,'食品'),(2,'服饰'),(3,'金属');

/*Table structure for table `game_products` */

DROP TABLE IF EXISTS `game_products`;

CREATE TABLE `game_products` (
  `pid` int(11) NOT NULL auto_increment,
  `cateid` int(3) NOT NULL default '0',
  `productname` varchar(15) NOT NULL default '' COMMENT '商品名称',
  `pic` varchar(30) NOT NULL default '',
  `level` int(3) NOT NULL default '1' COMMENT '等级',
  `price` int(10) NOT NULL default '0',
  `mode` tinyint(1) NOT NULL default '0',
  `remark` varchar(50) NOT NULL default '' COMMENT '备注',
  PRIMARY KEY  (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk;

/*Data for the table `game_products` */

insert  into `game_products`(`pid`,`cateid`,`productname`,`pic`,`level`,`price`,`mode`,`remark`) values (31,2,'麻布包','mabubao.jpg',1,35,1,''),(32,2,'麻布凉鞋','mabuliangxie.jpg',1,30,1,''),(33,3,'水果刀','shuiguodao.jpg',1,20,1,''),(34,3,'铁锹','tieqiao.jpg',1,40,1,''),(35,3,'水壶','shuihu.jpg',1,40,1,''),(36,3,'铁锅','guo.jpg',1,80,1,''),(37,3,'铜锁','suo.jpg',1,50,1,''),(38,3,'铜拉手','tonglashou.jpg',1,90,1,''),(39,3,'铜锣','tongluo.jpg',1,100,1,''),(40,3,'铁','tie.jpg',1,10,0,''),(41,3,'铜','tong.jpg',1,20,0,''),(42,3,'银','yin.jpg',1,50,0,''),(43,3,'金','jin.jpg',1,100,0,''),(44,2,'麻布','mabu.jpg',1,10,0,''),(45,2,'棉布','mianbu.jpg',1,15,0,''),(46,2,'丝绸','sichou.jpg',1,30,0,''),(47,2,'皮革','pige.jpg',1,50,0,''),(48,1,'面粉','mianfen.jpg',1,5,0,''),(49,1,'水果','shuiguo.jpg',1,10,0,''),(50,1,'肉','rou.jpg',1,20,0,''),(51,1,'鱼','yu.jpg',1,30,0,''),(52,3,'铜壶','tonghu.jpg',1,140,1,''),(53,3,'银手镯','yinshouzhuo.jpg',1,150,1,''),(54,3,'银挂坠','yinguazhui.jpg',1,170,1,''),(55,3,'银水杯','yinshuibei.jpg',1,300,1,''),(56,3,'银锁','yinsuo.jpg',1,380,1,''),(57,3,'金戒指','jinjiezhi.jpg',1,420,1,''),(58,3,'金挂坠','jinguazhui.jpg',1,500,1,''),(59,3,'金手链','jinshoulian.jpg',1,800,1,''),(60,3,'金牛','jinniu.jpg',1,1500,1,''),(61,2,'亚麻衬衫','yamachenshan.jpg',1,60,1,''),(62,2,'亚麻裤子','yamakuzi.jpg',1,70,1,''),(63,2,'帽子','maozi.jpg',1,50,1,''),(64,2,'鞋子','xiezi.jpg',1,45,1,''),(65,2,'全棉衬衫','chenshan.jpg',1,105,1,''),(66,2,'全棉西裤','xiku.jpg',1,125,1,''),(67,2,'丝巾','sijin.jpg',1,110,1,''),(68,2,'真丝裙子','qunzi.jpg',1,290,1,''),(69,2,'真丝衬衫','zhensichenshan.jpg',1,340,1,''),(70,2,'真丝旗袍','qipao.jpg',1,520,1,''),(71,2,'皮鞋','pixie.jpg',1,250,1,''),(72,2,'皮包','pibao.jpg',1,380,1,''),(73,2,'皮裤','piku.jpg',1,650,1,''),(74,2,'皮衣','piyi.jpg',1,1000,1,''),(75,1,'饼干','binggan.jpg',1,10,1,''),(76,1,'方便面','fangbianmian.jpg',1,20,1,''),(77,1,'面包','mianbao.jpg',1,25,1,''),(78,1,'蛋糕','dangao.jpg',1,30,1,''),(79,1,'果冻','guodong.jpg',1,20,1,''),(80,1,'果脯','guopu.jpg',1,25,1,''),(81,1,'水果罐头','shuiguoguantou.jpg',1,50,1,''),(82,1,'葡萄酒','putaojiu.jpg',1,240,1,''),(83,1,'肉松','rousong.jpg',1,100,1,''),(84,1,'肉脯','roupu.jpg',1,160,1,''),(85,1,'肉干','rougan.jpg',1,240,1,''),(86,1,'肉罐头','guantou.jpg',1,340,1,''),(87,1,'鱼片','kaoyupian.jpg',1,330,1,''),(88,1,'鱼子酱','yuzijiang.jpg',1,460,1,''),(89,1,'鱼肝油','yuganyou.jpg',1,570,1,''),(90,1,'鱼翅','yuchi.jpg',1,690,1,'');

/*Table structure for table `game_studylevel` */

DROP TABLE IF EXISTS `game_studylevel`;

CREATE TABLE `game_studylevel` (
  `elevel` int(3) NOT NULL default '0',
  `eprice` int(10) NOT NULL default '0',
  `clevel` int(3) NOT NULL default '0',
  `cprice` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_studylevel` */

insert  into `game_studylevel`(`elevel`,`eprice`,`clevel`,`cprice`) values (1,3500,1,3500),(2,4800,2,4800),(3,5900,3,5900),(4,12000,4,12000),(5,30000,5,30000);

/*Table structure for table `game_studys` */

DROP TABLE IF EXISTS `game_studys`;

CREATE TABLE `game_studys` (
  `efficiency` int(5) NOT NULL default '0',
  `cost` int(5) NOT NULL default '0',
  `level` int(5) NOT NULL default '0',
  `pic` varchar(20) NOT NULL default '',
  `remark` varchar(30) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_studys` */

insert  into `game_studys`(`efficiency`,`cost`,`level`,`pic`,`remark`) values (95,95,1,'',''),(80,80,2,'',''),(75,75,3,'',''),(70,70,4,'',''),(65,65,5,'','');

/*Table structure for table `game_tasklog` */

DROP TABLE IF EXISTS `game_tasklog`;

CREATE TABLE `game_tasklog` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `taskid` int(10) NOT NULL default '0',
  `name` varchar(15) NOT NULL default '',
  `reward` int(10) NOT NULL default '0',
  `dateline` int(10) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `game_tasklog` */

insert  into `game_tasklog`(`id`,`uid`,`taskid`,`name`,`reward`,`dateline`) values (1,397329017,2,'全民健康饮食',2577,1252314229),(2,397329017,1,'情人节的礼物',1354,1252314342),(3,397329017,2,'全民健康饮食',2324,1252315112),(4,397329017,1,'情人节的礼物',1143,1252315183),(5,397329017,2,'全民健康饮食',3096,1252315187),(6,397329017,2,'全民健康饮食',3038,1252315190),(7,397329017,1,'情人节的礼物',1477,1252371277);

/*Table structure for table `game_tasks` */

DROP TABLE IF EXISTS `game_tasks`;

CREATE TABLE `game_tasks` (
  `taskid` int(11) NOT NULL auto_increment,
  `name` varchar(15) NOT NULL default '',
  `description` varchar(50) NOT NULL default '',
  `pic` varchar(30) NOT NULL default '',
  `level` int(3) NOT NULL default '0',
  `items` varchar(30) NOT NULL default '',
  `rewardmin` int(10) NOT NULL default '1000',
  `rewardmax` int(10) NOT NULL default '1500',
  PRIMARY KEY  (`taskid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `game_tasks` */

insert  into `game_tasks`(`taskid`,`name`,`description`,`pic`,`level`,`items`,`rewardmin`,`rewardmax`) values (1,'情人节的礼物','收集 2 个<strong>金戒指</strong> 可获得金钱奖励！','',1,'57:2',1000,1500),(2,'全民健康饮食','收集 5 个<strong>鱼</strong> 可获得金钱奖励！','',1,'51:5',2200,3100);

/*Table structure for table `game_userfactory` */

DROP TABLE IF EXISTS `game_userfactory`;

CREATE TABLE `game_userfactory` (
  `factid` int(10) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `level` int(10) NOT NULL default '1',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`factid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_userfactory` */

insert  into `game_userfactory`(`factid`,`uid`,`level`,`status`) values (1,397329017,2,0),(3,397329017,5,0),(2,397329017,3,0);

/*Table structure for table `game_userlevel` */

DROP TABLE IF EXISTS `game_userlevel`;

CREATE TABLE `game_userlevel` (
  `level` int(10) NOT NULL default '0',
  `empiric` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_userlevel` */

insert  into `game_userlevel`(`level`,`empiric`) values (1,10),(2,35),(3,60),(4,73),(5,88),(6,96),(7,120),(8,145),(9,169),(10,282),(11,20),(12,210),(13,360),(14,490),(15,570),(16,680),(17,0),(18,0),(19,0),(20,0),(21,0),(22,0),(23,0),(24,0),(25,0),(26,0),(27,0),(28,0),(29,0),(30,0);

/*Table structure for table `game_userproperty` */

DROP TABLE IF EXISTS `game_userproperty`;

CREATE TABLE `game_userproperty` (
  `uid` int(11) NOT NULL default '0',
  `level` int(10) NOT NULL default '1',
  `money` int(10) NOT NULL default '1000',
  `empiricnow` int(10) NOT NULL default '0',
  `empiric` int(10) NOT NULL default '10',
  `attack` int(10) NOT NULL default '0',
  `defense` int(10) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_userproperty` */

insert  into `game_userproperty`(`uid`,`level`,`money`,`empiricnow`,`empiric`,`attack`,`defense`) values (397329017,12,28831,55,10,0,0);

/*Table structure for table `game_users` */

DROP TABLE IF EXISTS `game_users`;

CREATE TABLE `game_users` (
  `uid` int(11) NOT NULL,
  `uchid` int(11) NOT NULL default '0',
  `username` varchar(30) NOT NULL default '',
  `admin_level` varchar(10) NOT NULL default '',
  `siteid` int(10) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `updated` int(10) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_users` */

insert  into `game_users`(`uid`,`uchid`,`username`,`admin_level`,`siteid`,`status`,`updated`) values (397329017,1113812,'wxg19861007','USER',100,0,1251709604),(88520154,1113813,'格子被单','USER',100,0,1251709604),(498780579,1113814,'易品网络','USER',100,0,1251709604);

/*Table structure for table `game_userstudy` */

DROP TABLE IF EXISTS `game_userstudy`;

CREATE TABLE `game_userstudy` (
  `uid` int(11) NOT NULL default '0',
  `elevel` int(3) NOT NULL default '1',
  `clevel` int(3) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `game_userstudy` */

insert  into `game_userstudy`(`uid`,`elevel`,`clevel`) values (397329017,4,5);

/*Table structure for table `game_warehouses` */

DROP TABLE IF EXISTS `game_warehouses`;

CREATE TABLE `game_warehouses` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `pid` int(10) NOT NULL default '0',
  `count` int(10) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`pid`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

/*Data for the table `game_warehouses` */

insert  into `game_warehouses`(`id`,`uid`,`pid`,`count`) values (1,397329017,40,8),(2,397329017,48,27),(3,397329017,47,30),(30,397329017,57,2),(5,397329017,44,21),(29,397329017,51,2),(7,397329017,42,26),(8,397329017,89,12),(9,397329017,53,3),(10,397329017,87,1),(11,397329017,33,1),(12,397329017,54,7),(13,397329017,78,1),(15,397329017,82,2),(16,397329017,43,14),(17,397329017,58,1),(18,397329017,56,1),(19,397329017,50,22),(20,397329017,41,20),(21,397329017,45,20),(22,397329017,46,18),(23,397329017,49,20),(24,397329017,55,15),(28,397329017,69,1),(26,397329017,86,1),(27,397329017,35,13);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
