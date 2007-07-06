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
 * @version    $Id: FileReader.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 文件流读取类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class FileReader 
{
    var $_pos;
    var $_fd;
    var $_length;

    function __construct($filename) {
        if (file_exists($filename)) {
          $this->_length=filesize($filename);
          $this->_pos = 0;
          $this->_fd = fopen($filename,'rb');
          if (!$this->_fd) {
            $this->error = 3; // Cannot read file, probably permissions
            return false;
          }
        } else {
          $this->error = 2; // File doesn't exist
          return false;
        }
    }

    function read($bytes) {
        if ($bytes) {
          fseek($this->_fd, $this->_pos);
          $data = fread($this->_fd, $bytes);
          $this->_pos = ftell($this->_fd);
          
          return $data;
        } else return '';
    }

    function seekto($pos) {
        fseek($this->_fd, $pos);
        $this->_pos = ftell($this->_fd);
        return $this->_pos;
    }

    function currentpos() {
        return $this->_pos;
    }

    function length() {
        return $this->_length;
    }

    function close() {
        fclose($this->_fd);
    }

}
?>