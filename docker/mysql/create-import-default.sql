-- MySQL dump 10.13  Distrib 5.7.35, for osx10.16 (x86_64)
--
-- Host: 127.0.0.1    Database: fdp
-- ------------------------------------------------------
-- Server version	5.7.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `fdp`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `fdp` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `fdp`;

--
-- Table structure for table `affiliation`
--

DROP TABLE IF EXISTS `affiliation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliation` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `person` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `organization` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `department` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EA72153034DCD176` (`person`),
  KEY `IDX_EA721530C1EE637C` (`organization`),
  KEY `IDX_EA721530CD1DE18A` (`department`),
  CONSTRAINT `FK_EA72153034DCD176` FOREIGN KEY (`person`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_EA721530C1EE637C` FOREIGN KEY (`organization`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_EA721530CD1DE18A` FOREIGN KEY (`department`) REFERENCES `department` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliation`
--

/*!40000 ALTER TABLE `affiliation` DISABLE KEYS */;
/*!40000 ALTER TABLE `affiliation` ENABLE KEYS */;

--
-- Table structure for table `agent`
--

DROP TABLE IF EXISTS `agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agent` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dtype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent`
--

/*!40000 ALTER TABLE `agent` DISABLE KEYS */;
/*!40000 ALTER TABLE `agent` ENABLE KEYS */;

--
-- Table structure for table `annotation`
--

DROP TABLE IF EXISTS `annotation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annotation` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `entity` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `concept` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  KEY `IDX_2E443EF2E284468` (`entity`),
  KEY `IDX_2E443EF2E74A6050` (`concept`),
  CONSTRAINT `FK_2E443EF2E284468` FOREIGN KEY (`entity`) REFERENCES `castor_entity` (`id`),
  CONSTRAINT `FK_2E443EF2E74A6050` FOREIGN KEY (`concept`) REFERENCES `ontology_concept` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annotation`
--

/*!40000 ALTER TABLE `annotation` DISABLE KEYS */;
/*!40000 ALTER TABLE `annotation` ENABLE KEYS */;

--
-- Table structure for table `castor_entity`
--

DROP TABLE IF EXISTS `castor_entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `castor_entity` (
  `id` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `parent` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structure_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:StructureType)',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_90006568E7B003E9` (`study_id`),
  KEY `IDX_900065683D8E604F` (`parent`),
  CONSTRAINT `FK_900065683D8E604F` FOREIGN KEY (`parent`) REFERENCES `castor_entity` (`id`),
  CONSTRAINT `FK_90006568E7B003E9` FOREIGN KEY (`study_id`) REFERENCES `study_castor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `castor_entity`
--

/*!40000 ALTER TABLE `castor_entity` DISABLE KEYS */;
/*!40000 ALTER TABLE `castor_entity` ENABLE KEYS */;

--
-- Table structure for table `castor_institute`
--

DROP TABLE IF EXISTS `castor_institute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `castor_institute` (
  `id` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `country` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institute_name` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`,`study_id`),
  KEY `IDX_737F0857E7B003E9` (`study_id`),
  KEY `IDX_737F08575373C966` (`country`),
  CONSTRAINT `FK_737F08575373C966` FOREIGN KEY (`country`) REFERENCES `country` (`code`),
  CONSTRAINT `FK_737F0857E7B003E9` FOREIGN KEY (`study_id`) REFERENCES `study_castor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `castor_institute`
--

/*!40000 ALTER TABLE `castor_institute` DISABLE KEYS */;
/*!40000 ALTER TABLE `castor_institute` ENABLE KEYS */;

--
-- Table structure for table `castor_record`
--

DROP TABLE IF EXISTS `castor_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `castor_record` (
  `record_id` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `institute_id` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_on` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`record_id`,`study_id`),
  KEY `IDX_51CBE91E7B003E9` (`study_id`),
  KEY `IDX_51CBE91697B0F4CE7B003E9` (`institute_id`,`study_id`),
  CONSTRAINT `FK_51CBE91697B0F4CE7B003E9` FOREIGN KEY (`institute_id`, `study_id`) REFERENCES `castor_institute` (`id`, `study_id`),
  CONSTRAINT `FK_51CBE91E7B003E9` FOREIGN KEY (`study_id`) REFERENCES `study_castor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `castor_record`
--

/*!40000 ALTER TABLE `castor_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `castor_record` ENABLE KEYS */;

--
-- Table structure for table `castor_server`
--

DROP TABLE IF EXISTS `castor_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `castor_server` (
  `id` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:iri)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `castor_server`
--

/*!40000 ALTER TABLE `castor_server` DISABLE KEYS */;
INSERT INTO `castor_server` (`id`, `url`, `name`, `flag`, `default`) VALUES (1,'https://main.qa.castoredc.org','Main QA (Netherlands)','nl',1);
/*!40000 ALTER TABLE `castor_server` ENABLE KEYS */;

--
-- Table structure for table `castor_user`
--

DROP TABLE IF EXISTS `castor_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `castor_user` (
  `id` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `name_first` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_middle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_last` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_50208F68A76ED395` (`user_id`),
  CONSTRAINT `FK_50208F68A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `castor_user`
--

/*!40000 ALTER TABLE `castor_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `castor_user` ENABLE KEYS */;

--
-- Table structure for table `catalog`
--

DROP TABLE IF EXISTS `catalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `catalog` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `fdp` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accept_submissions` tinyint(1) NOT NULL,
  `submission_accesses_data` tinyint(1) NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_1B2C3247E28FB11F` (`fdp`),
  KEY `IDX_1B2C3247DE12AB56` (`created_by`),
  KEY `IDX_1B2C324716FE72E1` (`updated_by`),
  KEY `slug` (`slug`),
  CONSTRAINT `FK_1B2C324716FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_1B2C3247DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_1B2C3247E28FB11F` FOREIGN KEY (`fdp`) REFERENCES `fdp` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalog`
--

/*!40000 ALTER TABLE `catalog` DISABLE KEYS */;
/*!40000 ALTER TABLE `catalog` ENABLE KEYS */;

--
-- Table structure for table `catalogs_datasets`
--

DROP TABLE IF EXISTS `catalogs_datasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `catalogs_datasets` (
  `catalog_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `dataset_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`catalog_id`,`dataset_id`),
  KEY `IDX_1CF6F11DCC3C66FC` (`catalog_id`),
  KEY `IDX_1CF6F11DD47C2D1B` (`dataset_id`),
  CONSTRAINT `FK_1CF6F11DCC3C66FC` FOREIGN KEY (`catalog_id`) REFERENCES `catalog` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1CF6F11DD47C2D1B` FOREIGN KEY (`dataset_id`) REFERENCES `dataset` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalogs_datasets`
--

/*!40000 ALTER TABLE `catalogs_datasets` DISABLE KEYS */;
/*!40000 ALTER TABLE `catalogs_datasets` ENABLE KEYS */;

--
-- Table structure for table `catalogs_studies`
--

DROP TABLE IF EXISTS `catalogs_studies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `catalogs_studies` (
  `catalog_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `study_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`catalog_id`,`study_id`),
  KEY `IDX_E1F354EBCC3C66FC` (`catalog_id`),
  KEY `IDX_E1F354EBE7B003E9` (`study_id`),
  CONSTRAINT `FK_E1F354EBCC3C66FC` FOREIGN KEY (`catalog_id`) REFERENCES `catalog` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E1F354EBE7B003E9` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalogs_studies`
--

/*!40000 ALTER TABLE `catalogs_studies` DISABLE KEYS */;
/*!40000 ALTER TABLE `catalogs_studies` ENABLE KEYS */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `code` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `castor_country_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tld` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code`),
  KEY `castorCountryId` (`castor_country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` (`code`, `name`, `castor_country_id`, `abbreviation`, `tld`) VALUES ('AD','Andorra','8','AND','.ad'),('AE','United Arab Emirates','9','ARE','.ae'),('AF','Afghanistan','3','AFG','.af'),('AG','Antigua and Barbuda','15','ATG','.ag'),('AI','Anguilla','5','AIA','.ai'),('AL','Albania','7','ALB','.al'),('AM','Armenia','11','ARM','.am'),('AO','Angola','4','AGO','.ao'),('AQ','Antarctica','13','ATA','.aq'),('AR','Argentina','10','ARG','.ar'),('AS','American Samoa','12','ASM','.as'),('AT','Austria','17','AUT','.at'),('AU','Australia','16','AUS','.au'),('AW','Aruba','2','ABW','.aw'),('AX','Aland Islands','6','ALA','.ax'),('AZ','Azerbaijan','18','AZE','.az'),('BA','Bosnia and Herzegovina','28','BIH','.ba'),('BB','Barbados','35','BRB','.bb'),('BD','Bangladesh','24','BGD','.bd'),('BE','Belgium','20','BEL','.be'),('BF','Burkina Faso','23','BFA','.bf'),('BG','Bulgaria','25','BGR','.bg'),('BH','Bahrain','26','BHR','.bh'),('BI','Burundi','19','BDI','.bi'),('BJ','Benin','21','BEN','.bj'),('BL','Saint Bartholemy','29','BLM','.bl'),('BM','Bermuda','32','BMU','.bm'),('BN','Brunei','36','BRN','.bn'),('BO','Bolivia','33','BOL','.bo'),('BQ','Bonaire','22','BES','.an,.nl'),('BR','Brazil','34','BRA','.br'),('BS','Bahamas','27','BHS','.bs'),('BT','Bhutan','37','BTN','.bt'),('BV','Bouvet Island','38','BVT','.bv'),('BW','Botswana','39','BWA','.bw'),('BY','Belarus','30','BLR','.by'),('BZ','Belize','31','BLZ','.bz'),('CA','Canada','41','CAN','.ca'),('CC','Cocos (Keeling) Islands','42','CCK','.cc'),('CD','DR Congo','48','COD','.cd'),('CF','Central African Republic','40','CAF','.cf'),('CG','Republic of the Congo','49','COG','.cg'),('CH','Switzerland','43','CHE','.ch'),('CI','Ivory Coast','46','CIV','.ci'),('CK','Cook Islands','50','COK','.ck'),('CL','Chile','44','CHL','.cl'),('CM','Cameroon','47','CMR','.cm'),('CN','China','45','CHN','.cn'),('CO','Colombia','51','COL','.co'),('CR','Costa Rica','54','CRI','.cr'),('CU','Cuba','55','CUB','.cu'),('CV','Cape Verde','53','CPV','.cv'),('CW','Curacao','56','CUW','.cw'),('CX','Christmas Island','57','CXR','.cx'),('CY','Cyprus','59','CYP','.cy'),('CZ','Czech Republic','60','CZE','.cz'),('DE','Germany','61','DEU','.de'),('DJ','Djibouti','62','DJI','.dj'),('DK','Denmark','64','DNK','.dk'),('DM','Dominica','63','DMA','.dm'),('DO','Dominican Republic','65','DOM','.do'),('DZ','Algeria','66','DZA','.dz'),('EC','Ecuador','67','ECU','.ec'),('EE','Estonia','72','EST','.ee'),('EG','Egypt','68','EGY','.eg'),('EH','Sahrawi','70','ESH','.eh'),('ER','Eritrea','69','ERI','.er'),('ES','Spain','71','ESP','.es'),('ET','Ethiopia','73','ETH','.et'),('FI','Finland','74','FIN','.fi'),('FJ','Fiji','75','FJI','.fj'),('FK','Falkland Islands','76','FLK','.fk'),('FM','Micronesia','79','FSM','.fm'),('FO','Faroe Islands','78','FRO','.fo'),('FR','France','77','FRA','.fr'),('GA','Gabon','80','GAB','.ga'),('GB','United Kingdom','81','GBR','.uk'),('GD','Grenada','92','GRD','.gd'),('GE','Georgia','82','GEO','.ge'),('GF','French Guiana','95','GUF','.gf'),('GG','Guernsey','83','GGY','.gg'),('GH','Ghana','84','GHA','.gh'),('GI','Gibraltar','85','GIB','.gi'),('GL','Greenland','93','GRL','.gl'),('GM','Gambia','88','GMB','.gm'),('GN','Guinea','86','GIN','.gn'),('GP','Guadeloupe','87','GLP','.gp'),('GQ','Equatorial Guinea','90','GNQ','.gq'),('GR','Greece','91','GRC','.gr'),('GS','South Georgia','198','SGS','.gs'),('GT','Guatemala','94','GTM','.gt'),('GU','Guam','96','GUM','.gu'),('GW','Guinea-Bissau','89','GNB','.gw'),('GY','Guyana','97','GUY','.gy'),('HK','Hong Kong, Special Administrative Region of the People\'s Republic of China','98','HKG','.hk'),('HM','Heard Island and McDonald Islands','99','HMD','.hm,.aq'),('HN','Honduras','100','HND','.hn'),('HR','Croatia','101','HRV','.hr'),('HT','Haiti','102','HTI','.ht'),('HU','Hungary','103','HUN','.hu'),('ID','Indonesia','104','IDN','.id'),('IE','Ireland','108','IRL','.ie'),('IL','Israel','112','ISR','.il'),('IM','Isle of Man','105','IMN','.im'),('IN','India','106','IND','.in'),('IO','British Indian Ocean Territory','107','IOT','.io'),('IQ','Iraq','110','IRQ','.iq'),('IR','Iran','109','IRN','.ir'),('IS','Iceland','111','ISL','.is'),('IT','Italy','113','ITA','.it'),('JE','Jersey','115','JEY','.je'),('JM','Jamaica','114','JAM','.jm'),('JO','Jordan','116','JOR','.jo'),('JP','Japan','117','JPN','.jp'),('KE','Kenya','119','KEN','.ke'),('KG','Kyrgyzstan','120','KGZ','.kg'),('KH','Cambodia','121','KHM','.kh'),('KI','Kiribati','122','KIR','.ki'),('KM','Comoros','52','COM','.km'),('KN','Saint Kitts and Nevis','123','KNA','.kn'),('KP','North Korea','184','PRK','.kp'),('KR','South Korea','124','KOR','.kr'),('KW','Kuwait','126','KWT','.kw'),('KY','Cayman Islands','58','CYM','.ky'),('KZ','Kazakhstan','118','KAZ','.kz'),('LA','Laos','127','LAO','.la'),('LB','Lebanon','128','LBN','.lb'),('LC','Saint Lucia','131','LCA','.lc'),('LI','Liechtenstein','132','LIE','.li'),('LK','Sri Lanka','133','LKA','.lk'),('LR','Liberia','129','LBR','.lr'),('LS','Lesotho','134','LSO','.ls'),('LT','Lithuania','135','LTU','.lt'),('LU','Luxembourg','136','LUX','.lu'),('LV','Latvia','137','LVA','.lv'),('LY','Libya','130','LBY','.ly'),('MA','Morocco','140','MAR','.ma'),('MC','Monaco','141','MCO','.mc'),('MD','Moldova','142','MDA','.md'),('ME','Montenegro','151','MNE','.me'),('MF','Saint Martin','139','MAF','.fr,.gp'),('MG','Madagascar','143','MDG','.mg'),('MH','Marshall Islands','146','MHL','.mh'),('MK','Macedonia','147','MKD','.mk'),('ML','Mali','148','MLI','.ml'),('MM','Myanmar','150','MMR','.mm'),('MN','Mongolia','152','MNG','.mn'),('MO','Macau','138','MAC','.mo'),('MP','Northern Mariana Islands','153','MNP','.mp'),('MQ','Martinique','157','MTQ','.mq'),('MR','Mauritania','155','MRT','.mr'),('MS','Montserrat','156','MSR','.ms'),('MT','Malta','149','MLT','.mt'),('MU','Mauritius','158','MUS','.mu'),('MV','Maldives','144','MDV','.mv'),('MW','Malawi','159','MWI','.mw'),('MX','Mexico','145','MEX','.mx'),('MY','Malaysia','160','MYS','.my'),('MZ','Mozambique','154','MOZ','.mz'),('NA','Namibia','162','NAM','.na'),('NC','New Caledonia','163','NCL','.nc'),('NE','Niger','164','NER','.ne'),('NF','Norfolk Island','165','NFK','.nf'),('NG','Nigeria','166','NGA','.ng'),('NI','Nicaragua','167','NIC','.ni'),('NL','Netherlands','169','NLD','.nl'),('NO','Norway','170','NOR','.no'),('NP','Nepal','171','NPL','.np'),('NR','Nauru','172','NRU','.nr'),('NU','Niue','168','NIU','.nu'),('NZ','New Zealand','173','NZL','.nz'),('OM','Oman','174','OMN','.om'),('PA','Panama','176','PAN','.pa'),('PE','Peru','178','PER','.pe'),('PF','French Polynesia','188','PYF','.pf'),('PG','Papua New Guinea','181','PNG','.pg'),('PH','Philippines','179','PHL','.ph'),('PK','Pakistan','175','PAK','.pk'),('PL','Poland','182','POL','.pl'),('PM','Saint Pierre and Miquelon','206','SPM','.pm'),('PN','Pitcairn Islands','177','PCN','.pn'),('PR','Puerto Rico','183','PRI','.pr'),('PS','Palestine','187','PSE','.ps'),('PT','Portugal','185','PRT','.pt'),('PW','Palau','180','PLW','.pw'),('PY','Paraguay','186','PRY','.py'),('QA','Qatar','189','QAT','.qa'),('RE','Reunion,','190','REU','.re'),('RO','Romania','191','ROU','.ro'),('RS','Serbia','207','SRB','.rs'),('RU','Russia','192','RUS','.ru,.su'),('RW','Rwanda','193','RWA','.rw'),('SA','Saudi Arabia','194','SAU','.sa'),('SB','Solomon Islands','201','SLB','.sb'),('SC','Seychelles','216','SYC','.sc'),('SD','Sudan','195','SDN','.sd'),('SE','Sweden','213','SWE','.se'),('SG','Singapore','197','SGP','.sg'),('SH','Saint Helena','199','SHN','.sh'),('SI','Slovenia','212','SVN','.si'),('SJ','Svalbard and Jan Mayen','200','SJM','.sj'),('SK','Slovakia','211','SVK','.sk'),('SL','Sierra Leone','202','SLE','.sl'),('SM','San Marino','204','SMR','.sm'),('SN','Senegal','196','SEN','.sn'),('SO','Somalia','205','SOM','.so'),('SR','Suriname','210','SUR','.sr'),('SS','South Sudan','208','SSD','.ss'),('ST','Sao Tome and Principe','209','STP','.st'),('SV','El Salvador','203','SLV','.sv'),('SX','Sint Maarten','215','SXM','.sx'),('SY','Syria','217','SYR','.sy'),('SZ','Swaziland','214','SWZ','.sz'),('TC','Turks and Caicos Islands','218','TCA','.tc'),('TD','Chad','219','TCD','.td'),('TF','French Southern and Antarctic Lands','14','ATF','.tf'),('TG','Togo','220','TGO','.tg'),('TH','Thailand','221','THA','.th'),('TJ','Tajikistan','222','TJK','.tj'),('TK','Tokelau','223','TKL','.tk'),('TL','Timor-Leste','225','TLS','.tl'),('TM','Turkmenistan','224','TKM','.tm'),('TN','Tunisia','228','TUN','.tn'),('TO','Tonga','226','TON','.to'),('TR','Turkey','229','TUR','.tr'),('TT','Trinidad and Tobago','227','TTO','.tt'),('TV','Tuvalu','230','TUV','.tv'),('TW','Taiwan','231','TWN','.tw'),('TZ','Tanzania','232','TZA','.tz'),('UA','Ukraine','234','UKR','.ua'),('UG','Uganda','233','UGA','.ug'),('UM','United States Minor Outlying Islands','235','UMI','.us'),('US','United States','237','USA','.us'),('UY','Uruguay','236','URY','.uy'),('UZ','Uzbekistan','238','UZB','.uz'),('VA','Vatican City','239','VAT','.va'),('VC','Saint Vincent and the Grenadines','240','VCT','.vc'),('VE','Venezuela','241','VEN','.ve'),('VG','British Virgin Islands','242','VGB','.vg'),('VI','United States Virgin Islands','243','VIR','.vi'),('VN','Vietnam','244','VNM','.vn'),('VU','Vanuatu','245','VUT','.vu'),('WF','Wallis and Futuna','246','WLF','.wf'),('WS','Samoa','247','WSM','.ws'),('XK','Kosovo','125','KOS',''),('YE','Yemen','248','YEM','.ye'),('YT','Mayotte','161','MYT','.yt'),('ZA','South Africa','249','ZAF','.za'),('ZM','Zambia','250','ZMB','.zm'),('ZW','Zimbabwe','251','ZWE','.zw');
/*!40000 ALTER TABLE `country` ENABLE KEYS */;

--
-- Table structure for table `data_dictionary`
--

DROP TABLE IF EXISTS `data_dictionary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_dictionary` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_D095DFFFBF396750` FOREIGN KEY (`id`) REFERENCES `data_specification` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_dictionary`
--

/*!40000 ALTER TABLE `data_dictionary` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_dictionary` ENABLE KEYS */;

--
-- Table structure for table `data_dictionary_group`
--

DROP TABLE IF EXISTS `data_dictionary_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_dictionary_group` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_F8ECFBBBF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_dictionary_group`
--

/*!40000 ALTER TABLE `data_dictionary_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_dictionary_group` ENABLE KEYS */;

--
-- Table structure for table `data_dictionary_option_group`
--

DROP TABLE IF EXISTS `data_dictionary_option_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_dictionary_option_group` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EAE76F60DE12AB56` (`created_by`),
  KEY `IDX_EAE76F6016FE72E1` (`updated_by`),
  CONSTRAINT `FK_EAE76F6016FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_EAE76F60DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_dictionary_option_group`
--

/*!40000 ALTER TABLE `data_dictionary_option_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_dictionary_option_group` ENABLE KEYS */;

--
-- Table structure for table `data_dictionary_option_option`
--

DROP TABLE IF EXISTS `data_dictionary_option_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_dictionary_option_option` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `option_group` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FCBD70FC542BF9AD` (`option_group`),
  CONSTRAINT `FK_FCBD70FC542BF9AD` FOREIGN KEY (`option_group`) REFERENCES `data_dictionary_option_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_dictionary_option_option`
--

/*!40000 ALTER TABLE `data_dictionary_option_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_dictionary_option_option` ENABLE KEYS */;

--
-- Table structure for table `data_dictionary_variable`
--

DROP TABLE IF EXISTS `data_dictionary_variable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_dictionary_variable` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DataDictionaryDataType)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_DB31EFA2BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_dictionary_variable`
--

/*!40000 ALTER TABLE `data_dictionary_variable` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_dictionary_variable` ENABLE KEYS */;

--
-- Table structure for table `data_dictionary_version`
--

DROP TABLE IF EXISTS `data_dictionary_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_dictionary_version` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_74CABF5DBF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_version` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_dictionary_version`
--

/*!40000 ALTER TABLE `data_dictionary_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_dictionary_version` ENABLE KEYS */;

--
-- Table structure for table `data_model`
--

DROP TABLE IF EXISTS `data_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_992ABE46BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model`
--

/*!40000 ALTER TABLE `data_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model` ENABLE KEYS */;

--
-- Table structure for table `data_model_module`
--

DROP TABLE IF EXISTS `data_model_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_module` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_B9356A66BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_module`
--

/*!40000 ALTER TABLE `data_model_module` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_module` ENABLE KEYS */;

--
-- Table structure for table `data_model_node`
--

DROP TABLE IF EXISTS `data_model_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_node` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_671DFE7BBF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_node`
--

/*!40000 ALTER TABLE `data_model_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_node` ENABLE KEYS */;

--
-- Table structure for table `data_model_node_external`
--

DROP TABLE IF EXISTS `data_model_node_external`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_node_external` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `iri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:iri)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_FD7D7D44BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_node_external`
--

/*!40000 ALTER TABLE `data_model_node_external` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_node_external` ENABLE KEYS */;

--
-- Table structure for table `data_model_node_internal`
--

DROP TABLE IF EXISTS `data_model_node_internal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_node_internal` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_repeated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_AEDCC1FFBF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_node_internal`
--

/*!40000 ALTER TABLE `data_model_node_internal` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_node_internal` ENABLE KEYS */;

--
-- Table structure for table `data_model_node_literal`
--

DROP TABLE IF EXISTS `data_model_node_literal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_node_literal` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_B4D5BACFBF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_node_literal`
--

/*!40000 ALTER TABLE `data_model_node_literal` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_node_literal` ENABLE KEYS */;

--
-- Table structure for table `data_model_node_record`
--

DROP TABLE IF EXISTS `data_model_node_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_node_record` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_9D711823BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_node_record`
--

/*!40000 ALTER TABLE `data_model_node_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_node_record` ENABLE KEYS */;

--
-- Table structure for table `data_model_node_value`
--

DROP TABLE IF EXISTS `data_model_node_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_node_value` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `is_annotated_value` tinyint(1) NOT NULL,
  `data_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_repeated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_EE45F571BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_node_value`
--

/*!40000 ALTER TABLE `data_model_node_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_node_value` ENABLE KEYS */;

--
-- Table structure for table `data_model_predicate`
--

DROP TABLE IF EXISTS `data_model_predicate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_predicate` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `data_model` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `iri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:iri)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7C632AFF992ABE46` (`data_model`),
  KEY `IDX_7C632AFFDE12AB56` (`created_by`),
  KEY `IDX_7C632AFF16FE72E1` (`updated_by`),
  CONSTRAINT `FK_7C632AFF16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_7C632AFF992ABE46` FOREIGN KEY (`data_model`) REFERENCES `data_model_version` (`id`),
  CONSTRAINT `FK_7C632AFFDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_predicate`
--

/*!40000 ALTER TABLE `data_model_predicate` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_predicate` ENABLE KEYS */;

--
-- Table structure for table `data_model_prefix`
--

DROP TABLE IF EXISTS `data_model_prefix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_prefix` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `data_model` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `prefix` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:iri)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_26A0CAC0992ABE46` (`data_model`),
  KEY `IDX_26A0CAC0DE12AB56` (`created_by`),
  KEY `IDX_26A0CAC016FE72E1` (`updated_by`),
  CONSTRAINT `FK_26A0CAC016FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_26A0CAC0992ABE46` FOREIGN KEY (`data_model`) REFERENCES `data_model_version` (`id`),
  CONSTRAINT `FK_26A0CAC0DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_prefix`
--

/*!40000 ALTER TABLE `data_model_prefix` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_prefix` ENABLE KEYS */;

--
-- Table structure for table `data_model_triple`
--

DROP TABLE IF EXISTS `data_model_triple`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_triple` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `subject` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `predicate` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `object` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  KEY `IDX_F13D7030FBCE3E7A` (`subject`),
  KEY `IDX_F13D7030301BAA7B` (`predicate`),
  KEY `IDX_F13D7030A8ADABEC` (`object`),
  CONSTRAINT `FK_F13D7030301BAA7B` FOREIGN KEY (`predicate`) REFERENCES `data_model_predicate` (`id`),
  CONSTRAINT `FK_F13D7030A8ADABEC` FOREIGN KEY (`object`) REFERENCES `data_model_node` (`id`),
  CONSTRAINT `FK_F13D7030BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_elementgroup` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_F13D7030FBCE3E7A` FOREIGN KEY (`subject`) REFERENCES `data_model_node` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_triple`
--

/*!40000 ALTER TABLE `data_model_triple` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_triple` ENABLE KEYS */;

--
-- Table structure for table `data_model_version`
--

DROP TABLE IF EXISTS `data_model_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_model_version` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_2ECDAE18BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_version` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_model_version`
--

/*!40000 ALTER TABLE `data_model_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_model_version` ENABLE KEYS */;

--
-- Table structure for table `data_specification`
--

DROP TABLE IF EXISTS `data_specification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_FF7D0EB0DE12AB56` (`created_by`),
  KEY `IDX_FF7D0EB016FE72E1` (`updated_by`),
  CONSTRAINT `FK_FF7D0EB016FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_FF7D0EB0DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification`
--

/*!40000 ALTER TABLE `data_specification` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification` ENABLE KEYS */;

--
-- Table structure for table `data_specification_dependency`
--

DROP TABLE IF EXISTS `data_specification_dependency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_dependency` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `group_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `dtype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2AF6C929FE54D947` (`group_id`),
  KEY `IDX_2AF6C929DE12AB56` (`created_by`),
  KEY `IDX_2AF6C92916FE72E1` (`updated_by`),
  CONSTRAINT `FK_2AF6C92916FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_2AF6C929DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_2AF6C929FE54D947` FOREIGN KEY (`group_id`) REFERENCES `data_specification_dependency_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_dependency`
--

/*!40000 ALTER TABLE `data_specification_dependency` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_dependency` ENABLE KEYS */;

--
-- Table structure for table `data_specification_dependency_group`
--

DROP TABLE IF EXISTS `data_specification_dependency_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_dependency_group` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `combinator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DependencyCombinatorType)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_B589FC40BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_dependency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_dependency_group`
--

/*!40000 ALTER TABLE `data_specification_dependency_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_dependency_group` ENABLE KEYS */;

--
-- Table structure for table `data_specification_dependency_rule`
--

DROP TABLE IF EXISTS `data_specification_dependency_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_dependency_rule` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `element` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `operator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DependencyOperatorType)',
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_24BF36441405E39` (`element`),
  CONSTRAINT `FK_24BF36441405E39` FOREIGN KEY (`element`) REFERENCES `data_specification_element` (`id`),
  CONSTRAINT `FK_24BF364BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_dependency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_dependency_rule`
--

/*!40000 ALTER TABLE `data_specification_dependency_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_dependency_rule` ENABLE KEYS */;

--
-- Table structure for table `data_specification_element`
--

DROP TABLE IF EXISTS `data_specification_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_element` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `groupId` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `version` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orderNumber` int(11) DEFAULT NULL,
  `option_group` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  KEY `IDX_CE19CB2DBF1CD3C3` (`version`),
  KEY `IDX_CE19CB2DDE12AB56` (`created_by`),
  KEY `IDX_CE19CB2D16FE72E1` (`updated_by`),
  KEY `IDX_CE19CB2DED8188B0` (`groupId`),
  KEY `IDX_CE19CB2D542BF9AD` (`option_group`),
  CONSTRAINT `FK_CE19CB2D16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_CE19CB2D542BF9AD` FOREIGN KEY (`option_group`) REFERENCES `data_dictionary_option_group` (`id`),
  CONSTRAINT `FK_CE19CB2DBF1CD3C3` FOREIGN KEY (`version`) REFERENCES `data_specification_version` (`id`),
  CONSTRAINT `FK_CE19CB2DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_CE19CB2DED8188B0` FOREIGN KEY (`groupId`) REFERENCES `data_specification_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_element`
--

/*!40000 ALTER TABLE `data_specification_element` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_element` ENABLE KEYS */;

--
-- Table structure for table `data_specification_elementgroup`
--

DROP TABLE IF EXISTS `data_specification_elementgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_elementgroup` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `groupId` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B353A448DE12AB56` (`created_by`),
  KEY `IDX_B353A44816FE72E1` (`updated_by`),
  KEY `IDX_B353A448ED8188B0` (`groupId`),
  CONSTRAINT `FK_B353A44816FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B353A448DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B353A448ED8188B0` FOREIGN KEY (`groupId`) REFERENCES `data_specification_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_elementgroup`
--

/*!40000 ALTER TABLE `data_specification_elementgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_elementgroup` ENABLE KEYS */;

--
-- Table structure for table `data_specification_group`
--

DROP TABLE IF EXISTS `data_specification_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_group` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `version` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `dependencies` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_repeated` tinyint(1) NOT NULL DEFAULT '0',
  `is_dependent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orderNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_65CF49E1EA0F708D` (`dependencies`),
  KEY `IDX_65CF49E1BF1CD3C3` (`version`),
  KEY `IDX_65CF49E1DE12AB56` (`created_by`),
  KEY `IDX_65CF49E116FE72E1` (`updated_by`),
  CONSTRAINT `FK_65CF49E116FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_65CF49E1BF1CD3C3` FOREIGN KEY (`version`) REFERENCES `data_specification_version` (`id`),
  CONSTRAINT `FK_65CF49E1DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_65CF49E1EA0F708D` FOREIGN KEY (`dependencies`) REFERENCES `data_specification_dependency_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_group`
--

/*!40000 ALTER TABLE `data_specification_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_group` ENABLE KEYS */;

--
-- Table structure for table `data_specification_mappings`
--

DROP TABLE IF EXISTS `data_specification_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_mappings` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `study` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `version` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C7FCEF1E67F9749` (`study`),
  KEY `IDX_C7FCEF1BF1CD3C3` (`version`),
  KEY `IDX_C7FCEF1DE12AB56` (`created_by`),
  KEY `IDX_C7FCEF116FE72E1` (`updated_by`),
  CONSTRAINT `FK_C7FCEF116FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_C7FCEF1BF1CD3C3` FOREIGN KEY (`version`) REFERENCES `data_specification_version` (`id`),
  CONSTRAINT `FK_C7FCEF1DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_C7FCEF1E67F9749` FOREIGN KEY (`study`) REFERENCES `study` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_mappings`
--

/*!40000 ALTER TABLE `data_specification_mappings` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_mappings` ENABLE KEYS */;

--
-- Table structure for table `data_specification_mappings_element`
--

DROP TABLE IF EXISTS `data_specification_mappings_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_mappings_element` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `element` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `syntax` longtext COLLATE utf8mb4_unicode_ci,
  `transform_data` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A56F1D5341405E39` (`element`),
  CONSTRAINT `FK_A56F1D5341405E39` FOREIGN KEY (`element`) REFERENCES `data_specification_element` (`id`),
  CONSTRAINT `FK_A56F1D53BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_mappings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_mappings_element`
--

/*!40000 ALTER TABLE `data_specification_mappings_element` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_mappings_element` ENABLE KEYS */;

--
-- Table structure for table `data_specification_mappings_group`
--

DROP TABLE IF EXISTS `data_specification_mappings_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_mappings_group` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `entity` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `groupId` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  KEY `IDX_55557DF1ED8188B0` (`groupId`),
  KEY `IDX_55557DF1E284468` (`entity`),
  CONSTRAINT `FK_55557DF1BF396750` FOREIGN KEY (`id`) REFERENCES `data_specification_mappings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_55557DF1E284468` FOREIGN KEY (`entity`) REFERENCES `castor_entity` (`id`),
  CONSTRAINT `FK_55557DF1ED8188B0` FOREIGN KEY (`groupId`) REFERENCES `data_specification_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_mappings_group`
--

/*!40000 ALTER TABLE `data_specification_mappings_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_mappings_group` ENABLE KEYS */;

--
-- Table structure for table `data_specification_version`
--

DROP TABLE IF EXISTS `data_specification_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_specification_version` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `data_specification` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:version)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_304546D7FF7D0EB0` (`data_specification`),
  KEY `IDX_304546D7DE12AB56` (`created_by`),
  KEY `IDX_304546D716FE72E1` (`updated_by`),
  CONSTRAINT `FK_304546D716FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_304546D7DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_304546D7FF7D0EB0` FOREIGN KEY (`data_specification`) REFERENCES `data_specification` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_specification_version`
