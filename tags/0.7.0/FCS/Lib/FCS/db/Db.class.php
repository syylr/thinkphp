<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: Db.class.php									  |
| 功能: 数据库公共类									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.db.ResultSet");

class DB extends Base{

	// +----------------------------------------
	// | 数据库配置
	// | 在dbConfig文件中配置 支持数组和dsn
	// +----------------------------------------
	var $Username	= NULL;			//用户名
	var $Password	= NULL;			//密码
	var $Database	= NULL;			//数据库名
	var $Hostname	= NULL;			//主机地址
	var $Hostport	= NULL;			//主机端口

	// +----------------------------------------
	// | 参数设置
	// +----------------------------------------
	var $DbType		= NULL;			//数据库类型
	var $DbVersion	= NULL;			//数据库版本
	var $AutoFree    = False;		//是否自动释放
	var $AutoCommit  = True;		//是否自动提交
	var $Debug       = True;		//是否显示调试信息
	var $Pconnect	 = True;		//是否使用pconnect
	var $RsBuffer	 = False;		//是否缓存查询结果 暂时没用

	// +----------------------------------------
	// | SQL查询指令
	// +----------------------------------------
	var $QueryStr = '';

	// +----------------------------------------
	// |	查询结果
	// +----------------------------------------
	var $Result = NULL;				//当前结果
	var $ResultSet = NULL;			//结果数据集
	var $ResultType = 0;			//0 返回数组 1 返回对象
	var $Fields = NULL;				//当前查询返回的字段集
	var $LastInsID = NULL;			//最后插入ID
	var $NumRows = 0;				//返回或者影响记录数
	var $NumCols = 0;				//返回字段数
	var $QueryTimes = 0;			//查询次数
	var $WriteTimes = 0;			//写入次数
	var $TransTimes = 0;			//传送次数 用于rollback
	var $error = '';
	// +----------------------------------------
	// |	数据库连接ID和当前查询ID
	// +----------------------------------------
	var $LinkID  = 0;
	var $QueryID = 0;

	// +----------------------------------------
	// |	架构函数
	// +----------------------------------------
	function __construct(){

	}

	// +----------------------------------------
	// |	取得数据库类实例
	// +----------------------------------------
	function &getInstance() {
		static $Instance = array();
		$className = get_class($this);
		if (!isset($Instance[$className])) {
			$Instance[$className] = DB::Connect();
		}
		return $Instance[$className];
	}

	// +----------------------------------------
	// |	加载数据库 支持配置文件或者 DSN
	// |    Load DBMS driver 
	// +----------------------------------------
	function Connect($db_config=''){
		if ( is_string($db_config) && !empty($db_config) ) {
			$db_config = DB::parseDSN($db_config);
		}else if(empty($db_config)){
			$db_config = array (
			'dbms'     => DB_TYPE, 
			'username' => DB_USER, 
			'password' => DB_PWD, 
			'hostname' => DB_HOST, 
			'hostport' => DB_PORT, 
			'database' => DB_NAME
			);
		}
		$this->DbType = strtoupper($db_config['dbms']);
		$dbClass = 'db_'. strtolower($db_config['dbms']);
		if(file_exists(dirname(__FILE__).'/'.$dbClass . '.class.php')){
			if (include_once $dbClass . '.class.php')	
				$db = new $dbClass($db_config);
			if(!$db->connect()){
				ThrowException('无法加载数据库: ' . $db_config['dbms']);
			}
		}else ThrowException('系统暂时不支持数据库: ' . $db_config['dbms']);

		return $db;
	}

	// +----------------------------------------
	// | DSN解析
	// | 格式： mysql://username:passwd@localhost:3306/DbName
	// +----------------------------------------
	function parseDSN($dsnStr) {
		if( empty($dsnStr) ){return false;}
		$info = parse_url($dsnStr);
		if($info['scheme']){
			$DSN = array(
			'dbms'     => $info['scheme'], 
			'username' => $info['user'] ? $info['user'] : '', 
			'password' => $info['pass'] ? $info['pass'] : '', 
			'hostname' => $info['host'] ? $info['host'] : '', 
			'hostport' => $info['port'] ? $info['port'] : '', 
			'database' => $info['path'] ? substr($info['path'],1) : ''
			);
		}else {
		    preg_match('/^(.*?)\:\/\/(.*?)\:(.*?)\@(.*?)\:([0-9]{1, 6})\/(.*?)$/',trim($dsnStr),$matches);
			$DSN = array (
			'dbms'     => $matches[1], 
			'username' => $matches[2], 
			'password' => $matches[3], 
			'hostname' => $matches[4], 
			'hostport' => $matches[5], 
			'database' => $matches[6]
			);
		}
		return $DSN;
     }

