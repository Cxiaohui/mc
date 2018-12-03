

CREATE TABLE `jindu_zy_users` (
  id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  status tinyint NOT NULL DEFAULT '0' COMMENT '0未审核，1通过，2未通过',
  mobile varchar(12) NOT NULL DEFAULT '' COMMENT '手机',
  name varchar(32) NOT NULL DEFAULT '' COMMENT '姓名',
  gender tinyint NOT NULL DEFAULT '0' COMMENT '性别：0未知1男2女',
  dob date NOT NULL DEFAULT 0  COMMENT '出生日期',
  job_title varchar(64) NOT NULL DEFAULT '' COMMENT '职业',
  work_address varchar(225) NOT NULL DEFAULT '' COMMENT '工作单位',
  ID_number varchar(32) NOT NULL DEFAULT '' COMMENT '身份证号',
  head_pic varchar(225) NOT NULL DEFAULT '' COMMENT '头像',
  home_address varchar(225) NOT NULL DEFAULT '' COMMENT '家庭地址',
  special_ability varchar(225) NOT NULL DEFAULT '' COMMENT '特殊能力',
  device_token varchar(64) NOT NULL DEFAULT '' COMMENT '设备token',
  api_token varchar(32) NOT NULL DEFAULT '' COMMENT 'api token',
  add_time datetime NOT NULL DEFAULT 0  COMMENT '创建时间',
  isdel tinyint NOT NULL DEFAULT '0' COMMENT '0否1是',
  PRIMARY KEY (`id`)

)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='志愿者信息表';

CREATE TABLE jindu_zy_users_check_record(
  id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  zy_user_id int unsigned NOT NULL DEFAULT '0' COMMENT '志愿id',
  check_user_id int unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  check_user_name varchar(32) NOT NULL DEFAULT '' COMMENT '管理员名称',
  check_result varchar(225) NOT NULL DEFAULT '' COMMENT '审核结果',
  check_time datetime NOT NULL DEFAULT 0  COMMENT '审核时间',
  PRIMARY KEY (`id`),
 foreign key(zy_user_id) references jindu_zy_users(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='志愿者信息审核记录表';

CREATE TABLE jindu_zy_intelligences(
 id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 zy_user_id int unsigned NOT NULL DEFAULT '0' COMMENT '志愿id',
 status tinyint NOT NULL DEFAULT '0' COMMENT '0未审核，1通过，2未通过',
 type_id smallint unsigned NOT NULL DEFAULT '0' COMMENT '内容类型id',
 lbs_longitude varchar(16) NOT NULL DEFAULT '' COMMENT '经度',
 lbs_latitude varchar(16) NOT NULL DEFAULT '' COMMENT '纬度',
 add_time datetime NOT NULL DEFAULT 0  COMMENT '创建时间',
 PRIMARY KEY (`id`),
 foreign key(zy_user_id) references jindu_zy_users(id),
 foreign key(type_id) references jindu_zy_intelligence_types(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='志愿者情报收集信息表';

CREATE TABLE jindu_zy_intelligence_types(
 id smallint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 name varchar(32) NOT NULL DEFAULT '' COMMENT '分类名称',
 sort  tinyint NOT NULL DEFAULT '0' COMMENT '排序',
 PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='志愿者情报收集信息分类表';


CREATE TABLE jindu_zy_intelligence_contents(
 id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 zy_int_id int unsigned NOT NULL DEFAULT '0' COMMENT '情报id',
 type_id smallint unsigned NOT NULL DEFAULT '0' COMMENT '内容类型id',
 content text not null comment '内容',
 PRIMARY KEY (`id`),
 foreign key(type_id) references jindu_zy_intelligence_types(id),
 foreign key(zy_int_id) references jindu_zy_intelligences(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='志愿者情报收集信息内容表';

CREATE TABLE jindu_zy_intelligence_check_record(

  id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  zy_int_id int unsigned NOT NULL DEFAULT '0' COMMENT '情报id',
  check_user_id int unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  check_user_name varchar(32) NOT NULL DEFAULT '' COMMENT '管理员名称',
  check_result varchar(225) NOT NULL DEFAULT '' COMMENT '审核结果',
  check_time datetime NOT NULL DEFAULT 0  COMMENT '审核时间',
  PRIMARY KEY (`id`),
 foreign key(zy_int_id) references jindu_zy_intelligences(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='志愿者情报收集跟进';


CREATE TABLE jindu_zy_pushs(
 id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 zy_user_id int unsigned NOT NULL DEFAULT '0' COMMENT '志愿id',
 device_token varchar(64) NOT NULL DEFAULT '' COMMENT '设备token',
 title varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
 message varchar(255) NOT NULL DEFAULT '' COMMENT '消息内容',
 extras varchar(255) NOT NULL DEFAULT '' COMMENT '',
 push_time datetime NOT NULL DEFAULT 0  COMMENT '推送时间',
 push_result varchar(255) NOT NULL DEFAULT '' COMMENT '推送结果',
 add_time datetime NOT NULL DEFAULT 0  COMMENT '创建时间',
 PRIMARY KEY (`id`),
 foreign key(zy_user_id) references jindu_zy_users(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推送信息';

CREATE TABLE jindu_zy_news(
 id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 title varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
 type_id smallint unsigned NOT NULL DEFAULT '0' COMMENT '资讯类型id',
 news_time datetime NOT NULL DEFAULT 0  COMMENT '发布时间',
 content text not null comment '资讯内容',
 add_time datetime NOT NULL DEFAULT 0  COMMENT '创建时间',
 PRIMARY KEY (`id`),
 foreign key(type_id) references jindu_zy_news_types(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资讯';

CREATE TABLE jindu_zy_news_types(
 id smallint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 name varchar(32) NOT NULL DEFAULT '' COMMENT '类型名称',
 sort tinyint NOT NULL DEFAULT '0' COMMENT '排序',
 PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资讯类型';
