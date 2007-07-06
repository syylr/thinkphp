<?php 
/*
+--------------------------------------------------------
| 项目: FCS -- Fast,Compatible & Simple OOP PHP Framework
| 版本: 0.6.1
| PHP:	4.3.0 以上
| 文件: FCSException.class.php
| 功能:  异常处理类
| 最后修改：2006-1-19
+--------------------------------------------------------
| 版权声明: Copyright◎ 2005-2006 世纪流年 版权所有
| 主 页:	http://www.liu21st.com
| 作 者:	Liu21st <流年> liu21st@gmail.com 
+--------------------------------------------------------
*/
if(PHP_VERSION >'5.0.0'){
	class FCSException extends Exception
	{
		function __construct($message, $code = 0) {
			parent::__construct($message, $code);
		}

		function __toString() {
			$trace = $this->getTrace();
			$ExceptionMsg = '';
			switch($this->type){
			case 1:$ExceptionMsg = $this->message;break;
			default:$ExceptionMsg = "程序出错：[".$this->code."]". $this->message.' '.basename($this->file).'第'.$this->line."行。 \n";
			}

			$ExceptionMsg .= "<strong>错误信息：</strong>".$this->getMessage()."\n";
			$ExceptionMsg .= "<strong>错误文件：</strong>".$this->getFile()."(".$this->getLine().")\n";
			$ExceptionMsg .= "<strong>错误跟踪：</strong> \n";
			$i = 0;
			foreach($trace as $t)
			{

				$ExceptionMsg .= '#'.$i.':'.$t['file'].'('.$t['line'].')';
				$ExceptionMsg .= $t['class'].$t['type'].$t['function'].'(';
				$ExceptionMsg .= implode(', ', $t['args']);
				$ExceptionMsg .=")\n";

				$i++;
			}
			return $ExceptionMsg ;
		}

	}
}
?>