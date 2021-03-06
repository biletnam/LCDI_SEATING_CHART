DROP TABLE IF EXISTS discussions_reports;
DROP TABLE IF EXISTS discussions_comments;
DROP TABLE IF EXISTS discussions;
DROP TABLE IF EXISTS user_reports;
DROP TABLE IF EXISTS tasks_comments;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS tickets_reports;
DROP TABLE IF EXISTS tickets_comments;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS tasks_groups;
DROP TABLE IF EXISTS versions;
DROP TABLE IF EXISTS projects_phases;
DROP TABLE IF EXISTS projects_reports;
DROP TABLE IF EXISTS projects_comments;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS attachments;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS versions_status;
DROP TABLE IF EXISTS users_groups;
DROP TABLE IF EXISTS tickets_types;
DROP TABLE IF EXISTS tickets_status;
DROP TABLE IF EXISTS tasks_types;
DROP TABLE IF EXISTS tasks_status;
DROP TABLE IF EXISTS tasks_priority;
DROP TABLE IF EXISTS tasks_labels;
DROP TABLE IF EXISTS projects_types;
DROP TABLE IF EXISTS projects_status;
DROP TABLE IF EXISTS phases;
DROP TABLE IF EXISTS phases_status;
DROP TABLE IF EXISTS extra_fields_list;
DROP TABLE IF EXISTS extra_fields;
DROP TABLE IF EXISTS discussions_status;
DROP TABLE IF EXISTS configuration;


CREATE TABLE IF NOT EXISTS `configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO configuration VALUES
('1','app_administrator_email','admin@localhost.com'),
('2','app_administrator_password','P@ssw0rd'),
('3','app_app_name','LCDI Workplace'),
('4','app_app_short_name','LCDI'),
('5','app_email_label','LCDI -'),
('6','app_default_skin','RedBar'),
('7','sf_default_timezone','America/New_York'),
('8','sf_default_culture','en'),
('9','app_rows_per_page','50'),
('10','app_custom_short_date_format','M d, Y'),
('11','app_custom_logn_date_format','M d, Y H:i'),
('12','app_allow_adit_tasks_comments_date','off'),
('13','app_show_menu_icons','off'),
('14','app_show_footer_links','off'),
('15','app_tasks_fields_tasks_version','off'),
('16','app_tasks_fields_tasks_phase','on'),
('17','app_tasks_fields_tasks_group','off'),
('18','app_tasks_fields_priority','on'),
('19','app_tasks_fields_label','on'),
('20','app_tasks_fields_id','off'),
('21','app_tasks_fields_name','on'),
('22','app_tasks_fields_status','on'),
('23','app_tasks_fields_assigned_to','on'),
('24','app_tasks_fields_created_by','off'),
('25','app_tasks_fields_estimated_time','on'),
('26','app_tasks_fields_start_date','off'),
('27','app_tasks_fields_due_date','on'),
('28','app_tasks_fields_progress','off'),
('29','app_tasks_fields_created_at','off'),
('30','app_use_skins','on'),
('31','app_use_related_tasks','on'),
('32','app_use_public_tickets','on'),
('33','app_public_tickets_show_login_link','off'),
('34','app_public_tickets_allow_attachments','on'),
('35','app_use_project_phases','on'),
('36','app_use_project_versions','on'),
('37','app_use_project_discussions','on'),
('38','app_use_tasks_groups','off'),
('39','app_use_tasks_timetracker','on'),
('40','app_use_fck_editor','on'),
('41','app_notify_all_project_team','off'),
('42','app_notify_all_customers','off'),
('43','app_use_single_email','off'),
('44','app_single_email_addres_from',NULL),
('45','app_single_name_from',NULL),
('46','app_use_smtp','off'),
('47','app_smtp_server',NULL),
('48','app_smtp_port','25'),
('49','app_smtp_encryption',NULL),
('50','app_smtp_login',NULL),
('51','app_smtp_pass',NULL),
('52','app_use_ldap_login','off'),
('53','app_ldap_host',NULL),
('54','app_ldap_port',NULL),
('55','app_ldap_base_dn',NULL),
('56','app_ldap_version','3'),
('57','app_use_email_notification','on'),
('58','app_show_user_email','off'),
('59','app_show_user_photo','on'),
('60','app_tasks_fields_type','off'),
('61','app_login_page_heading','Welcome to the LCDI'),
('62','app_login_page_content',NULL),
('63','app_new_user_email_subject',NULL),
('64','app_new_user_email_body',NULL),
('65','app_amount_previous_comments','2'),
('66','app_rows_limit','250'),
('67','app_tasks_columns_list','TasksGroups,Versions,ProjectsPhases,TasksPriority,Name,TasksStatus,TasksTypes,AssignedTo,EstimatedTime,WorkHours,TogoHours,ActualTime,DueDate'),
('68','app_send_email_to_owner','off'),
('69','app_public_tickets_use_antispam','on'),
('70','app_app_logo','tt_logo3.png'),
('71','app_use_javascript_dropdown','on');

CREATE TABLE IF NOT EXISTS `discussions_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `default_value` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO discussions_status VALUES
('1','Open','0','1','1'),
('2','Closed','1',NULL,'1');

