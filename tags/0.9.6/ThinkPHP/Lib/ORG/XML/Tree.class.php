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
// $Id: Tree.class.php 33 2007-02-25 07:06:02Z liu21st $


/**
 +------------------------------------------------------------------------------
 * Tree 实现类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Tree.class.php 33 2007-02-25 07:06:02Z liu21st $
 +------------------------------------------------------------------------------
 */

	
	/**
	* PHP4 Tree class
	*
	*<code>
	* //create a root node by passing a variable with value null, 
	* //returns a reference to the root node
	*$null = null;
	*$data = array("dummykey"=>"dummyvalue");
	*$tree = new Tree($null);
	*
	* // you can add your data load also:
	* //$tree = new Tree($null,$data);
	*
	* // create a node, $node is a reference to a Tree object, use '&'!
	*$node = &$tree->addChild($data);
	* // create another node as subnode of $node
	*$subnode = &$node->addChild($data);
	*
	* // get the level of this node:
	*echo 'subnode is on level'.$subnode->getLevel();
	*
	* // have a look on the structure
	*echo '<pre>';
	*$tree->echoStructure();
	*echo '</pre>';
	*
	* // delete a node
	*$subnode->delete();
	*
	*</code>
	*
	* @author Martin Weis <tree@datenroulette.de>
   * @license http://opensource.org/licenses/gpl-license.php GNU Public License
   * @version early release, version 0.5
   * @copyright Copyright 2005, Martin Weis	
   */	
class Tree extends Base
{

    /**
     +----------------------------------------------------------
     * 节点编号
     +----------------------------------------------------------
     * @var integer
     * @access private
     +----------------------------------------------------------
     */
	var $_id;

    /**
     +----------------------------------------------------------
     * 节点级别
     +----------------------------------------------------------
     * @var integer
     * @access private
     +----------------------------------------------------------
     */
	var $_level;

    /**
     +----------------------------------------------------------
     * 上级节点
     +----------------------------------------------------------
     * @var Tree
     * @access private
     +----------------------------------------------------------
     */
	var $_parent;

    /**
     +----------------------------------------------------------
     * 子节点数组
     +----------------------------------------------------------
     * @var Array
     * @access private
     +----------------------------------------------------------
     */
    var $_children;

    /**
     +----------------------------------------------------------
     * 节点数据
     +----------------------------------------------------------
     * @var mixed
     * @access private
     +----------------------------------------------------------
     */
    var $data;

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed  $_parent  父节点
     * @param string $data  初始化节点数据
     +----------------------------------------------------------
     */
    function __construct($data=null,&$_parent=null) {
        
        if ($_parent===null){
            // 根节点
            $this->_level=0;
        }
        $this->data=$data;
        $this->_children=array(); 
        $this->_parent=&$_parent;
    }
	
    /**
     +----------------------------------------------------------
     * 添加子节点
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 节点数据
     +----------------------------------------------------------
     * @return Tree
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function &addChild($data) {
        
        $this->_children[]=&new Tree($data,&$this);
        end($this->_children);
        $key=key($this->_children);
        $this->_children[$key]->_setId($key);
        $this->_children[$key]->level = $this->_level + 1;
        return $this->_children[$key];
    }
	
    /**
     +----------------------------------------------------------
     * 移除子节点
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param integer $id 节点编号
     +----------------------------------------------------------
     * @return Boolean
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */	
    function removeChild($id) {
        if (!array_key_exists($id,$this->_children)){
            return false;
        }
        else{
            unset($this->_children[$id]);
            return true;
        }
    }

    /**
     +----------------------------------------------------------
     * 删除当前节点
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return Void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function delete(){
        if (!$this->isRoot){
            // remove using the _parent
            return $this->_parent->removeChild($this->_id);
        }
        else {
            // in the root node unset object
            unset ($this); //->_children=array();
            return true;
        }
    }

    /**
     +----------------------------------------------------------
     * 取得当前节点编号
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	function getId(){
		return $this->_id;
	}
	
    /**
     +----------------------------------------------------------
     * 设置当前节点编号
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param integer $id 节点编号
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	function _setId($id){
		 $this->_id=$id;
	}
	
    /**
     +----------------------------------------------------------
     * 取得子节点个数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 节点数据
     +----------------------------------------------------------
     * @return Integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	function numChildren(){
		return count($this->_children);
	}

    /**
     +----------------------------------------------------------
     * 取得子节点的编号数组
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 节点数据
     +----------------------------------------------------------
     * @return Array
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	function getChildrenIds(){
		return array_keys($this->_children);
	}
	
    /**
     +----------------------------------------------------------
     * 是否根节点
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 节点数据
     +----------------------------------------------------------
     * @return Boolean
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function isRoot(){
        if ($this->_parent==null){
            return true;
        }
        else{
            return false;
        }
    }
	
    /**
     +----------------------------------------------------------
     * 取得根节点
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 节点数据
     +----------------------------------------------------------
     * @return Tree
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function &getRoot(){
        $tmp=&$this;
        while (!$tmp->isRoot()){
            // iterate through parents, add IDs to (begin of) array
            $tmp=&$tmp->_parent;
        }
        return $tmp;
    }
	
    /**
     +----------------------------------------------------------
     * 取得当前节点路径
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 节点数据
     +----------------------------------------------------------
     * @return Array
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getPath(){
        $idarray=array();
        $tmp=&$this;
        while (!$tmp->isRoot()){
            // iterate through parents, add IDs to (begin of) array
            array_unshift ($idarray, $tmp->_id);
            $tmp=&$tmp->_parent;
        }
        return $idarray;
	}
	
    /**
     +----------------------------------------------------------
     * 取得当前节点级别
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return Integer
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getLevel(){
        $level=0;
        $tmp=&$this;
        while (!$tmp->isRoot()){
        //echo "adding level for parent, id :".$this->_id;
            $tmp=&$tmp->_parent;
            $level++;
        }
        return $level;
    }
	

    /**
     +----------------------------------------------------------
     * 输出结构
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $pre 节点数据
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function echoStructure($pre='') {
        $str    =   '<pre>';
        for ($i=0;$i<$this->getLevel();$i++){
            $pre.='|';//$this->getLevel();
        }
        $str   .= $pre."+[".$this->_id.']';
        if (is_array($this->data)){
            foreach ($this->data as $key=>$value) {
                     $str .= '('.$key.'|'.$value.')';
            }
        }
        $str   .= "\n</pre>";
        if ($this->numChildren()>0){
            foreach ($this->_children as $child) {
                $child->echoStructure($prefix);
            }
        }
        echo($str);
    }

}

?>

