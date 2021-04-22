create database autism_tracker;

use autism_tracker;

create table users (
	id			int not null auto_increment,
    username 	varchar(20) not null,
    password 	varchar(255) not null,
    email 		varchar(150) not null,
    is_deleted 	tinyint(1) not null default 0,
    is_admin	tinyint(1) not null default 0,
    created 	datetime not null,
    modified 	datetime not null,
    
    constraint pk_users primary key (id asc),
    constraint uq_users_username unique (username),
    constraint uq_users_email unique (email)
) engine = innodb;

create table user_sessions (
	id			int not null auto_increment,
    user_id 	int not null,
    session 	varchar(255) not null,
    user_agent	varchar(250) not null,
    created 	datetime not null,
    modified 	datetime not null,
    
    constraint pk_user_sessions primary key (id asc),
    constraint fk_user_sessions_users foreign key (user_id) references users (id),
    constraint uq_user_sessions unique (session)
) engine = innodb;

create table patients (
	id			int not null auto_increment,
    user_id		int not null,
    relationship varchar(50),
    age			int not null,
    sex			enum('M', 'F') not null,
    created		datetime not null,
    modified	datetime,
    
    constraint pk_patients primary key (id),
    constraint fk_patients_users foreign key (user_id) references users (id)
) engine = innodb;

create table diagnoses (
	id			int not null auto_increment,
    name		varchar(50) not null,
    description	text not null,
    created		datetime not null,
    created_by 	int not null,
    modified	datetime,
    modified_by	int,
    
    constraint pk_diagnoses primary key (id),
    constraint fk_diagnoses_users_created_by foreign key (created_by) references users (id),
    constraint fk_diagnoses_users_modified_by foreign key (modified_by) references users (id),
    constraint uq_diagnoses_name unique (name)
) engine = innodb;

create table diagnosis_levels (
	id			int not null auto_increment,
    name		varchar(50) not null,
    description	text not null,
    created		datetime not null,
    created_by 	int not null,
    modified	datetime,
    modified_by	int,
    
    constraint pk_diagnosis_levels primary key (id),
    constraint fk_diagnosis_levels_users_created_by foreign key (created_by) references users (id),
    constraint fk_diagnosis_levels_users_modified_by foreign key (modified_by) references users (id),
    constraint uq_diagnosis_levels_name unique (name)
) engine = innodb;

create table age_groups (
	id			int not null auto_increment,
    description	varchar(50) not null,
    minimum_age	int not null,
    maximum_age	int not null,
    diagnosis_id	int not null,
    created		datetime not null,
    created_by 	int not null,
    modified	datetime,
    modified_by	int,
    
    constraint pk_age_groups primary key (id),
    constraint fk_age_groups_users_created_by foreign key (created_by) references users (id),
    constraint fk_age_groups_users_modified_by foreign key (modified_by) references users (id),
    constraint fk_age_groups_diagnoses foreign key (diagnosis_id) references diagnoses (id)
) engine = innodb;

create table questions (
	id			int not null auto_increment,
    age_group_id	int not null,
    question	varchar(200) not null,
    created		datetime not null,
    created_by 	int not null,
    
    constraint pk_questions primary key (id),
    constraint fk_questions_age_groups foreign key (age_group_id) references age_groups (id),
    constraint uq_questions_age_group_id_question unique (age_group_id, question),
    constraint fk_questions_users_created_by foreign key (created_by) references users (id)
) engine = innodb;

create table patient_assessments (
	id			int not null auto_increment,
    patient_id	int not null,
    is_formally_diagnosed	tinyint(1) not null,
    diagnosis_id	int,
    diagnosis_level_id	int,
    age_at_diagnosis	int,
    verbal_level	varchar(50),
    created		datetime not null,
    created_by 	int not null,
    modified	datetime,
    modified_by	int,
    
    constraint pk_patient_assessments primary key (id),
    constraint fk_patient_assessments_users foreign key (patient_id) references patients (id),
    constraint fk_patient_assessments_diagnoses foreign key (diagnosis_id) references diagnoses (id),
    constraint fk_patient_assessments_diagnosis_levels foreign key (diagnosis_level_id) references diagnosis_levels (id),
    constraint fk_patient_assessments_users_created_by foreign key (created_by) references users (id),
    constraint fk_patient_assessments_users_modified_by foreign key (modified_by) references users (id)
) engine = innodb;

