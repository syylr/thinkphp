<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// ----------------加载REST模式的额外配置参数 可以在项目配置中修改--------------
C(array(
'REST_METHOD_LIST'=>'get,post,put,delete', // 允许的请求类型列表
'REST_DEFAULT_METHOD'=>'get', // 默认请求类型
'REST_CONTENT_TYPE_LIST'=>'html,xml,jpg,gif,png,js,json,css,rss,atom,pdf,text,csv', // REST允许请求的资源类型列表
'REST_DEFAULT_TYPE'=>'html', // 默认的资源类型
'REST_OUTPUT_TYPE'=>array(  // REST允许输出的资源类型列表
        'xml' => 'application/xml',
        'rawxml' => 'application/xml',
        'json' => 'application/json',
        'jsonp' => 'application/javascript',
        'serialized' => 'application/vnd.php.serialized',
        'html' => 'text/html',
    ),
));

//--------------------------------- 放置REST模式的额外函数--------------------------------

/**
 +----------------------------------------------------------
 * 获取当前请求的Accept头信息
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function get_accept_type(){
    $type = array(
        'html'=>'text/html,application/xhtml+xml,*/*',
        'xml'=>'application/xml,text/xml,application/x-xml',
        'json'=>'application/json,text/x-json,application/jsonrequest,text/json',
        'js'=>'text/javascript,application/javascript,application/x-javascript',
        'css'=>'text/css',
        'rss'=>'application/rss+xml',
        'yaml'=>'application/x-yaml,text/yaml',
        'atom'=>'application/atom+xml',
        'pdf'=>'application/pdf',
        'text'=>'text/plain',
        'png'=>'image/png',
        'jpg'=>'image/jpg,image/jpeg,image/pjpeg',
        'gif'=>'image/gif',
        'csv'=>'text/csv'
    );
    
    foreach($type as $key=>$val){
        $array   =  explode(',',$val);
        foreach($array as $k=>$v){
            if(stristr($_SERVER["HTTP_ACCEPT"], $v)) {
                return $key;
            }
        }
    }
    return false;
}

// 发送Http状态信息
function send_http_status($status) {
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    if(array_key_exists($code,$_status)) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:'.$code.' '.$_status[$code]);
    }
}
?>