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
// $Id: PlugInAction.class.php 78 2007-04-01 04:29:15Z liu21st $

import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 插件管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class PlugInAction extends AdminAction
{//类定义开始

    function index() 
    {
        $this->flesh();
        parent::index() ;        
    }
                
    function flesh() 
    {
        $dao = D("PlugIn");
        //$dao->startTrans();
        // 读取公共插件
        $plugins = get_plugins(THINK_PATH.'/PlugIns','Think');// 公共插件
        $this->_add_app_plugin($dao,$plugins,'Public');
        // 读取项目插件
        $nodeDao = D("Node");
        $list = $nodeDao->findAll('level=1 and status=1');
        foreach($list->getIterator() as $key=>$val) {
            $app = $val->name;
            $plugins = get_plugins(WEB_ROOT.$app.'/PlugIns',$app);// 项目插件
            $this->_add_app_plugin($dao,$plugins,$app);
            $this->_writePlugin($app);
        }
        //$dao->commit();   
        return ;
    }

    function _add_app_plugin($dao,$plugins,$app) 
    {
        foreach($plugins as $key=>$plugin) {
            $result = $dao->find('app="'.$app.'" and name="'.$plugin['name'].'"');
            if(!$result) {
                // 如果数据库不存在该插件 添加
                $plugin['app'] = $app;
                if(substr($plugin['name'],0,5) =='Think' ) {
                    // 对于Think开头的插件默认启用
                	$plugin['status'] =  1;
                }else {
                    $plugin['status'] =  0; //默认为禁用                	
                }
                $dao->add($plugin);
            }
        }    	
        return ;
    }

    function _writePlugin($app) 
    {
        // 如果插件有变化
        $dao = D("PlugIn");
        $list  = $dao->findAll('(app="'.$app.'" OR app="Public") AND status=1');
        $plugins    = $list->toResultSet();
        // 保存有效插件数据
        $content  = "<?php\n\r";
        $content .= "return ".var_export($plugins,true);
        $content .= ";\n\r?>";
        file_put_contents(WEB_ROOT.$app.'/Conf/_appPlugins.php',$content);     	
        return ;
    }
}//类定义结束
?>