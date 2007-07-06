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

/**
 +------------------------------------------------------------------------------
 * FCS
 +------------------------------------------------------------------------------
 * @package    Util
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: CacheFileReader.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */
import('FCS.Util.StringReader');
/**
 +------------------------------------------------------------------------------
 * 文件缓存读取类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class CachedFileReader extends StringReader 
{
    function __construct($filename) {
        if (file_exists($filename)) {

          $length=filesize($filename);
          $fd = fopen($filename,'rb');

          if (!$fd) {
        $this->error = 3; // Cannot read file, probably permissions
        return false;
          }
          $this->_str = fread($fd, $length);
          fclose($fd);

        } else {
          $this->error = 2; // File doesn't exist
          return false;
        }
    }
}
?>