--

/*!40000 ALTER TABLE `data_specification_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_specification_version` ENABLE KEYS */;

--
-- Table structure for table `dataset`
--

DROP TABLE IF EXISTS `dataset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dataset` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `study_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published` tinyint(1) NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_B7A041D0DE12AB56` (`created_by`),
  KEY `IDX_B7A041D016FE72E1` (`updated_by`),
  KEY `slug` (`slug`),
  KEY `IDX_B7A041D0E7B003E9` (`study_id`),
  CONSTRAINT `FK_B7A041D016FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B7A041D0DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B7A041D0E7B003E9` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dataset`
--

/*!40000 ALTER TABLE `dataset` DISABLE KEYS */;
/*!40000 ALTER TABLE `dataset` ENABLE KEYS */;

--
-- Table structure for table `dataset_contacts`
--

DROP TABLE IF EXISTS `dataset_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dataset_contacts` (
  `metadata_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `agent_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`metadata_id`,`agent_id`),
  KEY `IDX_ACD423FADC9EE959` (`metadata_id`),
  KEY `IDX_ACD423FA3414710B` (`agent_id`),
  CONSTRAINT `FK_ACD423FA3414710B` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ACD423FADC9EE959` FOREIGN KEY (`metadata_id`) REFERENCES `metadata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dataset_contacts`
--

/*!40000 ALTER TABLE `dataset_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `dataset_contacts` ENABLE KEYS */;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `organization` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `additional_information` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_CD1DE18AC1EE637C` (`organization`),
  CONSTRAINT `FK_CD1DE18ABF396750` FOREIGN KEY (`id`) REFERENCES `agent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_CD1DE18AC1EE637C` FOREIGN KEY (`organization`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

/*!40000 ALTER TABLE `department` DISABLE KEYS */;
/*!40000 ALTER TABLE `department` ENABLE KEYS */;

--
-- Table structure for table `distribution`
--

DROP TABLE IF EXISTS `distribution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `dataset_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `license` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_api` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  KEY `IDX_A4483781D47C2D1B` (`dataset_id`),
  KEY `IDX_A4483781DE12AB56` (`created_by`),
  KEY `IDX_A448378116FE72E1` (`updated_by`),
  KEY `slug` (`slug`),
  KEY `IDX_A44837815768F419` (`license`),
  KEY `IDX_A44837814613B984` (`user_api`),
  CONSTRAINT `FK_A448378116FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_A44837814613B984` FOREIGN KEY (`user_api`) REFERENCES `user_api` (`id`),
  CONSTRAINT `FK_A44837815768F419` FOREIGN KEY (`license`) REFERENCES `license` (`slug`),
  CONSTRAINT `FK_A4483781D47C2D1B` FOREIGN KEY (`dataset_id`) REFERENCES `dataset` (`id`),
  CONSTRAINT `FK_A4483781DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution`
