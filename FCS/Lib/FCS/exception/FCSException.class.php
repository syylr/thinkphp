<?php 
/*
+---------------------------------------------------------+
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework |
| 文件: FcsException.class.php							  |
| 功能: FCS异常基础类									  |
+---------------------------------------------------------+
| 本框架代码基于GPL协议，可免费使用，但必须保留版权信息	  |
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有		  |
| 主 页:	http://www.liu21st.com						  |
| 作 者:	Liu21st <流年> liu21st@gmail.com			  |
+---------------------------------------------------------+
*/
import("FCS.util.Log");

if(PHP_VERSION >'5.0.0'){
	//如果是PHP5扩展系统的Exception类
	class FCSException extends Exception
	{
		// +----------------------------------------
		// |	架构函数
		// +----------------------------------------
		function __construct($message) {
			parent::__construct($message);
			$this->type = get_class($this);
		}
		
		// +----------------------------------------
		// |	输出异常信息
		// +----------------------------------------
		function __toString() {
			$trace = $this->getTrace();
			$this->file = $trace[1]['file'];
			$this->line = $trace[1]['line'];
			$i = 0;
			$time = date("y-m-d H:i:m");
			foreach($trace as $t)
			{
				$ExceptionTrace .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
				$ExceptionTrace .= $t['class'].$t['type'].$t['function'].'(';
				$ExceptionTrace .= implode(', ', $t['args']);
				$ExceptionTrace .=")\n";
				$i++;
			}
			$error['message'] = $this->message;
			$error['type'] = $this->type;
			$error['detail'] = '模块['.MODULE_NAME.'] 操作['.ACTION_NAME.']';
			$error['file'] = $this->file;
			$error['line'] = $this->line;
			$error['trace'] = $ExceptionTrace;
			
			$errorStr = "\n\r错误信息：[ ".APP_NAME.' '.MODULE_NAME.' '.ACTION_NAME.' ]'.$this->message."\n\r";
			$errorStr .= "错误页面：".WEB_URL.$_SERVER["PHP_SELF"]."\n\r";
			$errorStr .="错误类型：".$this->type."\n\r";
			$errorStr .="错误跟踪：\n\r".$ExceptionTrace;
			//TODO
			//记录系统日志
			Log::Write($errorStr);

			return $error ;
		}

	}

}else {
	//PHP4版本的异常基础类
	class FCSException extends Base
	{
		// +----------------------------------------
		// |	私有属性
		// +----------------------------------------
		var $message;
		var $type;
		var $file;
		var $line;

		// +----------------------------------------
		// |	架构函数
		// +----------------------------------------
		function __construct($message) {
			$this->message = $message;
			$this->type = get_class($this);
		}

		// +----------------------------------------
		// |	输出异常信息
		// +----------------------------------------
		function __toString() {
			$trace = debug_backtrace();
			$this->file = $trace[1]['file'];
			$this->line = $trace[1]['line'];
			$i = 0;
			$time = date("y-m-d H:i:m");
			foreach($trace as $t)
			{
				$ExceptionTrace .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
				$ExceptionTrace .= $t['class'].$t['type'].$t['function'].'(';
				$ExceptionTrace .= implode(', ', $t['args']);
				$ExceptionTrace .=")\n";
				$i++;
			}
			$error['message'] = $this->message;
			$error['detail'] = '模块['.MODULE_NAME.'] 操作['.ACTION_NAME.']';
			$error['type'] = $this->type;
			$error['file'] = $this->file;
			$error['line'] = $this->line;
			$error['trace'] = $ExceptionTrace;
			$errorStr = "\n\r错误信息：".$this->message."\n\r";
			$errorStr .= "错误页面：".WEB_URL.$_SERVER["PHP_SELF"]."\n\r";
			$errorStr .="错误类型：".$this->type."\n\r";
			$errorStr .="错误跟踪：\n\r".$ExceptionTrace;
			//TODO
			//记录系统日志
			Log::Write($errorStr);

			return $error;
		}

	}
}
?>