create table assessment_behaviours (
	id			int not null auto_increment,
    question	varchar(300) not null,
    created		datetime not null,
    created_by 	int not null,
    modified	datetime,
    modified_by	int,
    
    constraint pk_assessment_behaviours primary key (id),
    constraint fk_assessment_behaviours_users_created_by foreign key (created_by) references users (id),
    constraint fk_assessment_behaviours_users_modified_by foreign key (modified_by) references users (id)
) engine = innodb;

create table assessment_behaviour_options (
	id			int not null auto_increment,
    assessment_behaviour_id	int not null,
    behaviour_option		varchar(50) not null,
    
    constraint pk_assessment_behaviour_options primary key (id),
    constraint fk_assessment_behaviour_options_assessment_behaviours foreign key (assessment_behaviour_id) references assessment_behaviours (id),
    constraint uq_assessment_behaviour_options_behaviour_option unique (assessment_behaviour_id, behaviour_option)
) engine = innodb;

create table patient_assessment_behaviours (
	id			int not null auto_increment,
    patient_assessment_id	int not null,
    assessment_behaviour_id	int not null,
    assessment_behaviour_option_id	int not null,
    other		varchar(100),
    
    constraint pk_patient_assessment_behaviours primary key (id),
    constraint fk_patient_assessment_behaviours_patient_assessments foreign key (patient_assessment_id) references patient_assessments (id),
    constraint fk_patient_assessment_behaviours_assessment_behaviour_options foreign key (assessment_behaviour_option_id) references assessment_behaviour_options (id)
) engine = innodb;

create table patient_diagnoses (
	id			int not null auto_increment,
	patient_id	int not null,
    diagnosis_id	int not null,
    is_diagnosed_positive	tinyint(1) not null,
    created		datetime not null,
    created_by 	int not null,
    modified	datetime,
    modified_by	int,
	
    constraint pk_patient_diagnoses primary key (id),
    constraint fk_patient_diagnoses_patients foreign key (patient_id) references patients (id),
    constraint fk_patient_diagnoses_users_created_by foreign key (created_by) references users (id),
    constraint fk_patient_diagnoses_users_modified_by foreign key (modified_by) references users (id),
    constraint fk_patient_diagnoses_diagnoses foreign key (diagnosis_id) references diagnoses (id)
) engine = innodb;

create table patient_diagnosis_questions (
	id			int not null auto_increment,
	patient_diagnosis_id	int not null,
    question_id	int not null,
    option_selected	tinyint(1),
	
    constraint pk_patient_diagnosis_questions primary key (id),
    constraint fk_patient_diagnosis_questions_patient_diagnoses foreign key (patient_diagnosis_id) references patient_diagnoses (id),
    constraint fk_patient_diagnosis_questions_questions foreign key (question_id) references questions (id)
) engine = innodb;

create table question_rules (
	id			int not null auto_increment,
    age_group_id	int not null,
    rule_name	varchar(50) not null,
    output		tinyint(1) default 0,
    created		datetime not null,
    created_by	int not null,
    modified	datetime,
    modified_by	int,
    
    constraint pk_question_rules primary key (id),
    constraint fk_question_rules_age_groups foreign key (age_group_id) references age_groups (id),
    constraint fk_question_rules_users_created_by foreign key (created_by) references users (id),
    constraint fk_question_rules_users_modified_by foreign key (modified_by) references users (id),
    constraint uq_question_rules_age_group_id_rule_name unique (age_group_id, rule_name),
    constraint ck_question_rules_output check (output in (0, 1))
) engine = innodb;

create table question_rule_details (
	id			int not null auto_increment,
    question_rule_id	int not null,
    question_id	int not null,
    option_value	tinyint(1) not null default 0,
    
    constraint pk_question_rule_details primary key (id),
    constraint fk_question_rule_details_question_rules foreign key (question_rule_id) references question_rules (id),
    constraint fk_question_rule_details_questions foreign key (question_id) references questions (id),
    constraint ck_question_rule_details_option_value check (option_value in (0, 1))
) engine = innodb;

-- updates: 2020-11-16
alter table patients 
	add column name varchar(50) after user_id,
    add column email varchar(150) after name;

alter table users add is_parent tinyint(1) default 0 after is_admin;