--

/*!40000 ALTER TABLE `distribution` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution` ENABLE KEYS */;

--
-- Table structure for table `distribution_contactpoint`
--

DROP TABLE IF EXISTS `distribution_contactpoint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution_contactpoint` (
  `distribution_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `agent_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`distribution_id`,`agent_id`),
  KEY `IDX_495112716EB6DDB5` (`distribution_id`),
  KEY `IDX_495112713414710B` (`agent_id`),
  CONSTRAINT `FK_495112713414710B` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_495112716EB6DDB5` FOREIGN KEY (`distribution_id`) REFERENCES `distribution` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_contactpoint`
--

/*!40000 ALTER TABLE `distribution_contactpoint` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution_contactpoint` ENABLE KEYS */;

--
-- Table structure for table `distribution_contents`
--

DROP TABLE IF EXISTS `distribution_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution_contents` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `distribution` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `access` enum('1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DistributionAccessType)',
  `is_published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dependencies` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `is_cached` tinyint(1) NOT NULL,
  `data_specification` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `data_specification_version` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_91757299A4483781` (`distribution`),
  UNIQUE KEY `UNIQ_91757299EA0F708D` (`dependencies`),
  KEY `IDX_91757299DE12AB56` (`created_by`),
  KEY `IDX_9175729916FE72E1` (`updated_by`),
  KEY `IDX_91757299FF7D0EB0` (`data_specification`),
  KEY `IDX_91757299304546D7` (`data_specification_version`),
  CONSTRAINT `FK_9175729916FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_91757299304546D7` FOREIGN KEY (`data_specification_version`) REFERENCES `data_specification_version` (`id`),
  CONSTRAINT `FK_91757299A4483781` FOREIGN KEY (`distribution`) REFERENCES `distribution` (`id`),
  CONSTRAINT `FK_91757299DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_91757299EA0F708D` FOREIGN KEY (`dependencies`) REFERENCES `distribution_dependency_group` (`id`),
  CONSTRAINT `FK_91757299FF7D0EB0` FOREIGN KEY (`data_specification`) REFERENCES `data_specification` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_contents`
--

/*!40000 ALTER TABLE `distribution_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution_contents` ENABLE KEYS */;

--
-- Table structure for table `distribution_csv`
--

DROP TABLE IF EXISTS `distribution_csv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution_csv` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_D815CB1ABF396750` FOREIGN KEY (`id`) REFERENCES `distribution_contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_csv`
--

/*!40000 ALTER TABLE `distribution_csv` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution_csv` ENABLE KEYS */;

--
-- Table structure for table `distribution_databases`
--

DROP TABLE IF EXISTS `distribution_databases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution_databases` (
  `distribution` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `database_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`distribution`),
  CONSTRAINT `FK_8086D1DEA4483781` FOREIGN KEY (`distribution`) REFERENCES `distribution` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_databases`
--

/*!40000 ALTER TABLE `distribution_databases` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution_databases` ENABLE KEYS */;

--
-- Table structure for table `distribution_dependency`
--

DROP TABLE IF EXISTS `distribution_dependency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution_dependency` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `group_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `dtype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3B1E9E0AFE54D947` (`group_id`),
  KEY `IDX_3B1E9E0ADE12AB56` (`created_by`),
  KEY `IDX_3B1E9E0A16FE72E1` (`updated_by`),
  CONSTRAINT `FK_3B1E9E0A16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_3B1E9E0ADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_3B1E9E0AFE54D947` FOREIGN KEY (`group_id`) REFERENCES `distribution_dependency_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_dependency`
--

/*!40000 ALTER TABLE `distribution_dependency` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution_dependency` ENABLE KEYS */;

--
-- Table structure for table `distribution_dependency_group`
--

DROP TABLE IF EXISTS `distribution_dependency_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution_dependency_group` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `combinator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DependencyCombinatorType)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_B0AD3893BF396750` FOREIGN KEY (`id`) REFERENCES `distribution_dependency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_dependency_group`
--

/*!40000 ALTER TABLE `distribution_dependency_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution_dependency_group` ENABLE KEYS */;

--
-- Table structure for table `distribution_dependency_rule`
--

DROP TABLE IF EXISTS `distribution_dependency_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution_dependency_rule` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `node` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DistributionContentsDependencyType)',
  `operator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DependencyOperatorType)',
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_26D833A3857FE845` (`node`),
  CONSTRAINT `FK_26D833A3857FE845` FOREIGN KEY (`node`) REFERENCES `data_model_node_value` (`id`),
  CONSTRAINT `FK_26D833A3BF396750` FOREIGN KEY (`id`) REFERENCES `distribution_dependency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_dependency_rule`
--

/*!40000 ALTER TABLE `distribution_dependency_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution_dependency_rule` ENABLE KEYS */;

--
-- Table structure for table `distribution_rdf`
--

DROP TABLE IF EXISTS `distribution_rdf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribution_rdf` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_DDC596AFBF396750` FOREIGN KEY (`id`) REFERENCES `distribution_contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distribution_rdf`
--

/*!40000 ALTER TABLE `distribution_rdf` DISABLE KEYS */;
/*!40000 ALTER TABLE `distribution_rdf` ENABLE KEYS */;

--
-- Table structure for table `element_mapping_castor_entity`
--

DROP TABLE IF EXISTS `element_mapping_castor_entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element_mapping_castor_entity` (
  `element_mapping_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `castor_entity_id` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`element_mapping_id`,`castor_entity_id`),
  KEY `IDX_57A8637756AF85A2` (`element_mapping_id`),
  KEY `IDX_57A86377380FC0DA` (`castor_entity_id`),
  CONSTRAINT `FK_57A86377380FC0DA` FOREIGN KEY (`castor_entity_id`) REFERENCES `castor_entity` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_57A8637756AF85A2` FOREIGN KEY (`element_mapping_id`) REFERENCES `data_specification_mappings_element` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_mapping_castor_entity`
