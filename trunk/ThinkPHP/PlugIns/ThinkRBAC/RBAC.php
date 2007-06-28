<?php 
/*
Plugin Name: ThinkRBAC
Plugin URI: http://fcs.org.cn/
Description: 基于角色的权限认证， 在需要获取用户权限的地方添加接口方法，需要数据库的支持
Author: 流年
Version: 1.0
Author URI: http://blog.liu21st.com/
*/ 

// 配置文件增加设置
// USER_AUTH_ON 是否需要认证
// USER_AUTH_TYPE 认证类型
// USER_AUTH_KEY 认证识别号
// REQUIRE_AUTH_MODULE  需要认证模块
// NOT_AUTH_MODULE 无需认证模块
// USER_AUTH_GATEWAY 认证网关
class RBAC extends Base 
{
    //委托身份认证方法
    function authenticate($map,$dao='UserDao',$provider='') 
    {
            //调用委托管理器进行身份认证
            import("RBAC.ProviderManager",dirname(__FILE__));
			if(empty($provider)) {
				$provider	=	C('USER_AUTH_PROVIDER');
			}
            $authProvider   =   ProviderManager::getInstance($provider);
            //使用给定的Map进行认证
            if($authProvider->authenticate($map,$dao)) {	
                $authInfo   =   $authProvider->data;
                return $authInfo;
            }else {
                //认证失败
                return false;
            }
    }

    //用于检测用户权限的方法,并保存到Session中
    function saveAccessList($authId=null) 
    {
            // 如果使用普通权限模式，保存当前用户的访问权限列表
            // 对管理员开发所有权限
            if(C('USER_AUTH_TYPE') !=2 && !Session::is_setLocal('administrator') ) {
                Session::set('_ACCESS_LIST',RBAC::getAccessList($authId));
            }	
            return ;
    }

    //取得用户的授权列表
    function getAccessList($authId=null) 
    {
            if(null===$authId) {
                $authId = Session::get(C('USER_AUTH_KEY'));
            }
            //获取权限访问列表
            import("RBAC.AccessDecisionManager",dirname(__FILE__));
            $accessManager = new AccessDecisionManager();
            $accessList = $accessManager->getAccessList($authId);
            return $accessList;
    }

    //检查当前操作是否需要认证
    function checkAccess() 
    {

        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if( C('USER_AUTH_ON') ){
            $notAuthModuleList = array(); 
            $requireAuthModuleList = array();
            if("" != C('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $requireAuthModuleList = explode(',',C('REQUIRE_AUTH_MODULE'));
            }else {
                //无需认证的模块
                $notAuthModuleList = explode(',',C('NOT_AUTH_MODULE'));  
            }
            //检查当前模块是否需要认证
            if(!in_array(MODULE_NAME,$notAuthModuleList) || in_array(MODULE_NAME,$requireAuthModuleList)) {
                return true;
            }else {
                return false;
            }
        }
        return false;	
    }

    //权限认证的过滤器方法
    function AccessDecision() 
    {
        //检查是否需要认证
        if(RBAC::checkAccess()) {
            //检查认证识别号
            if(!Session::is_set(C('USER_AUTH_KEY'))) {
                //跳转到认证网关
                redirect(PHP_FILE.C('USER_AUTH_GATEWAY'));
            }
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid   =   md5(APP_NAME.MODULE_NAME.ACTION_NAME);
            if(!Session::is_setLocal('administrator')) {//管理员无需认证
                if(C('USER_AUTH_TYPE')==2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    $accessList = RBAC::getAccessList();
                }else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if( Session::is_set($accessGuid)) {
                        return ;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = Session::get('_ACCESS_LIST');
                }
                if(!isset($accessList[strtoupper(APP_NAME)][strtoupper(MODULE_NAME)][strtoupper(ACTION_NAME)])) {
                    throw_exception(L('没有权限!'));
                }else {
                    Session::set($accessGuid,true);
                }
            }
        }
        return true;
    }	
}//end class
if(C('USER_AUTH_ON')) {
    //在应用初始化的时候添加认证过滤器
    add_filter('app_init',array('RBAC','AccessDecision'));	
}
?>