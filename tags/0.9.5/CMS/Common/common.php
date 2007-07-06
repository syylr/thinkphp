<?php 

    function getCategoryName($id) 
    {
        if(Session::is_set('categoryList')) {
        	$list  = Session::get('categoryList');
            return $list[$id];
        }
    	$dao = D("CategoryDao");
        $list  = $dao->findAll("status=1",'',"id,title");
        $cateList   =  $list->getCol("id,title");
        Session::set('categoryList',$cateList);
        return $cateList[$id];
    }
function getUserName($userId) 
{
    if($userId==0) {
    	return '系统';
    }
	if(Session::is_set('userName')) {
		$name	=	Session::get('userName');
		return $name[$userId];
	}
	import('@.Dao.UserDao');
	$dao	=	new UserDao();
	$list	=	$dao->findAll('','','id,nickname');
	$nameList	=	$list->getCol('id,nickname');
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
      $Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text); 
      $Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text); 
      $Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text); 
      $Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text); 
      $Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text); 
      $Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text); 
      $Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text); 
      $Text=preg_replace("/\[url\](http:\/\/.+?)\[\/url\]/is","<a href=\\1>\\1</a>",$Text); 
      $Text=preg_replace("/\[url\](.+?)\[\/url\]/is","<a href=\"http://\\1\">http://\\1</a>",$Text); 
      $Text=preg_replace("/\[url=(http:\/\/.+?)\](.*)\[\/url\]/is","<a href=\\1>\\2</a>",$Text); 
      $Text=preg_replace("/\[url=(.+?)\](.*)\[\/url\]/is","<a href=http://\\1>\\2</a>",$Text); 
      $Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$Text); 
      $Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$Text); 
      $Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$Text); 
      $Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text); 
      $Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text); 
      $Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text); 
      $Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$Text); 
      $Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text); 
      $Text=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$Text); 
      $Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text); 
      $Text=preg_replace("/\[quote\](.+?)\[\/quote\]/is","<blockquote><font size='1' face='Courier New'>quote:</font><hr>\\1<hr></blockquote>", $Text); 
      $Text=preg_replace("/\[code\](.+?)\[\/code\]/eis","highlight_code('\\1')", $Text); 
      $Text=preg_replace("/\[php\](.+?)\[\/php\]/eis","highlight_code('\\1')", $Text); 
      $Text=preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div style='text-align: left; color: darkgreen; margin-left: 5%'><br><br>--------------------------<br>\\1<br>--------------------------</div>", $Text); 
      return $Text; 
    }

function getShortTitle($title,$length=12) 
{
    return msubstr ($title,0,$length,OUTPUT_CHARSET);
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
?>