--

/*!40000 ALTER TABLE `element_mapping_castor_entity` DISABLE KEYS */;
/*!40000 ALTER TABLE `element_mapping_castor_entity` ENABLE KEYS */;

--
-- Table structure for table `fdp`
--

DROP TABLE IF EXISTS `fdp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fdp` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `iri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:iri)',
  `purl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:iri)',
  PRIMARY KEY (`id`),
  KEY `iri` (`iri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fdp`
--

/*!40000 ALTER TABLE `fdp` DISABLE KEYS */;
INSERT INTO `fdp` (`id`, `iri`, `purl`) VALUES ('075d8315-cf3f-11e9-99e5-eb3442afa83d','https://fdp.castoredc.local',NULL);
/*!40000 ALTER TABLE `fdp` ENABLE KEYS */;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `code` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language`
--

/*!40000 ALTER TABLE `language` DISABLE KEYS */;
INSERT INTO `language` (`code`, `name`) VALUES ('aa','Afar'),('ab','Abkhazian'),('af','Afrikaans'),('am','Amharic'),('ar','Arabic'),('as','Assamese'),('ay','Aymara'),('az','Azerbaijani'),('ba','Bashkir'),('be','Byelorussian'),('bg','Bulgarian'),('bh','Bihari'),('bi','Bislama'),('bn','Bengali'),('bo','Tibetan'),('br','Breton'),('ca','Catalan'),('co','Corsican'),('cs','Czech'),('cy','Welch'),('da','Danish'),('de','German'),('dz','Bhutani'),('el','Greek'),('en','English'),('eo','Esperanto'),('es','Spanish'),('et','Estonian'),('eu','Basque'),('fa','Persian'),('fi','Finnish'),('fj','Fiji'),('fo','Faeroese'),('fr','French'),('fy','Frisian'),('ga','Irish'),('gd','Scots Gaelic'),('gl','Galician'),('gn','Guarani'),('gu','Gujarati'),('ha','Hausa'),('he','Hebrew'),('hi','Hindi'),('hr','Croatian'),('hu','Hungarian'),('hy','Armenian'),('ia','Interlingua'),('id','Indonesian'),('ie','Interlingue'),('ik','Inupiak'),('in','former Indonesian'),('is','Icelandic'),('it','Italian'),('iu','Inuktitut (Eskimo)'),('iw','former Hebrew'),('ja','Japanese'),('ji','former Yiddish'),('jw','Javanese'),('ka','Georgian'),('kk','Kazakh'),('kl','Greenlandic'),('km','Cambodian'),('kn','Kannada'),('ko','Korean'),('ks','Kashmiri'),('ku','Kurdish'),('ky','Kirghiz'),('la','Latin'),('ln','Lingala'),('lo','Laothian'),('lt','Lithuanian'),('lv','Latvian, Lettish'),('mg','Malagasy'),('mi','Maori'),('mk','Macedonian'),('ml','Malayalam'),('mn','Mongolian'),('mo','Moldavian'),('mr','Marathi'),('ms','Malay'),('mt','Maltese'),('my','Burmese'),('na','Nauru'),('ne','Nepali'),('nl','Dutch'),('no','Norwegian'),('oc','Occitan'),('om','(Afan) Oromo'),('or','Oriya'),('pa','Punjabi'),('pl','Polish'),('ps','Pashto, Pushto'),('pt','Portuguese'),('qu','Quechua'),('rm','Rhaeto-Romance'),('rn','Kirundi'),('ro','Romanian'),('ru','Russian'),('rw','Kinyarwanda'),('sa','Sanskrit'),('sd','Sindhi'),('sg','Sangro'),('sh','Serbo-Croatian'),('si','Singhalese'),('sk','Slovak'),('sl','Slovenian'),('sm','Samoan'),('sn','Shona'),('so','Somali'),('sq','Albanian'),('sr','Serbian'),('ss','Siswati'),('st','Sesotho'),('su','Sudanese'),('sv','Swedish'),('sw','Swahili'),('ta','Tamil'),('te','Tegulu'),('tg','Tajik'),('th','Thai'),('ti','Tigrinya'),('tk','Turkmen'),('tl','Tagalog'),('tn','Setswana'),('to','Tonga'),('tr','Turkish'),('ts','Tsonga'),('tt','Tatar'),('tw','Twi'),('ug','Uigur'),('uk','Ukrainian'),('ur','Urdu'),('uz','Uzbek'),('vi','Vietnamese'),('vo','Volapuk'),('wo','Wolof'),('xh','Xhosa'),('yi','Yiddish'),('yo','Yoruba'),('za','Zhuang'),('zh','Chinese'),('zu','Zulu');
/*!40000 ALTER TABLE `language` ENABLE KEYS */;

--
-- Table structure for table `license`
--

DROP TABLE IF EXISTS `license`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `license` (
  `slug` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:iri)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `license`
--

/*!40000 ALTER TABLE `license` DISABLE KEYS */;
INSERT INTO `license` (`slug`, `url`, `name`) VALUES ('afl3.0','http://purl.org/NET/rdflicense/afl3.0','Academic Free License 3.0'),('againstdrm2.0','http://purl.org/NET/rdflicense/againstdrm2.0','Against DRM 2.0'),('agpl3.0','http://purl.org/NET/rdflicense/agpl3.0','GNU Affero General Public License 3.0'),('allrightsreserved','http://purl.org/NET/rdflicense/allrightsreserved','All rights reserved'),('APACHE1.0','http://purl.org/NET/rdflicense/APACHE1.0','Apache License 1.0'),('APACHE2.0','http://purl.org/NET/rdflicense/APACHE2.0','Apache License 2.0'),('ARTISTIC2.0','http://purl.org/NET/rdflicense/ARTISTIC2.0','Artistic License 2.0'),('BOOST1.0','http://purl.org/NET/rdflicense/BOOST1.0','BOOST Software License 1.0'),('BSD2.0','http://purl.org/NET/rdflicense/BSD2.0','2-clause BSD License 2.0'),('BSD3.0','http://purl.org/NET/rdflicense/BSD3.0','BSD License 3-clause 4.0'),('BSD4.0','http://purl.org/NET/rdflicense/BSD4.0','BSD License 4-clause 4.0'),('cc-by-nc-nd2.0','http://purl.org/NET/rdflicense/cc-by-nc-nd2.0','Creative Commons CC-BY-NC-ND 2.0'),('cc-by-nc-nd3.0','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0','Creative Commons CC-BY-NC-ND 3.0'),('cc-by-nc-nd3.0at','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0at','Creative Commons CC-BY-NC-ND Austria 3.0'),('cc-by-nc-nd3.0au','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0au','Creative Commons CC-BY-NC-ND Australia 3.0'),('cc-by-nc-nd3.0br','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0br','Creative Commons CC-BY-NC-ND Brazil 3.0'),('cc-by-nc-nd3.0ch','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0ch','Creative Commons CC-BY-NC-ND Switzerland 3.0'),('cc-by-nc-nd3.0cl','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0cl','Creative Commons CC-BY-NC-ND Chile 3.0'),('cc-by-nc-nd3.0cn','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0cn','Creative Commons CC-BY-NC-ND China 3.0'),('cc-by-nc-nd3.0de','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0de','Creative Commons CC-BY-NC-ND Germany 3.0'),('cc-by-nc-nd3.0ec','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0ec','Creative Commons CC-BY-NC-ND Ecuador 3.0'),('cc-by-nc-nd3.0es','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0es','Creative Commons CC-BY-NC-ND Spain 3.0'),('cc-by-nc-nd3.0fr','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0fr','Creative Commons CC-BY-NC-ND France 3.0'),('cc-by-nc-nd3.0gr','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0gr','Creative Commons CC-BY-NC-ND Greece 3.0'),('cc-by-nc-nd3.0ie','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0ie','Creative Commons CC-BY-NC-ND Ireland 3.0'),('cc-by-nc-nd3.0it','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0it','Creative Commons CC-BY-NC-ND Italy 3.0'),('cc-by-nc-nd3.0nl','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0nl','Creative Commons CC-BY-NC-ND Netherlands 3.0'),('cc-by-nc-nd3.0pt','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0pt','Creative Commons CC-BY-NC-ND Portugal 3.0'),('cc-by-nc-nd3.0ro','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0ro','Creative Commons CC-BY-NC-ND Romania 3.0'),('cc-by-nc-nd3.0ve','http://purl.org/NET/rdflicense/cc-by-nc-nd3.0ve','Creative Commons CC-BY-NC-ND Venezuela 3.0'),('cc-by-nc-nd4.0','http://purl.org/NET/rdflicense/cc-by-nc-nd4.0','Creative Commons CC-BY-NC-ND 4.0'),('cc-by-nc-sa2.0','http://purl.org/NET/rdflicense/cc-by-nc-sa2.0','Creative Commons CC-BY-NC-SA 2.0'),('cc-by-nc-sa3.0','http://purl.org/NET/rdflicense/cc-by-nc-sa3.0','Creative Commons CC-BY-NC-SA 3.0'),('cc-by-nc-sa4.0','http://purl.org/NET/rdflicense/cc-by-nc-sa4.0','Creative Commons CC-BY-NC-SA 4.0'),('cc-by-nc2.0','http://purl.org/NET/rdflicense/cc-by-nc2.0','Creative Commons CC-BY-NC 2.0'),('cc-by-nc3.0','http://purl.org/NET/rdflicense/cc-by-nc3.0','Creative Commons CC-BY-NC 3.0'),('cc-by-nc4.0','http://purl.org/NET/rdflicense/cc-by-nc4.0','Creative Commons CC-BY-NC 4.0'),('cc-by-nd2.0','http://purl.org/NET/rdflicense/cc-by-nd2.0','Creative Commons CC-BY-ND 2.0'),('cc-by-nd2.0be','http://purl.org/NET/rdflicense/cc-by-nd2.0be','Creative Commons CC-BY-ND Belgium 2.0'),('cc-by-nd2.5dk','http://purl.org/NET/rdflicense/cc-by-nd2.5dk','Creative Commons CC-BY-ND Denmark 2.5'),('cc-by-nd3.0','http://purl.org/NET/rdflicense/cc-by-nd3.0','Creative Commons CC-BY-ND 3.0'),('cc-by-nd3.0at','http://purl.org/NET/rdflicense/cc-by-nd3.0at','Creative Commons CC-BY-ND Austria 3.0'),('cc-by-nd3.0au','http://purl.org/NET/rdflicense/cc-by-nd3.0au','Creative Commons CC-BY-ND Australia 3.0'),('cc-by-nd3.0br','http://purl.org/NET/rdflicense/cc-by-nd3.0br','Creative Commons CC-BY-ND Brazil 3.0'),('cc-by-nd3.0ch','http://purl.org/NET/rdflicense/cc-by-nd3.0ch','Creative Commons CC-BY-ND Switzerland 3.0'),('cc-by-nd3.0cl','http://purl.org/NET/rdflicense/cc-by-nd3.0cl','Creative Commons CC-BY-ND Chile 3.0'),('cc-by-nd3.0cn','http://purl.org/NET/rdflicense/cc-by-nd3.0cn','Creative Commons CC-BY-ND China 3.0'),('cc-by-nd3.0de','http://purl.org/NET/rdflicense/cc-by-nd3.0de','Creative Commons CC-BY-ND Germany 3.0'),('cc-by-nd3.0ec','http://purl.org/NET/rdflicense/cc-by-nd3.0ec','Creative Commons CC-BY-ND Ecuador 3.0'),('cc-by-nd3.0es','http://purl.org/NET/rdflicense/cc-by-nd3.0es','Creative Commons CC-BY-ND 3.0 Spain 3.0'),('cc-by-nd3.0fr','http://purl.org/NET/rdflicense/cc-by-nd3.0fr','Creative Commons CC-BY-ND 3.0 France 3.0'),('cc-by-nd3.0gr','http://purl.org/NET/rdflicense/cc-by-nd3.0gr','Creative Commons CC-BY-ND 3.0 Greece 3.0'),('cc-by-nd3.0ie','http://purl.org/NET/rdflicense/cc-by-nd3.0ie','Creative Commons CC-BY-ND Ireland 3.0'),('cc-by-nd3.0it','http://purl.org/NET/rdflicense/cc-by-nd3.0it','Creative Commons CC-BY-ND 3.0 Italy 3.0'),('cc-by-nd3.0nl','http://purl.org/NET/rdflicense/cc-by-nd3.0nl','Creative Commons CC-BY-ND 3.0 Netherlands 3.0'),('cc-by-nd3.0pt','http://purl.org/NET/rdflicense/cc-by-nd3.0pt','Creative Commons CC-BY-ND 3.0 Portugal 3.0'),('cc-by-nd3.0ro','http://purl.org/NET/rdflicense/cc-by-nd3.0ro','Creative Commons CC-BY-ND 3.0 Romania 3.0'),('cc-by-nd3.0ve','http://purl.org/NET/rdflicense/cc-by-nd3.0ve','Creative Commons CC-BY-ND 3.0 Venezuela 3.0'),('cc-by-nd4.0','http://purl.org/NET/rdflicense/cc-by-nd4.0','Creative Commons CC-BY-ND 4.0'),('cc-by-sa2.0','http://purl.org/NET/rdflicense/cc-by-sa2.0','Creative Commons CC-BY-SA 2.0'),('cc-by-sa3.0','http://purl.org/NET/rdflicense/cc-by-sa3.0','Creative Commons CC-BY-SA 3.0'),('cc-by-sa3.0at','http://purl.org/NET/rdflicense/cc-by-sa3.0at','Creative Commons CC-BY-SA 3.0 Austria 3.0'),('cc-by-sa3.0au','http://purl.org/NET/rdflicense/cc-by-sa3.0au','Creative Commons CC-BY-SA 3.0 Australia 3.0'),('cc-by-sa3.0br','http://purl.org/NET/rdflicense/cc-by-sa3.0br','Creative Commons CC-BY-SA 3.0 Brazil 3.0'),('cc-by-sa3.0ch','http://purl.org/NET/rdflicense/cc-by-sa3.0ch','Creative Commons CC-BY-SA 3.0 Switzerland 3.0'),('cc-by-sa3.0cl','http://purl.org/NET/rdflicense/cc-by-sa3.0cl','Creative Commons CC-BY-SA 3.0 Chile 3.0'),('cc-by-sa3.0cn','http://purl.org/NET/rdflicense/cc-by-sa3.0cn','Creative Commons CC-BY-SA 3.0 China 3.0'),('cc-by-sa3.0de','http://purl.org/NET/rdflicense/cc-by-sa3.0de','Creative Commons CC-BY-SA 3.0 Germany 3.0'),('cc-by-sa3.0ec','http://purl.org/NET/rdflicense/cc-by-sa3.0ec','Creative Commons CC-BY-SA 3.0 Ecuador 3.0'),('cc-by-sa3.0es','http://purl.org/NET/rdflicense/cc-by-sa3.0es','Creative Commons CC-BY-SA 3.0 Spain 3.0'),('cc-by-sa3.0fr','http://purl.org/NET/rdflicense/cc-by-sa3.0fr','Creative Commons CC-BY-SA 3.0 France 3.0'),('cc-by-sa3.0gr','http://purl.org/NET/rdflicense/cc-by-sa3.0gr','Creative Commons CC-BY-SA 3.0 Greece 3.0'),('cc-by-sa3.0ie','http://purl.org/NET/rdflicense/cc-by-sa3.0ie','Creative Commons CC-BY-SA Ireland 3.0'),('cc-by-sa3.0it','http://purl.org/NET/rdflicense/cc-by-sa3.0it','Creative Commons CC-BY-SA 3.0 Italy 3.0'),('cc-by-sa3.0nl','http://purl.org/NET/rdflicense/cc-by-sa3.0nl','Creative Commons CC-BY-SA Netherlands 3.0'),('cc-by-sa3.0pt','http://purl.org/NET/rdflicense/cc-by-sa3.0pt','Creative Commons CC-BY-SA Portugal 3.0'),('cc-by-sa3.0ro','http://purl.org/NET/rdflicense/cc-by-sa3.0ro','Creative Commons CC-BY-SA Romania 3.0'),('cc-by-sa3.0ve','http://purl.org/NET/rdflicense/cc-by-sa3.0ve','Creative Commons CC-BY-SA Venezuela 3.0'),('cc-by-sa4.0','http://purl.org/NET/rdflicense/cc-by-sa4.0','Creative Commons CC-BY-SA 4.0'),('cc-by1.0','http://purl.org/NET/rdflicense/cc-by1.0','Creative Commons CC-BY 1.0'),('cc-by2.0','http://purl.org/NET/rdflicense/cc-by2.0','Creative Commons CC-BY 2.0'),('cc-by2.0at','http://purl.org/NET/rdflicense/cc-by2.0at','Creative Commons CC-BY 2.0 Austria 2.0'),('cc-by2.0au','http://purl.org/NET/rdflicense/cc-by2.0au','Creative Commons CC-BY 2.0 Australia 2.0'),('cc-by2.0be','http://purl.org/NET/rdflicense/cc-by2.0be','Creative Commons CC-BY Belgium 2.0'),('cc-by2.0br','http://purl.org/NET/rdflicense/cc-by2.0br','Creative Commons CC-BY 2.0 Brazil 2.0'),('cc-by2.0ca','http://purl.org/NET/rdflicense/cc-by2.0ca','Creative Commons CC-BY Canada 2.0'),('cc-by2.0cl','http://purl.org/NET/rdflicense/cc-by2.0cl','Creative Commons CC-BY Chile 2.0'),('cc-by2.0de','http://purl.org/NET/rdflicense/cc-by2.0de','Creative Commons CC-BY Germany 2.0'),('cc-by2.0es','http://purl.org/NET/rdflicense/cc-by2.0es','Creative Commons CC-BY Spain 2.0'),('cc-by2.0fr','http://purl.org/NET/rdflicense/cc-by2.0fr','Creative Commons CC-BY France 2.0'),('cc-by2.0it','http://purl.org/NET/rdflicense/cc-by2.0it','Creative Commons CC-BY Italy 2.0'),('cc-by2.0jp','http://purl.org/NET/rdflicense/cc-by2.0jp','Creative Commons CC-BY Japan 2.0'),('cc-by2.0kr','http://purl.org/NET/rdflicense/cc-by2.0kr','Creative Commons CC-BY Korea 2.0'),('cc-by2.0nl','http://purl.org/NET/rdflicense/cc-by2.0nl','Creative Commons CC-BY Netherlands 2.0'),('cc-by2.0uk','http://purl.org/NET/rdflicense/cc-by2.0uk','Creative Commons CC-BY England & Wales 2.0'),('cc-by2.0za','http://purl.org/NET/rdflicense/cc-by2.0za','Creative Commons CC-BY South Africa 2.0'),('cc-by2.5ar','http://purl.org/NET/rdflicense/cc-by2.5ar','Creative Commons CC-BY Argentina 2.5'),('cc-by2.5bg','http://purl.org/NET/rdflicense/cc-by2.5bg','Creative Commons CC-BY Bulgaria 2.5'),('cc-by2.5ch','http://purl.org/NET/rdflicense/cc-by2.5ch','Creative Commons CC-BY Switzerland 2.5'),('cc-by2.5cz','http://purl.org/NET/rdflicense/cc-by2.5cz','Creative Commons CC-BY Czech Republic 2.5'),('cc-by2.5dk','http://purl.org/NET/rdflicense/cc-by2.5dk','Creative Commons CC-BY Denmark 2.5'),('cc-by2.5hu','http://purl.org/NET/rdflicense/cc-by2.5hu','Creative Commons CC-BY Hungary 2.5'),('cc-by2.5il','http://purl.org/NET/rdflicense/cc-by2.5il','Creative Commons CC-BY Israel 2.5'),('cc-by2.5in','http://purl.org/NET/rdflicense/cc-by2.5in','Creative Commons CC-BY India 2.5'),('cc-by2.5mx','http://purl.org/NET/rdflicense/cc-by2.5mx','Creative Commons CC-BY Mexico 2.5'),('cc-by2.5pe','http://purl.org/NET/rdflicense/cc-by2.5pe','Creative Commons CC-BY 2.5 Peru 2.5'),('cc-by2.5pt','http://purl.org/NET/rdflicense/cc-by2.5pt','Creative Commons CC-BY 2.5 Portugal 2.5'),('cc-by2.5scotland','http://purl.org/NET/rdflicense/cc-by2.5scotland','Creative Commons CC-BY Scotland 2.5'),('cc-by2.5se','http://purl.org/NET/rdflicense/cc-by2.5se','Creative Commons CC-BY 2.5 Sweden 2.5'),('cc-by3.0','http://purl.org/NET/rdflicense/cc-by3.0','Creative Commons CC-BY 3.0'),('cc-by3.0at','http://purl.org/NET/rdflicense/cc-by3.0at','Creative Commons CC-BY 3.0 Austria 3.0'),('cc-by3.0au','http://purl.org/NET/rdflicense/cc-by3.0au','Creative Commons CC-BY 3.0 Australia 3.0'),('cc-by3.0br','http://purl.org/NET/rdflicense/cc-by3.0br','Creative Commons CC-BY 3.0 Brazil 3.0'),('cc-by3.0ch','http://purl.org/NET/rdflicense/cc-by3.0ch','Creative Commons CC-BY 3.0 Switzerland 3.0'),('cc-by3.0cl','http://purl.org/NET/rdflicense/cc-by3.0cl','Creative Commons CC-BY 3.0 Chile 3.0'),('cc-by3.0cn','http://purl.org/NET/rdflicense/cc-by3.0cn','Creative Commons CC-BY 3.0 China 3.0'),('cc-by3.0de','http://purl.org/NET/rdflicense/cc-by3.0de','Creative Commons CC-BY 3.0 Germany 3.0'),('cc-by3.0ec','http://purl.org/NET/rdflicense/cc-by3.0ec','Creative Commons CC-BY 3.0 Ecuador 3.0'),('cc-by3.0eg','http://purl.org/NET/rdflicense/cc-by3.0eg','Creative Commons CC-BY Egypt 3.0'),('cc-by3.0es','http://purl.org/NET/rdflicense/cc-by3.0es','Creative Commons CC-BY 3.0 Spain 3.0'),('cc-by3.0fr','http://purl.org/NET/rdflicense/cc-by3.0fr','Creative Commons CC-BY 3.0 France 3.0'),('cc-by3.0gr','http://purl.org/NET/rdflicense/cc-by3.0gr','Creative Commons CC-BY Greece 3.0'),('cc-by3.0ie','http://purl.org/NET/rdflicense/cc-by3.0ie','Creative Commons CC-BY Ireland 3.0'),('cc-by3.0it','http://purl.org/NET/rdflicense/cc-by3.0it','Creative Commons CC-BY 3.0 Italy 3.0'),('cc-by3.0lu','http://purl.org/NET/rdflicense/cc-by3.0lu','Creative Commons CC-BY Luxemburg 3.0'),('cc-by3.0nl','http://purl.org/NET/rdflicense/cc-by3.0nl','Creative Commons CC-BY 3.0 Netherlands 3.0'),('cc-by3.0nz','http://purl.org/NET/rdflicense/cc-by3.0nz','Creative Commons CC-BY New Zealand 3.0'),('cc-by3.0pl','http://purl.org/NET/rdflicense/cc-by3.0pl','Creative Commons CC-BY 3.0 Poland 3.0'),('cc-by3.0pt','http://purl.org/NET/rdflicense/cc-by3.0pt','Creative Commons CC-BY Portugal 3.0'),('cc-by3.0ro','http://purl.org/NET/rdflicense/cc-by3.0ro','Creative Commons CC-BY 3.0 Romania 3.0'),('cc-by3.0th','http://purl.org/NET/rdflicense/cc-by3.0th','Creative Commons CC-BY Thailand 3.0'),('cc-by3.0us','http://purl.org/NET/rdflicense/cc-by3.0us','Creative Commons CC-BY United States 3.0'),('cc-by3.0ve','http://purl.org/NET/rdflicense/cc-by3.0ve','Creative Commons CC-BY Venezuela 3.0'),('cc-by4.0','http://purl.org/NET/rdflicense/cc-by4.0','Creative Commons CC-BY 4.0'),('cc-zero1.0','http://purl.org/NET/rdflicense/cc-zero1.0','Creative Commons CC0 1.0'),('CDDL1.0','http://purl.org/NET/rdflicense/CDDL1.0','Common Development and Distribution License 1.0'),('clarin_aca_by','http://purl.org/NET/rdflicense/clarin_aca_by','CLARIN ACAdemic BY 1.0'),('COLORIURIS1.0','http://purl.org/NET/rdflicense/COLORIURIS1.0','ColorIURIS Copyright'),('COMMON1.0','http://purl.org/NET/rdflicense/COMMON1.0','Common Public License 1.0'),('CRYPTIX1.0','http://purl.org/NET/rdflicense/CRYPTIX1.0','Cryptix General License'),('ECLIPSE1.0','http://purl.org/NET/rdflicense/ECLIPSE1.0','Eclipse Public License 1.0'),('elra-end-user','http://purl.org/NET/rdflicense/elra-end-user','Language Resources End-User Agreement'),('elra-var','http://purl.org/NET/rdflicense/elra-var','LANGUAGE RESOURCES VALUE-ADDED-RESELLER AGREEMENT'),('EUC1.0','http://purl.org/NET/rdflicense/EUC1.0','European Commission Copyright 1.0'),('EUPL1.1','http://purl.org/NET/rdflicense/EUPL1.1','European Union Public License 1.1'),('fal1.3','http://purl.org/NET/rdflicense/fal1.3','Free Art License@en 1.3'),('FREEBSD1.0','http://purl.org/NET/rdflicense/FREEBSD1.0','Free BSD Documentation License'),('gfdl1.1','http://purl.org/NET/rdflicense/gfdl1.1','GNU Free Documentation License 1.1'),('gfdl1.3','http://purl.org/NET/rdflicense/gfdl1.3','GNU Free Documentation License 1.3'),('GOVTRACK1.0','http://purl.org/NET/rdflicense/GOVTRACK1.0','Govtrack License'),('gpl1.0','http://purl.org/NET/rdflicense/gpl1.0','GNU General Public License 1.0'),('gpl2.0','http://purl.org/NET/rdflicense/gpl2.0','GNU General Public License 2.0'),('gpl3.0','http://purl.org/NET/rdflicense/gpl3.0','GNU General Public License 3.0'),('IBM1.0','http://purl.org/NET/rdflicense/IBM1.0','IBM Public License 1.0'),('iodl1.0','http://purl.org/NET/rdflicense/iodl1.0','Italian Open Data License v1.0 1.0'),('lgpl2.0','http://purl.org/NET/rdflicense/lgpl2.0','GNU Library General Public License 2.0'),('lgpl2.1','http://purl.org/NET/rdflicense/lgpl2.1','GNU Lesser General Public License 2.1'),('lgpl3.0','http://purl.org/NET/rdflicense/lgpl3.0','GNU Lesser General Public License 3.0'),('MICROSOFT1.0','http://purl.org/NET/rdflicense/MICROSOFT1.0','Microsoft Reference Source License'),('MIT1.0','http://purl.org/NET/rdflicense/MIT1.0','MIT License 1.0'),('MOZILLA2.0','http://purl.org/NET/rdflicense/MOZILLA2.0','Mozilla Public License 2.0'),('ms-c-nored','http://purl.org/NET/rdflicense/ms-c-nored','META-SHARE Commercial NoRedistribution 1.0'),('﻿ms-c-nored-ff','http://purl.org/NET/rdflicense/ms-c-nored-ff','META-SHARE Commercial NoRedistribution For-A-Fee 1.0'),('ms-commons-byncnd','http://purl.org/NET/rdflicense/ms-commons-byncnd','META-SHARE Commons BYNCND 1.0'),('NDL1.0','http://purl.org/NET/rdflicense/NDL1.0','Web NDL Authority License'),('odbc-by1.0','http://purl.org/NET/rdflicense/odbc-by1.0','Open Data Commons Attribution License 1.0'),('odbc-pddl1.0','http://purl.org/NET/rdflicense/odbc-pddl1.0','Open Data Commons Public Domain Dedication and License 1.0'),('odbl1.0','http://purl.org/NET/rdflicense/odbl1.0','Open Data Commons Open Database License 1.0'),('OGCDocument1.0','http://purl.org/NET/rdflicense/OGCDocument1.0','Open Geospatial Consortium Document 1.0'),('OGCSoftware1.0','http://purl.org/NET/rdflicense/OGCSoftware1.0','Open Geospatial Consortium Software 1.0'),('ogl-nc1.0','http://purl.org/NET/rdflicense/ogl-nc1.0','UK NonCommercial Government License 1.0'),('OGL1.0','http://purl.org/NET/rdflicense/OGL1.0','UK NonCommercial Government License 1.0'),('OL1.0','http://purl.org/NET/rdflicense/OL1.0','License Ouverte 1.0'),('ORACLE1.0','http://purl.org/NET/rdflicense/ORACLE1.0','Oracle Berkely DB License 1.0'),('OS3.0','http://purl.org/NET/rdflicense/OS3.0','OS Open Data License 3.0'),('PDM1.0','http://purl.org/NET/rdflicense/PDM1.0','Public Domain Mark 1.0'),('publicdomain','http://purl.org/NET/rdflicense/publicdomain','Public domain'),('simple2.0','http://purl.org/NET/rdflicense/simple2.0','Simple Public License 2.0'),('ukogl-nc2.0','http://purl.org/NET/rdflicense/ukogl-nc2.0','Open Government Licence Non-Commercial 2.0'),('ukogl1.0','http://purl.org/NET/rdflicense/ukogl1.0','Open Government Licence 1.0'),('ukogl2.0','http://purl.org/NET/rdflicense/ukogl2.0','Open Government Licence 2.0'),('ukogl3.0','http://purl.org/NET/rdflicense/ukogl3.0','Open Government Licence 3.0'),('W3C1.0','http://purl.org/NET/rdflicense/W3C1.0','W3C Software Notice and License 1.0');
/*!40000 ALTER TABLE `license` ENABLE KEYS */;

--
-- Table structure for table `log_generation_distribution`
--

DROP TABLE IF EXISTS `log_generation_distribution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_generation_distribution` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `distribution` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DistributionGenerationStatusType)',
  `errors` json DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_85B347B0A4483781` (`distribution`),
  CONSTRAINT `FK_85B347B0A4483781` FOREIGN KEY (`distribution`) REFERENCES `distribution_contents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_generation_distribution`
--

/*!40000 ALTER TABLE `log_generation_distribution` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_generation_distribution` ENABLE KEYS */;

--
-- Table structure for table `log_generation_distribution_record`
--

DROP TABLE IF EXISTS `log_generation_distribution_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_generation_distribution_record` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `record` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `log` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:DistributionGenerationStatusType)',
  `errors` json DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_DDCF39AB9B349F91E67F9749` (`record`,`study`),
  KEY `IDX_DDCF39AB8F3F68C5` (`log`),
  CONSTRAINT `FK_DDCF39AB8F3F68C5` FOREIGN KEY (`log`) REFERENCES `log_generation_distribution` (`id`),
  CONSTRAINT `FK_DDCF39AB9B349F91E67F9749` FOREIGN KEY (`record`, `study`) REFERENCES `castor_record` (`record_id`, `study_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_generation_distribution_record`
--

/*!40000 ALTER TABLE `log_generation_distribution_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_generation_distribution_record` ENABLE KEYS */;

--
-- Table structure for table `metadata`
--

DROP TABLE IF EXISTS `metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `title` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `description` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `language` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:version)',
  `landing_page` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:iri)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `dtype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4F1434142B36786B` (`title`),
  UNIQUE KEY `UNIQ_4F1434146DE44026` (`description`),
  KEY `IDX_4F143414D4DB71B5` (`language`),
  KEY `IDX_4F1434145768F419` (`license`),
  KEY `IDX_4F143414DE12AB56` (`created_by`),
  KEY `IDX_4F14341416FE72E1` (`updated_by`),
  CONSTRAINT `FK_4F14341416FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_4F1434142B36786B` FOREIGN KEY (`title`) REFERENCES `text_localized` (`id`),
  CONSTRAINT `FK_4F1434145768F419` FOREIGN KEY (`license`) REFERENCES `license` (`slug`),
  CONSTRAINT `FK_4F1434146DE44026` FOREIGN KEY (`description`) REFERENCES `text_localized` (`id`),
  CONSTRAINT `FK_4F143414D4DB71B5` FOREIGN KEY (`language`) REFERENCES `language` (`code`),
  CONSTRAINT `FK_4F143414DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata`
