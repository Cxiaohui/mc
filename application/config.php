<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [
        'extend'=>'../extend/'
    ],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => true,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => ['log', 'error', 'notice', 'alert', 'debug'],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    //本地缓存设置
    /*'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],*/
    //线上缓存设置
    'cache'                  => [
        // 驱动方式
        'type'   => 'Redis',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'dfmc',
        // 驱动方式 支持redis memcache memcached
        'type'           => 'redis',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => 'sfezx',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    'company'=>[
        'full_name'=>'深圳市莫川建筑空间设计有限公司',
        'short_name'=>'莫川设计',
        'web_name'=>'莫川设计'
    ],
    //微信配置
    'wx'=>[
        'token'=>'',
        'encodingaeskey'=>'',
        'appid'=>'',
        'appsecret'=>'',
    ],
    'wx_pay'=>[
        'mch_id'=>'',
        'api_key'=>'',
        'sdk_dir'=>APP_PATH.'',
    ],
    'image_dirs'=>[
        0=>'',
        1=>'users/',//会员头像目录
        2=>'cusers/',//C端用户头像目录
        3=>'admins/',//后台用户头像目录
        4=>'artcover/',//文章封面
        5=>'recomd/',//推荐的展示图片
        6=>'groups/',//群封面图
        7=>'cases/',//案例图片
        8=>'cgoods/',
        9=>'diarys/'
    ],
    'img_size'=>[
        's1'=>[750,320],
        's2'=>[710,280],
        's3'=>[710,248],
        's4'=>[626,348]
    ],
    'cache_key'=>[
        'api_log_token'=>'apiLoginToken:',
        'api_team'=>'mc_teams',
        'mobile_verify_code'=>'mobile_verify_code:',
        'qiniu_uptoken'=>'qiniu_uptoken'
    ],
    'img_ext'=>['jpg','jpeg','png','gif'],
    'f_city_ids'=>[2,3,4,5],
    //验证码图片配置
    'captcha'=>[
        'length'=>4,
        'useNoise'=>false
    ],
    'sms_mc'=>[
        'userid'=>'192',
        'account'=>'Mokchuen',
        'pwd'=>'mc20150106',
        'sign'=>'【莫川】'
    ],
    'md_sms'=>[
        'account_sid'=>'cf786c4ed885470aaefc069938d09d76',
        'auth_token'=>'4f623c2eca524990b223496a8b119ad3',
        'sign'=>'【莫川】',
        'verify_tpl_id'=>331236034,//验证码短信模板id
        'verify_time'=>'15'//15分种
    ],
    'jpush'=>[
        'apns_production'=>false,
        'AppKey'=>'bdd37be781169b51d3b2fa47',
        'Secret'=>'56b431efc4a3c9768e8a7054',
        'log_file'=>LOG_PATH.DS.'jpush.log'
    ],
    'jpush_b'=>[
        'apns_production'=>false,
        'AppKey'=>'4c4d20022b6ad23b8fc86b74',
        'Secret'=>'9d7a2746b76987b60c525623',
        'log_file'=>LOG_PATH.DS.'jpush_b.log'
    ],
    'qiniu'=>[
        'host'=>'http://content.iytime.com/',
        'file_key_prefix'=>'mcdocs-',
        'AccessKey'=>'5NIES5Sut-V3VD0SH-y0ZHjNQQiIJ-CzBGu0sgDK',
        'SecretKey'=>'UgsbJ1V7g3jJPg92FuaPFNBkayPct_XppE7kfOVV',
        'bucket1'=>'mc-docs'
    ],
    'yunxin'=>[
        'AccessKey'=>'cdcabfab10b64364b39753bf027a0c7f',
        'SecretKey'=>'3c6d42c199c94f4fb0b9fd7c17763a2c',
        'im_app_key'=>'5a566e899102f9c937fa1d3b36b98d31',
        'im_app_ecret'=>'c1591cc44408'
    ],
    //版本
    'ver'=>'1.0.5',
    'host'=>''
];
