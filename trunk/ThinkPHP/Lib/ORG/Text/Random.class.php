<?php 

class Random extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $len 长度 
     * @param string $type 字串类型 
     * 0 字母 1 数字 其它 混合
     * @param string $addChars 额外字符 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function rand_string($len=6,$type='',$addChars='') { 
        $str ='';
        switch($type) { 
            case 0:
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars; 
                break;
            case 1:
                $chars='0123456789'; 
                break;
            case 2:
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars; 
                break;
            case 3:
                $chars='abcdefghijklmnopqrstuvwxyz'.$addChars; 
                break;
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars; 
                break;
        }
        if($len>10 ) {//位数过长重复字符串一定次数
            $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5); 
        }
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);

        return $str;
    }

    /**
     +----------------------------------------------------------
     * 生成一定数量的随机数，并且不重复
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param integer $number 数量
     * @param string $len 长度
     * @param string $type 字串类型 
     * 0 字母 1 数字 其它 混合
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function build_count_rand ($number,$length=4,$mode=1) { 
            if($mode==1 && $length<strlen($number) ) {
                //不足以生成一定数量的不重复数字
                return false;        	
            }
            $rand   =  array();
            for($i=0; $i<$number; $i++) {
                $rand[] =   rand_string($length,$mode);
            }
            $unqiue = array_unique($rand);
            if(count($unqiue)==count($rand)) {
                return $rand;
            }
            $count   = count($rand)-count($unqiue);
            for($i=0; $i<$count*3; $i++) {
                $rand[] =   rand_string($length,$mode);
            }
            $rand = array_slice(array_unique ($rand),0,$number);    	
            return $rand;
    }

    /**
     +----------------------------------------------------------
     *  带格式生成随机字符 支持批量生成 
     *  但可能存在重复
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $format 字符格式
     *     # 表示数字 * 表示字母和数字 $ 表示字母
     * @param integer $number 生成数量
     +----------------------------------------------------------
     * @return string | array
     +----------------------------------------------------------
     */
    function build_format_rand($format,$number=1) 
    {
        $str  =  array();
        $length =  strlen($format);
        for($j=0; $j<$number; $j++) {
            $strtemp   = '';
            for($i=0; $i<$length; $i++) {
                $char = substr($format,$i,1);
                switch($char){
                    case "*"://字母和数字混合
                        $strtemp   .= rand_string(1);
                        break;
                    case "#"://数字
                        $strtemp  .= rand_string(1,1);
                        break;
                    case "$"://大写字母
                        $strtemp .=  rand_string(1,2);
                        break;
                    default://其他格式均不转换
                        $strtemp .=   $char;
                        break;
               }
            } 
            $str[] = $strtemp;
        }
        
        return $number==1? $strtemp : $str ;
    }

    /**
     +----------------------------------------------------------
     * 获取一定范围内的随机数字 位数不足补零
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param integer $min 最小值
     * @param integer $max 最大值
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function rand_number ($min, $max) {
        Return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
    }

    /**
     +----------------------------------------------------------
     * 获取登录验证码 默认为4位数字
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $len 长度 
     * @param string $type 字串类型 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function build_verify ($length=4,$type=1) { 
        return rand_string($length,$type);
    }

}//类定义结束
?>