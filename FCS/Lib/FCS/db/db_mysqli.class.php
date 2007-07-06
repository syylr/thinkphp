<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: db_mysqli.class.php								  |
| 功能: MySQLi数据库操作类								  |
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

Class db_mysqli extends Db{

	// +----------------------------------------
	// |	架构函数 读取数据库配置信息
	// +----------------------------------------
	function __construct($config){	
		if ( !extension_loaded('mysqli') ) {	
			ThrowException('系统不支持mysqli');
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
			$this->LinkID = mysqli_connect($this->Hostname . ':' . $this->Hostport, $this->Username, $this->Password,$this->Database);

			if ( !$this->LinkID) {
				ThrowException(mysqli_connect_error());
				Return False;
			}
			if($this->AutoCommit){
				mysqli_autocommit($this->LinkID, True);
			}else {
				mysqli_autocommit($this->LinkID, False);
			}
			$this->DbVersion = mysqli_get_server_info($this->LinkID);
			if ($this->DbVersion >= "4.1") { 
				//使用UTF8存取数据库 需要mysql 4.1.0以上支持
				@mysqli_query( $this->LinkID,"SET NAMES 'utf8'");
			}
			unset($this->Username,$this->Password,$this->Database,$this->Hostname,$this->Hostport);
		}
		Return $this->LinkID;
	}

	// +----------------------------------------
	// |	释放查询结果
	// +----------------------------------------
	Function free() {
		@mysqli_free_result($this->QueryID);
		$this->QueryID = 0;
	}

	// +----------------------------------------
	// | 执行查询 针对 SELECT, SHOW 等指令
	// +----------------------------------------
	Function query($str='') {
		if ( !$this->LinkID ) Return False;
		if ( $str != '' ) $this->QueryStr = $str;
		//释放前次的查询结果
		if ( $this->QueryID ) {	$this->free();	}
		$this->QueryStr = $this->escape_string($this->QueryStr);
		$this->QueryTimes ++;
		if ( $this->Debug ) Log::Write(" SQL = ".$this->QueryStr,WEB_LOG_DEBUG);
		$this->QueryID = @mysqli_query($this->LinkID,$this->QueryStr );
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		} else {
			$this->NumRows = mysqli_num_rows($this->QueryID);
            $this->NumCols = mysqli_num_fields($this->QueryID);
			$this->ResultSet = $this->getAllRecord();
			Return new ResultSet($this->ResultSet);
		}
	}

	// +----------------------------------------
	// | 执行语句 针对 INSERT, UPDATE 以及DELETE
	// +----------------------------------------
	Function execute($str='') {
		if ( !$this->LinkID ) Return False;
		if ( $str != '' ) $this->QueryStr = $str;
		//释放前次的查询结果
		if ( $this->QueryID ) {	$this->free();	}
		$this->QueryStr = $this->escape_string($this->QueryStr);
		$this->WriteTimes ++;
		if ( $this->Debug ) Log::Write(" SQL = ".$this->QueryStr,WEB_LOG_DEBUG);
		if ( !@mysqli_query($this->LinkID,$this->QueryStr) ) {
			ThrowException($this->error());
			Return False;
		} else {
			$this->NumRows = mysqli_affected_rows($this->LinkID);
            $this->LastInsID = mysqli_insert_id($this->LinkID);
			Return $this->NumRows;
		}
	}

	// +----------------------------------------
	// | 执行语句 针对非autocommit状态
	// +----------------------------------------
    function commit()
    {
		return mysqli_commit($this->LinkID);
    }

	// +----------------------------------------
	// | 执行语句回滚
	// +----------------------------------------
    function rollback()
    {
		return mysqli_rollback($this->LinkID);
    }

	// +----------------------------------------
	// |  获得下一条查询结果 简易数据集获取方法
	// |  查询结果放到 Result 数组中
	// +----------------------------------------
	Function next() {
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		}
		if($this->ResultType==1){
			// 返回对象集
			$this->Result = @mysqli_fetch_object($this->QueryID);
			$stat = is_object($this->Result);
		}else{
			// 返回数组集
			$this->Result = @mysqli_fetch_array($this->QueryID,MYSQLI_ASSOC);
			$stat = is_array($this->Result);
		}
		Return $stat;
	}

	// +----------------------------------------
	// |	获得一条查询结果
	// +----------------------------------------
	Function getRow($seek=0,$str = NULL) {
		if ($str) $this->query($str);
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		}
		if(mysqli_data_seek($this->QueryID,$seek)){
			if($this->ResultType==1){
				//返回对象集
				$Result = @mysqli_fetch_object($this->QueryID);
			}else{
				// 返回数组集
				$Result = @mysqli_fetch_array($this->QueryID,MYSQLI_ASSOC);
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
		mysqli_data_seek($this->QueryID,0);
		if(!is_null($ResultType)){ $this->ResultType = $ResultType; }
		//返回数据集
		for($i=0;$i<$this->NumRows ;$i++ ){
			$row=mysqli_fetch_array($this->QueryID, MYSQLI_ASSOC);
			for($j=0;$j<$this->NumCols;$j++){
				$name = mysqli_field_name($this->QueryID, $j);
				if($this->ResultType==1){//返回对象集
					$Result[$i]->$name = $row[$name];
				}else{//返回数组集
					$Result[$i][$name] = $row[$name];
				}
			}
		}
		mysqli_data_seek($this->QueryID,0);
		Return $Result;
	}

	// +----------------------------------------
	// |	关闭数据库
	// +----------------------------------------
    function close() { 
		if (!empty($this->QueryID))
			@mysqli_free_result($this->QueryID);
        if (!@mysqli_close($this->LinkID)){
			ThrowException($this->error());
		}
		$this->LinkID = 0;
    } 

	// +----------------------------------------
	// |	数据库错误信息
	// +----------------------------------------
	function error() {
		$this->error = mysqli_error($this->LinkID);
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
        //$str = mysqli_real_escape_string($this->LinkID,$str); 
    } 

	//+----------------------------------------
	//|	析构函数
	//+----------------------------------------
	function __destruct(){

	}
}
?>