<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * 访问决策管理器
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  RBAC
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class AccessDecisionManager extends Base
{//类定义开始

    const	 RBAC_ROLE_TABLE	=	'think_role';
    const	 RBAC_USER_TABLE	=	'think_role_user';
    const	 RBAC_ACCESS_TABLE	=	'think_access';
    const	 RBAC_NODE_TABLE	 =	 'think_node';

	static public function configAccessList($config) {
		//self::RBAC_ROLE_TABLE	=	$config['role_table'];
		//self::RBAC_USER_TABLE	=	$config['user_table'];
		//self::RBAC_ACCESS_TABLE	=	$config['access_table'];
		//self::RBAC_NODE_TABLE	=	$config['node_table'];
	}

    /**
     +----------------------------------------------------------
     * 取得当前认证号的所有权限列表
     +----------------------------------------------------------
     * @param string $appPrefix 数据库前缀
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function getAccessList($authId)
    {
        // 文件方式权限数据
        // 读取该用户Id的权限列表
        // 权限文件格式 array('app1'=>array('module1'=>array('action1','action2',...),...),...);
        if(is_file(TEMP_PATH.'access_'.$authId.'.php')) {
            // 已经存在用户权限缓存
            $access	 =	 include TEMP_PATH.'access_'.$authId.'.php';
        }else{
            // 读取用户权限并生成缓存
        }
        return $access;
    }

	// 读取模块所属的记录访问权限
	public function getModuleAccessList($authId,$module) {
        // 文件方式
        // 权限文件格式 array('module'=>array('recordId1','recordId2',...),...);
        $access =	include DATA_PATH.'access_'.$module.'_'.$authId.'.php';
		return $access;
	}
}//类定义结束
?>