CREATE TABLE IF NOT EXISTS `extra_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `bind_type` varchar(64) NOT NULL DEFAULT '',
  `type` varchar(64) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `active` tinyint(1) DEFAULT NULL,
  `display_in_list` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO extra_fields VALUES
('9','Phone','users','text','0','1','1'),
('13','Budget       $','projects','number','0','1','1'),
('14','Name','tickets','text','0','1','1');

CREATE TABLE IF NOT EXISTS `extra_fields_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extra_fields_id` int(11) NOT NULL DEFAULT '0',
  `bind_id` int(11) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_extra_fields_list_extra_fields` (`extra_fields_id`),
  CONSTRAINT `extra_fields_list_ibfk_1` FOREIGN KEY (`extra_fields_id`) REFERENCES `extra_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO extra_fields_list VALUES
('95','9','3','802-323-4409'),
('96','9','4',''),
('97','9','5',''),
('98','9','6',''),
('99','13','1','1500'),
('106','9','7',''),
('108','9','9',''),
('109','13','2','5000'),
('116','14','3','IBM'),
('117','14','4','MyWebGrocer');

CREATE TABLE IF NOT EXISTS `phases_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT '0',
  `default_value` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO phases_status VALUES
('1','Open','0','1','1'),
('2','Completed','1',NULL,'1'),
('3','On Hold','2',NULL,'1'),
('4','Cancelled','3',NULL,'1');

CREATE TABLE IF NOT EXISTS `phases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `default_values` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO phases VALUES
('1','New Site','Quotes\r\nGraphic Design\r\nDevelopment\r\nSite Test\r\nUser Test\r\nGo Live\r\nWarranty'),
('2','Support','Quotes\r\nDefects\r\nChanges\r\n');

CREATE TABLE IF NOT EXISTS `projects_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT '0',
  `default_value` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO projects_status VALUES
('1','Open','0','1','1'),
('2','On Hold','1',NULL,'1'),
('3','Closed','2',NULL,'1'),
('4','Cancelled','3',NULL,'1');

CREATE TABLE IF NOT EXISTS `projects_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT '0',
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO projects_types VALUES
('5','Acquisition','0','1'),
('6','Analysis','0','1'),
('7','Other','0','1');

CREATE TABLE IF NOT EXISTS `tasks_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT '0',
  `default_value` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO tasks_labels VALUES
('1','Task','0','1','1'),
('2','Bug','1',NULL,'1'),
('3','Idea','2',NULL,'1'),
('4','Issue','4',NULL,'1'),
('5','Quote','3',NULL,'1'),
('6','Change','0',NULL,'1'),
('7','PlugIn','0',NULL,'1');

CREATE TABLE IF NOT EXISTS `tasks_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(64) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `default_value` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO tasks_priority VALUES
('1','Urgent','prio_1.png','5',NULL,'1'),
('2','High','prio_2.png','4',NULL,'1'),
('3','Low','prio_4.png','1',NULL,'1'),
('4','Unknown',NULL,'0',NULL,'1'),
('5','Medum','prio_3.png','2','1','1');

CREATE TABLE IF NOT EXISTS `tasks_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `group` varchar(64) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT '0',
  `default_value` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO tasks_status VALUES
('1','Open','open','0','1','1'),
('2','Suspended','closed','6',NULL,'1'),
('3','Waiting Assessment','open','0',NULL,'1'),
('4','Re-opened','open','2',NULL,'1'),
('5','Done?','done','1',NULL,'1'),
('6','Paid','closed','5',NULL,'1'),
('7','Completed','closed','4',NULL,'1'),
('8','Lost','closed','7',NULL,'1');

