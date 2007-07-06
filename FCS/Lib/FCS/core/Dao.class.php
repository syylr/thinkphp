<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: Dao.class.php									  |
| 功能: 数据访问基础类									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.db.Db");

class Dao extends Base{

	//+----------------------------------------
	//|	公共变量
	//+----------------------------------------
	var $db ;				// 数据库底层操作对象
	var $vo;
	var $dao;
	var $PK = 'id';

	//+----------------------------------------
	//|	数据表
	//| 数据表由<项目名_模块名_表名> 三部分组成
	//| appPrefix_modPrefix_table
	//+----------------------------------------
	var $appPrefix;			//项目前缀
	var $modPrefix;			//模块前缀
	var $tableName;			//数据表名

	//+----------------------------------------
	//|	架构函数
	//+----------------------------------------
	function __construct()
	{
		$this->db = DB::getInstance();
	}

	//+----------------------------------------
	//|	新增数据表
	//| 支持Vo对象和数组
	//+----------------------------------------
	function Add($Data,$Table=NULL)
	{
		if(is_array($Data)){
			$Map = new HashMap($Data);
		}else if(is_a($Data,'Vo')){
			$Map = $Data->toMap();
		}else {
			ThrowException('非法数据对象！');
		}
		$Map->remove($this->PK); //删除主键属性 由数据库自动生成
		$Table = empty($Table)?$this->getFullTableName():$Table;
		if(FALSE === $this->db->add($Map,$Table)){
			ThrowException("新增数据出错！"); 
		}else {
			return True;
		}
	}

	//+----------------------------------------
	//|	更新数据表
	//| 支持Vo对象和数组
	//+----------------------------------------
	function Save($Data,$Table=NULL)
	{
		if(is_array($Data)){
			$Map = new HashMap($Data);
		}else if(is_a($Data,'Vo')){
			$Map = $Data->toMap();
		}else {
			ThrowException('非法数据对象！');
		}
		$Where = $this->PK."=".$Map->get($this->PK);
		$Map->remove($this->PK); 
		$Table = empty($Table)?$this->getFullTableName():$Table;
		if(FALSE === $this->db->Save($Map,$Table,$Where)){
			ThrowException("更新数据出错！"); 
		}else {
			return True;
		}
	}

	//+----------------------------------------
	//|	根据ID删除数据表
	//+----------------------------------------
	function DeleteById($Id,$Table='')
	{
		$Table = empty($Table)?$this->getFullTableName():$Table;
		if(FALSE === $this->db->delete($this->PK."=$Id",$Table)){
			ThrowException("删除数据出错！"); 
		}else {
			return True;
		}
	}

	//+----------------------------------------
	//|	根据条件删除数据表
	//+----------------------------------------
	function Delete($Condition,$Table='')
	{
		$Table = empty($Table)?$this->getFullTableName():$Table;
		if(FALSE === $this->db->Remove($Condition,$Table)){
			ThrowException("删除数据出错！"); 
		}else {
			return True;
		}
	}

	//+----------------------------------------
	//|	根据条件删除数据表
	//+----------------------------------------
	function DeleteAll($Condition,$Table='')
	{
		$Table = empty($Table)?$this->getFullTableName():$Table;
		if(FALSE === $this->db->Remove($Condition,$Table)){
			ThrowException("删除数据出错！"); 
		}else {
			return True;
		}
	}

	//+----------------------------------------
	//|	根据ID得到一条数据
	//| 返回Vo对象
	//+----------------------------------------
	function getById($Id,$Table='',$Fields='*')
	{
		$Table = empty($Table)?$this->getFullTableName():$Table;
		$Rs = $this->db->find($this->PK."=$Id",$Table,$Fields);
		return $this->RsToVo($Rs->get(0));
	}

	//+----------------------------------------
	//|	根据条件得到一条数据
	//| 返回Vo List 对象
	//+----------------------------------------
	function find($Condition,$Table=NULL,$Fields='*')
	{
		$Table = empty($Table)?$this->getFullTableName():$Table;
		$Rs = $this->db->find($Condition,$Table,$Fields);
		return $this->RsToVo($Rs->get(0));
	}

	//+----------------------------------------
	//|	得到符合条件的所有数据
	//| 返回Vo对象列表
	//+----------------------------------------
	function findAll($Condition=NULL,$Table=NULL,$Order=NULL,$Limit=NULL,$Group=NULL,$Having=NULL)
	{
		$Table = empty($Table)?$this->getFullTableName():$Table;
		$Rs = $this->db->Find($Condition,$Table,$Order,$Limit,$Group,$Having);
		return $this->RsToVoList($Rs);
	}

	//+----------------------------------------
	//| 把一条查询结果转换为Vo对象
	//+----------------------------------------
	function RsToVo($Result,$VoClass=NULL)
	{
		$VoClass = !empty($VoClass)? $VoClass : $this->getVo();
		$Vo = new $VoClass($Result);
		return $Vo;
	}

	//+----------------------------------------
	//| 把查询结果集转换为Vo对象集
	//+----------------------------------------
	function RsToVoList($ResultSet,$VoClass=NULL)
	{
		$VoList = new VoList();
		$VoClass = !empty($VoClass)? $VoClass : $this->getVo();
		while ($ResultSet->valid())
		{
			$Result = $ResultSet->current();
			if($Result){
				$Vo = new $VoClass($Result);
				$VoList->add($Vo);
			}
			$ResultSet->next();
		}
		return $VoList;
	}

	//+----------------------------------------
	//|	getFullTableName
	//+----------------------------------------
	function getFullTableName()
	{
		$fullTableName  = $this->appPrefix ? $this->appPrefix.'_' : '';
		$fullTableName .= $this->modPrefix ? $this->modPrefix.'_' : '';	
		$fullTableName .= $this->tableName ? $this->tableName : substr($this->__toString(),0,-3);
		return $fullTableName;
	}

	//+----------------------------------------
	//|	getTableName
	//+----------------------------------------
	function getTableName()
	{
		if($this->tableName){
			return $this->tableName;
		}else 
			return substr($this->__toString(),0,-3);
	}

	//+----------------------------------------
	//|	getVo
	//+----------------------------------------
	function getVo()
	{
		return $this->getTableName().'Vo';
	}

	//+----------------------------------------
	//|	getDao
	//+----------------------------------------
	function getDao()
	{
		return $this->__toString();
	}

	//+----------------------------------------
	//|	
	//+----------------------------------------
	function QueryTimes()
	{
		return $this->db->getQueryTimes();
	}

	//+----------------------------------------
	//|	
	//+----------------------------------------
	function WriteTimes()
	{
		return $this->db->getWriteTimes();
	}
};
?>