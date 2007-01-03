<?php 
/*
Plugin Name: VStats
Plugin URI: http://www.topthink.com.cn/
Description: 网站统计插件
Author: 流年
Version: 1.0
Author URI: http://www.topthink.com.cn/
*/ 
// 统计类
 class VStats extends Base
 {
    var $table_stats;
    var $table_search;
    var $languages;
    var $db;
    /**
     +----------------------------------------------------------
     * 
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function __construct() 
    {
		// 统计数据表
		$this->table_stats  = DB_PREFIX. "_vstats";
        // 搜索数据表
		$this->table_search = DB_PREFIX . "_search";   
        // 语言对应的国家代码
		$this->languages = array( "af" => "Afrikaans", "sq" => "Albanian", "eu" => "Basque", "bg" => "Bulgarian", "be" => "Byelorussian", "ca" => "Catalan", "zh" => "Chinese", "zh-cn" => "Chinese/China", "zh-tw" => "Chinese/Taiwan", "zh-hk" => "Chinese/Hong Kong", "zh-sg" => "Chinese/singapore", "hr" => "Croatian", "cs" => "Czech", "da" => "Danish", "nl" => "Dutch", "nl-nl" => "Dutch/Netherlands", "nl-be" => "Dutch/Belgium", "en" => "English", "en-gb" => "English/United Kingdom", "en-us" => "English/United States", "en-au" => "English/Australian", "en-ca" => "English/Canada", "en-nz" => "English/New Zealand", "en-ie" => "English/Ireland", "en-za" => "English/South Africa", "en-jm" => "English/Jamaica", "en-bz" => "English/Belize", "en-tt" => "English/Trinidad", "et" => "Estonian", "fo" => "Faeroese", "fa" => "Farsi", "fi" => "Finnish", "fr" => "French", "fr-be" => "French/Belgium", "fr-fr" => "French/France", "fr-ch" => "French/Switzerland", "fr-ca" => "French/Canada", "fr-lu" => "French/Luxembourg", "gd" => "Gaelic", "gl" => "Galician", "de" => "German", "de-at" => "German/Austria", "de-de" => "German/Germany", "de-ch" => "German/Switzerland", "de-lu" => "German/Luxembourg", "de-li" => "German/Liechtenstein", "el" => "Greek", "he" => "Hebrew", "he-il" => "Hebrew/Israel", "hi" => "Hindi", "hu" => "Hungarian", "ie-ee" => "Internet Explorer/Easter Egg", "is" => "Icelandic", "id" => "Indonesian", "in" => "Indonesian", "ga" => "Irish", "it" => "Italian", "it-ch" => "Italian/ Switzerland", "ja" => "Japanese", "ko" => "Korean", "lv" => "Latvian", "lt" => "Lithuanian", "mk" => "Macedonian", "ms" => "Malaysian", "mt" => "Maltese", "no" => "Norwegian", "pl" => "Polish", "pt" => "Portuguese", "pt-br" => "Portuguese/Brazil", "rm" => "Rhaeto-Romanic", "ro" => "Romanian", "ro-mo" => "Romanian/Moldavia", "ru" => "Russian", "ru-mo" => "Russian /Moldavia", "gd" => "Scots Gaelic", "sr" => "Serbian", "sk" => "Slovack", "sl" => "Slovenian", "sb" => "Sorbian", "es" => "Spanish", "es-do" => "Spanish", "es-ar" => "Spanish/Argentina", "es-co" => "Spanish/Colombia", "es-mx" => "Spanish/Mexico", "es-es" => "Spanish/Spain", "es-gt" => "Spanish/Guatemala", "es-cr" => "Spanish/Costa Rica", "es-pa" => "Spanish/Panama", "es-ve" => "Spanish/Venezuela", "es-pe" => "Spanish/Peru", "es-ec" => "Spanish/Ecuador", "es-cl" => "Spanish/Chile", "es-uy" => "Spanish/Uruguay", "es-py" => "Spanish/Paraguay", "es-bo" => "Spanish/Bolivia", "es-sv" => "Spanish/El salvador", "es-hn" => "Spanish/Honduras", "es-ni" => "Spanish/Nicaragua", "es-pr" => "Spanish/Puerto Rico", "sx" => "Sutu", "sv" => "Swedish", "sv-se" => "Swedish/Sweden", "sv-fi" => "Swedish/Finland", "ts" => "Thai", "tn" => "Tswana", "tr" => "Turkish", "uk" => "Ukrainian", "ur" => "Urdu", "vi" => "Vietnamese", "xh" => "Xshosa", "ji" => "Yiddish", "zu" => "Zulu");
        $this->db  =  DB::getInstance();
    }

    /**
     +----------------------------------------------------------
     * 创建数据表
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function maybe_create_table($table_name, $create_ddl) 
    {
        foreach($this->db->getTables() as $table) {
            if ($table == $table_name) return true;
        }
        $this->db->query($create_ddl);
        // we cannot directly tell that whether this succeeded!
        foreach ($this->db->getTables() as $table ) {
            if ($table == $table_name) return true;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 统计插件安装和初始化
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	function setup() 
    {
        // 数据表已经创建 ，直接返回
        if(Session::is_set('shortStat_table')) return;
        // 创建数据表
		$table_stats_query = "CREATE TABLE $this->table_stats (
							  id int(11) unsigned NOT NULL auto_increment,
							  ip varchar(15) NOT NULL default '',
							  country varchar(50) NOT NULL default '',
                              area varchar(50) NOT NULL default '',
							  language VARCHAR(5) NOT NULL default '',
							  domain varchar(255) NOT NULL default '',
							  referer varchar(255) NOT NULL default '',
							  resource varchar(255) NOT NULL default '',
							  user_agent varchar(255) NOT NULL default '',
							  platform varchar(50) NOT NULL default '',
							  browser varchar(50) NOT NULL default '',
                              screen varchar(50) NOT NULL default '',
                              color varchar(50) NOT NULL default '',
                              flash varchar(50) NOT NULL default '',
							  version varchar(15) NOT NULL default '',
                              stype varchar(50) NOT NULL default '',
							  vtime int(10) unsigned NOT NULL default '0',
							  UNIQUE KEY id (id)
							  ) TYPE=MyISAM DEFAULT CHARSET=utf8 ";
			  
		$table_search_query = "CREATE TABLE $this->table_search (
							  id int(11) unsigned NOT NULL auto_increment,
							  searchterms varchar(255) NOT NULL default '',
							  count int(10) unsigned NOT NULL default '0',
							  PRIMARY KEY  (id)
							  ) TYPE=MyISAM DEFAULT CHARSET=utf8 ;";
		
		$this->maybe_create_table($this->table_stats, $table_stats_query);
		$result  =  $this->maybe_create_table($this->table_search, $table_search_query);
        if($result) {
        	// 创建数据表成功 或者已经创建
            Session::set('shortStat_table',true);
        }
		return ;
	} 	

    /**
     +----------------------------------------------------------
     * 跟踪访问 记录统计数据
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	function track($info=array()) 
    {			
		//if(APP_NAME=='Admin') return; 
        
        if(!empty($info)) {
        	$flashver   = $info['flashver'];
            $scrWidth  = $info['scrWidth'];
            $scrHeight = $info['scrHeight'];
           	$screen     = !isset($scrWidth)? '':$scrWidth.'×'.$scrHeight;
            $color       = !isset($info['color'])?'':$info['color'].'位';
        }
		$ip		= $_SERVER['REMOTE_ADDR'];
		$coun	= $this->determineCountry($ip);
        $cntry   = $coun['country'];
        $area    = $coun['area'];
		$lang	    = $this->determineLanguage();
		$ref	    = $_SERVER['HTTP_REFERER'];
		$url 	    = parse_url($ref);
		$domain= preg_replace("/^www./i","",$url['host']);
		$res	    = $_SERVER['REQUEST_URI'];
		$ua		= $_SERVER['HTTP_USER_AGENT'];
		$br		= $this->parseUserAgent($ua);
        $keyword        = $this->getSearchType($url);
        $st        = $keyword['type'];
		$dt	= time();

		$this->sniffKeywords($url);
		
		$query = "INSERT INTO $this->table_stats (ip,country,area,language,domain,referer,resource,user_agent,platform,browser,version,stype,vtime,screen,color,flash) 
				  VALUES ('$ip','$cntry','$area','$lang','$domain','$ref','$res','$ua','$br[platform]','$br[browser]','$br[version]','$st',$dt,'$screen','$color','$flashver')";
		
		$this->db->query($query);
	}

    /**
     +----------------------------------------------------------
     * 根据IP定位国家和区域
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	function determineCountry($ip)
    {
        import("ORG.Net.IpLocation");
		$iplocation = new IpLocation(); 
        $location = $iplocation->getlocation($ip); 
        $country   =  array();
        $country['country'] =  auto_charset($location['country'],'gb2312','utf-8');
        if('保留地址'==$country['country']) {
        	$country['country'] ='';
        }
        $country['area']  = auto_charset($location['area'],'gb2312','utf-8');
        $country['0']   =  auto_charset($location['country'].' '.$location['area'],'gb2312','utf-8');
        /*
		$coinfo = @file_get_contents('http://api.hostip.info/get_html.php?ip=' . $ip);
		$country_string = explode(':',$coinfo[0]);
		$country = trim($country_string[1]);
		
		if($country == '(Private Address) (XX)' 
		|| $country == '(Unknown Country?) (XX)' 
		|| $country == '' 
		|| !$country 
		  )return 'Indeterminable';
			*/
		return $country;
    }

    function getSearchType($url) 
    {
        $kw  = array();
		if (preg_match("/google\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['q'];
            $kw['type']  =  'Google';
			}
		else if (preg_match("/baidu\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['wd'];
            $kw['type']  =  '百度';
			}
		else if (preg_match("/163\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['q'];
            $kw['type']  =  '网易';
			}
		else if (preg_match("/soso\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['w'];
            $kw['type']  =  '腾讯';
			}
		else if (preg_match("/sogou\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['query'];
            $kw['type']  =  '搜狗';
			}
		else if (preg_match("/yahoo\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['p'];
            $kw['type']  =  '雅虎';
			}
		else if (preg_match("/search\.msn\./i", $url['host']) || preg_match("/live\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['q'];
            $kw['type']  =  'Msn&Live';
			}
		else if (preg_match("/tom\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['word'];
            $kw['type']  =  'Tom';
			}
		else if (preg_match("/iask\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['_searchkey'];
            $kw['type']  =  '新浪';
			}
		else if (preg_match("/search\.aol\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['query'];
            $kw['type']  =  'Aol';
			}
		else if (preg_match("/web\.ask\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['q'];
            $kw['type']  =  'Ask';
			}
		else if (preg_match("/search\.looksmart\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['p'];
            $kw['type']  =  'Looksmart';
			}
		else if (preg_match("/alltheweb\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['q'];
            $kw['type']  =  'Alltheweb';
			}
		else if (preg_match("/a9\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['q'];
            $kw['type']  =  'A9';
			}
		else if (preg_match("/gigablast\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['q'];
            $kw['type']  =  'Gigablast';
			}
		else if (preg_match("/s\.teoma\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['q'];
            $kw['type']  =  'Teoma';
			}
		else if (preg_match("/clusty\./i", $url['host'])) {
			parse_str($url['query'],$q);
			$kw['word'] = $q['query'];
            $kw['type']  =  'Clusty';
		}
      	return $kw;
    }
    /**
     +----------------------------------------------------------
     * 分析关键字
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function sniffKeywords($url) 
    { // $url should be an array created by parse_url($ref)
		
        $keyword  = $this->getSearchType($url);
	    $keyword  = $keyword['word'];
		if (!empty($keyword)) {
			// Remove BINARY from the SELECT statement for a case-insensitive comparison
			$exists_query = "SELECT id FROM $this->table_search WHERE searchterms = BINARY '$keyword'";
			$search_term_id = $this->db->getOne('id',$exists_query);
			
			if( $search_term_id ) {
				$query = "UPDATE $this->table_search SET count = (count+1) WHERE id = $search_term_id";
			} else {
				$query = "INSERT INTO $this->table_search (searchterms,count) VALUES ('$keyword',1)";
			}
			$this->db->query($query);
		}
	}

    /**
     +----------------------------------------------------------
     * 分析操作系统、浏览器
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	function parseUserAgent($ua)
    {
		$browser['platform']	= "Indeterminable";
		$browser['browser']	= "Indeterminable";
		$browser['version']		= "Indeterminable";
		$browser['majorver']	= "Indeterminable";
		$browser['minorver']	= "Indeterminable";
		
		// Test for platform
		if (preg_match('/Win95/i',$ua)) {
			$browser['platform'] = "Windows 95";
			}
		else if (preg_match('/Win98/i',$ua)) {
			$browser['platform'] = "Windows 98";
			}
		else if (preg_match('/Win 9x 4.90/i',$ua)) {
			$browser['platform'] = "Windows ME";
			}
		else if (preg_match('/Windows NT 5.0/i',$ua)) {
			$browser['platform'] = "Windows 2000";
			}
		else if (preg_match('/Windows NT 5.1/i',$ua)) {
			$browser['platform'] = "Windows XP";
			}
		else if (preg_match('/Windows NT 5.2/i',$ua)) {
			$browser['platform'] = "Windows 2003";
			}
		else if (preg_match('/Windows NT 6.0/i',$ua)) {
			$browser['platform'] = "Windows Longhorn beta";
			}
		else if (preg_match('/Windows/i',$ua)) {
			$browser['platform'] = "Windows";
			}
		else if (preg_match('/Mac OS X/i',$ua)) {
			$browser['platform'] = "Mac OS X";
			}
		else if (preg_match('/Macintosh/i',$ua)) {
			$browser['platform'] = "Mac OS Classic";
			}
		else if (preg_match('/Linux/i',$ua)) {
			$browser['platform'] = "Linux";
			}
		else if (preg_match('/BSD/i',$ua) || preg_match('/FreeBSD/i',$ua) || preg_match('/NetBSD/',$ua)) {
		$browser['platform'] = "BSD";
			}
		else if (preg_match('/SunOS/i',$ua)) {
			$browser['platform'] = "Solaris";
			}
		else if (preg_match('/CentOS/i',$ua)) {
			$browser['platform'] = "CentOs";
			}

		// Test for browser type
		if (preg_match('/Mozilla\/4/i',$ua) && !preg_match('/compatible/i',$ua)) {
			$browser['browser'] = "Netscape";
			preg_match('/Mozilla\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Mozilla\/5/i',$ua) || preg_match('/Gecko/i',$ua)) {
			$browser['browser'] = "Mozilla";
			preg_match('/rv(:| )([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[2];
			}
		if (preg_match('/Safari/i',$ua)) {
			$browser['browser'] = "Safari";
			$browser['platform'] = "Mac OS X";
			preg_match('/Safari/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];

			if (preg_match('/412/i',$browser['version'])) {
				$browser['version'] 	= 2.0;
				$browser['majorver']	= 2;
				$browser['minorver']	= 0;
				}
			else if (preg_match('/312/i',$browser['version'])) {
				$browser['version'] 	= 1.3;
				$browser['majorver']	= 1;
				$browser['minorver']	= 3;
				}
			else if (preg_match('/125/i',$browser['version'])) {
				$browser['version'] 	= 1.2;
				$browser['majorver']	= 1;
				$browser['minorver']	= 2;
				}
			else if (preg_match('/100/i',$browser['version'])) {
				$browser['version'] 	= 1.1;
				$browser['majorver']	= 1;
				$browser['minorver']	= 1;
				}
			else if (preg_match('/85/i',$browser['version'])) {
				$browser['version'] 	= 1.0;
				$browser['majorver']	= 1;
				$browser['minorver']	= 0;
				}
			else if ($browser['version']<85) {
				$browser['version'] 	= "1.0 beta";
				}
			}
		if (preg_match('/iCab/i',$ua)) {
			$browser['browser'] = "iCab";
			preg_match('/iCab ([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Firefox/i',$ua)) {
			$browser['browser'] = "Firefox";
			preg_match('/Firefox\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Firebird/i',$ua)) {
			$browser['browser'] = "Firebird";
			preg_match('/Firebird\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Phoenix/i',$ua)) {
			$browser['browser'] = "Phoenix";
			preg_match('/Phoenix\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Camino/i',$ua)) {
			$browser['browser'] = "Camino";
			preg_match('/Camino/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Chimera/i',$ua)) {
			$browser['browser'] = "Chimera";
			preg_match('/Chimera\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Netscape/i',$ua)) {
			$browser['browser'] = "Netscape";
			preg_match('/Netscape[0-9]?\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/MSIE/i',$ua)) {
			$browser['browser'] = "Internet Explorer";
			preg_match('/MSIE ([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/MSN Explorer/i',$ua)) {
			$browser['browser'] = "MSN Explorer";
			preg_match('/MSN Explorer ([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/AOL/i',$ua)) {
			$browser['browser'] = "AOL";
			preg_match('/AOL ([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/America Online Browser/i',$ua)) {
			$browser['browser'] = "AOL Browser";
			preg_match('/America Online Browser ([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/K-Meleon/i',$ua)) {
			$browser['browser'] = "K-Meleon";
			preg_match('/K-Meleon\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Beonex/i',$ua)) {
			$browser['browser'] = "Beonex";
			preg_match('/Beonex\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Opera/i',$ua)) {
			$browser['browser'] = "Opera";
			preg_match('/Opera( |/)([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[2];
			}
		if (preg_match('/OmniWeb/i',$ua)) {
			$browser['browser'] = "OmniWeb";
			preg_match('/OmniWeb\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];

			if (preg_match('/563/',$browser['version'])) {
				$browser['version'] 	= 5.1;
				$browser['majorver']	= 5;
				$browser['minorver']	= 1;
				}
			else if (preg_match('/558/',$browser['version'])) {
				$browser['version'] 	= 5.0;
				$browser['majorver']	= 5;
				$browser['minorver']	= 0;
				}
			else if (preg_match('/496/',$browser['version'])) {
				$browser['version'] 	= 4.5;
				$browser['majorver']	= 4;
				$browser['minorver']	= 5;
				}
			}
		if (preg_match('/Konqueror/i',$ua)) {
			$browser['platform'] = "Linux";
			$browser['browser'] = "Konqueror";
			preg_match('/Konqueror\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Galeon/i',$ua)) {
			$browser['browser'] = "Galeon";
			preg_match('/Galeon\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Epiphany/i',$ua)) {
			$browser['browser'] = "Epiphany";
			preg_match('/Epiphany\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Kazehakase/i',$ua)) {
			$browser['browser'] = "Kazehakase";
			preg_match('/Kazehakase\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/amaya/i',$ua)) {
			$browser['browser'] = "Amaya";
			preg_match('/amaya\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Crawl/i',$ua) || preg_match('/bot/i',$ua) || preg_match('/slurp/i',$ua) || preg_match('/spider/i',$ua)) {
			$browser['browser'] = "Crawler/Search Engine";
			}
		if (preg_match('/Lynx/i',$ua)) {
			$browser['browser'] = "Lynx";
			preg_match('/Lynx\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/Links/i',$ua)) {
			$browser['browser'] = "Links";
			preg_match('/\(([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		if (preg_match('/ELinks/i',$ua)) {
			$browser['browser'] = "ELinks";
			preg_match('/ELinks\/([\d\.]+)/i',$ua,$b);
			$browser['version'] = $b[1];
			}
		
		// Determine browser versions
		if (($browser['browser']!='AppleWebKit' || $browser['browser']!='OmniWeb') && $browser['browser'] != "Indeterminable" && $browser['browser'] != "Crawler/Search Engine" && $browser['version'] != "Indeterminable") {
			// Make sure we have at least .0 for a minor version for Safari and OmniWeb
			$browser['version'] = (!preg_match('/\./',$browser['version']))?$browser['version'].".0":$browser['version'];
			
			preg_match('/^([0-9]*).(.*)$/',$browser['version'],$v);
			$browser['majorver'] = $v[1];
			$browser['minorver'] = $v[2];
			}
		if (empty($browser['version']) || $browser['version']=='.0') {
			$browser['version']		= "Indeterminable";
			$browser['majorver']	= "Indeterminable";
			$browser['minorver']	= "Indeterminable";
			}
		
		return $browser;
	}
	
    /**
     +----------------------------------------------------------
     * 分析浏览器语言
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	function determineLanguage() 
    {
		$lang_choice = "empty"; 
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			// Capture up to the first delimiter (, found in Safari)
			preg_match("/([^,;]*)/",$_SERVER["HTTP_ACCEPT_LANGUAGE"],$langs);
			$lang_choice = $langs[0];
		}
		return $lang_choice;
	}

    // 用于显示统计数据的一些方法
   // 获取关键字访问统计
    function getKeywords($map=null) 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT searchterms as keyword, count  FROM $this->table_search $where ORDER BY count DESC	  LIMIT 0,36";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_search);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}
   // 获取关键字访问统计
    function getSearchTypes($map=null) 
    {
        $map->put('stype',array('neq',''));
        $where =  $this->db->parseWhere($map);
		$query = "SELECT stype, COUNT(stype) AS 'total'  FROM $this->table_stats $where GROUP BY stype  ORDER BY total DESC, vtime DESC";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}
    // 获取引用
	function getReferers($map=null) 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT referer, resource, vtime  FROM $this->table_stats  WHERE referer NOT LIKE '%".$this->trimReferer($_SERVER['SERVER_NAME'])."%' AND referer!=''   ORDER BY vtime DESC   LIMIT 0,36";
		$results = $this->db->getAll($query,1) ;
		return $results;
	}
	
   // 获取域名访问统计
	function getDomains($map=null) 
    {	
        $where =  $this->db->parseWhere($map);
		$query = "SELECT domain, referer, resource, COUNT(domain) AS 'total'   FROM $this->table_stats  $where GROUP BY domain  ORDER BY total DESC, vtime DESC";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}
   // 获取国家访问统计	
	function getCountries($map=null) 
    {
        $map->put('country',array('neq',''));
        $where =  $this->db->parseWhere($map);
		$query = "SELECT country,area, COUNT(country) AS 'total'  FROM $this->table_stats  $where  GROUP BY country,area ORDER BY total DESC";		
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}
	//获取页面访问统计
	function getResources() 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT resource, referer, COUNT(resource) AS 'requests'  FROM $this->table_stats  $where  GROUP BY resource ORDER BY requests DESC  LIMIT 0,36";
		$results = $this->db->getAll($query,1) ;
		return $results;
	}
	
	//获取平台访问统计
	function getPlatforms($map=null) 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT platform, COUNT(platform) AS 'total' FROM $this->table_stats	 $where GROUP BY platform  ORDER BY total DESC";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}
	//获取浏览器访问统计
	function getBrowsers($map=null) 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT browser, version, COUNT(*) AS 'total'  FROM $this->table_stats  $where GROUP BY browser, version  ORDER BY total DESC";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}
	//获取屏幕分辩率访问统计
	function getScreens($map=null) 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT screen, COUNT(*) AS 'total'  FROM $this->table_stats  $where GROUP BY screen  ORDER BY total DESC";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}
	//获取浏览器访问统计
	function getFlashVers($map=null) 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT flash, COUNT(*) AS 'total'  FROM $this->table_stats  $where GROUP BY flash  ORDER BY total DESC";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}

	// 获取浏览器语言
	function getLanguage($map) 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT language, COUNT(language) AS 'total'   FROM $this->table_stats  $where  GROUP BY language  ORDER BY total DESC";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}

	//获取浏览器访问统计
	function getColorDepts($map=null) 
    {
        $where =  $this->db->parseWhere($map);
		$query = "SELECT color, COUNT(*) AS 'total'  FROM $this->table_stats  $where GROUP BY color  ORDER BY total DESC";
		$results = $this->db->getAll($query,1) ;
        $total    = $this->db->count($map,$this->table_stats);
        foreach($results as $key=>$val) {
        	$val->per  = round(($val->total/$total)*100);
        }
		return $results;
	}

	//获取总的访问次数
	function getTotalHits()
    {
		$query = "SELECT COUNT(*) AS 'total' FROM $this->table_stats";
		return $this->db->getOne('total',$query);
	}
    //获取第一次访问时间
	function getFirstHit() 
    {
		$query = "SELECT vtime FROM $this->table_stats ORDER BY vtime ASC LIMIT 0,1";
		return $this->db->getOne('vtime',$query);
	}
    //获取唯一IP访问统计
	function getUniqueHits()
    {
		$query = "SELECT COUNT(DISTINCT ip) AS 'total' FROM $this->table_stats";
		return $this->db->getOne('total',$query);
	}
    //获取今日访问次数
	function getTodayHits() 
    {
		$query = "SELECT COUNT(*) AS 'total' FROM $this->table_stats WHERE vtime >= ".strtotime('today');
		return $this->db->getOne('total',$query);
	}
    //获取今日唯一iP访问次数
	function getTodayUniqueHits() 
    {
		$query = "SELECT COUNT(DISTINCT ip) AS 'total' FROM $this->table_stats WHERE vtime >=".strtotime('today');
		return $this->db->getOne('total',$query);
	}
    //获取今日访问次数
	function getYesterdayHits() 
    {
		$query = "SELECT COUNT(*) AS 'total' FROM $this->table_stats WHERE vtime >= ".strtotime('yesterday')." and vtime <".strtotime('today');
		return $this->db->getOne('total',$query);
	}
    //获取今日唯一iP访问次数
	function getYesterdayUniqueHits() 
    {
		$query = "SELECT COUNT(DISTINCT ip) AS 'total' FROM $this->table_stats WHERE vtime >=".strtotime('yesterday')." and vtime <".strtotime('today');
		return $this->db->getOne('total',$query);
	}
    //获取今日访问次数
	function getLastWeekHits() 
    {
		$query = "SELECT COUNT(*) AS 'total' FROM $this->table_stats WHERE vtime >= ".strtotime('last week');
		return $this->db->getOne('total',$query);
	}
    //获取今日唯一iP访问次数
	function getLastWeekUniqueHits() 
    {
		$query = "SELECT COUNT(DISTINCT ip) AS 'total' FROM $this->table_stats WHERE vtime >=".strtotime('last week');
		return $this->db->getOne('total',$query);
	}
    //获取今日访问次数
	function getLastMonthHits() 
    {
		$query = "SELECT COUNT(*) AS 'total' FROM $this->table_stats WHERE vtime >= ".strtotime(date("Y-m-01", time()));
		return $this->db->getOne('total',$query);
	}
    //获取今日唯一iP访问次数
	function getLastMonthUniqueHits() 
    {
		$query = "SELECT COUNT(DISTINCT ip) AS 'total' FROM $this->table_stats WHERE vtime >=".strtotime(date("Y-m-01", time()));
		return $this->db->getOne('total',$query);
	}
    //获取今日访问次数
	function getLastYearHits() 
    {
		$query = "SELECT COUNT(*) AS 'total' FROM $this->table_stats WHERE vtime >= ".strtotime(date("Y-01-01", time()));
		return $this->db->getOne('total',$query);
	}
    //获取今日唯一iP访问次数
	function getLastYearUniqueHits() 
    {
		$query = "SELECT COUNT(DISTINCT ip) AS 'total' FROM $this->table_stats WHERE vtime >=".strtotime(date("Y-01-01", time()));
		return $this->db->getOne('total',$query);
	}

	function truncate($var, $len = 120) 
    {
		if (empty ($var)) return "";
		if (strlen ($var) < $len) return $var; 
		if (preg_match ("/(.{1,$len})\s./ms", $var, $match)) { 
			return $match [1] . "..."; 
		} else { 
			return substr ($var, 0, $len) . "..."; 
		}
	}
	
	function trimReferer($r) 
    {
		$r = eregi_replace("http://","",$r);
		$r = eregi_replace("^www.","",$r);
		$r = $this->truncate($r,36);
		return $r;
	}

 }//end class
if(APP_NAME=='Admin') {
import("Admin.Action.AdminAction");
// 添加StortStat模块类
class  VStatsAction extends AdminAction
{
    function index() 
    {
        $stat = new VStats();
        $show  =  Array();
        // 统计图表属性
        $chartWidth   = 550;
        $chartHeight  = 300;
        $chartType = 'Column3D';
        $chartShow   =  true;
        $type = $_GET['type'];
        $map        = new HashMap();
        if(!empty($_POST['startTime1']) && !empty($_POST['startTime2'])) {
            $map->put('vtime', array(strtotime($_POST['startTime1']),strtotime($_POST['startTime2'])+86400));
        }elseif(!empty($_POST['startTime1'])) {
            $map->put('vtime', array('gt',strtotime($_POST['startTime1'])));
        }elseif(!empty($_POST['startTime2'])) {
            $map->put('vtime', array('lt',strtotime($_POST['startTime2'])+86400));
        }
        switch($type) {
        	case 'os'://系统统计
                $show['os']   = $stat->getPlatforms($map);
                if(!empty($show['os'])) {
                    $xml = "<chart caption='访问系统统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['os'] as $key=>$val) {
                        $xml .= "<set label='$val->platform' value='$val->total' />";
                    }
                    $xml .="</chart>";                	
                }else {
                	$chartShow   =  false;
                    $show['os']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                	
                }
                break;
            case 'language'://语言统计
            	$show['languages']   = $stat->getLanguage($map);
                if(!empty($show['languages'])) {
                    $xml = "<chart caption='访问语言统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['languages'] as $key=>$val) {
                        $xml .= "<set label='$val->language' value='$val->total' />";
                    }
                    $xml .="</chart>";                	
                }else {
                	$chartShow   =  false;
                    $show['languages']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                	
                }
            	break;
            case 'browser'://浏览器统计
            	$show['browsers']   =  $stat->getBrowsers($map);
                if(!empty($show['browsers'])) {
                    $xml = "<chart palette='2' showBorder='1' caption='访问浏览器统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['browsers'] as $key=>$val) {
                        $xml .= "<set label='$val->browser' value='$val->total' />";
                    }
                    $xml .="</chart>";                	
                }else {
                	$chartShow   =  false;
                    $show['browsers']  =  null;
                    $show['tipInfo'] =  '没有统计数据';
                }
            	break;
            case 'referer'://引用统计
            	$show['referers'] = $stat->getReferers();
                $chartShow = false;
            	break;
            case 'domain'://域名统计
            	$show['domains'] = $stat->getDomains($map);
                if(!empty($show['domains'])) {
                    $xml = "<chart caption='访问浏览器统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['domains'] as $key=>$val) {
                        $xml .= "<set label='$val->domain' value='$val->total' />";
                    }
                    $xml .="</chart>";                	
                }else {
                	$chartShow   =  false;
                    $show['domains']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                	
                }
            	break;
            case 'screen'://屏幕分辩率统计
                $show['screen'] = $stat->getScreens($map);
                if(!empty($show['screen'])) {
                    $xml = "<chart caption='屏幕分辩率统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['screen'] as $key=>$val) {
                        $xml .= "<set label='$val->screen' value='$val->total' />";
                    }
                    $xml .="</chart>";                	
                }else {
                	$chartShow   =  false;
                    $show['screen']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                	
                }
            	break;
            case 'color'://颜色深度统计
                $show['color'] = $stat->getColorDepts($map);
                if(!empty($show['color'])) {
                    $xml = "<chart caption='颜色深度统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['color'] as $key=>$val) {
                        $xml .= "<set label='$val->color' value='$val->total' />";
                    }
                    $xml .="</chart>";                    	
                }else {
                	$chartShow   =  false;
                    $show['color']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                   	
                }
            	break;
            case 'flash'://flash插件版本统计
                $show['flash'] = $stat->getFlashVers($map);
                if(!empty($show['flash'])) {
                    $xml = "<chart caption='Flash版本统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['flash'] as $key=>$val) {
                        $xml .= "<set label='$val->flash' value='$val->total' />";
                    }
                    $xml .="</chart>";                 	
                }else {
                	$chartShow   =  false;
                    $show['flash']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                 	
                }
            	break;
            case 'resource'://访问页面统计
               	$show['resources']   = $stat->getResources();
                $chartShow = false;
            	break;
            case 'keyword'://关键字统计
            	$show['keywords'] =  $stat->getKeywords($map);
                if(!empty($show['keywords'])) {
                    $xml = "<chart caption='关键字访问统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['keywords'] as $key=>$val) {
                        $xml .= "<set label='$val->keyword' value='$val->total' />";
                    }
                    $xml .="</chart>";                	
                }else {
                	$chartShow   =  false;
                    $show['keywords']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                    	
                }
            	break;
            case 'search':
            	$show['search']  = $stat->getSearchTypes($map);
                if(!empty($show['search'])) {
                    $xml = "<chart caption='搜索引擎统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['search'] as $key=>$val) {
                        $xml .= "<set label='$val->stype' value='$val->total' />";
                    }
                    $xml .="</chart>";                	
                }else {
                	$chartShow   =  false;
                    $show['search']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                	
                }
            	break;
            
            case 'country'://地理位置统计
            	$show['countrys'] = $stat->getCountries($map);
                if(!empty($show['countrys'])) {
                    $xml = "<chart caption='访问地址统计' showValues='1' decimals='0' formatNumberScale='0'>";
                    foreach($show['countrys'] as $key=>$val) {
                        $xml .= "<set label='$val->country' value='$val->total' />";
                    }
                    $xml .="</chart>";                	
                }else {
                	$chartShow   =  false;
                    $show['countrys']  =  null;
                    $show['tipInfo'] =  '没有统计数据';                	
                }
            	break;
            case 'detail'://详细访问统计
                $chartShow   =  false;
            	$dao = new VStatsDao();
                $vo  =  new VStatsVo();
                $vars       = $vo->__toArray();
                foreach($vars as $key=>$val) {
                    if(isset($_REQUEST[$key]) && $_REQUEST[$key]!='') {
                        $map->put($key,$_REQUEST[$key]);
                    }
                }
                if(isset($_REQUEST['order'])) {
                    $order = $_REQUEST['order'];
                }else {
                    $order = $dao->pk;
                }
                //排序方式默认按照倒序排列
                //接受 sost参数 0 表示倒序 非0都 表示正序
                if(isset($_REQUEST['sort'])) {
                    $sort = $_REQUEST['sort']?'asc':'desc';
                }else {
                    $sort = 'desc';
                }
                //取得满足条件的记录数
                $count      = $dao->getCount($map);
                import("ORG.Util.Page");
                //创建分页对象
                if(!empty($_GET['listRows'])) {
                    $listRows  =  $_GET['listRows'];
                }
                $p          = new Page($count,$listRows);
                //分页查询数据
                $voList     = $dao->findAll($map,'','*',$order.' '.$sort,$p->firstRow.','.$p->listRows);         
                //分页跳转的时候保证查询条件
                $condition  = $map->toArray();
                foreach($condition as $key=>$val) {
                    $p->parameter   .=   "$key=$val&";         
                }
                //分页显示
                $page       = $p->show();
                //列表排序显示
                $sortImg    = $sort ;                                   //排序图标
                $sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
                $sort       = $sort == 'desc'? 1:0;                     //排序方式
                //模板赋值显示
                $this->assign('list',       $voList);
                $this->assign('sort',       $sort);
                $this->assign('order',      $order);
                $this->assign('sortImg',    $sortImg);
                $this->assign('sortType',   $sortAlt);
                $this->assign("page",       $page);
            	break;
            case 'summary':
            default:
                $show['firstHit'] =  $stat->getFirstHit();
                if(empty($show['firstHit'])) {
                	$days   =  0;
                }else {
                    $diff = (int) abs(time() - $show['firstHit']);
                    $days = round($diff / 86400)+1;                	
                }
                $show['totalDays']  = $days;
                $show['totalHits']    =  $stat->getTotalHits();
                $show['uniqueHits'] = $stat->getUniqueHits();          
                $show['lastMonthHits']   =  $stat->getLastMonthHits();
                $show['lastMonthUHits'] = $stat->getLastMonthUniqueHits();
                $show['lastYearHits']   =  $stat->getLastYearHits();
                $show['lastYearUHits'] = $stat->getLastYearUniqueHits();
                if(!empty($days)) {
                    $show['averageHits']   =  $show['totalHits']/$days;
                    $show['averageUHits'] = $show['uniqueHits']/$days;                	
                }else {
                    $show['averageHits']   =  0;
                    $show['averageUHits'] = 0;                	
                }
                $show['todayHit']   =  $stat->getTodayHits();
                $show['todayUHit'] = $stat->getTodayUniqueHits();
                $show['yesterdayHit']   =  $stat->getYesterdayHits();
                $show['yesterdayUHit'] = $stat->getYesterdayUniqueHits();
                $show['lastWeekHits'] =  $stat->getLastWeekHits();
                $show['lastWeekUHits'] =  $stat->getLastWeekUniqueHits();                
                $this->assign('hits','hit');
                $chartShow = false;
            	break;            
        
        }
        $this->assign('chartShow',$chartShow);
        $this->assign('chartWidth',$chartWidth);
        $this->assign('chartHeight',$chartHeight);
        $this->assign('chartType',$chartType);
        $this->assign('dataXML',$xml);
        $this->assign($show);
    	$this->display(dirname(__FILE__).'/VStats.html');
    }
}

class VStatsDao extends Dao 
{
	
}//end class

class VStatsVo extends Vo 
{
	var $id;
    var $ip;
    var $country;
    var $area;
    var $language;
    var $domain;
    var $referer;
    var $resource;
    var $agent;
    var $os;
    var $browser;
    var $version;
    var $vtime;
    var $stype;
    var $total;
    var $screen;
    var $color;
    var $flash;
    var $per;

}//end class

class SearchVo extends Vo 
{
	var $id;
    var $keyword;
    var $count;
}//end class	
// 添加ShortStat模块
//add_module('VStats','VStatsModule');
}

function saveInfo() 
{
    if(!Session::is_set('trackInfo')) {
        $info = array();
        $info['flashver']   = $_POST['flashver'];
        $info['scrWidth']  = $_POST['scrWidth'];
        $info['scrHeight'] = $_POST['scrHeight'];
        $info['color']       = $_POST['color'];
        $stat = new VStats();
        $stat->track($info);
        Session::set('trackInfo',$_SERVER['REMOTE_ADDR']);    	
    }
    exit ;
}

function track() 
{
    $track   =  " if( navigator.appName != 'Netscape'){ var color = screen.colorDepth;} else { var color = screen.pixelDepth;}var scrWidth = screen.width;var scrHeight = screen.height;var version = deconcept.SWFObjectUtil.getPlayerVersion();var flashver	=	version['major'] +'.'+ version['minor'] +'.'+ version['rev'];SmartAjax.send('".__APP__."/Public/saveInfo','color='+color+'&scrWidth='+scrWidth+'&scrHeight='+scrHeight+'&flashver='+flashver,'');";
    exit($track);
}
 //实例化统计类
$stat = new VStats();

// 初始化统计类
//add_filter('app_init', array(&$stat, 'setup'));
// 统计信息保存操作
add_action('saveInfo','saveInfo');
// 跟踪页面
//add_filter('app_end', array(&$stat, 'track'));
// 需要添加js文件在页面底部
//<script language='JavaScript' src='__APP__/Public/track'></script> 
add_action('track','track');
?>