CREATE TABLE IF NOT EXISTS `tasks_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT '0',
  `default_value` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO tasks_types VALUES
('1','Change Priority Rate (Hourly rate $25.00)','0','1','1'),
('2','Changes (Hourly rate $15.00)','0',NULL,'1'),
('3','Defects (Hourly rate $0.00)','0',NULL,'1');

CREATE TABLE IF NOT EXISTS `tickets_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `group` varchar(64) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `default_value` int(11) DEFAULT NULL,
  `active` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO tickets_status VALUES
('1','New','open','0','1','1');

CREATE TABLE IF NOT EXISTS `tickets_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT NULL,
  `active` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO tickets_types VALUES
('1','Report a Bug','1','1'),
('2','Request a Change','0','1'),
('3','Raise an Issue','3','1'),
('4','Ask a Question','2','1');

CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `allow_view_all` tinyint(1) DEFAULT NULL,
  `allow_manage_projects` tinyint(1) DEFAULT NULL,
  `allow_manage_tasks` tinyint(1) DEFAULT NULL,
  `allow_manage_tickets` tinyint(1) DEFAULT NULL,
  `allow_manage_users` tinyint(1) DEFAULT NULL,
  `allow_manage_configuration` tinyint(1) DEFAULT NULL,
  `allow_manage_tasks_viewonly` tinyint(1) DEFAULT NULL,
  `allow_manage_discussions` tinyint(1) DEFAULT NULL,
  `allow_manage_discussions_viewonly` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO users_groups VALUES
('1','Admin','1','1','1','1','1','1',NULL,'1',NULL),
('6','Analyst',NULL,'3','3','2',NULL,NULL,NULL,NULL,NULL);

CREATE TABLE IF NOT EXISTS `versions_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT '0',
  `default_value` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO versions_status VALUES
('1','Open','0','1','1'),
('2','Done','0',NULL,'1');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_group_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `photo` varchar(64) DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `culture` varchar(5) DEFAULT NULL,
  `password` varchar(64) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT NULL,
  `skin` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pople_people_group` (`users_group_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`users_group_id`) REFERENCES `users_groups` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO users VALUES
('3','1','Alexander Acebo','918280-ALEX_ACEBO.JPG','alexander.acebo@mymail.champlain.edu','en','b5dee7f921b49f53508fea2352be9f5c','1','RedBar'),
('4','1','Joe Williams',NULL,'jwilliams@champlain.edu','en','5f4dcc3b5aa765d61d8327deb882cf99','1',NULL),
('5','1','Daniel Puckowski',NULL,'daniel.puckowski@mymail.champlain.edu','en','5f4dcc3b5aa765d61d8327deb882cf99','1',NULL),
('6','1','Alex Caron',NULL,'acaron@champlain.edu','en','f9414dd7c834ec8c89322d70ec76d852','1',NULL),
('7','1','Brianne DeVincent',NULL,'brianne.devincent@mymail.champlain.edu','en','5f4dcc3b5aa765d61d8327deb882cf99','1',NULL),
('9','6','forensics',NULL,'forensics@champlain.edu','en','5f4dcc3b5aa765d61d8327deb882cf99','1',NULL);

CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `sort_order` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `users_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_departments_users` (`users_id`),
  CONSTRAINT `fk_departments_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO departments VALUES
('2','Company',NULL,'1','6');

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` text NOT NULL,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `details` text NOT NULL,
  `users_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`),
  KEY `fk_events_users` (`users_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bind_type` varchar(64) NOT NULL DEFAULT '',
  `bind_id` int(11) NOT NULL DEFAULT '0',
  `file` varchar(255) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO attachments VALUES
('1','projects','1','771134-40.0.2214.115.manifest',NULL);

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_status_id` int(11) DEFAULT NULL,
  `projects_types_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `team` text,
  `created_at` datetime DEFAULT NULL,
  `order_tasks_by` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_projects_projects_status` (`projects_status_id`),
  KEY `fk_projects_project_types` (`projects_types_id`),
  KEY `fk_projects_pople` (`created_by`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`projects_status_id`) REFERENCES `projects_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`projects_types_id`) REFERENCES `projects_types` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO projects VALUES
