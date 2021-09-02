create database database_name_goes_here;

use database_name_goes_here;

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