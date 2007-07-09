<?php 
// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 liu21st.com All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the 'License');      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an 'AS IS' BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: liu21st <liu21st@gmail.com>                                  |
// +----------------------------------------------------------------------+
// $Id$

import("Think.Db.Db");
import("Think.Util.Cache");

define('HAS_ONE',1);
define('BELONGS_TO',2);
define('HAS_MANY',3);
define('MANY_TO_MANY',4);

define('MUST_TO_VALIDATE',1);
define('SET_TO_VAILIDATE',0);

define('Think_CACHE_NO',      -1);   //不缓存
define('Think_CACHE_DYNAMIC', 1);   //动态缓存
define('Think_CACHE_STATIC',  2);   //静态缓存（永久缓存）

/**
 +------------------------------------------------------------------------------
 * 数据访问基础类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Dao extends Base
{
	// 数据库底层操作对象
    var $db ;                

	// 主键名称
    var $pk =   'id';

	// 主键是否自动增加
    var $autoIncrement =   true;

	// 启用查询缓存
	var $cacheQuery =   true;

    // 返回元数据类型 1 Vo 0 数组
    var $resultType =  '';

    // 数据表前缀
    var $tablePrefix	=	'';            

    // 数据表后缀
	var $tableSuffix = '';

    // Dao名称
    var $name;            

    // 数据表名
    var $tableName;        

    // 字段信息
    var $fields = array();

	// 数据信息
	var $data	=	array();

    // 错误信息
    var $error;        

	// 乐观锁
	var $optimLock = 'lock_version';
	
	var $autoSaveRelations = false;		// 自动关联保存
	var $autoDelRelations = false;	// 自动关联删除
	var $autoAddRelations = false;		// 自动关联增加

	// 自动写入时间戳
	var $autoCreateTimestamps = array('create_at','create_on','cTime');
	var $autoUpdateTimestamps = array('update_at','update_on','mTime');

    /**
     +----------------------------------------------------------
     * 架构函数 取得DB类的实例对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $tablePrefix 数据库前缀
     * @param string $name 数据表名
     * @param string $pk  主键名
     * @param boolean $autoIncrement  是否自动增长
     +----------------------------------------------------------
     */
    function __construct($tablePrefix='')
    {
		$this->name	=	substr($this->__toString(),0,-3);
        $this->db = Db::getInstance();
        if(!empty($tablePrefix))  $this->tablePrefix    =   $tablePrefix;
		else $this->tablePrefix = C('DB_PREFIX');

		// 自动记录数据表字段		
		if( empty($this->fields)) {
			// 如果数据表字段没有保存则获取
			$guid	=	$this->name.'Vo';
			$this->fields = S($guid);
			if(!$this->fields) {
				$fields	=	$this->db->getFields($this->getTableName());
				$this->fields	=	array_keys($fields);
				$this->fields['_autoInc'] = false;
				foreach ($fields as $key=>$val){
					if($val['primary']) {
						$this->fields['_pk']	=	$key;
						if($val['autoInc'])	$this->fields['_autoInc']	=	true;
					}
				}
				S($guid,$this->fields);
			}
			// 自动获取主键和自动增长
			$this->pk	=	$this->fields['_pk'];
			$this->autoIncrement	=	$this->fields['_autoInc'];
		}

        //如果存在initialize方法，就首先调用
        if(method_exists($this,'_initialize')) {
            $this->_initialize();
        }
    }


    /**
     +----------------------------------------------------------
     * 新增数据
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param string $table  数据表名
     * @param string $pk 主键名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function add($data,$autoLink=false)
    {
		// 检查前置操作
		if(method_exists($this,'_before_add')) {
			$this->_before_add();
		}
        if(is_array($data)){
            $map = new HashMap($data);
        }
		else if(is_instance_of($data,'HashMap')){
            $map = $data;
        }
		else {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        if($this->autoIncrement) {
            //如果主键为自动增长类型
            //删除主键属性 由数据库自动生成
            $map->remove($this->pk); 
        }
		// 增加对数据库映射字段和属性不同的支持
		$map	=	$this->dealMap($map);
        $table = $this->getTableName();

		// 记录乐观锁
		if($this->optimLock && !$map->get($this->optimLock) ) {
			if(in_array($this->optimLock,$this->fields)) {
				$map->put($this->optimLock,0);
			}
		}

        if(FALSE === $this->db->add($map,$table)){
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
			$insertId	=	$this->db->getLastInsID();
			// 保存关联记录
			if ($this->autoAddRelations || $autoLink){
				if(empty($pk)) $pk	=	$this->pk;
				$map->put($pk, $insertId);
				$this->opRelation('ADD',$map);
			}
			// 检查后置操作
			if(method_exists($this,'_after_add')) {
				$this->_after_add();
			}
            //成功后返回插入ID
            return $insertId;
        }
    }

	// 对保存到数据库的Map对象进行处理
	// 增加对数据库映射字段和属性不同的支持
	function dealMap($map) {
		if(isset($this->_map)) {
			foreach ($this->_map as $key=>$val){
				$map->put($val,$map->get($key));
				$map->remove($key);
			}
		}
		return $map;
	}

    /**
     +----------------------------------------------------------
     * 批量新增数据
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $dataList 数据
     * @param string $table  数据表名
     * @param string $pk 主键名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function addAll($dataList)
    {
        if(is_object($dataList) && is_instance_of($dataList,'ResultSet')){
            $dataList = $dataList->toArray();
        }elseif(!is_array($dataList)) {
            $this->error = L('_DATA_TYPE_INVALID_');
			return false;
		}
        //启用事务操作
        $this->startTrans();
        foreach ($dataList as $data){
            $this->add($data);
        }
        //提交
        $this->commit();
        return true;
    }

    /**
     +----------------------------------------------------------
     * 更新数据
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 要更新的数据
     * @param string $table  数据表名
     * @param mixed $where 更新数据的条件
     * @param string $pk 主键名
     * @param integer $limit 要更新的记录数
     * @param string $order  更新的顺序
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function save($data,$where='',$limit=0,$order='')
    {
		// 检查前置操作
		if(method_exists($this,'_before_save')) {
			$this->_before_save($data);
		}
        if(is_array($data)){
            $map = new HashMap($data);
        }
        elseif(is_instance_of($data,'HashMap')) {
            $map    = $data;
        }
		elseif(is_instance_of($data,'ResultSet')){
			//启用事务操作
			$this->startTrans();
			foreach ($data as $val){
				$result   =  $this->save($val);
			}
			$this->commit();
			return $result;
		}
        else {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
		
        $pk     =	$this->pk;
        if($map->containsKey($pk)) {
			$id	=	$map->get($pk);
            $where  = $pk."=".$id;
            $map->remove($pk);         	
        }
		// 增加对数据库映射字段和属性不同的支持
		$map	=	$this->dealMap($map);		
        $table = $this->getTableName();

		// 检查乐观锁
		$guid   = $this->name.'_'.$id;
		if($this->optimLock && Session::is_set($guid.'_lock_version')) {
			$lock_version = Session::get($guid.'_lock_version');
			if(!empty($where)) {
				$vo = $this->find($where,$table,$this->optimLock);
			}else {
				$vo = $this->find($map,$table,$this->optimLock);
			}
			Session::set($guid.'_lock_version',$lock_version);
			$curr_version = $vo[$this->optimLock];
			if(isset($curr_version)) {
				if($curr_version>0 && $lock_version != $curr_version) {
					// 记录已经更新
					$this->error = L('_RECORD_HAS_UPDATE_');
					return false;
				}else{
					// 更新乐观锁
					$save_version = $map->get($this->optimLock);
					if($save_version != $lock_version+1) {
						$map->put($this->optimLock,$lock_version+1);
					}
					Session::set($guid.'_lock_version',$lock_version+1);
				}
			}
		}

        if(FALSE === $this->db->save($map,$table,$where,$limit,$order)){
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
			// 保存关联记录
			if ($this->autoSaveRelations){
				$this->opRelation('SAVE',$data);
			}
			// 检查后置操作
			if(method_exists($this,'_after_save')) {
				$this->_after_save($data);
			}
            return True;
        }
    }

    // 操作关联数据
    function opRelation($opType,$data,$type='',$name='') 
    {
  		$result	=	false;
        if(!empty($this->_link)) {
            foreach($this->_link as $val) {
                if(empty($type) || $val['mapping_type']==$type) {
                    $mappingType = $val['mapping_type'];
                    $mappingVo  = $val['class_name'];
                    $mappingFk   =  $val['foreign_key'];
                    if(empty($mappingFk)) {
                    	$mappingFk  =  $this->name.'_id';
                    }
                    $mappingName =  $val['mapping_name'];
                    $mappingCondition = $val['condition'];
                    if(empty($mappingCondition)) {
                        $pk   =  $data[$this->pk];
                        $mappingCondition = "$mappingFk={$pk}";
                    }
                    if( empty($name) || $mappingName == $name) {
                        $dao = D($mappingVo);
						$mappingData	=	$data[$mappingName];
						if(!empty($mappingData)) {
							switch($mappingType) {
								case HAS_ONE:
								case HAS_MANY:
									switch (strtoupper($opType)){
										case 'ADD'	 :	// 增加关联数据
											$mappingData[$mappingFk]	=	$pk;
											$result   =  $dao->add($mappingData);
											break;
										case 'SAVE' :	// 更新关联数据
											$result   =  $dao->save($mappingData,$mappingCondition);
											break;
										case 'DEL' :	// 删除关联数据
											$result   =  $dao->delete($mappingCondition);
											break;
										default:
											return false;
									}
									break;
							}
						}
                     }                	
                }
            }
        }      
		return $result;
    }

	// 保存多对多中间表
	function saveHabtm($data,$name,$relationData) 
	{
		$result	=	false;
		if(!empty($this->_link)) {
			// 存在关联
			foreach ($this->_link as $val){
				if($val['mapping_type']==MANY_TO_MANY && $val['mapping_name'] == $name) {
					// habtm 关联
                    $mappingVo  = $val['class_name'];
                    $mappingFk   =  $val['foreign_key'];
                    if(empty($mappingFk)) {
                    	$mappingFk  =  $this->name.'_id';
                    }
					$dao = D($mappingVo);
					$mappingRelationFk = $val['relation_foreign_key'];
					if(empty($mappingRelationFk)) {
						$mappingRelationFk   = $dao->name.'_id';
					}
					$mappingRelationTable  =  $val['relation_table'];
					if(empty($mappingRelationTable)) {
						$mappingRelationTable  =  $this->getRelationTableName($dao);
					}
					if(is_array($relationData)) {
						$relationData	=	implode(',',$relationData);
					}
			        $this->startTrans();
					// 删除关联表数据
					$this->db->remove($mappingFk.'='.$data[$this->pk],$mappingRelationTable);
					// 插入关联表数据
					$sql  = 'INSERT INTO '.$mappingRelationTable.' ('.$mappingFk.','.$mappingRelationFk.') SELECT a.'.$this->pk.',b.'.$dao->pk.' FROM '.$this->getTableName().' AS a ,'.$dao->getTableName()." AS b where a.".$this->pk.' ='. $data[$this->pk].' AND  b.'.$dao->pk.' IN ('.$relationData.") ";	
					$result	=	$dao->execute($sql);
					if($result) {
				        // 提交事务
				        $this->commit();
					}else {
						// 事务回滚
						$this->rollback();
					}
				}
			}
		}
		return $result;
	}

    /**
     +----------------------------------------------------------
     * 删除数据
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param integer $id 主键值
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function deleteById($id)
    {
        if(FALSE === $this->db->remove($this->pk."='$id'",$this->getTableName())){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据条件删除表数据
     * 如果成功返回删除记录个数
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     * @param string $table  数据表名
     * @param integer $limit 要删除的记录数
     * @param string $order  删除的顺序
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function delete($data,$limit='',$order='')
    {
		// 检查前置操作
		if(method_exists($this,'_before_delete')) {
			$this->_before_delete();
		}
        if(is_array($data)){
            $map = new HashMap($data);
        }
        elseif(is_instance_of($data,'HashMap')) {
            $map    = $data;
        }
        if(!empty($map)) {
			$map	=	$this->dealMap($map);
            $where  = $pk."=".$map->get($this->pk);            
        }else {
            $where  =   $data;
        }
        $result=    $this->db->remove($where,$this->getTableName(),$limit,$order);
        if(FALSE === $result ){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
			// 删除关联记录
			if ($this->autoDelRelations){
				$this->opRelation('DEL',$data);
			}
			// 检查后置操作
			if(method_exists($this,'_after_delete')) {
				$this->_after_delete($data);
			}
            //返回删除记录个数
            return $result;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据条件删除表数据
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function deleteAll($condition)
    {
        if(FALSE === $this->db->remove($condition,$this->getTableName())){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 清空表数据
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function clear()
    {
        if(FALSE === $this->db->clear($this->getTableName())){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据主键得到一条记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param int $id 主键的值
     * @param string $table  数据表名
     * @param string $fields 字段名，默认为*
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getById($id,$fields='*',$relation=false)
    {
        if(C('DATA_CACHE_ON')) {//启用动态数据缓存
        	$vo  =  $this->getCacheVo($id);
            if(false !== $vo) {
            	return $vo;
            }
        }
        $rs     = $this->db->find($this->pk."='{$id}'",$this->getTableName(),$fields);
        if($rs && $rs->size()>0) {
                $vo  =  $this->rsToVo($rs->get(0),'','',$relation);
                if(C('DATA_CACHE_ON')) 
                    $this->CacheVo($vo,$id);
                return $vo;
        }else {
            return false;
        }        	
    }

    /**
     +----------------------------------------------------------
     * 根据某个字段得到一条记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field 字段名称
     * @param mixed $value 字段的值
     * @param string $table  数据表名
     * @param string $fields 字段名，默认为*
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getBy($field,$value,$table='',$fields='*',$relation=false)
    {
        $table  = empty($table)?$this->getTableName():$table;
        if(C('DATA_CACHE_ON')) {//启用动态数据缓存
        	$vo  =  $this->getCacheVo($field.'_'.$value);
            if(false !== $vo) {
            	return $vo;
            }
        }
        $rs     = $this->db->find($field."='{$value}'",$table,$fields);
        if($rs && $rs->size()>0) {
                $vo  =  $this->rsToVo($rs->get(0),'','',$relation);
                if(C('DATA_CACHE_ON')) 
                    $this->CacheVo($vo,$field.'_'.$value);
                return $vo;
        }else {
            return false;
        }        	
    }

    /**
     +----------------------------------------------------------
     * 根据条件得到一条记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 条件
     * @param string $table  数据表名
     * @param string $fields 字段名，默认为*
     * @param boolean $cache 是否读取缓存
     +----------------------------------------------------------
     * @return Vo | false
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function find($condition='',$table=NULL,$fields='*',$cache=true,$relation=false)
    {
        $table  = empty($table)?$this->getTableName():$table;
        $identify   = to_guid_string($condition);
        if(C('DATA_CACHE_ON') && $cache) {//启用动态数据缓存
        	$vo  =  $this->getCacheVo($identify);
            if(false !== $vo) {
            	return $vo;
            }
        }
        $rs = $this->db->find($condition,$table,$fields,NULL,NULL,NULL,NULL,$cache);
        if($rs && $rs->size()>0) {
            $vo  =  $this->rsToVo($rs->get(0),'','',$relation);
            if(C('DATA_CACHE_ON'))
                $this->cacheVo($vo,$identify);
            return $vo;
        }else {
			$this->error = L('_SELECT_NOT_EXIST_');
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 根据条件得到一条记录
     * 并且返回关联记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition 条件
     * @param string $table  数据表名
     * @param string $fields 字段名，默认为*
     * @param boolean $cache 是否读取缓存
     +----------------------------------------------------------
     * @return Vo | false
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function xFind($condition='',$table=NULL,$fields='*',$cache=true) 
    {
        return $this->find($condition,$table,$fields,$cache,true);
    }

    /**
     +----------------------------------------------------------
     * 获取返回数据的关联记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $result  返回数据
     * @param string $type  关联类型
     * @param string $name 关联名称
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getRelation($result,$type='',$name='') 
    {
        if(!empty($this->_link)) {
            foreach($this->_link as $val) {
                if(empty($type) || $val['mapping_type']==$type) {
                    $mappingType = $val['mapping_type'];
                    $mappingVo  = $val['class_name'];
                    $mappingFk   =  $val['foreign_key'];
                    if(empty($mappingFk)) {
                    	$mappingFk  =  $this->name.'_id';
                    }
                    $mappingName =  $val['mapping_name'];
                    $mappingFields = $val['mapping_fields'];
                    $mappingCondition = $val['condition'];
                    $dao = D($mappingVo);
                    if(strtoupper($mappingVo)==strtoupper($this->name)) {
                        // 自引用
                        $mappingParentKey = !empty($val['parent_key'])? $val['parent_key'] : 'parent_id';
                    }
                    if(empty($mappingCondition)) {
                        $fk   =  $result[$this->pk];
                        if(empty($mappingParentKey)) {
                        	$mappingCondition = "{$mappingFk}={$fk}";
                        }else {
                            if($mappingType== BELONGS_TO) {
                                $parentKey   =  $result[$mappingParentKey];
                            	$mappingCondition = "{$dao->pk}={$parentKey}";
                            }else {
                            	$mappingCondition = "{$mappingParentKey}={$fk}";
                            }
                        }
                    }
                    if(empty($name) || $mappingName == $name) {
                        switch($mappingType) {
                            case HAS_ONE:
                            case BELONGS_TO:
                                // 不再获取关联记录的关联
                                $relation   =  $dao->find($mappingCondition,'',$mappingFields,false,false);
                                break;
                            case HAS_MANY:
                                $mappingOrder =  $val['mapping_order'];
                                $mappingLimit =  $val['mapping_limit'];
                                // 不再获取关联记录的关联
                                $relation   =  $dao->findAll($mappingCondition,'',$mappingFields,$mappingOrder,$mappingLimit);
                                break;
                            case MANY_TO_MANY:
                                $mappingOrder =  $val['mapping_order'];
                                $mappingLimit =  $val['mapping_limit'];
                                $mappingRelationFk = $val['relation_foreign_key'];
                                if(empty($mappingRelationFk)) {
                                	$mappingRelationFk   = $dao->name.'_id';
                                }
                                $mappingRelationTable  =  $val['relation_table'];
                                if(empty($mappingRelationTable)) {
                                	$mappingRelationTable  =  $this->getRelationTableName($dao);
                                }
								$sql = "SELECT b.* FROM {$mappingRelationTable} AS a, ".$mappingDao->getTableName()." AS b WHERE a.{$mappingRelationFk} = b.{$dao->pk} AND a.{$mappingCondition}";
                                if(!empty($mappingOrder)) {
                                	$sql .= ' ORDER BY '.$mappingOrder;
                                }
                                if(!empty($mappingLimit)) {
                                	$sql .= ' LIMIT '.$mappingLimit;
                                }
                                $rs   =  $this->query($sql);
                                $relation   =  $dao->rsToVoList($rs,'','',false);
                                break;
                        }
                        $result[$mappingName] = $relation;
                     }                	
                }
            }
        }
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 查找记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition  条件
     * @param string $table  数据表名
     * @param string $fields  需要显示的字段
     * @param string $order  排序字段
     * @param string $limit  
     * @param string $group  
     * @param string $having 
     +----------------------------------------------------------
     * @return VoList
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function findAll($condition='',$fields='*',$order='',$limit='',$group='',$having='',$relation=false)
    {
        //数据集唯一标识符
        $identify   =   to_guid_string(func_get_args());
        if(C('DATA_CACHE_ON') && $this->cacheQuery) { //启用数据动态缓存
            $voList = $this->getCacheVoList($identify);
            if (false !== $voList) {
                return $voList;
            }
        }
        $rs = $this->db->find($condition,$this->getTableName(),$fields,$order,$limit,$group,$having,$this->cacheQuery);
        $voList  = $this->rsToVoList($rs,'','',$relation);
        if(C('DATA_CACHE_ON'))
            $this->cacheVoList($voList,$identify);
        return $voList;
    }

    // 查询记录并返回相应的关联记录
    function xFindAll($condition='',$fields='*',$order='',$limit='',$group='',$having='') 
    {
    	return $this->findAll($condition,$fields,$order,$limit,$group,$having,true);
    }

    /**
     +----------------------------------------------------------
     * 查询SQL语句
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function query($sql)
    {
        if(empty($sql)) return false;
        $identify   = md5($sql);
        if(C('DATA_CACHE_ON') && $this->cacheQuery) {//启用动态数据缓存
        	$result =   $this->getCacheResultSet($identify);
            if(false !== $result) {
            	return $result;
            }
        }
        $result =   $this->db->query($sql);
        if(C('DATA_CACHE_ON'))
            $this->cacheResultSet($result,$identify);
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 执行SQL语句
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function execute($sql)
    {
        if(!empty($sql)) {
            $result =   $this->db->execute($sql);
            return $result;
        }else {
        	return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 获取一条记录的某个字段值
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getOne($field,$condition='')
    {
        $result    =   $this->find($condition);
        if(!empty($result)) {
            return $result[$field];
        }else {
            return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 获取没有vo对象的查询结果中的某个字段值
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param ResultSet $rs  查询结果
     * @param string $col  列名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCol($rs,$col) 
    {
        if(!empty($rs)) {
            $result =   $rs->get(0);
            $field  =   $result[$col];
            return $field;         	
        }else {
        	return NULL;
        }
    }

    /**
     +----------------------------------------------------------
     * 统计满足条件的记录个数
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCount($condition='',$field='*')
    {
        $fields = 'count('.$field.') as count';
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        if($rs && $rs->size()>0) {
        	return $this->getCol($rs,'count');
        }else {
        	return 0;
        }
    }

    /**
     +----------------------------------------------------------
     * 取得某个字段的最大值
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getMax($field,$condition='')
    {
        $fields = 'MAX('.$field.') as max';
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        if($rs) {
        	return $this->getCol($rs,'max');
        }else {
        	return NULL;
        }
    }

    /**
     +----------------------------------------------------------
     * 取得某个字段的最小值
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getMin($field,$condition='')
    {
        $fields = 'MIN('.$field.') as min';
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        if($rs) {
        	return $this->getCol($rs,'min');
        }else {
        	return NULL;
        }
    }

    /**
     +----------------------------------------------------------
     * 统计某个字段的总和
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param string $table  数据表名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getSum($field,$condition='')
    {
        $fields = 'SUM('.$field.') as sum';
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        if($rs) {
        	return $this->getCol($rs,'sum');
        }else {
        	return NULL;
        }
    }

    /**
     +----------------------------------------------------------
     * 查询符合条件的第N条记录
     * 0 表示第一条记录 -1 表示最后一条记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param integer $position 记录位置
     * @param mixed $condition 条件
     * @param string $table  数据表名
     * @param string $fields 字段名，默认为*
     * @param boolean $relation 是否读取关联
     +----------------------------------------------------------
     * @return Vo | null
     +----------------------------------------------------------
     */
    function getN($position=0,$condition='',$fields='*',$relation=false)
    {
        $rs = $this->db->find($condition,$this->getTableName(),$fields,NULL,NULL,NULL,NULL);
        if($rs && $rs->size()>0) {
			if($position<0) {
				// 逆序查找
				$position = $rs->size()-abs($position);
			}
			if($position<$rs->size()) {
				$vo  =  $this->rsToVo($rs->get($position),'','',$relation);
				return $vo;
			}else {
				return null;
			}
        }else {
            return null;
        }
    }

	// 获取第一条记录
	function getFirst($condition='',$fields='*',$relation=false) {
		return $this->getN(0,$condition,$this->getTableName(),$fields,$relation);
	}

	// 获取最后一条记录
	function getLast($condition='',$fields='*',$relation=false) {
		return $this->getN(-1,$condition,$this->getTableName(),$fields,$relation);
	}

    /**
     +----------------------------------------------------------
     * 缓存VoList对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param VoList $voList VoList对象实例
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cacheVoList($voList,$identify='') 
    {
        $guid   = $this->name.'List_'.$identify;
        $vo  =  $voList->get(0);
        if(empty($vo->_info)) {
			S($guid,$voList);
        }else {
            // 读取vo对象的缓存属性
            // 进行相应的缓存
            switch($vo->_info['cache']) {
            	case Think_CACHE_STATIC:
                     // 永久缓存
                    file_put_contents(TEMP_PATH.$guid,serialize($voList));
                    break;
                case Think_CACHE_NO:
                	// 禁止缓存
                	break;
                case Think_CACHE_DYNAMIC:
                    // 动态缓存
					S($guid,$voList);
                	break;
            }        	
        }
    }

    /**
     +----------------------------------------------------------
     * 缓存Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Vo $vo Vo对象实例
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cacheVo($vo,$identify='') 
    {
        if(empty($identify)) {
        	$identify   =  $vo[$this->pk];
        }
    	$guid    = $this->name.'_'.$identify;
        if(empty($this->_info)) {
			S($guid,$vo);
        }else {
            // 读取vo对象的缓存属性
            // 进行相应的缓存
            switch($this->_info['cache']) {
            	case Think_CACHE_STATIC:
                     // 永久缓存
                    file_put_contents(TEMP_PATH.$guid,serialize($vo));
                    break;
                case Think_CACHE_NO:
                	// 禁止缓存
                	break;
                case Think_CACHE_DYNAMIC:
                    // 动态缓存
					S($guid,$vo);
                	break;
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 缓存ResultSet对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param ResultSet $resultSet 插件结果集
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cacheResultSet($resultSet,$identify) 
    {
        $guid    = 'ResultSet_'.$identify;
		S($guid,$resultSet);
    }

    /**
     +----------------------------------------------------------
     * 获取缓存ResultSet对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCacheResultSet($identify) 
    {
        $guid    = 'ResultSet_'.$identify;
        return S($guid);   	
    }

    /**
     +----------------------------------------------------------
     * 获取缓存的Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $voClass Vo对象名
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCacheVo($identify='') 
    {
        $guid    = $this->name.'_'.$identify;
        //判断是否存在永久缓存
        if(file_exists(TEMP_PATH.$guid)) {
            $vo =   unserialize(file_get_contents(TEMP_PATH.$guid));
        }else {
			$vo	=	S($guid);
        }
		$this->cacheLockVersion($vo);
        return $vo;
    }

    /**
     +----------------------------------------------------------
     * 获取缓存的VoList对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $voClass Vo对象名
     * @param string $identify 缓存标识
     +----------------------------------------------------------
     * @return VoList
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCacheVoList($identify='') 
    {
        $guid    = $this->name.'List_'.$identify;
		$voList	=	S($guid);
        return $voList;
    }

	// 记录乐观锁
	function cacheLockVersion($vo) {
		if($this->optimLock) {
			if(isset($vo[$this->optimLock]) && isset($vo[$this->pk])) {
				Session::set($this->name.'_'.$vo[$this->pk].'_lock_version',$vo[$this->optimLock]);
			}
		}
	}

    /**
     +----------------------------------------------------------
     * 把一条查询结果转换为Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Result $result 记录
     * @param string $voClass Vo对象名
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function rsToVo($result,$relation=false) 
    {
		// 记录乐观锁
		$this->cacheLockVersion($result);

	   // 获取关联
       if( $relation ) {
           $type = isset($relation['type'])?$relation['type']:'';
           $name   =  isset($relation['name'])?$relation['name']:'';
       	   $result  =  $this->getRelation($result,$type,$name);
       }
       return auto_charset($result,C('DB_CHARSET'),C('TEMPLATE_CHARSET'));
    }

    /**
     +----------------------------------------------------------
     * 把查询记录集转换为VoList对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Result $resultSet 记录集
     * @param string $voClass Vo对象名
     +----------------------------------------------------------
     * @return VoList
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function rsToVoList($resultSet,$relation=false) 
    {
       if( $relation) {
           $type = isset($relation['type'])?$relation['type']:'';
           $name   =  isset($relation['name'])?$relation['name']:'';
       }
		if($resultSet) {
			$list  = $resultSet->toArray();
			if( $relation ) {
				foreach($list as $key=>$val) {
					$val  = $this->getRelation($val,$type,$name);
					$list[$key] = $val;
				}
			}            	
		}else {
			$list  = array();
		}
        return auto_charset($list,C('DB_CHARSET'),C('TEMPLATE_CHARSET'));
    }

    /**
     +----------------------------------------------------------
     * 根据表单创建Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $type 创建类型
     +----------------------------------------------------------
     * @return Vo
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function createVo($type='add')
    {
        if ( strtolower($type) == "add" ) { //新增
            $vo = array(); //新建Vo对象
        } else { //编辑
            //根据编号获取Vo对象
            $value   = $_REQUEST[$this->pk];
			$rs		= $this->db->find($this->pk."='{$value}'");
			if($rs && $rs->size()>0) {
				$vo = $rs->get(0); 
			}else {
				return false;
			}   
        }

        //给表单对象赋值
        foreach ( $this->fields as $key=>$name){
			if(substr($key,0,1)=='_') continue;
            $val = isset($_REQUEST[$name])?$_REQUEST[$name]:null;
            //保证赋值有效
            if(!is_null($val) ){
				// 首先保证表单赋值
                $vo[$name] = $val;
            }elseif(	(strtolower($type) == "add" && in_array($name,$this->autoCreateTimestamps)) || 
						(strtolower($type) == "edit" && in_array($name,$this->autoUpdateTimestamps)) ){ 
				// 自动保存时间戳
				$vo[$name] = time();
			}elseif($this->optimLock && $name==$this->optimLock ){
				// 自动保存乐观锁
				if(strtolower($type) == "add" ) {
					$vo[$name] = 0;
				}else {
					$vo[$name] += 1 ;
				}
			}elseif(strtolower($type) == "edit"){
				unset($vo[$name]);
			}
        }

        // 属性验证
        if(isset($this->_validate)) {
            // 如果设置了Vo验证
            // 则进行数据验证
            import("ORG.Text.Validation");
        	$validation = Validation::getInstance();
            foreach($this->_validate as $key=>$val) {
				if(isset($val[3]) && $val[3]== MUST_TO_VALIDATE) {
					// 必须验证字段
					if(isset($val[4]) && $val[4] && function_exists($val[1]) ) {
						// 使用函数验证是否相等
						if(!$val[1]($vo[$val[0]])) {
							$this->error = $val[2];
							return false;
						}
					}elseif(!$validation->check($vo[$val[0]],$val[1])) {
						$this->error    =   $val[2];
						return false;
					} 
				}elseif(isset($vo[$val[0]])){
					// 默认表单有设置才验证
					if(isset($val[4]) && $val[4] && function_exists($val[1]) ) {
						if(!$val[1]($vo[$val[0]])) {
							$this->error = $val[2];
							return false;
						}
					}elseif( !$validation->check($vo[$val[0]],$val[1])) {
						$this->error    =   $val[2];
						return false;
					} 
				}
            }
        }

		// Vo自动填充
		if(!empty($this->_auto)) {
			foreach ($this->_auto as $auto){
				if(in_array($auto[0],$this->fields)) {
					if(empty($auto[2])) $auto[2] = 'ADD';// 默认为新增的时候自动填充
					if( (strtolower($type) == "add"  && $auto[2] == 'ADD') || 	(strtolower($type) == "edit"  && $auto[2] == 'UPDATE') || $auto[2] == 'ALL') 
					{
						if( function_exists($auto[1])) {
							// 如果定义为函数则调用
							$vo[$auto[0]] = $auto[1]($vo[$auto[0]]);
						}else {
							// 否则作为字符串处理
							$vo[$auto[0]] = $auto[1];
						}
					}
				}
			}
		}
        return $vo;
    }

    /**
     +----------------------------------------------------------
     * 得到完整的数据表名
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getTableName()
    {
        if(empty($this->tableName)) {
            $tableName  = !empty($this->tablePrefix) ? $this->tablePrefix.'_' : '';
            $tableName .= !empty($this->name) ? $this->name : strtolower(substr($this->__toString(),0,-3));
            $tableName .= !empty($this->tableSuffix) ? '_'.$this->tableSuffix : '';    
            $this->tableName    =   strtolower($tableName);
        }
        return $this->tableName;
    }

    /**
     +----------------------------------------------------------
     * 得到关联的数据表名
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getRelationTableName($relationDao)
    {
        $relationTable  = !empty($this->tablePrefix) ? $this->tablePrefix.'_' : '';
        $relationTable .= !empty($this->name) ? $this->name : strtolower(substr($this->__toString(),0,-3));
        $relationTable .= '_'.strtolower(substr($relationDao->__toString(),0,-3));
        $relationTable .= !empty($this->tableSuffix) ? '_'.$this->tableSuffix : '';    
        return $relationTable;
    }

    /**
     +----------------------------------------------------------
     * 启动事务
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function startTrans()
    {
        $this->commit();
        $this->db->autoCommit = 0;
        return ;
    }

    /**
     +----------------------------------------------------------
     * 提交事务
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function commit()
    {
        return $this->db->commit();
    }

    /**
     +----------------------------------------------------------
     * 事务回滚
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function rollback()
    {
        return $this->db->rollback();
    }

};
?>