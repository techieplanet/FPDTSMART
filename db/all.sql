
use `itechweb_eventsmart_gates`;

/*
2014-01-05 Sean Smith 
For Request:
Add a check box for residential vs. non-residential on a students record.  Should fall after local address and before next of kin address
*/
ALTER TABLE `person`
ADD COLUMN `home_is_residential`  tinyint(1) NULL AFTER `home_postal_code`;

/*
2014-01-08 Sean Smith 
For Request:
Adding a pass/fail  credits columns in the class history table for a student.  
Both columns would behave the same way the grade column works, click on it and enter what you want.
*/

/* This was added to undo change to test.trainingdata.org db */
ALTER TABLE link_student_classes CHANGE pass_credits credits VARCHAR(50);
ALTER TABLE link_student_classes DROP COLUMN fail_credits;

ALTER TABLE `link_student_classes`
ADD COLUMN `credits`  varchar(50) NULL AFTER `grade`;

ALTER TABLE `link_student_practicums`
ADD COLUMN `credits`  varchar(50) NULL AFTER `grade`;

ALTER TABLE `link_student_licenses`
ADD COLUMN `credits`  varchar(50) NULL AFTER `grade`;


/*
2014-02-15 Greg Rossum
*/

alter table _system add column display_training_completion tinyint(1) NOT NULL DEFAULT '0';


/*
2014-02-14 Greg Rossum
*/
alter table evaluation_response add person_id int null after evaluation_to_training_id;
alter table evaluation_response change column trainer_person_id trainer_person_id int null;


/*
2014-07-29 Tamara Astakhova
For Request: Haiti PreService assignment
*/

ALTER TABLE `person` ADD COLUMN `custom_field1` varchar(255) NULL;
ALTER TABLE `person` ADD COLUMN `custom_field2` varchar(255) NULL;
ALTER TABLE `person` ADD COLUMN `custom_field3` varchar(255) NULL;
ALTER TABLE `person` ADD COLUMN `marital_status` varchar(20) NULL;
ALTER TABLE `person` ADD COLUMN `spouse_name` varchar(50) NULL;

ALTER TABLE `student` ADD COLUMN `hscomldate` DATE NOT NULL;
ALTER TABLE `student` ADD COLUMN `lastinstatt` varchar(50) NOT NULL;
ALTER TABLE `student` ADD COLUMN `schoolstartdate` DATE NOT NULL;
ALTER TABLE `student` ADD COLUMN `equivalence` tinyint(4) NOT NULL DEFAULT '0';
ALTER TABLE `student` ADD COLUMN `lastunivatt` varchar(50) NOT NULL;
ALTER TABLE `student` ADD COLUMN `personincharge` varchar(50) NOT NULL;
ALTER TABLE `student` ADD COLUMN `emergcontact`varchar(50) NOT NULL;


ALTER TABLE `tutor` ADD COLUMN `specialty` varchar(100) NOT NULL;
ALTER TABLE `tutor` ADD COLUMN `contract_type` varchar(100) NOT NULL;


ALTER TABLE `_system` ADD COLUMN `ps_display_inst_compl_date` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_last_inst_attended` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_start_school_date` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_equivalence` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_last_univ_attended` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_person_charge` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_custom_field1` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_custom_field2` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_custom_field3` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_marital_status` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_spouse_name` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_specialty` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_contract_type` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_local_address` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_permanent_address` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_religious_denomin` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_nationality` tinyint(1) NOT NULL DEFAULT '0';

/* just leave in for case if this records in database */
UPDATE translation SET is_deleted = 0 WHERE key_phrase = 'ps clinical allocation';
UPDATE translation SET is_deleted = 0 WHERE key_phrase = 'ps local address';

INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps clinical allocation','Clinical Allocation',1,null,0,'2014-07-25 13:40:02','2014-07-18 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps local address','Local Address',1,null,0,'2014-07-25 13:40:02','2014-07-18 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps last school attended','Last School Attended',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps high school completion date','High School Completion Date',1,null,0,'2014-07-25 13:40:02','2014-07-18 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps last school attended','Last School Attended',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps school start date','Admission to School Date',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps equivalence','Equivalence',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps last university attended','Last University Attended',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps person in charge','Person In Charge',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps license and registration','License And Registration',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps permanent address','Permanent Address',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps religious denomination','Religious Denomination',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps program enrolled in','Program Enrolled In',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps nationality','Nationality',1,null,0,'2014-07-25 13:40:02','2014-07-21 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps custom field 1','Custom Field 1',1,null,0,'2014-07-25 13:40:02','2014-07-22 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps custom field 2','Custom Field 2',1,null,0,'2014-07-25 13:40:02','2014-07-22 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps custom field 3','Custom Field 3',1,null,0,'2014-07-25 13:40:02','2014-07-22 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps marital status','Marital Status',1,null,0,'2014-07-25 13:40:02','2014-07-22 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps spouse name','Spouse Name',1,null,0,'2014-07-25 13:40:02','2014-07-22 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps specialty','Specialty',1,null,0,'2014-07-25 13:40:02','2014-07-22 00:00:00');
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`,`timestamp_updated`,`timestamp_created`) VALUES ('ps contract type','Type Of Contract',1,null,0,'2014-07-25 13:40:02','2014-07-22 00:00:00');

CREATE TABLE `tutor_contract_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `uuid` varchar(36) DEFAULT NULL,
  `contract_phrase` varchar(128) NOT NULL,
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `tutor_specialty_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `uuid` varchar(36) DEFAULT NULL,
  `specialty_phrase` varchar(128) NOT NULL,
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;



/*
2014-07-29 Tamara Astakhova
For Request: Haiti PreService assignment patch
*/



INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('ps tutor','Teacher',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('ps institution','Training Centre',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('ps zip code','Postal Code / ZIP',1,null,0);


ALTER TABLE `link_student_classes`
ADD COLUMN `camark` varchar(50) collate utf8_unicode_ci NOT NULL default '' after `linkclasscohortid`;

ALTER TABLE `link_student_classes`
ADD COLUMN `exammark` varchar(50) collate utf8_unicode_ci NOT NULL default '' AFTER `camark`;

/*
2014-09-14
Greg Rossum
For Request: Employee Funding Mechanisms
*/



alter table employee add column employee_code varchar(32) default null;
alter table employee add unique key employee_code_key (employee_code);

CREATE TABLE `employee_to_partner_to_subpartner_to_funder_to_mechanism` (
  `id` int(11) NOT NULL auto_increment,
  `partner_to_subpartner_to_funder_to_mechanism_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `subpartner_id` int(11) NOT NULL,
  `partner_funder_option_id` int(11) NOT NULL,
  `mechanism_option_id` int(11) NOT NULL,
  `percentage` int(11) NOT NULL default '0',
  `created_by` int(11) default NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `idx2` (`employee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2974 DEFAULT CHARSET=utf8;


CREATE TABLE `mechanism_option` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` char(36) default NULL,
  `mechanism_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) default NULL,
  `created_by` int(11) default NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_unique` (`mechanism_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=116 DEFAULT CHARSET=utf8;




CREATE TABLE `subpartner_to_funder_to_mechanism` (
  `id` int(11) NOT NULL auto_increment,
  `subpartner_id` int(11) NOT NULL,
  `partner_funder_option_id` int(11) NOT NULL,
  `mechanism_option_id` int(11) NOT NULL,
  `funding_end_date` datetime default NULL,
  `created_by` int(11) default NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `subpartner_id` (`subpartner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=216 DEFAULT CHARSET=utf8;

CREATE TABLE `partner_to_subpartner_to_funder_to_mechanism` (
  `id` int(11) NOT NULL auto_increment,
  `subpartner_to_funder_to_mechanism_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `subpartner_id` int(11) NOT NULL,
  `partner_funder_option_id` int(11) NOT NULL,
  `mechanism_option_id` int(11) NOT NULL,
  `funding_end_date` datetime default NULL,
  `created_by` int(11) default NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `partner_id` (`partner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=286 DEFAULT CHARSET=utf8;

/*
2014-10-01 
Tamara Astakhova
For Request: CHAI project
*/

ALTER TABLE `_system` ADD COLUMN `display_training_category` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_training_start_date` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_training_length` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_training_level` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_training_comments` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_facilitator_info` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_training_score` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_facility_address` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_facility_phone` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_facility_fax` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_facility_city` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_facility_type` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_people_birthdate` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_people_comments` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_people_facilitator` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_country_reports` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `_system` ADD COLUMN `display_facility_commodity` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `training` modify COLUMN `training_start_date` date default NULL;
ALTER TABLE `training` modify COLUMN `training_length_interval` enum('hour','week','day') default NULL;

ALTER TABLE `facility` modify COLUMN `type_option_id` int(11) default NULL;

CREATE TABLE `commodity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `consumption` int(11) DEFAULT NULL,
  `stock_out` char(1) NOT NULL DEFAULT 'N',
  `facility_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT '0',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `timestamp_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

CREATE TABLE `commodity_name_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commodity_name` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` tinyint(1) NOT NULL,
  `uuid` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Facility Commodity Column Table Commodity Name','Commodity Name',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Facility Commodity Column Table Date','Date',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Facility Commodity Column Table Consumption','Consumption',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Facility Commodity Column Table Out of Stock','Out of Stock',1,null,0);




/*
2014-10-07 
Ben Smith
For Request: PEPFAR South Africa Employee Module Translation
*/

INSERT INTO `translation` (`key_phrase`, `phrase`, `modified_by`, `is_deleted`) VALUES ('Employee', 'Employee', '1', '0');
INSERT INTO `translation` (`key_phrase`, `phrase`, `modified_by`, `is_deleted`) VALUES ('Employees', 'Employees', '1', '0');


ALTER TABLE `_system` ADD COLUMN `display_hours_per_mechanism` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `display_annual_cost_to_mechanism` tinyint(1) NOT NULL DEFAULT '0';
/*
2014-10-09
Rayce Rossum
For Request: Pre-service
*/

ALTER TABLE `_system` ADD COLUMN `ps_display_exam_mark` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_ca_mark` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system` ADD COLUMN `ps_display_credits` tinyint(1) NOT NULL DEFAULT '0';

INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('ps exam mark','Exam Mark',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('ps ca mark','CA Mark',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('ps credits','Credits',1,null,0);

alter table link_student_classes add column `exammark` varchar(50) collate utf8_unicode_ci NOT NULL default ''; 
alter table link_student_classes add column `camark` varchar(50) collate utf8_unicode_ci NOT NULL default '';

alter table training modify column training_length_value int(11) default '0'; 
alter table training modify column training_length_interval enum('hour','week','day') default 'hour'; 

/*
2014-10-24 
Tamara Astakhova
For Request: CHAI project: Monthly emails report
*/

ALTER TABLE _system ADD COLUMN display_email_report_1 tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE _system ADD COLUMN display_email_report_2 tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE _system ADD COLUMN display_email_report_3 tinyint(1) NOT NULL DEFAULT '0';

INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Label Email Report Level 1','Federal',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Label Email Report Level 2','State',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Label Email Report Level 3','LGA',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Emails Report Level 1','',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Emails Report Level 2','',1,null,0);
INSERT INTO `translation`(`key_phrase`,`phrase`,`modified_by`,`created_by`,`is_deleted`) VALUES ('Emails Report Level 3','',1,null,0);




INSERT INTO `acl` (`id`, `acl`) VALUES ('edit_partners', 'edit_partners');
INSERT INTO `acl` (`id`, `acl`) VALUES ('edit_mechanisms', 'edit_mechanisms');
INSERT INTO `acl` (`id`, `acl`) VALUES ('employees_module', 'employees_module');
INSERT INTO `acl` (`id`, `acl`) VALUES ('view_training_location', 'view_training_location');
INSERT INTO `acl` (`id`, `acl`) VALUES ('view_employee', 'view_employee');
INSERT INTO `acl` (`id`, `acl`) VALUES ('view_mechanisms', 'view_mechanisms');
INSERT INTO `acl` (`id`, `acl`) VALUES ('view_partners', 'view_partners');

ALTER TABLE `user_to_acl` MODIFY COLUMN `acl_id` ENUM('ps_view_student_grades','ps_edit_student_grades','ps_view_student','ps_edit_student','in_service','pre_service','employees_module','edit_employee','view_training_location','view_employee','view_mechanisms','view_partners','edit_course','view_course','duplicate_training','approve_trainings','master_approver','edit_people','view_people','edit_training_location','edit_facility','view_facility','view_create_reports','training_organizer_option_all','training_title_option_all','use_offline_app','admin_files','facility_and_person_approver','edit_evaluations','edit_country_options','acl_editor_training_category','acl_editor_people_qualifications','acl_editor_people_responsibility','acl_editor_training_organizer','acl_editor_people_trainer','acl_editor_training_topic','acl_editor_people_titles','acl_editor_training_level','acl_editor_refresher_course','acl_editor_people_trainer_skills','acl_editor_pepfar_category','acl_editor_people_languages','acl_editor_funding','acl_editor_people_affiliations','acl_editor_recommended_topic','acl_editor_nationalcurriculum','acl_editor_people_suffix','acl_editor_method','acl_editor_people_active_trainer','acl_editor_facility_types','acl_editor_ps_classes','acl_editor_facility_sponsors','acl_editor_ps_cadres','acl_editor_ps_degrees','acl_editor_ps_funding','acl_editor_ps_institutions','acl_editor_ps_languages','acl_editor_ps_nationalities','acl_editor_ps_joindropreasons','acl_editor_ps_sponsors','acl_editor_ps_tutortypes','acl_editor_ps_coursetypes','acl_editor_ps_religions','add_edit_users','acl_admin_training','acl_admin_people','acl_admin_facilities','import_training','import_training_location','import_facility','import_person','edit_partners','edit_mechanisms','add_new_facility') NOT NULL DEFAULT 'view_course' ;
INSERT INTO `user_to_acl` (acl_id, user_id, created_by, timestamp_created) select 'employees_module', user_id, created_by, timestamp_created from user_to_acl where acl_id = 'edit_employee';
INSERT INTO `translation` (`key_phrase`, `phrase`, `modified_by`, `is_deleted`) VALUES ('Employer', 'Employer', '1', '0');CREATE TABLE `link_user_cadres` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `cadreid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx2` (`userid`,`cadreid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

insert into acl values 
( 'edit_studenttutorinst', 'edit_studenttutorinst'),
( 'acl_delete_ps_cohort', 'acl_delete_ps_cohort'),
( 'view_studenttutorinst', 'view_studenttutorinst'),
( 'acl_delete_ps_student', 'acl_delete_ps_student'),
( 'acl_delete_ps_grades', 'acl_delete_ps_grades');

ALTER TABLE user_to_acl
change acl_id acl_id enum('in_service','pre_service','edit_employee','edit_course','view_course','duplicate_training','approve_trainings','master_approver','edit_people','view_people','edit_training_location','edit_facility','view_facility','view_create_reports','training_organizer_option_all','training_title_option_all','use_offline_app','admin_files','facility_and_person_approver','edit_evaluations','edit_country_options','acl_editor_training_category','acl_editor_people_qualifications','acl_editor_people_responsibility','acl_editor_training_organizer','acl_editor_people_trainer','acl_editor_training_topic','acl_editor_people_titles','acl_editor_training_level','acl_editor_refresher_course','acl_editor_people_trainer_skills','acl_editor_pepfar_category','acl_editor_people_languages','acl_editor_funding','acl_editor_people_affiliations','acl_editor_recommended_topic','acl_editor_nationalcurriculum','acl_editor_people_suffix','acl_editor_method','acl_editor_people_active_trainer','acl_editor_facility_types','acl_editor_ps_classes','acl_editor_facility_sponsors','acl_editor_ps_cadres','acl_editor_ps_degrees','acl_editor_ps_funding','acl_editor_ps_institutions','acl_editor_ps_languages','acl_editor_ps_nationalities','acl_editor_ps_joindropreasons','acl_editor_ps_sponsors','acl_editor_ps_tutortypes','acl_editor_ps_coursetypes','acl_editor_ps_religions','add_edit_users','acl_admin_training','acl_admin_people','acl_admin_facilities','import_training','import_training_location','import_facility','import_person',
'edit_studenttutorinst', 'acl_delete_ps_cohort', 'view_studenttutorinst', 'acl_delete_ps_student', 'acl_delete_ps_grades');


/****************************************************************
2015-01-06
Tamara Astakhova
For Request: CHAI project: DHIS2 automatic upload of facility/locations, commodity names, commodity data. In 'dev_test_copy' only.
****************************************************************/

ALTER TABLE `facility` ADD COLUMN `external_id` varchar(20) DEFAULT NULL UNIQUE;
ALTER TABLE `location` ADD COLUMN `external_id` varchar(20) DEFAULT NULL UNIQUE;
ALTER TABLE `commodity_name_option` ADD COLUMN `external_id` varchar(20) DEFAULT NULL UNIQUE;

delete from facility;

/* leave only 6 zones and 37 states: total 43 records should be left in 'location' table*/
delete from location where tier=3;
UPDATE location SET location_name='Akwa Ibom' WHERE location_name='Akwa-Ibom';

delete from commodity_name_option;

delete from commodity;

/***************************************************************
2015-01-06
Tamara Astakhova
For Request: CHAI project: DHIS2 automatic upload of facility report rate
****************************************************************/

CREATE TABLE `facility_report_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `facility_external_id` varchar(20) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7569 DEFAULT CHARSET=latin1;/*
2014-11-01
Tamara Astakhova
For Request: CHAI project: Commodity type
*/

drop table `commodity`;

CREATE TABLE `commodity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` int(11) NOT NULL,
  `date` date NOT NULL,
  `consumption` int(11) DEFAULT NULL,
  `stock_out` char(1) NOT NULL DEFAULT 'N',
  `facility_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT '0',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `timestamp_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;

CREATE TABLE `commodity_type_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commodity_type` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` tinyint(1) NOT NULL,
  `uuid` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;


ALTER TABLE `commodity_name_option` ADD COLUMN `commodity_type_id` int(11) DEFAULT NULL;

INSERT INTO `acl`(`id`,`acl`) VALUES ('add_new_facility','add_new_facility');

ALTER TABLE `user_to_acl` 
CHANGE COLUMN `acl_id` `acl_id` ENUM('ps_view_student_grades','ps_edit_student_grades','ps_view_student','ps_edit_student','in_service','pre_service','edit_employee','edit_course','view_course','duplicate_training','approve_trainings','master_approver','edit_people','view_people','edit_training_location','edit_facility','view_facility','view_create_reports','training_organizer_option_all','training_title_option_all','use_offline_app','admin_files','facility_and_person_approver','edit_evaluations','edit_country_options','acl_editor_training_category','acl_editor_people_qualifications','acl_editor_people_responsibility','acl_editor_training_organizer','acl_editor_people_trainer','acl_editor_training_topic','acl_editor_people_titles','acl_editor_training_level','acl_editor_refresher_course','acl_editor_people_trainer_skills','acl_editor_pepfar_category','acl_editor_people_languages','acl_editor_funding','acl_editor_people_affiliations','acl_editor_recommended_topic','acl_editor_nationalcurriculum','acl_editor_people_suffix','acl_editor_method','acl_editor_people_active_trainer','acl_editor_facility_types','acl_editor_ps_classes','acl_editor_facility_sponsors','acl_editor_ps_cadres','acl_editor_ps_degrees','acl_editor_ps_funding','acl_editor_ps_institutions','acl_editor_ps_languages','acl_editor_ps_nationalities','acl_editor_ps_joindropreasons','acl_editor_ps_sponsors','acl_editor_ps_tutortypes','acl_editor_ps_coursetypes','acl_editor_ps_religions','add_edit_users','acl_admin_training','acl_admin_people','acl_admin_facilities','import_training','import_training_location','import_facility','import_person','add_new_facility') NOT NULL DEFAULT 'view_course' ;





ALTER TABLE `_system` ADD COLUMN `display_training_location` tinyint(1) NOT NULL DEFAULT '0';