('1','1','5','6','JonTest','we need you to acquire one computer from the client. good luck','6','2015-02-25 15:19:25',NULL),
('2','1','6','3','testCase','test description','9','2015-04-01 14:09:14',NULL);

CREATE TABLE IF NOT EXISTS `projects_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_id` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_projects_comments_projects` (`projects_id`),
  KEY `fk_projects_comments_pople` (`created_by`),
  CONSTRAINT `projects_comments_ibfk_1` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projects_comments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `projects_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `display_on_home` tinyint(1) DEFAULT NULL,
  `projects_id` text,
  `projects_type_id` text,
  `projects_status_id` text,
  `in_team` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `display_in_menu` tinyint(1) DEFAULT NULL,
  `visible_on_home` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `projects_reports_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `projects_phases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_id` int(11) NOT NULL DEFAULT '0',
  `phases_status_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_projects_phases_projects` (`projects_id`),
  KEY `fk_projects_phases_phases_status` (`phases_status_id`),
  CONSTRAINT `projects_phases_ibfk_1` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projects_phases_ibfk_2` FOREIGN KEY (`phases_status_id`) REFERENCES `phases_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_id` int(11) NOT NULL DEFAULT '0',
  `versions_status_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_versions_versions_status` (`versions_status_id`),
  KEY `fk_versions_projects` (`projects_id`),
  CONSTRAINT `versions_ibfk_1` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `versions_ibfk_2` FOREIGN KEY (`versions_status_id`) REFERENCES `versions_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tasks_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_tasks_groups_projects` (`projects_id`),
  CONSTRAINT `tasks_groups_ibfk_1` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departments_id` int(11) DEFAULT NULL,
  `tickets_types_id` int(11) DEFAULT NULL,
  `tickets_status_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `users_id` int(11) NOT NULL DEFAULT '0',
  `projects_id` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tickets_users` (`users_id`),
  KEY `fk_tickets_tickets_status` (`tickets_status_id`),
  KEY `fk_tickets_tickets_types` (`tickets_types_id`),
  KEY `fk_tickets_projects` (`projects_id`),
  KEY `fk_tickets_departments` (`departments_id`),
  CONSTRAINT `fk_tickets_tickets_status` FOREIGN KEY (`tickets_status_id`) REFERENCES `tickets_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `fk_tickets_tickets_types` FOREIGN KEY (`tickets_types_id`) REFERENCES `tickets_types` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `fk_tickets_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`departments_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO tickets VALUES
('3','2','2','1','IBM','this is IBM','3','2','2015-04-01 14:11:59'),
('4','2','2','1','MyWebGrocer','test description','3','2','2015-04-01 14:13:46');

