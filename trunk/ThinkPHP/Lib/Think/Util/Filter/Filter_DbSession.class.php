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

/**
 +------------------------------------------------------------------------------
 * 数据库Session处理过滤器
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Filter_DbSession extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * Session有效时间
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
   var $lifeTime=''; 
    /**
     +----------------------------------------------------------
     * session保存的数据库名
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
   var $sessionTable='';

    /**
     +----------------------------------------------------------
     * 数据库句柄
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
   var $dbHandle; 

    /**
     +----------------------------------------------------------
     * 打开Session 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $savePath 
     * @param mixed $sessName  
     +----------------------------------------------------------
     */
    function open($savePath, $sessName) { 
       // get session-lifetime 
       $this->lifeTime = C('SESSION_EXPIRE'); 
	   $this->sessionTable	 =	 C('SESSION_TABLE');
       $dbHandle = mysql_connect(C('DB_HOST'),C('DB_USER'),C('DB_PWD')); 
       $dbSel = mysql_select_db(C('DB_NAME'),$dbHandle); 
       // return success 
       if(!$dbHandle || !$dbSel) 
           return false; 
       $this->dbHandle = $dbHandle; 
       return true; 
    } 

    /**
     +----------------------------------------------------------
     * 关闭Session 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
   function close() { 
       $this->gc(ini_get('session.gc_maxlifetime')); 
       // close database-connection 
       return mysql_close($this->dbHandle); 
   } 
    /**
     +----------------------------------------------------------
     * 读取Session 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sessID 
     +----------------------------------------------------------
     */
   function read($sessID) { 
       // fetch session-data 
       $res = mysql_query("SELECT session_data AS d FROM ".$this->sessionTable." WHERE session_id = '$sessID'   AND session_expires >".time(),$this->dbHandle); 
       // return data or an empty string at failure 
       if($res) {
           $row = mysql_fetch_assoc($res);
           $data = $row['d'];
            if( function_exists('gzcompress')) {
                //启用数据压缩
                //$data   =   gzuncompress($data);
            }
           return $data; 
       }
       return ""; 
   } 

    /**
     +----------------------------------------------------------
     * 写入Session 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sessID 
     * @param String $sessData  
     +----------------------------------------------------------
     */
   function write($sessID,$sessData) { 
       // new session-expire-time 
       $newExp = time() + $this->lifeTime; 
        if( function_exists('gzcompress')) {
            //数据压缩
            //$sessData   =   gzcompress($sessData,3);
        }
       // is a session with this id in the database? 
       $res = mysql_query("SELECT * FROM ".$this->sessionTable." WHERE session_id = '$sessID'",$this->dbHandle); 
       // if yes, 
       if(mysql_num_rows($res)) { 
           // ...update session-data 
           mysql_query("UPDATE ".$this->sessionTable."  SET session_expires = '$newExp', session_data = '$sessData' WHERE session_id = '$sessID'",$this->dbHandle); 
           // if something happened, return true 
           if(mysql_affected_rows($this->dbHandle)) 
               return true; 
       } 
       // if no session-data was found, 
       else { 
           // create a new row 
           mysql_query("INSERT INTO ".$this->sessionTable." (  session_id, session_expires, session_data)  VALUES( '$sessID', '$newExp',  '$sessData')",$this->dbHandle); 
           // if row was created, return true 
           if(mysql_affected_rows($this->dbHandle)) 
               return true; 
       } 
       // an unknown error occured 
       return false; 
   } 

    /**
     +----------------------------------------------------------
     * 删除Session 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sessID 
     +----------------------------------------------------------
     */
   function destroy($sessID) { 
       // delete session-data 
       mysql_query("DELETE FROM ".$this->sessionTable." WHERE session_id = '$sessID'",$this->dbHandle); 
       // if session was deleted, return true, 
       if(mysql_affected_rows($this->dbHandle)) 
           return true; 
       // ...else return false 
       return false; 
   } 

    /**
     +----------------------------------------------------------
     * Session 垃圾回收
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $sessMaxLifeTime 
     +----------------------------------------------------------
     */
   function gc($sessMaxLifeTime) { 
       // delete old sessions 
       mysql_query("DELETE FROM ".$this->sessionTable." WHERE session_expires < ".time(),$this->dbHandle); 
       // return affected rows 
       return mysql_affected_rows($this->dbHandle); 
   } 

    /**
     +----------------------------------------------------------
     * 打开Session 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $savePath 
     * @param mixed $sessName  
     +----------------------------------------------------------
     */
    function execute() 
    {
    	session_set_save_handler(array(&$this,"open"), 
                         array(&$this,"close"), 
                         array(&$this,"read"), 
                         array(&$this,"write"), 
                         array(&$this,"destroy"), 
                         array(&$this,"gc")); 

    }
}//类定义结束
?>