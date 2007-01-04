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
 * 图像操作类库
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Image extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {

    }

    /**
     +----------------------------------------------------------
     * 取得图像信息
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $image 图像文件名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if( $imageInfo!== false) {
            $imageType = image_type_to_extension($imageInfo[2]);
            $imageSize = filesize($img);
            $info = array(
                "width"=>$imageInfo[0],
                "height"=>$imageInfo[1],
                "type"=>$imageType,
                "size"=>$imageSize,
                "mime"=>$imageInfo['mime']
            );
            return $info;
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 显示服务器图像文件
     * 支持URL方式
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $imgFile 图像文件名
     * @param string $text 文字字符串
     * @param string $width 图像宽度
     * @param string $height 图像高度
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function showImg($imgFile,$text='',$width=80,$height=30) {
        //获取图像文件信息
        $info = Image::getImageInfo($imgFile);
        if($info !== false) {
            $createFun  =   str_replace('/','createfrom',$info['mime']);
            $im = $createFun($imgFile); 
            if($im) {
                $ImageFun= str_replace('/','',$info['mime']);
                if(!empty($text)) {
                    $tc  = imagecolorallocate($im, 0, 0, 0);
                    imagestring($im, 3, 5, 5, $text, $tc);
                }
                Header("Content-type: ".$info['mime']);
                $ImageFun($im);        	            	
                @ImageDestroy($im);
                return ;
            }
        }
        //获取或者创建图像文件失败则生成空白PNG图片
        $im  = imagecreatetruecolor($width, $height); 
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        imagestring($im, 4, 5, 5, "NO PIC", $tc);
        Image::output($im);
        return ;
    }

    /**
     +----------------------------------------------------------
     * 生成缩略图
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $image  原图
     * @param string $type 图像格式
     * @param string $filename 缩略图文件名
     * @param string $maxWidth  宽度
     * @param string $maxHeight  高度
     * @param string $position 缩略图保存目录
     * @param boolean $interlace 启用隔行扫描
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function thumb($image,$type='',$filename='',$maxWidth=200,$maxHeight=50,$position='',$interlace=true) 
    {
        // 获取原图信息
        $info  = Image::getImageInfo($image); 
         if($info !== false) {
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $type = empty($type)?$info['type']:$type;
            $position   = empty($position)?THUMB_DIR:$position;
            $interlace  =  $interlace? 1:0;
            unset($info);
            $scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); // 计算缩放比例

            // 缩略图尺寸
            $width  = (int)($srcWidth*$scale);
            $height = (int)($srcHeight*$scale);

            // 载入原图
            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $srcImg     = $createFun($image); 

            //创建缩略图
            if($type!='gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height); 
            else
                $thumbImg = imagecreate($width, $height); 

            // 复制图片
            if(function_exists("ImageCopyResampled"))
                ImageCopyResampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth,$srcHeight); 
            else
                ImageCopyResized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height,  $srcWidth,$srcHeight); 

            // 对jpeg图形设置隔行扫描
            if('jpg'==$type) 	imageinterlace($thumbImg,$interlace);

            // 生成图片
            $imageFun = 'image'.($type=='jpg'?'jpeg':$type);
            $filename  = empty($filename)? basename($image,'.'.$type).'_thumb.' : $filename;
            $imageFun($thumbImg,$position.$filename.$type); 
            ImageDestroy($thumbImg);
         }
    }

    /**
     +----------------------------------------------------------
     * 生成图像验证码
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $length  位数
     * @param string $mode  类型
     * @param string $type 图像格式
     * @param string $width  宽度
     * @param string $height  高度
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function buildImageVerify($length=4,$mode=1,$type='png',$width=48,$height=22,$verifyName='verify') 
    {
        $randval = build_verify($length,$mode);
        $_SESSION[$verifyName]= md5($randval);
        $width = ($length*9+10)>$width?$length*9+10:$width;
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($width,$height);
        }else {
            $im = @imagecreate($width,$height);
        }
        $r = Array(225,255,255,223);
        $g = Array(225,236,237,255);
        $b = Array(225,236,166,125);
        $key = mt_rand(0,3);

        $backColor = ImageColorAllocate($im, $r[$key],$g[$key],$b[$key]);    //背景色（随机）
        $borderColor = ImageColorAllocate($im, 0, 0, 0);                    //边框色
        $pointColor = ImageColorAllocate($im, 0, 255, 255);                    //点颜色

        @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $stringColor = ImageColorAllocate($im, 255,51,153);
        for($i=0;$i<=10;$i++){
            $pointX = mt_rand(2,$width-2);
            $pointY = mt_rand(2,$height-2);
            @imagesetpixel($im, $pointX, $pointY, $pointColor);
        }

        @imagestring($im, 5, 5, 3, $randval, $stringColor);
        Image::output($im,$type);
    }

    /**
     +----------------------------------------------------------
     * 生成高级图像验证码
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $type 图像格式
     * @param string $width  宽度
     * @param string $height  高度
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function buildAdvImageVerify($key,$type='png',$width=100,$height=45) 
    {
        $rand = build_count_rand(10,1,3);
        $verify  =  '';
        for($i=0; $i<strlen($key); $i++) {
        	$verify .=  $rand[$key[$i]];
        }
        $_SESSION['verify']= md5($verify);
        $randval   =  implode('',$rand);
        $width = ($length*9+10)>$width?$length*9+10:$width;
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($width,$height);
        }else {
            $im = @imagecreate($width,$height);
        }
        $r = Array(225,255,255,223);
        $g = Array(225,236,237,255);
        $b = Array(225,236,166,125);
        $key = mt_rand(0,3);

        $backColor = ImageColorAllocate($im, $r[$key],$g[$key],$b[$key]);    //背景色（随机）
        $borderColor = ImageColorAllocate($im, 0, 0, 0);                    //边框色
        $pointColor = ImageColorAllocate($im, 0, 255, 255);                    //点颜色

        @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $stringColor1 = ImageColorAllocate($im, 255,51,153);
        $stringColor2 = ImageColorAllocate($im, 65,65,65);
        for($i=0;$i<=10;$i++){
            $pointX = mt_rand(2,$width-2);
            $pointY = mt_rand(2,$height-2);
            @imagesetpixel($im, $pointX, $pointY, $pointColor);
        }

        @imagestring($im, 5, 5, 3, $randval, $stringColor1);
        @imagestring($im, 5, 5, 25, '0123456789', $stringColor2);
        Image::output($im,$type);
    }

    function showAdvVerify($type='png',$width=180,$height=40) 
    {
        $verifyCodeRandArray = build_count_rand(10,1,3);
        $i=0;
        while (list($k,$v)=each($verifyCodeRandArray)) {
            $verifyCode[$i] = $v;
            $i++;
        }
        $letter = implode(" ",$verifyCode);
        $_SESSION['verifyCode'] = $verifyCode;

        $im = @imagecreate($width,$height);
        $r = Array(225,255,255,223);
        $g = Array(225,236,237,255);
        $b = Array(225,236,166,125);

        $key = rand(0,3);

        $backColor = ImageColorAllocate($im, $r[$key],$g[$key],$b[$key]); //璉春︹繦诀
        $borderColor = ImageColorAllocate($im, 0, 0, 0);				  //娩︹

        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $stringColor1 = ImageColorAllocate($im, 255,rand(0,100), rand(0,100));
        $stringColor2 = ImageColorAllocate($im, rand(0,100), rand(0,100), 255);
        for($i=0;$i<=50;$i++){
            $pointX = rand(4,$width-4);
            $pointY = rand(16,$height-4);
            @imagesetpixel($im, $pointX, $pointY, $stringColor2);
        }

        imagestring($im, 5, 5, 1, "0 1 2 3 4 5 6 7 8 9", $stringColor1);
        imagestring($im, 5, 5, 20, $letter, $stringColor2);
        Image::output($im,$type);
	
    }
    /**
     +----------------------------------------------------------
     * 生成UPC-A条形码
     * 
     +----------------------------------------------------------
     * @static
     +----------------------------------------------------------
     * @param string $type 图像格式
     * @param string $type 图像格式
     * @param string $lw  单元宽度
     * @param string $hi   条码高度
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function UPCA($code,$type='png',$lw=2,$hi=100) { 
        static $Lencode = array('0001101','0011001','0010011','0111101','0100011', 
                         '0110001','0101111','0111011','0110111','0001011'); 
        static $Rencode = array('1110010','1100110','1101100','1000010','1011100', 
                         '1001110','1010000','1000100','1001000','1110100'); 
        $ends = '101'; 
        $center = '01010'; 
        /* UPC-A Must be 11 digits, we compute the checksum. */ 
        if ( strlen($code) != 11 ) { die("UPC-A Must be 11 digits."); } 
        /* Compute the EAN-13 Checksum digit */ 
        $ncode = '0'.$code; 
        $even = 0; $odd = 0; 
        for ($x=0;$x<12;$x++) { 
          if ($x % 2) { $odd += $ncode[$x]; } else { $even += $ncode[$x]; } 
        } 
        $code.=(10 - (($odd * 3 + $even) % 10)) % 10; 
        /* Create the bar encoding using a binary string */ 
        $bars=$ends; 
        $bars.=$Lencode[$code[0]]; 
        for($x=1;$x<6;$x++) { 
          $bars.=$Lencode[$code[$x]]; 
        } 
        $bars.=$center; 
        for($x=6;$x<12;$x++) { 
          $bars.=$Rencode[$code[$x]]; 
        } 
        $bars.=$ends; 
        /* Generate the Barcode Image */ 
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($lw*95+30,$hi+30);
        }else {
            $im = @imagecreate($lw*95+30,$hi+30);
        }
        $fg = ImageColorAllocate($img, 0, 0, 0); 
        $bg = ImageColorAllocate($im, 255, 255, 255); 
        ImageFilledRectangle($im, 0, 0, $lw*95+30, $hi+30, $bg); 
        $shift=10; 
        for ($x=0;$x<strlen($bars);$x++) { 
          if (($x<10) || ($x>=45 && $x<50) || ($x >=85)) { $sh=10; } else { $sh=0; } 
          if ($bars[$x] == '1') { $color = $fg; } else { $color = $bg; } 
          ImageFilledRectangle($im, ($x*$lw)+15,5,($x+1)*$lw+14,$hi+5+$sh,$color); 
        } 
        /* Add the Human Readable Label */ 
        ImageString($im,4,5,$hi-5,$code[0],$fg); 
        for ($x=0;$x<5;$x++) { 
          ImageString($im,5,$lw*(13+$x*6)+15,$hi+5,$code[$x+1],$fg); 
          ImageString($im,5,$lw*(53+$x*6)+15,$hi+5,$code[$x+6],$fg); 
        } 
        ImageString($im,4,$lw*95+17,$hi-5,$code[11],$fg); 
        /* Output the Header and Content. */ 
        Image::output($im,$type);
    } 

    function output($im,$type='png') 
    {
        Header("Content-type: image/".$type);
        $ImageFun='Image'.$type;
        $ImageFun($im);
        @ImageDestroy($im);  	
    }

 
}//类定义结束
?>