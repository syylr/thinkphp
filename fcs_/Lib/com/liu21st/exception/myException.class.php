<?php 
/*
+--------------------------------------------------------
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework
| 版本: 0.6.0
| PHP:	4.3.0 以上
| 文件: myException.class.php
| 功能:  异常处理类
| 最后修改：2006-1-19
+--------------------------------------------------------
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/
if(PHP_VERSION >'5.0.0'){
	class MyException extends Exception
	{
		var $type = 0;

		function __construct($message, $file='',$type=0,$code = 0) {
			parent::__construct($message, $code);
			$this->type = $type;
			$this->msg = $message;
			if($file){
				$this->file = $file;
			} 

		}

		function __toString() {
			$ExceptionMsg = '';
			switch($this->type){
			case 1:$ExceptionMsg = $this->message;break;
			default:$ExceptionMsg = "程序出错：[".$this->code."]". $this->message.' '.basename($this->file).'第'.$this->line.'行。';
			}
			return $ExceptionMsg ;
		}

	}
}
?>