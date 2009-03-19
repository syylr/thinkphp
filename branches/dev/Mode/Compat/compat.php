<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

define('DATA_TYPE_OBJ',1);
define('DATA_TYPE_ARRAY',0);

$_convention  =  array(

	/* Dispatch设置 */
	'DISPATCH_ON'				=>	true,	// 是否启用Dispatcher
	'DISPATCH_NAME'			=>	'Think',	// 默认的Dispatcher名称
	// URL模式： 0 普通模式 1 PATHINFO 2 REWRITE 3 兼容模式
	'URL_MODEL'					=>	1,		// 默认为PATHINFO 模式，提供最好的用户体验和SEO支持
	// PATHINFO 模式
	// 普通模式1 参数没有顺序/m/module/a/action/id/1
	// 智能模式2 自动识别模块和操作/module/action/id/1/ 或者 /module,action,id,1/...
	// 兼容模式3 通过一个GET变量将PATHINFO传递给dispather，默认为s index.php?s=/module/action/id/1
	'PATH_MODEL'					=>	2,	// 默认采用智能模式
	'PATH_DEPR'					=>	'/',	// PATHINFO参数之间分割号
	'ROUTER_ON'					=>	true,	// 启用路由判断
	'COMPONENT_DEPR'			=>	'@',		// 组件模块和操作的URL分割符
	'COMPONENT_TYPE'			=>	1,	 //组件目录结构  1 Lib\User\Action\ 2 Lib\Action\User\
    'URL_CASE_INSENSITIVE'  =>   false, // URL是否不区分大小写
    'URL_AUTO_REDIRECT'     =>   false, // URL 是否开启自动重定向 当使用PATHINFO模式的时候传入GET参数可以支持自动重定向
    'CHECK_FILE_CASE'  =>   false, // 是否检查文件的大小写 对Windows平台有效

	/* 日志设置 */
	'WEB_LOG_RECORD'			=>	false,	 // 默认不记录日志
    'LOG_RECORD_LEVEL'       =>   array('EMERG','ALERT','CRIT','ERR'),  // 允许记录的日志级别
	'LOG_FILE_SIZE'				=>	2097152,	// 日志文件大小限制

	/* 插件设置 */
	'THINK_PLUGIN_ON'			=>	false,	// 默认启用插件机制
    'APP_AUTO_SETUP'           =>   false, // 是否启动自动安装支持

	/* 防刷新设置 */
	'LIMIT_RESFLESH_ON'		=>	false,	// 默认关闭防刷新机制
	'LIMIT_REFLESH_TIMES'	=>	3,	// 页面防刷新时间 默认3秒

	/* 错误设置 */
	'DEBUG_MODE'				=>	false,	 // 调试模式默认关闭
	'ERROR_MESSAGE'			=>	'您浏览的页面暂时发生了错误！请稍后再试～',	// 错误显示信息 非调试模式有效
	'ERROR_PAGE'					=>	'',	// 错误定向页面
    'SHOW_ERROR_MSG'        =>   true,

	/* 系统变量设置 */
	'VAR_PATHINFO'				=>	's',	// PATHINFO 兼容模式获取变量例如 ?s=/module/action/id/1 后面的参数取决于PATH_MODEL 和 PATH_DEPR
	'VAR_MODULE'					=>	'm',		// 默认模块获取变量
	'VAR_ACTION'					=>	'a',		// 默认操作获取变量
	'VAR_ROUTER'					=>	'r',		// 默认路由获取变量
	'VAR_FILE'						=>	'f',		// 默认文件变量
	'VAR_PAGE'						=>	'p',		// 默认分页跳转变量
	'VAR_LANGUAGE'				=>	'l',		// 默认语言切换变量
	'VAR_TEMPLATE'				=>	't',		// 默认模板切换变量
	'VAR_AJAX_SUBMIT'			=>	'ajax', // 默认的AJAX提交变量
	'VAR_RESFLESH'				=>	'h', //是否强制刷新,用来忽略防刷新设置,例如验证码

	/* 模块和操作设置 */
	'DEFAULT_MODULE'			=>	'Index', // 默认模块名称
	'DEFAULT_ACTION'			=>	'index', // 默认操作名称
    'MODULE_REDIRECT'        =>   '',  // 模块伪装
    'ACTION_REDIRECT'         =>   '', //  操作伪装

	/* 模板设置 */
	'TMPL_CACHE_ON'			=>	true,		// 默认开启模板编译缓存 false 的话每次都重新编译模板
	'TMPL_CACHE_TIME'		=>	-1,		// 模板缓存有效期 -1 永久 单位为秒
	'TMPL_SWITCH_ON'			=>	true,	// 启用多模版支持
	'DEFAULT_TEMPLATE'		=>	'default',	// 默认模板名称
	'TEMPLATE_SUFFIX'			=>	'.html',	 // 默认模板文件后缀
	'CACHFILE_SUFFIX'			=>	'.php',	// 默认模板缓存后缀
	'TEMPLATE_CHARSET'		=>	'utf-8',	// 模板模板编码
	'OUTPUT_CHARSET'			=>	'utf-8',	// 默认输出编码
    'DEFAULT_LAYOUT'         =>    'Layout:index', // 默认布局模板文件
    'AUTO_DETECT_THEME'   =>   false, // 自动侦测模板主题

	/* 模型设置 */
	'CONTR_CLASS_PREFIX'	=>	'',     // 控制器前缀
	'CONTR_CLASS_SUFFIX'	=>	'Action',  // 控制器后缀
	'ACTION_PREFIX'				=>	'', // 操作方法前缀
	'ACTION_SUFFIX'				=>	'', // 操作方法后缀
	'MODEL_CLASS_PREFIX'		=>	'', // 模型前缀
	'MODEL_CLASS_SUFFIX'	=>	'Model',  // 模型后缀
    'AUTO_NAME_IDENTIFY'  =>    True, // 模型对应数据表名称智能识别 UserType => user_type
    'DEFAULT_MODEL_APP'     =>   '@',   // 默认模型类所在的项目名称 @ 表示当前项目

	/* 静态缓存设置 */
	'HTML_FILE_SUFFIX'			=>	'.shtml',	 // 默认静态文件后缀
	'HTML_CACHE_ON'			=>	false,		 // 默认关闭静态缓存
	'HTML_CACHE_TIME'		=>	60,		 // 静态缓存有效期
	'HTML_READ_TYPE'			=>	1,			// 静态缓存读取方式 0 readfile 1 redirect
	'HTML_URL_SUFFIX'			=>	'',	// 伪静态后缀设置

	/* 语言时区设置 */
	'LANG_SWITCH_ON'			=>	false,	 // 默认关闭多语言包功能
    'AUTO_DETECT_LANG'      =>   false,     // 自动侦测语言
    'LANG_CACHE_ON'           =>    false, // 默认关闭语言包的缓存 大型应用可以开启 按照模块的语言包来缓存
	'DEFAULT_LANGUAGE'		=>	'zh-cn',	 // 默认语言
	'TIME_ZONE'					=>	'PRC',		 // 默认时区

	/* 用户认证设置 */
	'USER_AUTH_ON'				=>	false,		// 默认不启用用户认证
	'USER_AUTH_TYPE'			=>	1,		// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'			=>	'authId',	// 用户认证SESSION标记
	'ADMIN_AUTH_KEY'			=>	'administrator',
	'USER_AUTH_MODEL'		=>   'User',	// 默认验证数据表模型
	'AUTH_PWD_ENCODER'		=>	'md5',	// 用户认证密码加密方式
	'USER_AUTH_PROVIDER'	=>	'DaoAuthentictionProvider',	 // 默认认证委托器
	'USER_AUTH_GATEWAY'	=>	'/Public/login',	// 默认认证网关
	'NOT_AUTH_MODULE'		=>	'Public',		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'=>	'',		// 默认需要认证模块
	'NOT_AUTH_ACTION'		=>'',		// 默认无需认证操作
	'REQUIRE_AUTH_ACTION'=>'',		// 默认需要认证操作
    'GUEST_AUTH_ON'          => false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'           =>    0,     // 游客的用户ID
	'RBAC_ERROR_PAGE'	        =>	'',		// RBAC认证没有权限的错误页面

	/* SESSION设置 */
	'SESSION_NAME'				=>	'ThinkID',		// 默认Session_name 如果需要不同项目共享SESSION 可以设置相同
	'SESSION_PATH'				=>	'',			// 采用默认的Session save path
	'SESSION_TYPE'				=>	'File',			// 默认Session类型 支持 DB 和 File
	'SESSION_EXPIRE'			=>	'300000',		// 默认Session有效期
	'SESSION_TABLE'				=>	'think_session',	// 数据库Session方式表名
	'SESSION_CALLBACK'		=>	'',			// 反序列化对象的回调方法

	/* 数据库设置 */
	'DB_CHARSET'					=>	'utf8',			// 数据库编码默认采用utf8
	'DB_DEPLOY_TYPE'			=>	0,			// 数据库部署方式 0 集中式（单一服务器） 1 分布式（主从服务器）
    'DB_RW_SEPARATE'         =>    false,
	'SQL_DEBUG_LOG'			=>	false,			// 记录SQL语句到日志文件
	'DB_FIELDS_CACHE'			=>	true,			// 缓存数据表字段信息
    'SQL_MODE'                    =>   '',          // SQL MODE 针对mysql
    'FIELDS_DEPR'                 =>   ',',   // 多字段查询的分隔符
    'TABLE_DESCRIBE_SQL'     =>   '',             //  取得数据表的字段信息的SQL语句
    'FETCH_TABLES_SQL'        =>  '',          //  获取数据库的表信息的SQL语句
    /*  下面的数据库配置参数是为Oracle提供 */
    'DB_TRIGGER_PREFIX'	=>	'tr_',   //触发器前缀，其后与表名一致
    'DB_SEQUENCE_PREFIX'	=>	'seq_',  //序列前缀，其后与表名一致
    'DB_CASE_LOWER' =>	true, //隐式参数，ORACLE返回数据集，键名大小写，默认强制为true小写，以适应TP Model类如count方法等

	/* 数据缓存设置 */
	'DATA_CACHE_TIME'		=>	-1,			// 数据缓存有效期
	'DATA_CACHE_COMPRESS'=>	false,		// 数据缓存是否压缩缓存
	'DATA_CACHE_CHECK'		=>	false,		// 数据缓存是否校验缓存
	'DATA_CACHE_TYPE'		=>	'File',		// 数据缓存类型 支持 File Db Apc Memcache Shmop Sqlite Xcache Apachenote Eaccelerator
	'DATA_CACHE_SUBDIR'		=>	false,		// 使用子目录缓存 （自动根据缓存标识的哈希创建子目录）
	'DATA_CACHE_TABLE'		=>	'think_cache',	// 数据缓存表 当使用数据库缓存方式时有效
	'CACHE_SERIAL_HEADER'	=>	"<?php\n//",	// 文件缓存开始标记
	'CACHE_SERIAL_FOOTER'	=>	"\n?".">",	// 文件缓存结束标记
	'SHARE_MEM_SIZE'			=>	1048576,		// 共享内存分配大小
	'ACTION_CACHE_ON'		=>	false,		// 默认关闭Action 缓存

	/* 运行时间设置 */
	'SHOW_RUN_TIME'			=>	false,			// 运行时间显示
	'SHOW_ADV_TIME'			=>	false,			// 显示详细的运行时间
	'SHOW_DB_TIMES'			=>	false,			// 显示数据库查询和写入次数
	'SHOW_CACHE_TIMES'		=>	false,		// 显示缓存操作次数
	'SHOW_USE_MEM'			=>	false,			// 显示内存开销
	'SHOW_PAGE_TRACE'		=>	false,		// 显示页面Trace信息 由Trace文件定义和Action操作赋值

	/* 模板引擎设置 */
	'TMPL_ENGINE_TYPE'		=>	'Think',		// 默认模板引擎 以下设置仅对使用Think模板引擎有效
	'TMPL_DENY_FUNC_LIST'	=>	'echo,exit',	// 模板引擎禁用函数
	'TMPL_L_DELIM'				=>	'{',			// 模板引擎普通标签开始标记
	'TMPL_R_DELIM'				=>	'}',			// 模板引擎普通标签结束标记
	'TAGLIB_BEGIN'				=>	'<',			// 标签库标签开始标记
	'TAGLIB_END'					=>	'>',			// 标签库标签结束标记
	'TAG_NESTED_LEVEL'		=>	3,				// 标签库

	/* Cookie设置 */
	'COOKIE_EXPIRE'				=>	3600,		// Coodie有效期
	'COOKIE_DOMAIN'			=>	'',	// Cookie有效域名
	'COOKIE_PATH'				=>	'/',			// Cookie路径
	'COOKIE_PREFIX'				=>	'', // Cookie前缀 避免冲突
    'COOKIE_SECRET_KEY'     =>   '',  // Cookie 加密Key

	/* 分页设置 */
	'PAGE_NUMBERS'				=>	5,			// 分页显示页数
	'LIST_NUMBERS'				=>	20,			// 分页每页显示记录数

	/* 数据格式设置 */
	'AJAX_RETURN_TYPE'		=>	'JSON', //AJAX 数据返回格式 JSON XML ...
	'DATA_RESULT_TYPE'		=>	0,	// 默认数据返回格式 1 对象 0 数组

	/* 其它设置 */
	'AUTO_LOAD_PATH'			=>	'Think.Util.',	//	 __autoLoad 的路径设置 当前项目的Model和Action类会自动加载，无需设置 注意搜索顺序
	'AUTO_LOAD_CLASS'		=>	'',		// 初始化需要导入的公共类 使用import的导入机制 例如 @.Action.CommonAction
	'CALLBACK_LOAD_PATH'	=>	'',				//	反序列化对象时自动加载的路径设置
	'UPLOAD_FILE_RULE'		=>	'uniqid',			//  文件上传命名规则 例如 time uniqid com_create_guid 等 支持自定义函数 仅适用于内置的UploadFile类
	'LIKE_MATCH_FIELDS'		=>	'', //数据库查询的时候需要进行模糊匹配的字段
	'ACTION_JUMP_TMPL'=>	'Public:success',    // 页面跳转的模板文件
	'ACTION_404_TMPL'=>	'Public:404',         // 404错误的模板文件
    'TOKEN_ON'                    =>   true,     // 开启令牌验证
    'TOKEN_NAME'                =>   'think_html_token',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'                 =>    'md5',   // 令牌验证哈希规则
    'APP_DOMAIN_DEPLOY'     =>  false,     // 是否使用独立域名部署项目
);
C($_convention);