	// +----------------------------------------
	// |	分析 SQL 指令 
	// +----------------------------------------

	// +----------------------------------------
	// |	Tables
	// +----------------------------------------
	function parseTables($Tables){
		if(is_array($Tables)) $TablesStr = implode(',', $Tables);
		else if(is_string($Tables)) $TablesStr = $Tables;
		return empty($TablesStr)?'':$TablesStr;
	}

	// +----------------------------------------
	// |	WHERE
	// +----------------------------------------
	function parseWhere($Where){
		if(is_object($Where)){
			if(is_a($Where,'Vo')){
				//如果是Vo对象则转换为Array对象
				$Where = get_object_vars($Where);
			}
			else if(is_a($Where,'HashMap')){
				while ($Where->valid()) { 
					$val = $Where->current();
					$key = $Where->key();
					$WhereStr .= "$key = ".$this->fieldFormat($val)." AND ";
					$Where->next();
				}
				$WhereStr = substr($WhereStr,0,-4);
			}else{
				ThrowException('非法数据对象！');
			}
		}
		if(is_array($Where)){
			//支持数组作为条件
			foreach ($Where as $key=>$val){
				$WhereStr .= "$key = ".$this->fieldFormat($val)." AND ";
			}
			$WhereStr = substr($str,0,-4);
		}else if(is_string($Where)) { 
			//支持String作为条件 如使用 > like 等
			$WhereStr = $Where; 
		}

		return empty($WhereStr)?'':' WHERE '.$WhereStr;
	}

	// +----------------------------------------
	// |	ORDER BY 
	// +----------------------------------------
	function parseOrder($Order){
		if(is_array($Order)) 	$OrderStr .= ' ORDER BY '.implode(',', $Order);
		else if(is_string($Order)) $OrderStr .= ' ORDER BY '.$Order;
		return empty($OrderStr)?'':$OrderStr;
	}

	// +----------------------------------------
	// |	LIMIT 
	// +----------------------------------------
	function parseLimit($Limit){
		if(!empty($Limit)) 	$LimitStr .= ' LIMIT '.$Limit;
		return empty($LimitStr)?'':$LimitStr;
	}

	// +----------------------------------------
	// |	GROUP BY 
	// +----------------------------------------
	function parseGroup($Group){
		if(is_array($Group)) 	$GroupStr .= ' GROUP BY '.implode(',', $Group);
		else if(!empty($Group)) $GroupStr .= ' GROUP BY '.$Group;
		return empty($GroupStr)?'':$GroupStr;
	}

	// +----------------------------------------
	// |	HAVING
	// +----------------------------------------
	function parseHaving($Having){
		if(!empty($Having)) 	$HavingStr .= ' HAVING '.$Having;
		return empty($Having)?'':$HavingStr;
	}

	// +----------------------------------------
	// |	Fields
	// +----------------------------------------
	function parseFields($Fields){
		if(is_array($Fields)) $FieldsStr = implode(',', $Fields);
		else if(!empty($Fields)) $FieldsStr = $Fields;
		else $FieldsStr = '*';
		return $FieldsStr;
	}

	// +----------------------------------------
	// |	Values
	// +----------------------------------------
	function parseValues($Values){
		if(is_array($Values)) {
			array_walk($Values, array($this, 'fieldFormat'));
			$ValuesStr = implode(',', $Values);
		}
		else if(is_string($Values)) $ValuesStr = $Values;
		return $ValuesStr;
	}

	// +----------------------------------------
	// |	SET 
	// +----------------------------------------
	function parseSets($Sets){
		if(is_object($Sets) && !empty($Sets)){
			if(is_a($Sets,'Vo')){
				//如果是Vo对象则转换为Map对象
				$Sets = $Sets->toMap();
			}
			if(is_a($Sets,'HashMap')){
				$Sets = $Sets->toArray();
			}
		}
		if(is_array($Sets)){
			foreach ($Sets as $key=>$val){
				if(!is_null($val)){//过滤空值元素
					$SetsStr .= "$key = ".$this->fieldFormat($val).",";
				}
			}
			$SetsStr = substr($SetsStr,0,-1);
		}else if(is_string($Sets)) { $SetsStr = $Sets; }
		return $SetsStr;
	}

	// +----------------------------------------
	// |    字段格式化
	// +----------------------------------------
	function fieldFormat(&$value) {
		if(is_int($value)) {
			$value = intval($value);
			return $value;
		} else
		if(is_float($value)) {
			$value = floatval($value);
			return $value;
		} else
		if(defined($value) && $value === null) {
			$value = strval(constant($value));
			return $value;
		} else
		if(is_string($value)) {
			$value = '\''.addslashes($value).'\'';
			return $value;
		} else {
			return $value;
		}
	}

