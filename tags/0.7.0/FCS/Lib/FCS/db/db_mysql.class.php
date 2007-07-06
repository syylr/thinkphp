<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: db_mysql.class.php								  |
| 功能: MySQL数据库操作类								  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.db.Db");
import("FCS.db.ReusltSet");
import("FCS.util.Log");

Class db_mysql extends Db{

	// +----------------------------------------
	// |	架构函数 读取数据库配置信息
	// +----------------------------------------
	function __construct($config){	
		if ( !extension_loaded('mysql') ) {	
			ThrowException('系统不支持mysql');
		}
		$this->Username = $config['username'];
		$this->Password = $config['password'];
		$this->Database = $config['database'];
		$this->Hostname = $config['hostname'];
		$this->Hostport = $config['hostport'];
	}

	// +----------------------------------------
	// |	连接数据库
	// +----------------------------------------
	Function connect() {
		if ( !$this->LinkID ) {
			$conn = $this->Pconnect ? 'mysql_pconnect':'mysql_connect';
			$this->LinkID = @$conn( $this->Hostname . ':' . $this->Hostport, $this->Username, $this->Password);

			if ( !$this->LinkID) {
				ThrowException(mysql_error());
				Return False;
			}

			if ( !@mysql_select_db($this->Database, $this->LinkID) ) {
				ThrowException($this->error());
				Return False;
			}
			$this->DbVersion = mysql_get_server_info($this->LinkID);
			if ($this->DbVersion >= "4.1") { 
				//使用UTF8存取数据库 需要mysql 4.1.0以上支持
				@mysql_query("SET NAMES 'utf8'", $this->LinkID);
			}
			unset($this->Username,$this->Password,$this->Database,$this->Hostname,$this->Hostport);
		}
		Return $this->LinkID;
	}

	// +----------------------------------------
	// |	释放查询结果
	// +----------------------------------------
	Function free() {
		@mysql_free_result($this->QueryID);
		$this->QueryID = 0;
	}

	// +----------------------------------------
	// | 执行查询 针对 SELECT, SHOW 等指令
	// +----------------------------------------
	Function query($str='') {
		if ( !$this->LinkID ) Return False;
		if ( $str != '' ) $this->QueryStr = $str;
        if (!$this->AutoCommit && $this->isMainIps($this->QueryStr)) {
			//数据rollback 支持
            if ($this->TransTimes == 0) {
                @mysql_query('SET AUTOCOMMIT=0', $this->LinkID);
                @mysql_query('BEGIN', $this->LinkID);
            }
            $this->TransTimes++;
        }else {
			//释放前次的查询结果
			if ( $this->QueryID ) {	$this->free();	}
		}
		$this->escape_string($this->QueryStr);
		$this->QueryTimes ++;
		if ( $this->Debug ) Log::Write(" SQL = ".$this->QueryStr,WEB_LOG_DEBUG);
		$this->QueryID = @mysql_query($this->QueryStr, $this->LinkID);
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		} else {
			$this->NumRows = mysql_num_rows($this->QueryID);
            $this->NumCols = mysql_num_fields($this->QueryID);
			$this->ResultSet = $this->getAll();
			$this->Fields  = $this->getFields();
			Return new ResultSet($this->ResultSet);
		}
	}

	// +----------------------------------------
	// | 执行语句 针对 INSERT, UPDATE 以及DELETE
	// +----------------------------------------
	Function execute($str='') {
		if ( !$this->LinkID ) Return False;
		if ( $str != '' ) $this->QueryStr = $str;
		if (!$this->AutoCommit && $this->isMainIps($this->QueryStr)) {
			//数据rollback 支持
            if ($this->TransTimes == 0) {
                @mysql_query('SET AUTOCOMMIT=0', $this->LinkID);
                @mysql_query('BEGIN', $this->LinkID);
            }
            $this->TransTimes++;
        }else {
			//释放前次的查询结果
			if ( $this->QueryID ) {	$this->free();	}
		}
		$this->escape_string($this->QueryStr);
		$this->WriteTimes ++;
		if ( $this->Debug ) Log::Write(" SQL = ".$this->QueryStr,WEB_LOG_DEBUG);
		if ( !@mysql_query($this->QueryStr, $this->LinkID) ) {
			ThrowException($this->error());
			Return False;
		} else {
			$this->NumRows = mysql_affected_rows($this->LinkID);
            $this->LastInsID = mysql_insert_id($this->LinkID);
			Return $this->NumRows;
		}
	}

	// +----------------------------------------
	// | 执行语句 针对非AutoCommit状态
	// +----------------------------------------
    function commit()
    {
        if ($this->TransTimes > 0) {
            $result = @mysql_query('COMMIT', $this->LinkID);
            $result = @mysql_query('SET AUTOCOMMIT=1', $this->LinkID);
            $this->TransTimes = 0;
			if(!$result){
				ThrowException($this->error());
				return False;
			}
        }
        return true;
    }

	// +----------------------------------------
	// | 执行语句回滚
	// +----------------------------------------
    function rollback()
    {
        if ($this->TransTimes > 0) {
            $result = @mysql_query('ROLLBACK', $this->LinkID);
            $result = @mysql_query('SET AUTOCOMMIT=1', $this->LinkID);
            $this->TransTimes = 0;
			if(!$result){
				ThrowException($this->error());
				return False;
			}
        }
        return True;
    }

	// +----------------------------------------
	// |  获得下一条查询结果 简易数据集获取方法
	// |  当前查询结果放到 Result 数组中
	// +----------------------------------------
	Function next() {
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		}
		if($this->ResultType==1){
			// 返回对象集
			$this->Result = @mysql_fetch_object($this->QueryID);
			$stat = is_object($this->Result);
		}else{
			// 返回数组集
			$this->Result = @mysql_fetch_array($this->QueryID,MYSQL_ASSOC);
			$stat = is_array($this->Result);
		}
		Return $stat;
	}

	// +----------------------------------------
	// |  获得某一条查询结果 从0开始
	// |  查询结果放到 Result 数组中
	// +----------------------------------------
	Function getRow($seek=0,$str = NULL) {
		if ($str) $this->query($str);
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		}
		if(mysql_data_seek($this->QueryID,$seek)){
			if($this->ResultType==1){
				//返回对象集
				$Result = @mysql_fetch_object($this->QueryID);
			}else{
				// 返回数组集
				$Result = @mysql_fetch_array($this->QueryID,MYSQL_ASSOC);
			}
		}
		Return $Result;
	}

	// +----------------------------------------
	// |  获得所有的查询数据
	// |  查询结果放到 ResultSet 数组中
	// +----------------------------------------
	Function getAll($ResultType=NULL) {
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		}
		mysql_data_seek($this->QueryID,0);
		if(!is_null($ResultType)){ $this->ResultType = $ResultType; }
		//返回数据集
		for($i=0;$i<$this->NumRows ;$i++ ){
			$row=mysql_fetch_array($this->QueryID, MYSQL_ASSOC);
			for($j=0;$j<$this->NumCols;$j++){
				$name = mysql_field_name($this->QueryID, $j);
				if($this->ResultType==1){//返回对象集
					$Result[$i]->$name = $row[$name];
				}else{//返回数组集
					$Result[$i][$name] = $row[$name];
				}
			}
		}
		mysql_data_seek($this->QueryID,0);
		Return $Result;
	}

	// +----------------------------------------
	// |	取得查询字段信息
	//	blob:         $meta->blob
	//	max_length:   $meta->max_length
	//	multiple_key: $meta->multiple_key
	//	name:         $meta->name
	//	not_null:     $meta->not_null
	//	numeric:      $meta->numeric
	//	primary_key:  $meta->primary_key
	//	table:        $meta->table
	//	type:         $meta->type
	//	unique_key:   $meta->unique_key
	//	unsigned:     $meta->unsigned
	//	zerofill:     $meta->zerofill
	// +----------------------------------------
	function getFields(){
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		}
		for($i=0; $i < $this->NumCols; $i++){
			$Fields[$i] = mysql_fetch_field($this->QueryID,$i);
		}
		return $Fields;
 
	}

	// +----------------------------------------
	// |	关闭数据库
	// +----------------------------------------
    function close() { 
		if (!empty($this->QueryID))
			@mysql_free_result($this->QueryID);
        if (!@mysql_close($this->LinkID)){
			ThrowException($this->error());
		}
		$this->LinkID = 0;
    } 

	// +----------------------------------------
	// |	数据库错误信息
	// +----------------------------------------
	function error() {
		$this->error = mysql_error($this->LinkID);
		if($this->QueryStr!=''){
			$this->error .= "\n [ SQL语句 ] : ".$this->QueryStr;
		}
		return $this->error;
	}

	// +----------------------------------------
	// |	查询安全过滤
	// +----------------------------------------
    function escape_string(&$str) { 
		$str = str_replace("&quot;", "\"", $str);
		$str = str_replace("&lt;", "<", $str);
		$str = str_replace("&gt;", ">", $str);
		$str = str_replace("&amp;", "&", $str);
		if (get_magic_quotes_gpc()) $str = stripslashes($str);
        //return mysql_real_escape_string($string, $this->LinkID); 
    } 

	//+----------------------------------------
	//|	析构函数
	//+----------------------------------------
	function __destruct(){

	}
}
?>