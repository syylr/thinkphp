<?php
/**
 +----------------------------------------------------------
 * ThinkPHP2.0 配置设置变更说明
 +----------------------------------------------------------
 */

     2.0 配置名称               1.6RC1+ 版本配置名称
**********************          *************************
/* 项目设定 */
'APP_DEBUG'				----->  'DEBUG_MODE',
'APP_PLUGIN_ON'         ----->  'TAG_PLUGIN_ON',
'APP_FILE_CASE'         ----->  'CHECK_FILE_CASE',
'APP_GROUP_DEPR'        ----->  'GROUP_DEPR',    // 2.0新增
'APP_GROUP_LIST'        ----->  'APP_GROUP',     // 2.0新增
'APP_AUTOLOAD_REG'      ----->  'AUTOLOAD_REG',  // 2.0新增
'APP_AUTOLOAD_PATH'     ----->  'AUTO_LOAD_PATH',
'APP_CONFIG_LIST'       ----->  'EXTEND_CONFIG_LIST',

/* 默认设定 */
'DEFAULT_APP'           ----->  'DEFAULT_MODEL_APP',
'DEFAULT_GROUP'         ----->  // 2.0新增
'DEFAULT_CHARSET'       ----->  'OUTPUT_CHARSET',
'DEFAULT_TIMEZONE'      ----->  'TIME_ZONE',
'DEFAULT_AJAX_RETURN'   ----->  'AJAX_RETURN_TYPE',
'DEFAULT_LANG'          ----->  'DEFAULT_LANGUAGE','LANG_DEFAULT',
'DEFAULT_THEME'         ----->  'TMPL_DEFAULT_THEME',

/* 数据库设置 */
'DB_FIELDTYPE_CHECK'    ----->  // 2.0新增

/* 语言设置 */
'LANG_AUTO_DETECT'      ----->  'AUTO_DETECT_LANG',

/* 日志设置 */
'LOG_RECORD'            ----->  'WEB_LOG_RECORD',

/* 分页设置 */
'PAGE_ROLLPAGE'         ----->  'PAGE_NUMBERS',
'PAGE_LISTROWS'         ----->  'LIST_NUMBERS',

/* 模板引擎设置 */
'TMPL_DEFAULT_THEME'    ----->  'DEFAULT_TEMPLATE',
'TMPL_DETECT_THEME'     ----->  'AUTO_DETECT_THEME',
'TMPL_TEMPLATE_SUFFIX'  ----->  'TEMPLATE_SUFFIX',
'TMPL_CACHFILE_SUFFIX'  ----->  'CACHFILE_SUFFIX',
'TMPL_PARSE_STRING'     ----->  // 2.0 新增
'TMPL_VAR_IDENTIFY'     ----->  // 2.0 新增,
'TMPL_STRIP_SPACE'      ----->  // 2.0 新增,


'TMPL_ACTION_ERROR'     ----->  // 2.0 新增,
'TMPL_ACTION_SUCCESS'   ----->  'ACTION_JUMP_TMPL',
'TMPL_TRACE_FILE'       ----->  // 2.0 新增,
'TMPL_EXCEPTION_FILE'   ----->  // 2.0 新增,
'TMPL_FILE_DEPR'        ----->  // 2.0 新增,


// Think模板引擎标签库相关设定
'TAGLIB_LOAD'           ----->  // 2.0功能修改
'TAGLIB_BUILD_IN'       ----->  // 2.0新增,
'TAGLIB_PRE_LOAD'       ----->  'TAGLIB_LIST',
'TAG_EXTEND_PARSE'      ----->  // 2.0新增,

/* 表单令牌验证 */
'TOKEN_ON'              ----->  // 2.0新增
'TOKEN_NAME'            ----->  // 2.0新增
'TOKEN_TYPE'            ----->  // 2.0新增

/* URL设置 */
'URL_CASE_INSENSITIVE'  ----->  // 2.0新增
'URL_ROUTER_ON'         ----->  'ROUTER_ON',
'URL_DISPATCH_ON'       ----->  'DISPATCH_ON',
'URL_PATHINFO_MODEL'    ----->  'PATH_MODEL',
'URL_PATHINFO_DEPR'     ----->  'PATH_DEPR',
'URL_HTML_SUFFIX'       ----->  'HTML_URL_SUFFIX',

/* 系统变量名称设置 */
'VAR_GROUP'             ----->  // 2.0新增

?>