	// +----------------------------------------
	// |  是否为数据查询操作
	// +----------------------------------------
    function isMainIps($query)
    {
        $queryIps = 'INSERT|UPDATE|DELETE|REPLACE|'
                . 'CREATE|DROP|'
                . 'LOAD DATA|SELECT .* INTO|COPY|'
                . 'ALTER|GRANT|REVOKE|'
                . 'LOCK|UNLOCK';
        if (preg_match('/^\s*"?(' . $queryIps . ')\s+/i', $query)) {
            return true;
        }
        return false;
    }

	// +----------------------------------------
	// |    抽象化的查询方法
	// |	find()
	// |	add()
	// |	remove()
	// |	save()
	// +----------------------------------------

	// +----------------------------------------
	// |	查找记录
	// | Where 为条件Map、Array或者String
	// | 其它为String
	// +----------------------------------------
	function Find($Where,$Tables,$Fields='*',$Order=NULL,$Limit=NULL,$Group=NULL,$Having=NULL)
	{
		$this->QueryStr = 'SELECT '.$this->parseFields($Fields)
						.' FROM '.$Tables
						.$this->parseWhere($Where)
						.$this->parseGroup($Group)
						.$this->parseHaving($Having)
						.$this->parseOrder($Order)
						.$this->parseLimit($Limit);
		
		return $this->Query();
	}

	// +----------------------------------------
	// | 新增记录
	// | 只支持HashMap方式增加数据
	// +----------------------------------------
	function Add($Map,$Table){
		if(!is_a($Map,'HashMap')){
			ThrowException('新增记录格式非法');
		}
		$Map = $Map->toArray();

		//如果某个字段的值为非字符串的NULL，则过滤该字段和值
		foreach ($Map as $key=>$val){
			if(is_null($val)){
				unset($Map[$key]);
			}
		}
		$Fields = array_keys($Map);
		$Values = array_Values($Map);
		$FieldsStr = implode(',', array_keys($Map));
		array_walk($Values, array($this, 'fieldFormat'));

		$ValuesStr = implode(',', $Values);
		$this->QueryStr =	'INSERT INTO '.$Table.'('.$FieldsStr.') VALUES ('.$ValuesStr.')';
		return $this->Execute();
	}

	// +----------------------------------------
	// | 删除记录
	// | Where 为条件Map、Array或者String
	// +----------------------------------------
	function Remove($Where,$Table){
		$this->QueryStr = 'DELETE FROM '.$Table.$this->parseWhere($Where);
		return $this->Execute();
	}

	// +----------------------------------------
	// | 保存记录
	// | 只支持Map对象保存
	// | Where 为条件Map、Array或者String
	// +----------------------------------------
	function Save($Sets,$Table,$Where){
		if(!is_a($Sets,'HashMap')){
			ThrowException('新增记录格式非法');
		}
		$this->QueryStr = 'UPDATE '.$Table.' SET '.$this->parseSets($Sets).$this->parseWhere($Where);
		return $this->Execute();
	}

	// +----------------------------------------
	// |  查询数据集返回 Array Iterator
	// +----------------------------------------
	Function getArrayIterator() {
		return new ResultSet($this->getAll(0));
	}

	// +----------------------------------------
	// |  查询数据集返回 Object Iterator
	// +----------------------------------------
	Function getObjectIterator() {
		return new ResultSet($this->getAll(1));
	}

	// +----------------------------------------
	// |	get set 方法
	// +----------------------------------------
	function getAutoFree() {return $this->AutoFree;}
	function getAutoCommit() {return $this->AutoCommit;}
	function getPconnect() {return $this->Pconnect;}
	function getDebug() {return $this->Debug;}

	//只读属性获取
	function getDbType() {return $this->DbType;}
	function getDbVersion() {return $this->DbVersion;}
	function getResult()  {return $this->Result;}
	function getFields()  {return $this->Fields;}
	function getLastInsID()  {return $this->LastInsID;}
	function getNumCols() {return $this->NumCols;}
	function getNumRows() {return $this->NumRows;}
    function getQueryTimes() { return $this->QueryTimes;  }
    function getWriteTimes() { return $this->WriteTimes;  }

	function setAutoFree($autofree) {$this->AutoFree = $autofree;}
	function setAutoCommit($autocommit) {$this->AutoCommit = $autocommit;}
	function setPconnect($pconnect) {$this->Pconnect = $pconnect;}
	function setDebug($debug) {$this->Debug = $debug;}


};
?>