--

/*!40000 ALTER TABLE `metadata` DISABLE KEYS */;
INSERT INTO `metadata` (`id`, `title`, `description`, `language`, `license`, `created_by`, `updated_by`, `version`, `landing_page`, `created_at`, `updated_at`, `dtype`) VALUES ('78371032-1d25-11eb-91d6-4e92c6a65be2','78372c8d-1d25-11eb-91d6-4e92c6a65be2','78373ef0-1d25-11eb-91d6-4e92c6a65be2','en','allrightsreserved','24db9952-02ed-11eb-888f-4e92c6a65be2','24db9952-02ed-11eb-888f-4e92c6a65be2','1.0.0','','2020-11-02 16:07:13',NULL,'fairdatapointmetadata');
/*!40000 ALTER TABLE `metadata` ENABLE KEYS */;

--
-- Table structure for table `metadata_catalog`
--

DROP TABLE IF EXISTS `metadata_catalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_catalog` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `catalog` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `homepage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:iri)',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:iri)',
  PRIMARY KEY (`id`),
  KEY `IDX_895DE2BF1B2C3247` (`catalog`),
  CONSTRAINT `FK_895DE2BF1B2C3247` FOREIGN KEY (`catalog`) REFERENCES `catalog` (`id`),
  CONSTRAINT `FK_895DE2BFBF396750` FOREIGN KEY (`id`) REFERENCES `metadata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_catalog`
--

/*!40000 ALTER TABLE `metadata_catalog` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_catalog` ENABLE KEYS */;

