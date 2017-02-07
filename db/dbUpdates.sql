create database APS;

create table answer(
	answerId int(10) auto_increment primary key,
    questionId int(10),
    answerText varchar(5000),
    userId int(10),
    creationTime datetime,
    upVotes int(10) default 0,
    status enum('live','history')    
);

create table answerUpvotes(
	id int(10) auto_increment primary key,
	userId int(10),
    answerId int(10)
);

create table questions(
 questionId int(10)  auto_increment primary key,
 title varchar(1000),
 description varchar(5000),
 creationTime datetime not null,
 updated datetime,
 viewCount int(10) default 0,
 ansCount int(10) default 0,
 userId int(10),
 status enum('live','history')
);

create table tag(
 tagId int(10) auto_increment primary key,
 tagName varchar(100),
 `tag_quality_score` float(10,4) default 0.00
);

create table questionTag(
	id int(10) auto_increment primary key,
	questionId int(10),
    tagId int(10)
);