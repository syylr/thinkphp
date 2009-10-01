<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP标签库TagLib解析基类
 *
 * 要在模板页面中引入标签库，使用taglib标签，例如:
 * <taglib name='cs' />
 * 如果要引入多个标签库，可以使用
 * <taglib name='cs,mx,html' />
 *
 * 如果要指定标签库解析类，可以使用
 * <taglib name='cx'  class='Think.Template.TagLib.TagLib_cx' />
 * 系统内置引入了cx标签库，所以，如果需要使用cx标签库，无需使用taglib标签引入
 * 但是无需写cs前缀
 * 例如 <cx:vo name='user' value='id' />
 * 应该写成 <vo name='user' value='id' />
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Template
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class TagLib extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 标签库定义XML文件
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $xml = '';

    /**
     +----------------------------------------------------------
     * 标签库名称
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $tagLib ='';

    /**
     +----------------------------------------------------------
     * 标签库标签列表
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $tagList = array();

    /**
     +----------------------------------------------------------
     * 标签库分析数组
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $parse = array();

    /**
     +----------------------------------------------------------
     * 标签库是否有效
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $valid = false;

    /**
     +----------------------------------------------------------
     * 当前模板对象
     +----------------------------------------------------------
     * @var object
     * @access protected
     +----------------------------------------------------------
     */
    var $tpl;

    var $comparison = array(' nheq '=>' !== ',' heq '=>' === ',' neq '=>' != ',' eq '=>' == ',' egt '=>' >= ',' gt '=>' > ',' elt '=>' <= ',' lt '=>' < ');

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    function __construct($tagLib='',$filename='')
    {
        if(empty($tagLib)) {
            $tagLib =   strtolower(substr(get_class($this),6));
        }
        $this->tagLib  = $tagLib;
        $this->tpl       = ThinkTemplate::getInstance();
        if(!empty($filename)) {
            $this->xml = $filename;
        }else {
            $this->xml = dirname(__FILE__).'/Tags/'.$tagLib.'.xml';
        }
        $this->load();
    }

    function load() {
        $xml = file_get_contents($this->xml);
        $array   =  $this->xmlToArray($xml);
        if($array !== false) {
            $this->parse = $array;
            $this->valid = true;
        }else{
            $this->valid = false;
        }
    }

    /**
     +----------------------------------------------------------
     * 把XML数据转换成数组
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $xml  XML数据
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function xmlToArray($xml)
    {
        $values = array();
        $index  = array();
        $array  = array();
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        if(0===xml_parse_into_struct($parser, $xml, $values, $index)) {
            return false;
        }
        xml_parser_free($parser);
        $i = 0;
        $name = $values[$i]['tag'];
        $array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : '';
        $array[$name] = $this->_struct_to_array($values, $i);

        return $array[$name];
    }

    /**
     +----------------------------------------------------------
     * 把XML结构转换成数组
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $values  XML结构
     * @param integer $i  节点索引
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _struct_to_array($values, &$i)
    {
        $child = array();
        if (isset($values[$i]['value'])) array_push($child, $values[$i]['value']);

        while ($i++ < count($values)) {
            switch ($values[$i]['type']) {
                case 'cdata':
                    array_push($child, $values[$i]['value']);
                    break;

            	case 'complete':
                    $name = $values[$i]['tag'];
                	if( !empty($name)){
                        $child[$name]= isset($values[$i]['value'])?($values[$i]['value']):'';
                        if(isset($values[$i]['attributes'])) {
                            $child[$name] = $values[$i]['attributes'];
                        }
                    }
                    break;

                case 'open':
                    $name = $values[$i]['tag'];
                    $size = isset($child[$name]) ? sizeof($child[$name]) : 0;
                    $child[$name][$size] = $this->_struct_to_array($values, $i);
                    break;

                case 'close':
                    return $child;
                    break;
            }
        }
        return $child;
    }

    /**
     +----------------------------------------------------------
     * 分析TagLib文件的信息是否有效
     * 有效则转换成数组
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $name 数据
     * @param string $value  数据表名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function valid()
    {
        return $this->valid;
    }

    /**
     +----------------------------------------------------------
     * 获取TagLib名称
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getTagLib()
    {
        return $this->tagLib;
    }

    /**
     +----------------------------------------------------------
     * 获取Tag列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getTagList()
    {
        if(empty($this->tagList)) {
            $tags = $this->parse['tag'];
            $list = array();
            if(is_object($tags)) {
                $list[] =  array(
                    'name'=>$tags->name,
                    'content'=>$tags->bodycontent,
                    'nested'=>isset($tags->nested)?$tags->nested:0,
                    'attribute'=>isset($tags->attribute)?$tags->attribute:'',
                    );
                if(isset($tags->alias)) {
                    $alias  =   explode(',',$tag->alias);
                    foreach ($alias as $tag){
                        $list[] =  array(
                            'name'=>$tag,
                            'content'=>$tags->bodycontent,
                            'nested'=>isset($tags->nested)?$tags->nested:0,
                            'attribute'=>isset($tags->attribute)?$tags->attribute:'',
                            );
                    }
                }
            }else{
                foreach($tags as $tag) {
                    $tag = (array)$tag;
                    $list[] =  array(
                        'name'=>$tag['name'],
                        'content'=>$tag['bodycontent'],
                        'nested'=>isset($tag['nested'])?$tag['nested']:0,
                        'attribute'=>isset($tag['attribute'])?$tag['attribute']:'',
                        );
                    if(isset($tag['alias'])) {
                        $alias  =   explode(',',$tag['alias']);
                        foreach ($alias as $tag1){
                            $list[] =  array(
                                'name'=>$tag1,
                                'content'=>$tag['bodycontent'],
                                'nested'=>isset($tag['nested'])?$tag['nested']:0,
                                'attribute'=>isset($tag['attribute'])?$tag['attribute']:'',
                                );
                        }
                    }
                }
            }
            $this->tagList = $list;
        }
        return $this->tagList;
    }

    /**
     +----------------------------------------------------------
     * 获取某个Tag属性的信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getTagAttrList($tagName)
    {
        static $_tagCache   = array();
        $_tagCacheId        =   md5($this->tagLib.$tagName);
        if(isset($_tagCache[$_tagCacheId])) {
            return $_tagCache[$_tagCacheId];
        }
        $list = array();
        $tags = $this->parse['tag'];
        foreach($tags as $tag) {
            $tag = (array)$tag;
            if( strtolower($tag['name']) == strtolower($tagName)) {
                if(isset($tag['attribute'])) {
                    if(is_object($tag['attribute'])) {
                        // 只有一个属性
                        $attr = $tag['attribute'];
                        $list[] = array(
                            'name'=>$attr->name,
                            'required'=>$attr->required
                            );
                    }else{
                        // 存在多个属性
                        foreach($tag['attribute'] as $attr) {
                            $attr = (array)$attr;
                            $list[] = array(
                                'name'=>$attr['name'],
                                'required'=>$attr['required']
                                );
                        }
                    }
                }
            }
        }
        $_tagCache[$_tagCacheId]    =   $list;
        return $list;
    }

    /**
     +----------------------------------------------------------
     * TagLib标签属性分析 返回标签属性数组
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $tagStr 标签内容
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    function parseXmlAttr($attr,$tag)
    {
        //XML解析安全过滤
        $attr = str_replace("<","&lt;", $attr);
        $attr = str_replace(">","&gt;", $attr);
        $xml =  '<tpl><tag '.$attr.' /></tpl>';
        $array = $this->xmltoarray($xml);
        $array  = array_change_key_case($array['tag']);
        $attrs	= $this->getTagAttrList($tag);
        foreach($attrs as $val) {
            if( !isset($array[strtolower($val['name'])])) {
                $array[strtolower($val['name'])] = '';
            }
        }
        return $array;
    }

    /**
     +----------------------------------------------------------
     * 解析条件表达式
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $condition 表达式标签内容
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    function parseCondition($condition) {
        $condition = str_ireplace(array_keys($this->comparison),array_values($this->comparison),$condition);
        return $condition;
    }

    /**
     +----------------------------------------------------------
     * 日期格式化
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $var 变量
     * @param string $format 格式
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function dateFormat($var,$format,$true=false)
    {
        if($true) {
            $tmplContent = 'date( "'.$format.'", intval('.$var.') )';
        }else {
            $tmplContent = 'date( "'.$format.'", strtotime('.$var.') )';
        }
        return $tmplContent;
    }

    /**
     +----------------------------------------------------------
     * 字符串格式化
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $var 变量
     * @param string $format 格式
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function stringFormat($var,$format)
    {
        $tmplContent = 'sprintf("'.$format.'", '.$var.')';
        return $tmplContent;
    }

    /**
     +----------------------------------------------------------
     * 字符串格式化
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $var 变量
     * @param string $format 格式
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function numericFormat($var,$format)
    {
        $tmplContent = 'number_format("'.$var.'")';
        return $tmplContent;
    }

    /**
     +----------------------------------------------------------
     * 自动识别构建变量
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 变量描述
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function autoBuildVar($name) {
        if('Think.' == substr($name,0,6)){
            // 特殊变量
            return $this->parseThinkVar($name);
        }elseif(strpos($name,'.')) {
            // 数组和对象自动判断支持
            $vars = explode('.',$name);
            $name = 'is_array($'.$vars[0].')?$'.$vars[0].'["'.$vars[1].'"]:$'.$vars[0].'->'.$vars[1];
        }elseif(strpos($name,':')){
            // 额外的对象方式支持
            $name   =   '$'.str_replace(':','->',$name);
        }elseif(!defined($name)) {
            $name = '$'.$name;
        }
        return $name;
    }

    /**
     +----------------------------------------------------------
     * 用于标签属性里面的特殊模板变量解析
     * 格式 以 Think. 打头的变量属于特殊模板变量
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $varStr  变量字符串
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function parseThinkVar($varStr){
        $vars = explode('.',$varStr);
        $vars[1] = strtoupper(trim($vars[1]));
        $parseStr = '';

        if(count($vars)==3){
            $vars[2] = trim($vars[2]);
            switch($vars[1]){
                case 'SERVER':    $parseStr = '$_SERVER[\''.$vars[2].'\']';break;
                case 'GET':         $parseStr = '$_GET[\''.$vars[2].'\']';break;
                case 'POST':       $parseStr = '$_POST[\''.$vars[2].'\']';break;
                case 'COOKIE':    $parseStr = '$_COOKIE[\''.$vars[2].'\']';break;
                case 'SESSION':   $parseStr = '$_SESSION[\''.$vars[2].'\']';break;
                case 'ENV':         $parseStr = '$_ENV[\''.$vars[2].'\']';break;
                case 'REQUEST':  $parseStr = '$_REQUEST[\''.$vars[2].'\']';break;
                case 'CONST':     $parseStr = strtoupper($vars[2]);break;
                case 'LANG':       $parseStr = 'L("'.$vars[2].'")';break;
                case 'CONFIG':    $parseStr = 'C("'.$vars[2].'")';break;
                default:break;
            }
        }else if(count($vars)==2){
            switch($vars[1]){
                case 'NOW':       $parseStr = "date('Y-m-d g:i a',time())";break;
                case 'VERSION':  $parseStr = 'THINK_VERSION';break;
                case 'TEMPLATE':$parseStr = 'C("TMPL_FILE_NAME")';break;
                case 'LDELIM':    $parseStr = 'C("TMPL_L_DELIM")';break;
                case 'RDELIM':    $parseStr = 'C("TMPL_R_DELIM")';break;
            }
            if(defined($vars[1])){ $parseStr = strtoupper($vars[1]);}
        }
        return $parseStr;
    }

}//类定义结束
?>