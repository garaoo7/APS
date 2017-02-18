create database APS;

create table answer(
	id int(10) auto_increment primary key,
	answerId int(10),
    questionId int(10),
    answerText varchar(5000),
    userId int(10),
    creationDate datetime,
    upVotes int(10) default 0,
    status enum('live','deleted','closed','abused') NOT NULL DEFAULT 'live'
);

create table answerUpvotes(
	id int(10) auto_increment primary key,
	userId int(10),
    answerId int(10)
);

create table questions(
 id int(10)  auto_increment primary key,
 questionId int(10),
 title varchar(5000),
 description varchar(5000),
 creationDate datetime not null,
 updated datetime,
 viewCount int(10) default 0,
 ansCount int(10) default 0,
 userId int(10),
 status enum('live','deleted','closed','abused') NOT NULL DEFAULT 'live'
);

create table tag(
 id int(10) auto_increment primary key,	
 tagId int(10),
 tagName varchar(255),
 tag_quality_score float(10,4) default 0.00,
 main_id int(10),
 status enum('live','deleted') NOT NULL DEFAULT 'live'
);

create table questionTag(
	id int(10) auto_increment primary key,
	questionId int(10),
    tagId int(10),
    status enum('live','deleted') NOT NULL DEFAULT 'live'
);


create index questionId on questions (questionId);
create index tagId on tag(tagId);
create index questionTag on questionTag(questionId,tagId);

SET GLOBAL group_concat_max_len=100999;