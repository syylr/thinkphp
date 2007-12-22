<?php 
function IP($ip='',$file='UTFWry.dat') {
	import("ORG.Net.IpLocation");
	$iplocation = new IpLocation($file); 
	$location = $iplocation->getlocation($ip); 
	return $location;
}


function toDate($time,$format='Y年m月d日 H:i:s') 
{
	if( empty($time)) {
		return '';
	}
    $format = str_replace('#',':',$format);
	return date(auto_charset($format),$time);
}

function showTags($tags) 
{
	$tags = explode(' ',$tags);
    $str = '';
    foreach($tags as $key=>$val) {
    	$tag =  trim($val);
        $str  .= ' <a href="'.__URL__.'/tag/name/'.urlencode($tag).'">'.$tag.'</a>  ';
    }
    return $str;
}
function firendlyTime($time) 
{
    if(empty($time)) {
    	return '';
    }
	import('ORG.Date.Date');
	$date	=	new Date(intval($time)); 
    return $date->timeDiff(time(),2);
}
function autourl($message){
      $message= preg_replace( array(
     "/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/|www\.)([a-z0-9\/\-_+=.~!%@?#%&;:$\\│]+)/i",
     "/(?<=[^\]a-z0-9\/\-_.~?=:.])([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4}))/i"
      ), array(
      "[url]\\1\\3[/url]",
      "[email]\\0[/email]"
      ), ' '.$message);
      return $message;
     }


    function getCategoryName($id) 
    {
        if(isset($_SESSION['categoryList'])) {
        	$list  = $_SESSION['categoryList'];
            return $list[$id];
        }
    	$dao = D("Category");
        $cateList  = $dao->getFields("id,title");
        $_SESSION['categoryList']=$cateList;
        return $cateList[$id];
    }
    function getAbstract($content,$id) 
    {
        if(false !== $pos=strpos($content,'[separator]')) {
            $content  =  substr($content,0,$pos).'  <P> <a href="'.__URL__.'/'.$id.'"><B>阅读文章全部内容… </B></a> ';
         }
         return $content;
    }
   

function getTitleSize($count) 
{
    $size = (ceil($count/10)+11).'px';
    return $size;   	
}

function getBlogTitle($id) 
{
	$dao = D("Blog");
    $blog   =  $dao->getById($id);
    if($blog) {
    	return $blog->title;
    }else {
    	return '';
    }
}

function getTopicTitle($id) 
{
	$dao = D("Topic");
    $topic   =  $dao->getById($id);
    if($topic) {
    	return $topic->title;
    }else {
    	return '';
    }
}

function getCategoryBlogCount($categoryId) 
{
   $dao = D("Blog");
   $count  =  $dao->count("categoryId='{$categoryId}'");    	
    return $count;
}

function rcolor() {
$rand = rand(0,255);
return sprintf("%02X","$rand");
}
function rand_color() 
{
	return '#'.rcolor().rcolor().rcolor();
}


