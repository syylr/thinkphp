<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: Action.class.php								  |
| 功能: 模块操作基础类									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.core.Template");

class Action extends Base{

	//+----------------------------------------
	//|	公共属性
	//+----------------------------------------
	var $Public = Array();
	var $Mod	= Array();		//模块属性数组
	var $Template;					//模板对象

	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------
	function __construct()
	{
		$this->Template = Template::getInstance();	
	}

	//+----------------------------------------
	//|	内置模板的相关调用方法
	//+----------------------------------------

	//+----------------------------------------
	//|	模板显示
	//+----------------------------------------
	function Display($templateFile='')
	{
		$this->Template->Display($templateFile);
	}

	//+----------------------------------------
	//|	模板变量赋值
	//+----------------------------------------
	function assign($name,$value){
		$this->Template->assign($name,$value);
	}

	//+----------------------------------------
	//|	数据对象模板赋值
	//+----------------------------------------
	function assignVo($name,$Vo){
		$this->Template->assignVo($name,$Vo);
	}

	//+----------------------------------------
	//|	数据对象模板赋值
	//+----------------------------------------
	function assignVoList($name,$VoList){
		$this->Template->assignVoList($name,$VoList);
	}

	//+----------------------------------------
	//|	取得某个模板变量
	//+----------------------------------------
	function get($name){
		return $this->Template->get($name);
	}

	function forward(){
		$publicDir = WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/public/';
		$this->assign('publicCss',$publicDir."css/style.css");
		$this->assign('publicDir',$publicDir);
		$forwardFile = TEMPLATE_PATH.'/public/success'.TEMPLATE_SUFFIX;
		if(!$this->get('jumpUrl')){
			$this->assign('jumpUrl',"javascript:history.back(-1);");
		}
		if(!$this->get('waitSecond')){
			$this->assign('waitSecond',"5");
		}
		if(!$this->get('closeWin')){
			$this->assign('closeWin','0');
		}
		$this->Display($forwardFile);
	}

	//+----------------------------------------
	//|	创建数据模型Vo对象
	//| $VoClass	要创建的Vo对象名称
	//| $Type		edit 编辑 add 新增
	//| 返回Vo对象
	//+----------------------------------------
	function createVo($VoClass,$Type='add',$PK='id')
	{
		if ( strtolower($Type) == "add" ) { //新增
			$Vo = new $VoClass(); //新建Vo对象
			//可以在这里定义系统的一些默认属性
			// 如 $Vo->status = 1;

		} else { //编辑
			//根据编号获取Vo对象
			$DaoClass = substr($VoClass,0,-2).'Dao';
			$Dao = new $DaoClass();
			$Vo  = $Dao->find($_GET[$PK]);
		}
		//给Vo对象赋值
		foreach ( $Vo->__varList() as $name){
			$val = isset($_POST[$name])?$_POST[$name]:$_GET[$name];
			if(isset($val)){
				$Vo->$name = $val;
			}
		}
		return $Vo;
	}

};
?>