CREATE TABLE IF NOT EXISTS `tickets_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `tickets_id` int(11) NOT NULL DEFAULT '0',
  `users_id` int(11) DEFAULT NULL,
  `tickets_status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tickets_comments_tickets` (`tickets_id`),
  KEY `fk_tickets_comments_users` (`users_id`),
  KEY `k_tickets_comments_status` (`tickets_status_id`),
  CONSTRAINT `fk_tickets_comments_tickets` FOREIGN KEY (`tickets_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tickets_comments_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tickets_comments_ibfk_1` FOREIGN KEY (`tickets_status_id`) REFERENCES `tickets_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tickets_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `display_on_home` tinyint(1) DEFAULT NULL,
  `projects_id` text,
  `projects_type_id` text,
  `projects_status_id` text,
  `departments_id` text,
  `tickets_types_id` text,
  `tickets_status_id` text,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `tickets_reports_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_id` int(11) NOT NULL DEFAULT '0',
  `tasks_status_id` int(11) DEFAULT NULL,
  `tasks_priority_id` int(11) DEFAULT NULL,
  `tasks_type_id` int(11) DEFAULT NULL,
  `tasks_label_id` int(11) DEFAULT NULL,
  `tasks_groups_id` int(11) DEFAULT NULL,
  `projects_phases_id` int(11) DEFAULT NULL,
  `versions_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `assigned_to` varchar(255) DEFAULT NULL,
  `estimated_time` float DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `tickets_id` int(11) DEFAULT NULL,
  `closed_date` date DEFAULT NULL,
  `discussion_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `progress` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tasks_projects` (`projects_id`),
  KEY `fk_tasks_task_status` (`tasks_status_id`),
  KEY `fk_tasks_task_type` (`tasks_type_id`),
  KEY `fk_tasks_task_label` (`tasks_label_id`),
  KEY `fk_tasks_projects_phases` (`projects_phases_id`),
  KEY `fk_tasks_pople` (`created_by`),
  KEY `fk_tasks_tasks_groups` (`tasks_groups_id`),
  KEY `fk_tasks_versions` (`versions_id`),
  KEY `fk_tasks_tasks_priority` (`tasks_priority_id`),
  KEY `fk_tasks_tickets` (`tickets_id`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_ibfk_10` FOREIGN KEY (`tickets_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`tasks_status_id`) REFERENCES `tasks_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`tasks_priority_id`) REFERENCES `tasks_priority` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tasks_ibfk_4` FOREIGN KEY (`tasks_type_id`) REFERENCES `tasks_types` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tasks_ibfk_5` FOREIGN KEY (`tasks_label_id`) REFERENCES `tasks_labels` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tasks_ibfk_6` FOREIGN KEY (`tasks_groups_id`) REFERENCES `tasks_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_ibfk_7` FOREIGN KEY (`projects_phases_id`) REFERENCES `projects_phases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_ibfk_8` FOREIGN KEY (`versions_id`) REFERENCES `versions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_ibfk_9` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tasks_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tasks_id` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `tasks_status_id` int(11) DEFAULT NULL,
  `tasks_priority_id` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `worked_hours` float DEFAULT NULL,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `progress` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tasks_comments_pople` (`created_by`),
  KEY `fk_tasks_comments_tasks` (`tasks_id`),
  KEY `fk_tasks_comments_status` (`tasks_status_id`),
  KEY `fk_tasks_comments_priority` (`tasks_priority_id`),
  CONSTRAINT `tasks_comments_ibfk_1` FOREIGN KEY (`tasks_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_comments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tasks_comments_ibfk_3` FOREIGN KEY (`tasks_status_id`) REFERENCES `tasks_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `tasks_comments_ibfk_4` FOREIGN KEY (`tasks_priority_id`) REFERENCES `tasks_priority` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `display_on_home` tinyint(1) DEFAULT NULL,
  `projects_id` text,
  `projects_type_id` text,
  `projects_status_id` text,
  `assigned_to` text,
  `tasks_status_id` text,
  `tasks_type_id` text,
  `tasks_label_id` text,
  `due_date_from` date DEFAULT NULL,
  `due_date_to` date DEFAULT NULL,
  `created_from` date DEFAULT NULL,
  `created_to` date DEFAULT NULL,
  `closed_from` date DEFAULT NULL,
  `closed_to` date DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_reports_users` (`users_id`),
  CONSTRAINT `user_reports_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO user_reports VALUES
('1','6','test','1',NULL,NULL,NULL,NULL,'1,3,4,5,7,6,2,8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

CREATE TABLE IF NOT EXISTS `discussions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_id` int(11) NOT NULL DEFAULT '0',
  `users_id` int(11) DEFAULT NULL,
  `discussions_status_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `assigned_to` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_discussions_projects` (`projects_id`),
  KEY `fk_discussions_users` (`users_id`),
  KEY `fk_discussions_discussions_status` (`discussions_status_id`),
  CONSTRAINT `discussions_ibfk_1` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `discussions_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `discussions_ibfk_3` FOREIGN KEY (`discussions_status_id`) REFERENCES `discussions_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `discussions_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discussions_id` int(11) NOT NULL DEFAULT '0',
  `users_id` int(11) DEFAULT NULL,
  `discussions_status_id` int(11) DEFAULT NULL,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_discussions_comments_discussions` (`discussions_id`),
  KEY `fk_discussions_comments_users` (`users_id`),
  KEY `fk_discussions_status_id` (`discussions_status_id`),
  CONSTRAINT `discussions_comments_ibfk_1` FOREIGN KEY (`discussions_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `discussions_comments_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `discussions_comments_ibfk_3` FOREIGN KEY (`discussions_status_id`) REFERENCES `discussions_status` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `discussions_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `display_on_home` tinyint(1) DEFAULT NULL,
  `projects_id` text,
  `projects_type_id` text,
  `projects_status_id` text,
  `discussions_status_id` text,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  CONSTRAINT `discussions_reports_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

