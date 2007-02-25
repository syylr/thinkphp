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
 * 文件类型缓存类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Cache_File extends Cache
{//类定义开始


    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct($options)
    {
        if(!empty($options['temp'])){
            $this->options['temp'] = $options['temp'];
        }else {
            $this->options['temp'] = TEMP_PATH;
        }
        $this->expire = isset($options['expire'])?$options['expire']:DATA_CACHE_TIME;
        if(!is_dir($this->options['temp'])){
            mkdir($this->options['temp']);
        }
        if(substr($this->options['temp'], -1) != "/")    $this->options['temp'] .= "/";
        $this->connected = is_dir($this->options['temp']) && is_writeable($this->options['temp']);
        $this->type = strtoupper(substr(__CLASS__,6));
        $this->init();

    }

    /**
     +----------------------------------------------------------
     * 初始化检查
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function init() 
    {
        $stat = stat($this->options['temp']);
		$dir_perms = $stat['mode'] & 0007777; // Get the permission bits.
		$file_perms = $dir_perms & 0000666; // Remove execute bits for files.

		// 创建项目缓存目录
		if (!file_exists($this->options['temp'])) {
			if (!  mkdir($this->options['temp']))
				return false;
			 chmod($this->options['temp'], $dir_perms);
		}
        // 创建缓存目录安全文件
		if (!file_exists($this->options['temp']."index.php")) {
			 touch($this->options['temp']."index.php");
			 chmod($this->options['temp']."index.php", $file_perms);
		}    	
    }

    /**
     +----------------------------------------------------------
     * 是否连接
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function isConnected()
    {
        return $this->connected;
    }

    /**
     +----------------------------------------------------------
     * 取得变量的存储文件名
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function filename($name)
    {
        return $this->options['temp'].$this->prefix.md5($name).'.php';
    }

    
    /**
     +----------------------------------------------------------
     * 读取缓存
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    function get($name)
    {
        $filename   =   $this->filename($name);
        if (!$this->isConnected() || !file_exists($filename)) {
           return false;
        }
        $content    =   file_get_contents($filename);
        if( false !== $content) {
            $expire  =  substr($content,strlen(CACHE_SERIAL_HEADER), 6);
            if($expire != -1 && time() > filemtime($filename) + $expire) { 
                //缓存过期删除缓存文件
                unlink($filename);
                return false;
            }
            if(DATA_CACHE_CHECK) {//开启数据校验
                $check  =  substr($content,strlen(CACHE_SERIAL_HEADER)+6, 32);
                $content   =  substr($content,strlen(CACHE_SERIAL_HEADER)+6+32, -strlen(CACHE_SERIAL_FOOTER));
                if($check != md5($content)) {//校验错误
                    return false;
                }
            }else {
            	$content   =  substr($content,strlen(CACHE_SERIAL_HEADER)+6, -strlen(CACHE_SERIAL_FOOTER));
            }
            if(DATA_CACHE_COMPRESS && function_exists('gzcompress')) {
                //启用数据压缩
                $content   =   gzuncompress($content);
            }
            $content    =   unserialize($content);
            return $content;
        }
        else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 写入缓存
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function set($name, $value,$expire='')
    {
        if(empty($expire)) {
        	$expire =  $this->expire;
        }
        $filename   =   $this->filename($name);
        $data   =   serialize($value);
        if( DATA_CACHE_COMPRESS && function_exists('gzcompress')) {
            //数据压缩
            $data   =   gzcompress($data,3);
        }
        if(DATA_CACHE_CHECK) {//开启数据校验
        	$check  =  md5($data);
        }else {
        	$check  =  '';
        }
        $data    = CACHE_SERIAL_HEADER.sprintf('%06d',$expire).$check.$data.CACHE_SERIAL_FOOTER;
        $result  =   file_put_contents($filename,$data);
        if($result) {
            clearstatcache();
            return true;
        }else {
        	return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 删除缓存
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function rm($name)
    {
        return unlink($this->filename($name));
    }

    /**
     +----------------------------------------------------------
     * 清除缓存
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function clear()
    {
        import("Think.Util.Dir");
        Dir::del($this->options['temp']);
    }

}//类定义结束
?>