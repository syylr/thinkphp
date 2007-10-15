<?php 
function IP($ip='',$file='UTFWry.dat') {
	import("ORG.Net.IpLocation");
	$iplocation = new IpLocation($file); 
	$location = $iplocation->getlocation($ip); 
	return $location;
}

function showUpdateType($type) 
{
	switch($type) {
		case 1: 
            $show = '<IMG SRC="'.WEB_HOST.WEB_PUBLIC_URL.'/Images/bug.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="BUG修复" align="absmiddle"> ';
            break;
        case 3:
        	$show =  '<IMG SRC="'.WEB_HOST.WEB_PUBLIC_URL.'/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="功能增加" align="absmiddle"> ';
        	break;
        case 4:
        	$show =  '<IMG SRC="'.WEB_HOST.WEB_PUBLIC_URL.'/Images/checkin.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="其他更新" align="absmiddle"> ';
        	break;
        case 2:
        default:
        	$show =  '<IMG SRC="'.WEB_HOST.WEB_PUBLIC_URL.'/Images/call.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="系统完善" align="absmiddle"> ';
	}
    return $show;
}
function toDate($time,$format='Y年m月d日 H:i:s') 
{
	if( empty($time)) {
		return '';
	}
    $format = str_replace('#',':',$format);
	return date(auto_charset($format),$time);
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
function sendMsg($userId,$title,$msg) 
{
    $dao = D("Message");
    $dao->sendId = 1;
    $dao->toId = $userId;
    $dao->title  = $title;
    $dao->content = $msg;
    $dao->sendTime = time();
    $dao->status = 1;
    $result = $dao->add();
	return $result;
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
// utf8 -> unicode 
function utf8_unicode($c) { 
    switch(strlen($c)) { 
    case 1: 
    return ord($c); 
    case 2: 
    $n = (ord($c[0]) & 0x3f) << 6; 
    $n += ord($c[1]) & 0x3f; 
    return $n; 
    case 3: 
    $n = (ord($c[0]) & 0x1f) << 12; 
    $n += (ord($c[1]) & 0x3f) << 6; 
    $n += ord($c[2]) & 0x3f; 
    return $n; 
    case 4: 
    $n = (ord($c[0]) & 0x0f) << 18; 
    $n += (ord($c[1]) & 0x3f) << 12; 
    $n += (ord($c[2]) & 0x3f) << 6; 
    $n += ord($c[3]) & 0x3f; 
    return $n; 
    } 
} 

    function getCategoryName($id) 
    {
        if(Session::is_set('categoryList')) {
        	$list  = Session::get('categoryList');
            return $list[$id];
        }
    	$dao = D("Category");
        $cateList  = $dao->getFields("id,title");
        Session::set('categoryList',$cateList);
        return $cateList[$id];
    }
    function getAbstract($content,$id) 
    {
        if(false !== $pos=strpos($content,'[separator]')) {
            $content  =  substr($content,0,$pos).'  <P> <a href="'.__URL__.'/'.$id.'"><B>阅读文章全部内容… </B></a> ';
         }
         return $content;
    }

    function s2m($second){
        if($second>=60) {
        	$minute=floor($second/60)."分".(sprintf("%d",$second%60))."秒";
        }else {
        	$minute=(sprintf("%d",$second%60))."秒";
        }
        
        return $minute;
    }

function getTitleSize($count) 
{
    $size = (ceil($count/10)+11).'px';
    return $size;   	
}
function showASCIIImg($image) 
{
    import('ORG.Util.Image');
	return Image::showASCIIImg($image);
}
function getGroupPic($id,$type=1) 
{
    static $_img = array();
    if(isset($_img[$id.'_'.$type])) {
    	return $_img[$id.'_'.$type];
    }
    //读取附件信息
    $attachDao = D('Attach');
    $attach = $attachDao->find("module='Block' and recordId='{$id}'");
    if($attach) {
        switch($type) {
        	case 1:
                $img = '<img width="16" height="16" class="userPic" style="padding:1px"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" align="absmiddle" src="'.WEB_PUBLIC_URL.'/Images/Group/'.$id.'_min.'.$attach->extension.'"/>';
                break;
            case 2:
            	$img = '<img width="32" height="32" class="userPic" align="absmiddle"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/Group/'.$id.'_small.'.$attach->extension.'"/>';
            	break;
            case 3:
            	$img = '<img  width="75" height="75"  align="middle" class="userPic"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/Group/'.$id.'_big.'.$attach->extension.'"/>';
            	break;
        }
        $_img[$id.'_'.$type] = $img;    	
    }else {
    	switch($type) {
        	case 1:
                $img = '<img width="16" height="16" class="userPic" style="padding:1px"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" align="absmiddle" src="'.WEB_PUBLIC_URL.'/Images/Group/blank_min.png"/>';
                break;
            case 2:
            	$img = '<img width="32" height="32" class="userPic" align="absmiddle"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/Group/blank_small.png"/>';
            	break;
            case 3:
            	$img = '<img  width="75" height="75"  align="middle" class="userPic"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/Group/blank_big.png"/>';
            	break;
        }
    }
    return $img;	
}
function getBlockPic($id,$type=1) 
{
    static $_img = array();
    if(isset($_img[$id.'_'.$type])) {
    	return $_img[$id.'_'.$type];
    }
    //读取附件信息
    $attachDao = D('Attach');
    $attach = $attachDao->find("module='Block' and recordId='{$id}'");
    if($attach) {
        switch($type) {
        	case 1:
                $img = '<img width="16" height="16" class="userPic" style="padding:1px"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" align="absmiddle" src="'.WEB_PUBLIC_URL.'/Images/Block/'.$id.'_min.'.$attach->extension.'"/>';
                break;
            case 2:
            	$img = '<img width="32" height="32" class="userPic" align="absmiddle"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/Block/'.$id.'_small.'.$attach->extension.'"/>';
            	break;
            case 3:
            	$img = '<img  width="75" height="75"  align="middle" class="userPic"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/Block/'.$id.'_big.'.$attach->extension.'"/>';
            	break;
        }
        $_img[$id.'_'.$type] = $img;    	
    }else {
    	switch($type) {
        	case 1:
                $img = '<img width="16" height="16" class="userPic" style="padding:1px"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" align="absmiddle" src="'.WEB_PUBLIC_URL.'/Images/Block/blank_min.png"/>';
                break;
            case 2:
            	$img = '<img width="32" height="32" class="userPic" align="absmiddle"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/Block/blank_small.png"/>';
            	break;
            case 3:
            	$img = '<img  width="75" height="75"  align="middle" class="userPic"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/Block/blank_big.png"/>';
            	break;
        }
    }
    return $img;	
}
function getUserPic($id,$type=1) 
{
    static $_img = array();
    if(isset($_img[$id.'_'.$type])) {
    	return $_img[$id.'_'.$type];
    }
/*
		$dao = D("User");
		//读取附件信息
		$attachDao = D('Attach');
		$attach = $attachDao->find("module='User' and recordId='{$id}'",'extension');
		$ext = $attach->extension;*/

$list = glob(dirname(realpath(THINK_PATH)).'/Public/Images/User/'.$id.'_big.*');
    if($list) {
		$ext	=	substr(strrchr($list[0], '.'),1);
        switch($type) {
        	case 1:
                $img = '<img width="16" height="16" class="userPic" style="padding:1px"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" align="absmiddle" src="'.WEB_PUBLIC_URL.'/Images/User/'.$id.'_min.'.$ext.'"/>';
                break;
            case 2:
            	$img = '<img width="32" height="32" class="userPic" align="absmiddle"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/User/'.$id.'_small.'.$ext.'"/>';
            	break;
            case 3:
            	$img = '<img  width="75" height="75"  align="middle" class="userPic"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/User/'.$id.'_big.'.$ext.'"/>';
            	break;
        }
        $_img[$id.'_'.$type] = $img;    	
    }else {
    	switch($type) {
        	case 1:
                $img = '<img width="16" height="16" class="userPic" style="padding:1px"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" align="absmiddle" src="'.WEB_PUBLIC_URL.'/Images/User/blank_min.png"/>';
                break;
            case 2:
            	$img = '<img width="32" height="32" class="userPic" align="absmiddle"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/User/blank_small.png"/>';
            	break;
            case 3:
            	$img = '<img  width="75" height="75"  align="middle" class="userPic"  onmouseover="this.style.filter=\'alpha(opacity=100)\';"  onmouseout="this.style.filter=\'alpha(opacity=60)\';" style="filter:alpha(opacity=80);cursor:hand" src="'.WEB_PUBLIC_URL.'/Images/User/blank_big.png"/>';
            	break;
        }
    }
    return $img;
}

function getUserTopicRate($userId) 
{
        // 用户发表主题数目
        $dao = D("Topic");
        $topicCount   = $dao->count("userId='{$userId}'");
        $totalCount   =  $dao->count();
        $rate = round(($topicCount/$totalCount)*100,3);
        return $rate.'％';
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
function getBlockTopicCount($blockId) 
{
    if(!Session::is_set('BlockTopicCount_'.$blockId)) {
        $dao = D("Topic");
        $count  =  $dao->count("blockId='{$blockId}'");
        Session::set('BlockTopicCount_'.$blockId,$count);
    }else {
    	$count  =  Session::get('BlockTopicCount_'.$blockId);
    }
    return $count;
}
function getBlockCommentCount($blockId) 
{
    if(!Session::is_set('BlockCommentCount_'.$blockId)) {
        $dao = D("Comment");
        $count  =  $dao->count("blockId='{$blockId}'");    	
        Session::set('BlockCommentCount_'.$blockId,$count);
    }else {
    	$count  =  Session::get('BlockCommentCount_'.$blockId);
    }
    return $count;
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
    function rcolor() {
$rand = rand(0,255);
return sprintf("%02X","$rand");
}
function rand_color() 
{
	return '#'.rcolor().rcolor().rcolor();
}

function getBlockName($id) 
{
	if(Session::is_set('blockName')) {
		$name	=	Session::get('blockName');
		return $name[$id];
	}
	$dao	=	D("Block");
	$list	=	$dao->findAll('','id,title');
	foreach ($list as $vo){
		$nameList[$vo->id]	=	$vo->title;
	}
	$name	=	$nameList[$id];
	Session::set('blockName',$nameList);
    return $name;
}
function getUserCity($id) 
{
	$dao = D("User");
    $user  = $dao->getById($id,'id,city');
    return $user->city;
}
function getUserOnline($id) 
{
	$dao = D("Online");
    $result  =  $dao->getBy('memberId',$id,'id');
    if($result) {
    	return '<span style="color:#006600">connected</span>';
    }else {
    	return '<span style="color:#CC3300">disconnected</span>';
    }
}
function getUserName($userId) 
{
    if($userId==0) {
    	return '游客';
    }
	if(Session::is_set('userName')) {
		$name	=	Session::get('userName');
		return $name[$userId];
	}
	$dao	=	D("User");
	$nameList	=	$dao->getFields('id,nickname');
	$name	=	$nameList[$userId];
	Session::set('userName',$nameList);
    return $name;
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

function readFileList($filename) 
{
    $file = base64_encode($filename);
    $name  =  auto_charset(basename($filename),'gb2312','utf-8');
    if(is_dir($filename)) {
    	$click = "readDir(\"$file\")";
         return "<a href='".__URL__.'/index/f/'.$file."'>".$name."</a>";
    }else if(is_file($filename)) {
        $writeable =  is_writable($filename);
    	$click = "readFile(\"$file\",\"$writeable\")";
        return "<a href='javascript:void(0)' onclick='$click'>".$name."</a>";
    }


}

function highlight_code($str,$show=false)
{
    if(file_exists($str)) {
        $str    =   file_get_contents($str);
    }
    $str  =  stripslashes(trim($str));
    // The highlight string function encodes and highlights 
    // brackets so we need them to start raw 
    $str = str_replace(array('&lt;', '&gt;','&quot;'), array('<', '>','"'), $str);

    // Replace any existing PHP tags to temporary markers so they don't accidentally
    // break the string out of PHP, and thus, thwart the highlighting.

    $str = str_replace(array('&lt;?php', '?&gt;',  '\\'), array('phptagopen', 'phptagclose', 'backslashtmp'), $str);
        
    // The highlight_string function requires that the text be surrounded
    // by PHP tags.  Since we don't know if A) the submitted text has PHP tags,
    // or B) whether the PHP tags enclose the entire string, we will add our
    // own PHP tags around the string along with some markers to make replacement easier later

    $str = '<?php //tempstart'."\n".$str.'//tempend ?>'; // <?

    // All the magic happens here, baby!
    $str = highlight_string($str, TRUE);

    // Prior to PHP 5, the highlight function used icky font tags
    // so we'll replace them with span tags.	
    if (abs(phpversion()) < 5)
    {
        $str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
        $str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
    }

    // Remove our artificially added PHP
    $str = preg_replace("#\<code\>.+?//tempstart\<br />\</span\>#is", "<code>\n", $str);
    $str = preg_replace("#\<code\>.+?//tempstart\<br />#is", "<code>\n", $str);
    $str = preg_replace("#//tempend.+#is", "</span>\n</code>", $str);

    // Replace our markers back to PHP tags.
    $str = str_replace(array('phptagopen', 'phptagclose', 'backslashtmp'), array('&lt;?php', '?&gt;', '\\'), $str); //<?
    $line   =   explode("<br />", rtrim(ltrim($str,'<code>'),'</code>'));
    $result =   '<div class="code"><ol>';
    foreach($line as $key=>$val) {
        $result .=  '<li>'.$val.'</li>';
    }
    $result .=  '</ol></div>';
    $result = str_replace("\n", "", $result);
    if( $show!== false) {
        echo($result);
    }else {
        return $result;
    }
}
    function sendMail($to,$subject,$content,$html=false,$type='mail',$smtpParams=array()) 
    {
        import('ORG.Mail.Mail');
        $mail = new Mail();
        $mail->setFrom('TopThink <postmaster@topthink.com.cn>');
        $mail->setSubject($subject);
        $mail->setSMTPParams($smtpParams['host'],$smtpParams['port'],$smtpParams['helo'],$smtpParams['auth'],$smtpParams['user'],$smtpParams['pass']);
        if(!$html) {
        	$mail->setText($content);
        }else {
        	$mail->setHTML($content);
        }
        $mail->setSendmailPath('/usr/sbin/sendmail -ti');
        if(!is_array($to)) {
        	$to   =  explode(',',$to);
        }
        $result  =  $mail->send($to,$type);
        if($result) {
        	return true;
        }else {
        	return $mail->errors;
        }
        /*
        import('ORG.Mail.EMail');
        $mail = new EMail();
        $mail->setTo($to);
        $mail->setFrom('admin@17358.cc');
        $mail->setSubject($subject);
        $mail->setHTML($content);
        return $mail->send();*/
    }
?>