<?php 
// +----------------------------------------------------------------------+
// | ThinkCMS                                                             |
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
 * CMS 文章数据访问对象
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
class ArticleDao extends Dao
{
	function top($condition) 
	{
        $table = $this->getRealTableName();
        if(FALSE === $this->db->execute('update '.$table.' set isTop=1 where isTop=0 and ('.$condition.')')){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
	}
	function untop($condition) 
	{
        $table = $this->getRealTableName();
        if(FALSE === $this->db->execute('update '.$table.' set isTop=0 where isTop=1 and ('.$condition.')')){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
	}

	function recommend($condition) 
	{
        $table = $this->getRealTableName();
        if(FALSE === $this->db->execute('update '.$table.' set isRecommend=1 where isRecommend=0 and ('.$condition.')')){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
	}

	function unrecommend($condition) 
	{
        $table = $this->getRealTableName();
        if(FALSE === $this->db->execute('update '.$table.' set isRecommend=0 where isRecommend=1 and ('.$condition.')')){
            $this->error =  _OPERATION_WRONG_;
            return false;
        }else {
            return True;
        }
	}
}//end class
?>