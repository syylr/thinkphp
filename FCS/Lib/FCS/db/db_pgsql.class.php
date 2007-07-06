<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: db_pgsql.class.php								  |
| 功能: PgSQL数据库操作类								  |
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

Class db_pgsql extends Db{

	// +----------------------------------------
	// |	架构函数 读取数据库配置信息
	// +----------------------------------------
	function __construct($config){	
		if ( !extension_loaded('pgsql') ) {	
			ThrowException('系统不支持pgsql');
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
			$conn = $this->Pconnect ? 'pg_pconnect':'pg_connect';
			$this->LinkID =  $conn(
                'host='      . $this->Hostname .
                ' port='     . $this->Hostport .
                ' dbname='   . $this->Database .
                ' user='     . $this->Username .
                ' password=' . $this->Password
            );

			 if (pg_connection_status($this->LinkID) !== 0)
				ThrowException($this->error(False));
				Return False;
			}
			$pgInfo = pg_version($this->LinkID);
			$this->DbVersion = $pgInfo['server'];
			@pg_query( $this->LinkID,"SET NAMES 'utf8'");
			unset($this->Username,$this->Password,$this->Database,$this->Hostname,$this->Hostport);
		}
		Return $this->LinkID;
	}

	// +----------------------------------------
	// |	释放查询结果
	// +----------------------------------------
	Function free() {
		@pg_free_result($this->QueryID);
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
                @pg_exec($this->LinkID,'begin;');
            }
            $this->TransTimes++;
        }else {
			//释放前次的查询结果
			if ( $this->QueryID ) {	$this->free();	}
		}
		$this->QueryStr = $this->escape_string($this->QueryStr);
		$this->QueryTimes ++;
		if ( $this->Debug ) Log::Write(" SQL = ".$this->QueryStr,WEB_LOG_DEBUG);
		$this->QueryID = @pg_query($this->LinkID,$this->QueryStr );
		if ( !$this->QueryID ) {
			ThrowException($this->error());
			Return False;
		} else {
			$this->NumRows = pg_num_rows($this->QueryID);
            $this->NumCols = pg_num_fields($this->QueryID);
			Return $this->NumRows;
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
                @pg_exec($this->LinkID,'begin;');
            }
            $this->TransTimes++;
        }else {
			//释放前次的查询结果
			if ( $this->QueryID ) {	$this->free();	}
		}

		$this->QueryStr = $this->escape_string($this->QueryStr);
		$this->WriteTimes ++;
		if ( $this->Debug ) Log::Write(" SQL = ".$this->QueryStr,WEB_LOG_DEBUG);
		if ( !@pg_query($this->LinkID,$this->QueryStr) ) {
			ThrowException($this->error());
			Return False;
		} else {
			$this->NumRows = pg_affected_rows($this->QueryID);
            $this->LastInsID = pg_last_oid($this->QueryID);
			Return $this->NumRows;
		}
	}

	// +----------------------------------------
	// | 执行语句 针对非autocommit状态
	// +----------------------------------------
    function commit()
    {
        if ($this->TransTimes > 0) {
            $result = @pg_exec($this->LinkID,'end;');
			if(!$result){
				ThrowException($this->error());
				return False;
			}
            $this->TransTimes = 0;
        }
        return true;
    }

	// +----------------------------------------
	// | 执行语句回滚
	// +----------------------------------------
    function rollback()
    {
        if ($this->TransTimes > 0) {
            $result = @pg_exec($this->LinkID,'abort;');
			if(!$result){
				ThrowException($this->error());
				return False;
			}
            $this->TransTimes = 0;
        }
        return True;
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
		// 查询结果
		if($this->ResultType==1){
			$this->Result = @pg_fetch_object($this->QueryID);
			$stat = is_object($this->Result);
		}else{
			$this->Result = @pg_fetch_assoc($this->QueryID);
			$stat = is_array($this->Result);
		}
		Return $stat;
	}

	// +----------------------------------------
	// |	获得一条查询结果
	// +----------------------------------------
     function getRow($seek=0,$str = NULL) 
		{
            if ($str) $this->query($str);
			if ( !$this->QueryID ) {
				ThrowException($this->error());
				Return False;
			}
			if(pg_result_seek($this->QueryID,$seek)){
				if($this->ResultType==1){
					//返回对象集
					$Result = @pg_fetch_object($this->QueryID);
				}else{
					// 返回数组集
					$Result = @pg_fetch_assoc($this->QueryID);
				}
			}
            return $Result;
            
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
		pg_result_seek($this->QueryID,0);
		if(!is_null($ResultType)){ $this->ResultType = $ResultType; }
		//返回数据集
		for($i=0;$i<$this->NumRows ;$i++ ){
			$row=pg_fetch_assoc($this->QueryID);
			for($j=0;$j<$this->NumCols;$j++){
				$name = pg_field_name($this->QueryID, $j);
				if($this->ResultType==1){//返回对象集
					$Result[$i]->$name = $row[$name];
				}else{//返回数组集
					$Result[$i][$name] = $row[$name];
				}
			}
		}
		pg_result_seek($this->QueryID,0);
		Return $Result;
	}

	// +----------------------------------------
	// |	关闭数据库
	// +----------------------------------------
    function close() { 
		if (!empty($this->QueryID))
			@pg_free_result($this->QueryID);
		if(!@pg_close($this->LinkID)){
			ThrowException($this->error(False));
		}
		$this->LinkID = 0;
    } 

	// +----------------------------------------
	// |	数据库错误信息
	// +----------------------------------------
	function error($Result = True) {
		if($Result){
			$this->error = pg_result_error($this->QueryID);
		}else{
			$this->error = pg_last_error($this->LinkID);
		}
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
	    //$str = pg_escape_string($str); 
    } 

	//+----------------------------------------
	//|	析构函数
	//+----------------------------------------
	function __destruct(){
		
	}
}
?>