--
-- Table structure for table `metadata_catalog_themetaxonomies`
--

DROP TABLE IF EXISTS `metadata_catalog_themetaxonomies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_catalog_themetaxonomies` (
  `catalog_metadata_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `ontology_concept_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`catalog_metadata_id`,`ontology_concept_id`),
  KEY `IDX_ED914358CD2AC8F7` (`catalog_metadata_id`),
  KEY `IDX_ED9143584ACEC524` (`ontology_concept_id`),
  CONSTRAINT `FK_ED9143584ACEC524` FOREIGN KEY (`ontology_concept_id`) REFERENCES `ontology_concept` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ED914358CD2AC8F7` FOREIGN KEY (`catalog_metadata_id`) REFERENCES `metadata_catalog` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_catalog_themetaxonomies`
--

/*!40000 ALTER TABLE `metadata_catalog_themetaxonomies` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_catalog_themetaxonomies` ENABLE KEYS */;

--
-- Table structure for table `metadata_dataset`
--

DROP TABLE IF EXISTS `metadata_dataset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_dataset` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `dataset` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `keyword` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_25D191285A93713B` (`keyword`),
  KEY `IDX_25D19128B7A041D0` (`dataset`),
  CONSTRAINT `FK_25D191285A93713B` FOREIGN KEY (`keyword`) REFERENCES `text_localized` (`id`),
  CONSTRAINT `FK_25D19128B7A041D0` FOREIGN KEY (`dataset`) REFERENCES `dataset` (`id`),
  CONSTRAINT `FK_25D19128BF396750` FOREIGN KEY (`id`) REFERENCES `metadata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_dataset`
--

/*!40000 ALTER TABLE `metadata_dataset` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_dataset` ENABLE KEYS */;

--
-- Table structure for table `metadata_dataset_themes`
--

DROP TABLE IF EXISTS `metadata_dataset_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_dataset_themes` (
  `dataset_metadata_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `ontology_concept_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`dataset_metadata_id`,`ontology_concept_id`),
  KEY `IDX_E5606EA54C040FE1` (`dataset_metadata_id`),
  KEY `IDX_E5606EA54ACEC524` (`ontology_concept_id`),
  CONSTRAINT `FK_E5606EA54ACEC524` FOREIGN KEY (`ontology_concept_id`) REFERENCES `ontology_concept` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E5606EA54C040FE1` FOREIGN KEY (`dataset_metadata_id`) REFERENCES `metadata_dataset` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_dataset_themes`
--

/*!40000 ALTER TABLE `metadata_dataset_themes` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_dataset_themes` ENABLE KEYS */;

--
-- Table structure for table `metadata_distribution`
--

DROP TABLE IF EXISTS `metadata_distribution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_distribution` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `distribution` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  KEY `IDX_EC1C7F7BA4483781` (`distribution`),
  CONSTRAINT `FK_EC1C7F7BA4483781` FOREIGN KEY (`distribution`) REFERENCES `distribution` (`id`),
  CONSTRAINT `FK_EC1C7F7BBF396750` FOREIGN KEY (`id`) REFERENCES `metadata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_distribution`
--

/*!40000 ALTER TABLE `metadata_distribution` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_distribution` ENABLE KEYS */;

--
-- Table structure for table `metadata_fdp`
--

DROP TABLE IF EXISTS `metadata_fdp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_fdp` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `fdp` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  KEY `IDX_9B30AE35E28FB11F` (`fdp`),
  CONSTRAINT `FK_9B30AE35BF396750` FOREIGN KEY (`id`) REFERENCES `metadata` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9B30AE35E28FB11F` FOREIGN KEY (`fdp`) REFERENCES `fdp` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_fdp`
--

/*!40000 ALTER TABLE `metadata_fdp` DISABLE KEYS */;
INSERT INTO `metadata_fdp` (`id`, `fdp`) VALUES ('78371032-1d25-11eb-91d6-4e92c6a65be2','075d8315-cf3f-11e9-99e5-eb3442afa83d');
/*!40000 ALTER TABLE `metadata_fdp` ENABLE KEYS */;

--
-- Table structure for table `metadata_publishers`
--

DROP TABLE IF EXISTS `metadata_publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_publishers` (
  `metadata_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `agent_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`metadata_id`,`agent_id`),
  KEY `IDX_99C06534DC9EE959` (`metadata_id`),
  KEY `IDX_99C065343414710B` (`agent_id`),
  CONSTRAINT `FK_99C065343414710B` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_99C06534DC9EE959` FOREIGN KEY (`metadata_id`) REFERENCES `metadata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_publishers`
--

/*!40000 ALTER TABLE `metadata_publishers` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_publishers` ENABLE KEYS */;

--
-- Table structure for table `metadata_study`
--

DROP TABLE IF EXISTS `metadata_study`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_study` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `study_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `studied_condition` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `intervention` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `brief_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scientific_name` longtext COLLATE utf8mb4_unicode_ci,
  `brief_summary` longtext COLLATE utf8mb4_unicode_ci,
  `summary` longtext COLLATE utf8mb4_unicode_ci,
  `estimated_enrollment` int(11) DEFAULT NULL,
  `estimated_study_start_date` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `estimated_study_completion_date` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `study_completion_date` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `study_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:StudyType)',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:iri)',
  `recruitment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:RecruitmentStatusType)',
  `consent_publish` tinyint(1) DEFAULT NULL,
  `consent_social_media` tinyint(1) DEFAULT NULL,
  `method_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:MethodType)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:version)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `keyword` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_41C2F2BD11814AB` (`intervention`),
  UNIQUE KEY `UNIQ_41C2F2B9F6690FD` (`studied_condition`),
  UNIQUE KEY `UNIQ_41C2F2B5A93713B` (`keyword`),
  KEY `IDX_41C2F2BE7B003E9` (`study_id`),
  KEY `IDX_41C2F2BDE12AB56` (`created_by`),
  KEY `IDX_41C2F2B16FE72E1` (`updated_by`),
  CONSTRAINT `FK_41C2F2B16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_41C2F2B5A93713B` FOREIGN KEY (`keyword`) REFERENCES `text_localized` (`id`),
  CONSTRAINT `FK_41C2F2B9F6690FD` FOREIGN KEY (`studied_condition`) REFERENCES `text_coded` (`id`),
  CONSTRAINT `FK_41C2F2BD11814AB` FOREIGN KEY (`intervention`) REFERENCES `text_coded` (`id`),
  CONSTRAINT `FK_41C2F2BDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_41C2F2BE7B003E9` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_study`
--

/*!40000 ALTER TABLE `metadata_study` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_study` ENABLE KEYS */;

--
-- Table structure for table `metadata_study_centers`
--

