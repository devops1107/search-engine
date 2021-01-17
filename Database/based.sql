/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `based_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_attempts` (
  `attempt_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attempt_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_ip` text COLLATE utf8mb4_unicode_ci,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attempt_time` bigint(20) NOT NULL,
  PRIMARY KEY (`attempt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_attempts` WRITE;
/*!40000 ALTER TABLE `based_attempts` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `based_attempts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_content` (
  `content_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_author` int(11) NOT NULL,
  `content_body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `content_size` bigint(20) DEFAULT '0',
  `content_mimetype` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_meta` text COLLATE utf8mb4_unicode_ci,
  `created_at` bigint(20) NOT NULL DEFAULT '0',
  `updated_at` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`),
  UNIQUE KEY `content_slug` (`content_slug`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_content` WRITE;
/*!40000 ALTER TABLE `based_content` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_content` VALUES (1,'Contact Us','contact-us',1,'<p>Well, let\'s just dump it in the sewer and say we delivered it.When I was first asked to make a film about my nephew, Hubert Farnsworth, I thought \"Why should I?\" Then later, Leela made the film. But if I did make it, you can bet there would have been more topless women on motorcycles. Roll film!In our darkest hour, we can stand erect, with proud upthrust bosoms.<br></p>','','page',0,NULL,'{\"description\":\"\",\"image\":\"\"}',1602402922,1603092994),(2,'About Us','about-us',1,'<p><span>Well, let\'s just dump it in the sewer and say we delivered it.When I was first asked to make a film about my nephew, Hubert Farnsworth, I thought \"Why should I?\" Then later, Leela made the film. But if I did make it, you can bet there would have been more topless women on motorcycles. Roll film!In our darkest hour, we can stand erect, with proud upthrust bosoms.</span></p><p>These old Doomsday Devices are dangerously unstable. I\'ll rest easier not knowing where they are. Maybe I love you so much I love you no matter who you are pretending to be. Doomsday device? Ah, now the ball\'s in Farnsworth\'s court!</p><p>Bender, quit destroying the universe! With a warning label this big, you know they gotta be fun! Um, is this the boring, peaceful kind of taking to the streets? That could be \'my\' beautiful soul sitting naked on a couch. If I could just learn to play this stupid thing.</p><p>You won\'t have time for sleeping, soldier, not with all the bed making you\'ll be doing. Why would I want to know that? Okay, I like a challenge. And remember, don\'t do anything that affects anything, unless it turns out you were supposed to, in which case, for the love of God, don\'t not do it!</p><p>Who are those horrible orange men? Bender, this is Fry\'s decision… and he made it wrong. So it\'s time for us to interfere in his life. No! I want to live! There are still too many things I don\'t own!</p><p>And why did \'I\' have to take a cab? A true inspiration for the children. And I\'d do it again! And perhaps a third time! But that would be it. It\'s just like the story of the grasshopper and the octopus. All year long, the grasshopper kept burying acorns for winter, while the octopus mooched off his girlfriend and watched TV. But then the winter came, and the grasshopper died, and the octopus ate all his acorns. Also he got a race car. Is any of this getting through to you?</p><p>And I\'d do it again! And perhaps a third time! But that would be it. I haven\'t felt much of anything since my guinea pig died. Oh Leela! You\'re the only person I could turn to; you\'re the only person who ever loved me.</p><p>Oh right. I forgot about the battle. Please, Don-Bot… look into your hard drive, and open your mercy file! Quite possible. We live long and are celebrated poopers. I don\'t know what you did, Fry, but once again, you screwed up! Now all the planets are gonna start cracking wise about our mamas.</p><p>You know, I was God once. You seem malnourished. Are you suffering from intestinal parasites? Hey! I\'m a porno-dealing monster, what do I care what you think? In our darkest hour, we can stand erect, with proud upthrust bosoms.</p><p>You mean while I\'m sleeping in it? Eeeee! Now say \"nuclear wessels\"! We don\'t have a brig. Bender, this is Fry\'s decision… and he made it wrong. So it\'s time for us to interfere in his life. Oh, I always feared he might run off like this. Why, why, why didn\'t I break his legs?</p><p>Would you censor the Venus de Venus just because you can see her spewers? That\'s the ONLY thing about being a slave. Noooooo! We\'re also Santa Claus! There\'s no part of that sentence I didn\'t like!</p><p>Yeah. Give a little credit to our public schools. Calculon is gonna kill us and it\'s all everybody else\'s fault! Bender, being God isn\'t easy. If you do too much, people get dependent on you, and if you do nothing, they lose hope. You have to use a light touch. Like a safecracker, or a pickpocket.</p>','','page',0,NULL,'{\"description\":\"\",\"image\":\"\"}',1602402933,1603092819),(3,'Privacy Policy','privacy-policy',1,'<p><span>At Based, accessible from based.domain, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Based and how we use it.</span></p>\r\n\r\n<p>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.</p>\r\n\r\n<p>This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and/or collect in Based. This policy is not applicable to any information collected offline or via channels other than this website.&nbsp;</p>\r\n\r\n<h4>Consent</h4>\r\n\r\n<p>By using our website, you hereby consent to our Privacy Policy and agree to its terms.</p>\r\n\r\n<h4>Information we collect</h4>\r\n\r\n<p>The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information.</p>\r\n<p>If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and/or attachments you may send us, and any other information you may choose to provide.</p>\r\n<p>When you register for an Account, we may ask for your contact information, including items such as name, company name, address, email address, and telephone number.</p>\r\n\r\n<h4>How we use your information</h4>\r\n\r\n<p>We use the information we collect in various ways, including to:</p><p>\r\n\r\n\r\nProvide, operate, and maintain our webste\r\nImprove, personalize, and expand our webste\r\nUnderstand and analyze how you use our webste\r\nDevelop new products, services, features, and functionality\r\nCommunicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the webste, and for marketing and promotional purposes\r\nSend you emails\r\nFind and prevent fraud\r\n\r\n\r\n</p><h4>Log Files</h4>\r\n\r\n<p>Based follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services\' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users\' movement on the website, and gathering demographic information.</p>\r\n\r\n<h4>Cookies and Web Beacons</h4>\r\n\r\n<p>Like any other website, Based uses \'cookies\'. These cookies are used to store information including visitors\' preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users\' experience by customizing our web page content based on visitors\' browser type and/or other information.</p>\r\n\r\n\r\n\r\n<h4>Advertising Partners Privacy Policies</h4>\r\n\r\n<p>You may consult this list to find the Privacy Policy for each of the advertising partners of Based.</p>\r\n\r\n<p>Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on Based, which are sent directly to users\' browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and/or to personalize the advertising content that you see on websites that you visit.</p>\r\n\r\n<p>Note that Based has no access to or control over these cookies that are used by third-party advertisers.</p>\r\n\r\n<h4>Third Party Privacy Policies</h4>\r\n\r\n<p>Based\'s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options. </p>\r\n\r\n<p>You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers\' respective websites.</p>\r\n\r\n<h4>CCPA Privacy Rights (Do Not Sell My Personal Information)</h4>\r\n\r\n<p>Under the CCPA, among other rights, California consumers have the right to:</p>\r\n<p>Request that a business that collects a consumer\'s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers.</p>\r\n<p>Request that a business delete any personal data about the consumer that a business has collected.</p>\r\n<p>Request that a business that sells a consumer\'s personal data, not sell the consumer\'s personal data.</p>\r\n<p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p>\r\n\r\n<h4>GDPR Data Protection Rights</h4>\r\n\r\n<p>We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:</p>\r\n<p>The right to access – You have the right to request copies of your personal data. We may charge you a small fee for this service.</p>\r\n<p>The right to rectification – You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.</p>\r\n<p>The right to erasure – You have the right to request that we erase your personal data, under certain conditions.</p>\r\n<p>The right to restrict processing – You have the right to request that we restrict the processing of your personal data, under certain conditions.</p>\r\n<p>The right to object to processing – You have the right to object to our processing of your personal data, under certain conditions.</p>\r\n<p>The right to data portability – You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</p>\r\n<p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p>\r\n\r\n<h4>Children\'s Information</h4>\r\n\r\n<p>Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate in, and/or monitor and guide their online activity.</p>\r\n\r\n<p>Based does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information from our records.</p>','','page',0,NULL,'{\"description\":\"\",\"image\":\"\"}',1602522226,1603268557),(4,'Terms of Service','terms-of-service',1,'<h4><span>1. Terms</span></h4>\r\n\r\n<p>By accessing this Website, accessible from based.domain, you are agreeing to be bound by these Website Terms and Conditions of Use and agree that you are responsible for the agreement with any applicable local laws. If you disagree with any of these terms, you are prohibited from accessing this site. The materials contained in this Website are protected by copyright and trade mark law.</p>\r\n\r\n<h4>2. Use License</h4>\r\n\r\n<p>Permission is granted to temporarily download one copy of the materials on MirazMacStudios\'s Website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p><p>\r\n\r\n\r\n    modify or copy the materials;\r\n    use the materials for any commercial purpose or for any public display;\r\n    attempt to reverse engineer any software contained on MirazMacStudios\'s Website;\r\n    remove any copyright or other proprietary notations from the materials; or\r\n    transferring the materials to another person or \"mirror\" the materials on any other server.\r\n\r\n\r\n</p><p>This will let MirazMacStudios to terminate upon violations of any of these restrictions. Upon termination, your viewing right will also be terminated and you should destroy any downloaded materials in your possession whether it is printed or electronic format.</p>\r\n\r\n<h4>3. Disclaimer</h4>\r\n\r\n<p>All the materials on MirazMacStudios’s Website are provided \"as is\". MirazMacStudios makes no warranties, may it be expressed or implied, therefore negates all other warranties. Furthermore, MirazMacStudios does not make any representations concerning the accuracy or reliability of the use of the materials on its Website or otherwise relating to such materials or any sites linked to this Website.</p>\r\n\r\n<h4>4. Limitations</h4>\r\n\r\n<p>MirazMacStudios or its suppliers will not be hold accountable for any damages that will arise with the use or inability to use the materials on MirazMacStudios’s Website, even if MirazMacStudios or an authorize representative of this Website has been notified, orally or written, of the possibility of such damage. Some jurisdiction does not allow limitations on implied warranties or limitations of liability for incidental damages, these limitations may not apply to you.</p>\r\n\r\n<h4>5. Revisions and Errata</h4>\r\n\r\n<p>The materials appearing on MirazMacStudios’s Website may include technical, typographical, or photographic errors. MirazMacStudios will not promise that any of the materials in this Website are accurate, complete, or current. MirazMacStudios may change the materials contained on its Website at any time without notice. MirazMacStudios does not make any commitment to update the materials.</p>\r\n\r\n<h4>6. Links</h4>\r\n\r\n<p>MirazMacStudios has not reviewed all of the sites linked to its Website and is not responsible for the contents of any such linked site. The presence of any link does not imply endorsement by MirazMacStudios of the site. The use of any linked website is at the user’s own risk.</p>\r\n\r\n<h4>7. Site Terms of Use Modifications</h4>\r\n\r\n<p>MirazMacStudios may revise these Terms of Use for its Website at any time without prior notice. By using this Website, you are agreeing to be bound by the current version of these Terms and Conditions of Use.</p>\r\n\r\n<h4>8. Your Privacy</h4>\r\n\r\n<p>Please read our Privacy Policy.</p>\r\n\r\n<h4>9. Governing Law</h4>\r\n\r\n<p>Any claim related to MirazMacStudios\'s Website shall be governed by the laws of bd without regards to its conflict of law provisions.</p>','','page',0,NULL,'{\"description\":\"\",\"image\":\"\"}',1602522292,1603093644);
/*!40000 ALTER TABLE `based_content` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_engines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_engines` (
  `engine_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `engine_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Engine name',
  `engine_cse_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Google CSE ID for this engine.',
  `engine_is_image` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Choose if the result type is image or not',
  `engine_show_thumb` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Choose if thumbnails will be when if available (web result only)',
  `engine_show_ads` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Choose whether to show CSE ads for this engine.',
  `engine_log_search` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Choose to log the search history for this engine, overrides global setting.',
  `engine_order` int(5) NOT NULL DEFAULT '0' COMMENT '@skip',
  `created_at` bigint(20) NOT NULL DEFAULT '0',
  `updated_at` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`engine_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_engines` WRITE;
/*!40000 ALTER TABLE `based_engines` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_engines` VALUES (1,'Web','partner-pub-9134522736300956:4140494421',0,0,0,0,1,1601037982,1602951182),(2,'Images','017868052422402550355:ys3fjkmxqfe',1,0,0,0,2,1601038370,1602908385),(3,'Videos','017868052422402550355:dtkzl6yzv74',0,1,0,0,3,1601117187,1602908385),(4,'News','partner-pub-9134522736300956:2425971629',0,1,0,0,4,1601900374,1602908385),(5,'Torrents','017868052422402550355:5xcraecopso',0,0,0,0,5,1601900398,1602908385),(6,'Subtitles','017868052422402550355:1tcfk8s8xi4',0,0,1,0,6,1601900422,1602908385);
/*!40000 ALTER TABLE `based_engines` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_menus` (
  `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The menu name',
  `created_at` bigint(20) NOT NULL,
  `updated_at` bigint(20) NOT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_menus` WRITE;
/*!40000 ALTER TABLE `based_menus` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_menus` VALUES (2,'Footer Menu',1573115932,1603093676),(3,'OffCanvas Menu',1574259698,1602522326);
/*!40000 ALTER TABLE `based_menus` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_menus_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_menus_rel` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_label` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_url` text COLLATE utf8mb4_unicode_ci,
  `item_class` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_icon` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_menus_rel` WRITE;
/*!40000 ALTER TABLE `based_menus_rel` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_menus_rel` VALUES (56,'Contact Us','/pages/contact-us','','',3,0,2),(26,'Contact','/pages/contact-us','','',2,0,2),(27,'About','/pages/about-us','','',2,0,1),(28,'Terms','/pages/terms-of-service','','',2,0,3),(29,'Business','/page/terms','','heart',4,0,3),(47,'India','/pages/contact-us','','analytics',4,0,2),(31,'Home','/','',NULL,1,0,0),(32,'About Us','/page/about-us','',NULL,1,0,0),(33,'Contact Us','/page/contact-us','',NULL,1,0,0),(34,'Terms','/page/terms','',NULL,1,0,0),(35,'World','#','','share',4,0,4),(36,'Politics','#','','image',4,0,5),(37,'Sports','#','','flag',4,0,6),(41,'Lifestyle','#','','mail',4,0,7),(42,'More','#','','chatbubbles',4,0,9),(43,'Insparational','#','',NULL,4,42,11),(44,'Religious','#','','settings',4,0,8),(45,'Piracy','#','',NULL,4,42,12),(46,'Services','/','','',4,42,10),(52,'Home','/pages/contact-us','','compass',4,0,1),(55,'About Us','/pages/about-us','','',3,0,3),(57,'Privacy Policy','/pages/privacy-policy','','',3,0,1),(58,'Terms of Service','/pages/terms-of-service','','',3,0,4),(59,'Privacy','/pages/privacy-policy','','',2,0,4);
/*!40000 ALTER TABLE `based_menus_rel` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_meta` (
  `meta_id` int(10) NOT NULL AUTO_INCREMENT,
  `meta_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_key` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_target_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `meta_key` (`meta_key`),
  KEY `meta_target_id` (`meta_target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_meta` WRITE;
/*!40000 ALTER TABLE `based_meta` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `based_meta` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_options` (
  `option_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_value` text COLLATE utf8mb4_unicode_ci,
  `option_autoload` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `options_name_unique` (`option_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_options` WRITE;
/*!40000 ALTER TABLE `based_options` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_options` VALUES ('default_engine','1',1),('serp_domain_color','#009e45',1),('active_theme','default',1),('ad_unit_1','',0),('ad_unit_2','',0),('ad_unit_3','',0),('captcha_enabled','0',1),('footer_scripts','',1),('google_recaptcha_secret_key','',0),('google_recaptcha_site_key','',1),('header_scripts','',1),('site_description','Based is a Google powered search engine that runs on Google Programmable Search, without the need of any kind of API keys or limits.',1),('search_items_count','12',0),('site_email','site@admin.com',1),('site_locale','en_US',1),('site_logo','/site/themes/default/assets/img/logo.png',1),('site_name','Based',1),('site_tagline','The Ultimate PHP Search Engine Based on Google CSE',1),('smtp_auth_enabled','1',0),('smtp_enabled','',0),('smtp_host','smtp.gmail.com',0),('smtp_password','',0),('smtp_port','587',0),('smtp_username','user@gmail.com',0),('timezone','Asia/Dhaka',1),('sitemap_links_per_page','1000',0),('facebook_app_id','',0),('spark_cron_job_token','22c0221b228f143c2841',0),('site_language','en_US',1),('smtp_secure','tls',0),('vk_username','mirazmac',0),('__spark__menu_id__header-nav','4',0),('__spark__menu_id__footer-nav','2',0),('site_favicon','/favicon.ico',1),('captcha_locations','{\"auth.signin\":1}',0),('search_logo','/site/themes/default/assets/img/search-logo.png',1),('opengraph_image','/site/assets/img/og-image.png',1),('dark_logo','/site/themes/default/assets/img/logo-for-dark.png',1),('image_search_items_count','20',0),('search_links_newwindow','1',1),('safesearch_status','off',1),('theme_home_max_engines_count','3',1),('search_autocomplete','1',1),('search_logo_width','66',0),('serp_link_color','#004ecc',1),('enable_backgrounds','1',1),('__spark__menu_id__offcanvas-nav','3',0),('show_engines_in_offcanvas','0',1),('home_logo_align','left',1),('search_log','1',1),('serp_text_color','#878787',0),('show_entities','1',1),('show_answers','1',1),('search_logo_dark','/site/themes/default/assets/img/search-logo-white.png',1),('enable_darkmode','0',1),('home_logo_width','100',0),('rss_items_per_page','0',0),('enable_ajax_nav','1',1);
/*!40000 ALTER TABLE `based_options` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_permissions` (
  `perm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `perm_desc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`perm_id`),
  UNIQUE KEY `perm_desc` (`perm_desc`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_permissions` WRITE;
/*!40000 ALTER TABLE `based_permissions` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_permissions` VALUES (1,'access_dashboard'),(2,'add_user'),(3,'edit_user'),(4,'delete_user'),(5,'add_role'),(6,'edit_role'),(7,'delete_role'),(8,'change_settings'),(10,'change_user_role'),(11,'manage_gallery'),(12,'manage_pages'),(13,'manage_themes'),(14,'access_gallery'),(15,'change_user_status'),(23,'manage_engines'),(17,'bypass_captcha'),(18,'manage_menus'),(22,'manage_queries');
/*!40000 ALTER TABLE `based_permissions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_queries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_queries` (
  `query_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `query_term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `query_count` int(10) NOT NULL DEFAULT '1',
  `created_at` bigint(20) NOT NULL,
  `updated_at` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`query_id`),
  UNIQUE KEY `query_term` (`query_term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_queries` WRITE;
/*!40000 ALTER TABLE `based_queries` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `based_queries` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_role_perm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_role_perm` (
  `role_id` int(10) unsigned NOT NULL,
  `perm_id` int(10) unsigned NOT NULL,
  KEY `role_id` (`role_id`),
  KEY `perm_id` (`perm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_role_perm` WRITE;
/*!40000 ALTER TABLE `based_role_perm` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_role_perm` VALUES (1,22),(1,18),(1,23),(1,15),(1,14),(1,13),(1,12),(1,11),(1,10),(1,8),(1,7),(1,6),(1,5),(1,4),(1,3),(2,19),(2,15),(2,12),(2,11),(1,2),(2,3),(1,1),(2,2),(2,1);
/*!40000 ALTER TABLE `based_role_perm` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_protected` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` bigint(20) DEFAULT '0',
  `updated_at` bigint(20) DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_roles` WRITE;
/*!40000 ALTER TABLE `based_roles` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_roles` VALUES (1,'Administrator',1,1543473719,1603281646),(2,'Moderator',1,1543473766,1575622821),(3,'User',1,1543473780,1588774913);
/*!40000 ALTER TABLE `based_roles` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_tokens` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varbinary(128) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `token_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_expires` bigint(20) NOT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_tokens` WRITE;
/*!40000 ALTER TABLE `based_tokens` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `based_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

DROP TABLE IF EXISTS `based_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `based_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'user ID',
  `email` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'user e-mail',
  `full_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` int(1) NOT NULL DEFAULT '0',
  `password` char(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'password hash',
  `username` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'unique username',
  `avatar` text COLLATE utf8mb4_unicode_ci COMMENT 'user''s avatar url',
  `cover` text COLLATE utf8mb4_unicode_ci,
  `user_ip` text COLLATE utf8mb4_unicode_ci COMMENT 'user''s creation IP',
  `role_id` int(10) unsigned NOT NULL DEFAULT '3' COMMENT 'role ID of user',
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s block status',
  `has_password` int(1) NOT NULL DEFAULT '1' COMMENT 'If 1 user must need to provide the current password to set a new one, otherwise he can set a new one',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'whether the user is verifed or not',
  `last_seen` bigint(20) DEFAULT '0' COMMENT 'last activity timestamp',
  `created_at` bigint(20) NOT NULL DEFAULT '0' COMMENT 'when the row was created',
  `updated_at` bigint(20) DEFAULT '0' COMMENT 'when the row was last updated',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `based_users` WRITE;
/*!40000 ALTER TABLE `based_users` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `based_users` VALUES (1,'demo@mirazmac.com','Administrator',1,'$2y$10$8Na1OWLc45LaMwpC454xyOvU7p8ZP1.LIXbn7eNoVc0F6AOKwL7h6','mirazmac',NULL,NULL,'127.0.0.1',1,0,1,1,1603369541,1589461665,1603369541);
/*!40000 ALTER TABLE `based_users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

