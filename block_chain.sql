/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 10.1.9-MariaDB : Database - test
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`test` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `test`;

/*Table structure for table `block_chain` */

DROP TABLE IF EXISTS `block_chain`;

CREATE TABLE `block_chain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sid` varchar(30) NOT NULL DEFAULT '' COMMENT '数据id',
  `sign` char(32) NOT NULL DEFAULT '' COMMENT '唯一标识',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '名称',
  `apply_num` varchar(50) NOT NULL DEFAULT '' COMMENT '申请号',
  `apply_date` varchar(50) NOT NULL DEFAULT '' COMMENT '申请日',
  `public_num` varchar(50) NOT NULL DEFAULT '' COMMENT '公开号',
  `public_date` varchar(50) NOT NULL DEFAULT '' COMMENT '公开日',
  `ipc_cate` varchar(50) NOT NULL DEFAULT '' COMMENT 'IPC主分类号',
  `ipc_cate_other` varchar(100) NOT NULL DEFAULT '' COMMENT 'IPC分类号',
  `applicant` varchar(100) NOT NULL DEFAULT '' COMMENT '申请人',
  `inventor` varchar(30) NOT NULL DEFAULT '' COMMENT '发明人',
  `agent` varchar(30) NOT NULL DEFAULT '' COMMENT '代理人',
  `digest` blob COMMENT '摘要',
  `agency` varchar(100) NOT NULL DEFAULT '' COMMENT '代理机构',
  `applicant_address` varchar(100) NOT NULL DEFAULT '' COMMENT '申请人地址',
  `applicant_zip` varchar(20) NOT NULL DEFAULT '' COMMENT '申请人邮编',
  `low_status` blob NOT NULL COMMENT '审核状态--法律状态',
  `cpc_cate` varchar(100) NOT NULL DEFAULT '' COMMENT 'CPC分类号',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) NOT NULL DEFAULT '0' COMMENT '申请时间',
  PRIMARY KEY (`id`),
  KEY `sign` (`sign`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='区块链-国家专利局表';

/*Data for the table `block_chain` */

LOCK TABLES `block_chain` WRITE;

insert  into `block_chain`(`id`,`sid`,`sign`,`title`,`apply_num`,`apply_date`,`public_num`,`public_date`,`ipc_cate`,`ipc_cate_other`,`applicant`,`inventor`,`agent`,`digest`,`agency`,`applicant_address`,`applicant_zip`,`low_status`,`cpc_cate`,`createtime`,`updatetime`) values (3,'CN201910025654.220190315FM','bb43af6ba8b6e872dd09b7aaa3237671','一种集群密钥生成方法、装置及其存储介质','CN201910025654','2019.01.10','CN109474430A','2019.03.15','H04L9/30','H04L9/30; H04L9/08; H04L9/32; H04L29/08','四川虹微技术有限公司;四川长虹电器股份有限公司;','康红娟;李琦;','','[{\"formatId\":null,\"indexCnName\":\"摘要\",\"indexCode\":\"ABVIEW\",\"indexEnName\":\"Abstract \",\"indexId\":\"IVDB078\",\"value\":\"<RESULT><table><tr><td class=\\\"content\\\" id=\\\"cpp_content_i0j3\\\"><business:Abstract xmlns:business=\\\"http:\\/\\/www.sipo.gov.cn\\/XMLSchema\\/business\\\" dataFormat=\\\"original\\\" lang=\\\"zh\\\" sourceDB=\\\"national office\\\" processingType=\\\"original\\\" creator=\\\"03\\\">\\n    <base:Paragraphs xmlns:base=\\\"http:\\/\\/www.sipo.gov.cn\\/XMLSchema\\/base\\\" num=\\\"0001\\\">本发明提供了一种集群密钥生成方法、装置及其存储介质，涉及区块链资源加密技术领域。该集群密钥生成方法包括：确定节点集群的集群权限，所述节点集群包括至少一个联盟链的链节点，所述集群权限用于表示所述节点集群是否有权限获取所述联盟链中的对应资源；基于所述集群权限生成所述节点集群中所有链节点共用的集群密钥对。该方法通过节点集群的集群权限生成集群密钥对，提供了联盟链的分布式环境下具有共同资源分享需求的高效率的密钥生成方式。<\\/base:Paragraphs>\\n    \\n<\\/business:Abstract><\\/td><\\/tr><\\/table><\\/RESULT>\"}]','','中国(四川)自由贸易试验区成都高新区天府四街199号1栋33层;','610000;','[{\"country\":\"CN\",\"id\":null,\"lawStateCNMeaning\":\"发明专利申请公布\",\"lawStateCode\":\"zlgk\",\"lawStateENMeaning\":null,\"lawStateExtendMeaning\":null,\"nrdAn\":\"CN201910025654\",\"nrdPn\":null,\"prsDate\":\"20190315\"}]','H04L9/3268;H04L9/3066;H04L9/0869;H04L9/0861;H04L9/0891;H04L67/1044',1553156553,1553156553),(4,'CN201811306195.720190308FM','58b1ab6d3918fc644d6f565ab5657b4f','数字银行的总分双链的弱中心化可信数据管理系统及方法','CN201811306195','2019.01.25','CN109447807A','2019.03.08','G06Q40/04','G06Q40/04; G06Q40/02; G06Q20/38','杨城;成都九宽科技有限公司;','杨城;','','[{\"formatId\":null,\"indexCnName\":\"摘要\",\"indexCode\":\"ABVIEW\",\"indexEnName\":\"Abstract \",\"indexId\":\"IVDB078\",\"value\":\"<RESULT><table><tr><td class=\\\"content\\\" id=\\\"cpp_content_i0j3\\\"><business:Abstract xmlns:business=\\\"http:\\/\\/www.sipo.gov.cn\\/XMLSchema\\/business\\\" dataFormat=\\\"original\\\" lang=\\\"zh\\\" sourceDB=\\\"national office\\\" processingType=\\\"original\\\" creator=\\\"03\\\">\\n    <base:Paragraphs xmlns:base=\\\"http:\\/\\/www.sipo.gov.cn\\/XMLSchema\\/base\\\" num=\\\"0001\\\">本发明公开了一种数字银行的总分双链的弱中心化可信数据管理系统及方法，该方法包括：将所有交易流水依据交易主体和交易时间交织成一张有向交易网；将所述有向交易网分别从横竖两个方向切割成一条总账链和若干条分户账链；将所有分户账链的链头信息同步并入总账链的区块中，从而将所述总账链和分户账链合并成一条BC链；通过链头验证和交易验证对相关交易和账户信息进行查验，确保查验信息的真实性。本发明通过总分双链结构，在维持中心对数据集中存储和管理的前提下，保证数据公开透明，具有区块链的不可篡改性和可追溯性特征，并使得所有储户都是监督人，间接实施对系统整体账务数据的审计，从而避免银行篡改数据、记假账的可能。<\\/base:Paragraphs>\\n    \\n<\\/business:Abstract><\\/td><\\/tr><\\/table><\\/RESULT>\"}]','','四川省成都市成华区双华路3号65栋25号;','611130;','[{\"country\":\"CN\",\"id\":null,\"lawStateCNMeaning\":\"发明专利申请公布\",\"lawStateCode\":\"zlgk\",\"lawStateENMeaning\":null,\"lawStateExtendMeaning\":null,\"nrdAn\":\"CN201811306195\",\"nrdPn\":null,\"prsDate\":\"20190308\"}]','G06Q20/3825;G06Q20/3829;G06Q40/04;G06Q40/02',1553156788,1553156788),(5,'CN201910014247.120190312FM','96653b4faa280a942f439bcc5a2c77a8','终端连接基站的方法、设备及存储介质','CN201910014247','2019.01.08','CN109462849A','2019.03.12','H04W12/06','H04W12/06; H04W12/12; H04W76/10','中国联合网络通信集团有限公司;','田新雪;肖征荣;马书惠;','','[{\"formatId\":null,\"indexCnName\":\"摘要\",\"indexCode\":\"ABVIEW\",\"indexEnName\":\"Abstract \",\"indexId\":\"IVDB078\",\"value\":\"<RESULT><table><tr><td class=\\\"content\\\" id=\\\"cpp_content_i0j3\\\"><business:Abstract xmlns:business=\\\"http:\\/\\/www.sipo.gov.cn\\/XMLSchema\\/business\\\" dataFormat=\\\"original\\\" lang=\\\"zh\\\" sourceDB=\\\"national office\\\" processingType=\\\"original\\\" creator=\\\"03\\\">\\n    <base:Paragraphs xmlns:base=\\\"http:\\/\\/www.sipo.gov.cn\\/XMLSchema\\/base\\\" num=\\\"0001\\\">本发明提供一种终端连接基站的方法、设备及存储介质，通过终端获取待连接基站的基站信息；终端从区块链网络的账本中获取合法基站信息列表，并根据所述待连接基站的基站信息以及合法基站信息列表判断待连接基站是否合法；若合法，则终端可与待连接基站进行连接。本发明中可在终端连接基站前进行基站的合法性判断，保障了终端连接基站的安全性和可靠性，避免了终端连接到伪基站，进而避免终端连接到伪基站后接收到垃圾短信或终端的用户信息被盗取。<\\/base:Paragraphs>\\n    \\n<\\/business:Abstract><\\/td><\\/tr><\\/table><\\/RESULT>\"}]','','北京市西城区金融大街21号;','100033;','[{\"country\":\"CN\",\"id\":null,\"lawStateCNMeaning\":\"发明专利申请公布\",\"lawStateCode\":\"zlgk\",\"lawStateENMeaning\":null,\"lawStateExtendMeaning\":null,\"nrdAn\":\"CN201910014247\",\"nrdPn\":null,\"prsDate\":\"20190312\"}]','H04W12/06;H04W12/12;H04W76/10',1553157514,1553157514);

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
