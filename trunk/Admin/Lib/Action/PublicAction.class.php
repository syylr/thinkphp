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
 * CMS 公共管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
import('@.Action.AdminAction');

class PublicAction extends AdminAction
{//类定义开始

    /**
     +----------------------------------------------------------
     * 验证Vo对象数据有效性
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function _validation() 
    {

        $validation = Validation::getInstance();
        //对所有需要验证的数据进行验证
        if(isset($_POST['name'])) {
            if(!$validation->check($_POST['name'],'require')) {
                $this->error    =   '用户名必填！';
				return false;
            }        
        }
        if(isset($_POST['oldpassword'])) {
            if(!$validation->check($_POST['oldpassword'],'require')) {
                $this->error    =   '旧密码有误';
				return false;
            }
        }
        if(isset($_POST['password'])) {
            if(!$validation->check($_POST['password'],'require')) {
                $this->error    =   '密码有误';
				return false;
            }
        }
        if(isset($_POST['repassword'])) {
            if(!$validation->check($_POST['repassword'],'require')) {
                $this->error    =   '请输入确认密码';
				return false;
            }
            if($_POST['repassword']!= $_POST['password']) {
                $this->error    =   '确认密码不正确';
				return false;
            }
        }
        if(isset($_POST['verify'])) {
            if(''==$_POST['verify'] ) {
                $this->error    =   '验证码必填！';
				return false;
            }
        }
        return true;
    }

    function install() 
    {
    	$this->display();
    }

    function installok() 
    {
        $db_config = array (
            'dbms'     => $_POST['db_type'], 
            'username' => $_POST['db_username'], 
            'password' => $_POST['db_password'], 
            'hostname' => $_POST['db_hostname'], 
            'hostport' => $_POST['db_hostport'], 
            'database' => $_POST['db_database']
        );
        // 创建数据表
		$table_user_query = "CREATE TABLE {$db_prefix}user (
							  id int(11) unsigned NOT NULL auto_increment,
							  name varchar(30) NOT NULL default '',
							  nickname varchar(50) NOT NULL default '',
                              password varchar(32) NOT NULL default '',
							  email VARCHAR(255) NOT NULL default '',
							  url varchar(255) NOT NULL default '',
							  verify varchar(8) NOT NULL default '',
							  rtime int(11) unsigned NOT NULL default 0,
							  ltime int(11) unsigned NOT NULL default 0,
                              guid varchar(32) NOT NULL default '',
							  status tinyint(1) unsigned NOT NULL default 0,
							  PRIMARY KEY  (id)
							  ) TYPE=InnoDB DEFAULT CHARSET=utf8 ";
        $result  =  $db->execute($table_user_query);
        if(false === $result) {
        	$this->error('数据库错误！');
        }

		$table_category_query = "CREATE TABLE {$db_prefix}category (
							  id mediumint(5) unsigned NOT NULL auto_increment,
							  name varchar(30) NOT NULL default '',
							  title varchar(50) NOT NULL default '',
                              remark varchar(255) NOT NULL default '',
							  seqno mediumint(5) unsigned NOT NULL default 0,
							  pid mediumint(5) unsigned NOT NULL default 0,
							  level smallint(2) unsigned NOT NULL default 0,
							  status tinyint(1) unsigned NOT NULL default 0,
							  PRIMARY KEY  (id)
							  ) TYPE=InnoDB DEFAULT CHARSET=utf8 ";
        $result  =  $db->execute($table_category_query);
        if(false === $result) {
        	$this->error('数据库错误！');
        }

		$table_comment_query = "CREATE TABLE {$db_prefix}comment (
							  id mediumint(5) unsigned NOT NULL auto_increment,
							  articleid int(11) unsigned NOT NULL default 0,
							  userid mediumint(5) unsigned NOT NULL default 0,
                              author varchar(50) NOT NULL default '',
							  email varchar(255) NOT NULL default '',
							  url varchar(255) NOT NULL default '',
							  ip varchar(25) NOT NULL default '',
							  content text NOT NULL default '',
							  ctime int(11) unsigned  NOT NULL default 0,      
							  agent int(11) unsigned NOT NULL default 0,                                  
							  status tinyint(1) unsigned NOT NULL default 0,
							  PRIMARY KEY  (id)
							  ) TYPE=InnoDB DEFAULT CHARSET=utf8 ";
        $result  =  $db->execute($table_comment_query);
        if(false === $result) {
        	$this->error('数据库错误！');
        }

		$table_board_query = "CREATE TABLE {$db_prefix}board (
							  id mediumint(5) unsigned NOT NULL auto_increment,
							  title varchar(255) NOT NULL default '',
							  content text  NOT NULL default '',
                              btime int(11) unsigned NOT NULL default 0,
							  etime int(11) unsigned NOT NULL default 0,
							  status tinyint(1) unsigned NOT NULL default 0,
							  PRIMARY KEY  (id)
							  ) TYPE=InnoDB DEFAULT CHARSET=utf8 ";
        $result  =  $db->execute($table_board_query);
        if(false === $result) {
        	$this->error('数据库错误！');
        }

		$table_article_query = "CREATE TABLE {$db_prefix}article (
							  id int(11) unsigned NOT NULL auto_increment,
							  name varchar(15) NOT NULL default '',
							  userid mediumint(5) unsigned NOT NULL default 0,
                              title varchar(255) NOT NULL default '',
							  content text NOT NULL default '',
							  password varchar(32) NOT NULL default '',
							  ctime int(11) unsigned NOT NULL default 0,
							  atime int(11) unsigned NOT NULL default 0,
							  mtime int(11) unsigned NOT NULL default 0,
							  status tinyint(1) unsigned NOT NULL default 0,
							  isrecommend tinyint(1) unsigned NOT NULL default 0,
                              istop tinyint(1) unsigned unsigned NOT NULL default 0,
                              commentstatus tinyint(1) unsigned NOT NULL default 0,
                              guid varchar(50) NOT NULL default '',
							  readcount mediumint(5) unsigned NOT NULL default 0,
                              commentcount mediumint(5) unsigned NOT NULL default 0,
							  PRIMARY KEY  (id)
							  ) TYPE=InnoDB DEFAULT CHARSET=utf8 ";
        $result  =  $db->execute($table_article_query);
        if(false === $result) {
        	$this->error('数据库错误！');
        }
        $admin =  array();
        $admin['name']    = $_POST['admin_username'];
        $admin['password']    =  $_POST['admin_password'];
        $admin['status']          = 1;
        $admin['rtime']          =  time();
        $admin['nickname']    =  'administrator';
        $result  =  $db->add($admin);
        if(false !== $result) {
        	$this->success('安装成功！');
        }
			      	
    }
    /**
     +----------------------------------------------------------
     * 登录操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function login() 
    {
        if(!Session::is_set(USER_AUTH_KEY)) {
            $this->display();
            return ;
        }else {
			redirect(__APP__);
        }
    }

	function index() 
	{
		//如果通过认证跳转到首页
		redirect(__APP__);
	}


    /**
     +----------------------------------------------------------
     * 用户登出
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function logout() 
    {
        if(Session::is_set(USER_AUTH_KEY)) {
            Session::clear();
            $this->assign("message",'登出成功！');
            $this->assign("jumpUrl",__URL__.'/login/');
        }else {
            $this->assign('error', '已经登出！');
        }

        $this->forward();
    }


    /**
     +----------------------------------------------------------
     * 用户登录检查
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function checkLogin() 
    {
        import('FCS.Util.HashMap');
        //生成认证条件
        $map            =   new HashMap();
        $map->put("name",$_POST['name']);

        //检查密码加密方式
        $encoder = AUTH_PWD_ENCODER;
        if(!empty($encoder) && function_exists($encoder)) {
            $map->put("password",$encoder($_POST['password']));
        }
        else {
        	$map->put("password",$_POST['password']);
        }
        $map->put("status",1);
        $verifyCodeStr   = $_POST['verify'];
        $verifyCodeNum   = array_flip($_SESSION['verifyCode']);
        for($i=0; $i<strlen($_POST['verify']); $i++) {
        	$verify .=  $verifyCodeNum[$verifyCodeStr[$i]];
        }
        $map->put("verify",$verify);
        $authInfo = RBAC::authenticate($map);

        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
            $this->assign('error','登录失败，请检查用户名、密码和验证码！');
        }else {
            Session::set(USER_AUTH_KEY,$authInfo->id);
            Session::set('loginUserName',$authInfo->nickname);
            Session::set('lastLoginTime',$authInfo->lTime);
            if($authInfo->name=='admin') {
            	Session::setLocal('administrator',true);
            }
            //保存登录时间
            import('@.Dao.UserDao');
            $dao    =   new UserDao();
            $map->clear();
            $map->put('id',$authInfo->id);
            $map->put('lTime',time());
            $dao->save($map);
            RBAC::saveAccessList();
            $this->assign("message",'登录成功！');
            $this->assign("jumpUrl",__APP__);
        }

        $this->forward();

    }

    /**
     +----------------------------------------------------------
     * 修改用户信息
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	function change() 
	{
		if(Session::is_set('agencyId')) {
			import('@.Dao.AgencyDao');
			$dao = new AgencyDao();
		}elseif(Session::is_set('providerId') || Session::is_set('dealerId')) {
			import('@.Dao.DealerDao');
			$dao = new DealerDao();
		}elseif(Session::is_set('girlId')) {
			import('@.Dao.GirlDao');
			$dao = new GirlDao();
		}elseif(Session::is_set('userId')) {
			import('@.Dao.UserDao');
			$dao = new UserDao();
		}	
		$this->_update($dao);
	}

    /**
     +----------------------------------------------------------
     * 修改密码
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function password() 
    {
        if(Session::is_set(USER_AUTH_KEY)) {
            $this->assign("login",true);
        }
    	$this->display();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 更换密码
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function changePwd() 
    {
        //对表单提交处理进行处理或者增加非表单数据
        $encoder = AUTH_PWD_ENCODER;
        $map    =   new HashMap();
        if(!empty($encoder) && function_exists($encoder)) {
            $_POST['password']      =   $encoder($_POST['password']);
            $_POST['oldpassword']      =   $encoder($_POST['oldpassword']);
        }
        $map->put('password',$_POST['oldpassword']);
        if(isset($_POST['name'])) {
            $map->put('name',$_POST['name']);
        }elseif(Session::is_set(USER_AUTH_KEY)) {
            $map->put('id',Session::get(USER_AUTH_KEY));
        }else {
        	
        }
        //检查用户
        $dao    =   new UserDao();
        $vo     =   $dao->find($map);
        if($vo->isEmpty()) {
            $this->assign('error','旧密码不符或者用户名错误！');
        }else {
        	$map->put('password',$_POST['password']);
            $map->put('id',$vo->id);
            $user = new UserVo($map);
            $result = $dao->save($user);
            if($result) {
                 $this->assign("message",'密码修改成功');
                 $this->assign("jumpUrl",__APP__);
            }else {
            $this->assign('error','密码修改失败！');            	
            }
        }
        $this->forward();
    }

}//类定义结束
?>