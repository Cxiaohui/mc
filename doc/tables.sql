-- mc_admin  表加下入以下字段

head_pic
mobile
allow_lg_b '允许登录b端：0否，1是'
b_power_tag 'b端权限标记：0没有权限,1查看所有项目，2查看操作所有荐，3项目中分配的权限'
post_desc '岗位描述'
post_duties '岗位职责'
is_join_team
team_id
team_name
jpush_tag

CREATE TABLE `mc_admin` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `log` varchar(20) NOT NULL DEFAULT '' COMMENT '账号',
  `pwd` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `stat` char(5) NOT NULL DEFAULT '' COMMENT '随机码',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `role` varchar(10) NOT NULL DEFAULT '' COMMENT '角色名',
  `name` varchar(16) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(15) NOT NULL DEFAULT '',
  `allow_lg_b` tinyint(4) NOT NULL DEFAULT '0' COMMENT '允许登录b端：0否，1是',
  `b_power_tag` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'b端权限标记：0没有权限,1查看所有项目，2查看操作所有荐，3项目中分配的权限',
  `head_pic` varchar(128) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别:0未知，1男，2女',
  `is_work` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否在职',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '登录控制',
  `depart_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '部门Id',
  `department` varchar(20) NOT NULL DEFAULT '' COMMENT '部门名称',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `loginip` varchar(64) NOT NULL DEFAULT '' COMMENT '最近登录ip',
  `lgtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最近登录时间',
  `post` varchar(20) NOT NULL DEFAULT '' COMMENT '职位',
  `post_desc` varchar(128) NOT NULL DEFAULT '' COMMENT '岗位描述',
  `post_duties` varchar(225) NOT NULL DEFAULT '' COMMENT '岗位职责',
  `contact` varchar(100) NOT NULL DEFAULT '' COMMENT '联系方式',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注信息',
  `is_join_team` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否加入团队',
  `team_id` int(11) NOT NULL DEFAULT '0',
  `team_name` varchar(32) NOT NULL DEFAULT '',
  `jpush_tag` varchar(32) NOT NULL DEFAULT '' COMMENT 'jpush 推送的标识',
  `im_token` varchar(130) NOT NULL DEFAULT '' COMMENT 'IM Token',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `depart_id` (`depart_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户表';


-- 团队分类
CREATE TABLE mc_team_names(
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '团队ID',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '名称',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间 ',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='团队分类';


-- 客户信息表[C端]
CREATE TABLE `mc_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态，0失效/不能登录，1正常',
  `uname` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名称',
  `gender` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号',
  `lgpwd` varchar(33) NOT NULL DEFAULT '',
  `lgstat` varchar(10) NOT NULL DEFAULT '',
  `head_pic` varchar(128) NOT NULL DEFAULT '' COMMENT '头像',
  `qiniu_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未上传，1未下载，2本地和七牛都有备份',
  `jpush_tag` varchar(32) NOT NULL DEFAULT '' COMMENT 'jpush 推送的标识',
  `im_token` varchar(130) NOT NULL DEFAULT '' COMMENT 'IM TOken',
  `loginip` varchar(32) NOT NULL DEFAULT '',
  `logintime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `remark` varchar(225) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uptime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='客户信息表[C端]';


-- app token [redis]
create table mc_app_tokens(
  id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  user_id int unsigned NOT NULL DEFAULT 0 comment '',
  user_type tinyint not null default 1 comment '1-B端，2-C端',
  api_token VARCHAR(33) not null default '' comment '',
  token_expiry datetime NOT NULL DEFAULT 0  comment '过期时间',
  uptime datetime NOT NULL DEFAULT 0 ,
  PRIMARY KEY (id),
  key(user_id),
  key(user_type),
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='app token';

-- 项目主表
CREATE TABLE `mc_projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '项目名称',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '项目状态',
  `address` varchar(128) NOT NULL DEFAULT '' COMMENT '项目地址',
  `acreage` varchar(16) NOT NULL DEFAULT '0' COMMENT '面积',
  `house_type` varchar(32) NOT NULL DEFAULT '' COMMENT '户型',
  `decoration_style` varchar(32) NOT NULL DEFAULT '' COMMENT '装修风格',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '项目范围:1施工+设计 2仅施工 3仅设计',
  `customer_manager_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '客户经理-B端',
  `customer_manager_role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '客户经理-B端权限角色',
  `desgin_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目负责设计师-B端',
  `desgin_role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目负责设计师-B端权限角色',
  `desgin_assistant_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目负责设计师助理-B端',
  `desgin_assistant_role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目负责设计师助理-B端权限角色',
  `manager_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目负责项目经理-B端',
  `manager_role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目负责项目经理-B端权限角色',
  `supervision_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目负责监理,项目质检-B端',
  `supervision_role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目负责监理,项目质检-B端权限角色',
  `decorate_butler_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '装修管家-B端',
  `decorate_butler_role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '装修管家-B端权限角色',
  `owner_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '业主id-C端',
  `owner_name` varchar(32) NOT NULL DEFAULT '' COMMENT '业主名称',
  `owner_mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '业主电话',
  `owner_address` varchar(32) NOT NULL DEFAULT '' COMMENT '联系地址',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uptime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `desgin_user_id` (`desgin_user_id`),
  KEY `manager_user_id` (`manager_user_id`),
  KEY `supervision_user_id` (`supervision_user_id`),
  KEY `owner_user_id` (`owner_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目主表';

CREATE TABLE `mc_project_remarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目iD',
  `p_remarks` text COMMENT '项目备注-图文内容',
  `owner_remarks` text COMMENT '业主备注信息-图文内容',
  `uptime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目主表';

create table mc_project_admin(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  p_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目iD',
  type tinyint not null default 0 comment '负责事项：1项目经理，2客户经理，3设计师，4设计师助理，5项目监理质检，6装修管家',
  b_user_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'B端user_id',
  uptime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY key(id),
  key(p_id),
  key(b_user_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目与负责人对应表';

-- 项目付款信息
CREATE TABLE `mc_project_pay_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `p_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1设计，2施工',
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '分期名',
  `payable` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payable_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `paied` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paied_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目付款信息';


-- 项目验收报告

CREATE TABLE `mc_project_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未确认，1设计确认，2项目经理确认，3业主确认，4业主修改',
  `sign_img` varchar(128) NOT NULL DEFAULT '',
  `remark` varchar(225) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `passtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checktime1` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checktime2` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='项目验收报告';

-- 项目验收报告文档
CREATE TABLE `mc_project_report_docs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `p_rep_id` int(10) unsigned NOT NULL DEFAULT '0',
  `file_type` varchar(6) NOT NULL DEFAULT '' COMMENT '文档类型',
  `file_name` varchar(128) NOT NULL DEFAULT '' COMMENT '文档名称',
  `file_path` varchar(128) NOT NULL DEFAULT '' COMMENT '文档地址',
  `file_hash` varchar(128) NOT NULL DEFAULT '',
  `qiniu_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未上传，1未下载，2本地和七牛都有备份',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`),
  KEY `p_rep_id` (`p_rep_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='项目验收报告文档';

-- 项目验收报告修改

CREATE TABLE `mc_project_report_modify` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `p_rep_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '提出修改的人(1设计师，2项目经理，3业主)',
  `content` text NOT NULL,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`),
  KEY `p_rep_id` (`p_rep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目验收报告修改';


-- 项目施工预算

CREATE TABLE `mc_project_offer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未确认，1设计确认，2项目经理确认，3业主确认，4业主修改',
  `sign_img` varchar(128) NOT NULL DEFAULT '',
  `remark` varchar(225) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `passtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' comment '业主确认时间',
  `checktime1` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' comment '设计确认时间',
  `checktime2` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' comment '项目经理确认时间',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目施工预算';

-- 项目施工预算文档
CREATE TABLE `mc_project_offer_docs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `p_offer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `file_type` varchar(6) NOT NULL DEFAULT '' COMMENT '文档类型',
  `file_name` varchar(128) NOT NULL DEFAULT '' COMMENT '文档名称',
  `file_path` varchar(128) NOT NULL DEFAULT '' COMMENT '文档地址',
  `file_hash` varchar(128) NOT NULL DEFAULT '',
  `qiniu_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未上传，1未下载，2本地和七牛都有备份',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`),
  KEY `p_offer_id` (`p_offer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目施工预算文档';

-- 项目施工预算修改

CREATE TABLE `mc_project_offer_modify` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `p_offer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '提出修改的人(1设计师，2项目经理，3业主)',
  `content` text NOT NULL,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`),
  KEY `p_offer_id` (`p_offer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目施工预算修改';

-- 项目设计


-- 各阶段的提交客户确认的内容

-- 项目的阶段配置信息
create table mc_step_configs(
  id int unsigned NOT NULL AUTO_INCREMENT,
  pid int unsigned NOT NULL DEFAULT 0,
  name VARCHAR (32) not null DEFAULT '',
  level tinyint not null default 0,
  sort tinyint unsigned not null default 0,
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY  KEY (id),
  key(pid)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目的阶段配置信息';

-- 主阶段表
CREATE TABLE `mc_p_steps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父节点id',
  `level` tinyint(4) NOT NULL DEFAULT '1',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1设计，2施工，3验收',
  `name` varchar(32) NOT NULL DEFAULT '',
  `step_tag` tinyint(4) NOT NULL DEFAULT '1' COMMENT '阶段标记',
  `step_sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '阶段排序',
  `plan_time` varchar(64) NOT NULL DEFAULT '' COMMENT '计划时间',
  `realtime` varchar(64) NOT NULL DEFAULT '' COMMENT '实际时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态：0未开始，1进行中，2待客户确认，3已驳回,4已完成',
  `other_desc` varchar(225) NOT NULL DEFAULT '' COMMENT '其他说明',
  `c_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'C端用户id',
  `b_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'B端用户iD',
  `b_user_name` varchar(32) NOT NULL DEFAULT '' COMMENT 'B端用户名称',
  `b_user_mesg` varchar(225) NOT NULL DEFAULT '' COMMENT 'B端用户留言',
  `b_upload_desc` varchar(225) NOT NULL DEFAULT '' COMMENT '上传说明',
  `b_other_desc` varchar(225) NOT NULL DEFAULT '' COMMENT '其他说明',
  `reject_reason` varchar(225) NOT NULL DEFAULT '' COMMENT '驳回理由',
  `reject_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '驳回时间',
  `pass_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '客户通过时间',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主阶段表';

-- 修改意见

create table mc_step_modify(
id int unsigned NOT NULL AUTO_INCREMENT,
p_id int unsigned NOT NULL DEFAULT 0,
p_step_id int unsigned NOT NULL DEFAULT 0,
c_user_id int unsigned NOT NULL DEFAULT 0,
img VARCHAR (225) not null default '',
content text not null,
addtime datetime NOT NULL DEFAULT 0 ,
PRIMARY KEY (id),
  key(p_step_id)

)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='阶段修改意见';


-- 阶段中的文档内容
create table mc_p_sub_step_docs(
  id int unsigned NOT NULL AUTO_INCREMENT,
  p_id int unsigned NOT NULL DEFAULT 0,
  p_step_id int unsigned NOT NULL DEFAULT 0,
  file_type tinyint not null default 0 comment '文档类型',
  file_name VARCHAR (128) not null default '' comment '文档名称',
  file_path VARCHAR (128) not null default '' comment '文档地址',
  qiniu_status tinyint not null default 0 comment '0未上传，1未下载，2本地和七牛都有备份',
  addtime datetime NOT NULL DEFAULT 0 ,
  PRIMARY KEY (id),
  key(p_step_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='阶段中的文档内容';

-- 项目操作日志
create table mc_p_oper_log(
  id int unsigned NOT NULL AUTO_INCREMENT,
  p_id int unsigned NOT NULL DEFAULT 0,
  p_step_id int unsigned NOT NULL DEFAULT 0,
  p_step_type tinyint NOT NULL DEFAULT 0,
  user_type tinyint not null default 1 comment '1-B端，2-C端',
  oper_user_id int unsigned NOT NULL DEFAULT 0,
  oper_user_name VARCHAR (32) not null default '' comment '操作人信息',
  oper_desc VARCHAR (225) not null default '' comment '操作事项',
  addtime datetime NOT NULL DEFAULT 0 ,
  PRIMARY KEY (id),
  key(p_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目操作日志';


-- IM 列表
CREATE TABLE `mc_im_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-B端，2-C端',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `im_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1单聊，2群聊，3系统',
  `target_tag` varchar(32) NOT NULL DEFAULT '' COMMENT '1单聊-对方IM-user_id，2群聊-群id，3系统-系统账户IM-id',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '1单聊-对方名称，2群聊-群名称，3系统-固定的名称',
  `has_new` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有新消息：0否，1是',
  `uptime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `user_type` (`user_type`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='IM 列表';

CREATE TABLE `mc_project_im` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `im_userid` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `icon` varchar(128) NOT NULL DEFAULT '',
  `token` varchar(33) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目IM 信息';


-- 文章分类表

CREATE TABLE `mc_article_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上层分类id',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '等级',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章分类表';

-- 文章主表
CREATE TABLE `mc_articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  --`all_site` tinyint(4) NOT NULL DEFAULT '0' COMMENT '显示城市：0所有，1指定',
  `author` varchar(16) NOT NULL DEFAULT '' COMMENT '作者',

  `view_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数',
  --`comment_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `title` varchar(225) NOT NULL DEFAULT '' COMMENT '标题',
  `proveid` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '省',
  `prove` varchar(16) NOT NULL DEFAULT '',
  `cityid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '市',
  `city` varchar(16) NOT NULL DEFAULT '',
  `isrecmd` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '推荐，0未推荐，1推荐',
  `reid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推荐ID',
  `summary` varchar(225) NOT NULL DEFAULT '' COMMENT '摘要',
  `coverimg` varchar(128) NOT NULL DEFAULT '' COMMENT '封面',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发布状态,0未发布，1已发布',
  --`recmd` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '推荐状态：1推荐 2不推荐',
  `tag` varchar(50) NOT NULL DEFAULT '' COMMENT '标签',
  `addtime`  datetime NOT NULL DEFAULT 0  COMMENT '创建时间',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `acid` (`acid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='文章主表';

--文章内容表

CREATE TABLE `mc_article_conts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `artid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章Id',
  `content` blob NOT NULL COMMENT '文章内容',
  PRIMARY KEY (`id`),
  KEY `artid` (`artid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章内容表';


-- 客户端权限节点
create table mc_app_power_nodes(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  ios_name VARCHAR(64) not null default '' comment 'ios上的节点名称',
  android_name VARCHAR(64) not null default '' comment '安卓上的节点名称',
  remark VARCHAR(225) not null DEFAULT '' comment '备注',
  uptime datetime NOT NULL DEFAULT 0  COMMENT '更新时间',
  PRIMARY KEY(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户端权限节点';

-- 客户端角色
CREATE TABLE `mc_app_power_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `remark` varchar(128) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='客户端角色';

create table mc_app_power_role_nodes(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  role_id int(10) unsigned NOT NULL DEFAULT 0,
  node_id int(10) unsigned NOT NULL DEFAULT 0,
  addtime datetime NOT NULL DEFAULT 0  ,
  PRIMARY key(id),
  key(node_id)
)ENGINE=MyIsam DEFAULT CHARSET=utf8 COMMENT='客户端角色与操作';

-- 短信记录
create table mc_sms_record(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  mobile VARCHAR(225) not null DEFAULT '' comment '备注',
  content VARCHAR(225) not null DEFAULT '' comment '备注',
  submit_time datetime NOT NULL DEFAULT 0  COMMENT '提交时间',
  returnstatus VARCHAR(64) not null default '' comment '返回状态值：成功返回Success 失败返回：Faild',
  message VARCHAR(64) not null default '' comment '返回信息',
  remainpoint int(10) unsigned NOT NULL DEFAULT 0 comment '返回余额',
  taskID  int(10) unsigned NOT NULL DEFAULT 0 comment '返回本次任务的序列ID',
  successCounts smallint unsigned not null default 0 comment '成功短信数：当成功后返回提交成功短信数',
  PRIMARY key (id)
)ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='短信记录';


-- 建议反馈
CREATE TABLE `mc_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1装修问题，2软件问题',
  `content` varchar(225) NOT NULL DEFAULT '' COMMENT '反馈内容',
  `img` varchar(128) NOT NULL DEFAULT '' COMMENT '图片',
  `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '联系电话',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `do_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `do_user_name` varchar(32) NOT NULL DEFAULT '',
  `do_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '处理状态：0未处理，1已处理',
  `do_remark` varchar(225) NOT NULL DEFAULT '' COMMENT '处理备注',
  `do_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `do_user_id` (`do_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='建议反馈';

-- 咨询
CREATE TABLE `mc_consultation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '联系电话',
  `content` varchar(225) NOT NULL DEFAULT '' COMMENT '咨询内容',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `do_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `do_user_name` varchar(32) NOT NULL DEFAULT '',
  `do_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '处理状态：0未处理，1已处理',
  `do_remark` varchar(225) NOT NULL DEFAULT '' COMMENT '处理备注',
  `do_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `do_user_id` (`do_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='咨询';


-- 预约
create table mc_booking(
  id int unsigned NOT NULL AUTO_INCREMENT,
  p_id int unsigned NOT NULL DEFAULT 0,
  booking_user_id int unsigned NOT NULL DEFAULT 0 comment '发起预约的人-C端',
  to_user_id int unsigned NOT NULL DEFAULT 0  comment '被预约的人-B端',
  booking_time datetime NOT NULL DEFAULT 0,
  booking_content VARCHAR(225) not null DEFAULT '' comment '预约事项',
  addtime datetime NOT NULL DEFAULT 0,
  do_user_id int unsigned NOT NULL DEFAULT 0,
  `do_user_name` varchar(32) NOT NULL DEFAULT '',
  do_status  tinyint NOT NULL DEFAULT 0 comment '处理状态：0未处理，1已处理',
  do_remark VARCHAR(225) not null DEFAULT '' comment '处理备注',
  do_time datetime NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  key(p_id),
  key(booking_user_id),
  key(to_user_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预约';


-- 后台管理人员操作日志
create table mc_admin_oper_log(
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL DEFAULT 0,
  user_name VARCHAR (32) not null default '' comment '操作人信息',
  content VARCHAR (128) not null default '' comment '操作事项',
  addtime datetime NOT NULL DEFAULT 0 ,
  PRIMARY KEY (id),
  key(user_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理人员操作日志';

-- 分公司信息
create table mc_admin_company(
  id int unsigned NOT NULL AUTO_INCREMENT,
  name VARCHAR (32) not null default ''
  power_tag tinyint NOT NULL DEFAULT 1 comment '权限标志，0总部权限，1分权限',
  PRIMARY KEY (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分公司信息';


CREATE TABLE `mc_recommend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `retype` tinyint NOT NULL DEFAULT 1 COMMENT '推荐类型，1动态，2静态',
  `pos` tinyint NOT NULL DEFAULT '1' COMMENT '展示的位置，1首页，2备用',
  `stable` varchar(16) NOT NULL DEFAULT '' COMMENT '推荐资源所在的表',
  `sid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源的id',
  `type` tinyint NOT NULL DEFAULT 1 COMMENT '内容类型，1图片，2图文,3文字',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '静态类型-标题',
  `pic` varchar(128) NOT NULL DEFAULT '' COMMENT '静态类型-logo',
  `url` varchar(128) NOT NULL DEFAULT '' COMMENT '静态类型-链接',
  `sort` tinyint(4) NOT NULL DEFAULT '0',
  `betime` datetime NOT NULL DEFAULT 0 , COMMENT '开始时间',
  `entime` datetime NOT NULL DEFAULT 0 , COMMENT '结束时间',
  `adtime` datetime NOT NULL DEFAULT 0 , COMMENT '添加时间',
  `isdel` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `retype` (`retype`),
  KEY `pos` (`pos`),
  KEY `sid` (`sid`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='推荐表';


----- 案例相关表

CREATE TABLE `mc_cases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `huxing` varchar(64) NOT NULL DEFAULT '',
  `mianji` varchar(64) NOT NULL DEFAULT '',
  `feige` varchar(64) NOT NULL DEFAULT '',
  `seijishi` varchar(64) NOT NULL DEFAULT '',
  `jingli` varchar(64) NOT NULL DEFAULT '',
  `jianli` varchar(64) NOT NULL DEFAULT '',
  `step_json` varchar(225) NOT NULL DEFAULT '',
  `step_img` varchar(128) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='案例主表';

-- 案例子内容
CREATE TABLE mc_case_steps(
id int(10) unsigned NOT NULL AUTO_INCREMENT,
case_id int(10) unsigned NOT NULL DEFAULT 0,
title VARCHAR(128) not null default '',
summary VARCHAR(225) not null default '',
content text not null,
addtime datetime NOT NULL DEFAULT 0 ,
PRIMARY key(id),
key(case_id)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='案例子内容表';

-- 案例子内容中的图片
CREATE TABLE mc_case_step_imgs(
id int(10) unsigned NOT NULL AUTO_INCREMENT,
case_id int(10) unsigned NOT NULL DEFAULT 0,
case_step_id int(10) unsigned NOT NULL DEFAULT 0,
img_path VARCHAR(128) not null default '',
addtime datetime NOT NULL DEFAULT 0 ,
PRIMARY key(id),
key(case_id),
key(case_step_id)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='案例子内容中的图片表';


-- 项目静态文件

CREATE TABLE `mc_project_static_docs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint unsigned NOT NULL DEFAULT '0' comment '类型：1效果图，2CAD图，3主材',
  `file_type` varchar(6) NOT NULL DEFAULT '' COMMENT '文档类型',
  `file_name` varchar(128) NOT NULL DEFAULT '' COMMENT '文档名称',
  `file_path` varchar(128) NOT NULL DEFAULT '' COMMENT '文档地址',
  `file_hash` varchar(128) NOT NULL DEFAULT '',
  `qiniu_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未上传，1未下载，2本地和七牛都有备份',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目静态文件';


-- 主材商城

create table mc_goods_cates(
id int(10) unsigned NOT NULL AUTO_INCREMENT,
name varchar(16) NOT NULL DEFAULT '',
sort tinyint not null default 0,
addtime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类';

create table mc_goods(
id int(10) unsigned NOT NULL AUTO_INCREMENT,
cate_id int(10) unsigned NOT NULL DEFAULT '0',
name VARCHAR(128) not null default '',
coverimg VARCHAR(128) not null default '',
uptime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
addtime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
isdel tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
PRIMARY key(id),
key(cate_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品';

create table mc_goods_content(
id int(10) unsigned NOT NULL AUTO_INCREMENT,
g_id int(10) unsigned NOT NULL DEFAULT '0',
content text not null,
uptime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY key(id),
key(g_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品详情内容';


create table mc_goods_imgs(
id int(10) unsigned NOT NULL AUTO_INCREMENT,
g_id int(10) unsigned NOT NULL DEFAULT '0',
sort tinyint(4) NOT NULL DEFAULT '0',
`file_name` varchar(128) NOT NULL DEFAULT '' COMMENT '文档名称',
  `file_path` varchar(128) NOT NULL DEFAULT '' COMMENT '文档地址',
  `file_hash` varchar(128) NOT NULL DEFAULT '',
  `qiniu_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未上传，1未下载，2本地和七牛都有备份',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY keY(id),
key(g_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品图片';


-- 事务提醒
create table mc_notices(
id int(10) unsigned NOT NULL AUTO_INCREMENT,
p_id int(10) unsigned NOT NULL DEFAULT '0',
type tinyint(4) NOT NULL DEFAULT '0' comment '1验收，2付款，3预约',
target_id int(10) unsigned NOT NULL DEFAULT '0',
user_type tinyint not null default 1 comment '1-B端，2-C端',
user_id int(10) unsigned NOT NULL DEFAULT '0',
status tinyint(4) NOT NULL DEFAULT '0' comment '0待处理，1已处理',
title varchar(64) NOT NULL DEFAULT '',
content varchar(225) NOT NULL DEFAULT '',
addtime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
donetime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
primary key(id),
key(p_id),
key(user_id)

)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='事务提醒';

-- 问答测试
create table mc_test_qa(
 id int(10) unsigned NOT NULL AUTO_INCREMENT,
 pageid varchar(32) NOT NULL DEFAULT '',
  uname  varchar(32) NOT NULL DEFAULT '',
  mobile  varchar(12) NOT NULL DEFAULT '',
  answer1  varchar(2) NOT NULL DEFAULT '',
  answer2 varchar(2) NOT NULL DEFAULT '',
  answer3 varchar(2) NOT NULL DEFAULT '',
  answer4 varchar(2) NOT NULL DEFAULT '',
  answer5 varchar(2) NOT NULL DEFAULT '',
  addtime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  primary key(id)

)engine=innodb default charset =utf8 comment='问答测试';


-- 项目展示信息
create table mc_project_static(
 id int(10) unsigned NOT NULL AUTO_INCREMENT,
 p_id int(10) unsigned NOT NULL DEFAULT '0',
 name  varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
 type  tinyint unsigned NOT NULL DEFAULT '0'  COMMENT '类型：1效果图，2CAD图，3主材',
 status  tinyint unsigned NOT NULL DEFAULT '0' COMMENT '状态：0待客户确认，1已驳回,2确认通过',
 `sign_img` varchar(225) NOT NULL DEFAULT '',
  `remark` varchar(225) NOT NULL DEFAULT '',
 `reject_reason` varchar(225) NOT NULL DEFAULT '' COMMENT '驳回理由',
  `reject_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '驳回时间',
  `pass_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '客户通过时间',
  `uptime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`)
)engine=innodb default charset =utf8 comment='项目展示信息';


-- 项目展示信息修改信息
create table mc_project_static_modify(
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `p_static_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '提出修改的人(1设计师，2项目经理，3业主)',
  `content` text NOT NULL,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`),
  KEY `p_static_id` (`p_static_id`)
)engine=innodb default charset =utf8 comment='项目展示信息修改信息';

-- 自定义消息提醒

CREATE TABLE `mc_push` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目Id',
  `p_name` varchar(128) NOT NULL DEFAULT '' COMMENT '项目名称',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0自定义 1项目阶段 2预算 3付款 4验收 5图纸',
  `type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'type中对应的id',
  `geter_users` varchar(128) NOT NULL DEFAULT '' COMMENT '接收者的名称',
  `geter_user_ids` varchar(128) NOT NULL DEFAULT '' COMMENT '如：c_213,b_33434',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '消息标题',
  `message` varchar(225) NOT NULL DEFAULT '' COMMENT '消息内容',
  `metas` varchar(128) NOT NULL DEFAULT '' COMMENT '跳转数据字符串',
  `run_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1立即执行，2单次定时，3多次定时',
  `once_run_time_option` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '单次定时，时间设置项',
  `begin_time` date NOT NULL DEFAULT '0000-00-00' COMMENT '多次定时任务范围-起始时间',
  `end_time` date NOT NULL DEFAULT '0000-00-00' COMMENT '多次定时任务范围-结束时间',
  `run_rate_day` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '运行频率，多少天一次',
  `run_rate_time` varchar(8) NOT NULL DEFAULT '' COMMENT '每次运行的时间',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uptime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自定义消息提醒';

-- 推送运行列表

CREATE TABLE `mc_push_runtime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `not_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消息提醒 Id',
  `jpush_user_id` varchar(16) NOT NULL DEFAULT '' COMMENT '如：c_213,b_33434',
  `message` varchar(128) NOT NULL DEFAULT '' COMMENT '消息标题',
  `metas` varchar(128) NOT NULL DEFAULT '' COMMENT '跳转数据字符串',
  `runtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `donetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `result` varchar(225) NOT NULL DEFAULT '' COMMENT '跳转数据字符串',
  PRIMARY KEY (`id`),
  KEY `not_id` (`not_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='推送运行列表';

-- 采购信息
create table mc_purchase(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  p_id int(10) unsigned NOT NULL DEFAULT '0',
  name varchar(128) NOT NULL DEFAULT '',
  status tinyint NOT NULL DEFAULT '0' comment '0待确认，1客户已确认',
  `remark` varchar(225) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `passtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pushid` int(11) NOT NULL DEFAULT '0',
 `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
 PRIMARY key(id),
 key(p_id)

)ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='采购信息';


CREATE TABLE `mc_purchase_modify` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pu_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '提出修改的人(1业主)',
  `content` text NOT NULL,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `p_id` (`p_id`),
  KEY `pu_id` (`pu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='采购信息修改';

create table mc_purchase_docs(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  pu_id int(10) unsigned NOT NULL DEFAULT '0',
  `file_type` varchar(6) NOT NULL DEFAULT '' COMMENT '文档类型',
  `file_name` varchar(128) NOT NULL DEFAULT '' COMMENT '文档名称',
  `file_path` varchar(128) NOT NULL DEFAULT '' COMMENT '文档地址',
  `file_hash` varchar(128) NOT NULL DEFAULT '',
  `sign_complex_path` varchar(255) NOT NULL DEFAULT '',
  `qiniu_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未上传，1未下载，2本地和七牛都有备份',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
 PRIMARY key(id),
 key(pu_id)

)ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='采购信息';

-- IM 群
CREATE TABLE `mc_im_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` int(11) NOT NULL DEFAULT '0',
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `tname` varchar(128) NOT NULL DEFAULT '' COMMENT '群名称',
  `icon` varchar(225) NOT NULL DEFAULT '' COMMENT '群头像',
  `muteType` varchar(16) NOT NULL DEFAULT '',
  `mute` varchar(32) NOT NULL DEFAULT '',
  `beinvitemode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '被邀请人同意方式，0-需要同意(默认),1-不需要同意',
  `joinmode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '群建好后，sdk操作时，0不用验证，1需要验证,2不允许任何人加入',
  `invitemode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '谁可以邀请他人入群，0-管理员(默认),1-所有人',
  `uptinfomode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '谁可以修改群资料，0-管理员(默认),1-所有人',
  `upcustommode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '谁可以更新群自定义属性，0-管理员(默认),1-所有人',
  `size` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `maxusers` tinyint(3) unsigned NOT NULL DEFAULT '200' COMMENT '群主ID',
  `owner` varchar(16) NOT NULL DEFAULT '' COMMENT '群主ID',
  `announcement` varchar(255) NOT NULL DEFAULT '' COMMENT '群公告',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '群描述',
  `members` varchar(255) NOT NULL DEFAULT '' COMMENT '群成功ID 字串',
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='IM 群';


-- 资讯推送

CREATE TABLE `mc_push_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资讯Id',
  `geter_users` varchar(128) NOT NULL DEFAULT '' COMMENT '接收者的名称',
  `geter_user_ids` varchar(128) NOT NULL DEFAULT '' COMMENT 'all,all_c,all_b',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '消息标题',
  `message` varchar(225) NOT NULL DEFAULT '' COMMENT '消息内容',
  `metas` varchar(128) NOT NULL DEFAULT '' COMMENT '跳转数据字符串',
  `run_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1立即执行，2定时执行',
  `run_time` date NOT NULL DEFAULT '0000-00-00' COMMENT '定时执行的时间',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uptime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isdel` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资讯推送';


update mc_p_steps set plan_time1 = left(plan_time,10),plan_time2=right(plan_time,10),realtime1=left(realtime,10),realtime2=left(realtime,10) where 1=1;