DROP TABLE IF EXISTS `metadata_study_centers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_study_centers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `metadata` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `organization` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4F7869194F143414` (`metadata`),
  KEY `IDX_4F786919C1EE637C` (`organization`),
  KEY `IDX_4F786919DE12AB56` (`created_by`),
  KEY `IDX_4F78691916FE72E1` (`updated_by`),
  CONSTRAINT `FK_4F78691916FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_4F7869194F143414` FOREIGN KEY (`metadata`) REFERENCES `metadata_study` (`id`),
  CONSTRAINT `FK_4F786919C1EE637C` FOREIGN KEY (`organization`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_4F786919DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_study_centers`
--

/*!40000 ALTER TABLE `metadata_study_centers` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_study_centers` ENABLE KEYS */;

--
-- Table structure for table `metadata_study_centers_departments`
--

DROP TABLE IF EXISTS `metadata_study_centers_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_study_centers_departments` (
  `participating_center_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `department_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`participating_center_id`,`department_id`),
  KEY `IDX_168E7C40D75DAFF6` (`participating_center_id`),
  KEY `IDX_168E7C40AE80F5DF` (`department_id`),
  CONSTRAINT `FK_168E7C40AE80F5DF` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_168E7C40D75DAFF6` FOREIGN KEY (`participating_center_id`) REFERENCES `metadata_study_centers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_study_centers_departments`
--

/*!40000 ALTER TABLE `metadata_study_centers_departments` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_study_centers_departments` ENABLE KEYS */;

--
-- Table structure for table `metadata_study_conditions`
--

DROP TABLE IF EXISTS `metadata_study_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_study_conditions` (
  `study_metadata_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `ontology_concept_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`study_metadata_id`,`ontology_concept_id`),
  KEY `IDX_4278168091AB1465` (`study_metadata_id`),
  KEY `IDX_427816804ACEC524` (`ontology_concept_id`),
  CONSTRAINT `FK_427816804ACEC524` FOREIGN KEY (`ontology_concept_id`) REFERENCES `ontology_concept` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4278168091AB1465` FOREIGN KEY (`study_metadata_id`) REFERENCES `metadata_study` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_study_conditions`
--

/*!40000 ALTER TABLE `metadata_study_conditions` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_study_conditions` ENABLE KEYS */;

--
-- Table structure for table `metadata_study_team`
--

DROP TABLE IF EXISTS `metadata_study_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_study_team` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `metadata` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `person` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `is_contact` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A6D5E8694F143414` (`metadata`),
  KEY `IDX_A6D5E86934DCD176` (`person`),
  KEY `IDX_A6D5E869DE12AB56` (`created_by`),
  KEY `IDX_A6D5E86916FE72E1` (`updated_by`),
  CONSTRAINT `FK_A6D5E86916FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_A6D5E86934DCD176` FOREIGN KEY (`person`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_A6D5E8694F143414` FOREIGN KEY (`metadata`) REFERENCES `metadata_study` (`id`),
  CONSTRAINT `FK_A6D5E869DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_study_team`
--

/*!40000 ALTER TABLE `metadata_study_team` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_study_team` ENABLE KEYS */;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_versions` (
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
INSERT INTO `migration_versions` (`version`, `executed_at`, `execution_time`) VALUES ('DoctrineMigrations\\Version20220317091351','2022-03-17 09:58:26',101);
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;

--
-- Table structure for table `ontology`
--

DROP TABLE IF EXISTS `ontology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ontology` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:iri)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio_portal_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `0454bc56-454b-48cd-9de3-b8b6c288db13` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http://purl.bioontology.org/ontology/PATO/` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Phenotypic Quality Ontology` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PATO` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ontology`
--

/*!40000 ALTER TABLE `ontology` DISABLE KEYS */;
INSERT INTO `ontology` (`id`, `url`, `name`, `bio_portal_id`, `0454bc56-454b-48cd-9de3-b8b6c288db13`, `http://purl.bioontology.org/ontology/PATO/`, `Phenotypic Quality Ontology`, `PATO`) VALUES ('0454bc56-454b-48cd-9de3-b8b6c288db13','http://purl.bioontology.org/ontology/PATO/','Phenotypic Quality Ontology','PATO',NULL,NULL,NULL,NULL),('6963da22-fe2e-41b9-a55c-f751c56272f7','http://purl.bioontology.org/ontology/SNOMEDCT','SNOMED CT','SNOMEDCT',NULL,NULL,NULL,NULL),('93a4f622-f86c-4533-aab6-2962003cfa9c','https://purl.org/vodan/whocovid19crfsemdatamodel/','WHO COVID-19 Rapid Version CRF semantic data model','COVIDCRFRAPID',NULL,NULL,NULL,NULL),('dc463847-3105-4f03-ba04-4b5226e274f3','http://purl.bioontology.org/ontology/HL7/','Health Level Seven Reference Implementation Model, Version 3','HL7',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ontology` ENABLE KEYS */;

--
-- Table structure for table `ontology_concept`
--

DROP TABLE IF EXISTS `ontology_concept`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ontology_concept` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `ontology` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:iri)',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5972B8FFDAF05D3` (`ontology`),
  KEY `ontology_code` (`ontology`,`code`),
  CONSTRAINT `FK_5972B8FFDAF05D3` FOREIGN KEY (`ontology`) REFERENCES `ontology` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ontology_concept`
--

/*!40000 ALTER TABLE `ontology_concept` DISABLE KEYS */;
/*!40000 ALTER TABLE `ontology_concept` ENABLE KEYS */;

--
-- Table structure for table `orcid_user`
--

DROP TABLE IF EXISTS `orcid_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orcid_user` (
  `orcid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`orcid`),
  UNIQUE KEY `UNIQ_D8886EB5A76ED395` (`user_id`),
  CONSTRAINT `FK_D8886EB5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orcid_user`
--

/*!40000 ALTER TABLE `orcid_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `orcid_user` ENABLE KEYS */;

--
-- Table structure for table `organization`
--

DROP TABLE IF EXISTS `organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `homepage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:iri)',
  `country` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coordinates_latitude` decimal(10,8) DEFAULT NULL,
  `coordinates_longitude` decimal(11,8) DEFAULT NULL,
  `grid_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C1EE637C5373C966` (`country`),
  KEY `grid_id` (`grid_id`),
  CONSTRAINT `FK_C1EE637C5373C966` FOREIGN KEY (`country`) REFERENCES `country` (`code`),
  CONSTRAINT `FK_C1EE637CBF396750` FOREIGN KEY (`id`) REFERENCES `agent` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization`
--

/*!40000 ALTER TABLE `organization` DISABLE KEYS */;
/*!40000 ALTER TABLE `organization` ENABLE KEYS */;

--
-- Table structure for table `permission_catalog`
--

DROP TABLE IF EXISTS `permission_catalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_catalog` (
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `catalog_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:PermissionType)',
  PRIMARY KEY (`user_id`,`catalog_id`),
  KEY `IDX_2C3DD310A76ED395` (`user_id`),
  KEY `IDX_2C3DD310CC3C66FC` (`catalog_id`),
  CONSTRAINT `FK_2C3DD310A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_2C3DD310CC3C66FC` FOREIGN KEY (`catalog_id`) REFERENCES `catalog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_catalog`
--

/*!40000 ALTER TABLE `permission_catalog` DISABLE KEYS */;
/*!40000 ALTER TABLE `permission_catalog` ENABLE KEYS */;

--
-- Table structure for table `permission_data_specification`
--

DROP TABLE IF EXISTS `permission_data_specification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_data_specification` (
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `data_specification_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:PermissionType)',
  PRIMARY KEY (`user_id`,`data_specification_id`),
  KEY `IDX_426BAC0EA76ED395` (`user_id`),
  KEY `IDX_426BAC0E13281BD0` (`data_specification_id`),
  CONSTRAINT `FK_426BAC0E13281BD0` FOREIGN KEY (`data_specification_id`) REFERENCES `data_specification` (`id`),
  CONSTRAINT `FK_426BAC0EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_data_specification`
--

/*!40000 ALTER TABLE `permission_data_specification` DISABLE KEYS */;
/*!40000 ALTER TABLE `permission_data_specification` ENABLE KEYS */;

--
-- Table structure for table `permission_dataset`
--