function get_client_ip(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
       $ip = getenv("REMOTE_ADDR");
   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
       $ip = $_SERVER['REMOTE_ADDR'];
   else
       $ip = "unknown";
   return($ip);
}

/**
 +----------------------------------------------------------
 * URL组装 支持不同模式和路由
 +----------------------------------------------------------
 * @param string $action 操作名
 * @param string $module 模块名
 * @param string $app 项目名
 * @param string $route 路由名
 * @param array $params 其它URL参数
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function url($action=ACTION_NAME,$module=MODULE_NAME,$route='',$app=APP_NAME,$params=array()) {
    if(C('DISPATCH_ON') && C('URL_MODEL')>0) {
        $depr = C('PATH_MODEL')==2?C('PATH_DEPR'):'/';
        if(!empty($route)) {
            $url    =   str_replace(APP_NAME,$app,__APP__).'/'.$route.$depr.implode($depr,$params);
        }else{
            $str    =   $depr;
            foreach ($params as $var=>$val)
                $str .= $var.$depr.$val.$depr;
            $url    =   str_replace(APP_NAME,$app,__APP__).'/'.$module.$depr.$action.substr($str,0,-1);
        }
        if(C('HTML_URL_SUFFIX')) {
            $url .= C('HTML_URL_SUFFIX');
        }
    }else{
        $params =   http_build_query($params);
        if(!empty($route)) {
            $url    =   str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_ROUTER').'='.$route.'&'.$params;
        }else{
            $url    =   str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_MODULE').'='.$module.'&'.C('VAR_ACTION').'='.$action.'&'.$params;
        }
    }
    return $url;
}

/**
 +----------------------------------------------------------
 * 系统调试输出 Log::record 的一个调用方法
 +----------------------------------------------------------
 * @param string $msg 调试信息
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function system_out($msg)
{
    if(!empty($msg))
        Log::record($msg,Log::DEBUG);
}


/**
 +----------------------------------------------------------
 * 判断是否为对象实例
 +----------------------------------------------------------
 * @param mixed $object 实例对象
 * @param mixed $className 对象名
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function is_instance_of($object, $className)
{
	if (!is_object($object) && !is_string($object)) {
		return false;
	}
    return $object instanceof $className;
}

/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
	if($suffix)
		$suffixStr = "…";
	else
		$suffixStr = "";

    if(function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset).$suffixStr;
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset).$suffixStr;
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    return $slice.$suffixStr;
}

/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_string($len=6,$type='',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    if($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);
    }else{
        // 中文随机字
        for($i=0;$i<$len;$i++){
          $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }
    }
    return $str;
}

/**
 +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
 +----------------------------------------------------------
 * @param string $fmode 文件名
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_verify ($length=4,$mode=1) {
    return rand_string($length,$mode);
}

/**
 +----------------------------------------------------------
 * stripslashes扩展 可用于数组
 +----------------------------------------------------------
 * @param mixed $value 变量
 +----------------------------------------------------------
 * @return mixed
 +----------------------------------------------------------
 */
if(!function_exists('stripslashes_deep')) {
    function stripslashes_deep($value) {
       $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
       return $value;
    }
}


// 快速创建一个对象实例
function I($class,$baseUrl = '',$ext='.class.php') {
    static $_class = array();
    if(isset($_class[$baseUrl.$class])) {
        return $_class[$baseUrl.$class];
    }
    $class_strut = explode(".",$class);
    $className  =   array_pop($class_strut);
    if($className != '*') {
        import($class,$baseUrl,$ext,false);
        if(class_exists($className)) {
            $_class[$baseUrl.$class] = new $className();
            return $_class[$baseUrl.$class];
        }else{
            return false;
        }
    }else {
        return false;
    }
}


// 清除缓存目录
function clearCache($type=0,$path=NULL) {
        if(is_null($path)) {
            switch($type) {
            case 0:// 模版缓存目录
                $path = CACHE_PATH;
                break;
            case 1:// 数据缓存目录
                $path   =   TEMP_PATH;
                break;
            case 2://  日志目录
                $path   =   LOG_PATH;
                break;
            case 3://  数据目录
                $path   =   DATA_PATH;
            }
        }
        import("ORG.Io.Dir");
        Dir::del($path);
    }
?>