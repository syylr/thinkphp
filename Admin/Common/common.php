<?php 
// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2005-2006 liu21st.com.  All rights reserved.                |
// | Website: http://www.fcs.org.cn/                                           |
// | Author : Liu21st 流年 <liu21st@gmail.com>                                 |
// +---------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify it   |
// | under the terms of the GNU General Public License as published by the     |
// | Free Software Foundation; either version 2 of the License,  or (at your   |
// | option) any later version.                                                |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,  but      |
// | WITHOUT ANY WARRANTY; without even the implied warranty of                |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General |
// | Public License for more details.                                          |
// +---------------------------------------------------------------------------+
    function getcon($varName)
    {
        switch($res = get_cfg_var($varName))
        {
            case 0:
            return NO;
            break;
            case 1:
            return YES;
            break;
            default:
            return $res;
            break;
        }
         
    }
    function bar($percent)
    {
    ?>
<ul style="border:1px solid #2D2F2C; background:#6C6754; height:8px; font-size:2px;">
	<li style="width:<?=$percent?>%">&nbsp;</li>
</ul>
<?php
    }

        function sys_linux()
    {
        // CPU
        if (false === ($str = @file("/proc/cpuinfo"))) return false;
        $str = implode("", $str);
        @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(.]+)[\r\n]+/", $str, $model);
        //@preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
        @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
        if (false !== is_array($model[1]))
            {
            $res['cpu']['num'] = sizeof($model[1]);
            for($i = 0; $i < $res['cpu']['num']; $i++)
            {
                $res['cpu']['detail'][] = "类型：".$model[1][$i]." 缓存：".$cache[1][$i];
            }
            if (false !== is_array($res['cpu']['detail'])) $res['cpu']['detail'] = implode("<br />", $res['cpu']['detail']);
            }
         
         
        // UPTIME
        if (false === ($str = @file("/proc/uptime"))) return false;
        $str = explode(" ", implode("", $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days."天";
        if ($hours !== 0) $res['uptime'] .= $hours."小时";
        $res['uptime'] .= $min."分钟";
         
        // MEMORY
        if (false === ($str = @file("/proc/meminfo"))) return false;
        $str = implode("", $str);
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
         
        $res['memTotal'] = round($buf[1][0]/1024, 2);
        $res['memFree'] = round($buf[2][0]/1024, 2);
        $res['memUsed'] = ($res['memTotal']-$res['memFree']);
        $res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;
         
        $res['swapTotal'] = round($buf[3][0]/1024, 2);
        $res['swapFree'] = round($buf[4][0]/1024, 2);
        $res['swapUsed'] = ($res['swapTotal']-$res['swapFree']);
        $res['swapPercent'] = (floatval($res['swapTotal'])!=0)?round($res['swapUsed']/$res['swapTotal']*100,2):0;
         
        // LOAD AVG
        if (false === ($str = @file("/proc/loadavg"))) return false;
        $str = explode(" ", implode("", $str));
        $str = array_chunk($str, 3);
        $res['loadAvg'] = implode(" ", $str[0]);
         
        return $res;
    }
/*-------------------------------------------------------------------------------------------------------------
    系统参数探测 FreeBSD
--------------------------------------------------------------------------------------------------------------*/
    function sys_freebsd()
    {
        //CPU
        if (false === ($res['cpu']['num'] = get_key("hw.ncpu"))) return false;
        $res['cpu']['detail'] = get_key("hw.model");
         
        //LOAD AVG
        if (false === ($res['loadAvg'] = get_key("vm.loadavg"))) return false;
        $res['loadAvg'] = str_replace("{", "", $res['loadAvg']);
        $res['loadAvg'] = str_replace("}", "", $res['loadAvg']);
         
        //UPTIME
        if (false === ($buf = get_key("kern.boottime"))) return false;
        $buf = explode(' ', $buf);
        $sys_ticks = time() - intval($buf[3]);
        $min = $sys_ticks / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days."天";
        if ($hours !== 0) $res['uptime'] .= $hours."小时";
        $res['uptime'] .= $min."分钟";
         
        //MEMORY
        if (false === ($buf = get_key("hw.physmem"))) return false;
        $res['memTotal'] = round($buf/1024/1024, 2);
        $buf = explode("\n", do_command("vmstat", ""));
        $buf = explode(" ", trim($buf[2]));
         
        $res['memFree'] = round($buf[5]/1024, 2);
        $res['memUsed'] = ($res['memTotal']-$res['memFree']);
        $res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;
		         
        $buf = explode("\n", do_command("swapinfo", "-k"));
        $buf = $buf[1];
        preg_match_all("/([0-9]+)\s+([0-9]+)\s+([0-9]+)/", $buf, $bufArr);
        $res['swapTotal'] = round($bufArr[1][0]/1024, 2);
        $res['swapUsed'] = round($bufArr[2][0]/1024, 2);
        $res['swapFree'] = round($bufArr[3][0]/1024, 2);
        $res['swapPercent'] = (floatval($res['swapTotal'])!=0)?round($res['swapUsed']/$res['swapTotal']*100,2):0;
         
        return $res;
    }

    /*-------------------------------------------------------------------------------------------------------------
    取得参数值 FreeBSD
--------------------------------------------------------------------------------------------------------------*/
function get_key($keyName)
    {
        return do_command('sysctl', "-n $keyName");
    }
     
/*-------------------------------------------------------------------------------------------------------------
    确定执行文件位置 FreeBSD
--------------------------------------------------------------------------------------------------------------*/
    function find_command($commandName)
    {
        $path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
        foreach($path as $p)
        {
            if (@is_executable("$p/$commandName")) return "$p/$commandName";
        }
        return false;
    }
     
/*-------------------------------------------------------------------------------------------------------------
    执行系统命令 FreeBSD
--------------------------------------------------------------------------------------------------------------*/
    function do_command($commandName, $args)
    {
        $buffer = "";
        if (false === ($command = find_command($commandName))) return false;
        if ($fp = @popen("$command $args", 'r'))
            {
				while (!@feof($fp))
				{
					$buffer .= @fgets($fp, 4096);
				}
				return trim($buffer);
			}
        return false;
    }

        function isfun($funName)
    {
        return (false !== function_exists($funName))?YES:NO;
    }
###################################################################################
# XML_serialize: serializes any PHP data structure into XML
# Takes one parameter: the data to serialize. Must be an array.
###################################################################################
function & XML_serialize(&$data, $level = 0, $prior_key = NULL){
	if($level == 0){ ob_start(); echo '<?xml version="1.0" ?>',"\n"; }
	while(list($key, $value) = each($data))
		if(!strpos($key, ' attr')) #if it's not an attribute
			#we don't treat attributes by themselves, so for an empty element
			# that has attributes you still need to set the element to NULL

			if(is_array($value) and array_key_exists(0, $value)){
				XML_serialize($value, $level, $key);
			}else{
				$tag = $prior_key ? $prior_key : $key;
				echo str_repeat("\t", $level),'<',$tag;
				if(array_key_exists("$key attr", $data)){ #if there's an attribute for this element
					while(list($attr_name, $attr_value) = each($data["$key attr"]))
						echo ' ',$attr_name,'="',htmlspecialchars($attr_value),'"';
					reset($data["$key attr"]);
				}

				if(is_null($value)) echo " />\n";
				elseif(!is_array($value)) echo '>',htmlspecialchars($value),"</$tag>\n";
				else echo ">\n",XML_serialize($value, $level+1),str_repeat("\t", $level),"</$tag>\n";
			}
	reset($data);
	if($level == 0){ $str = &ob_get_contents(); ob_end_clean(); return $str; }
}

    function getUserRate($user,$score) 
    {
        if(!empty($user->rate)) {
        	// 如果有设置某个用户的提成比例 
            // 按照这个固定提成比进行结算
            $rate = $user->rate;
        }else {
            $dao = D("RateDao");
            switch(strtoupper(get_class($user))) {
                case 'DEALERVO':
                    if($user->level==1) {
                        $list  = $dao->findAll("type=3");
                    }else {
                        $list  = $dao->findAll("type=4");
                    }
                    break;
                case 'AGENCYVO':
                    $list  = $dao->findAll("type=2");
                    break;
                case 'GIRLVO':
                    $list  = $dao->findAll("type=5");
                    break;
            }
            if($list) {
                $level   =  $list->getCol("id,level");
                asort($level);
                $rateArray = $list->getCol("id,rate"); 	
                foreach($level as $key=>$val) {
                    if($score<=$val) { 
                        $rate = $rateArray[$key];
                        break;
                    }
                }            	
            }
            
        }
        return $rate;
    }

function getRemark($configName) 
{
        /*if(Session::is_set('_configShowList')) {
        	$show  =  Session::get('_configShowList');
            return $show[$configName];
        }*/
        $dao = D("ConfigDao");
        $list = $dao->findAll();
        $show  =  array();
        foreach($list->getIterator() as $key=>$config) {
        	$show[$config->title] = '<a href="javascript:edit('.$config->id.')" title="'.$config->remark.'">'.$config->title.'</a>';
        }
        Session::set('_configShowList',$show);
        return $show[$configName];
}

    function s2m($fSecond){
        $minute=floor($fSecond/60)."分".(sprintf("%02d",$fSecond%60))."秒";
        return $minute;
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

function readFileList($filename) 
{
    $file = base64_encode($filename);
    $name  =  auto_charset(basename($filename),'gb2312','utf-8');
    if(is_dir($filename)) {
    	$click = "readDir(\"$file\")";
         return "<a href='".__URL__.'/sindex/f/'.$file."'>".$name."</a>";
    }else if(is_file($filename)) {
        $writeable =  is_writable($filename);
    	$click = "parent.readFile(\"$file\",\"$writeable\")";
        return "<a href='javascript:void(0)' onclick='$click'>".$name."</a>";
    }


}
/**
 +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
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
 * 取得下载图标显示 
 * 
 +----------------------------------------------------------
 * @param integer $type 下载文件类型
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getFileIcon($type) 
{
	switch($type) {
		case 'doc': $icon = '<IMG SRC="'.APP_PUBLIC_URL.'/images/ok.gif" BORDER="0" align="absmiddle" ALT="">';break;
	default:
		$icon = '<IMG SRC="'.APP_PUBLIC_URL.'/images/ok.gif" align="absmiddle" BORDER="0" ALT="">';
	}
	return $icon;
}

function getArticleStatus($status) 
{
	switch($status) {
		case 0:$status    = '禁用';
            break;
        case 1:$status    = '草稿';
        	break;
        case 2:$status    = '待审';
        	break;
        case 3:$status    = '发布';
        	break;
        case 4:$status    = '锁定';
        	break;
        default :$status  = '未知';
	}
    return $status;
}
/**
 +------------------------------------------------------------------------------
 * Admin模块自定义函数
 +------------------------------------------------------------------------------
 * @package    Common
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: common.php 2 2007-01-03 07:52:09Z liu21st $
 +------------------------------------------------------------------------------
 */
    function getAppId($appName=APP_NAME) 
    {
        if(Session::is_set('app')) {
            $app	=	Session::get('app');
            //return $app[$appName];
        }
    	import('@.Dao.NodeDao');
        $dao = new NodeDao();
        $appList = $dao->findAll('level=1');
        $app = $appList->getCol('name,id');
        $appName = $app[$appName];
        Session::set('app',$app);
        return $appName;

    }
    function getUserPostCount($userId) 
    {
    	$dao = D("ArticleDao");
        $count  =  $dao->getCount("userId='".$userId."'");
        return $count;
    }

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
/**
 +----------------------------------------------------------
 * 取得状态显示 
 * 
 +----------------------------------------------------------
 * @param integer $status 数据库中存储的状态值
 * @param integer $imageShow 是否显示图形 默认显示
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getStatus($status,$imageShow=true) 
{
    switch($status) {
    	case 0:
            $showText   = '禁用';
            $showImg    = '<IMG SRC="'.APP_PUBLIC_URL.'/images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
            break;
    	case 2:
            $showText   = '保护';
            $showImg    = '<IMG SRC="'.APP_PUBLIC_URL.'/images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="保护">';
            break;
        case 1:
        default:
            $showText   =   '正常';
            $showImg    =   '<IMG SRC="'.APP_PUBLIC_URL.'/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
            
    }
    return ($imageShow===true)? auto_charset($showImg) : $showText;

}

function getGirlStatus($status,$imageShow=false) 
{
    switch($status) {
    	case 0:
            $showText   = '禁用';
            $showImg    = '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
            break;
    	case 2:
            $showText   = '在线';
            $showImg    = '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="保护">';
            break;
        case 1:
        default:
            $showText   =   '离线';
            $showImg    =   '<IMG SRC="'.WEB_URL.'/'.TMPL_DIR.'/'.TEMPLATE_NAME.'/'.APP_NAME.'/Public/images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
            
    }
    return ($imageShow===true)? auto_charset($showImg) : $showText;

}
function getPluginNameUri($pluginId) 
{
	import("@.Dao.PlugInDao");
    $dao = new PlugInDao();
    $vo  = $dao->find('id="'.$pluginId.'"','','id,uri,name');
    $uri  =  '<a href="'.$vo->uri.'" target="_blank" >'.$vo->name.'</a>';
    return $uri;
}

/**
 +----------------------------------------------------------
 * 取得充值卡类型显示 
 * 
 +----------------------------------------------------------
 * @param integer $typeId 充值卡类型id
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getCardType($typeId) 
{
	if(Session::is_set('cardType')) {
		$type	=	Session::get('cardType');
		return $type[$typeId];
	}
	import('@.Dao.CardTypeDao');
	$dao	=	new CardTypeDao();
	$typeList	=	$dao->findAll('','','id,name,status');
	$type	=	$typeList->getCol('id,name');
	$name	=	$type[$typeId];
	Session::set('cardType',$type);
    return $name;

}

function getTypeName($typeId) 
{
    switch($typeId) {
    	case 1: $name  =  '会员';break;
        case 2: $name = '电话';break;
        case 3:$name = '游客';break;
    }
	return $name;
}
/**
 +----------------------------------------------------------
 * 取得充值卡类型显示 
 * 
 +----------------------------------------------------------
 * @param integer $typeId 充值卡类型id
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getGiftName($id) 
{
	if(Session::is_set('GiftName_'.$id)) {
		$name	=	Session::get('GiftName_'.$id);
		return $name;
	}
	import('@.Dao.GiftDao');
	$dao	=	new GiftDao();
	$gift	=	$dao->getById($id);
	$name	=	$gift->name;
	Session::set('GiftName_'.$id,$name);
    return $name;

}

function getGirlPic($id) 
{
        import("@.Dao.AttachDao");
        $attachDao = new AttachDao();
        $attach = $attachDao->find("module='girl' and recordId=$id");
        if( !empty($attach)) {
	        $pic  = "<img src='".WEB_PUBLIC_URL."/images/girl/$id.".$attach->extension."' width='35' height='35' border=0 />";
        }
        return $pic;
}
/**
 +----------------------------------------------------------
 * 取得充值卡类型显示 
 * 
 +----------------------------------------------------------
 * @param integer $typeId 充值卡类型id
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getUserType($typeId) 
{
	if(Session::is_set('userType')) {
		$type	=	Session::get('userType');
		return $type[$typeId];
	}
	import('@.Dao.UserTypeDao');
	$dao	=	new UserTypeDao();
	$typeList	=	$dao->findAll('','','id,name,status');
	$type	=	$typeList->getCol('id,name');
	$name	=	$type[$typeId];
	Session::set('userType',$type);
    return $name;

}

function getGroupName($id) 
{
    if($id==0) {
    	return '无上级组';
    }
	if(Session::is_set('groupName')) {
		$name	=	Session::get('groupName');
		return $name[$id];
	}
	import('@.Dao.GroupDao');
	$dao	=	new GroupDao();
	$list	=	$dao->findAll('','','id,name');
	$nameList	=	$list->getCol('id,name');
	$name	=	$nameList[$id];
	Session::set('groupName',$nameList);
    return $name;

}

function getBlockName($id) 
{
    if($id==0) {
    	return '无上级版块';
    }
	if(Session::is_set('blockName')) {
		$name	=	Session::get('blockName');
		return $name[$id];
	}
	import('@.Dao.BlockDao');
	$dao	=	new BlockDao();
	$list	=	$dao->findAll('','','id,title');
	$nameList	=	$list->getCol('id,title');
	$name	=	$nameList[$id];
	Session::set('blockName',$nameList);
    return $name;

}

function getRoomName($id) 
{
	if(Session::is_set('roomName')) {
		$name	=	Session::get('roomName');
		return $name[$id];
	}
	import('@.Dao.RoomDao');
	$dao	=	new RoomDao();
	$list	=	$dao->findAll('','','id,name');
	$nameList	=	$list->getCol('id,name');
	$name	=	$nameList[$id];
	Session::set('roomName',$nameList);
    return $name;

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

function getMemberAccount($userId) 
{
    if($userId==0) {
    	return '系统';
    }
	if(Session::is_set('memberAccount')) {
		$name	=	Session::get('memberAccount');
		return $name[$userId];
	}
	$dao	=	D("MemberDao");
	$list	=	$dao->findAll('','','id,name');
	$nameList	=	$list->getCol('id,name');
	$name	=	$nameList[$userId];
	Session::set('memberAccount',$nameList);
    return $name;

}

function getGirlName($userId) 
{
	if(Session::is_set('girlName')) {
		$name	=	Session::get('girlName');
		return $name[$userId];
	}
	import('@.Dao.GirlDao');
	$dao	=	new GirlDao();
	$list	=	$dao->findAll('','','id,name');
	$nameList	=	$list->getCol('id,name');
	$name	=	$nameList[$userId];
	Session::set('girlName',$nameList);
    return $name;

}

function getGirlNickName($userId) 
{
	if(Session::is_set('girlNickName')) {
		$name	=	Session::get('girlNickName');
		return $name[$userId];
	}
	import('@.Dao.GirlDao');
	$dao	=	new GirlDao();
	$list	=	$dao->findAll('','','id,nickname');
	$nameList	=	$list->getCol('id,nickname');
	$name	=	$nameList[$userId];
	Session::set('girlNickName',$nameList);
    return $name;

}
function dateDiff($date1,$date2) 
{
	import('ORG.Date.Date');
	$date	=	new Date(intval($date1));
	$return	=	$date->dateDiff($date2);
	if($return<=1) {
		$return =	'今天';
	}elseif($return <=2) {
		$return = '昨天';
	}elseif($return <=7) {
		$return = $date->cWeekday;
	}
	elseif($return>7 && $return <14) {
		$return = '上周';
	}
	else {
		$return = '两周前';
	}
	return $return;
}

function getMemberName($userId) 
{
	if(Session::is_set('memberName')) {
		$name	=	Session::get('memberName');
		return $name[$userId];
	}
	import('@.Dao.MemberDao');
	$dao	=	new MemberDao();
	$list	=	$dao->findAll('','','id,name');
	$nameList	=	$list->getCol('id,name');
	$name	=	$nameList[$userId];
	Session::set('memberName',$nameList);
    return $name;

}

/**
 +----------------------------------------------------------
 * 取得充值卡类型显示 
 * 
 +----------------------------------------------------------
 * @param integer $typeId 充值卡类型id
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getDealer($id) 
{
	import('@.Dao.DealerDao');
	$dao	=	new DealerDao();
	$typeList	=	$dao->findAll('','','id,company');
	$type	=	$typeList->getCol('id,company');
	$name	=	$type[$id];

    return $name;

}

function getAgency($id) 
{
	import('@.Dao.AgencyDao');
	$dao	=	new AgencyDao();
	$typeList	=	$dao->findAll('','','id,company');
	$type	=	$typeList->getCol('id,company');
	$name	=	$type[$id];

    return $name;

}
/**
 +----------------------------------------------------------
 * 取得充值卡类型显示 
 * 
 +----------------------------------------------------------
 * @param integer $typeId 充值卡类型id
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getCardStatus($status) 
{
	switch($status) {
		case 0:$show = '未启用';break;
		case 1:$show = '已启用'; break;
		case 2:$show = '使用中'; break;
		case 3:$show = '已禁用'; break;
		case 4:$show = '已作废'; break;
	}
    return $show;

}

function getAdPos($pos) 
{
	switch($pos) {
		case 0:$show = '首页';break;
		case 1:$show = '主持人页面'; break;
		case 2:$show = '聊天页面'; break;

	}
    return $show;

}


function getMessageStatus($status) 
{
	switch($status) {
		case 2: $icon = '<IMG SRC="'.APP_PUBLIC_URL.'/images/hasread.gif" BORDER="0"  ALT="已读">';break;
	default:
		$icon = '<IMG SRC="'.APP_PUBLIC_URL.'/images/newmessage.gif" BORDER="0" ALT="未读">';
	}
	return $icon;
}

/**
 +----------------------------------------------------------
 * 取得下载图标显示 
 * 
 +----------------------------------------------------------
 * @param integer $type 下载文件类型
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getRecommend($type) 
{
	switch($type) {
		case 1: $icon = '<IMG SRC="'.APP_PUBLIC_URL.'/images/allow.gif" BORDER="0" align="absmiddle" ALT="">';break;
	default:
		$icon = '';
	}
	return $icon;
}


/**
 +----------------------------------------------------------
 * 取得标题的截断显示 默认截断长度为12 个字 
 * 
 +----------------------------------------------------------
 * @param string $title 标题
 * @param integer $length 截断长度 默认12
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getShortTitle($title,$length=12) 
{
    return msubstr ($title,0,$length,OUTPUT_CHARSET);
}


function getOrderStatus($status) 
{
		switch($status) {
		case 0:$show = '未付款';break;
		case 1:$show = '已付款'; break;
		case 2:$show = '已取消'; break;
	}
    return $show;
}
/**
 +----------------------------------------------------------
 * 取得充值卡类型显示 
 * 
 +----------------------------------------------------------
 * @param integer $typeId 充值卡类型id
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getModule($name) 
{
	if(Session::is_set('module')) {
		$module	=	Session::get('module');
		return $module[$name];
	}
	import('@.Dao.ModuleDao');
	$dao	=	new ModuleDao();
	$moduleList	=	$dao->findAll('','','name,title');
	$module	=	$moduleList->getCol('name,title');
	$title	=	$module[$name];
	Session::set('module',$module);
    return $title;

}

/**
 +----------------------------------------------------------
 * 取得充值卡类型显示 
 * 
 +----------------------------------------------------------
 * @param integer $typeId 充值卡类型id
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function getAction($name) 
{
	if(Session::is_set('action')) {
		$action	=	Session::get('action');
		return $action[$name];
	}
	import('@.Dao.ActionDao');
	$dao	=	new ActionDao();
	$actionList	=	$dao->findAll('','','name,title');
	$action	=	$actionList->getCol('name,title');
	$title	=	$action[$name];
	Session::set('action',$action);
    return $title;
}
function getServerType($type) 
{
	switch(strtoupper($type)) {
		case 'FMS': 
            $type = '视频服务器 [ <a href="javascript:void(0)" onclick="PopWindow(\''.__APP__.'/FMS/\',920,600)" >管理</a> ]';
            break;
        case 'WEB':
        	$type = 'WEB服务器';
        	break;
	}
    return $type;
}
function getUser($id) 
{
	if(Session::is_set('user')) {
		$user	=	Session::get('user');
		return $user[$id];
	}
	import('@.Dao.UserDao');
	$dao	=	new UserDao();
	$userList	=	$dao->findAll('','','id,name,nickname');
	$user	=	$userList->getCol('id,name');
	$name	=	$user[$id];
	Session::set('user',$user);
    return $name;	
}

    function readConfig() 
    {
        if(!Session::is_set('config')) {
            import("@.Dao.ConfigDao");
            $dao = new ConfigDao();
            $list =  $dao->findAll();
            $config =  array();
            foreach($list->getIterator() as $key=>$val) {
                $config[$val->title] =  $val->value;
            }
            Session::set('config',$config);        	
        }
        $config =  Session::get('config');
        foreach($config as $key=>$val) {
            if(!defined(strtoupper('conf_'.$key))) {
                define(strtoupper('conf_'.$key),$val);
            }
        }
    }

            
?>