DROP TABLE IF EXISTS `permission_dataset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_dataset` (
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `dataset_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:PermissionType)',
  PRIMARY KEY (`user_id`,`dataset_id`),
  KEY `IDX_80B1A087A76ED395` (`user_id`),
  KEY `IDX_80B1A087D47C2D1B` (`dataset_id`),
  CONSTRAINT `FK_80B1A087A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_80B1A087D47C2D1B` FOREIGN KEY (`dataset_id`) REFERENCES `dataset` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_dataset`
--

/*!40000 ALTER TABLE `permission_dataset` DISABLE KEYS */;
/*!40000 ALTER TABLE `permission_dataset` ENABLE KEYS */;

--
-- Table structure for table `permission_distribution`
--

DROP TABLE IF EXISTS `permission_distribution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_distribution` (
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `distribution_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:PermissionType)',
  PRIMARY KEY (`user_id`,`distribution_id`),
  KEY `IDX_CD5B6B74A76ED395` (`user_id`),
  KEY `IDX_CD5B6B746EB6DDB5` (`distribution_id`),
  CONSTRAINT `FK_CD5B6B746EB6DDB5` FOREIGN KEY (`distribution_id`) REFERENCES `distribution` (`id`),
  CONSTRAINT `FK_CD5B6B74A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_distribution`
--

/*!40000 ALTER TABLE `permission_distribution` DISABLE KEYS */;
/*!40000 ALTER TABLE `permission_distribution` ENABLE KEYS */;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orcid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:iri)',
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `name_origin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:NameOriginType)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_34DCD176A76ED395` (`user_id`),
  CONSTRAINT `FK_34DCD176A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_34DCD176BF396750` FOREIGN KEY (`id`) REFERENCES `agent` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--

/*!40000 ALTER TABLE `person` DISABLE KEYS */;
/*!40000 ALTER TABLE `person` ENABLE KEYS */;

--
-- Table structure for table `study`
--

DROP TABLE IF EXISTS `study`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entered_manually` tinyint(1) NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `source_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:StudySource)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E67F9749DE12AB56` (`created_by`),
  KEY `IDX_E67F974916FE72E1` (`updated_by`),
  KEY `slug` (`slug`),
  CONSTRAINT `FK_E67F974916FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_E67F9749DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study`
--

/*!40000 ALTER TABLE `study` DISABLE KEYS */;
/*!40000 ALTER TABLE `study` ENABLE KEYS */;

--
-- Table structure for table `study_castor`
--

DROP TABLE IF EXISTS `study_castor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_castor` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `server` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4DB9B2045A6DD5F6` (`server`),
  CONSTRAINT `FK_4DB9B2045A6DD5F6` FOREIGN KEY (`server`) REFERENCES `castor_server` (`id`),
  CONSTRAINT `FK_4DB9B204BF396750` FOREIGN KEY (`id`) REFERENCES `study` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_castor`
--

/*!40000 ALTER TABLE `study_castor` DISABLE KEYS */;
/*!40000 ALTER TABLE `study_castor` ENABLE KEYS */;

--
-- Table structure for table `text_coded`
--

DROP TABLE IF EXISTS `text_coded`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `text_coded` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `text_coded`
--

/*!40000 ALTER TABLE `text_coded` DISABLE KEYS */;
/*!40000 ALTER TABLE `text_coded` ENABLE KEYS */;

--
-- Table structure for table `text_localized`
--

DROP TABLE IF EXISTS `text_localized`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `text_localized` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `text_localized`
--

/*!40000 ALTER TABLE `text_localized` DISABLE KEYS */;
INSERT INTO `text_localized` (`id`) VALUES ('017b62aa-03e9-11eb-9825-4e92c6a65be2'),('017b7a93-03e9-11eb-9825-4e92c6a65be2'),('01afafac-9573-11ec-84c0-6a492e0db66d'),('01b01623-9573-11ec-84c0-6a492e0db66d'),('069dddf4-03e9-11eb-9825-4e92c6a65be2'),('069de9f5-03e9-11eb-9825-4e92c6a65be2'),('076187b1-cf3f-11e9-99e5-eb3442afa83d'),('0766b08e-cf3f-11e9-99e5-eb3442afa83d'),('0be7a57d-03e9-11eb-9825-4e92c6a65be2'),('0be7b63f-03e9-11eb-9825-4e92c6a65be2'),('0e4938a4-51c4-11ec-8083-6266d7089f06'),('0e498981-51c4-11ec-8083-6266d7089f06'),('0e49f4d5-51c4-11ec-8083-6266d7089f06'),('129302d6-03e9-11eb-9825-4e92c6a65be2'),('129316d5-03e9-11eb-9825-4e92c6a65be2'),('171812cb-03e9-11eb-9825-4e92c6a65be2'),('171b3aa6-03e9-11eb-9825-4e92c6a65be2'),('1e29884e-20f0-11ec-9209-f27b926a5e95'),('1e2a37b7-20f0-11ec-9209-f27b926a5e95'),('212a4f1d-8a78-11ec-84c0-6a492e0db66d'),('212ac635-8a78-11ec-84c0-6a492e0db66d'),('22d9661f-03e9-11eb-9825-4e92c6a65be2'),('22d9953c-03e9-11eb-9825-4e92c6a65be2'),('25f49ec5-2e53-11eb-91d6-4e92c6a65be2'),('25f4b839-2e53-11eb-91d6-4e92c6a65be2'),('266dcddf-20f0-11ec-9209-f27b926a5e95'),('266e7f88-20f0-11ec-9209-f27b926a5e95'),('27ccde3c-277d-11eb-91d6-4e92c6a65be2'),('27ccfef1-277d-11eb-91d6-4e92c6a65be2'),('2918010f-03e9-11eb-9825-4e92c6a65be2'),('29180ec5-03e9-11eb-9825-4e92c6a65be2'),('2be6f785-204d-11ec-9209-f27b926a5e95'),('2be7e221-204d-11ec-9209-f27b926a5e95'),('2e624709-20f0-11ec-9209-f27b926a5e95'),('2e62fd72-20f0-11ec-9209-f27b926a5e95'),('2e8cf236-9972-11ec-84c0-6a492e0db66d'),('2e8d65b2-9972-11ec-84c0-6a492e0db66d'),('318ee134-9572-11ec-84c0-6a492e0db66d'),('318f2d39-9572-11ec-84c0-6a492e0db66d'),('318f7a7b-9572-11ec-84c0-6a492e0db66d'),('31f9eef0-02ef-11eb-888f-4e92c6a65be2'),('31fa02ca-02ef-11eb-888f-4e92c6a65be2'),('34e4c11e-8a78-11ec-84c0-6a492e0db66d'),('34e5253a-8a78-11ec-84c0-6a492e0db66d'),('36c96954-20f0-11ec-9209-f27b926a5e95'),('36ca34d6-20f0-11ec-9209-f27b926a5e95'),('3aaeb799-9972-11ec-84c0-6a492e0db66d'),('3aaf4548-9972-11ec-84c0-6a492e0db66d'),('3d81f6fd-20f0-11ec-9209-f27b926a5e95'),('3d82b885-20f0-11ec-9209-f27b926a5e95'),('3f957e0e-03e9-11eb-9825-4e92c6a65be2'),('3f9591dc-03e9-11eb-9825-4e92c6a65be2'),('42b0a403-9572-11ec-84c0-6a492e0db66d'),('42b124d1-9572-11ec-84c0-6a492e0db66d'),('42b17157-9572-11ec-84c0-6a492e0db66d'),('45158ef8-20f0-11ec-9209-f27b926a5e95'),('45164238-20f0-11ec-9209-f27b926a5e95'),('45a6697c-03e9-11eb-9825-4e92c6a65be2'),('45a68def-03e9-11eb-9825-4e92c6a65be2'),('471d0509-20ef-11ec-9209-f27b926a5e95'),('471dca50-20ef-11ec-9209-f27b926a5e95'),('4949915a-9f1f-11ec-84c0-6a492e0db66d'),('494a27a2-9f1f-11ec-84c0-6a492e0db66d'),('4a4a5fc0-27dc-11eb-91d6-4e92c6a65be2'),('4a4a77ee-27dc-11eb-91d6-4e92c6a65be2'),('4c7a7c55-03e9-11eb-9825-4e92c6a65be2'),('4c7a926e-03e9-11eb-9825-4e92c6a65be2'),('4cddf174-20f0-11ec-9209-f27b926a5e95'),('4cdf67ca-20f0-11ec-9209-f27b926a5e95'),('4f6951d0-03e8-11eb-9825-4e92c6a65be2'),('4f69730e-03e8-11eb-9825-4e92c6a65be2'),('513d8cd2-03e9-11eb-9825-4e92c6a65be2'),('513da04a-03e9-11eb-9825-4e92c6a65be2'),('53a6514b-20f0-11ec-9209-f27b926a5e95'),('53a6f774-20f0-11ec-9209-f27b926a5e95'),('53b13549-2134-11ec-9209-f27b926a5e95'),('53b20035-2134-11ec-9209-f27b926a5e95'),('58068d51-03e9-11eb-9825-4e92c6a65be2'),('5806aad8-03e9-11eb-9825-4e92c6a65be2'),('5d231b0c-20f0-11ec-9209-f27b926a5e95'),('5d23dff8-20f0-11ec-9209-f27b926a5e95'),('5d5995ed-03e9-11eb-9825-4e92c6a65be2'),('5d59b03e-03e9-11eb-9825-4e92c6a65be2'),('61a6ef66-20ef-11ec-9209-f27b926a5e95'),('61aa720f-20ef-11ec-9209-f27b926a5e95'),('61aeb704-02ed-11eb-888f-4e92c6a65be2'),('61aed52b-02ed-11eb-888f-4e92c6a65be2'),('64f2c832-03e9-11eb-9825-4e92c6a65be2'),('64f2da63-03e9-11eb-9825-4e92c6a65be2'),('654543e1-03e8-11eb-9825-4e92c6a65be2'),('65455b73-03e8-11eb-9825-4e92c6a65be2'),('67271902-20f0-11ec-9209-f27b926a5e95'),('6727c8ba-20f0-11ec-9209-f27b926a5e95'),('6e3e7e9e-03e8-11eb-9825-4e92c6a65be2'),('6e3e93c6-03e8-11eb-9825-4e92c6a65be2'),('6f46d8c4-20f0-11ec-9209-f27b926a5e95'),('6f4798d9-20f0-11ec-9209-f27b926a5e95'),('75e8ff0b-20ef-11ec-9209-f27b926a5e95'),('75e9baaa-20ef-11ec-9209-f27b926a5e95'),('766bd2dc-20f0-11ec-9209-f27b926a5e95'),('766c8dde-20f0-11ec-9209-f27b926a5e95'),('76f6dc34-03e8-11eb-9825-4e92c6a65be2'),('76f6ec2c-03e8-11eb-9825-4e92c6a65be2'),('78372c8d-1d25-11eb-91d6-4e92c6a65be2'),('78373ef0-1d25-11eb-91d6-4e92c6a65be2'),('7cf80e7f-20f0-11ec-9209-f27b926a5e95'),('7cf8c92e-20f0-11ec-9209-f27b926a5e95'),('7fa7b13a-03e8-11eb-9825-4e92c6a65be2'),('7fa7c6e4-03e8-11eb-9825-4e92c6a65be2'),('8087622c-20ef-11ec-9209-f27b926a5e95'),('80882fdb-20ef-11ec-9209-f27b926a5e95'),('8109a781-1d25-11eb-91d6-4e92c6a65be2'),('8109b591-1d25-11eb-91d6-4e92c6a65be2'),('87a9d4a9-03e8-11eb-9825-4e92c6a65be2'),('87a9ea2b-03e8-11eb-9825-4e92c6a65be2'),('88ac9b6c-20f0-11ec-9209-f27b926a5e95'),('88ad4678-20f0-11ec-9209-f27b926a5e95'),('8f7ed2d8-20f0-11ec-9209-f27b926a5e95'),('8f7f83e8-20f0-11ec-9209-f27b926a5e95'),('90cc1014-20ef-11ec-9209-f27b926a5e95'),('90ccd2e7-20ef-11ec-9209-f27b926a5e95'),('913b9bba-03e8-11eb-9825-4e92c6a65be2'),('913bad5b-03e8-11eb-9825-4e92c6a65be2'),('9730f534-20ef-11ec-9209-f27b926a5e95'),('9731b2aa-20ef-11ec-9209-f27b926a5e95'),('98cea9b5-20f0-11ec-9209-f27b926a5e95'),('98cf64bb-20f0-11ec-9209-f27b926a5e95'),('9c3222dd-1dcc-11eb-91d6-4e92c6a65be2'),('9c32370a-1dcc-11eb-91d6-4e92c6a65be2'),('9c324066-1dcc-11eb-91d6-4e92c6a65be2'),('9c388dab-03e8-11eb-9825-4e92c6a65be2'),('9c38aa36-03e8-11eb-9825-4e92c6a65be2'),('9cbda8df-20ef-11ec-9209-f27b926a5e95'),('9cbe591a-20ef-11ec-9209-f27b926a5e95'),('9f20423f-e3bf-11eb-a61d-4e92c6a65be2'),('9f205a26-e3bf-11eb-a61d-4e92c6a65be2'),('a35f114f-03e8-11eb-9825-4e92c6a65be2'),('a35f5aa6-03e8-11eb-9825-4e92c6a65be2'),('a4dd2db7-20f0-11ec-9209-f27b926a5e95'),('a4ddf55a-20f0-11ec-9209-f27b926a5e95'),('a74184db-20ef-11ec-9209-f27b926a5e95'),('a7424864-20ef-11ec-9209-f27b926a5e95'),('a760298a-ad7a-11eb-84bc-4e92c6a65be2'),('a760437d-ad7a-11eb-84bc-4e92c6a65be2'),('a96046d5-03e8-11eb-9825-4e92c6a65be2'),('a9606d6a-03e8-11eb-9825-4e92c6a65be2'),('aa5b5661-9eb7-11ec-84c0-6a492e0db66d'),('aa5c7c11-9eb7-11ec-84c0-6a492e0db66d'),('ad907eee-20f0-11ec-9209-f27b926a5e95'),('ad913bde-20f0-11ec-9209-f27b926a5e95'),('add475f7-20ef-11ec-9209-f27b926a5e95'),('add52c3d-20ef-11ec-9209-f27b926a5e95'),('af355f09-03e8-11eb-9825-4e92c6a65be2'),('af3572b2-03e8-11eb-9825-4e92c6a65be2'),('b5b8a7cc-20f0-11ec-9209-f27b926a5e95'),('b5b98e4f-20f0-11ec-9209-f27b926a5e95'),('b6d8d37c-20ef-11ec-9209-f27b926a5e95'),('b6d99a65-20ef-11ec-9209-f27b926a5e95'),('bbcfc8ea-03e8-11eb-9825-4e92c6a65be2'),('bbcfddb9-03e8-11eb-9825-4e92c6a65be2'),('bca5bba5-20f0-11ec-9209-f27b926a5e95'),('bca68023-20f0-11ec-9209-f27b926a5e95'),('bf2ada49-20ef-11ec-9209-f27b926a5e95'),('bf2b91af-20ef-11ec-9209-f27b926a5e95'),('c2dbe4a4-20f0-11ec-9209-f27b926a5e95'),('c2dcb727-20f0-11ec-9209-f27b926a5e95'),('c610aa7e-0302-11eb-9825-4e92c6a65be2'),('c610d33f-0302-11eb-9825-4e92c6a65be2'),('c6339efa-20ef-11ec-9209-f27b926a5e95'),('c6344bb4-20ef-11ec-9209-f27b926a5e95'),('c6e49ff9-1d25-11eb-91d6-4e92c6a65be2'),('c6e4bb0b-1d25-11eb-91d6-4e92c6a65be2'),('cb183504-0476-11eb-9825-4e92c6a65be2'),('cb184315-0476-11eb-9825-4e92c6a65be2'),('cbce3cac-20f0-11ec-9209-f27b926a5e95'),('cbcef9ea-20f0-11ec-9209-f27b926a5e95'),('cc879976-20ef-11ec-9209-f27b926a5e95'),('cc885a15-20ef-11ec-9209-f27b926a5e95'),('cf4d4fce-03e8-11eb-9825-4e92c6a65be2'),('cf4d6405-03e8-11eb-9825-4e92c6a65be2'),('d1010115-204c-11ec-9209-f27b926a5e95'),('d1022d9a-204c-11ec-9209-f27b926a5e95'),('d20d3c86-1d25-11eb-91d6-4e92c6a65be2'),('d20d52d7-1d25-11eb-91d6-4e92c6a65be2'),('d4c3e34c-20ef-11ec-9209-f27b926a5e95'),('d4c488e3-20ef-11ec-9209-f27b926a5e95'),('d5d15843-03e8-11eb-9825-4e92c6a65be2'),('d5d16efe-03e8-11eb-9825-4e92c6a65be2'),('da94490d-03e8-11eb-9825-4e92c6a65be2'),('da946787-03e8-11eb-9825-4e92c6a65be2'),('dc2a0db7-20ef-11ec-9209-f27b926a5e95'),('dc2ac23b-20ef-11ec-9209-f27b926a5e95'),('e057a055-03e8-11eb-9825-4e92c6a65be2'),('e057ae6d-03e8-11eb-9825-4e92c6a65be2'),('e0ddbcf9-20f0-11ec-9209-f27b926a5e95'),('e0e110f4-20f0-11ec-9209-f27b926a5e95'),('e17acbfa-930d-11ec-84c0-6a492e0db66d'),('e17b3165-930d-11ec-84c0-6a492e0db66d'),('e8307fa7-03e8-11eb-9825-4e92c6a65be2'),('e8308e98-03e8-11eb-9825-4e92c6a65be2'),('eb7ce399-e3be-11eb-a61d-4e92c6a65be2'),('eb7cf616-e3be-11eb-a61d-4e92c6a65be2'),('ecc85ceb-20ef-11ec-9209-f27b926a5e95'),('ecca137c-20ef-11ec-9209-f27b926a5e95'),('ef6a35c0-03e8-11eb-9825-4e92c6a65be2'),('ef6a504a-03e8-11eb-9825-4e92c6a65be2'),('f110b447-277c-11eb-91d6-4e92c6a65be2'),('f110dba9-277c-11eb-91d6-4e92c6a65be2'),('f110ed6b-277c-11eb-91d6-4e92c6a65be2'),('f431274d-03e8-11eb-9825-4e92c6a65be2'),('f431382c-03e8-11eb-9825-4e92c6a65be2'),('f4337ad0-20ef-11ec-9209-f27b926a5e95'),('f43436bf-20ef-11ec-9209-f27b926a5e95'),('fa007ded-03e8-11eb-9825-4e92c6a65be2'),('fa0088c9-03e8-11eb-9825-4e92c6a65be2'),('fb84712b-2e52-11eb-91d6-4e92c6a65be2'),('fb84bda5-2e52-11eb-91d6-4e92c6a65be2'),('fb84c6a0-2e52-11eb-91d6-4e92c6a65be2');
/*!40000 ALTER TABLE `text_localized` ENABLE KEYS */;

--
-- Table structure for table `text_localized_item`
--

DROP TABLE IF EXISTS `text_localized_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `text_localized_item` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `parent` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `language` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_923CA1F03D8E604F` (`parent`),
  KEY `IDX_923CA1F0D4DB71B5` (`language`),
  CONSTRAINT `FK_923CA1F03D8E604F` FOREIGN KEY (`parent`) REFERENCES `text_localized` (`id`),
  CONSTRAINT `FK_923CA1F0D4DB71B5` FOREIGN KEY (`language`) REFERENCES `language` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `text_localized_item`
--

/*!40000 ALTER TABLE `text_localized_item` DISABLE KEYS */;
INSERT INTO `text_localized_item` (`id`, `parent`, `language`, `text`) VALUES ('78373919-1d25-11eb-91d6-4e92c6a65be2','78372c8d-1d25-11eb-91d6-4e92c6a65be2','en','Demo - Castor FAIR Data Point'),('78374344-1d25-11eb-91d6-4e92c6a65be2','78373ef0-1d25-11eb-91d6-4e92c6a65be2','en','Castor\'s Demo FAIR Data Point');
/*!40000 ALTER TABLE `text_localized_item` ENABLE KEYS */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `castor_user_id` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orcid_user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `person_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649A7F0FBB4` (`castor_user_id`),
  UNIQUE KEY `UNIQ_8D93D649BDC9B428` (`orcid_user_id`),
  UNIQUE KEY `UNIQ_8D93D649217BBB47` (`person_id`),
  CONSTRAINT `FK_8D93D649217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8D93D649A7F0FBB4` FOREIGN KEY (`castor_user_id`) REFERENCES `castor_user` (`id`),
  CONSTRAINT `FK_8D93D649BDC9B428` FOREIGN KEY (`orcid_user_id`) REFERENCES `orcid_user` (`orcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

--
-- Table structure for table `user_api`
--

DROP TABLE IF EXISTS `user_api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_api` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `server` int(11) DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_secret` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4613B9845A6DD5F6` (`server`),
  CONSTRAINT `FK_4613B9845A6DD5F6` FOREIGN KEY (`server`) REFERENCES `castor_server` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_api`
--

/*!40000 ALTER TABLE `user_api` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_api` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-03-17 19:11:23