function byte_format($input, $dec=0) 
{ 
  $prefix_arr = array("B", "K", "M", "G", "T"); 
  $value = round($input, $dec); 
  $i=0; 
  while ($value>1024) 
  { 
     $value /= 1024; 
     $i++; 
  } 
  $return_str = round($value, $dec).$prefix_arr[$i]; 
  return $return_str; 
}
/**
 +----------------------------------------------------------
 * UBB 解析
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
    function ubb($Text) { 
      $Text=trim($Text);
      //$Text=htmlspecialchars($Text);  
      //$Text=ereg_replace("\n","<br>",$Text); 
      $Text=preg_replace("/\\t/is","  ",$Text); 
      $Text=preg_replace("/\[hr\]/is","<hr>",$Text); 
      $Text=preg_replace("/\[separator\]/is","<br/>",$Text);
      $Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text); 
      $Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text); 
      $Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text); 
      $Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text); 
      $Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text); 
      $Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text); 
      $Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text); 
      //$Text=preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is","<a href=\\1 target='_blank'>\\2</a>",$Text); 
      $Text=preg_replace("/\[url\](.+?)\[\/url\]/is","<a href=\"\\1\" target='_blank'>\\1</a>",$Text); 
      $Text=preg_replace("/\[url=(http:\/\/.+?)\](.+?)\[\/url\]/is","<a href='\\1' target='_blank'>\\2</a>",$Text); 
      $Text=preg_replace("/\[url=(.+?)\](.+?)\[\/url\]/is","<a href=\\1>\\2</a>",$Text); 
      $Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$Text); 
      $Text=preg_replace("/\[img\s(.+?)\](.+?)\[\/img\]/is","<img \\1 src=\\2>",$Text); 
      $Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$Text); 
      $Text=preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis","color_txt('\\1')",$Text); 
      $Text=preg_replace("/\[style=(.+?)\](.+?)\[\/style\]/is","<div class='\\1'>\\2</div>",$Text); 
      $Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$Text); 
      $Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text); 
      $Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text); 
      $Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text); 
      $Text=preg_replace("/\[emot\](.+?)\[\/emot\]/eis","emot('\\1')",$Text); 
      $Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$Text); 
      $Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text); 
      $Text=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$Text); 
      $Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text); 
      $Text=preg_replace("/\[quote\](.+?)\[\/quote\]/is","<blockquote>引用:<div style='border:1px solid silver;background:#EFFFDF;color:#393939;padding:5px' >\\1</div></blockquote>", $Text); 
      $Text=preg_replace("/\[code\](.+?)\[\/code\]/eis","highlight_code('\\1')", $Text); 
      $Text=preg_replace("/\[php\](.+?)\[\/php\]/eis","highlight_code('\\1')", $Text); 
      $Text=preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div style='text-align: left; color: darkgreen; margin-left: 5%'><br><br>--------------------------<br>\\1<br>--------------------------</div>", $Text); 
      return $Text; 
    }

function color_txt($str) 
{
    if(function_exists('iconv_strlen')) {
    	$len  = iconv_strlen($str);
    }else if(function_exists('mb_strlen')) {
    	$len = mb_strlen($str);
    }
    $colorTxt = '';
    for($i=0; $i<$len; $i++) {
               $colorTxt .=  '<span style="color:'.rand_color().'">'.msubstr($str,$i,1,'utf-8','').'</span>';
     }
		
    return $colorTxt;
}
function showExt($ext,$pic=true) {
	static $_extPic = array(
		'dir'=>"folder.gif",
		'doc'=>'msoffice.gif',
		'rar'=>'rar.gif',
		'zip'=>'zip.gif',
		'txt'=>'text.gif',
		'pdf'=>'pdf.gif',
		'html'=>'html.gif',
		'png'=>'image.gif',
		'gif'=>'image.gif',
		'jpg'=>'image.gif',
		'php'=>'text.gif',
	);
	static $_extTxt = array(
		'dir'=>'文件夹',
		'jpg'=>'JPEG图象',
		);
	if($pic) {
		if(array_key_exists(strtolower($ext),$_extPic)) {
			$show = "<IMG SRC='".WEB_PUBLIC_URL."/Images/extension/".$_extPic[strtolower($ext)]."' BORDER='0' alt='' align='absmiddle'>";
		}else{
			$show = "<IMG SRC='".WEB_PUBLIC_URL."/Images/extension/common.gif' WIDTH='16' HEIGHT='16' BORDER='0' alt='文件' align='absmiddle'>";
		}
	}else{
		if(array_key_exists(strtolower($ext),$_extTxt)) {
			$show = $_extTxt[strtolower($ext)];
		}else{
			$show = $ext?$ext:'文件夹';
		}
	}

	return $show;
}
function emot($emot) 
{
	return '<img src="'.WEB_PUBLIC_URL.'/Images/emot/'.$emot.'.gif" align="absmiddle" style="border:none;margin:0px 1px">';
}
function getShortTitle($title,$length=12) 
{
	if(empty($title)) {
		return '...';
	}
    return msubstr ($title,0,$length,C('OUTPUT_CHARSET'));
}

?>